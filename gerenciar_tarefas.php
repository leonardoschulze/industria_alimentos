<?php

$mysqli = new mysqli("localhost", "root", "root", "industria_alimento");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    die("Usuário não especificado.");
}

function buscarTarefas($mysqli, $user_id, $status)
{
    $stmt = $mysqli->prepare("SELECT * FROM tarefa WHERE fk_id_usuario = ? AND status = ?");
    $stmt->bind_param("is", $user_id, $status);
    $stmt->execute();
    return $stmt->get_result();
}

$tarefas_fazer = buscarTarefas($mysqli, $user_id, 'a fazer');
$tarefas_fazendo = buscarTarefas($mysqli, $user_id, 'fazendo');
$tarefas_pronto = buscarTarefas($mysqli, $user_id, 'pronto');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Tarefas</title>

 <script>
        function confirmarExclusao(idTarefa, userId) {
            if (confirm("Tem certeza de que deseja excluir esta tarefa?")) {
                window.location.href = "excluir_tarefa.php?id_tarefa=" + idTarefa + "&user_id=" + userId;
            }
        }
    </script>
</head>

<body>

    <h2>Gerenciar Tarefas</h2>

    <div class="container">
        <?php
        $colunas = [
            'A Fazer' => $tarefas_fazer,
            'Fazendo' => $tarefas_fazendo,
            'Pronto' => $tarefas_pronto
        ];

        foreach ($colunas as $titulo => $tarefas): ?>
            <div class="coluna">
                <h3><?= $titulo ?></h3>
                <?php while ($tarefa = $tarefas->fetch_assoc()): ?>
                    <div class="tarefa">
                        <p><strong><?= htmlspecialchars($tarefa['descricao']) ?></strong></p>
                        <p>Setor: <?= htmlspecialchars($tarefa['nome']) ?></p>
                        <p>Prioridade: <?= htmlspecialchars($tarefa['prioridade']) ?></p>
                        <p>Status: <?= htmlspecialchars($tarefa['status']) ?></p>

                        <div class="botoes">
                            <a class="editar" href="editar_tarefa.php?id_tarefa=<?= $tarefa['id_tarefa'] ?>&user_id=<?= $user_id ?>">Editar</a>
                            <a class="excluir" href="javascript:void(0);" onclick="confirmarExclusao(<?= $tarefa['id_tarefa'] ?>, <?= $user_id ?>)">Excluir</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="index.php" class="voltar">← Voltar ao cadastro de usuário</a>

</body>
</html>