<?php
// cortes.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VERIFICAÇÃO DE SEGURANÇA
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];

// --- CONSULTA PRINCIPAL ---
$stmt = $conn->prepare("SELECT corte_id, corte_nome, corte_preco, corte_descricao FROM corte ORDER BY corte_id ASC");
$stmt->execute();
$todos_os_cortes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortes | Admin Roxinho's Barber</title>
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
            background-image: url('../../public/assets/img/background.png');
            color: var(--cor-texto);
        }

        .btn-purple {
            background-color: var(--cor-primaria);
            border-color: var(--cor-primaria);
            color: #fff;
        }

        .btn-purple:hover {
            background-color: #8722a0ff;
            border-color: #8722a0ff;
            color: #fff;
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

        /* CONTEÚDO DESBLOQUEADO */
        .main-content {
            margin-left: 250px;
            padding: 30px;
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* CORREÇÃO AQUI: Layout Responsivo */
        @media(max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.is-active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            /* AQUI ESTÁ A MUDANÇA PARA O BOTÃO APARECER */
            .header {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 20px;
            }

            .header a,
            .header button {
                width: 100%;
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
        <div class="logo">ROXINHO'S BARBER <br> ADMIN</div>
        <div> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-graph-up me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
            <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-people-fill me-2"></i> Usuários</a></li>
            <li class="nav-item"><a class="nav-link active" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a></li>
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

        <header class="header">
            <div>
                <h2>Gerenciamento de Cortes</h2>
                <p class="lead">Adicione, edite e remova os serviços oferecidos.</p>
            </div>

            <a href="forms/adicionarCorte.php"><button class="btn btn-purple"><i
                        class="bi bi-plus-circle-fill me-2 text-white"></i> Adicionar Corte</button></a>
        </header>
        <br>
        <div class="card-kpi p-3 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="filtroBusca" class="form-label">Buscar por Corte</label>
                    <input type="text" id="filtroBusca" class="form-control form-control-dark"
                        placeholder="Digite o nome do corte...">
                </div>
                <div class="col-md-7">
                    <label class="form-label">Exibir Colunas:</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check form-check-inline"><input class="form-check-input coluna-toggle"
                                type="checkbox" id="checkId" value="0" checked><label class="form-check-label"
                                for="checkId">ID</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input coluna-toggle"
                                type="checkbox" id="checkNome" value="1" checked><label class="form-check-label"
                                for="checkNome">Nome</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input coluna-toggle"
                                type="checkbox" id="checkPreco" value="2" checked><label class="form-check-label"
                                for="checkPreco">Preço</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input coluna-toggle"
                                type="checkbox" id="checkDesc" value="3" checked><label class="form-check-label"
                                for="checkDesc">Descrição</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input coluna-toggle"
                                type="checkbox" id="checkAcoes" value="4" checked><label class="form-check-label"
                                for="checkAcoes">Ações</label></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-kpi p-3">
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle" id="tabelaPrincipal">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Descrição</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaCortes">
                        <?php if (empty($todos_os_cortes)): ?>
                            <tr>
                                <td colspan="5" class="text-center p-4">Nenhum corte cadastrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todos_os_cortes as $corte): ?>
                                <tr>
                                    <td><?php echo $corte['corte_id']; ?></td>
                                    <td><?php echo htmlspecialchars($corte['corte_nome']); ?></td>
                                    <td>R$ <?php echo number_format($corte['corte_preco'], 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($corte['corte_descricao'] ?: 'N/A'); ?></td>
                                    <td class="text-center">
                                        <a href="forms/editarCortes.php?id=<?php echo $corte['corte_id']; ?>"
                                            class="btn btn-sm btn-outline-primary" title="Editar Corte"><i
                                                class="bi bi-pencil-fill"></i></a>
                                        <a href="../../controllers/admin/excluirCorte.php?id=<?php echo $corte['corte_id']; ?>"
                                            class="btn btn-sm btn-outline-danger" title="Excluir Corte"
                                            onclick="return confirm('Tem certeza?');"><i class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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

        // Lógica dos filtros de tabela
        document.addEventListener('DOMContentLoaded', function () {
            const filtroBusca = document.getElementById('filtroBusca');
            const tabela = document.getElementById('tabelaCortes');
            const linhas = tabela.getElementsByTagName('tr');
            const checkboxes = document.querySelectorAll('.coluna-toggle');

            filtroBusca.addEventListener('keyup', function () {
                let filtro = this.value.toLowerCase();
                for (let i = 0; i < linhas.length; i++) {
                    let conteudoDaLinha = linhas[i].textContent || linhas[i].innerText;
                    linhas[i].style.display = (conteudoDaLinha.toLowerCase().indexOf(filtro) > -1) ? "" : "none";
                }
            });

            function toggleColuna() {
                checkboxes.forEach(checkbox => {
                    const colunaIndex = checkbox.value;
                    const isChecked = checkbox.checked;
                    const displayStyle = isChecked ? '' : 'none';
                    const celulas = document.querySelectorAll(
                        `#tabelaPrincipal th:nth-child(${parseInt(colunaIndex) + 1}), #tabelaPrincipal td:nth-child(${parseInt(colunaIndex) + 1})`
                    );
                    celulas.forEach(celula => {
                        celula.style.display = displayStyle;
                    });
                });
            }
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleColuna);
            });
            toggleColuna();
        });
    </script>
</body>

</html>