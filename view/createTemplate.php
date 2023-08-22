<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <title>Template Generator</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <form>
                <div class="form-group col-md-6">
                    <label for="Document-Name">Document Name</label>
                    <input type="text" class="form-control Document-Name" id="Document-Name" aria-required="true"
                        placeholder="Enter field name">
                    <label for="type-select">Type</label>
                    <select class="form-control" id="type-select" name="type_id">
                        <option selected disabled>Choose one</option>
                    </select>
                    <label for="field-name">Field Name</label>
                    <input type="text" class="form-control" id="field-name" placeholder="Enter field name"
                        aria-required="true">
                    <label for="field-required">Required</label>
                    <input type="checkbox" class="form-check-input" id="field-required">
                </div>

                <div class="form-group col-md-6">
                    <label for="field-type">Field Type</label>
                    <select class="form-control field-type" id="field-type">
                        <option selected disabled>Choose one</option>

                        <option value="text">Text</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="date">Date</option>
                        <option value="number">Number</option>
                        <option value="select">Select</option>
                        <option value="file">File</option>
                        <option value="country">Country</option>
                        <option value="city">City</option>
                    </select>

                </div>
                <div class="form-group col-md-6">
                    <label for="file-accept-container">File Accept</label>
                    <select class="form-control" id="file-accept-container" class="file-accept-container"
                        style="display:none">
                        <option value="image/*">Images</option>
                        <option value=".pdf">PDF</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-primary" id="add-field">Add Field</button>
                </div>
                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-primary" id="save-template">Save Template</button>
                </div>
            </form>
        </div>
        <div id="fields-container"></div>
        <div id="field-option"></div>

        <h3>JSON View:</h3>
        <pre id="json-view"></pre>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            var fields = [];

            fetch('/DTT/get_types')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch types.');
                    }
                    return response.json();
                })
                .then(data => {
                    var typeSelect = $('#type-select');
                    data.types.forEach(type => {
                        typeSelect.append($('<option>', {
                            value: type.type_id,
                            text: type.type_name
                        }));
                    });
                })
                .catch(error => {
                    alert(error.message);
                });


            $('#add-field').on('click', function () {
                var fieldName = $('#field-name').val();
                var fieldType = $('#field-type').val();
                var isFieldRequired = $('#field-required').prop('checked');

                var existingField = fields.find(field => field.field_name === fieldName);
    if (existingField) {
        alert("Field name already exists. Please choose a different name.");
        return;
    }
    
                var field = {
                    field_name: fieldName,
                    field_type: fieldType,
                    required: isFieldRequired,
                    isDeleted: false

                };
                if (!fieldName) {
                    alert("Enter a Name For the field")
                }
                else {
                    if (fieldType === 'radio' || fieldType === 'checkbox' || fieldType === 'select') {
                        var options = [];
                        $('.option input').each(function () {
                            options.push($(this).val());
                        });

                        if (options.length > 0) {
                            field.options = options;
                        }
                    }

                    if (fieldType === 'file') {
                        var fileAccept = $('#file-accept-container').val();
                        field.type = fileAccept;
                    }

                    fields.push(field);
                    updateFieldsContainer();
                    updateJsonView();



                }


            });

            $("select.field-type").on('change', function () {
                var selectedFieldType = $(this).val();
                var html = '';

                if (selectedFieldType === 'radio' || selectedFieldType === 'checkbox' || selectedFieldType === 'select') {
                    var existingOptions = [];
                    var existingOptionInputs = $('.option input');
                    existingOptionInputs.each(function () {
                        existingOptions.push($(this).val());
                    });

                    html += '<div class="col-md-6">';
                    html += '<h3>Options:</h3>';
                    html += '<div class="option">';
                    if (existingOptions.length > 0) {
                        existingOptions.forEach(function (option) {
                            html += '<input type="text" class="form-control" placeholder="Option" value="' + option + '">';
                        });
                    } else {
                        html += '<input type="text" class="form-control" placeholder="Option">';
                    }
                    html += '<button type="button" class="btn btn-secondary add-option">Add Option</button>';
                    html += '</div>';
                    html += '</div>';
                } else if (selectedFieldType === 'file') {
                    $('#file-accept-container').show();
                } else {
                    $('#file-accept-container').hide();
                }

                $('#fields-container .option').remove();
                $('#fields-container').append(html);
            });

            $('#fields-container').on('click', '.add-option', function () {
                var $optionInput = $('<input type="text" class="form-control" placeholder="Option">');
                $('.option').append($optionInput);
            });

            function updateFieldsContainer() {
                var html = '<div class="row">';
                if (fields.length > 0) {
                    html += '<div class="col-md-6">';
                    html += '<h3>Fields:</h3>';
                    html += '<h2>Document Name: ' + $('#Document-Name').val() + '</h2>';
                    html += '<ul>';
                    fields.forEach(function (field) {
                        html += '<li>' + field.field_name + ' - ' + field.field_type + '</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                }
                if (fields.length > 0) {

                }
                html += '</div>';
                $('#fields-container').html(html);
            }

            function updateJsonView() {
                var jsonText = JSON.stringify(fields, null, 2);
                $('#json-view').text(jsonText);
            }

            $('#save-template').on('click', function () {
                var fieldTypeTemplate = $('#type-select').val();
                var documentName = $('#Document-Name').val();

                if (!documentName || fields.length === 0) {
                    alert("Please fill in the document name and add at least one field.");
                    return;
                }
                if (!fieldTypeTemplate) {
                    alert("Should choose a type for your Template");
                    return;
                }
                var jsonData = {
                    template_name: documentName,
                    template_structure: fields,
                    type_id: fieldTypeTemplate
                };
                $.ajax({
                    type: "POST",
                    url: "/DTT/save_template/",
                    data: JSON.stringify(jsonData),
                    contentType: "application/json",
                    dataType: 'json',

                })
                    .done(function (res) {
                        alert(res.message);
                        location.reload();

                    })
                    .fail(function (xhr, status, error) {

                        alert(xhr.responseJSON.message);
                    })




            });
        });
    </script>
</body>

</html>