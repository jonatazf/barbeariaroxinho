<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BLOQUEIO DE SEGURANÇA
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

// Inclui a conexão
require_once '../../config/database.php';

// Pega e valida o ID da URL
$usuario_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($usuario_id) {
    $stmt = $conn->prepare("DELETE FROM usuario WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?sucesso=excluido");
    } else {
        header("Location: usuarios.php?erro=falha_excluir");
    }
    $stmt->close();
} else {
    header("Location: usuarios.php?erro=id_invalido");
}

$conn->close();
exit();
?>