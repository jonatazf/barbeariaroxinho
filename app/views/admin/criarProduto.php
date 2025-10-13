<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Segurança: Apenas admins podem acessar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto | Admin Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        /* Estilos consistentes com o painel de admin */
        :root { --cor-fundo: #181828; --cor-fundo-secundario: #1f1f2e; --cor-texto: #f0f0f0; --cor-primaria: #d633ff; --cor-secundaria: #e6b800; } 
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); } 
        .main-content { padding: 30px; } 
        .card-kpi { background-color: var(--cor-fundo-secundario); border: 1px solid #333; border-radius: 12px; padding: 20px; color: var(--cor-texto); } 
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); } 
        .form-control-dark:focus { background-color: #2a2a3e; color: #fff; border-color: var(--cor-secundaria); box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25); }
        .btn-purple { background-color: var(--cor-primaria); color: #fff; border: none; } 
        .btn-purple:hover { background-color: #b822e0; }
    </style>
</head>
<body>

<div class="main-content container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Cadastrar Novo Produto</h2>
            <p class="lead">Preencha os dados do item para adicioná-lo ao estoque.</p>
        </div>
        <a href="estoque.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i> Voltar para o Estoque</a>
    </header>
    
    <div class="card-kpi p-4">
        <form action="../../controllers/admin/EstoqueController.php" method="POST">
            <input type="hidden" name="acao" value="criar">
            
            <div class="mb-3">
                <label for="produto_nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-dark" id="produto_nome" name="produto_nome" required>
            </div>
            
            <div class="mb-3">
                <label for="produto_qtd" class="form-label">Quantidade Inicial <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-dark" id="produto_qtd" name="produto_qtd" min="0" value="0" required>
            </div>

            <button type="submit" class="btn btn-purple btn-lg"><i class="bi bi-check-circle-fill me-2"></i> Salvar Produto</button>
        </form>
    </div>
</div>

</body>
</html>