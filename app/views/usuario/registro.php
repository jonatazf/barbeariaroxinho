<?php
// views/usuario/registro.php
session_start();
// Redireciona se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: ../../controllers/admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro | Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        /* Seus estilos CSS permanecem os mesmos */
        body {
            font-family: 'Barlow Condensed', sans-serif;
            background-color: #121212;
            color: #fff;
            min-height: 100vh;
        }

        .login-container {
            max-width: 450px;
            margin: 5vh auto;
            background: #181828;
            padding: 36px 30px;
            border-radius: 18px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            color: #d633ff;
            font-weight: bold;
            font-size: 2.2rem;
            margin-bottom: 8px;
            text-align: center;
        }

        .form-label {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .form-control::placeholder{
            color: #919191ff;
            opacity: 1;
        }
        
        .form-control,
        .form-select {
            background-color: #1f1f2e !important;
            color: #fff !important;
            border-radius: 14px;
            padding: 14px;
            border: 1.5px solid #d633ff;
            margin-bottom: 18px;
            font-size: 1.15rem;
        }

        .btn-purple {
            background: #d633ff;
            color: #fff;
            font-weight: bold;
            padding: 12px 18px;
            font-size: 1.15rem;
            border-radius: 30px;
            width: 100%;
            margin-bottom: 12px;
            border: none;
            transition: 0.3s;
        }

        .btn-purple:hover {
            background: #b026d1;
            color: #fff;
        }

        .link-register {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #d633ff;
            font-weight: 600;
            text-decoration: none;
        }

        .link-register:hover {
            color: #e6b800;
        }

        .alert-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <?php
        if (isset($_GET['erro'])) {
            $msg = "";
            if ($_GET['erro'] == 2)
                $msg = "Preencha os campos obrigatórios (*).";
            if ($_GET['erro'] == 3)
                $msg = "Erro ao cadastrar. Email ou CPF já podem estar em uso.";
            echo "<div class='alert-message alert-danger'>$msg</div>";
        }
        ?>
        <div class="login-title"><i class="bi bi-person-plus-fill"></i> Cadastro</div>
        <form method="POST" action="../../controllers/UsuarioController.php">
            <label class="form-label" for="nome">Nome Completo *</label>
            <input type="text" class="form-control" name="nome" id="nome" required placeholder="Seu nome completo">

            <label class="form-label" for="email">Email *</label>
            <input type="email" class="form-control" name="email" id="email" required placeholder="Seu melhor email">

            <label class="form-label" for="cpf">CPF (Opcional)</label>
            <input type="text" class="form-control" name="cpf" id="cpf" placeholder="000.000.000-00">

            <label class="form-label" for="tel">Telefone (Opcional)</label>
            <input type="tel" class="form-control" name="tel" id="tel" placeholder="(00) 00000-0000">

            <label class="form-label" for="senha">Senha *</label>
            <input type="password" class="form-control" name="senha" id="senha" required
                placeholder="Crie uma senha forte">

            <button type="submit" class="btn btn-purple" name="registro">Cadastrar</button>

            <a class="link-register" href="login.php">Já possui conta? Fazer Login</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/imask"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Máscara para o campo de CPF
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) {
                const cpfMask = IMask(cpfInput, { mask: '000.000.000-00' });
            }

            // Máscara para o campo de Telefone (aceita 8 ou 9 dígitos)
            const telInput = document.getElementById('tel');
            if (telInput) {
                const telMask = IMask(telInput, {
                    mask: [
                        { mask: '(00) 0000-0000' },
                        { mask: '(00) 00000-0000' }
                    ]
                });
            }
        });
    </script>
</body>

</html>