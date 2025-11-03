<?php
$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$id_tarefa = $_GET['id_tarefa'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$id_tarefa || !$user_id) {
    die("Tarefa ou usuário não especificado.");
}

$stmt = $mysqli->prepare("SELECT * FROM tarefa WHERE id_tarefa = ?");
$stmt->bind_param("i", $id_tarefa);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Tarefa não encontrada.");
}
$tarefa = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['salvar'])) {
    $descricao = $_POST['descricao'] ?? "";
    $nome = $_POST['nome'] ?? "";
    $prioridade = $_POST['prioridade'] ?? "";
    $status = $_POST['status'] ?? "";

    if (!empty($descricao) && !empty($nome) && !empty($prioridade) && !empty($status)) {
        $stmt = $mysqli->prepare("
            UPDATE tarefa 
            SET descricao = ?, nome = ?, prioridade = ?, status = ?
            WHERE id_tarefa = ?
        ");
        $stmt->bind_param("ssssi", $descricao, $nome, $prioridade, $status, $id_tarefa);
        if ($stmt->execute()) {
            header("Location: gerenciar_tarefas.php?user_id=" . $user_id);
            exit;
        } else {
            $erro = "Erro ao atualizar tarefa: " . $stmt->error;
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
    <title>Editar Tarefa</title>
</head>
<body>

    <h2>Editar Tarefa</h2>

    <form method="post">
        <label>Descrição:</label><br>
        <textarea name="descricao" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea><br>

        <label>Setor:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($tarefa['nome']) ?>" required><br>

        <label>Prioridade:</label><br>
        <select name="prioridade" required>
            <option value="Baixa" <?= $tarefa['prioridade'] == 'Baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="Média" <?= $tarefa['prioridade'] == 'Média' ? 'selected' : '' ?>>Média</option>
            <option value="Alta" <?= $tarefa['prioridade'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
        </select><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option value="a fazer" <?= $tarefa['status'] == 'a fazer' ? 'selected' : '' ?>>A Fazer</option>
            <option value="fazendo" <?= $tarefa['status'] == 'fazendo' ? 'selected' : '' ?>>Fazendo</option>
            <option value="pronto" <?= $tarefa['status'] == 'pronto' ? 'selected' : '' ?>>Pronto</option>
        </select><br>

        <button type="submit" name="salvar">Salvar Alterações</button>
        <a href="gerenciar_tarefas.php?user_id=<?= $user_id ?>">Cancelar</a>
    </form>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

</body>
</html>