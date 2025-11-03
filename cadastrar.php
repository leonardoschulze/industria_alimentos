<?php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function register($nome, $email, $password)
    {
        $sql = "INSERT INTO usuarios (nome, email) VALUES (:nome , :email,)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
}

$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$register_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $new_user = $_POST['new_nome'] ?? "";
    $new_email = $_POST['new_email'] ?? "";
    $new_cep = $_POST['cep'] ?? "";
    $new_rua = $_POST['rua'] ?? "";
    $new_bairro = $_POST['bairro'] ?? "";
    $new_cidade = $_POST['cidade'] ?? "";
    $new_estado = $_POST['estado'] ?? "";

    if ($new_user && $new_email) {

        $stmt = $mysqli->prepare("
    INSERT INTO usuario (nome_usuario, email_usuario, cep, rua, bairro, cidade, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
        $stmt->bind_param(
            "sssssss",
            $new_user,
            $new_email,
            $new_cep,
            $new_rua,
            $new_bairro,
            $new_cidade,
            $new_estado
        );

        if ($stmt->execute()) {
            $register_msg = "Usuário cadastrado com sucesso!";
        } else {
            $register_msg = "Erro ao cadastrar novo usuário: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $register_msg = "Preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Novo Usuário do Sistema</title>
    <link rel="stylesheet" href="../style.css">

    <script>
        function limpa_formulário_cep() {
            document.getElementById('rua').value = "";
            document.getElementById('bairro').value = "";
            document.getElementById('cidade').value = "";
            document.getElementById('estado').value = "";
        }

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                document.getElementById('rua').value = conteudo.logradouro;
                document.getElementById('bairro').value = conteudo.bairro;
                document.getElementById('cidade').value = conteudo.localidade;
                document.getElementById('estado').value = conteudo.uf;
            } else {
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
        }

        function pesquisacep(valor) {
            var cep = valor.replace(/\D/g, '');
            if (cep != "") {
                var validacep = /^[0-9]{8}$/;
                if (validacep.test(cep)) {
                    document.getElementById('rua').value = "...";
                    document.getElementById('bairro').value = "...";
                    document.getElementById('cidade').value = "...";
                    document.getElementById('estado').value = "...";
                    var script = document.createElement('script');
                    script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';
                    document.body.appendChild(script);
                } else {
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } else {
                limpa_formulário_cep();
            }
        }
    </script>
</head>

<body>
    <div class="card">
        <form method="post">
            <h2>Bem-vindo, <?= isset($_SESSION["nome"]) ? htmlspecialchars($_SESSION["nome"]) : "Visitante" ?>!</h2>
            <h3>Cadastro de Novo Usuário</h3>

            <?php if ($register_msg): ?>
                <p><?= htmlspecialchars($register_msg) ?></p>
            <?php endif; ?>

            <input type="text" name="new_nome" placeholder="Nome de usuário" required><br><br>
            <input type="email" name="new_email" placeholder="E-mail do usuário" required><br><br>

            <br><br>

            <h3>Endereço</h3>
            <input name="cep" type="text" id="cep" placeholder="CEP" maxlength="9"
                onblur="pesquisacep(this.value);" required><br><br>
            <input name="rua" type="text" id="rua" placeholder="Rua" required><br><br>
            <input name="bairro" type="text" id="bairro" placeholder="Bairro" required><br><br>
            <input name="cidade" type="text" id="cidade" placeholder="Cidade" required><br><br>
            <input name="estado" type="text" id="estado" placeholder="Estado" maxlength="2" required><br><br>

            <button type="submit" name="register" value="1">Cadastrar</button>
            <br><br>

            <button type="button" onclick="window.location.href='index.php'">Voltar</button>
        </form>
    </div>
</body>

</html>