<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

// --- Salvando a edição (quando o formulário é enviado) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) {
        die("Produto inválido.");
    }

    $id        = (int) $_POST['id'];
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = trim($_POST['preco'] ?? '');
    $estoque   = trim($_POST['estoque'] ?? '');

    if ($nome === '' || !is_numeric($preco) || !ctype_digit($estoque)) {
        die("Dados inválidos.");
    }

    $sql = "UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $nome, $descricao, $preco, $estoque, $id);

    if ($stmt->execute()) {
        header("Location: produtos.php?sucesso=1");
        exit;
    } else {
        error_log("Erro ao atualizar produto: " . $stmt->error);
        die("Erro ao salvar as alterações.");
    }
}

// --- Carregando os dados atuais (quando a página é apenas aberta) ---
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Produto inválido.");
}
$id = (int) $_GET['id'];

$sql = "SELECT id, nome, descricao, preco, estoque FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Produto não encontrado.");
}
$produto = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Produto</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Editar Produto</h1>
    <form action="produto_editar.php" method="POST">
        <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?= htmlspecialchars($produto['descricao']) ?>">
        </div>
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="number" step="0.01" min="0" id="preco" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>" required>
        </div>
        <div class="form-group">
            <label for="estoque">Estoque:</label>
            <input type="number" step="1" min="0" id="estoque" name="estoque" value="<?= htmlspecialchars($produto['estoque']) ?>" required>
        </div>
        <button type="submit">Salvar</button>
        <a href="produtos.php">Cancelar</a>
    </form>
</div>
</body>
</html>