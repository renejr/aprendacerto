<?php
@session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="bemvindo.php">Aprenda</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="aprenda.php">Aprenda Jogando</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">Meu Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </nav>