<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../private/connection.php';
    // Pārbaudam, vai lietotājs ir autorizējies
    if(!isset($_SESSION['username'])){
        header("Location: ../login");
    }
    if($_SESSION['role'] != 'admin'){
        header("Location: ../");
    }
    $lietotaji = $datubaze->query('
        SELECT lietotajvards, epasts, tel_nr, loma, registrejies
        FROM lietotajs
    ');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mani saraksti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- Navigācija -->
    <?php include "modules/nav.php"; ?>

    <div class="container card mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Lietotājvārds</th>
                    <th scope="col">E-pasts</th>
                    <th scope="col">Tel. nr.</th>
                    <th scope="col">Loma</th>
                    <th scope="col">Reģistrācijas datums</th>
                    <th scope="col">Rediģēt</th>
                </tr>
            </thead>
            <tbody>
                <?php while($lietotajs = $lietotaji->fetch_object()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lietotajs->lietotajvards);?></td>
                        <td><?php echo htmlspecialchars($lietotajs->epasts);?></td>
                        <td><?php echo htmlspecialchars($lietotajs->tel_nr);?></td>
                        <td><?php echo htmlspecialchars($lietotajs->loma);?></td>
                        <td>
                            <?php echo date_format(date_create($lietotajs->registrejies), "d.m.Y H:i");?>
                        </td>
                        <td>
                            <a href="user?username=<?php echo htmlspecialchars($lietotajs->lietotajvards);?>" 
                            class="btn btn-primary">
                                rediģēt
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
