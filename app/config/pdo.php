<?php
// config/database.php (PDO Connection)

$host = 'localhost';
$dbname = 'roxinhos_barber'; 
$user = 'admin_roxinho'; 
$password = 'Roxinhos12@'; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em produção, você deve logar o erro e mostrar uma mensagem genérica.
    die("Erro na conexão com o banco de dados."); 
}

// Lembre-se que as tabelas 'usuarios', 'agendamento' e 'cortes' devem existir.
// A tabela 'usuarios' precisa de 'senha_hash' e 'usuario_tipo', e para os KPIs, 'data_cadastro'.
?>