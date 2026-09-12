<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
    <p><a href="produtos.php">Acessar Produtos</a></p>
    <p><a href="logout.php">Sair</a></p>
</div>
</body>
</html>