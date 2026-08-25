<?php
// app/controllers/UsuarioController.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../config/database.php';
// Crie o arquivo validador.php se ele não existir
require_once __DIR__ . '/../includes/validador.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php");
    exit();
}

$acao = $_POST['acao'] ?? '';

// --- LÓGICA DE REGISTRO (ATUALIZADA) ---
if ($acao === 'registrar') {
    $nome = trim($_POST['nome']);
    $usuario_user = trim($_POST['usuario_user']); // Novo campo
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $erros = [];

    // Validações
    if (empty($nome) || empty($usuario_user) || empty($email) || empty($cpf) || empty($telefone) || empty($senha)) $erros[] = "Todos os campos marcados com * são obrigatórios.";
    if (strpos($usuario_user, ' ') !== false) $erros[] = "O nome de usuário não pode conter espaços.";
    if ($senha !== $confirma_senha) $erros[] = "As senhas não coincidem.";
    if (!Validador::isEmailValido($email)) $erros[] = "O formato do e-mail é inválido.";
    if (!Validador::isCPFValido($cpf)) $erros[] = "O CPF informado é inválido.";
    
    // Se passou nas validações, verifica se o usuário/email/cpf já existe
    if (empty($erros)) {
        $cpf_limpo_check = preg_replace('/\D/', '', $cpf);
        $stmt = $conn->prepare("SELECT usuario_id FROM usuario WHERE usuario_email = ? OR usuario_cpf = ? OR usuario_user = ?");
        $stmt->bind_param("sss", $email, $cpf_limpo_check, $usuario_user);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $erros[] = "O e-mail, CPF ou nome de usuário informado já está cadastrado.";
        $stmt->close();
    }

    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        $_SESSION['inputs'] = $_POST;
        header("Location: ../views/usuario/registro.php");
        exit();
    } 
    // Se não houver erros, cadastra o usuário
    else {
        $senha_hash = password_hash($senha, PASSWORD_ARGON2ID);
        $cpf_limpo = preg_replace('/\D/', '', $cpf);
        $telefone_limpo = preg_replace('/\D/', '', $telefone);

        // CORRIGIDO: Inserindo o campo 'usuario_user' e definindo 'usuario_tipo' como 0
        $stmt = $conn->prepare("INSERT INTO usuario (usuario_nome, usuario_user, usuario_email, usuario_cpf, usuario_tel, usuario_senha, usuario_tipo) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssss", $nome, $usuario_user, $email, $cpf_limpo, $telefone_limpo, $senha_hash);
        
        if ($stmt->execute()) {
            header("Location: ../views/usuario/login.php?cadastro=sucesso");
        } else {
            $_SESSION['erros'] = ["Ocorreu um erro ao salvar os dados. Tente novamente."];
            header("Location: ../views/usuario/registro.php");
        }
        $stmt->close();
        exit();
    }
}

// --- LÓGICA DE LOGIN (ATUALIZADA) ---
if ($acao === 'login') {
    $user_or_email = trim($_POST['user_or_email']);
    $senha = $_POST['senha'];

    if (empty($user_or_email) || empty($senha)) {
        header("Location: ../views/usuario/login.php?erro=1");
        exit();
    }

    // Busca por email OU por nome de usuário
    $stmt = $conn->prepare("SELECT usuario_id, usuario_nome, usuario_senha, usuario_tipo FROM usuario WHERE usuario_email = ? OR usuario_user = ?");
    $stmt->bind_param("ss", $user_or_email, $user_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();

    if ($usuario && password_verify($senha, $usuario['usuario_senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['usuario_nome'] = $usuario['usuario_nome'];
        $_SESSION['usuario_tipo'] = $usuario['usuario_tipo'];
        
        if ($usuario['usuario_tipo'] == 1) { // Admin
            header("Location: ../views/admin/");
        } else { // Usuário Comum
            header("Location: ../public/index.php");
        }
        exit();
    } else {
        header("Location: ../views/usuario/login.php?erro=1");
        exit();
    }
}

header("Location: ../public/index.php");
exit();
?>