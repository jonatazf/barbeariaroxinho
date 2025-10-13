<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];

$stmt_users = $conn->prepare("
    SELECT usuario_id, usuario_nome, usuario_email, usuario_cpf, usuario_tel, usuario_data_cadastro, usuario_tipo
    FROM usuario 
    ORDER BY usuario_data_cadastro DESC
");
$stmt_users->execute();
$todos_os_usuarios = $stmt_users->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_users->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usuários - Admin Roxinho's Barber</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
<style>
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
        top: 0; 
        left: 0; 
        bottom: 0; 
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
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active { 
        background-color: var(--cor-primaria); 
        color: #fff;
    }
    .sidebar .logout-link { 
        margin-top: auto; 
    }
    .mobile-header {
        display: none;
        background-color: #000;
        padding: 10px 15px;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1001;
    }
    .menu-toggle {
        font-size: 1.5rem;
        color: #fff;
        background: none;
        border: none;
        cursor: pointer;
    }
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
    .overlay.is-active {
        display: block;
    }
    .main-content { 
        margin-left: 250px; 
        padding: 30px;
        filter:blur(); 
        pointer-events: none;
        transition: margin-left 0.3s ease;
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
    .modal-backdrop { 
        background-color: #000 !important; 
        opacity: 0.9 !important; 
    }
    #senhaModal .modal-content { 
        background-color: #222; 
        color: #fff; 
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
            padding-top: 70px;
        }
        .mobile-header {
            display: flex;
        }
    }
</style>
</head>
<body>

<div id="overlay" class="overlay"></div>

<!-- Mobile Header -->
<div class="mobile-header">
    <button class="menu-toggle" id="menu-toggle">
        <i class="bi bi-list"></i>
    </button>
    <div class="logo ms-3" style="color: var(--cor-primaria); font-weight: bold; font-size: 1.5rem;">ROXINHO'S ADM</div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo">ROXINHO'S ADM</div>
    <div class="text-center mb-3"> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
        <li class="nav-item"><a class="nav-link active" href="usuarios.php"><i class="bi bi-people-fill me-2"></i> Usuários</a></li>
        <li class="nav-item"><a class="nav-link" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a></li>
        <li class="nav-item"><a class="nav-link" href="estoque.php"><i class="bi bi-box2-fill me-2"></i> Estoque</a></li>
    </ul>
    <ul class="nav flex-column logout-link">
        <li class="nav-item"><a class="nav-link" href="../../controllers/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <header class="header">
        <h2>Gerenciamento de Usuários</h2>
        <p class="lead">Filtre, busque e gerencie os clientes cadastrados.</p>
    </header>
    
    <div class="card-kpi p-3 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                <label for="filtroBusca" class="form-label">Buscar Usuário</label>
                <input type="text" id="filtroBusca" class="form-control" placeholder="Digite nome, email, etc...">
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Filtrar por Tipo:</label>
                <div>
                    <input class="form-check-input filtro-tipo" type="radio" name="filtroTipo" id="filtroTodos" value="todos" checked>
                    <label class="form-check-label" for="filtroTodos">Mostrar Todos</label>
                    <input class="form-check-input filtro-tipo ms-2" type="radio" name="filtroTipo" id="filtroAdmins" value="admin">
                    <label class="form-check-label" for="filtroAdmins">Apenas Admins</label>
                    <input class="form-check-input filtro-tipo ms-2" type="radio" name="filtroTipo" id="filtroComuns" value="comum">
                    <label class="form-check-label" for="filtroComuns">Apenas Usuários Comuns</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-kpi p-3">
        <div class="table-responsive">
            <table class="table table-dark-custom align-middle" id="tabelaPrincipal">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Data de Cadastro</th>
                        <th>Administrador?</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaUsuarios">
                    <?php foreach($todos_os_usuarios as $usuario): ?>
                        <?php $tipoLinha = ($usuario['usuario_tipo'] == 1) ? 'admin' : 'comum'; ?>
                        <tr data-tipo="<?= $tipoLinha ?>">
                            <td><?= htmlspecialchars($usuario['usuario_nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario_email']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario_cpf'] ?: 'Não informado') ?></td>
                            <td><?= htmlspecialchars($usuario['usuario_tel'] ?: 'Não informado') ?></td>
                            <td><?= date("d/m/Y", strtotime($usuario['usuario_data_cadastro'])) ?></td>
                            <td>
                                <?php if ($usuario['usuario_id'] != $_SESSION['usuario_id']): ?>
                                <input type="checkbox" class="admin-toggle" data-id="<?= $usuario['usuario_id'] ?>"
                                    <?= ($usuario['usuario_tipo'] == 1) ? "checked" : "" ?>>
                                <?php else: ?>
                                <span class="text-muted">Você</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../../controllers/admin/editarUsuario.php?id=<?= $usuario['usuario_id']; ?>"><button class="btn btn-sm btn-outline-light" title="Editar Usuário"><i class="bi bi-pencil-fill"></i></button></a>
                                <a href="../../controllers/admin/excluirUsuario.php?id=<?= $usuario['usuario_id']; ?>"><button class="btn btn-sm btn-outline-danger" title="Excluir Usuário"><i class="bi bi-trash-fill"></i></button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL SENHA -->
<div class="modal fade" id="senhaModal" tabindex="-1" aria-labelledby="senhaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="senhaModalLabel">Confirme sua senha</h5>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="senhaAdmin" class="form-label">Senha de administrador:</label>
          <input type="password" class="form-control" id="senhaAdmin" autocomplete="off" autofocus>
          <div class="invalid-feedback mt-2" id="senhaErroMsg"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-primary" id="btnAutenticar">Entrar</button>
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

// Mostra o modal ao carregar a página
const senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'));
senhaModal.show();
document.getElementById('senhaAdmin').focus();

// Desbloqueia/desabilita toda a main-content após autenticação
function desbloquearConteudo() {
    document.getElementById('main-content').style.filter = "";
    document.getElementById('main-content').style.pointerEvents = "auto";
    senhaModal.hide();
    document.getElementById('senhaAdmin').value = "";
    document.getElementById('senhaErroMsg').textContent = "";
}

// AJAX verificação senha
document.getElementById('btnAutenticar').onclick = function() {
    autenticaSenha();
};

document.getElementById('senhaAdmin').addEventListener('keydown', function(e){
    if (e.key === 'Enter') autenticaSenha();
});

function autenticaSenha() {
    document.getElementById('btnAutenticar').disabled = true;
    let senha = document.getElementById('senhaAdmin').value;
    fetch('senhaAdminCheck.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'senha=' + encodeURIComponent(senha)
    })
    .then(r=>r.text()).then(ret=>{
        document.getElementById('btnAutenticar').disabled = false;
        if(ret === "ok"){
            desbloquearConteudo();
        } else {
            document.getElementById('senhaAdmin').value = "";
            document.getElementById('senhaErroMsg').textContent = "Senha incorreta!";
            document.getElementById('senhaAdmin').classList.add('is-invalid');
            document.getElementById('senhaAdmin').focus();
        }
    });
}

// Filtros JS
function aplicarFiltros() {
    let texto = document.getElementById('filtroBusca').value.toLowerCase();
    let tipo = document.querySelector('input[name="filtroTipo"]:checked').value;
    document.querySelectorAll('#tabelaUsuarios tr').forEach(function(linha) {
        let conteudo = linha.textContent.toLowerCase();
        let tipoLinha = linha.getAttribute('data-tipo');
        let show = conteudo.indexOf(texto) > -1 && (tipo === 'todos' || tipoLinha === tipo);
        linha.style.display = show ? "" : "none";
    });
}

document.getElementById('filtroBusca').addEventListener('keyup', aplicarFiltros);
document.querySelectorAll('.filtro-tipo').forEach(e => e.addEventListener('change', aplicarFiltros));

// AJAX privilegio admin
document.querySelectorAll('.admin-toggle').forEach(function(chk) {
    chk.addEventListener('change', function() {
        const id = this.dataset.id;
        const isAdmin = this.checked;
        fetch('adminToggle.php', {
            method: 'POST', 
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id='+encodeURIComponent(id)+'&is_admin='+(isAdmin ? 1 : 0)
        })
        .then(r=>r.text())
        .then(ret => {
            if (!ret.startsWith("ok")) {
                alert("Erro ao alterar privilégio.");
                this.checked = !isAdmin;
            } else {
                aplicarFiltros();
            }
        });
    });
});
</script>
</body>
</html>
