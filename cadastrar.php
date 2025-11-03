<?php

$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

session_start();


$register_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $new_user = $_POST['nome'] ?? "";
    $new_email = $_POST['email'] ?? "";

    if (!empty($new_user) && !empty($new_email)) {
        $stmt = $mysqli->prepare("INSERT INTO usuario (nome_usuario, email_usuario) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_user, $new_email);

        if ($stmt->execute()) {
            $new_user_id = $stmt->insert_id;
            $stmt->close();

            header("Location: index.php?user_id=" . $new_user_id);
            exit;
        } else {
            $register_msg = "Erro ao cadastrar novo usuário: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $register_msg = "Preencha todos os campos.";
    }
}
?>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Novo Usuário do Sistema</title>
</head>
<body>

    <form method="post">
        <h2>
            Bem-vindo,
            <?= isset($_SESSION["nome"]) ? htmlspecialchars($_SESSION["nome"]) : "Visitante" ?>!
        </h2>
        <h3>Cadastro Novo Usuário</h3>

        <?php if ($register_msg): ?>
            <p><?= htmlspecialchars($register_msg) ?></p>
        <?php endif; ?>

        <input type="text" name="nome" placeholder="Novo Usuário" required>
        <input type="email" name="email" placeholder="Novo Email" required>
        <button type="submit" name="register" value="1">Cadastrar</button>
    </form>

    <p><a href="index.php">Voltar</a></p>

</body>
</html>