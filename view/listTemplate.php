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



            fetch("/DTT/get_templates/")
                .then(response => response.json())
                .then(data => {
                    data.templates.forEach(template => {
                        const row = templateList.insertRow();
                        row.insertCell(0).textContent = template.template_name;
                        row.insertCell(1).innerHTML = '<button class="btn btn-danger deleteTemplate" data-id="' + template.template_id + '">Delete</button> <button class="btn btn-primary update-template" data-id="' + template.template_id + '">Update</button>';
                    });
                })
                .catch(error => console.error("Failed to fetch templates:", error));



        });

    </script>
</body>

</html>