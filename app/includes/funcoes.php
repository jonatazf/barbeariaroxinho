<?php
// Linhas de depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Caminho corrigido para o config
require_once __DIR__ . '/../config/database.php';

$acao = $_GET['acao'] ?? '';

// Ação para carregar os dados do calendário
if ($acao == 'carregaCalendario') {
    $ano = filter_input(INPUT_GET, 'ano', FILTER_VALIDATE_INT);
    $mes = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT);

    if (!$ano || !$mes) { echo json_encode(['erro' => 'Ano e mês inválidos']); exit; }

    $primeiroDia = "$ano-$mes-01";
    $ultimoDia = date("Y-m-t", strtotime($primeiroDia));

    // Busca dias inativos (sem alteração)
    $stmtInativos = $conn->prepare("SELECT diaInativo_data_inativa FROM dia_inativo WHERE diaInativo_data_inativa BETWEEN ? AND ?");
    $stmtInativos->bind_param("ss", $primeiroDia, $ultimoDia);
    $stmtInativos->execute();
    $resultadoInativos = $stmtInativos->get_result();
    $diasInativos = [];
    while ($row = $resultadoInativos->fetch_assoc()) {
        $diasInativos[] = $row['diaInativo_data_inativa'];
    }
    $stmtInativos->close();

    // --- INÍCIO DA NOVA LÓGICA ---
    // Busca dias que já possuem agendamentos
    $stmtAgendados = $conn->prepare("
        SELECT DISTINCT agen_data_a 
        FROM agendamento 
        WHERE agen_data_a BETWEEN ? AND ?
    ");
    $stmtAgendados->bind_param("ss", $primeiroDia, $ultimoDia);
    $stmtAgendados->execute();
    $resultadoAgendados = $stmtAgendados->get_result();
    $diasComAgendamento = [];
    while ($row = $resultadoAgendados->fetch_assoc()) {
        $diasComAgendamento[] = $row['agen_data_a'];
    }
    $stmtAgendados->close();
    // --- FIM DA NOVA LÓGICA ---

    // Envia as duas listas no JSON
    echo json_encode([
        'inativos' => $diasInativos,
        'comAgendamento' => $diasComAgendamento
    ]);
    exit;
}

// Ação para buscar os horários (sem alteração)
if ($acao == 'buscaHorarios') {
    $data = $_GET['data'] ?? '';
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data)) {
        echo json_encode(['erro' => 'Data inválida']); exit;
    }

    $todosOsHorarios = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'];

    $stmtAgendados = $conn->prepare("SELECT TIME_FORMAT(agen_hora_a, '%H:%i') as hora FROM agendamento WHERE agen_data_a = ?");
    $stmtAgendados->bind_param("s", $data);
    $stmtAgendados->execute();
    $resultadoAgendados = $stmtAgendados->get_result();
    $horariosOcupados = [];
    while ($row = $resultadoAgendados->fetch_assoc()) {
        $horariosOcupados[] = $row['hora'];
    }
    $stmtAgendados->close();

    $resposta = [
        'todosOsHorarios' => $todosOsHorarios,
        'horariosOcupados' => $horariosOcupados
    ];

    echo json_encode($resposta);
    exit;
}

echo json_encode(['erro' => 'Ação não especificada ou inválida.']);
?>