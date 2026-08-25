<?php
// Inicia a sessão e faz o bloqueio de segurança
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];
$pagina_atual_sidebar = 'usuarios';

// --- CONSULTA PRINCIPAL: Buscar todos os usuários ---
$stmt_users = $conn->prepare("SELECT usuario_id, usuario_nome, usuario_user, usuario_email, usuario_cpf, usuario_tel, usuario_data_cadastro, usuario_tipo FROM usuario ORDER BY usuario_nome ASC");
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
    <title>Usuários | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #181828; --cor-fundo-secundario: #1f1f2e; --cor-texto: #f0f0f0; --cor-primaria: #d633ff; --cor-secundaria: #e6b800; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); }
        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1021; width: 250px; padding: 20px; background-color: #000; display: flex; flex-direction: column; transition: transform 0.3s ease; }
        .sidebar .logo { font-size: 1.8rem; font-weight: bold; color: var(--cor-primaria); text-align: center; margin-bottom: 30px; }
        .sidebar .nav-link { color: var(--cor-texto); font-size: 1.2rem; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: background-color 0.3s, color 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: var(--cor-primaria); color: #fff; }
        .sidebar .logout-link { margin-top: auto; }
        /* CORREÇÃO: Removido o 'filter: blur' e 'pointer-events' */
        .main-content { margin-left: 250px; padding: 30px; transition: margin-left 0.3s ease; }
        .card-kpi { background-color: var(--cor-fundo-secundario); border: 1px solid #333; border-radius: 12px; padding: 20px; color: var(--cor-texto); }
        .table-dark-custom thead th { color: var(--cor-texto); background-color: var(--cor-primaria); }
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        .mobile-header { display: none; background-color: #000; padding: 10px 15px; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1020; }
        .menu-toggle { font-size: 1.5rem; color: #fff; background: none; border: none; }
        .overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; }
        .form-check-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; } .form-check-switch .form-check-input:checked { background-color: var(--cor-primaria); border-color: var(--cor-primaria); } .form-check-input:disabled { opacity: 0.5; cursor: not-allowed; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.is-active { transform: translateX(0); } .main-content { margin-left: 0; padding-top: 80px; } .header { display: none; } .mobile-header { display: flex; } .overlay.is-active { display: block; } }
    </style>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content" id="main-content">
    <div class="mobile-header">
        <button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
        <div class="logo ms-3">ROXINHO'S ADM</div>
    </div>
    <header class="header">
        <div><h2>Gerenciamento de Usuários</h2><p class="lead">Filtre e gerencie os clientes cadastrados.</p></div>
    </header>
    <div class="card-kpi p-3 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-5 col-md-12"><label for="filtroBusca" class="form-label">Buscar Usuário</label><input type="text" id="filtroBusca" class="form-control form-control-dark" placeholder="Digite nome, email, etc..."></div>
            <div class="col-lg-7 col-md-12">
                <label class="form-label">Filtrar por Tipo:</label>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check"><input class="form-check-input filtro-tipo" type="radio" name="filtroTipo" id="filtroTodos" value="todos" checked><label class="form-check-label" for="filtroTodos">Mostrar Todos</label></div>
                    <div class="form-check"><input class="form-check-input filtro-tipo" type="radio" name="filtroTipo" id="filtroAdmins" value="admin"><label class="form-check-label" for="filtroAdmins">Apenas Admins</label></div>
                    <div class="form-check"><input class="form-check-input filtro-tipo" type="radio" name="filtroTipo" id="filtroComuns" value="comum"><label class="form-check-label" for="filtroComuns">Apenas Usuários Comuns</label></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-kpi p-3">
        <div class="table-responsive">
            <table class="table table-dark-custom align-middle" id="tabelaPrincipal">
                <thead><tr><th>ID</th><th>Nome</th><th>Usuário</th><th>Email</th><th>Telefone</th><th class="text-center">Admin</th><th class="text-center">Ações</th></tr></thead>
                <tbody id="tabelaUsuarios">
                    <?php foreach($todos_os_usuarios as $usuario): ?>
                    <tr data-tipo="<?php echo ($usuario['usuario_tipo'] == 1) ? 'admin' : 'comum'; ?>">
                        <td><?php echo $usuario['usuario_id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['usuario_nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['usuario_user']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['usuario_email']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['usuario_tel'] ?: 'N/A'); ?></td>
                        <td class="text-center">
                            <form method="POST" class="d-inline-flex align-items-center admin-toggle-form">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['usuario_id']; ?>">
                                <div class="form-check form-switch form-check-switch">
                                    <input class="form-check-input admin-toggle" type="checkbox" role="switch"
                                        data-id="<?php echo $usuario['usuario_id']; ?>"
                                        <?php echo ($usuario['usuario_tipo'] == 1) ? 'checked' : ''; ?>
                                        <?php echo ($_SESSION['usuario_id'] == $usuario['usuario_id']) ? 'disabled' : ''; ?>>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-light" title="Editar"><i class="bi bi-pencil-fill"></i></button>
                            <?php if ($_SESSION['usuario_id'] != $usuario['usuario_id']): ?>
                            <a href="../../controllers/admin/excluirUsuario.php?id=<?php echo $usuario['usuario_id']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="bi bi-trash-fill"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ==========================================================
// JAVASCRIPT CORRIGIDO (SEM LÓGICA DE MODAL)
// ==========================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Menu mobile
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if(menuToggle) {
        menuToggle.addEventListener('click', () => { sidebar.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
        overlay.addEventListener('click', () => { sidebar.classList.remove('is-active'); overlay.classList.remove('is-active'); });
    }

    // Filtros da Tabela
    const filtroBusca = document.getElementById('filtroBusca');
    const filtrosTipo = document.querySelectorAll('.filtro-tipo');
    const tabela = document.getElementById('tabelaUsuarios');
    const linhas = tabela.getElementsByTagName('tr');

    function aplicarFiltros() {
        let textoBusca = filtroBusca.value.toLowerCase();
        let tipoSelecionado = document.querySelector('input[name="filtroTipo"]:checked').value;
        for (let linha of linhas) {
            let conteudoDaLinha = linha.textContent || linha.innerText;
            let tipoDaLinha = linha.getAttribute('data-tipo');
            const matchTexto = conteudoDaLinha.toLowerCase().indexOf(textoBusca) > -1;
            const matchTipo = (tipoSelecionado === 'todos' || tipoDaLinha === tipoSelecionado);
            linha.style.display = (matchTexto && matchTipo) ? "" : "none";
        }
    }

    filtroBusca.addEventListener('keyup', aplicarFiltros);
    filtrosTipo.forEach(radio => radio.addEventListener('change', aplicarFiltros));
    
    // AJAX para tornar Admin
    document.querySelectorAll('.admin-toggle').forEach(chk => {
        chk.addEventListener('change', function() {
            const id = this.dataset.id;
            const isAdmin = this.checked;
            fetch('../../controllers/admin/adminToggle.php', {
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
                    this.closest('tr').dataset.tipo = isAdmin ? 'admin' : 'comum';
                    aplicarFiltros();
                }
            });
        });
    });

    // Aplica filtros ao carregar a página (caso o usuário volte com o formulário preenchido)
    aplicarFiltros();
});
</script>

</body>
</html>