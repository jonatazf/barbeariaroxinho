<?php
// dashboard.php

if (session_status() === PHP_SESSION_NONE) session_start();

// --- VERIFICAÇÃO DE SEGURANÇA ---
// Se não existir sessão ou o tipo de usuário não for 1 (Admin)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    // Redireciona para o login com mensagem de erro
    header("Location: ../../views/usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/pdo.php';

// Crie os formatadores de data (Substituindo o strftime obsoleto)
$fmt_titulo = new IntlDateFormatter('pt_BR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MMMM \'de\' yyyy');
$fmt_dropdown = new IntlDateFormatter('pt_BR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MMMM/yyyy');

$nome_admin = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Admin';

// --- Lógica de Filtro Mensal/Anual ---
$filtro_mes_ano = isset($_GET['mes_ano']) ? $_GET['mes_ano'] : date('Y-m');
$data_inicio_filtro = date('Y-m-01', strtotime($filtro_mes_ano));
$data_fim_filtro = date('Y-m-t', strtotime($filtro_mes_ano));
$filtro_ano = date('Y', strtotime($filtro_mes_ano));

// --- Processamento de Dados para KPIs e Gráficos ---

// 1. KPIs (Filtrados pelo Mês/Ano)
$kpi_stmt = $conn->prepare("SELECT SUM(c.corte_preco) AS total_faturado, COUNT(a.agen_id) AS total_pedidos FROM agendamento a JOIN corte c ON a.corte_id = c.corte_id JOIN usuario u ON a.usuario_id = u.usuario_id WHERE a.agen_status IN ('Confirmado', 'Concluído') AND a.agen_data_a BETWEEN :inicio AND :fim");
$kpi_stmt->bindParam(':inicio', $data_inicio_filtro);
$kpi_stmt->bindParam(':fim', $data_fim_filtro);
$kpi_stmt->execute();
$kpis = $kpi_stmt->fetch(PDO::FETCH_ASSOC);

$total_faturado_mensal = $kpis['total_faturado'] ?? 0;
$total_pedidos_mensal = $kpis['total_pedidos'] ?? 0;
$ticket_medio_mensal = ($total_pedidos_mensal > 0) ? ($total_faturado_mensal / $total_pedidos_mensal) : 0;

// 2. Gráfico de Rosca (Filtrado pelo Mês/Ano)
$servicos_stmt = $conn->prepare("SELECT c.corte_nome, SUM(c.corte_preco) AS total_vendido, COUNT(a.agen_id) AS total_unidades FROM agendamento a JOIN corte c ON a.corte_id = c.corte_id JOIN usuario u ON a.usuario_id = u.usuario_id WHERE a.agen_status IN ('Confirmado', 'Concluído') AND a.agen_data_a BETWEEN :inicio AND :fim GROUP BY c.corte_nome ORDER BY total_vendido DESC");
$servicos_stmt->bindParam(':inicio', $data_inicio_filtro);
$servicos_stmt->bindParam(':fim', $data_fim_filtro);
$servicos_stmt->execute();
$dados_servicos_mensal = $servicos_stmt->fetchAll(PDO::FETCH_ASSOC);

$maior_faturamento_mensal = $dados_servicos_mensal[0]['corte_nome'] ?? 'Nenhum Serviço';
$chart_servicos_labels = json_encode(array_column($dados_servicos_mensal, 'corte_nome'));
$chart_servicos_data = json_encode(array_column($dados_servicos_mensal, 'total_vendido'));
$total_unidades_mensal = array_sum(array_column($dados_servicos_mensal, 'total_unidades'));

// 3. Gráfico de Barras (Filtrado pelo Ano)
$meses_stmt = $conn->prepare("SELECT DATE_FORMAT(agen_data_a, '%Y-%m') AS mes_ano, SUM(c.corte_preco) AS faturamento_mensal FROM agendamento a JOIN corte c ON a.corte_id = c.corte_id JOIN usuario u ON a.usuario_id = u.usuario_id WHERE a.agen_status IN ('Confirmado', 'Concluído') AND YEAR(a.agen_data_a) = :ano GROUP BY mes_ano ORDER BY mes_ano ASC");
$meses_stmt->bindParam(':ano', $filtro_ano);
$meses_stmt->execute();
$faturamento_mensal_anual = $meses_stmt->fetchAll(PDO::FETCH_ASSOC);

$meses_ptbr = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
$chart_mensal_data_anual = [];
$chart_mensal_labels_anual = [];
for ($i = 1; $i <= 12; $i++) {
    $mes_str = date('m', mktime(0, 0, 0, $i, 1, $filtro_ano));
    $label_ptbr = $meses_ptbr[$i - 1];
    $faturado_no_mes = 0;
    foreach ($faturamento_mensal_anual as $fm) {
        if (substr($fm['mes_ano'], 5) === $mes_str) {
            $faturado_no_mes = $fm['faturamento_mensal'];
            break;
        }
    }
    if ($faturado_no_mes > 0 || ($filtro_ano == date('Y') && $i <= date('m')) || $filtro_ano < date('Y')) {
        $chart_mensal_data_anual[] = (float)$faturado_no_mes;
        $chart_mensal_labels_anual[] = $label_ptbr;
    }
}
$chart_mensal_labels = json_encode($chart_mensal_labels_anual);
$chart_mensal_data = json_encode($chart_mensal_data_anual);

// 4. Histórico para Dropdown
$historico_stmt = $conn->prepare("SELECT DISTINCT DATE_FORMAT(agen_data_a, '%Y-%m') AS mes_ano FROM agendamento ORDER BY mes_ano DESC");
$historico_stmt->execute();
$meses_historico = $historico_stmt->fetchAll(PDO::FETCH_COLUMN);

$titulo_mes = ucfirst($fmt_titulo->format(strtotime($filtro_mes_ano)));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>
        :root {
            --cor-fundo: #121212;
            --cor-fundo-card: #181828;
            --cor-fundo-secundario: #1f1f2e;
            --cor-texto: #f0f0f0;
            --cor-primaria: #d633ff;
            --cor-secundaria: #e6b800;
        }
        body {
            font-family: 'Barlow Condensed', sans-serif;
            background-image: url('../../public/assets/img/background.png');
            color: var(--cor-texto);
        }
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 1000; width: 250px; padding: 20px;
            background-color: #000;
            display: flex; flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar .logo {
            font-size: 1.8rem; font-weight: 700;
            color: var(--cor-primaria); text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .nav-link {
            color: var(--cor-texto); font-size: 1.2rem;
            padding: 10px 15px; margin-bottom: 5px;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--cor-primaria);
            color: #fff;
        }
        .sidebar .logout-link { margin-top: auto; }
        
        .main-content {
            margin-left: 250px; padding: 30px;
            transition: margin-left 0.3s ease;
            /* Sem blur ou bloqueio */
        }
        
        .form-control-dark {
            background-color: var(--cor-fundo-secundario) !important;
            color: #fff !important;
            border-radius: 14px;
            padding: 10px;
            border: 1.5px solid var(--cor-primaria);
            font-size: 1.1rem;
        }
        .form-control-dark:focus, .form-control-dark:active {
            border-color: var(--cor-secundaria);
            box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25);
        }

        .header h2 { color: var(--cor-primaria); font-weight: 700; }
        .card-kpi h4 { color: var(--cor-texto); font-weight: 700; }
        .card-kpi {
            background-color: var(--cor-fundo-card);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 20px;
            color: var(--cor-texto);
        }
        .kpi-value {
            font-size: 2.5rem; font-weight: 700;
            color: var(--cor-secundaria);
        }
        .kpi-value.text-primary { color: var(--cor-primaria) !important; }
        .kpi-label {
            font-size: 1.2rem; color: #ccc;
            font-weight: 400; text-transform: uppercase;
        }
        
        .chart-container { height: 320px; width: 100%; }
        
        .mobile-header { display: none; background-color: #000; padding: 10px 15px; align-items: center; }
        .menu-toggle { font-size: 1.5rem; color: #fff; background: none; border: none; }
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.is-active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .header { display: none; }
            .mobile-header { display: flex; }
        }
    </style>
</head>
<body>
    <div id="overlay" class="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="logo">ROXINHO'S BARBER <br> ADMIN</div>
        <div> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-graph-up me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
            <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-people-fill me-2"></i> Usuários</a></li>
            <li class="nav-item"><a class="nav-link" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a></li>
            <li class="nav-item"><a class="nav-link" href="estoque.php"><i class="bi bi-box2-fill me-2"></i> Estoque</a></li>
            <li class="nav-item"><a class="nav-link" href="diasInativos.php"><i class="bi bi-calendar2-x-fill me-2"></i> Dias Inativos</a></li>
        </ul>
        <ul class="nav flex-column logout-link">
            <li class="nav-item"><a class="nav-link" href="../../controllers/UsuarioController.php?logout=1"><i class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <div class="mobile-header">
            <button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
            <div class="logo ms-3">ROXINHO'S ADM</div>
        </div>

        <header class="header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-graph-up-arrow me-2"></i>Dashboard de Performance</h2>
                    <p class="lead">Métricas de Faturamento, Serviços e Pedidos.</p>
                </div>
                <div class="d-flex align-items-center">
                    <h4 class="me-3 mb-0 text-primary d-none d-lg-block"><?php echo $titulo_mes; ?></h4>
                    <select id="filtroMesAno" class="form-select form-control-dark" onchange="window.location.href='dashboard.php?mes_ano='+this.value" style="width: 200px;">
                        <option value="<?php echo date('Y-m'); ?>">Mês Atual</option>
                        <?php foreach($meses_historico as $ma): ?>
                            <option value="<?php echo $ma; ?>" <?php echo ($ma == $filtro_mes_ano) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($fmt_dropdown->format(strtotime($ma))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </header>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card-kpi d-flex flex-column h-100">
                    <div class="mb-3">
                        <span class="kpi-value text-primary">R$ <?php echo number_format($total_faturado_mensal, 2, ',', '.'); ?></span>
                        <div class="kpi-label">TOTAL FATURADO (Mês)</div>
                    </div>
                    <div class="mb-3">
                        <span class="kpi-value"><?php echo $total_unidades_mensal; ?></span>
                        <div class="kpi-label">TOTAL UNIDADES (Serviços)</div>
                    </div>
                    <div class="mb-3">
                        <span class="kpi-value"><?php echo $total_pedidos_mensal; ?></span>
                        <div class="kpi-label">TOTAL PEDIDOS (Agendamentos)</div>
                    </div>
                    <div class="mb-3">
                        <span class="kpi-value">R$ <?php echo number_format($ticket_medio_mensal, 2, ',', '.'); ?></span>
                        <div class="kpi-label">GASTO MÉDIO POR CLIENTE</div>
                    </div>
                    <div class="mt-auto">
                        <span class="kpi-value text-primary"><?php echo htmlspecialchars($maior_faturamento_mensal); ?></span>
                        <div class="kpi-label">MAIOR SERVIÇO VENDIDO</div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card-kpi h-100">
                    <h4 class="mb-3 text-secondary">FATURAMENTO POR SERVIÇO (Mês)</h4>
                    <div class="chart-container"><canvas id="chartRosca"></canvas></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-kpi h-100">
                    <h4 class="mb-3 text-secondary">HISTÓRICO MENSAL (Ano: <?php echo $filtro_ano; ?>)</h4>
                    <div class="chart-container"><canvas id="chartBarras"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dados PHP para JS
        const CH_SERVICOS_LABELS = <?php echo $chart_servicos_labels; ?>;
        const CH_SERVICOS_DATA = <?php echo $chart_servicos_data; ?>;
        const CH_MENSAL_LABELS = <?php echo $chart_mensal_labels; ?>;
        const CH_MENSAL_DATA = <?php echo $chart_mensal_data; ?>;

        // Cores da Identidade Visual
        const COR_PRIMARIA = 'rgb(214,51,255)'; // Roxo
        const COR_SECUNDARIA = 'rgb(230,184,0)'; // Amarelo
        const COR_TEXTO = 'rgb(240,240,240)';
        const COR_FUNDO_CARD = 'rgb(24,24,40)';
        const COR_GRID = 'rgba(255, 255, 255, 0.1)';
        const PALETA_ROSCA = [COR_PRIMARIA, COR_SECUNDARIA, '#f0f0f0', '#b026d1', '#997a00', '#6a006a'];

        // Configuração global do Chart.js
        Chart.defaults.color = COR_TEXTO;
        Chart.defaults.borderColor = COR_GRID;
        Chart.register(ChartDataLabels);

        // Menu mobile
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const menuToggle = document.getElementById('menu-toggle');
        const overlay = document.getElementById('overlay');
        if (menuToggle) menuToggle.addEventListener('click', () => { sidebar.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
        if (overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('is-active'); overlay.classList.remove('is-active'); });

        // Funções de Gráficos Chart.js
        function criarGraficoRosca() {
            const ctx = document.getElementById('chartRosca');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: CH_SERVICOS_LABELS,
                    datasets: [{
                        data: CH_SERVICOS_DATA,
                        backgroundColor: PALETA_ROSCA,
                        borderColor: COR_FUNDO_CARD,
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: COR_TEXTO, boxWidth: 20 } },
                        title: { display: false },
                        datalabels: {
                            formatter: (value, ctx) => {
                                let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let percentage = (value * 100 / sum).toFixed(1) + '%';
                                return percentage;
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14,
                                family: 'Barlow Condensed'
                            }
                        }
                    }
                }
            });
        }

        function criarGraficoBarras() {
            const ctx = document.getElementById('chartBarras');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: CH_MENSAL_LABELS,
                    datasets: [{
                        label: 'Faturamento Mensal (R$)',
                        data: CH_MENSAL_DATA,
                        backgroundColor: COR_PRIMARIA,
                        borderColor: COR_PRIMARIA,
                        hoverBackgroundColor: COR_SECUNDARIA,
                        hoverBorderColor: COR_SECUNDARIA,
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                color: COR_TEXTO, 
                                callback: function (value) {
                                    return 'R$' + value; 
                                } 
                            },
                            grid: { color: COR_GRID }
                        },
                        x: {
                            ticks: { color: COR_TEXTO },
                            grid: { color: 'transparent' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: COR_SECUNDARIA,
                            font: {
                                weight: 'bold',
                                size: 14,
                                family: 'Barlow Condensed'
                            },
                            formatter: (value) => {
                                return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            }
                        }
                    }
                }
            });
        }

        function inicializarDashboard() {
            if (CH_SERVICOS_DATA.length > 0) {
                criarGraficoRosca();
            } else {
                const container = document.getElementById('chartRosca')?.parentElement;
                if(container) container.innerHTML = '<div class="alert alert-secondary text-center" style="background-color: var(--cor-fundo-secundario); border: none;">Nenhum serviço vendido neste mês.</div>';
            }
            if (CH_MENSAL_DATA.length > 0) {
                criarGraficoBarras();
            } else {
                 const container = document.getElementById('chartBarras')?.parentElement;
                if(container) container.innerHTML = '<div class="alert alert-secondary text-center" style="background-color: var(--cor-fundo-secundario); border: none;">Nenhum faturamento neste ano.</div>';
            }
        }

        // Inicialização direta (sem modal)
        document.addEventListener('DOMContentLoaded', () => {
            inicializarDashboard();
        });
    </script>
</body>
</html>