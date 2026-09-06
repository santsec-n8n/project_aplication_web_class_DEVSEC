<?php 
// Carrega o autoloader do Composer e a biblioteca de variáveis de ambiente 
require __DIR__ . '/vendor/autoload.php'; 
 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
$dotenv->load(); 
 
// Lê a configuração do arquivo .env (nunca versionado no Git) 
$server   = $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']; 
$user     = $_ENV['DB_USER']; 
$password = $_ENV['DB_PASSWORD']; 
$database = $_ENV['DB_NAME']; 
 
// Conecta ao banco de dados 
$conn = new mysqli($server, $user, $password, $database); 
 
// Verifica a conexão 
if ($conn->connect_error) { 
   // Em produção, nunca exiba $conn->connect_error diretamente ao usuário final 
   error_log("Erro de conexão MySQL: " . $conn->connect_error); 
   die("Não foi possível conectar ao banco de dados no momento."); 
} 
 
// Garante que acentos/caracteres em português sejam tratados corretamente 
$conn->set_charset("utf8mb4");