<?php
    session_start();
    require_once "db/connection.php";

    if(isset($_SESSION['username'])){
        header("Location: home");
    }
    if(isset($_POST['login'])){
        # atrodam lietotāju
        $query = $datubaze->prepare('
            SELECT *
            FROM lietotajs
            WHERE lietotajvards = ?
        ');
        $query->bind_param('s',$_POST['username']);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows == 0){
            # Autorizācijas kļūdās jāizvairās no jebkādas lietotāja informācijas izpaušanas! Pat, ja konkrētais lietotājs neeksistē
            $error = 'Nepareizs lietotājvārds un/vai parole!';
        }else{

            $lietotajs = $result->fetch_object();

            if(password_verify($_POST['password'],$lietotajs->parole)){
                $_SESSION['username'] = $lietotajs->lietotajvards;
                $_SESSION['email'] = $lietotajs->epasts;
                $_SESSION['phone'] = $lietotajs->tel_nr;
                $_SESSION['role'] = $lietotajs->loma;

                header('Location: home');
            }else{
                # nepareiza parole
                $error = 'Nepareizs lietotājvārds un/vai parole!';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorizēties</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="index" class="navbar-brand">Sākums</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
<div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Autorizēties</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php
                                if(isset($error)){
                                    echo "
                                    <div class='alert alert-warning' role='alert'>
                                        $error
                                    </div>";
                                }
                            ?>
                            <div class="mb-3">
                                <label for="username" class="form-label">Lietotājvārds</label>
                                <input type="text" class="form-control" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Parole</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="login">Autorizēties</button>
                            <a class="btn btn-secondary" href="register">Reģistrēties</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>