<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <title>Document List</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2>Document List</h2>
                <div class="alert alert-success " id="success-alert" style="display:none" role="alert">

                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Template ID</th>
                            <th>User ID</th>
                            <th>Data Content</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="document-list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Document</h5>
                    <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateForm"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="close">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>



        document.addEventListener("DOMContentLoaded", function () {

            const successAlert = document.getElementById('success-alert');
            const updateInput = document.getElementById('updateInput');
            const updateModal = document.getElementById('updateModal');
            const updateModalLabel = document.getElementById('updateModalLabel');
            const updateSubmit = document.getElementById('updateSubmit');
            const updateForm = document.getElementById('updateForm');
            let currentUpdatingDocument;

            function uploadImage(fileInput, type) {
                const formData = new FormData();
                formData.append("image", fileInput.files[0]);

                return fetch(`/DTT/upload?type=${type}`, {
                    method: "POST",
                    body: formData,
                }).then((response) => response.json());
            }
            function openUpdateModal(doc) {
                updateForm.innerHTML = '';
                const inputChangedFlags = {};
                const dataContent = JSON.parse(doc.data_content);
                console.log('===============DATA cONTENT=====================');
                console.log(dataContent);
                console.log('====================================');
                for (const key in dataContent) {
                    currentUpdatingDocument = doc;
                    if (typeof dataContent[key] === 'object' && dataContent[key].filename) {
                        const fileInput = document.createElement('input');
                        fileInput.type = 'file';
                        fileInput.className = 'form-control-file';

                        const fileType = dataContent[key].type;
                        if (fileType.startsWith('image/')) {
                            fileInput.accept = 'image/*';
                        } else if (fileType.startsWith('audio/')) {
                            fileInput.accept = 'audio/*';
                        } else if (fileType.startsWith('video/')) {
                            fileInput.accept = 'video/*';
                        }

                        const img = document.createElement('img');
                        img.alt = key;
                        img.style.maxWidth = '100px';
                        img.style.maxHeight = '100px';

                        if (dataContent[key].filename) {
                            img.src = 'http://localhost/DTT/uploads/' + dataContent[key].filename;
                        }

                        updateForm.appendChild(img);
                        updateForm.appendChild(fileInput);

                        fileInput.addEventListener('change', (event) => {
                            const selectedFile = event.target.files[0];
                            inputChangedFlags[key] = true;
                            if (selectedFile) {
                                img.src = URL.createObjectURL(selectedFile);
                                inputChangedFlags[key] = true;
                            }
                        });
                    }
                    else {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'form-control';
                        input.value = dataContent[key];
                        input.setAttribute('label', key);

                        const label = document.createElement('label');
                        label.textContent = key;

                        input.addEventListener('input', () => {
                            inputChangedFlags[key] = true;
                        });

                        updateForm.appendChild(label);
                        updateForm.appendChild(input);
                    }
                }


                updateModalLabel.textContent = `Update Document ${doc.document_id}`;
                updateModal.show();
            }


            updateSubmit.addEventListener('click', () => {
                if (currentUpdatingDocument) {
                    const updatedDataContent = {};

                    const formInputs = updateForm.getElementsByTagName('input');

                    for (const input of formInputs) {
                        const label = input.getAttribute('label');
                        if (input.type === 'file') {
                            if (input.files.length > 0) {
                                const selectedFile = input.files[0];
                                const fileType = selectedFile.type;
                                updatedDataContent[label] = {
                                    type: fileType,
                                    filename: null,
                                };
                            } else if (currentUpdatingDocument.data_content[label] && currentUpdatingDocument.data_content[label].filename) {
                                updatedDataContent[label] = currentUpdatingDocument.data_content[label];
                            }
                        } else {
                            const value = input.value;
                            updatedDataContent[label] = value;
                        }
                    }

                    submitUpdate(currentUpdatingDocument.document_id, JSON.stringify(updatedDataContent));
                }
            });


            function submitUpdate(documentId, updatedDataContent) {
                const requestData = {
                    document_id: documentId,
                    data_content: JSON.parse(updatedDataContent),
                };

                const fileInputs = updateForm.getElementsByTagName('input');
                const uploadPromises = [];

                for (const input of fileInputs) {
                    if (input.type === 'file') {
                        const label = input.getAttribute('label');
                        const selectedFile = input.files[0];

                        if (selectedFile) {
                            uploadPromises.push(uploadImage(selectedFile, label).then(result => {
                                if (result.success) {
                                    requestData.data_content[label] = {
                                        type: selectedFile.type,
                                        filename: result.filename,
                                    };
                                }
                            }));
                        } else {
                            // If no new image is selected, retain the old value
                            const dataContent = currentUpdatingDocument.data_content;
                            if (dataContent && dataContent[label] && dataContent[label].filename) {
                                requestData.data_content[label] = {
                                    type: dataContent[label].type,
                                    filename: dataContent[label].filename,
                                };
                            }
                        }
                    }
                }


                Promise.all(uploadPromises)
                    .then(() => {
                        $.ajax({
                            url: "/DTT/update_document/",
                            method: "PUT",
                            dataType: "json",
                            contentType: "application/json",
                            data: JSON.stringify(requestData),
                            success: function (data) {
                                alert('HI')
                                if (data.success) {
                                    successAlert.style.display = "block";
                                    successAlert.textContent = "Document updated successfully";
                                    // setTimeout(() => {
                                    //     location.reload();
                                    // }, 800);
                                } else {
                                    console.error("Failed to update document");
                                }
                            },
                            error: function (error) {
                                console.error("An error occurred: " + error);
                            },
                        });
                    })
                    .catch(error => {
                        console.error("Error uploading image:", error);
                    });
            }

            const docList = document.getElementById("document-list");

            fetch("/DTT/get_documents/")
                .then(response => response.json())
                .then(data => {
                    data.documents.forEach(doc => {
                        const row = docList.insertRow();
                        row.classList.add('document-row');
                        row.insertCell(0).textContent = doc.document_id;
                        row.insertCell(1).textContent = doc.template_id;
                        row.insertCell(2).textContent = doc.user_id;

                        try {
                            const dataContent = doc.data_content === 'undefined' ? {} : JSON.parse(doc.data_content.replace(/^"/, '').replace(/"$/, '').replace(/\\"/g, '"'));
                            const cell = row.insertCell(3);

                            if (typeof dataContent === 'object' && dataContent !== null) {
                                for (const key in dataContent) {
                                    if (dataContent.hasOwnProperty(key)) {
                                        const p = document.createElement('p');
                                        if (key.includes('Image') && dataContent[key].filename) {
                                            const img = document.createElement('img');
                                            img.src = 'http://localhost/DTT/uploads/' + dataContent[key].filename;
                                            img.alt = key;
                                            img.style.maxWidth = '100px';
                                            img.style.maxHeight = '100px';
                                            cell.appendChild(img);
                                        } else {
                                            p.textContent = key + ': ' + dataContent[key];
                                            cell.appendChild(p);
                                        }
                                    }
                                }
                            } else {
                                cell.textContent = doc.data_content;
                            }


                            /*  Delete Button */

                            const actionCell = row.insertCell(4);
                            const deleteButton = document.createElement('button');
                            deleteButton.classList.add('btn', 'btn-danger');
                            deleteButton.textContent = 'Delete';
                            deleteButton.addEventListener('click', () => {
                                const confirmDelete = confirm('Are you sure you want to delete this document?');
                                if (confirmDelete) {
                                    fetch(`/DTT/delete_document?id=${doc.document_id}`, {
                                        method: 'DELETE',
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                successAlert.style.display = "block";
                                                successAlert.textContent = "Delete successfuly"
                                                setTimeout(() => {
                                                    location.reload();
                                                }, 800);
                                            } else {
                                                successAlert.style.display = "block";
                                                successAlert.textContent = "Delete successfuly";
                                                successAlert.classList.add('alert-danger')
                                                setTimeout(() => {
                                                    location.reload();
                                                }, 800);
                                            }
                                        })
                                        .catch(error => console.error('Error deleting document:', error));

                                }
                            });
                            actionCell.appendChild(deleteButton);


                            //** Update Button  */
                            const updateButton = document.createElement('button');
                            updateButton.classList.add('btn', 'btn-primary', 'ml-2');
                            updateButton.textContent = 'Update';
                            updateButton.setAttribute('data-bs-toggle', 'modal');
                            updateButton.setAttribute('data-bs-target', '#updateModal');
                            updateButton.addEventListener('click', () => {
                                openUpdateModal(doc);
                            });
                            actionCell.appendChild(updateButton);

                        } catch (error) {
                            console.error("Error parsing data_content:", error);
                        }
                    });
                }).catch(error => console.error("Failed to fetch documents:", error));
        });
    </script>
</body>

</html>