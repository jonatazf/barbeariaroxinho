<?php
// app/includes/funcoes.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

$acao = $_GET['acao'] ?? '';

// --- AÇÃO: CARREGA DADOS PARA O CALENDÁRIO ---
if ($acao == 'carregaCalendario') {
    $ano = filter_input(INPUT_GET, 'ano', FILTER_VALIDATE_INT);
    $mes = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT);
    if (!$ano || !$mes) { echo json_encode(['erro' => 'Ano e mês inválidos']); exit; }

    $primeiroDia = "$ano-$mes-01";
    $ultimoDia = date("Y-m-t", strtotime($primeiroDia));

    // CORREÇÃO: Busca dias inativos (folgas, feriados) que ocupam o dia inteiro
    $stmt = $conn->prepare("
        SELECT diaInativo_data_inativa, diaInativo_motivo 
        FROM dia_inativo 
        WHERE diaInativo_data_inativa BETWEEN ? AND ? 
        AND diaInativo_hora_inicio IS NULL
    ");
    $stmt->bind_param("ss", $primeiroDia, $ultimoDia);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close(); // Fecha a primeira consulta

    // Cria um array onde a chave é a data e o valor é o motivo
    $diasInativos = [];
    foreach ($resultado as $row) {
        $diasInativos[$row['diaInativo_data_inativa']] = $row['diaInativo_motivo'] ?: 'Indisponível';
    }
    
    // Busca dias que já possuem agendamentos para destacar
    $stmtAg = $conn->prepare("SELECT DISTINCT agen_data_a FROM agendamento WHERE agen_data_a BETWEEN ? AND ?");
    $stmtAg->bind_param("ss", $primeiroDia, $ultimoDia);
    $stmtAg->execute();
    $resultadoAgendados = $stmtAg->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtAg->close(); // Fecha a segunda consulta

    $diasComAgendamento = [];
    foreach($resultadoAgendados as $row){
        $diasComAgendamento[] = $row['agen_data_a'];
    }

    // Envia os dois conjuntos de dados para o frontend
    echo json_encode(['inativos' => $diasInativos, 'comAgendamento' => $diasComAgendamento]);
    exit;
}

// --- AÇÃO: BUSCA HORÁRIOS DISPONÍVEIS ---
if ($acao == 'buscaHorarios') {
    $data = $_GET['data'] ?? '';
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data)) {
        echo json_encode(['erro' => 'Data inválida']); exit;
    }

    $horariosDeTrabalho = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
    
    $stmtAgendados = $conn->prepare("SELECT TIME_FORMAT(agen_hora_a, '%H:%i') as hora FROM agendamento WHERE agen_data_a = ?");
    $stmtAgendados->bind_param("s", $data);
    $stmtAgendados->execute();
    $resultadoAgendados = $stmtAgendados->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtAgendados->close();

    $horariosOcupados = array_column($resultadoAgendados, 'hora');

    $stmtInativos = $conn->prepare("SELECT diaInativo_hora_inicio, diaInativo_hora_fim FROM dia_inativo WHERE diaInativo_data_inativa = ? AND diaInativo_hora_inicio IS NOT NULL");
    $stmtInativos->bind_param("s", $data);
    $stmtInativos->execute();
    $resultadoInativos = $stmtInativos->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtInativos->close();

    foreach($resultadoInativos as $row){
        $inicio = new DateTime($row['diaInativo_hora_inicio']);
        $fim = new DateTime($row['diaInativo_hora_fim']);
        foreach($horariosDeTrabalho as $horario){
            $horarioAtual = new DateTime($horario);
            if($horarioAtual >= $inicio && $horarioAtual < $fim){
                $horariosOcupados[] = $horario;
            }
        }
    }

    echo json_encode(['todosOsHorarios' => $horariosDeTrabalho, 'horariosOcupados' => array_unique($horariosOcupados)]);
    exit;
}
?>