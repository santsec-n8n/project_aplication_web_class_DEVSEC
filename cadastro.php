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
$sql = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)"; 
$stmt = $conn->prepare($sql); 
 
if ($stmt === false) { 
   error_log("Erro ao preparar SQL: " . $conn->error); 
   die("Não foi possível processar o cadastro no momento."); 
} 
 
$stmt->bind_param("ssss", $nome, $email, $telefone, $senhaHash); 
 
try {
    if ($stmt->execute()) { 
       header("Location: index.html?sucesso=1"); 
       exit; 
    } else { 
       error_log("Erro ao cadastrar: " . $stmt->error); 
       die("Erro ao cadastrar. Tente novamente mais tarde."); 
    }
} catch (mysqli_sql_exception $e) {
   // Verifica se é violação de UNIQUE (código 1062)
   if ($e->getCode() === 1062) {
       die("Já existe um cadastro com este e-mail.");
   }
   // Outros erros de banco de dados
   error_log("Erro ao cadastrar: " . $e->getMessage());
   die("Erro ao cadastrar. Tente novamente mais tarde.");
} 
$stmt->close(); 
$conn->close();
