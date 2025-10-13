<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Roxinho's Barber</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
  <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />

  <style>
    body {
      font-family: 'Barlow Condensed', sans-serif;
      background-color: #121212;
      color: #fff;
      min-height: 100vh;
    }
    .login-container {
      max-width: 400px;
      margin: 10vh auto;
      background: #181828;
      padding: 36px 30px;
      border-radius: 18px;
      box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    }
    .login-title {
      color: #d633ff;
      font-weight: bold;
      font-size: 2.2rem;
      margin-bottom: 8px;
      text-align: center;
    }
    input:focus{
      color: #fff;
    }
    .form-label {
      color: #fff;
      font-size: 1.1rem;
      font-weight: 700;
    }
    .form-control, .form-select {
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
    .social-login {
      text-align: center;
      margin-bottom: 10px;
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
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-title"><i class="bi bi-person-circle"></i> Login</div>
    <form method="POST" action="../../controllers/UsuarioController.php">
      <label class="form-label" for="user_or_email">Usuário ou Email</label>
      <input type="texavt" class="form-control" name="user_or_email" id="user_or_email" required placeholder="Digite seu usuário ou email">

      <label class="form-label" for="senha">Senha</label>
      <input type="password" class="form-control" name="senha" id="senha" required placeholder="Digite sua senha">

      <button type="submit" class="btn btn-purple" name="login">Entrar</button>

      <a class="link-register" href="registro.php">Não possui conta? Cadastre-se</a>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Usa URLSearchParams para pegar os parâmetros da URL de forma segura
    const params = new URLSearchParams(window.location.search);

    // Verifica se existe um parâmetro 'erro'
    if (params.has('erro')) {
        let mensagem = '';
        const erro = params.get('erro');
        
        switch (erro) {
            case '1':
                mensagem = 'Usuário ou senha inválidos. Tente novamente.';
                break;
            case '2':
                mensagem = 'Por favor, preencha todos os campos.';
                break;
            case 'precisa_logar':
                mensagem = 'Você precisa estar logado para agendar um horário.';
                break;
            case 'acessonegado':
                mensagem = 'Você não tem permissão para acessar esta página.';
                break;
            default:
                mensagem = 'Ocorreu um erro inesperado.';
                break;
        }
        // Exibe o alerta com a mensagem de erro
        alert(mensagem);
    }

    // Verifica se existe um parâmetro 'cadastrado' (para sucesso)
    if (params.has('cadastrado') && params.get('cadastrado') == '1') {
        alert('Cadastro realizado com sucesso! Por favor, faça o login.');
    }
</script>
</body>
</html>
