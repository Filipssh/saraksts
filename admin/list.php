<?php
    session_start();
    include_once "../../../../private/connection.php";

    if(empty($_SESSION['username'])){
        header("Location: login");
    }
    if($_SESSION['role'] != 'admin'){
        header("Location: ../login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include "modules/nav.php"; ?>

    <?php 
        // Atrodam sarakstu, kuru lietotājs vēlas apskatīt
        $query = $datubaze->prepare('
            SELECT *
            FROM saraksts
            WHERE id = ?
        ');
        $query->bind_param('i',$_GET['id']);
        $query->execute();
        $result = $query->get_result();
        $saraksts = $result->fetch_object();

        // Atrodam saraksta ierakstus
        $query2 = $datubaze->prepare('
            SELECT *
            FROM ieraksts
            WHERE saraksts_id = ?
        ');

        // ja saraksts neeksistē vai lietotājs nav saraksta īpašnieks, tad izvadam atbilstošu ziņojumu
        if( empty($_GET['id']) || $result->num_rows == 0):
            include "modules/not_found.php";
        else:  
    ?>
        <div class="container mt-5">
            <h1 class="text-center">
                <span id="nosaukums"><?php echo htmlspecialchars($saraksts->nosaukums) ?></span>
                <button id="edit-nosaukums" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <img src="../edit.png">
                </button>
            </h1>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="input-group mb-3">
                        <input type="text" id="task" class="form-control" placeholder="Jauns ieraksts">
                        <button id="addTask" class="btn btn-primary">Pievienot</button>
                    </div>
                    <ul class="list-group" id="taskList">
                        <!-- Saraksta elementi tiks dinamiski ievietoti šeit -->

                        <!-- Piemērs vienam ierakstam  -->
                        <!-- <li class="list-group-item">Nopirkt ballītei balonus</li> -->
    <?php 
    $query2->bind_param('i',$saraksts->id);
    $query2->execute();
    $ieraksti = $query2->get_result();
    while($ieraksts = $ieraksti->fetch_object()):
        $klase = "text-decoration-line-through";
        $klase = ($ieraksts->izsvitrots == 1) ? $klase : '';
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?php echo $ieraksts->id?>">
        <span class="me-auto <?php echo $klase; ?>"><?php echo htmlspecialchars($ieraksts->teksts); ?></span>
        <button class="btn me-2 btn-outline-primary" data-id="<?php echo $ieraksts->id?>" data-bs-toggle="modal" data-bs-target="#exampleModal">rediģēt</button>
        <button class="btn btn-outline-danger" data-id="<?php echo $ieraksts->id?>">X</button>
    </li>
    <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
        <script>
            // saglabājam aktuālā saraksta ID kā javascript mainīgo. 
            // Tas palīdzēs ievietot jaunus ierakstus un noteikt saraksta autoru
            const saraksts_id = <?php echo $_GET['id']; ?>; 

            let teksts = document.getElementById("task");
            let poga = document.getElementById("addTask");
            let saraksts = document.getElementById("taskList");

            function ievietotSarakstā(){
                if(teksts.value.trim() != ""){
                    /** 
                     * AJAX – Asynchronous JavaScript And XML.
                     * https://www.w3schools.com/xml/ajax_intro.asp
                     * .ajax() izveido HTTP-request pieprasījumu, un gaida atbildi no servera 
                     */ 
                    $.ajax({ 
                        type:'POST',
                        url: 'db/list_item/insert.php',
                        data: {
                            teksts: teksts.value,
                            saraksts_id: saraksts_id,
                        },
                        dataType: 'json',
                        encode: true,
                    }).done(function (data) { // reaģējam uz servera atbildi
                        console.log(data);

                        if(data.response == '200'){
                            // konstruējam <li> elementu
                            let li = document.createElement("li");
                            li.classList.add(
                                "list-group-item",
                                "d-flex",
                                "justify-content-between",
                                "align-items-center"
                            );
                            // ieraksta teksts
                            let span = document.createElement('span');
                            span.classList.add("me-auto");
                            span.innerText = teksts.value;
                            li.appendChild(span);

                            // poga "rediģēt"
                            let button = document.createElement('button');
                            button.classList.add(
                                "btn",
                                "me-2",
                                "btn-outline-primary"
                            );
                            button.setAttribute('data-id', data.id);
                            button.setAttribute('data-bs-toggle', "modal");
                            button.setAttribute('data-bs-target', "#exampleModal");
                            button.innerText = "rediģēt";
                            li.appendChild(button);

                            // poga "dzēst"
                            let button2 = document.createElement('button');
                            button2.classList.add(
                                "btn",
                                "btn-outline-danger"
                            );
                            button2.setAttribute('data-id', data.id);
                            button2.innerText = "X";
                            li.appendChild(button2);

                            li.setAttribute('data-id', data.id);
                            teksts.value = '';

                            saraksts.appendChild(li);
                        }
                    });
                }
            }

            poga.addEventListener("click", ievietotSarakstā );
            teksts.addEventListener("keypress", function(e){
                if(e.key == "Enter"){
                    ievietotSarakstā();
                }
            });
            
            // "Event delegation"
            // ne visi saraksta elementi ir pieejami, kad mēs ielādējam lapu, līdz ar to nepietiek ar to, ka uzstādam event listener dokumenta ielādēšanās brīdī
            // Izmantojot jQuery, deliģējam document objektu klausīties klikšķi uz kādu saraksta elementu
            // https://learn.jquery.com/events/event-delegation/
            $(document).on('click', '#taskList li span', function(){
                $(this).toggleClass("text-decoration-line-through");

                $.ajax({
                    type:'POST',
                    url: 'db/list_item/toggle.php',
                    data: {
                        ieraksts_id: $(this).parent().attr('data-id'),
                        saraksts_id: saraksts_id,
                    },
                    dataType: 'json',
                    encode: true,
                }).done(function (data) {
                    console.log(data);
                });

            });

            /*
             * Ieraksta dzēšana. 
             */
            $(document).on('click', '#taskList li .btn-outline-danger', function(){

                const ieraksts = $(this);
                $.ajax({
                    type:'POST',
                    url: 'db/list_item/delete.php',
                    data: {
                        ieraksts_id: ieraksts.attr('data-id'),
                        saraksts_id: saraksts_id,
                    },
                    dataType: 'json',
                    encode: true,
                }).done(function (data) {
                    console.log(data);
                    ieraksts.parent().remove();
                });

            });

            /*
             * Ieraksta rediģēšana – ievades lauka parādīšana.
             * Ieraksta teksts tiek aizvietots ar input lauku, kurā var rediģēt tekstu
             */
            $(document).on('click','#taskList li .btn-outline-primary', function(){
                const teksts = $(this).siblings("span").text();
                const id = $(this).attr('data-id');

                $('#input-edit').val(teksts);
                $('#input-edit').attr('data-id',id);
                $('#input-edit').attr('data-nosaukums', 0);
            });

            $(document).on('click','#edit-nosaukums', function(){
                const teksts = $("#nosaukums").text();

                $('#input-edit').val(teksts);
                $('#input-edit').attr('data-nosaukums', 1);
            });

            /*
             * Rediģētā ieraksta saglabāšana.
             * Nododam serverim tekstu, kuru ievadīja lietotājs, ieraksta id un saraksta id
             * Sagaidot atbildi no servera, aizvietojam input lauku ar jauno tekstu
             */
            document.addEventListener('DOMContentLoaded', function(){
                $('#save-edit').click(function(){
                    if($('#input-edit').attr('data-nosaukums') == true){
                        // nosaukuma rediģēšana
                        const text = $('#input-edit').val();
                        $.ajax({
                            type:'POST',
                            url: 'db/list/update.php',
                            data: {
                                saraksts_id: saraksts_id,
                                text: text,
                            },
                            dataType: 'json',
                            encode: true,
                        }).done(function (data) {
                            console.log(data);
                            
                            $("#nosaukums").text(data.text);
                        });
                    }else{
                        // ieraksta rediģēšana
                        const text = $('#input-edit').val();
                        const id = $('#input-edit').attr('data-id');

                        $.ajax({
                            type:'POST',
                            url: 'db/list_item/update.php',
                            data: {
                                ieraksts_id: id,
                                saraksts_id: saraksts_id,
                                text: text,
                            },
                            dataType: 'json',
                            encode: true,
                        }).done(function (data) {
                            console.log(data);
                            
                            $('#taskList li[data-id='+ id +'] span').text(data.text);

                        });
                    }
                });
            });
            

        </script>
        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Launch demo modal
        </button> -->

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Rediģēt ierakstu</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="input-edit">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Atcelt</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="save-edit">Saglabāt</button>
            </div>
            </div>
        </div>
        </div>

    <?php endif; ?>

</body>
</html>