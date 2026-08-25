<?php
// controllers/admin/excluirAgendamento.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Apenas admin logado pode excluir
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../../views/usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/database.php';

// Pega o id do agendamento pela URL
$agen_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$agen_id) {
    // id inválido ou ausente
    header("Location: ../../views/admin/agendamentos.php?erro=id_invalido");
    exit();
}

// Monta o DELETE com prepared statement
$stmt = $conn->prepare("DELETE FROM agendamento WHERE agen_id = ?");
if (!$stmt) {
    // erro ao preparar
    header("Location: ../../views/admin/agendamentos.php?erro=erro_preparar");
    exit();
}

$stmt->bind_param("i", $agen_id);

if ($stmt->execute()) {
    // Verifica se alguma linha foi realmente apagada
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../../views/admin/agendamentos.php?sucesso=excluido");
        exit();
    } else {
        // Nenhum agendamento com esse id
        $stmt->close();
        $conn->close();
        header("Location: ../../views/admin/agendamentos.php?erro=nao_encontrado");
        exit();
    }
} else {
    // Erro ao executar
    $stmt->close();
    $conn->close();
    header("Location: ../../views/admin/agendamentos.php?erro=erro_excluir");
    exit();
}
