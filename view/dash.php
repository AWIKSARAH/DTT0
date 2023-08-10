<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
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
                    <label for="field-name">Field Name</label>
                    <input type="text" class="form-control" id="field-name" placeholder="Enter field name"
                        aria-required="true">
                </div>
                <div class="form-group col-md-6">
                    <label for="field-type">Field Type</label>
                    <select class="form-control field-type" id="field-type">
                        <option value="text">Text</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="radio">Number</option>
                        <option value="select">Select</option>
                        <option value="file">File</option>
                        <option value="country">Country</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="file-accept-container">File Accept</label>
                    <select class="form-control" id="file-accept-container" class="file-accept-container"
                        style="display:none">
                        <option value="image/*">Images</option>
                        <option value="audio/*">Audio</option>
                        <option value="video/*">Video</option>
                        <option value=".pdf">PDF</option>
                        <option value=".txt,.csv">Text and CSV</option>
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
        <div id="field-option">hiii</div>

        <h3>JSON View:</h3>
        <pre id="json-view"></pre>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            var fields = [];

            $('#add-field').on('click', function () {
                var fieldName = $('#field-name').val();
                var fieldType = $('#field-type').val();
                var field = {
                    field_name: fieldName,
                    field_type: fieldType
                };

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
                    field.type = 'file';
                    var fileAccept = $('#file-accept').val();
                    field.fileAccept = fileAccept;
                }

                fields.push(field);
                updateFieldsContainer();
                updateJsonView();
            });
            $("select").change(function () {
                var selectedFieldType = $(this).val();
                var html = '';

                if (selectedFieldType === 'radio' || selectedFieldType === 'checkbox' || selectedFieldType === 'select') {
                    html += '<div class="col-md-6">';
                    html += '<h3>Options:</h3>';
                    html += '<div class="option">';
                    if (fields[fields.length - 1].options) {
                        fields[fields.length - 1].options.forEach(function (option) {
                            html += '<input type="text" class="form-control" placeholder="Option" value="' + option + '">';
                        });
                    } else {
                        html += '<input type="text" class="form-control" placeholder="Option">';
                    }
                    html += '<button type="button" class="btn btn-secondary add-option">Add Option</button>';
                    html += '</div>';
                    html += '</div>';
                }
                else if (selectedFieldType === 'file') {
                    // Show the "File Accept" field when the field type is "File"
                    $('#file-accept-container').show();
                } else {
                    // Hide the "File Accept" field for other field types
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
                    html += '<h2>Document Name: ' + fields[0].document_name + '</h2>';
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
                var documentName = $('#Document-Name').val();
                var jsonData = {
                    template_name: documentName,
                    template_structure: fields
                };
                if (!documentName || !fields) {
                    alert("You can't save an empty template.");
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "/DTT/save_template/",
                    data: jsonData,
                    dataType: 'json', // Set the expected response data type to JSON
                })
                    .always(function (responseOrXhr, status, error) {
                        console.log('====================================');
                        console.log(responseOrXhr);
                        console.log('====================================');
                        if (responseOrXhr.status === 200) {
                            alert("Data saved successfully.");

                        } else {
                            console.error(responseOrXhr);
                            alert("An error occurred. Please check the console for more details.");
                        }
                    });
            });

        });
    </script>
</body>

</html>