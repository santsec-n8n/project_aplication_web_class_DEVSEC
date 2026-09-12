<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Produto inválido.");
}
$id = (int) $_GET['id'];

$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: produtos.php?sucesso=1");
    exit;
} else {
    error_log("Erro ao excluir produto: " . $stmt->error);
    die("Erro ao excluir o produto.");
}