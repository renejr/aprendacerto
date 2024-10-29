<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda Jogando - Complete a Frase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleCompleteaFrase.css"> </head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1 class="text-center">Complete a Frase - Nível <?php echo $_GET['level_id']; ?></h1>
        <div id="timer">Tempo: <span id="tempo">300</span>s</div>
        <div class="row">
            <div class="col-md-6">
            <div id="palavras-container">
                <div class="palavra">
                    <span class="letra" data-letra="A">_</span>
                    <span class="letra" data-letra="B">_</span>
                    <span class="letra" data-letra="C">_</span>
                    <span class="letra" data-letra="D">_</span>
                </div>
                <div id="definicao-atual"></div>
            </div>
            </div>
            <div class="col-md-6">
                <div id="definicoes"></div>
            </div>
        </div>
        <div id="teclado"></div>
        <div id="palavras-acertadas"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scriptCompleteaFrase.js"></script>
    <script>
        var jogo_id = <?php echo $_GET['jogo_id']; ?>;
        var level_id = <?php echo $_GET['level_id']; ?>;
    </script>
</body>
</html>