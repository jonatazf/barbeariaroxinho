<?php
// app/views/usuario/editar_dados.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Bloqueio de segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?erro=precisa_logar");
    exit();
}

// 2. Conexão e busca dos dados atuais do usuário
require_once '../../config/database.php';
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT usuario_nome, usuario_user, usuario_email, usuario_cpf, usuario_tel FROM usuario WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$usuario) {
    header("Location: ../../controllers/logout.php");
    exit();
}

// Pega erros da sessão, se houver
$erros = $_SESSION['erros'] ?? [];
unset($_SESSION['erros']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Editar Meus Dados | Roxinho's Barber</title>
    <meta charset="UTF-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-card: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); }
        .section-title { font-size: 2.5rem; color: var(--cor-primaria); font-weight: bold; }
        .card-dados { background-color: var(--cor-fundo-card); border: 1px solid var(--cor-primaria); border-radius: 15px; }
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        .form-control-dark:focus { background-color: #2a2a3e; color: #fff; box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25); }
        .btn-purple { background-color: var(--cor-primaria); color: white; border: none; font-weight: bold; }
    </style>
</head>
<body>

<main class="container" style="padding-top: 50px; padding-bottom: 80px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="section-title text-center mb-5">Editar Minhas Informações</h2>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?><li><?php echo $erro; ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card-dados p-4 p-md-5 mb-5">
                <form action="../../controllers/UsuarioController.php" method="POST">
                    <input type="hidden" name="acao" value="atualizar_dados">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control form-control-dark" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['usuario_nome']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario_user" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control form-control-dark" id="usuario_user" name="usuario_user" value="<?php echo htmlspecialchars($usuario['usuario_user']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-dark" id="email" name="email" value="<?php echo htmlspecialchars($usuario['usuario_email']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="cpf" class="form-label">CPF</label><input type="text" class="form-control form-control-dark" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario['usuario_cpf']); ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="telefone" class="form-label">Telefone</label><input type="tel" class="form-control form-control-dark" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['usuario_tel']); ?>" required></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="meus_dados.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-purple">Salvar Alterações</button>
                    </div>
                </form>
            </div>

            <div class="card-dados p-4 p-md-5">
                <h4 class="mb-4">Alterar Senha</h4>
                <form action="../../controllers/UsuarioController.php" method="POST">
                    <input type="hidden" name="acao" value="atualizar_senha">
                    <div class="mb-3">
                        <label for="senha_atual" class="form-label">Senha Atual</label>
                        <input type="password" class="form-control form-control-dark" id="senha_atual" name="senha_atual" required>
                    </div>
                    <div class="mb-3">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control form-control-dark" id="nova_senha" name="nova_senha" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirma_nova_senha" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control form-control-dark" id="confirma_nova_senha" name="confirma_nova_senha" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-purple">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });
        IMask(document.getElementById('telefone'), { mask: [ { mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' } ] });
    });
</script>
</body>
</html>