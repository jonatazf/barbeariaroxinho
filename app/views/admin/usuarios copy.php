<?php
// Inicia a sessão e faz o bloqueio de segurança
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];
$pagina_atual_sidebar = 'usuarios';

// =================================================================
//  LÓGICA DE FILTRO E PAGINAÇÃO TOTALMENTE CORRIGIDA
// =================================================================

// 1. Pega os filtros da URL
$busca = $_GET['busca'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? 'todos';

// 2. Prepara a base das consultas
$base_sql = " FROM usuario";
$where_conditions = [];
$params = [];
$types = '';

// 3. Adiciona dinamicamente os filtros à consulta, se existirem
if (!empty($busca)) {
    $where_conditions[] = "(usuario_nome LIKE ? OR usuario_user LIKE ? OR usuario_email LIKE ?)";
    $busca_param = "%{$busca}%";
    // Adiciona o mesmo parâmetro 3 vezes
    array_push($params, $busca_param, $busca_param, $busca_param);
    $types .= 'sss';
}
if ($filtro_tipo === 'admin') {
    $where_conditions[] = "usuario_tipo = ?";
    $params[] = 1;
    $types .= 'i';
} elseif ($filtro_tipo === 'comum') {
    $where_conditions[] = "usuario_tipo = ?";
    $params[] = 0;
    $types .= 'i';
}

$where_sql = count($where_conditions) > 0 ? " WHERE " . implode(' AND ', $where_conditions) : "";

// 4. CONTA o total de usuários COM o filtro aplicado
$count_sql = "SELECT COUNT(usuario_id) as total" . $base_sql . $where_sql;
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_usuarios = $stmt_count->get_result()->fetch_assoc()['total'];
$stmt_count->close();

// 5. Lógica de Paginação
$usuarios_por_pagina = 50;
$total_paginas = $total_usuarios > 0 ? ceil($total_usuarios / $usuarios_por_pagina) : 1;
$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina_atual < 1)
    $pagina_atual = 1;
if ($pagina_atual > $total_paginas)
    $pagina_atual = $total_paginas;
$offset = ($pagina_atual - 1) * $usuarios_por_pagina;

// 6. BUSCA os usuários da página atual COM o filtro
$select_sql = "SELECT usuario_id, usuario_nome, usuario_user, usuario_email, usuario_cpf, usuario_tel, usuario_data_cadastro, usuario_tipo" . $base_sql . $where_sql . " ORDER BY usuario_id ASC LIMIT ? OFFSET ?";
$params[] = $usuarios_por_pagina;
$params[] = $offset;
$types .= 'ii';

$stmt_users = $conn->prepare($select_sql);
$stmt_users->bind_param($types, ...$params);
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
            z-index: 1021;
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

        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: margin-left 0.3s ease, filter 0.3s ease;
            filter: blur(8px);
            pointer-events: none;
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

        .mobile-header {
            display: none;
            background-color: #000;
            padding: 10px 15px;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020;
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
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-backdrop {
            z-index: 1040 !important;
            background-color: #000 !important;
            opacity: 0.85 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        #senhaModal .modal-content {
            background-color: var(--cor-fundo-secundario);
            color: #fff;
            border: 1px solid var(--cor-primaria);
        }

        .form-check-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }

        .form-check-switch .form-check-input:checked {
            background-color: var(--cor-primaria);
            border-color: var(--cor-primaria);
        }

        .form-check-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
                padding-top: 80px;
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
    </style>
</head>

<body>

    <div id="overlay" class="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="logo">ROXINHO'S ADM</div>
        <div> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i
                        class="bi bi-house-door-fill me-2"></i> Início</a></li>
            <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i
                        class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
            <li class="nav-item"><a class="nav-link active" href="usuarios.php"><i class="bi bi-people-fill me-2"></i>
                    Usuários</a></li>
            <li class="nav-item"><a class="nav-link" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="estoque.php"><i class="bi bi-box2-fill me-2"></i> Estoque</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="diasInativos.php"><i class="bi bi-calendar2-x-fill me-2"></i>
                    Dias Inativos</a></li>
        </ul>
        <ul class="nav flex-column logout-link">
            <li class="nav-item"><a class="nav-link" href="../../controllers/logout.php"><i
                        class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
        </ul>
    </div>
    <div class="main-content" id="main-content">
        <div class="mobile-header">
            <button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
            <div class="logo ms-3">ROXINHO'S ADM</div>
        </div>
        <header class="header">
            <div>
                <h2>Gerenciamento de Usuários</h2>
                <p class="lead">Filtre e gerencie os clientes cadastrados.</p>
            </div>
        </header>

        <div class="card-kpi p-3 mb-4">
            <form action="usuarios.php" method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-lg-5 col-md-12">
                        <label for="filtroBusca" class="form-label">Buscar Usuário</label>
                        <input type="text" id="filtroBusca" name="busca" class="form-control form-control-dark"
                            placeholder="Digite nome, usuário ou email..."
                            value="<?php echo htmlspecialchars($busca); ?>">
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <label class="form-label">Filtrar por Tipo:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check"><input class="form-check-input" type="radio" name="tipo"
                                    id="filtroTodos" value="todos" <?php if ($filtro_tipo == 'todos')
                                        echo 'checked'; ?>><label class="form-check-label" for="filtroTodos">Todos</label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="tipo"
                                    id="filtroAdmins" value="admin" <?php if ($filtro_tipo == 'admin')
                                        echo 'checked'; ?>><label class="form-check-label" for="filtroAdmins">Admins</label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="tipo"
                                    id="filtroComuns" value="comum" <?php if ($filtro_tipo == 'comum')
                                        echo 'checked'; ?>><label class="form-check-label" for="filtroComuns">Comuns</label></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        <a href="usuarios.php" class="btn btn-secondary w-100">Limpar</a>
                    </div>
                </div>
            </form>
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
                        <?php foreach ($todos_os_usuarios as $usuario): ?>
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
                                    <a href="../../controllers/admin/editarUsuario.php?id=<?= $usuario['usuario_id']; ?>"><button
                                            class="btn btn-sm btn-outline-light" title="Editar Usuário"><i
                                                class="bi bi-pencil-fill"></i></button></a>
                                    <a href="../../controllers/admin/excluirUsuario.php?id=<?= $usuario['usuario_id']; ?>"><button
                                            class="btn btn-sm btn-outline-danger" title="Excluir Usuário"><i
                                                class="bi bi-trash-fill"></i></button></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
*\
        <?php if ($total_paginas > 1): ?>
            <nav aria-label="Navegação de páginas" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    $query_params = http_build_query(['busca' => $busca, 'tipo' => $filtro_tipo]);
                    $primeira_pagina_url = "?pagina=1&" . $query_params;
                    $anterior_pagina_url = "?pagina=" . ($pagina_atual - 1) . "&" . $query_params;
                    $proxima_pagina_url = "?pagina=" . ($pagina_atual + 1) . "&" . $query_params;
                    $ultima_pagina_url = "?pagina=" . $total_paginas . "&" . $query_params;
                    ?>
                    <li class="page-item <?php if ($pagina_atual <= 1) {
                        echo 'disabled';
                    } ?>"><a class="page-link"
                            href="<?php echo $primeira_pagina_url; ?>">Primeira</a></li>
                    <li class="page-item <?php if ($pagina_atual <= 1) {
                        echo 'disabled';
                    } ?>"><a class="page-link"
                            href="<?php echo $anterior_pagina_url; ?>">Anterior</a></li>
                    <li class="page-item <?php if ($pagina_atual >= $total_paginas) {
                        echo 'disabled';
                    } ?>"><a
                            class="page-link" href="<?php echo $proxima_pagina_url; ?>">Próxima</a></li>
                    <li class="page-item <?php if ($pagina_atual >= $total_paginas) {
                        echo 'disabled';
                    } ?>"><a
                            class="page-link" href="<?php echo $ultima_pagina_url; ?>">Última</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
    </div>

    </div>
    </div>

    <div class="modal fade" id="senhaModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Confirme sua senha de administrador</h5>
                </div>
                <div class="modal-body">
                    <input type="password" id="senhaAdmin" class="form-control form-control-dark" autocomplete="off"
                        autofocus>
                    <div id="senhaErroMsg" class="invalid-feedback mt-2" style="display:none;">Senha incorreta.</div>
                </div>
                <div class="modal-footer border-0"><button id="btnAutenticar" class="btn btn-primary">Entrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainContent = document.getElementById('main-content');
            const senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'));
            const senhaAdminInput = document.getElementById('senhaAdmin');
            const btnAutenticar = document.getElementById('btnAutenticar');
            const senhaErroMsg = document.getElementById('senhaErroMsg');

            senhaModal.show();
            document.getElementById('senhaModal').addEventListener('shown.bs.modal', () => {
                senhaAdminInput.focus();
            });

            function desbloquearConteudo() {
                mainContent.style.filter = 'none';
                mainContent.style.pointerEvents = 'auto';
                senhaModal.hide();
                inicializarPainel();
            }

            function mostrarErroSenha() {
                senhaErroMsg.style.display = 'block';
                senhaAdminInput.classList.add('is-invalid');
                senhaAdminInput.value = '';
                senhaAdminInput.focus();
            }

            function validarSenhaAdmin() {
                btnAutenticar.disabled = true;
                senhaErroMsg.style.display = 'none';
                senhaAdminInput.classList.remove('is-invalid');

                fetch('../../controllers/admin/verificaSenhaAdmin.php', {
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
                            mostrarErroSenha();
                        }
                    }).catch(() => { btnAutenticar.disabled = false; mostrarErroSenha(); });
            }

            btnAutenticar.addEventListener('click', validarSenhaAdmin);
            senhaAdminInput.addEventListener('keydown', e => { if (e.key === 'Enter') validarSenhaAdmin(); });

            function inicializarPainel() {
                const menuToggle = document.getElementById('menu-toggle');
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');
                if (menuToggle) {
                    menuToggle.addEventListener('click', () => { sidebar.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
                    overlay.addEventListener('click', () => { sidebar.classList.remove('is-active'); overlay.classList.remove('is-active'); });
                }

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

                document.querySelectorAll('.admin-toggle').forEach(chk => {
                    chk.addEventListener('change', function () {
                        const id = this.dataset.id;
                        const isAdmin = this.checked;
                        fetch('../../controllers/admin/adminToggle.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + encodeURIComponent(id) + '&is_admin=' + (isAdmin ? 1 : 0)
                        })
                            .then(r => r.text())
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
            }
        });
    </script>

</body>

</html>