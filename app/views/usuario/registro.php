<?php
// app/views/usuario/registro.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['usuario_id'])) { header("Location: ../../index.php"); exit(); }
$erros = $_SESSION['erros'] ?? [];
$inputs = $_SESSION['inputs'] ?? [];
unset($_SESSION['erros'], $_SESSION['inputs']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro | Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-card: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-image: url('../../public/assets/img/background.png'); color: var(--cor-texto); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px 0;}
        .card-cadastro { background-color: var(--cor-fundo-card); border: 1px solid var(--cor-primaria); border-radius: 15px; max-width: 500px; width: 100%; }
        .card-title { font-size: 2rem; color: var(--cor-primaria); font-weight: bold; }
        .form-label { color: white; }
        
        /* Estilos dos Inputs */
        .form-control-dark { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        
        /* Ajustes para o Input Group (Olhinho) */
        .input-group .form-control-dark { border-right: none; }
        .input-group .btn-reveal {
            background-color: #2a2a3e;
            border: 1px solid var(--cor-primaria);
            border-left: none;
            color: #fff;
        }
        .input-group .btn-reveal:hover { background-color: #3b3b54; color: var(--cor-primaria); }
        
        /* Foco no grupo */
        .form-control-dark:focus, .btn-reveal:focus { border-color: var(--cor-primaria); box-shadow: none; }
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25);
            border-radius: 0.375rem;
        }
        /* Foco para inputs normais (sem grupo) */
        .form-control-dark:not(.input-group > .form-control-dark):focus { 
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25); 
        }

        .btn-purple { background-color: var(--cor-primaria); color: white; border: none; font-weight: bold; }
        .btn-purple:hover { background-color: #9333ea; }
        .login-header {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }
        .login-header a {
            font-size: 2rem;
            font-weight: bold;
            color: var(--cor-primaria) !important;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <header class="login-header">
        <a href="../../public/index.php">ROXINHO'S</a>
    </header>
    <div class="card card-cadastro p-4 p-md-5">
        <div class="card-body">
            <h2 class="card-title text-center mb-4"><i class="bi bi-person-plus-fill me-2"></i>Crie sua Conta</h2>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger"><ul class="mb-0">
                    <?php foreach ($erros as $erro): ?><li><?php echo $erro; ?></li><?php endforeach; ?>
                </ul></div>
            <?php endif; ?>

            <form action="../../controllers/UsuarioController.php" method="POST">
                <input type="hidden" name="acao" value="registrar">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control form-control-dark" id="nome" name="nome" value="<?php echo htmlspecialchars($inputs['nome'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="usuario_user" class="form-label">Nome de Usuário *</label>
                    <input type="text" class="form-control form-control-dark" id="usuario_user" name="usuario_user" placeholder="Ex: seunome123" value="<?php echo htmlspecialchars($inputs['usuario_user'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control form-control-dark" id="email" name="email" value="<?php echo htmlspecialchars($inputs['email'] ?? ''); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="cpf" class="form-label">CPF *</label><input type="text" class="form-control form-control-dark" id="cpf" name="cpf" value="<?php echo htmlspecialchars($inputs['cpf'] ?? ''); ?>" placeholder="000.000.000-00" required></div>
                    <div class="col-md-6 mb-3"><label for="telefone" class="form-label">Telefone *</label><input type="tel" class="form-control form-control-dark" id="telefone" name="telefone" value="<?php echo htmlspecialchars($inputs['telefone'] ?? ''); ?>" placeholder="(00) 90000-0000" required></div>
                </div>
                
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha *</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-dark" id="senha" name="senha" required>
                        <button class="btn btn-reveal" type="button" id="toggleSenha">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="confirma_senha" class="form-label">Confirmar Senha *</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-dark" id="confirma_senha" name="confirma_senha" required>
                        <button class="btn btn-reveal" type="button" id="toggleConfirmaSenha">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid"><button type="submit" class="btn btn-purple btn-lg">Cadastrar</button></div>
                <div class="text-center mt-3 text-white"><p>Já tem uma conta? <a href="login.php" style="color: var(--cor-secundaria);">Faça login</a></p></div>
            </form>
        </div>
    </div>
    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Máscaras
            IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });
            IMask(document.getElementById('telefone'), { mask: [ { mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' } ] });

            // Função para alternar visibilidade da senha
            function setupPasswordToggle(buttonId, inputId) {
                const button = document.getElementById(buttonId);
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');

                button.addEventListener('click', function () {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            }

            // Ativa para ambos os campos
            setupPasswordToggle('toggleSenha', 'senha');
            setupPasswordToggle('toggleConfirmaSenha', 'confirma_senha');
        });
    </script>
</body>
</html>