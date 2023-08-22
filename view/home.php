<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Welcome Please fill the document
            <?php
            echo $_SESSION["username"]; ?>
        </h1>
        <div class="form-group">
            <label for="templateSelect">Select Template:</label>
            <select class="form-control" id="templateSelect">
                <option selected disabled>Choose one</option>
            </select>


        </div>
        <form id="dynamicForm">
            <div id="formContainer">
            </div>
            <button id="submitButton" style="display:none" class="btn btn-primary">Submit</button>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/home.js"></script>
</body>

</html>