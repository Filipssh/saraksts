<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../private/connection.php';
    // Pārbaudam, vai lietotājs ir autorizējies
    if(!isset($_SESSION['username'])){
        header("Location: login");
    }

    if(isset($_POST['delete'])){
        $query = $datubaze->prepare('
        SELECT *
        FROM saraksts
        WHERE id = ?
        ');
        $query->bind_param('i',$_POST['id']);
        $query->execute();
        $result = $query->get_result();
        $saraksts = $result->fetch_object();

        if($saraksts->lietotajvards == $_SESSION['username']){
            $query = $datubaze->prepare("
                DELETE FROM ieraksts WHERE saraksts_id = ?
            ");
            $query->bind_param('i', $_POST['id']);
            $query->execute();
            $query = $datubaze->prepare("
                DELETE FROM saraksts WHERE id = ?
            ");
            $query->bind_param('i', $_POST['id']);
            $query->execute();
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mani saraksti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navigācija -->
    <?php include "modules/nav.php"; ?>

    <div class="container mt-4">
        <div class="row">
            <?php 
                // Atrodam visus lietotāja sarakstus
                $query = $datubaze->prepare('
                    SELECT *
                    FROM saraksts
                    WHERE lietotajvards = ?
                ');
                $query->bind_param('s', $_SESSION['username']);
                $query->execute();
                $saraksti = $query->get_result();

                // Sagatavojam vaicājumu lai atlasītu pirmos piecus ierakstus no saraksta
                $query2 = $datubaze->prepare('
                    SELECT *
                    FROM ieraksts
                    WHERE saraksts_id = ? LIMIT 5
                ');

                // Izvadam visus sarakstus
                while($saraksts = $saraksti->fetch_object()):
            ?>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($saraksts->nosaukums); ?></h5>
                    <p class="card-text">
                        <?php 
                            // iegūstam konkrētā saraksta ierakstus, izmantojot iepriekš sagatavoto vaicājumu
                            $query2->bind_param('i',$saraksts->id);
                            $query2->execute();
                            $ieraksti = $query2->get_result();
                            // izvadam visus ierakstus
                            while($ieraksts = $ieraksti->fetch_object()){
                                // ja ieraksts ir izsvītrots, tad pievienojam klasi, kas to izsvītro
                                $klase = "class=\"text-decoration-line-through\"";
                                $klase = ($ieraksts->izsvitrots == 1) ? $klase : '';
                                echo "<span " . $klase . ">" .  htmlspecialchars($ieraksts->teksts) . "</span><br>";
                            }
                        ?>
                    </p>
                    <!-- Pārvirzām uz saraksta lapu, nododot saraksta id kā GET parametru -->
                    <div class="row">
                        <div class="col-6">
                            <a href="list?id=<?php echo  htmlspecialchars($saraksts->id) ?>" class="btn btn-primary">Apskatīt</a>
                        </div>
                        <div class="col-6">
                            <form action="" method="POST">
                                <input type="text" name="id" style="display:none" value="<?php echo  htmlspecialchars($saraksts->id) ?>">
                                <button type="submit" name="delete" class="btn btn-outline-danger">Dzēst</button>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <?php endwhile; ?>

        </div>

        <div class="fixed-bottom m-3 d-grid gap-2 d-md-flex justify-content-md-end">
            <a class="btn btn-primary me-md-2" href="create_list" >+Jauns saraksts</a>
        </div>
    </div>
</body>
</html>