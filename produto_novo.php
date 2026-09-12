<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = trim($_POST['preco'] ?? '');
    $estoque   = trim($_POST['estoque'] ?? '');

    if ($nome === '' || $preco === '' || $estoque === '') {
        $erro = 'Preencha nome, preço e estoque.';
    } elseif (!is_numeric($preco) || $preco < 0) {
        $erro = 'Preço inválido.';
    } elseif (!ctype_digit($estoque)) {
        $erro = 'Estoque deve ser um número inteiro.';
    } else {
        $sql = "INSERT INTO produtos (nome, descricao, preco, estoque) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Erro ao preparar SQL: " . $conn->error);
            $erro = 'Não foi possível processar o cadastro no momento.';
        } else {
            $stmt->bind_param("ssdi", $nome, $descricao, $preco, $estoque);

            if ($stmt->execute()) {
                header("Location: produtos.php?sucesso=1");
                exit;
            } else {
                error_log("Erro ao cadastrar produto: " . $stmt->error);
                $erro = 'Erro ao cadastrar. Tente novamente mais tarde.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Produto</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Novo Produto</h1>
    <?php if ($erro): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <form action="produto_novo.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao">
        </div>
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="number" step="0.01" min="0" id="preco" name="preco" required>
        </div>
        <div class="form-group">
            <label for="estoque">Estoque:</label>
            <input type="number" step="1" min="0" id="estoque" name="estoque" required>
        </div>
        <div class="form-group">
            <button type="submit">Cadastrar</button>
        </div>
    </form>
    <p><a href="produtos.php">Voltar para a listagem</a></p>
</div>
</body>
</html>