<?php
// ATENÇÃO: COLOQUE SEU USUÁRIO E SENHA DO MYSQL AQUI
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // <-- Padrão do XAMPP
define('DB_PASSWORD', '');     // <-- Padrão do XAMPP
define('DB_NAME', 'barbearia');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Erro fatal de conexão com o banco de dados: " . $conn->connect_error);
}
?>