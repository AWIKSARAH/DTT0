<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <title>Template List</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Template List</h2>
                <div id="success-message" class="alert alert-success d-none" role="alert">
                    Template deleted successfully.
                </div>
                <div class=" btn col-md-6  createTemplate">
                    <div class="col-md-6">
                        <label for="typeSelect">Select Type: </label>
                        <select class="form-control" id="typeSelect">
                            <option name="" id="">Select
                                Type</option>
                        </select>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="template-list">
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const templateList = document.getElementById("template-list");
            const successMessage = document.getElementById("success-message");
            const typeSelect = document.getElementById("typeSelect");
            function deleteTemplate(templateId) {
                $.ajax({
                    url: `/DTT/delete_template?id=${templateId}`,
                    method: "DELETE",
                    dataType: "json",
                    success: function (data) {
                        if (data.success) {
                            const row = document.querySelector(`[data-template-id="${templateId}"]`);
                            if (row) {
                                row.remove();
                                showSuccessMessage();
                            }
                        } else {
                            console.error("Failed to delete template:", data.error);
                        }
                    },
                    error: function (error) {
                        console.error("An error occurred:", error.responseText);
                    }
                });
            }
            function fetchTypes() {
                fetch("/DTT/get_types/")
                    .then(response => response.json())
                    .then(data => {
                        data.types.forEach(type => {
                            const option = document.createElement("option");
                            option.value = type.type_id;
                            option.textContent = type.type_name;
                            typeSelect.appendChild(option);
                        });

                        typeSelect.addEventListener("change", () => {
                            const selectedType = typeSelect.value;
                            updateTemplateList(selectedType);
                        });

                        const initialSelectedTypeId = typeSelect.value;
                        if (initialSelectedTypeId !== "") {
                            updateTemplateList(initialSelectedTypeId);
                        }
                    })
                    .catch(error => console.error("Failed to fetch types:", error));
            }

            function updateTemplateList(selectedTypeId) {
                fetch(`/DTT/get_templates_by_type?type_id=${selectedTypeId}`)
                    .then(response => response.json())
                    .then(data => {
                        templateList.innerHTML = "";

                        if (data && data.templates && Array.isArray(data.templates) && data.templates.length > 0) {
                            data.templates.forEach(template => {
                                const row = templateList.insertRow();
                                row.dataset.templateId = template.template_id;
                                row.insertCell(0).textContent = template.template_name;
                                row.insertCell(1).innerHTML = '<button class="btn btn-danger deleteTemplate" data-id="' + template.template_id + '">Delete</button>';
                            });

                            const deleteButtons = document.querySelectorAll(".deleteTemplate");
                            deleteButtons.forEach(button => {
                                button.addEventListener("click", function () {
                                    const templateId = button.getAttribute("data-id");
                                    if (confirm("Are you sure you want to delete this template?")) {
                                        deleteTemplate(templateId);
                                    }
                                });
                            });
                        } else {
                            const noTemplatesRow = templateList.insertRow();
                            const noTemplatesCell = noTemplatesRow.insertCell();
                            noTemplatesCell.colSpan = 2;
                            noTemplatesCell.textContent = "No templates available for this type.";
                        }
                    })
                    .catch(error => console.error("Failed to fetch templates by type:", error));
            }

            fetchTypes();

            const initialSelectedTypeId = typeSelect.value;
            if (initialSelectedTypeId !== "") {
                updateTemplateList(initialSelectedTypeId);
            }
        });

    </script>


</body>

</html>