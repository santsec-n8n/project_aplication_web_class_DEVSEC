<?php 
session_start(); 
require 'db.php'; 
 
if (!isset($_SESSION['usuario_id'])) { 
   header("Location: login.html"); 
   exit; 
} 
 
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) { 
   die("Cliente inválido."); 
} 
$id = (int) $_GET['id']; 
 
$sql = "DELETE FROM usuarios WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $id); 
 
if ($stmt->execute()) { 
   header("Location: home.php"); 
   exit; 
} else { 
   error_log("Erro ao excluir cliente: " . $stmt->error); 
   die("Erro ao excluir o cliente."); 
} 
 