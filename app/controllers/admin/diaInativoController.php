<?php
// app/controllers/admin/DiaInativoController.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../../views/usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/database.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$usuario_id = $_SESSION['usuario_id']; // Necessário para a nova tabela

switch ($acao) {
    // --- CRIAR ---
    case 'criar':
        $data = $_POST['data_inativa'] ?? '';
        $hora_inicio = !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : null;
        $hora_fim = !empty($_POST['hora_fim']) ? $_POST['hora_fim'] : null;
        $motivo = $_POST['motivo'] ?? '';

        if (empty($data)) {
            header("Location: ../../views/admin/diasInativos.php?status=erro");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO dia_inativo (diaInativo_data_inativa, diaInativo_hora_inicio, diaInativo_hora_fim, diaInativo_motivo, usuario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $data, $hora_inicio, $hora_fim, $motivo, $usuario_id);

        if ($stmt->execute()) {
            header("Location: ../../views/admin/diasInativos.php?status=sucesso_criar");
        } else {
            header("Location: ../../views/admin/diasInativos.php?status=erro");
        }
        $stmt->close();
        break;

    // --- EDITAR ---
    case 'editar':
        $id = filter_input(INPUT_POST, 'diaInativo_id', FILTER_VALIDATE_INT);
        $data = $_POST['data_inativa'] ?? '';
        $hora_inicio = !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : null;
        $hora_fim = !empty($_POST['hora_fim']) ? $_POST['hora_fim'] : null;
        $motivo = $_POST['motivo'] ?? '';

        if (!$id || empty($data)) {
            header("Location: ../../views/admin/diasInativos.php?status=erro_id");
            exit();
        }

        $stmt = $conn->prepare("UPDATE dia_inativo SET diaInativo_data_inativa = ?, diaInativo_hora_inicio = ?, diaInativo_hora_fim = ?, diaInativo_motivo = ?, usuario_id = ? WHERE diaInativo_id = ?");
        $stmt->bind_param("ssssii", $data, $hora_inicio, $hora_fim, $motivo, $usuario_id, $id);

        if ($stmt->execute()) {
            header("Location: ../../views/admin/diasInativos.php?status=sucesso_editar");
        } else {
            header("Location: ../../views/admin/diasInativos.php?status=erro");
        }
        $stmt->close();
        break;

    // --- EXCLUIR ---
    case 'excluir':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM dia_inativo WHERE diaInativo_id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: ../../views/admin/diasInativos.php?status=sucesso_excluir");
            } else {
                header("Location: ../../views/admin/diasInativos.php?status=erro");
            }
            $stmt->close();
        } else {
            header("Location: ../../views/admin/diasInativos.php?status=erro_id");
        }
        break;

    default:
        header("Location: ../../views/admin/diasInativos.php");
        break;
}
$conn->close();
?>