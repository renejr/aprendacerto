<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda Jogando - Palavras Cruzadas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleCruzadas.css"> 
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
    <h1 class="text-center">Palavras Cruzadas - Nível <?php echo $_GET['level_id']; ?></h1>
        <div class="row">
            <div class="col-md-6">
                <table id="tabuleiro"></table>
            </div>
            <div class="col-md-6">
                <div id="definicoes"></div>
                <button id="verificar" class="btn btn-primary">Verificar Respostas</button>
            </div>
            <div id="instrucoes">
                <h3>Instruções:</h3>
                <ol>
                    <li>Preencha o tabuleiro com as palavras que correspondem às definições.</li>
                    <li>Clique em uma célula com letra para ver a definição da palavra.</li>
                    <li>Clique no botão "Verificar Respostas" para validar suas respostas.</li>
                </ol>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var jogo_id = <?php echo $_GET['jogo_id']; ?>;
        var nivelDificuldade = <?php echo $_GET['level_id']; ?>;
    </script>
    <script src="scriptCruzadas.js"></script>
</body>
</html>