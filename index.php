<?php

$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["nome"] ?? "";
    $email = $_POST["email"] ?? "";

    $stmt = $mysqli->prepare("SELECT id_usuario, nome_usuario, email_usuario FROM usuario WHERE nome_usuario=? AND email_usuario=?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados = $result->fetch_assoc();
    $stmt->close();

    if ($dados) {
        $_SESSION["user_id"] = $dados["id_usuario"];
        $_SESSION["nome"] = $dados["nome_usuario"];
        header("Location: atribuir_tarefa.php");
        exit;
    } else {
        $msg = "Usuário ou senha incorretos!";
    }
}
?>

<?php if (!empty($_SESSION["user_id"])): ?>
  <div class="card">
    <h3>Bem-vindo, <?= $_SESSION["nome"] ?>!</h3>
    <p>Sessão ativa.</p>
    <p><a href="?logout=1">Sair</a></p>
  </div>

<?php else: ?>
  <div class="card">
    <h2>Bem-Vindo</h2>
    <?php if ($msg): ?><p class="msg"><?= $msg ?></p><?php endif; ?>
    <form method="post">
        <div class="loguim">
          <div class="user">
            <input type="text" name="nome" placeholder="Usuário" required>
          </div>
          <div class="senh">
            <input type="email" name="email" placeholder="Email" required>
          </div>
        </div>
        <button type="submit">Entrar</button>
        <br>
        <div class="forgot-adm">
            <a href="cadastrar.php">Cadastrar</a>
        </div>
    </form>
  </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
</html>