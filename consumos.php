<?php
ini_set('memory_limit', '256M'); // Aumenta o limite de memória para 256MB
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
                $_SESSION['usuario_id'] = $usuario[0]['id'];
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
    } elseif ($action == 'atualizar_perfil') {
        // Obter os dados do formulário (exceto o email)
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $cpf = $_POST['cpf'];
        $sexo = $_POST['sexo'];
        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $telefone_celular = $_POST['telefone_celular'];
        $data_aniversario = $_POST['data_aniversario'];

        // Processamento da imagem
        if (isset($_POST['imagem_cropped']) && !empty($_POST['imagem_cropped'])) {
            // Decodifica a imagem em base64
            $imagem = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['imagem_cropped']));
        } else {
            // Se não houver imagem recortada, mantém a imagem existente ou define como NULL
            $imagem = isset($dados_usuario['imagem']) ? $dados_usuario['imagem'] : NULL;
        }
        
        // Obter o ID do usuário a partir do email da sessão
        $usuario = $db->select("SELECT id FROM usuarios WHERE email = :email", ['email' => $_SESSION['email']]);
        $usuario_id = $usuario[0]['id'];

        // Verificar se já existe um registro para o usuario_id
        $perfil_existente = $db->select("SELECT 1 FROM dados_usuario WHERE usuario_id = :usuario_id", ['usuario_id' => $usuario_id]);

        // Definir a query SQL com base na existência do perfil
        if ($perfil_existente) {
            // Já existe um perfil, então fazemos UPDATE
            $sql = "UPDATE dados_usuario SET 
                        nome = :nome,
                        sobrenome = :sobrenome,
                        cpf = :cpf,
                        sexo = :sexo,
                        cep = :cep,
                        endereco = :endereco,
                        numero = :numero,
                        complemento = :complemento,
                        bairro = :bairro,
                        cidade = :cidade,
                        estado = :estado,
                        telefone_celular = :telefone_celular,
                        data_aniversario = :data_aniversario
                        , imagem = :imagem
                    WHERE usuario_id = :usuario_id";
        } else {
            // Não existe um perfil, então fazemos INSERT
            $sql = "INSERT INTO dados_usuario (
                        usuario_id, 
                        nome, 
                        sobrenome, 
                        cpf, 
                        sexo, 
                        cep, 
                        endereco, 
                        numero, 
                        complemento, 
                        bairro, 
                        cidade, 
                        estado, 
                        telefone_celular, 
                        data_aniversario
                        , imagem
                    ) VALUES (
                        :usuario_id, 
                        :nome, 
                        :sobrenome, 
                        :cpf, 
                        :sexo, 
                        :cep, 
                        :endereco, 
                        :numero, 
                        :complemento, 
                        :bairro, 
                        :cidade, 
                        :estado, 
                        :telefone_celular, 
                        :data_aniversario
                        , :imagem
                    )";
        }
    
        $params = [
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'cpf' => $cpf,
            'sexo' => $sexo,
            'cep' => $cep,
            'endereco' => $endereco,
            'numero' => $numero,
            'complemento' => $complemento,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado,
            'telefone_celular' => $telefone_celular,
            'data_aniversario' => $data_aniversario,
            'usuario_id' => $usuario_id
            , 'imagem' => $imagem
        ];
    
        // Executar a query (UPDATE ou INSERT)
        if ($db->update($sql, $params)) { // O método update() também funciona para INSERT
            // Redirecionar para a página de perfil com mensagem de sucesso
            header("Location: perfil.php?success=1");
            exit();
        } else {
            // Redirecionar para a página de perfil com mensagem de erro
            header("Location: perfil.php?error=1");
            exit();
        }
    } elseif ($action == "buscar_palavras_cruzadas"){
        // print_r($_POST);
        // print_r($_SESSION);

        $jogo_id = $_POST['jogo_id'];
        $nivel = $_POST['nivel'];
        $usuario_id = $_SESSION['usuario_id'];

        // echo("<hr>");

        // echo("jogo_id: $jogo_id<br>");
        // echo("nivel: $nivel<br>");
        // echo("usuario_id: $usuario_id<br>");

        // Consulta SQL para buscar as palavras do jogo e nível especificados para o usuario ativo
        $result = $db->select("SELECT p.palavra, p.definicao FROM palavras p INNER JOIN plays pl ON p.id = pl.palavra_id WHERE pl.jogo_id = $jogo_id AND pl.nivel = $nivel AND pl.usuario_id = $usuario_id LIMIT 100;");

        // Verifica se a consulta retornou resultados
        if ($result) {
            $palavras = array();
            foreach ($result as $row) {
                $palavras[] = $row;
            }
            // print_r($palavras);
        } else {
            echo "Nenhuma palavra encontrada.";
        }

        // print_r($palavras);

        echo json_encode($palavras);
        exit();
    
    } elseif ($action == 'buscar_palavra') {
        // Busca a palavra no banco de dados e na tabela plays
        $palavra = $db->select("SELECT pa.id, pa.palavra, pa.definicao, pa.exemplos, pa.sinonimos FROM plays p JOIN palavras pa ON p.palavra_id = pa.id WHERE p.usuario_id = $_SESSION[usuario_id] AND p.nivel = $_POST[nivel] AND p.status = 0 AND p.jogo_id = $_POST[jogo_id] ORDER BY RAND() LIMIT 0, 1");
        $_palavra = str_replace('_', '-', $palavra[0]['palavra']);

        // print_r($palavra[0]);

        echo json_encode(['id' => $palavra[0]['id'], 'palavra' =>  $_palavra, 'definicao' => $palavra[0]['definicao'], 'exemplos' => $palavra[0]['exemplos'], 'sinonimos' => $palavra[0]['sinonimos']]);
    } elseif ($action == 'modifica_status') {
        // print_r($_POST);
        // print_r($_SESSION);

        // Atualiza o status da palavra no banco de dados
        $sql = "UPDATE plays SET status = :status, tentativas = :tentativas WHERE palavra_id = :palavra_id AND usuario_id = :usuario_id AND jogo_id = :jogo_id";
        $params = [
            'status' => $_POST['status'],
            'palavra_id' => $_POST['palavra_id'], 
            'tentativas' => $_POST['tentativas'],
            'usuario_id' => $_SESSION['usuario_id'],
            'jogo_id' => $_POST['jogo_id']
        ];

        if ($db->update($sql, $params)) {
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
    }
}

