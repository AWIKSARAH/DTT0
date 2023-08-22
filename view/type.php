<!DOCTYPE html>
<html>

<head>
    <title>Type CRUD Application</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Type List</h2>
        <button id="showCreateFormBtn" class="btn btn-primary ml-2">Add Type</button>

        <h2>Create New Type</h2>
        <form id="createTypeForm">
            <div class="form-group">
                <label for="typeName">Type Name:</label>
                <input type="text" class="form-control" id="typeName" placeholder="Enter type name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Type</button>
        </form>
        <div id="typeList"></div>
    </div>
    <!-- EDIT Modal -->


    <div class="modal fade" id="editTypeModal" tabindex="-1" aria-labelledby="editTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTypeModalLabel">Edit Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="type_name" class="type_name" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="updateTypeBtn" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>







<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    $(document).ready(function () {
        loadTypeList();
        $('#createTypeForm').hide();

        $('#showCreateFormBtn').click(function () {
            $('#createTypeForm').toggle();
        });

        $('#createTypeForm').submit(function (event) {
            event.preventDefault();

            const typeName = $('#typeName').val();
            createType(typeName);
        });

        $('#updateTypeBtn').click(function () {
            const updatedTypeName = $('#type_name').val();
            const typeId = $('#editTypeModal').data('type-id');
            updateType(typeId, updatedTypeName);
        });

    });


    function createType(typeName) {
        $.ajax({
            url: '/DTT/add_type',
            method: 'POST',
            dataType: 'json',
            data: { type_name: typeName },
            success: function (data) {
                if (data.success) {
                    $('#typeName').val('');
                    $('#createTypeForm').hide();
                    loadTypeList();
                    showSuccessMessage('Type added successfully !!!.');
                } else {
                    console.error('Failed to add type:', data.message);
                }
            },
            error: function (error) {
                console.error('An error occurred:', error.responseText);
            }
        });
    }
    function loadTypeList() {
        fetch('/DTT/get_types')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch types.');
                }
                return response.json();
            })
            .then(data => {
                displayTypes(data.types);
            })
            .catch(error => {
                alert(error.message);
            });
    }

    function deleteType(id) {
        if (confirm("Are you sure you want to delete this type?")) {
            $.ajax({
                url: `/DTT/delete_type?id=${id}`,
                method: "DELETE",
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        const card = $(`[data-type-id="${id}"]`);
                        if (card) {
                            card.remove();
                            showSuccessMessage();
                        }
                    } else {
                        console.error("Failed to delete type:", data.error);
                    }
                },
                error: function (error) {
                    console.error("An error occurred:", error.responseText);
                }
            });
        }
    }



    function editType(typeId, typeName) {
        $('#type_name').val(typeName);
        $('#editTypeModal').data('type-id', typeId);


    }

    function updateType(typeId, typeName) {
        $.ajax({
            url: '/DTT/update_type',
            method: 'POST',
            dataType: 'json',
            data: { type_id: typeId, type_name: typeName },
            success: function (data) {
                if (data.success) {
                    $('#editTypeModal').modal('hide');
                    loadTypeList();
                    showSuccessMessage('Type updated successfully.');
                } else {
                    console.error('Failed to update type:', data.message);
                }
            },
            error: function (error) {
                console.error('An error occurred:', error.responseText);
            }
        });
    }
    function displayTypes(types) {
        var typeListContainer = $('#typeList');
        typeListContainer.empty();

        types.forEach(function (type) {
            var card = $('<div class="card mb-3">');
            card.attr('data-type-id', type.type_id);

            var cardBody = $('<div class="card-body">');
            var cardTitle = $('<h5 class="card-title">').text(type.type_name);
            var editLink = $('<button type="button" class="btn btn-primary ml-2">Edit</button>');
            editLink.attr('data-bs-toggle', 'modal');
            editLink.attr('data-bs-target', '#editTypeModal');
            editLink.on('click', function () {
                editType(type.type_id, type.type_name);
            });

            var deleteLink = $('<a href="#">Delete</a>').addClass('btn btn-danger ml-2');
            deleteLink.on('click', function () {
                deleteType(type.type_id);
            });

            cardBody.append(cardTitle, editLink, deleteLink);
            card.append(cardBody);
            typeListContainer.append(card);
        });
    }


    function showSuccessMessage() {

        const successMessage = $('<div>').addClass('alert alert-success mt-2').text('Type deleted successfully.');
        $('.container').append(successMessage);
        setTimeout(function () {
            successMessage.remove();
        }, 3000);
    }
</script>
</body>

</html>