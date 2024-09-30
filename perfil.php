<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

require_once 'bdclass.php';
$db = new BD();

// Consulta para buscar os dados do usuário
$sql = "SELECT d.* 
        FROM usuarios u
        INNER JOIN dados_usuario d ON u.id = d.usuario_id
        WHERE u.email = :email";

$dados_usuario = $db->select($sql, ['email' => $_SESSION['email']]);
if (!empty($dados_usuario)) {
    $dados_usuario = $dados_usuario[0];
} else {
    echo "<div class='alert alert-warning'>Você ainda não possui um perfil cadastrado.</div>";
    // Redirecionar para um formulário de criação de perfil, se necessário
}

// Exibir mensagens de sucesso/erro
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success'>Dados atualizados com sucesso!</div>";
} elseif (isset($_GET['error'])) {
    echo "<div class='alert alert-danger'>Erro ao atualizar os dados.</div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - AprendaCerto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000; 
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Meu Perfil</h4>
                    </div>
                    <div class="card-body">
                    <form method="POST" action="consumos.php">
                        <input type="hidden" name="action" id="action" value="atualizar_perfil">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                value="<?php echo isset($dados_usuario['nome']) ? $dados_usuario['nome'] : ''; ?>" required minlength="3">
                        </div>
                        <div class="mb-3">
                            <label for="sobrenome" class="form-label">Sobrenome:</label>
                            <input type="text" class="form-control" id="sobrenome" name="sobrenome" 
                                value="<?php echo isset($dados_usuario['sobrenome']) ? $dados_usuario['sobrenome'] : ''; ?>" required minlength="3">
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF:</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" 
                                value="<?php echo isset($dados_usuario['cpf']) ? $dados_usuario['cpf'] : ''; ?>" required onblur="if(!validarCPF(this.value)){ alert('CPF inválido!'); this.value=''; this.focus(); }">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?php echo $_SESSION['email']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="sexo" class="form-label">Sexo:</label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Selecione</option>
                                <option value="Masculino" <?php echo (isset($dados_usuario['sexo']) && $dados_usuario['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Feminino" <?php echo (isset($dados_usuario['sexo']) && $dados_usuario['sexo'] == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
                                <option value="Outro" <?php echo (isset($dados_usuario['sexo']) && $dados_usuario['sexo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cep" class="form-label">CEP:</label>
                            <input type="text" class="form-control" id="cep" name="cep" 
                                value="<?php echo isset($dados_usuario['cep']) ? $dados_usuario['cep'] : ''; ?>" required onblur="buscarEndereco(this.value);">
                        </div>
                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço:</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" 
                                value="<?php echo isset($dados_usuario['endereco']) ? $dados_usuario['endereco'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="numero" class="form-label">Número:</label>
                            <input type="text" class="form-control" id="numero" name="numero" 
                                value="<?php echo isset($dados_usuario['numero']) ? $dados_usuario['numero'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" 
                                value="<?php echo isset($dados_usuario['complemento']) ? $dados_usuario['complemento'] : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="bairro" class="form-label">Bairro:</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" 
                                value="<?php echo isset($dados_usuario['bairro']) ? $dados_usuario['bairro'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="cidade" class="form-label">Cidade:</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" 
                                value="<?php echo isset($dados_usuario['cidade']) ? $dados_usuario['cidade'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado:</label>
                            <input type="text" class="form-control" id="estado" name="estado" 
                                value="<?php echo isset($dados_usuario['estado']) ? $dados_usuario['estado'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone_celular" class="form-label">Telefone Celular:</label>
                            <input type="text" class="form-control" id="telefone_celular" name="telefone_celular" 
                                value="<?php echo isset($dados_usuario['telefone_celular']) ? $dados_usuario['telefone_celular'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="data_aniversario" class="form-label">Data de Aniversário:</label>
                            <input type="date" class="form-control" id="data_aniversario" name="data_aniversario" 
                                value="<?php echo isset($dados_usuario['data_aniversario']) ? $dados_usuario['data_aniversario'] : ''; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#cpf').mask('000.000.000-00');
        $('#telefone_celular').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
    });
    function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, ''); // Remove caracteres não numéricos
            if (cpf == '') return false;
            // Elimina CPFs inválidos conhecidos
            if (cpf.length != 11 ||
                cpf == "00000000000" ||
                cpf == "11111111111" ||
                cpf == "22222222222" ||
                cpf == "33333333333" ||
                cpf == "44444444444" ||
                cpf == "55555555555" ||
                cpf == "66666666666" ||
                cpf == "77777777777" ||
                cpf == "88888888888" ||
                cpf == "99999999999")
                return false;
            // Valida 1o digito
            add = 0;
            for (i = 0; i < 9; i++)
                add += parseInt(cpf.charAt(i)) * (10 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(9)))
                return false;
            // Valida 2o digito
            add = 0;
            for (i = 0; i < 10; i++)
                add += parseInt(cpf.charAt(i)) * (11 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(10)))
                return false;
            return true;
        }

        function buscarEndereco(cep) {
            // Remove caracteres não numéricos do CEP
            cep = cep.replace(/\D/g, '');

            // Verifica se o CEP possui 8 dígitos
            if (cep.length === 8) {
                // Acessa a API do ViaCEP
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    // Verifica se houve erro na consulta
                    if (!data.erro) {
                    // Preenche os campos do formulário
                    document.getElementById('endereco').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                    } else {
                    // Exibe uma mensagem de erro caso o CEP seja inválido
                    alert('CEP não encontrado.');
                    }
                })
                .catch(error => console.error('Erro ao consultar API:', error));
            }
        }

    </script>
</body>
</html>
