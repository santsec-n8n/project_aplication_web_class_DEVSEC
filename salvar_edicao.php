<?php 
session_start(); 
require 'db.php'; 
 
if (!isset($_SESSION['usuario_id'])) { 
   header("Location: login.html"); 
   exit; 
} 
 
if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) { 
   die("Cliente inválido."); 
} 
 
$id       = (int) $_POST['id']; 
$nome     = trim($_POST['nome'] ?? ''); 
$email    = trim($_POST['email'] ?? ''); 
$telefone = trim($_POST['telefone'] ?? ''); 
 
if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { 
   die("Dados inválidos."); 
} 
 
$sql = "UPDATE usuario SET nome = ?, email = ?, telefone = ? WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("sssi", $nome, $email, $telefone, $id); 
 
if ($stmt->execute()) { 
   header("Location: home.php"); 
   exit; 
} else { 
   error_log("Erro ao atualizar cliente: " . $stmt->error); 
   die("Erro ao salvar as alterações."); 
} 
