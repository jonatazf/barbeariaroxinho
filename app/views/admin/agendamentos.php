<?php
// agendamentos.php
// Inicia a sessão e faz o bloqueio de segurança
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];

// --- CONSULTA PRINCIPAL: Buscar todos os agendamentos com detalhes ---
$stmt = $conn->prepare("
    SELECT 
        a.agen_id, a.agen_data_a, a.agen_hora_a, a.agen_status,
        u.usuario_nome, u.usuario_tel,
        c.corte_nome, c.corte_preco
    FROM agendamento a
    JOIN usuario u ON a.usuario_id = u.usuario_id
    JOIN corte c ON a.corte_id = c.corte_id
    ORDER BY a.agen_data_a DESC, a.agen_hora_a ASC
");
$stmt->execute();
$todos_agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agendamentos | Admin Roxinho's Barber</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet" />
<link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
<style>
    /* Seu CSS padrão */
    :root {
        --cor-fundo: #181828;
        --cor-fundo-secundario: #1f1f2e;
        --cor-texto: #f0f0f0;
        --cor-primaria: #d633ff;
        --cor-secundaria: #e6b800;
    }
    body {
        font-family: 'Barlow Condensed', sans-serif;
        background-color: var(--cor-fundo);
        color: var(--cor-texto);
    }
    .sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 1000;
        width: 250px;
        padding: 20px;
        background-color: #000;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
    }
    .sidebar .logo {
        font-size: 1.8rem;
        font-weight: bold;
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
        transition: background-color 0.3s, color 0.3s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background-color: var(--cor-primaria);
        color: #fff;
    }
    .sidebar .logout-link {
        margin-top: auto;
    }
    .main-content {
        margin-left: 250px;
        padding: 30px;
        transition: margin-left 0.3s ease;
        filter: blur();
        pointer-events: none;
        user-select: none;
    }
    .card-kpi {
        background-color: var(--cor-fundo-secundario);
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
        box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25);
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
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 999;
    }
    @media (max-width: 992px) {
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
    /* Modal styles */
    .modal-backdrop {
        background-color: #000 !important;
        opacity: 0.85 !important;
    }
    #senhaModal .modal-content {
        background-color: #222;
        color: #fff;
    }
</style>
</head>
<body>
<div id="overlay" class="overlay"></div>

<div class="sidebar" id="sidebar">
    <div class="logo">ROXINHO'S ADM</div>
    <div> Olá, <?php echo htmlspecialchars($nome_admin); ?>!</div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link active" href="agendamentos.php"><i class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-people-fill me-2"></i> Usuários</a></li>
        <li class="nav-item"><a class="nav-link" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a></li>
        <li class="nav-item"><a class="nav-link" href="estoque.php"><i class="bi bi-box2-fill me-2"></i> Estoque</a></li>
    </ul>
    <ul class="nav flex-column logout-link">
        <li class="nav-item"><a class="nav-link" href="../../controllers/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
    </ul>
</div>

<div class="main-content" id="main-content">
    <div class="mobile-header">
        <button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
        <div class="logo ms-3">ROXINHO'S ADM</div>
    </div>

    <header class="header">
        <div>
            <h2>Gerenciamento de Agendamentos</h2>
            <p class="lead">Visualize e filtre todos os agendamentos do sistema.</p>
        </div>
    </header>

    <div class="card-kpi p-3 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="filtroBusca" class="form-label">Buscar por Cliente ou Serviço</label>
                <input type="text" id="filtroBusca" class="form-control form-control-dark" placeholder="Digite um nome...">
            </div>
            <div class="col-md-5">
                <label for="filtroData" class="form-label">Filtrar por Data Específica</label>
                <input type="date" id="filtroData" class="form-control form-control-dark">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-secondary" id="limparFiltros">Limpar Filtros</button>
            </div>
        </div>
    </div>

    <div class="card-kpi p-3">
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
                        <tr><td colspan="7" class="text-center p-4">Nenhum agendamento encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach($todos_agendamentos as $ag): ?>
                        <tr data-data="<?php echo $ag['agen_data_a']; ?>">
                            <td><?php echo date("d/m/Y", strtotime($ag['agen_data_a'])); ?></td>
                            <td><?php echo substr($ag['agen_hora_a'], 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($ag['usuario_nome']); ?></td>
                            <td><?php echo htmlspecialchars($ag['corte_nome']); ?></td>
                            <td>R$ <?php echo number_format($ag['corte_preco'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                $status_class = 'bg-secondary';
                                if ($ag['agen_status'] == 'Confirmado') $status_class = 'bg-success';
                                if ($ag['agen_status'] == 'Concluído') $status_class = 'bg-info';
                                if ($ag['agen_status'] == 'Cancelado') $status_class = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($ag['agen_status']); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-light" title="Editar Agendamento"><i class="bi bi-pencil-fill"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Cancelar Agendamento"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal senha admin -->
<div class="modal fade" id="senhaModal" tabindex="-1" aria-labelledby="senhaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="senhaModalLabel">Confirme sua senha de administrador</h5>
            </div>
            <div class="modal-body">
                <input type="password" id="senhaAdmin" class="form-control" placeholder="Senha de administrador" autocomplete="off" autofocus />
                <div id="senhaErroMsg" class="invalid-feedback mt-2" style="display:none;">Senha incorreta. Tente novamente.</div>
            </div>
            <div class="modal-footer border-0">
                <button id="btnAutenticar" class="btn btn-primary">Entrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Menu mobile toggle
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

    // Desbloquear conteúdo após senha correta
    function desbloquearConteudo() {
        const mainContent = document.getElementById('main-content');
        mainContent.style.removeProperty = 'blur';
        mainContent.style.pointerEvents = 'auto';
        mainContent.style.userSelect = 'auto';

        const senhaModal = bootstrap.Modal.getInstance(document.getElementById('senhaModal'));
        senhaModal.hide();

        const senhaErroMsg = document.getElementById('senhaErroMsg');
        const senhaAdminInput = document.getElementById('senhaAdmin');
        senhaErroMsg.style.display = 'none';
        senhaAdminInput.value = '';
        senhaAdminInput.classList.remove('is-invalid');
    }

    // Mostrar mensagem de erro no modal
    function mostrarErro() {
        const senhaErroMsg = document.getElementById('senhaErroMsg');
        const senhaAdminInput = document.getElementById('senhaAdmin');
        senhaErroMsg.style.display = 'block';
        senhaAdminInput.classList.add('is-invalid');
        senhaAdminInput.value = '';
        senhaAdminInput.focus();
    }

    // Valida senha via AJAX
    function validarSenhaAdmin() {
        const btnAutenticar = document.getElementById('btnAutenticar');
        const senhaAdminInput = document.getElementById('senhaAdmin');
        btnAutenticar.disabled = true;
        document.getElementById('senhaErroMsg').style.display = 'none';
        senhaAdminInput.classList.remove('is-invalid');

        fetch('senhaAdminCheck.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'senha=' + encodeURIComponent(senhaAdminInput.value)
        })
        .then(r => r.text())
        .then(resp => {
            btnAutenticar.disabled = false;
            if (resp.trim() === 'ok') {
                desbloquearConteudo();
            } else {
                mostrarErro();
            }
        })
        .catch(() => {
            btnAutenticar.disabled = false;
            mostrarErro();
        });
    }

    // Inicializa modal e binds
    document.addEventListener('DOMContentLoaded', () => {
        const senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'));
        senhaModal.show();

        const senhaAdminInput = document.getElementById('senhaAdmin');
        senhaAdminInput.focus();

        document.getElementById('btnAutenticar').addEventListener('click', validarSenhaAdmin);

        senhaAdminInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') validarSenhaAdmin();
        });
    });

    // Filtros da tabela
    document.addEventListener('DOMContentLoaded', () => {
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

                const matchTexto = conteudoDaLinha.toLowerCase().includes(textoBusca);
                const matchData = dataBusca === '' || dataDaLinha === dataBusca;

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
