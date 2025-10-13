<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BLOQUEIO DE SEGURANÇA
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

// Inclui a conexão
require_once '../../config/database.php';

$erros = [];
$corte_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$corte_id) {
    header("Location: cortes.php?erro=id_invalido");
    exit();
}

// --- PROCESSAMENTO DO FORMULÁRIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = filter_input(INPUT_POST, 'corte_id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['corte_nome']);
    $preco = str_replace(',', '.', trim($_POST['corte_preco'])); // Aceita vírgula e ponto
    $descricao = trim($_POST['corte_descricao']);
    $foto = trim($_POST['corte_foto']);

    // Validação
    if (empty($nome)) $erros[] = "O nome do corte é obrigatório.";
    if (!is_numeric($preco) || $preco < 0) $erros[] = "O preço deve ser um número válido.";

    if (empty($erros)) {
        $stmt = $conn->prepare("UPDATE corte SET corte_nome = ?, corte_preco = ?, corte_descricao = ?, corte_foto = ? WHERE corte_id = ?");
        // sdssi = string, double, string, string, integer
        $stmt->bind_param("sdssi", $nome, $preco, $descricao, $foto, $id_post);

        if ($stmt->execute()) {
            header("Location: cortes.php?sucesso=alterado");
            exit();
        } else {
            $erros[] = "Erro ao atualizar o corte.";
        }
        $stmt->close();
    }
}

// --- BUSCAR DADOS DO CORTE (GET) ---
$stmt = $conn->prepare("SELECT corte_nome, corte_preco, corte_descricao, corte_foto FROM corte WHERE corte_id = ?");
$stmt->bind_param("i", $corte_id);
$stmt->execute();
$result = $stmt->get_result();
$corte = $result->fetch_assoc();
$stmt->close();

if (!$corte) {
    header("Location: cortes.php?erro=corte_nao_encontrado");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Corte - Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Copie e cole o CSS do seu 'cortes.php' aqui */
        :root { --cor-fundo: #181828; --cor-fundo-secundario: #1f1f2e; --cor-texto: #f0f0f0; --cor-primaria: #d633ff; --cor-secundaria: #e6b800; } body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); } .sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000; width: 250px; padding: 20px; background-color: #000; display: flex; flex-direction: column; transition: transform 0.3s ease; } .sidebar .logo { font-size: 1.8rem; font-weight: bold; color: var(--cor-primaria); text-align: center; margin-bottom: 30px; } .sidebar .nav-link { color: var(--cor-texto); font-size: 1.2rem; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: background-color 0.3s, color 0.3s; } .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: var(--cor-primaria); color: #fff; } .sidebar .logout-link { margin-top: auto; } .main-content { margin-left: 250px; padding: 30px; transition: margin-left 0.3s ease; } .card-kpi { background-color: var(--cor-fundo-secundario); border: 1px solid #333; border-radius: 12px; padding: 20px; color: var(--cor-texto); } .table-dark-custom thead th { color: var(--cor-texto); background-color: var(--cor-primaria); } td{color: white}; .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); } .form-control-dark:focus { background-color: #2a2a3e; color: #fff; border-color: var(--cor-secundaria); box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25); } .mobile-header { display: none; background-color: #000; padding: 10px 15px; align-items: center; } .menu-toggle { font-size: 1.5rem; color: #fff; background: none; border: none; } .overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 999; } .btn-purple { background-color: var(--cor-primaria); color: #fff; border: none; } .btn-purple:hover { background-color: #b822e0; } @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.is-active { transform: translateX(0); } .main-content { margin-left: 0; } .header { display: none; } .mobile-header { display: flex; } .overlay.is-active { display: block; } }
    </style>
</head>
<body>
<div class="main-content" id="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Editar Corte</h2>
            <p class="lead">Altere as informações do serviço abaixo.</p>
        </div>
        <a href="cortes.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
    </header>
    
    <div class="card-kpi p-4">
        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $erro): ?><p class="mb-0"><?php echo htmlspecialchars($erro); ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="editarCorte.php?id=<?php echo $corte_id; ?>" method="POST">
            <input type="hidden" name="corte_id" value="<?php echo $corte_id; ?>">
            <div class="mb-3">
                <label for="corte_nome" class="form-label">Nome do Corte</label>
                <input type="text" class="form-control form-control-dark" id="corte_nome" name="corte_nome" value="<?php echo htmlspecialchars($corte['corte_nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="corte_preco" class="form-label">Preço (R$)</label>
                <input type="text" class="form-control form-control-dark" id="corte_preco" name="corte_preco" value="<?php echo number_format($corte['corte_preco'], 2, ',', '.'); ?>" required>
            </div>
             <div class="mb-3">
                <label for="corte_descricao" class="form-label">Descrição</label>
                <textarea class="form-control form-control-dark" id="corte_descricao" name="corte_descricao" rows="3"><?php echo htmlspecialchars($corte['corte_descricao']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="corte_foto" class="form-label">Nome do Arquivo da Foto</label>
                <input type="text" class="form-control form-control-dark" id="corte_foto" name="corte_foto" value="<?php echo htmlspecialchars($corte['corte_foto']); ?>">
            </div>
            <button type="submit" class="btn btn-purple btn-lg"><i class="bi bi-check-circle-fill me-2"></i> Salvar Alterações</button>
        </form>
    </div>
</div>
</body>
</html>