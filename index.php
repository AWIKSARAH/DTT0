<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PHP App</title>
</head>

<body>
    <header>
        <?php include "view/header.php"; ?>
    </header>

    <main>
        <?php
        
        require __DIR__ . '/route.php';
        ?>
    </main>

    <footer>
        <?php include "view/footer.php"; ?>
    </footer>
</body>

</html>