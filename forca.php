<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

require_once 'bdclass.php';
$db = new BD();

if (isset($_GET['level_id'])){
    $nivelDificuldade = $_GET['level_id'];
} else {
    header("Location: niveis.php");
    exit();
}

if(isset($_GET['jogo_id'])){
    $jogo_id = $_GET['jogo_id'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda Jogando - Forca</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1 class="text-center">Jogo da Forca</h1>
        <div class="row">
            <div class="col-md-6">
                <canvas id="forca"></canvas>
            </div>
            <div class="col-md-6">
                <div id="palavra-secreta">
                    </div>
                <div id="letras-digitadas">
                    </div>
                <div id="teclado">
                    </div>
                <hr>
                <h2><div id="definicao"></div></h2>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalInfoPalavra" tabindex="-1" role="dialog" aria-labelledby="modalInfoPalavraLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInfoPalavraLabel">Informações da Palavra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Palavra:</strong> <span id="modalPalavra"></span></p>
                    <p><strong>Definição:</strong> <span id="modalDefinicao"></span></p>
                    <p><strong>Exemplos:</strong> <span id="modalExemplos"></span></p>
                    <p><strong>Sinônimos:</strong> <span id="modalSinonimos"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var nivelDificuldade = <?php echo $nivelDificuldade; ?>;
        var jogo_id = <?php echo $jogo_id; ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>