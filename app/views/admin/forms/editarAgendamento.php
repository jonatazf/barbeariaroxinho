<?php
// Inicia sessão e inclui conexões/config
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../../views/admin/usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../../config/database.php';

$erros = [];
$agendamento_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$agendamento_id) {
    header("Location: ../../views/admin/agendamentos.php?erro=id_invalido");
    exit();
}

// Processa envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = filter_input(INPUT_POST, 'agen_id', FILTER_VALIDATE_INT);
    $data = $_POST['agen_data_a'] ?? '';
    $hora = $_POST['agen_hora_a'] ?? '';
    $status = $_POST['agen_status'] ?? '';

    // Validações simples
    if (empty($data)) $erros[] = "A data é obrigatória.";
    if (empty($hora)) $erros[] = "A hora é obrigatória.";
    if (empty($status)) $erros[] = "O status é obrigatório.";

    if (empty($erros)) {
        $stmt = $conn->prepare("UPDATE agendamento SET agen_data_a = ?, agen_hora_a = ?, agen_status = ? WHERE agen_id = ?");
        $stmt->bind_param("sssi", $data, $hora, $status, $id_post);
        if ($stmt->execute()) {
            header("Location: ../agendamentos.php?sucesso=alterado");
            exit();
        } else {
            $erros[] = "Erro ao atualizar o agendamento.";
        }
        $stmt->close();
    }
}

// Busca dados do agendamento para exibir no formulário
$stmt = $conn->prepare("SELECT agen_id, agen_data_a, agen_hora_a, agen_status FROM agendamento WHERE agen_id = ?");
$stmt->bind_param("i", $agendamento_id);
$stmt->execute();
$result = $stmt->get_result();
$agendamento = $result->fetch_assoc();
$stmt->close();
if (!$agendamento) {
    header("Location: ../../views/admin/agendamentos.php?erro=agendamento_nao_encontrado");
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../../public/icon.ico" type="image/x-icon" />
    <style>
        body {
            font-family: 'Barlow Condensed', sans-serif;
            background-image: url('../../../public/assets/img/background.png');
            color: var(--cor-texto);
        }
        :root {
            --cor-fundo: #181828;
            --cor-fundo-secundario: #1f1f2e;
            --cor-texto: #f0f0f0;
            --cor-primaria: #d633ff;
            --cor-secundaria: #e6b800;
        }
        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            width: 250px; padding: 20px;
            background-color: #000;
            display: flex; flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar .logo {
            font-size: 1.8rem; font-weight: bold;
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
            background-color: var(--cor-primaria); color: #fff;
        }
        .sidebar .logout-link { margin-top: auto; }
        .main-content { margin-left: 250px; padding: 30px; transition: margin-left 0.3s ease; }
        .card-kpi { background-color: var(--cor-fundo-secundario); border: 1px solid #333; border-radius: 12px; padding: 20px; color: var(--cor-texto); }
        .form-control-dark {
            background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria);
        }
        .form-control-dark:focus {
            background-color: #2a2a3e; color: #fff;
            border-color: var(--cor-secundaria);
            box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25);
        }
        .btn-purple { background-color: var(--cor-primaria); color: #fff; border: none; }
        .btn-purple:hover { background-color: #b822e0; }
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.is-active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .header { display: none; }
            .mobile-header { display: flex; }
            .overlay.is-active { display: block; }
        }
    </style>
</head>
<body>
    <div class="main-content" id="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Editar Agendamento</h2>
                <p class="lead">Altere os dados do agendamento abaixo.</p>
            </div>
            <a href="../agendamentos.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
        </header>
        <div class="card-kpi p-4">
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                <?php foreach ($erros as $erro): ?>
                    <p class="mb-0"><?php echo htmlspecialchars($erro); ?></p>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="editarAgendamento.php?id=<?php echo $agendamento_id; ?>" method="POST">
                <input type="hidden" name="agen_id" value="<?php echo $agendamento['agen_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="agen_data_a" class="form-label">Data</label>
                        <input type="date" class="form-control form-control-dark" id="agen_data_a" name="agen_data_a"
                            value="<?php echo htmlspecialchars($agendamento['agen_data_a']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="agen_hora_a" class="form-label">Hora</label>
                        <input type="time" class="form-control form-control-dark" id="agen_hora_a" name="agen_hora_a"
                            value="<?php echo htmlspecialchars(substr($agendamento['agen_hora_a'],0,5)); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="agen_status" class="form-label">Status</label>
                    <select class="form-control form-control-dark" id="agen_status" name="agen_status" required>
                        <?php
                        $statuses = ["Confirmado","Concluído","Cancelado","Pendente"];
                        foreach ($statuses as $st): ?>
                            <option value="<?php echo $st; ?>" <?php if($agendamento['agen_status']==$st) echo 'selected'; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-purple btn-lg"><i class="bi bi-check-circle-fill me-2"></i> Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>
