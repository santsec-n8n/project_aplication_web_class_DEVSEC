<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Produtos</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container-lg">
    <div class="topo">
        <h1>Produtos</h1>
        <a href="produto_novo.php" class="btn">Novo Produto</a>
    </div>

    <?php if (!empty($_GET['sucesso'])): ?>
        <p class="sucesso">Operação realizada com sucesso.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nome</th><th>Preço</th><th>Estoque</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT id, nome, preco, estoque FROM produtos ORDER BY id DESC";
            $res = $conn->query($sql);

            if ($res->num_rows === 0) {
                echo '<tr><td colspan="5">Nenhum produto cadastrado.</td></tr>';
            }

            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . (int) $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>R$ " . number_format((float) $row['preco'], 2, ',', '.') . "</td>";
                echo "<td>" . (int) $row['estoque'] . "</td>";
                echo "<td>";
                echo "<a href='produto_editar.php?id=" . (int) $row['id'] . "'>Editar</a> ";
                echo "<a href='produto_excluir.php?id=" . (int) $row['id'] . "' onclick='return confirm(\"Deseja excluir?\")'>Excluir</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <p><a href="home.php">Voltar ao painel</a></p>
</div>
</body>
</html>
