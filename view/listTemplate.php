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

    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Template</h5>
                    <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="formContainer">
                    </div>
                    <button id="addFieldButton" class="btn btn-secondary">Add Field</button>
                </div>
                <div class="row">
                    <form>
                        <div id="add-field-form" style="display: none;">
                            <div class="form-group">
                                <label for="new-field-name">Field Name</label>
                                <input type="text" class="form-control" id="new-field-name"
                                    placeholder="Enter field name">
                                <label for="field-required">Required</label>
                                <input type="checkbox" class="form-check-input" id="field-required">
                            </div>

                            <div class="form-group">
                                <label for="new-field-type">Field Type</label>
                                <select class="form-control" id="new-field-type">
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
                            <div class="form-group" id="file-accept-container-new" style="display: none;">
                                <label for="new-file-accept">File Accept</label>
                                <select class="form-control" id="new-file-accept">
                                    <option value="image/*">Images</option>
                                    <option value=".pdf">PDF</option>
                                </select>
                            </div>
                            <div class="form-group" id="field-option-new" style="display: none;">
                                <label>Options</label>
                                <div id="options-container">
                                    <input type="text" class="form-control" id="new-option" placeholder="Enter option">
                                    <button class="btn btn-secondary" id="add-option" type="button">Add Option</button>
                                </div>
                            </div>



                            <div class="form-group col-md-6">
                                <button type="button" class="btn btn-primary" id="save-template">Save Template</button>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="close">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../js/template.js"></script>


</body>

</html>