<!DOCTYPE html>
<html lang="en">


<head>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <form method="post" action="/DTT/">
                <div class="form-group col-md-6 mx-auto">
                    <label for="exampleInputEmail1">UserName</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="username"
                        aria-describedby="emailHelp" placeholder="Enter username">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your username with anyone
                        else.</small>
                </div>
                <div class="form-group col-md-6 mx-auto">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password"
                        placeholder="Password">
                </div>
                <div class="form-group col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>