<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BLOQUEIO DE SEGURANÇA: Apenas admins logados podem ver
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../../views/admin/usuario/login.php?erro=acessonegado");
    exit();
}

// Inclui a conexão com o banco de dados
require_once '../../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];
$pagina_atual_sidebar = 'usuarios';

$erros = [];
$usuario_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não houver ID na URL, redireciona de volta
if (!$usuario_id) {
    header("Location: ../../views/admin/usuarios.php?erro=id_invalido");
    exit();
}

// --- LÓGICA PARA PROCESSAR O FORMULÁRIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $id_post = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['usuario_nome']);
    $email = filter_input(INPUT_POST, 'usuario_email', FILTER_VALIDATE_EMAIL);
    $cpf = trim($_POST['usuario_cpf']);
    $tel = trim($_POST['usuario_tel']);

    // Validação
    if (empty($nome))
        $erros[] = "O nome é obrigatório.";
    if (!$email)
        $erros[] = "O email fornecido é inválido.";

    if (empty($erros)) {
        $stmt = $conn->prepare("UPDATE usuario SET usuario_nome = ?, usuario_email = ?, usuario_cpf = ?, usuario_tel = ? WHERE usuario_id = ?");
        $stmt->bind_param("ssssi", $nome, $email, $cpf, $tel, $id_post);

        if ($stmt->execute()) {
            header("Location: ../usuarios.php?sucesso=alterado");
            exit();
        } else {
            $erros[] = "Erro ao atualizar o usuário.";
        }
        $stmt->close();
    }
}

// --- LÓGICA PARA BUSCAR OS DADOS DO USUÁRIO (GET) ---
$stmt = $conn->prepare("SELECT usuario_nome, usuario_email, usuario_cpf, usuario_tel FROM usuario WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: ../../views/admin/usuarios.php?erro=usuario_nao_encontrado");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../../public/icon.ico" type="image/x-icon" />
    <style>
        /* Copie e cole o CSS do seu 'usuarios.php' aqui para manter a consistência */
        :root {
            --cor-fundo: #181828;
            --cor-fundo-secundario: #1f1f2e;
            --cor-texto: #f0f0f0;
            --cor-primaria: #d633ff;
            --cor-secundaria: #e6b800;
        }

        body {
            font-family: 'Barlow Condensed', sans-serif;
            background-image: url('../../../public/assets/img/background.png');
            color: var(--cor-texto);
        }

        /* Sidebar fixa com z-index alto */
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

        /* Header Mobile Padronizado */
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
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door-fill me-2"></i>
                    Início</a></li>
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-graph-up me-2"></i>
                    Dashboard</a></li>
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
            <li class="nav-item"><a class="nav-link" href="../../controllers/UsuarioController.php?logout=1"><i
                        class="bi bi-box-arrow-left me-2"></i> Sair</a></li>
        </ul>
    </div>    
<div class="main-content" id="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Editar Usuário</h2>
                <p class="lead">Altere as informações do cliente abaixo.</p>
            </div>
            <a href="../usuarios.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
        </header>

        <div class="card-kpi p-4">
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($erros as $erro): ?>
                        <p class="mb-0"><?php echo htmlspecialchars($erro); ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="editarUsuario.php?id=<?php echo $usuario_id; ?>" method="POST">
                <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
                <div class="mb-3">
                    <label for="usuario_nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control form-control-dark" id="usuario_nome" name="usuario_nome"
                        value="<?php echo htmlspecialchars($usuario['usuario_nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="usuario_email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-dark" id="usuario_email" name="usuario_email"
                        value="<?php echo htmlspecialchars($usuario['usuario_email']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usuario_cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control form-control-dark" id="usuario_cpf" name="usuario_cpf"
                            value="<?php echo htmlspecialchars($usuario['usuario_cpf']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="usuario_tel" class="form-label">Telefone</label>
                        <input type="text" class="form-control form-control-dark" id="usuario_tel" name="usuario_tel"
                            value="<?php echo htmlspecialchars($usuario['usuario_tel']); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-purple btn-lg"><i class="bi bi-check-circle-fill me-2"></i> Salvar
                    Alterações</button>
            </form>
        </div>
    </div>
</body>

</html>