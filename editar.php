<?php 
session_start(); 
require 'db.php'; 
 
if (!isset($_SESSION['usuario_id'])) { 
    header("Location: login.html"); 
    exit; 
} 
 
// Valida que o id é realmente um número antes de qualquer uso 
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) { 
    die("Cliente inválido."); 
} 
$id = (int) $_GET['id']; 
 
$sql = "SELECT id, nome, email, telefone FROM usuarios WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$res = $stmt->get_result(); 
 
if ($res->num_rows === 0) { 
    die("Cliente não encontrado."); 
} 
$cliente = $res->fetch_assoc(); 
?> 
<!DOCTYPE html> 
<html lang="pt-br"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Editar Cliente</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body> 
<div class="container mt-5"> 
    <h2>Editar Cliente</h2> 
    <form action="salvar_edicao.php" method="POST"> 
        <input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>"> 
        <div class="mb-3"> 
            <label>Nome</label> 
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']) ?>" required> 
        </div> 
        <div class="mb-3"> 
            <label>Email</label> 
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email']) ?>" required> 
        </div> 
        <div class="mb-3"> 
            <label>Telefone</label> 
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($cliente['telefone']) ?>"> 
        </div> 
        <button type="submit" class="btn btn-primary">Salvar</button> 
        <a href="home.php" class="btn btn-secondary">Cancelar</a> 
    </form> 
</div> 
</body> 
</html> 
