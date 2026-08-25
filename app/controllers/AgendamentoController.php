<?php
// app/controllers/AgendamentoController.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validação inicial: usuário logado e dados mínimos
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/usuario/login.php?erro=agendar");
        exit();
    }
    $corte_id = filter_input(INPUT_POST, 'corte_id', FILTER_VALIDATE_INT);
    $data_agendamento = $_POST['data_agendamento'] ?? '';
    $hora_agendamento = $_POST['hora_agendamento'] ?? '';
    $usuario_id = $_SESSION['usuario_id'];

    if (!$corte_id || empty($data_agendamento) || empty($hora_agendamento)) {
        header("Location: ../public/index.php?agendamento=erro_dados#servicos");
        exit();
    }
    
    date_default_timezone_set('America/Sao_Paulo');

    // 2. Validação de Dia da Semana (não permite agendar em Dom, Seg, Sáb)
    $diaDaSemana = date('w', strtotime($data_agendamento)); // 0=Domingo, 1=Segunda, 6=Sábado
    if (in_array($diaDaSemana, [0, 1, 6])) {
        header("Location: ../public/index.php?agendamento=erro_diadasemana#servicos");
        exit();
    }

    // 3. Validação de Data Passada
    $dataHoraAgendamento = new DateTime($data_agendamento . ' ' . $hora_agendamento);
    $agora = new DateTime();
    if ($dataHoraAgendamento < $agora) {
        header("Location: ../public/index.php?agendamento=erro_passado#servicos");
        exit();
    }

    // 4. Validação de Conflito de Horário (horário já ocupado)
    $stmt_check = $conn->prepare("SELECT agen_id FROM agendamento WHERE agen_data_a = ? AND agen_hora_a = ?");
    $stmt_check->bind_param("ss", $data_agendamento, $hora_agendamento);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        header("Location: ../public/index.php?agendamento=erro_ocupado#servicos");
        exit();
    }
    $stmt_check->close();

    // 5. Se todas as validações passaram, insere no banco
    $sql = "INSERT INTO agendamento (agen_data_a, agen_hora_a, usuario_id, corte_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $data_agendamento, $hora_agendamento, $usuario_id, $corte_id);

    if ($stmt->execute()) {
        header("Location: ../public/index.php?agendamento=sucesso#inicio");
    } else {
        header("Location: ../public/index.php?agendamento=erro_salvar#servicos");
    }
    $stmt->close();
    $conn->close();
}
?>