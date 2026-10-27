<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redireciona se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    // Redireciona para o painel de admin ou para a home, dependendo do tipo de usuário
    if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 1) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../../public/index.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-card: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-image: url('../../public/assets/img/background.png'); color: var(--cor-texto); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px 0;}
        .card-login { background-color: var(--cor-fundo-card); border: 1px solid var(--cor-primaria); border-radius: 15px; max-width: 450px; width: 100%; }
        .card-title { font-size: 2rem; color: var(--cor-primaria); font-weight: bold; }
        .form-label { color: white; }
        
        /* Ajustes para o Input Group (Olhinho) */
        .input-group .form-control-dark {
            border-right: none; /* Remove borda direita para colar no botão */
        }
        .input-group .btn-reveal {
            background-color: #2a2a3e;
            border: 1px solid var(--cor-primaria);
            border-left: none; /* Remove borda esquerda */
            color: #fff;
        }
        .input-group .btn-reveal:hover {
            background-color: #3b3b54;
            color: var(--cor-primaria);
        }
        /* Foco no grupo */
        .form-control-dark:focus, .btn-reveal:focus {
            border-color: var(--cor-primaria);
            box-shadow: none; /* Remove sombra padrão para não quebrar o layout */
        }
        /* Borda iluminada no grupo inteiro ao focar no input */
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25);
            border-radius: 0.375rem;
        }

        .form-control-dark { background-color: #2a2a3e; color: white; border: 1px solid var(--cor-primaria); }
        
        /* Regra específica para input isolado (sem grupo) */
        .form-control-dark:not(.input-group > .form-control-dark):focus { 
            background-color: #2a2a3e; color: #fff; box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25); 
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

    <div class="card card-login p-4 p-md-5">
        <div class="card-body">
            <h2 class="card-title text-center mb-4"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h2>

            <?php if(isset($_GET['erro'])):
                $mensagem_erro = '';
                switch ($_GET['erro']) {
                    case '1': $mensagem_erro = 'Email/usuário ou senha inválidos. Tente novamente.'; break;
                    case '2': $mensagem_erro = 'Por favor, preencha todos os campos.'; break;
                    case 'precisa_logar': $mensagem_erro = 'Você precisa fazer login para poder agendar um horário.'; break;
                    case 'acessonegado': $mensagem_erro = 'Você não tem permissão para acessar esta página.'; break;
                    default: $mensagem_erro = 'Ocorreu um erro inesperado.'; break;
                }
            ?>
                <div class="alert alert-danger"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
            
            <?php if(isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                <div class="alert alert-success">Cadastro realizado com sucesso! Faça seu login.</div>
            <?php endif; ?>

            <form action="../../controllers/UsuarioController.php" method="POST">
                <input type="hidden" name="acao" value="login">
                <div class="mb-3">
                    <label for="user_or_email" class="form-label">Email ou Nome de Usuário</label>
                    <input type="text" class="form-control form-control-dark" id="user_or_email" name="user_or_email" required>
                </div>
                
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-dark" id="senha" name="senha" required>
                        <button class="btn btn-reveal" type="button" id="toggleSenha">
                            <i class="bi bi-eye-slash" id="iconeOlho"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid"><button type="submit" class="btn btn-purple btn-lg">Entrar</button></div>
                <div class="text-center mt-3 text-white"><p>Não tem uma conta? <a href="registro.php" style="color: var(--cor-secundaria);">Cadastre-se</a></p></div>
                <div class="text-center mt-3 text-white"><p><a href="registro.php" style="color: var(--cor-secundaria);">Esqueceu a senha?</a></p></div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('toggleSenha').addEventListener('click', function () {
            const senhaInput = document.getElementById('senha');
            const iconeOlho = document.getElementById('iconeOlho');

            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                iconeOlho.classList.remove('bi-eye-slash');
                iconeOlho.classList.add('bi-eye');
            } else {
                senhaInput.type = 'password';
                iconeOlho.classList.remove('bi-eye');
                iconeOlho.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>
</html>