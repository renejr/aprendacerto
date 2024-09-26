<?php
session_start();
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

        // Busca o usuário no banco de dados pelo email
        $usuario = $db->select("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);

        if ($usuario) {
            // Verifica se a senha está correta usando password_verify
            if (password_verify($senha, $usuario[0]['senha'])) {
                // Inicia a sessão do usuário
                $_SESSION['email'] = $email;
                echo json_encode(['status' => 'success', 'message' => 'Login efetuado com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Senha incorreta.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado.']);
        }

    } elseif ($action == 'cadastrar') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

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
            // Insere novo usuário com a senha já em hash
            $db->insert("INSERT INTO usuarios (email, senha) VALUES (:email, :senha)", ['email' => $email, 'senha' => $senha_hash]);
            echo json_encode(['status' => 'success', 'message' => 'Cadastro efetuado com sucesso']);
        }

    } elseif ($action == 'recuperar') {
        $email = $_POST['email'];

        // Verificação de formato de e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de e-mail inválido']);
            exit;
        }

        // Busca o usuário no banco de dados pelo email
        $usuario = $db->select("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);

        if ($usuario) {
            // Implemente aqui a lógica de redefinição de senha 
            // (ex: enviar um email com um link para redefinir a senha)
            echo json_encode(['status' => 'success', 'message' => 'Um email foi enviado para você com instruções para redefinir sua senha.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado']);
        }
    }
}
