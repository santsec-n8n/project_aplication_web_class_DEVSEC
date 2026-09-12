<?php 
session_start(); 
require 'db.php'; // já usa .env, usuário de aplicação e utf8mb4 (Etapa 13) 
 
if ($_SERVER["REQUEST_METHOD"] === "POST") { 
    $email = trim($_POST['email'] ?? ''); 
    $senhaDigitada = $_POST['senha'] ?? ''; 
 
    if ($email === '' || $senhaDigitada === '') { 
        header("Location: login.html?erro=" . urlencode("Preencha usuário e senha.")); 
        exit; 
    } 
 
    // Busca o usuário pelo e-mail (prepared statement) 
    $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?"; 
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("s", $email); 
    $stmt->execute(); 
    $result = $stmt->get_result(); 
 
    if ($result->num_rows > 0) { 
        $linha = $result->fetch_assoc(); 
 
        // Compara a senha digitada com o hash salvo, sem nunca descriptografar o hash 
        if (password_verify($senhaDigitada, $linha['senha'])) { 
            // Regenera o ID de sessão ao logar — evita "session fixation" 
            session_regenerate_id(true); 
            $_SESSION['usuario_id'] = $linha['id']; 
            $_SESSION['usuario_nome'] = $linha['nome']; 
            header("Location: home.php"); 
            exit; 
        } 
    } 
 
    // Mensagem intencionalmente genérica: não revela se foi o e-mail ou a senha que errou 
    header("Location: login.html?erro=" . urlencode("E-mail ou senha inválidos.")); 
    exit; 
} 
 
$conn->close();
