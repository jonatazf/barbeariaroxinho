<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Segurança: só admins podem chamar este script
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    http_response_code(403);
    exit('Acesso negado');
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
    $senha_digitada = $_POST['senha'];
    $admin_id = $_SESSION['usuario_id'];

    // Busca a senha com hash do admin logado no banco
    $stmt = $conn->prepare("SELECT usuario_senha FROM usuario WHERE usuario_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    // Compara a senha digitada com a senha do banco
    if ($admin && password_verify($senha_digitada, $admin['usuario_senha'])) {
        echo 'ok';
    } else {
        echo 'erro';
    }
} else {
    http_response_code(400);
    exit('Requisição inválida');
}
?>