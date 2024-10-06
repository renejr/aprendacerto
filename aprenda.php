<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

require_once 'bdclass.php';
$db = new BD();

$sql = "SELECT * FROM jogos ORDER BY nome ASC"; 
$jogos = $db->select($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda Jogando - Aprenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000; 
        }

        .jogo-container {
            background-size: cover; /* Imagem cobre a célula */
            background-position: center; /* Centraliza a imagem */
            height: 400px; /* Altura da célula - ajuste conforme necessário */
            margin-bottom: 20px; /* Espaçamento entre as células */
            border-radius: 10px; /* Borda arredondada - opcional */
            overflow: hidden; /* Esconde a imagem que ultrapassar a borda */
            position: relative; /* Para posicionar o link */
        }

        .jogo-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none; /* Remove a linha do link */
            color: white; /* Cor do texto do link */
            font-size: 2em; /* Tamanho da fonte do link */
            font-weight: bold; /* Negrito no texto do link */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Sombra no texto */
        }

        /* Para telas menores, ajuste a quantidade de colunas */
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 0 0 50%; /* 2 colunas em telas médias */
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .col-md-4 {
                flex: 0 0 100%; /* 1 coluna em telas pequenas */
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> 

    <div class="container mt-5">
        <div class="row">
            <?php if (!empty($jogos)): ?>
                <?php foreach ($jogos as $jogo): ?>
                    <div class="col-md-4"> 
                        <div class="jogo-container" style="background-image: url('img/<?php echo $jogo['image']; ?>.jpg');"> 
                            <a href="<?php //echo $jogo['link']; ?>niveis.php?jogo_id=<?php echo $jogo['id']; ?>" class="jogo-link">
                                <?php echo $jogo['nome']; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p class="text-muted">Nenhum jogo disponível no momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
