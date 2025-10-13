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
$corte_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($corte_id) {
    $stmt = $conn->prepare("DELETE FROM corte WHERE corte_id = ?");
    $stmt->bind_param("i", $corte_id);

    if ($stmt->execute()) {
        header("Location: cortes.php?sucesso=excluido");
    } else {
        header("Location: cortes.php?erro=falha_excluir");
    }
    $stmt->close();
} else {
    header("Location: cortes.php?erro=id_invalido");
}

$conn->close();
exit();
?>