<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../../usuario/login.php?erro=acessonegado");
    exit();
}
require_once '../../../config/database.php';
$nome_admin = $_SESSION['usuario_nome'];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: ../diasInativos.php?status=erro_id"); exit(); }

$stmt = $conn->prepare("SELECT * FROM dia_inativo WHERE diaInativo_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$dado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dado) { header("Location: ../diasInativos.php?status=erro_id"); exit(); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dia Inativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #181828; --cor-fundo-secundario: #1f1f2e; --cor-texto: #f0f0f0; --cor-primaria: #d633ff; --cor-secundaria: #e6b800; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); }
        .main-content { max-width: 800px; margin: 40px auto; padding: 20px; }
        .card-kpi { background-color: var(--cor-fundo-secundario); border: 1px solid #333; border-radius: 12px; padding: 30px; }
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        .btn-purple { background-color: var(--cor-primaria); color: #fff; border: none; }
    </style>
</head>
<body>
<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Dia Inativo</h2>
        <a href="../diasInativos.php" class="btn btn-outline-light">Voltar</a>
    </header>
    <div class="card-kpi">
        <form action="../../../controllers/admin/DiaInativoController.php" method="POST">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="diaInativo_id" value="<?= $id ?>">

            <div class="mb-3">
                <label class="form-label">Data <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-dark" name="data_inativa" value="<?= $dado['diaInativo_data_inativa'] ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hora Início</label>
                    <input type="time" class="form-control form-control-dark" name="hora_inicio" value="<?= $dado['diaInativo_hora_inicio'] ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hora Fim</label>
                    <input type="time" class="form-control form-control-dark" name="hora_fim" value="<?= $dado['diaInativo_hora_fim'] ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Motivo</label>
                <textarea class="form-control form-control-dark" name="motivo" rows="3"><?= htmlspecialchars($dado['diaInativo_motivo']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-purple w-100">Salvar Alterações</button>
        </form>
    </div>
</div>
</body>
</html>