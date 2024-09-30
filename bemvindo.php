<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    // Redireciona para a página de login se não estiver logado
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-Vindo - AprendaCerto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

    <div class="container text-center">
        <img src="img/logo-aprendacerto.png" alt="Logo AprendaCerto" width="250" height="43" class="mt-5">
        <div class="col-md-6 offset-md-3 mt-5">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Bem-vindo(a), <?php echo $_SESSION['email']; ?>!</h4>
                <p>Você efetuou login com sucesso no sistema <strong>AprendaCerto</strong>.</p>
                <hr>
                <p class="mb-0">Obrigado por utilizar nossa plataforma. Continue navegando e aproveite nossos conteúdos!</p>
            </div>
        </div>
    </div>
</body>
</html>
