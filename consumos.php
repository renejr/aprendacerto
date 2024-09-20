<?php
session_start();  // Inicia a sessão
require_once 'bdclass.php';
$db = new BD();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Verificação de formato de e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de e-mail inválido']);
            exit;
        }

        // Valida usuário e senha no banco
        $usuario = $db->select("SELECT * FROM usuarios WHERE email = :email AND senha = :senha", ['email' => $email, 'senha' => $senha]);
        if ($usuario) {
            // Inicia a sessão do usuário
            $_SESSION['email'] = $email;
            echo json_encode(['status' => 'success', 'message' => 'Login efetuado com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Dados incorretos ou usuário inexistente']);
        }

    } elseif ($action == 'cadastrar') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Verificação de formato de e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de e-mail inválido']);
            exit;
        }

        // Verifica se o usuário já está cadastrado
        $usuario_existente = $db->select("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);
        if ($usuario_existente) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário já cadastrado']);
        } else {
            // Insere novo usuário
            $db->insert("INSERT INTO usuarios (email, senha) VALUES (:email, :senha)", ['email' => $email, 'senha' => $senha]);
            echo json_encode(['status' => 'success', 'message' => 'Cadastro efetuado com sucesso']);
        }

    } elseif ($action == 'recuperar') {
        $email = $_POST['email'];

        // Verificação de formato de e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de e-mail inválido']);
            exit;
        }

        // Busca a senha do usuário no banco
        $usuario = $db->select("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);
        if ($usuario) {
            echo json_encode(['status' => 'success', 'message' => 'Sua senha é: ' . $usuario[0]['senha']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado']);
        }
    }
}
