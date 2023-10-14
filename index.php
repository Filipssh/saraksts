<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "modules/nav.php"; ?>
    <div id="fons">
        <iframe src="p5js/index.html" frameborder="0" scrolling="no"></iframe>

        <div id="sakums" class="container mt-4">
            <div id="call-to-action">
                <h1>Izveido sarakstu jau tagad!</h1>
                <a href="register" class="btn btn-primary">Reģistrēties</a>
            </div>
        </div>
    </div>
</body>
</html>