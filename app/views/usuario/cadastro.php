<?php
// Inicia a sessão para poder exibir as mensagens de erro
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Pega erros e inputs da sessão (se existirem)
$erros = $_SESSION['erros'] ?? [];
$inputs = $_SESSION['inputs'] ?? [];

// Limpa a sessão para não mostrar os erros novamente
unset($_SESSION['erros']);
unset($_SESSION['inputs']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Cadastro | Roxinho's Barber</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-card: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card-cadastro { background-color: var(--cor-fundo-card); border: 1px solid var(--cor-primaria); border-radius: 15px; max-width: 500px; width: 100%; }
        .card-title { font-size: 2rem; color: var(--cor-primaria); font-weight: bold; }
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        .form-control-dark:focus { background-color: #2a2a3e; color: #fff; border-color: var(--cor-secundaria); box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25); }
        .btn-purple { background-color: var(--cor-primaria); color: white; border: none; font-weight: bold; }
        .btn-purple:hover { background-color: #9333ea; }
    </style>
</head>
<body>
    <div class="card card-cadastro p-4 p-md-5">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Crie sua Conta</h2>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo $erro; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="../../controllers/UsuarioController.php" method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control form-control-dark" id="nome" name="nome" value="<?php echo htmlspecialchars($inputs['nome'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-dark" id="email" name="email" value="<?php echo htmlspecialchars($inputs['email'] ?? ''); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control form-control-dark" id="cpf" name="cpf" value="<?php echo htmlspecialchars($inputs['cpf'] ?? ''); ?>" placeholder="000.000.000-00" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control form-control-dark" id="telefone" name="telefone" value="<?php echo htmlspecialchars($inputs['telefone'] ?? ''); ?>" placeholder="(00) 90000-0000" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control form-control-dark" id="senha" name="senha" required>
                </div>
                <div class="mb-3">
                    <label for="confirma_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control form-control-dark" id="confirma_senha" name="confirma_senha" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-purple btn-lg">Cadastrar</button>
                </div>
                <div class="text-center mt-3">
                    <p>Já tem uma conta? <a href="login.php" style="color: var(--cor-secundaria);">Faça login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>