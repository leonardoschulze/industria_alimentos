<?php

$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    die("Usuário não especificado.");
}

$stmt = $mysqli->prepare("SELECT nome_usuario, email_usuario FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Usuário não encontrado.");
}
$user = $result->fetch_assoc();
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cadastrar_tarefa'])) {
    $descricao = $_POST['descricao'] ?? "";
    $setor = $_POST['setor'] ?? "";
    $prioridade = $_POST['prioridade'] ?? "";
    $data_cadastro = date("Y-m-d");
    $status = "a fazer";

    if (!empty($descricao) && !empty($setor) && !empty($prioridade)) {
        $stmt = $mysqli->prepare("
            INSERT INTO tarefa (descricao, nome, prioridade, data_cadastro, status, fk_id_usuario)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssi", $descricao, $setor, $prioridade, $data_cadastro, $status, $user_id);

        if ($stmt->execute()) {
            header("Location: gerenciar_tarefas.php?user_id=" . $user_id);
            exit;
        } else {
            $erro = "Erro ao cadastrar tarefa: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuir Tarefa</title>
</head>
<body>

    <form method="post">
        <h2>Atribuir tarefa para: <?= htmlspecialchars($user['nome_usuario']) ?></h2>
        <textarea name="descricao" placeholder="Descrição da tarefa" required></textarea>
        <input type="text" name="setor" placeholder="Nome do Setor" required>
        <select name="prioridade" required>
            <option value="">Selecione a prioridade</option>
            <option value="Baixa">Baixa</option>
            <option value="Média">Média</option>
            <option value="Alta">Alta</option>
        </select>
        <button type="submit" name="cadastrar_tarefa">Cadastrar Tarefa</button>

        <?php if (isset($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
    </form>

</body>
</html>