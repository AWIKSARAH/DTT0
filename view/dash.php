<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard For Admin</title>
</head>

<body>
    <?php
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];
        echo "Hello, $username! , This is Your Dashboard";
    }
    ?> 
</body>

</html> -->

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
                    <label for="field-name">Field Name</label>
                    <input type="text" class="form-control" id="field-name" placeholder="Enter field name">
                </div>
                <div class="form-group col-md-6">
                    <label for="field-type">Field Type</label>
                    <select class="form-control" id="field-type">
                        <option value="text">Text</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="select">Select</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="file-accept">File Accept</label>
                    <input type="text" class="form-control" id="file-accept" placeholder="image/*">
                </div>
                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-primary" id="add-field">Add Field</button>
                </div>
            </form>
        </div>

        <div id="fields-container"></div>

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
                    field.options = options;
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

            function updateFieldsContainer() {
                var html = '<div class="row">';
                if (fields.length > 0) {
                    html += '<div class="col-md-6">';
                    html += '<h3>Fields:</h3>';
                    html += '<ul>';
                    fields.forEach(function (field) {
                        html += '<li>' + field.field_name + ' - ' + field.field_type + '</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                }
                if (fields.length > 0 && (fields[fields.length - 1].field_type === 'radio' || fields[fields.length - 1].field_type === 'checkbox' || fields[fields.length - 1].field_type === 'select')) {
                    html += '<div class="col-md-6">';
                    html += '<h3>Options:</h3>';
                    html += '<div class="option">';
                    html += '<input type="text" class="form-control" placeholder="Option">';
                    html += '<button type="button" class="btn btn-secondary add-option">Add Option</button>';
                    html += '</div>';
                    html += '</div>';
                }
                html += '</div>';
                $('#fields-container').html(html);
            }

            function updateJsonView() {
                var jsonText = JSON.stringify(fields, null, 2); // 2 spaces indentation
                $('#json-view').text(jsonText);
            }

            $('#fields-container').on('click', '.add-option', function () {
                var $optionInput = $('<input type="text" class="form-control" placeholder="Option">');
                $('.option').append($optionInput);
            });

            
        });
    </script>
</body>

</html>