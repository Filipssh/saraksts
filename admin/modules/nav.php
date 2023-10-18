<!-- navbar(sākums(index.php), saraksti(home.php), profils, atteikties(logout)), 
    lietotāja izveidotie saraksti, iespēja izveidot jaunu sarakstu-->

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="../" class="navbar-brand">Sākums</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="home" class="nav-link">saraksti</a>
                </li>
                <li class="nav-item">
                    <a href="user" class="nav-link">profils</a>
                </li>
                <li class="nav-item">
                    <a href="users" class="nav-link">lietotāji</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <?php
                        if(isset($_SESSION['username'])){
                            echo "<a href=\"logout\" class=\"nav-link\">Atteikties</a>";
                        }else{
                            echo "<a href=\"login\" class=\"nav-link\">Autorizēties</a>";
                        }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>