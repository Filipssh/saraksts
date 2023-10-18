<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '../private/connection.php';

    if(empty($_SESSION['username'])){
        header('Location: ../login');
    }

    if($_SESSION['role'] != 'admin'){
        header('Location: ../');
    }

    // struct - datu struktūra
    class lietotajs {
        public $lietotajvards;
        public $epasts;
        public $tel_nr;
        public $loma;
    }

    // Atlasīt lietotāju no GET 
    if(isset($_GET['username'])){
        $query = $datubaze->prepare('
            SELECT lietotajvards, epasts, tel_nr, loma
            FROM lietotajs
            WHERE lietotajvards = ?
        ');
        $query->bind_param('s', $_GET['username']);
        $query->execute();
        $result = $query->get_result();
        if($result->num_rows > 0){
            $lietotajs = $result->fetch_object();
        }
    }

    if(empty($lietotajs)){
        $lietotajs = new lietotajs;
        $lietotajs->lietotajvards = $_SESSION['username'];
        $lietotajs->epasts = $_SESSION['email'];
        $lietotajs->tel_nr = $_SESSION['phone'];
        $lietotajs->loma = $_SESSION['role'];
    }

    $admin_skaits = $datubaze->query('
        SELECT COUNT(*) AS skaits
        FROM lietotajs
        WHERE loma = "admin"
    ');
    $admin_skaits = $admin_skaits->fetch_object();
    $admin_skaits = $admin_skaits->skaits;

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
            $query->bind_param('ss', $_POST['username'], $lietotajs->lietotajvards);
            $query->execute();

            $lietotajs->lietotajvards = $_POST['username'];
        }else{
            $error = "Šis lietotājvārds ir jau aizņemts.";
        }
    }

    if(isset($_POST['submit-email'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET epasts = ?
            WHERE lietotajvards = ?
        ');
        $query->bind_param('ss', $_POST['email'], $lietotajs->lietotajvards);
        $query->execute();

        $lietotajs->epasts = $_POST['email'];
    }

    if(isset($_POST['submit-phone'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET tel_nr = ?
            WHERE lietotajvards = ?
        ');
        $query->bind_param('ss', $_POST['phone'], $lietotajs->lietotajvards);
        $query->execute();

        $lietotajs->tel_nr = $_POST['phone'];
    }

    if(isset($_POST['submit-password'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET parole = ?
            WHERE lietotajvards = ?
        ');
        $parole_new = password_hash($_POST['password-new'], PASSWORD_ARGON2I);
        $query->bind_param('ss', $parole_new, $lietotajs->lietotajvards);
        $query->execute();
    }

    if(isset($_POST['submit-role'])){
        $query = $datubaze->prepare('
            UPDATE lietotajs SET loma = ?
            WHERE lietotajvards = ?
        ');
        $query->bind_param('ss',$_POST['role'], $lietotajs->lietotajvards);
        $query->execute();

        $lietotajs->loma = $_POST['role'];
    }

    if(isset($_POST['delete'])){
        $query = $datubaze->prepare('
            DELETE FROM ieraksts 
            WHERE saraksts_id IN ( 
                SELECT id FROM saraksts WHERE lietotajvards = ?
            )
        ');
        $query->bind_param('s', $lietotajs->lietotajvards);
        $query->execute();

        $query = $datubaze->prepare('
            DELETE FROM saraksts WHERE lietotajvards = ?
        ');
        $query->bind_param('s', $lietotajs->lietotajvards);
        $query->execute();

        $query = $datubaze->prepare('
            DELETE FROM lietotajs
            WHERE lietotajvards = ?
        ');
        $query->bind_param('s', $lietotajs->lietotajvards);
        $query->execute();

        header('Location: users');
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
                            value="<?php echo htmlspecialchars($lietotajs->lietotajvards); ?>" required>
                            <button class="btn btn-primary" name="submit-username">Saglabāt</button>
                        </div>
                    </div>
                </form>
                
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="role" class="form-label">Loma</label>
                        <div class="input-group">
                            <select id="role" name="role" class="form-select"
                            <?php if( $lietotajs->lietotajvards == $_SESSION['username'])
                                echo 'disabled';
                            ?>
                            >
                                <option value="admin" 
                                    <?php 
                                        if($lietotajs->loma == 'admin')
                                            echo "selected";
                                    ?> 
                                >Admin</option>
                                <option value="lietotajs" 
                                    <?php 
                                        if($lietotajs->loma == 'lietotajs')
                                            echo "selected";
                                    ?> 
                                >Lietotājs</option>
                            </select>
                            <button class="btn btn-primary" name="submit-role">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="email" class="form-label">E-pasta adrese</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo htmlspecialchars($lietotajs->epasts); ?>" required>
                            <button class="btn btn-primary" name="submit-email">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="phone" class="form-label">Tel.nr.</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($lietotajs->tel_nr); ?>" required>
                            <button class="btn btn-primary" name="submit-phone">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3 card" action="" method="POST">
                    <div class="card-body">
                        <label for="password-new" class="form-label">Jaunā parole</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password-new" name="password-new" required>
                            <button class="btn btn-primary" name="submit-password">Saglabāt</button>
                        </div>
                    </div>
                </form>
                <form class="mb-3" action="" method="POST">
                    <button class="btn btn-danger" name="delete" 
                    <?php if($admin_skaits == 1 && $lietotajs->loma == 'admin')
                        echo 'disabled';
                    ?>
                    >Dzēst kontu</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>