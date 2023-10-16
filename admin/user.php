<?php
    session_start();
    require_once "../../../../private/connection.php";

    if(empty($_SESSION['username'])){
        header('Location: login');
    }

    if(isset($_POST['submit-username'])){
        $query = $datubaze->prepare('
            SELECT lietotajvards
            FROM lietotajs
            WHERE lietotajvards = ?
        ');
        $query->bind_param('s', $_POST['username']);
        $query->execute();
        $res = $query->get_result();

        if($res->num_rows == 0){
            $query = $datubaze->prepare('
                UPDATE lietotajs SET lietotajvards = ?
                WHERE lietotajvards = ?
            ');
            $query->bind_param('ss', $_POST['username'], $_SESSION['username']);
            $query->execute();

            $_SESSION['username'] = $_POST['username'];
        }else{
            $error = "Šis lietotājvārds ir jau aizņemts.";
        }
    }

    if(isset($_POST['submit-email'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET epasts = ?
            WHERE lietotajvards = ?
        ');
        $query->bind_param('ss', $_POST['email'], $_SESSION['username']);
        $query->execute();

        $_SESSION['email'] = $_POST['email'];
    }

    if(isset($_POST['submit-phone'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET tel_nr = ?
            WHERE lietotajvards = ?
        ');
        $query->bind_param('ss', $_POST['phone'], $_SESSION['username']);
        $query->execute();

        $_SESSION['phone'] = $_POST['phone'];
    }

    if(isset($_POST['submit-password'])){
        $query = $datubaze->prepare('
            SELECT parole
            FROM lietotajs
            WHERE lietotajvards = ?
        ');
        $query->bind_param('s', $_SESSION['username']);
        $query->execute();
        $res = $query->get_result();
        $parole = $res->fetch_object();

        if(password_verify($_POST['password'],$parole->parole)){
            $query = $datubaze->prepare('
                UPDATE lietotajs SET parole = ?
                WHERE lietotajvards = ?
            ');
            $parole_new = password_hash($_POST['password-new'], PASSWORD_ARGON2I);
            $query->bind_param('ss', $parole_new, $_SESSION['username']);
            $query->execute();
        }else{
            $error = "Nepareiza parole.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <?php include "modules/nav.php"; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                    if(isset($error)){
                        echo "
                        <div class='alert alert-warning' role='alert'>
                            $error
                        </div>";
                    }
                ?>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="username" class="form-label">Lietotājvārds</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" name="username" 
                            value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                            <button class="btn btn-primary" name="submit-username">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="email" class="form-label">E-pasta adrese</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                            <button class="btn btn-primary" name="submit-email">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="phone" class="form-label">Tel.nr.</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($_SESSION['phone']); ?>" required>
                            <button class="btn btn-primary" name="submit-phone">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="password" class="form-label">Parole</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <label for="password-new" class="form-label">Jaunā parole</label>
                        <input type="password" class="form-control mb-3" id="password-new" name="password-new" required>
                        <button class="btn btn-primary" name="submit-password">Saglabāt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>