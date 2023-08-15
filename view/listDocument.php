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
                    <form id="dynamicForm">
                        <div id="formContainer">
                        </div>
                        <button id="submitButton" style="display:none" class="btn btn-primary">Submit</button>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="close">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const successAlert = document.getElementById("success-alert");
            const updateInput = document.getElementById("updateInput");
            const updateModal = document.getElementById("updateModal");
            const updateModalLabel = document.getElementById("updateModalLabel");
            const updateSubmit = document.getElementById("updateSubmit");
            const updateForm = document.getElementById("updateForm");
            let currentUpdatingDocument;

            const docList = document.getElementById("document-list");

            fetch("/DTT/get_documents/")
                .then((response) => response.json())
                .then((data) => {
                    data.documents.forEach((doc) => {
                        const row = docList.insertRow();
                        row.classList.add("document-row");
                        row.insertCell(0).textContent = doc.document_id;
                        row.insertCell(1).textContent = doc.template_id;
                        row.insertCell(2).textContent = doc.user_id;

                        try {
                            const dataContent =
                                doc.data_content === "undefined"
                                    ? {}
                                    : JSON.parse(
                                        doc.data_content
                                            .replace(/^"/, "")
                                            .replace(/"$/, "")
                                            .replace(/\\"/g, '"')
                                    );
                            const cell = row.insertCell(3);

                            if (typeof dataContent === "object" && dataContent !== null) {
                                for (const key in dataContent) {
                                    if (dataContent.hasOwnProperty(key)) {
                                        const p = document.createElement("p");
                                        if (key.includes("Image") && dataContent[key].filename) {
                                            const img = document.createElement("img");
                                            img.src =
                                                "http://localhost/DTT/uploads/" + dataContent[key].filename;
                                            img.alt = key;
                                            img.style.maxWidth = "100px";
                                            img.style.maxHeight = "100px";
                                            cell.appendChild(img);
                                        } else {
                                            p.textContent = key + ": " + dataContent[key];
                                            cell.appendChild(p);
                                        }
                                    }
                                }
                            } else {
                                cell.textContent = doc.data_content;
                            }

                            /*  Delete Button */

                            const actionCell = row.insertCell(4);
                            const deleteButton = document.createElement("button");
                            deleteButton.classList.add("btn", "btn-danger");
                            deleteButton.textContent = "Delete";
                            deleteButton.addEventListener("click", () => {
                                const confirmDelete = confirm(
                                    "Are you sure you want to delete this document?"
                                );
                                if (confirmDelete) {
                                    fetch(`/DTT/delete_document?id=${doc.document_id}`, {
                                        method: "DELETE",
                                    })
                                        .then((response) => response.json())
                                        .then((data) => {
                                            if (data.success) {
                                                successAlert.style.display = "block";
                                                successAlert.textContent = "Delete successfuly";
                                                setTimeout(() => {
                                                    location.reload();
                                                }, 800);
                                            } else {
                                                successAlert.style.display = "block";
                                                successAlert.textContent = "Delete successfuly";
                                                successAlert.classList.add("alert-danger");
                                                setTimeout(() => {
                                                    location.reload();
                                                }, 800);
                                            }
                                        })
                                        .catch((error) =>
                                            console.error("Error deleting document:", error)
                                        );
                                }
                            });
                            actionCell.appendChild(deleteButton);

                            //** Update Button  */
                            const updateButton = document.createElement("button");
                            updateButton.classList.add("btn", "btn-primary", "ml-2");
                            updateButton.textContent = "Update";

                            updateButton.setAttribute("data-bs-toggle", "modal");
                            updateButton.setAttribute("data-bs-target", "#updateModal");
                            updateButton.addEventListener("click", () => {
                                fetchTemplateData(doc.document_id);

                                console.log(doc);
                            });
                            actionCell.appendChild(updateButton);
                        } catch (error) {
                            console.error("Error parsing data_content:", error);
                        }
                    });
                })
                .catch((error) => console.error("Failed to fetch documents:", error));
        });

    </script>

    <script src="../js/document.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>