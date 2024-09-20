# AprendaCerto

**AprendaCerto** é um sistema de gerenciamento de usuários com funcionalidades de login, cadastro e recuperação de senha, desenvolvido com HTML, CSS, Bootstrap, JavaScript, jQuery, PHP e MySQL.

## Funcionalidades

- Login de usuários
- Cadastro de novos usuários
- Recuperação de senha
- Proteção de páginas restritas com sessão
- Logout de usuários

## Tecnologias Utilizadas

- **HTML5**
- **CSS3**
- **Bootstrap 5**
- **JavaScript (ES6)**
- **jQuery**
- **PHP 7.x+**
- **MySQL**
- **PDO (PHP Data Objects)**

## Requisitos

1. **Servidor Web**: Apache (preferencialmente com suporte ao PHP) ou outro servidor com suporte a PHP.
2. **PHP**: Versão 7.0 ou superior.
3. **MySQL**: Versão 5.7 ou superior.
4. **Composer** (opcional): Caso queira gerenciar dependências PHP, embora o projeto não tenha dependências externas.

## Instalação

### 1. Clone o Repositório

Clone o projeto do seu repositório Git localmente:

```bash
git clone https://github.com/seu-usuario/aprendacerto.git
cd aprendacerto
```

### 2. Configuração do Banco de Dados

#### a. Criação do Banco de Dados

Crie o banco de dados **`aprendacerto`** no seu servidor MySQL:

```sql
CREATE DATABASE aprendacerto;
```

#### b. Criação da Tabela `usuarios`

Execute o seguinte comando SQL para criar a tabela `usuarios`:

```sql
CREATE TABLE `usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(250) NOT NULL,
    `senha` VARCHAR(250) NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;
```

### 3. Configuração do Arquivo `bdclass.php`

Abra o arquivo `bdclass.php` e verifique as configurações de conexão ao banco de dados. Certifique-se de que os parâmetros correspondem ao seu ambiente de desenvolvimento.

```php
<?php
class BD {
    private $host = 'localhost';
    private $port = '3307';  // Ajuste a porta caso necessário
    private $db = 'aprendacerto';
    private $user = 'root';
    private $pass = '';  // Se você tiver senha, adicione aqui
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->db", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
        }
    }

    // Métodos de SELECT, INSERT, UPDATE, DELETE...
}
?>
```

### 4. Configuração do Servidor

Certifique-se de que seu servidor Apache ou outro servidor esteja configurado para processar arquivos PHP. Caso esteja usando o **XAMPP** ou **WAMP**, coloque o projeto na pasta `htdocs` ou `www`, respectivamente.

### 5. Inicialização do Projeto

Após configurar o servidor e o banco de dados, acesse a URL onde o projeto está hospedado. Por exemplo:

```
http://localhost/aprendacerto/index.html
```

### 6. Uso

#### a. **Login**
- Acesse a tela de login em `index.html`.
- Informe o e-mail e a senha cadastrados.
- Caso o login seja bem-sucedido, você será redirecionado para a página de boas-vindas.

#### b. **Cadastro**
- Acesse a tela de cadastro em `novaconta.html`.
- Informe um e-mail válido e uma senha.
- Se o e-mail ainda não estiver cadastrado, o sistema gravará os dados no banco.

#### c. **Recuperação de Senha**
- Na tela de recuperação de senha `esquecisenha.html`, informe o e-mail.
- Caso o e-mail exista no banco, a senha será exibida.

#### d. **Proteção por Sessão**
- Páginas restritas, como `bemvindo.php`, são protegidas. O sistema redireciona automaticamente para o login se o usuário não estiver autenticado.

#### e. **Logout**
- O usuário pode se desconectar do sistema usando o botão "Sair" na página de boas-vindas, o que encerra a sessão.

## Estrutura de Diretórios

aprendacerto/
│
├── img/
│   └── logo-aprendacerto.png  # Logo do sistema
├── bdclass.php            # Classe de conexão ao banco de dados (PDO)
├── consumos.php           # Lógica de backend (login, cadastro, etc.)
├── index.html             # Página de login
├── novaconta.html         # Página de cadastro
├── esquecisenha.html      # Página de recuperação de senha
├── bemvindo.php           # Página de boas-vindas (protegida por sessão)
├── logout.php             # Arquivo para realizar o logout (encerrar a sessão)
└── README.md              # Instruções para execução do projeto
```

## Observações

- **Validação de E-mail**: Tanto no frontend quanto no backend, os e-mails são validados.
- **Segurança**: A senha é salva em texto plano no banco de dados para simplificação, mas, em ambientes de produção, recomenda-se utilizar hashing de senha com algoritmos como `bcrypt`.
- **Validação do Formulário**: Todos os formulários têm validação tanto no frontend quanto no backend.

## Licença

Este projeto é de código aberto e está licenciado sob os termos da MIT License.