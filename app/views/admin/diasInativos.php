<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];

$stmt_dias = $conn->prepare("SELECT diaInativo_id, diaInativo_data_inativa, diaInativo_hora_inicio, diaInativo_hora_fim, diaInativo_motivo FROM dia_inativo ORDER BY diaInativo_data_inativa DESC, diaInativo_hora_inicio ASC");
$stmt_dias->execute();
$diasInativos = $stmt_dias->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_dias->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dias Inativos | Admin Roxinho's Barber</title>
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

        .btn-purple {
            background-color: var(--cor-primaria);
            color: #fff;
            border: none;
        }

        .btn-purple:hover {
            background-color: #b822e0;
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
    </style>
</head>

<body>
    <div id="overlay" class="overlay"></div>
    <div class="mobile-header"><button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
        <div class="logo ms-3" style="color: var(--cor-primaria);">ROXINHO'S ADM</div>
    </div>
    <div class="sidebar" id="sidebar">
        <div class="logo">ROXINHO'S BARBER <br> ADMIN</div>
        <div> Olá, <?php echo htmlspecialchars($nome_admin) ?>!</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-graph-up me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i class="bi bi-calendar-check-fill me-2"></i> Agendamentos</a></li>
            <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-people-fill me-2"></i> Usuários</a></li>
            <li class="nav-item"><a class="nav-link" href="cortes.php"><i class="bi bi-scissors me-2"></i> Cortes</a></li>
            <li class="nav-item"><a class="nav-link" href="estoque.php"><i class="bi bi-box2-fill me-2"></i> Estoque</a></li>
            <li class="nav-item"><a class="nav-link active" href="diasInativos.php"><i class="bi bi-calendar2-x-fill me-2"></i> Dias Inativos</a></li>
        </ul>
        <ul class="nav flex-column logout-link">
            <li class="nav-item"><a class="nav-link" href="../../controllers/usuarioController.php?logout=1"><i class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <header class="mb-4">
            <h2>Gerenciamento de Dias Inativos</h2>
            <p class="lead">Bloqueie datas ou períodos para evitar agendamentos.</p>
        </header>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?= (strpos($_GET['status'], 'sucesso') !== false) ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php
                if ($_GET['status'] == 'sucesso_criar') echo 'Cadastrado com sucesso!';
                if ($_GET['status'] == 'sucesso_editar') echo 'Atualizado com sucesso!';
                if ($_GET['status'] == 'sucesso_excluir') echo 'Excluído com sucesso!';
                if ($_GET['status'] == 'erro') echo 'Erro na operação.';
                if ($_GET['status'] == 'erro_id') echo 'ID inválido ou não encontrado.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card-kpi p-3 mb-4">
            <div class="row align-items-end">
                <div class="col-lg-5"><label class="form-label">Buscar</label><input type="text" id="filtroBusca" class="form-control form-control-dark" placeholder="Data ou motivo..."></div>
                <div class="col-lg-7 text-md-end"><a href="forms/criarDiaInativo.php" class="btn btn-purple"><i class="bi bi-plus-circle-fill me-2"></i> Cadastrar Novo Dia Inativo</a></div>
            </div>
        </div>

        <div class="card-kpi p-3">
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle" id="tabelaDiasInativos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Período</th>
                            <th>Motivo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($diasInativos)): ?><tr>
                                <td colspan="5" class="text-center p-4">Nenhum registro.</td>
                            </tr><?php else: ?>
                            <?php foreach ($diasInativos as $dia): ?>
                                <tr>
                                    <td><?= $dia['diaInativo_id'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($dia['diaInativo_data_inativa'])) ?></td>
                                    <td><?= ($dia['diaInativo_hora_inicio']) ? substr($dia['diaInativo_hora_inicio'], 0, 5) . ' - ' . substr($dia['diaInativo_hora_fim'], 0, 5) : 'Dia Todo' ?></td>
                                    <td><?= htmlspecialchars($dia['diaInativo_motivo']) ?></td>
                                    <td class="text-center">
                                        <a href="forms/editarDiaInativo.php?id=<?= $dia['diaInativo_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill"></i></a>
                                        <a href="../../controllers/admin/DiaInativoController.php?acao=excluir&id=<?= $dia['diaInativo_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este registro?');"><i class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                        <?php endforeach;
                                endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menu = document.getElementById('menu-toggle'),
            sidebar = document.getElementById('sidebar'),
            overlay = document.getElementById('overlay');
        menu.addEventListener('click', () => {
            sidebar.classList.toggle('is-active');
            overlay.classList.toggle('is-active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('is-active');
            overlay.classList.remove('is-active');
        });
        document.getElementById('filtroBusca').addEventListener('keyup', function() {
            let val = this.value.toLowerCase(),
                rows = document.querySelectorAll('#tabelaDiasInativos tbody tr');
            rows.forEach(r => r.style.display = r.innerText.toLowerCase().includes(val) ? '' : 'none');
        });
    </script>
</body>

</html>