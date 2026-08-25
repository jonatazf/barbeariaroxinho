<?php
// app/views/admin/adicionar_corte.php
// Inicia a sessão e faz o bloqueio de segurança
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

$nome_admin = $_SESSION['usuario_nome'];
$pagina_atual_sidebar = 'cortes'; // Para manter o menu "Cortes" ativo
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Corte | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../../public/icon.ico" type="image/x-icon" />
    <style>
        /* Estilos idênticos aos das outras páginas do admin para consistência */
        :root {
            --cor-fundo: #181828;
            --cor-fundo-secundario: #1f1f2e;
            --cor-texto: #f0f0f0;
            --cor-primaria: #d633ff;
            --cor-secundaria: #e6b800;
        }

        body {
            font-family: 'Barlow Condensed',
                sans-serif;
            background-image: url('../../../public/assets/img/background.png');
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
            transition: margin-left 0.3s ease;
        }

        .card-kpi {
            background-color: var(--cor-fundo-secundario);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 20px;
            color: var(--cor-texto);
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
            font-weight: bold;
        }

        .btn-purple:hover {
            background-color: #b822e0;
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

        <header class="header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Adicionar Novo Corte</h2>
                <p class="lead">Preencha os dados do novo serviço.</p>
            </div>
            <a href="../cortes.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
        </header>

        <div class="card-kpi p-4 p-md-5">

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">
                    <?php if ($_GET['erro'] == 'dados_invalidos')
                        echo 'Dados inválidos. Verifique o nome e o preço.'; ?>
                </div>
            <?php endif; ?>

            <form action="../../../controllers/admin/cortesController.php" method="POST">
                <input type="hidden" name="acao" value="criar">

                <div class="mb-3">
                    <label for="corte_nome" class="form-label">Nome do Corte *</label>
                    <input type="text" class="form-control form-control-dark" id="corte_nome" name="corte_nome"
                        required>
                </div>

                <div class="mb-3">
                    <label for="corte_preco" class="form-label">Preço (R$) *</label>
                    <input type="text" class="form-control form-control-dark" id="corte_preco" name="corte_preco"
                        placeholder="Ex: 30,00" required>
                </div>

                <div class="mb-3">
                    <label for="corte_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control form-control-dark" id="corte_descricao" name="corte_descricao"
                        rows="3" placeholder="Ex: Corte clássico na tesoura e máquina..."></textarea>
                </div>

                <button type="submit" class="btn btn-purple btn-lg"><i class="bi bi-check-circle-fill me-2"></i> Salvar
                    Novo Corte</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script>
        // --- SCRIPT PARA MENU MOBILE ---
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { sidebar.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
            overlay.addEventListener('click', () => { sidebar.classList.remove('is-active'); overlay.classList.remove('is-active'); });
        }

        // --- MÁSCARA PARA O CAMPO DE PREÇO ---
        const precoInput = document.getElementById('corte_preco');
        if (precoInput) {
            IMask(precoInput, {
                mask: 'R$ num',
                blocks: {
                    num: {
                        mask: Number,
                        scale: 2, // 2 casas decimais
                        thousandsSeparator: '.',
                        padFractionalZeros: true, // Garante que 30 vira 30,00
                        radix: ',' // Separador decimal
                    }
                }
            });
        }
    </script>
</body>

</html>