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
   <title>Clientes</title> 
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body> 
<div class="container mt-5"> 
   <div class="d-flex justify-content-between align-items-center mb-4"> 
       <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1> 
       <a href="logout.php" class="btn btn-outline-secondary">Sair</a> 
   </div> 
 
   <h2 class="mb-4">Lista de Clientes</h2> 
   <table class="table table-striped table-bordered"> 
       <thead class="table-dark"> 
           <tr> 
               <th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th> 
           </tr> 
       </thead> 
       <tbody> 
       <?php 
       $sql = "SELECT id, nome, email, telefone FROM usuario ORDER BY id DESC"; 
       $res = $conn->query($sql); 
       if ($res->num_rows === 0) { 
           echo '<tr><td colspan="5" class="text-center">Nenhum cliente cadastrado.</td></tr>'; 
       } 
       while ($row = $res->fetch_assoc()) { 
           echo "<tr>"; 
           echo "<td>" . (int) $row['id'] . "</td>"; 
           echo "<td>" . htmlspecialchars($row['nome']) . "</td>"; 
           echo "<td>" . htmlspecialchars($row['email']) . "</td>"; 
           echo "<td>" . htmlspecialchars($row['telefone']) . "</td>"; 
           echo "<td> 
                   <a href='editar.php?id=" . (int) $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a> 
                   <a href='excluir.php?id=" . (int) $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Deseja excluir?\")'>Excluir</a> 
                 </td>"; 
           echo "</tr>"; 
       } 
       ?> 
       </tbody> 
   </table> 
</div> 
</body> 
</html> 
