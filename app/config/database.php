<?php
// ATENÇÃO: COLOQUE SEU USUÁRIO E SENHA DO MYSQL AQUI
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'admin_roxinho'); // <-- Padrão do XAMPP
define('DB_PASSWORD', 'Roxinhos12@');     // <-- Padrão do XAMPP
define('DB_NAME', 'roxinhos_barber');

/*// ATENÇÃO: COLOQUE SEU USUÁRIO E SENHA DO MYSQL AQUI
define('DB_SERVER', 'sql300.infinityfree.com');
define('DB_USERNAME', 'if0_40084658'); // <-- Padrão do XAMPP
define('DB_PASSWORD', 'nGhd8lXREbiJ9K');     // <-- Padrão do XAMPP
define('DB_NAME', 'if0_40084658_barbearia');
*/

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Erro fatal de conexão com o banco de dados: " . $conn->connect_error);
}
?>