<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

require_once 'bdclass.php';
$db = new BD();

// Consulta SQL com parâmetro para prevenir Injeção SQL
$sql = "SELECT * FROM jogos WHERE id = :jogo_id"; 
$jogos = $db->select($sql, ['jogo_id' => $_GET['jogo_id']]);

$sql = "SELECT * FROM levels ORDER BY posicao ASC"; 
$niveis = $db->select($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda Jogando <?php echo $jogos[0]['nome']; ?>  - Aprenda</title>
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

        /* .img-trofeu{
            width: 50px;
            height: 50px;
            position: absolute;
            background-color: red;

        } */

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
            <?php if (!empty($niveis)): ?>
                <?php foreach ($niveis as $nivel): ?>
                    <?php            
                    $nivelLibe = 1;        
                    $sql = "SELECT * FROM plays where jogo_id = :jogo_id AND usuario_id = :usuario_id AND nivel = :level";
                    $plays = $db->select($sql, ['jogo_id' => $_GET['jogo_id'], 'usuario_id' => $_SESSION['usuario_id'], 'level' => $nivel['level']]);

                    if (empty($plays)) {
                        // Seleciona 100 palavras aleatorias na tabela 'palavras' com o level correto
                        $sql = "SELECT DISTINCT * FROM palavras WHERE nivel = :level ORDER BY RAND() LIMIT 100";
                        $palavras = $db->select($sql, ['level' => $nivel['level']]);

                        // Insere o usuario_id, jogo_id, palavra_id e o nivel na tabela 'plays'
                        foreach ($palavras as $palavra) {
                            $sql = "INSERT INTO plays (usuario_id, jogo_id, palavra_id, nivel) VALUES (:usuario_id, :jogo_id, :palavra_id, :level)";
                            $db->insert($sql, ['usuario_id' => $_SESSION['usuario_id'], 'jogo_id' => $_GET['jogo_id'], 'palavra_id' => $palavra['id'], 'level' => $nivel['level']]);
                        }
                    }

                    if($nivel['level'] > 1 || $nivel['level'] <= 10 ) {
                        // Valido na tabela plays se o nivel anterior ja possui 5 palavras jogadas e com p status = 1 para mostrar o botão de proximo nivel
                        $sql = "SELECT * FROM plays WHERE jogo_id = :jogo_id AND usuario_id = :usuario_id AND nivel = :level AND status = 1";
                        $plays = $db->select($sql, ['jogo_id' => $_GET['jogo_id'], 'usuario_id' => $_SESSION['usuario_id'], 'level' => $nivel['level'] - 1]);
                        if (count($plays) >= 5) {
                            $nivelLibe = 1;
                        } else {
                            $nivelLibe = 0;
                        }
                    }
                    
                    $jogoNome = lcfirst($jogos[0]['nome']);
                    $jogoNome = str_replace(' ', '', $jogoNome);
                    ?>
                    
                    <div class="col-md-4"> 
                        <div class="jogo-container" style="background-image: url('img/<?php echo $nivel['imagem']; ?>.jpg');"> 
                            <?php
                            if ($nivelLibe == 1) {
                                ?>
                                 <!-- <div id="trofeu" name="trofeu" class="img-trofeu"></div> -->
                                <a href="<?php echo $jogoNome; ?>.php?jogo_id=<?php echo $_GET['jogo_id']; ?>&level_id=<?php echo $nivel['id']; ?>" class="jogo-link"></a>
                                <?php
                            } else {
                                ?>
                                <a href="javascript:void(0);" class="jogo-link"></a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p class="text-muted">Nenhum nível de jogo disponível no momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
