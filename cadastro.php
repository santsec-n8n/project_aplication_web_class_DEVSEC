<?php 
require 'db.php'; 
 
// Recebe os dados do formulário 
$nome     = trim($_POST['nome'] ?? ''); 
$email    = trim($_POST['email'] ?? ''); 
$telefone = trim($_POST['telefone'] ?? ''); 
$senhaTexto = $_POST['senha'] ?? ''; 
 
// Validação básica de entrada 
if ($nome === '' || $email === '' || $senhaTexto === '') { 
   die("Preencha todos os campos obrigatórios."); 
} 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
   die("E-mail inválido."); 
} 
 
// Nunca gravar senha em texto puro — gera um hash seguro 
$senhaHash = password_hash($senhaTexto, PASSWORD_DEFAULT); 
 
// Prepared statement: os "?" são substituídos com segurança pelo driver mysqli 
$sql = "INSERT INTO usuario (nome, email, telefone, senha) VALUES (?, ?, ?, ?)"; 
$stmt = $conn->prepare($sql); 
 
if ($stmt === false) { 
   error_log("Erro ao preparar SQL: " . $conn->error); 
   die("Não foi possível processar o cadastro no momento."); 
} 
 
$stmt->bind_param("ssss", $nome, $email, $telefone, $senhaHash); 
 
if ($stmt->execute()) { 
   header("Location: index.html?sucesso=1"); 
   exit; 
} else { 
   if ($conn->errno === 1062) { // código de erro do MySQL para violação de UNIQUE 
       die("Já existe um cadastro com este e-mail."); 
   } 
   error_log("Erro ao cadastrar: " . $stmt->error); 
   die("Erro ao cadastrar. Tente novamente mais tarde."); 
} 
 
$stmt->close(); 
$conn->close();
