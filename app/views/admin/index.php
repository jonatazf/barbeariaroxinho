<?php
// admin/index.php

if (session_status() === PHP_SESSION_NONE) session_start();

// --- VERIFICAÇÃO DE SEGURANÇA ---
// Se não existir sessão ou o tipo de usuário não for 1 (Admin)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    // Redireciona para o login com mensagem de erro
    header("Location: ../../views/usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/pdo.php';

$nome_admin = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Admin';

function getPeriodo($tipo = 'mes')
{
    $inicio = ($tipo === 'semana') ? date('Y-m-d', strtotime('monday this week')) : date('Y-m-01');
    $fim = ($tipo === 'semana') ? date('Y-m-d', strtotime('sunday this week')) : date('Y-m-t');
    return ['inicio' => $inicio, 'fim' => $fim];
}

// 1. Busca todos os agendamentos
$stmt = $conn->prepare("SELECT a.agen_id,a.agen_data_a,a.agen_hora_a,a.agen_status,u.usuario_nome,u.usuario_tel AS usuario_tel,c.corte_nome,c.corte_preco FROM agendamento a JOIN usuario u ON a.usuario_id=u.usuario_id JOIN corte c ON a.corte_id=c.corte_id ORDER BY a.agen_data_a DESC,a.agen_hora_a ASC");
$stmt->execute();
$todos_agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$datasAgendamento = array_unique(array_column($todos_agendamentos, 'agen_data_a'));

$periodo_mes = getPeriodo('mes');
$periodo_semana = getPeriodo('semana');

// 2. Faturamento Previsto
$stmt_faturamento = $conn->prepare("SELECT SUM(c.corte_preco) as total_previsto FROM agendamento a JOIN corte c ON a.corte_id=c.corte_id WHERE (a.agen_status='Confirmado' OR a.agen_status='Concluído') AND a.agen_data_a BETWEEN :inicio AND :fim");
$stmt_faturamento->bindParam(':inicio', $periodo_mes['inicio']);
$stmt_faturamento->bindParam(':fim', $periodo_mes['fim']);
$stmt_faturamento->execute();
$faturamento_mes = $stmt_faturamento->fetch(PDO::FETCH_ASSOC)['total_previsto'] ?? 0;

$stmt_faturamento->bindParam(':inicio', $periodo_semana['inicio']);
$stmt_faturamento->bindParam(':fim', $periodo_semana['fim']);
$stmt_faturamento->execute();
$faturamento_semana = $stmt_faturamento->fetch(PDO::FETCH_ASSOC)['total_previsto'] ?? 0;

// 3. Novos Usuários Cadastrados
$stmt_usuarios = $conn->prepare("SELECT COUNT(*) as total_usuarios FROM usuario WHERE usuario_data_cadastro BETWEEN :inicio AND :fim");
$stmt_usuarios->bindParam(':inicio', $periodo_mes['inicio']);
$stmt_usuarios->bindParam(':fim', $periodo_mes['fim']);
$stmt_usuarios->execute();
$usuarios_mes = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'] ?? 0;

$stmt_usuarios->bindParam(':inicio', $periodo_semana['inicio']);
$stmt_usuarios->bindParam(':fim', $periodo_semana['fim']);
$stmt_usuarios->execute();
$usuarios_semana = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'] ?? 0;

// 4. Agendamentos Recentes
$data_hoje = date('Y-m-d');
$data_limite_recente = date('Y-m-d', strtotime('+7 days'));
$stmt_recentes = $conn->prepare("SELECT COUNT(*) as total_agendamentos FROM agendamento WHERE agen_data_a BETWEEN :hoje AND :limite AND agen_status!='Cancelado'");
$stmt_recentes->bindParam(':hoje', $data_hoje);
$stmt_recentes->bindParam(':limite', $data_limite_recente);
$stmt_recentes->execute();
$agendamentos_proxima_semana = $stmt_recentes->fetch(PDO::FETCH_ASSOC)['total_agendamentos'] ?? 0;

$json_agendamentos = json_encode($todos_agendamentos);
$json_datas = json_encode(array_values($datasAgendamento));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root {
            --cor-fundo: #121212;
            --cor-fundo-card: #181828;
            --cor-fundo-secundario: #1f1f2e;
            --cor-texto: #f0f0f0;
            --cor-primaria: #d633ff;
            --cor-secundaria: #e6b800;
        }

        .text-purple{
            color: var(--cor-primaria);
        }
        body {
            font-family: 'Barlow Condensed', sans-serif;
            background-image: url('../../public/assets/img/background.png');
            color: var(--cor-texto);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            width: 250px;
            padding: 20px;
            background-color: #000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }

        .sidebar .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--cor-primaria);
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .nav-link {
            color: var(--cor-texto);
            font-size: 1.2rem;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: background-color .3s, color .3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--cor-primaria);
            color: #fff;
        }

        .sidebar .logout-link {
            margin-top: auto;
        }

        /* Blur removido aqui */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: margin-left .3s ease;
        }

        .card-kpi {
            background-color: var(--cor-fundo-card);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 20px;
            color: var(--cor-texto);
        }

        .table-dark-custom thead th {
            color: var(--cor-texto);
            background-color: var(--cor-primaria);
        }

        .form-control-dark {
            background-color: #2a2a3e;
            color: #fff;
            border: 1px solid var(--cor-primaria);
        }

        .form-control-dark:focus {
            background-color: #2a2a3e;
            color: #fff;
            border-color: var(--cor-secundaria);
            box-shadow: 0 0 0 .25rem rgba(230, 184, 0, .25);
        }

        .mobile-header {
            display: none;
            background-color: #000;
            padding: 10px 15px;
            align-items: center;
        }

        .menu-toggle {
            font-size: 1.5rem;
            color: #fff;
            background: none;
            border: none;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, .5);
            z-index: 999;
        }

        @media (max-width:992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.is-active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                display: none;
            }

            .mobile-header {
                display: flex;
            }

            .overlay.is-active {
                display: block;
            }
        }

        .btn-kpi-select {
            color: var(--cor-secundaria);
            border-color: var(--cor-secundaria);
            font-weight: 700;
            font-size: .9rem;
        }

        .btn-kpi-select.active {
            background-color: var(--cor-secundaria);
            color: #000;
        }
    </style>
</head>

<body>
    <div id="overlay" class="overlay"></div>
    <div class="sidebar" id="sidebar">
        <div class="logo">ROXINHO'S BARBER <br> ADMIN</div>
        <div> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-graph-up me-2"></i> Dashboard</a></li>
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
        <div class="mobile-header"><button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
            <div class="logo ms-3">ROXINHO'S ADM</div>
        </div>
        <header class="header">
            <div>
                <h2>Agenda Geral e Estatísticas</h2>
                <p class="lead">Visão rápida sobre agendamentos e desempenho.</p>
            </div>
        </header>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card-kpi d-flex flex-column justify-content-between">
                    <h4 class="mb-1 text-secondary">Faturamento Previsto</h4>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <div class="btn-group btn-group-sm mb-2 text-purple" role="group"><button type="button" class="btn btn-kpi-select active text-purple" data-kpi="faturamento" data-periodo="mes">Mês</button><button type="button" class="btn btn-kpi-select text-purple" data-kpi="faturamento" data-periodo="semana">Semana</button></div>
                            <h3 class="mb-0 text-purple" id="kpiFaturamento">R$ <?php echo number_format($faturamento_mes, 2, ',', '.'); ?></h3>
                        </div><i class="bi bi-currency-dollar text-purple" style="font-size:3rem; color: #d633ff'"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-kpi d-flex flex-column justify-content-between">
                    <h4 class="mb-1 text-secondary">Agendamentos Próx. 7 Dias</h4>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <div class="btn-group btn-group-sm mb-2 invisible"><span>&nbsp;</span></div>
                            <h3 class="mb-0 text-purple" id="kpiAgendamentos"><?php echo $agendamentos_proxima_semana; ?></h3>
                        </div><i class="bi bi-calendar-range text-purple" style="font-size:3rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-kpi d-flex flex-column justify-content-between">
                    <h4 class="mb-1 text-secondary">Novos Usuários Cadastrados</h4>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <div class="btn-group btn-group-sm mb-2" role="group"><button type="button" class="btn btn-kpi-select active" data-kpi="usuarios" data-periodo="mes">Mês</button><button type="button" class="btn btn-kpi-select" data-kpi="usuarios" data-periodo="semana">Semana</button></div>
                            <h3 class="mb-0 text-purple" id="kpiUsuarios"><?php echo $usuarios_mes; ?></h3>
                        </div><i class="bi bi-person-plus-fill text-purple" style="font-size:3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-kpi mb-4 p-3">
            <h4 class="mb-3" id="mesAnoTitulo">Calendário de Agendamentos</h4>
            <div class="row">
                <div class="col-lg-6" id="calendar-container"></div>
                <div class="col-lg-6">
                    <h5 id="tituloDestaqueDia" class="mb-3">Selecione um dia</h5>
                    <div id="destaqueDia"></div>
                </div>
            </div>
        </div>
        <div class="card-kpi p-3 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label for="filtroBusca" class="form-label">Buscar por Cliente ou Serviço</label><input type="text" id="filtroBusca" class="form-control form-control-dark" placeholder="Digite um nome..."></div>
                <div class="col-md-5"><label for="filtroData" class="form-label">Filtrar por Data Específica</label><input type="date" id="filtroData" class="form-control form-control-dark"></div>
                <div class="col-md-2 d-grid"><button class="btn btn-secondary" id="limparFiltros">Limpar Filtros</button></div>
            </div>
        </div>
        <div class="card-kpi p-3">
            <h4>Lista de Agendamentos</h4>
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaAgendamentos">
                        <?php if (empty($todos_agendamentos)): ?>
                            <tr>
                                <td colspan="7" class="text-center p-4">Nenhum agendamento encontrado.</td>
                            </tr>
                        <?php else: ?> <?php foreach ($todos_agendamentos as $ag): ?>
                                <tr data-data="<?php echo $ag['agen_data_a']; ?>">
                                    <td><?php echo date("d/m/Y", strtotime($ag['agen_data_a'])); ?></td>
                                    <td><?php echo substr($ag['agen_hora_a'], 0, 5); ?></td>
                                    <td><?php echo htmlspecialchars($ag['usuario_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($ag['corte_nome']); ?></td>
                                    <td>R$ <?php echo number_format($ag['corte_preco'], 2, ',', '.'); ?></td>
                                    <td><?php $status_class = 'bg-secondary';
                                            if ($ag['agen_status'] == 'Confirmado') $status_class = 'bg-success';
                                            if ($ag['agen_status'] == 'Concluído') $status_class = 'bg-info';
                                            if ($ag['agen_status'] == 'Cancelado') $status_class = 'bg-danger'; ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($ag['agen_status']); ?></span>
                                    </td>
                                    <td>
                                        <a href="forms/editarAgendamento.php?id=<?= $ag['agen_id']; ?>">
                                            <button class="btn btn-sm btn-outline-primary" title="Editar Agendamento">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </a>
                                        <a href="../../controllers/admin/excluirAgendamento.php?id=<?= $ag['agen_id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">
                                            <button class="btn btn-sm btn-outline-danger" title="Excluir Agendamento">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </a>
                                    </td>
                                </tr><?php endforeach; ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dadosKpi = {
            faturamento: {
                mes: <?php echo $faturamento_mes; ?>,
                semana: <?php echo $faturamento_semana; ?>
            },
            usuarios: {
                mes: <?php echo $usuarios_mes; ?>,
                semana: <?php echo $usuarios_semana; ?>
            }
        };
        const datasComAgendamento = <?php echo $json_datas; ?>;
        const todosAgendamentos = <?php echo $json_agendamentos; ?>;
        let diaSelecionado = (new Date()).toISOString().split('T')[0];
        let calendarioData = new Date();
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-active');
            overlay.classList.toggle('is-active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('is-active');
            overlay.classList.remove('is-active');
        });

        // Lógica de formatação
        function formatarMoeda(valor) {
            return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Lógica do Calendário
        function trocaMes(inc) {
            calendarioData.setMonth(calendarioData.getMonth() + inc);
            montarCalendarioAdmin(calendarioData.getFullYear(), calendarioData.getMonth() + 1);
        }

        function mostrarAgendamentosDoDia(dia) {
            const lista = todosAgendamentos.filter(ag => ag.agen_data_a === dia);
            let html = '';
            const dataObj = new Date(dia + 'T00:00:00');
            const dataStrFormatada = dataObj.toLocaleDateString('pt-BR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('tituloDestaqueDia').textContent = 'Agendamentos para ' + dataStrFormatada;
            if (lista.length === 0) {
                html = '<div class="alert alert-secondary">Nenhum agendamento neste dia.</div>';
            } else {
                html = "<ul class='list-group'>";
                lista.forEach(ag => {
                    html += `<li class='list-group-item bg-dark text-white d-flex justify-content-between align-items-center'><div><b>${ag.corte_nome}</b> (${ag.agen_hora_a.substr(0, 5)})<br>Cliente: ${ag.usuario_nome}</div><span class='badge bg-secondary ms-2'>${ag.agen_status}</span></li>`;
                });
                html += '</ul>';
            }
            document.getElementById('destaqueDia').innerHTML = html;
        }

        function montarCalendarioAdmin(year, month) {
            const container = document.getElementById('calendar-container');
            container.innerHTML = '';
            const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            const primeiroDiaSemana = new Date(year, month - 1, 1).getDay();
            const totalDias = new Date(year, month, 0).getDate();
            let html = '<div class="d-flex justify-content-between align-items-center mb-2">';
            html += `<button class="btn btn-sm btn-outline-secondary" id="calPrev">&lt;</button>`;
            html += `<strong style="font-size:1.3rem;">${meses[month - 1]} / ${year}</strong>`;
            html += `<button class="btn btn-sm btn-outline-secondary" id="calNext">&gt;</button>`;
            html += '</div>';
            html += '<div class="d-grid" style="grid-template-columns: repeat(7,1fr);gap:2px;">';
            ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'].forEach(d => html += `<div class="fw-bold text-warning small text-center">${d}</div>`);
            for (let i = 0; i < primeiroDiaSemana; i++) html += '<div>&nbsp;</div>';
            for (let d = 1; d <= totalDias; d++) {
                const diaStr = `${year}-${String(month).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                let classe = 'btn btn-light btn-sm w-100 mb-1';
                if (datasComAgendamento.includes(diaStr)) classe += ' border border-warning bg-gradient';
                if (diaStr === diaSelecionado) classe += ' bg-primary text-white';
                html += `<button type="button" class="${classe}" style="min-height:34px" data-dia="${diaStr}">${d}</button>`;
            }
            html += '</div>';
            container.innerHTML = html;
            document.getElementById('calPrev').onclick = () => trocaMes(-1);
            document.getElementById('calNext').onclick = () => trocaMes(1);
            document.querySelectorAll('#calendar-container button[data-dia]').forEach(btn => {
                btn.onclick = function() {
                    diaSelecionado = this.dataset.dia;
                    montarCalendarioAdmin(year, month);
                    mostrarAgendamentosDoDia(diaSelecionado);
                }
            });
        }

        function inicializarPainel() {
            calendarioData = new Date();
            diaSelecionado = calendarioData.toISOString().split('T')[0];
            montarCalendarioAdmin(calendarioData.getFullYear(), calendarioData.getMonth() + 1);
            mostrarAgendamentosDoDia(diaSelecionado);
        }

        // Inicialização direta sem modal
        document.addEventListener('DOMContentLoaded', () => {
            // Chama o painel diretamente, sem pedir senha
            inicializarPainel();

            // KPIs
            document.querySelectorAll('.btn-kpi-select').forEach(button => {
                button.addEventListener('click', function() {
                    const kpi = this.getAttribute('data-kpi');
                    const periodo = this.getAttribute('data-periodo');
                    const valor = dadosKpi[kpi][periodo];
                    document.querySelectorAll(`.btn-kpi-select[data-kpi="${kpi}"]`).forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    if (kpi === 'faturamento') {
                        document.getElementById('kpiFaturamento').textContent = formatarMoeda(valor);
                    } else if (kpi === 'usuarios') {
                        document.getElementById('kpiUsuarios').textContent = valor;
                    }
                });
            });

            // Filtros
            const filtroBusca = document.getElementById('filtroBusca');
            const filtroData = document.getElementById('filtroData');
            const limparFiltrosBtn = document.getElementById('limparFiltros');
            const tabela = document.getElementById('tabelaAgendamentos');
            const linhas = tabela.getElementsByTagName('tr');

            function aplicarFiltros() {
                let textoBusca = filtroBusca.value.toLowerCase();
                let dataBusca = filtroData.value;
                for (let i = 0; i < linhas.length; i++) {
                    let linha = linhas[i];
                    let conteudoDaLinha = linha.textContent || linha.innerText;
                    let dataDaLinha = linha.getAttribute('data-data');
                    const matchTexto = conteudoDaLinha.toLowerCase().indexOf(textoBusca) > -1;
                    const matchData = (dataBusca === '' || dataDaLinha === dataBusca);
                    linha.style.display = (matchTexto && matchData) ? '' : 'none';
                }
            }
            filtroBusca.addEventListener('keyup', aplicarFiltros);
            filtroData.addEventListener('change', aplicarFiltros);
            limparFiltrosBtn.addEventListener('click', () => {
                filtroBusca.value = '';
                filtroData.value = '';
                aplicarFiltros();
            });
        });
    </script>
</body>

</html>