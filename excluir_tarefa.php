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

$stmt = $mysqli->prepare("DELETE FROM tarefa WHERE id_tarefa = ?");
$stmt->bind_param("i", $id_tarefa);

if ($stmt->execute()) {
    header("Location: gerenciar_tarefas.php?user_id=" . $user_id);
    exit;
} else {
    echo "Erro ao excluir tarefa: " . $stmt->error;
}

$stmt->close();
?>