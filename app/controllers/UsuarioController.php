<?php
// controllers/usuariocontroller.php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- LÓGICA DE LOGIN (COM A NOVA LÓGICA DE REDIRECIONAMENTO) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (empty($_POST['user_or_email']) || empty($_POST['senha'])) {
        header("Location: ../views/usuario/login.php?erro=2");
        exit();
    }
    
    $usuario = Usuario::autenticar($conn, $_POST['user_or_email'], $_POST['senha']);

    if ($usuario) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['usuario_nome'] = $usuario['usuario_nome'];
        $_SESSION['usuario_tipo'] = $usuario['usuario_tipo'];
        
        // =================================================================
        //  INÍCIO DA MUDANÇA: Redirecionamento baseado no tipo de usuário
        // =================================================================

        if ($usuario['usuario_tipo'] == 1) {
            // Se usuario_tipo for 1, o usuário é um Administrador
            // Redireciona para o painel de admin
            header("Location: ../views/admin/dashboard.php"); // Verifique se este é o caminho correto para seu painel de admin
        } else {
            // Se for qualquer outro valor (geralmente 0), é um usuário comum
            // Redireciona para a página principal
            header("Location: ../public/index.php");
        }
        
        // =================================================================
        //  FIM DA MUDANÇA
        // =================================================================
        
        exit();
    } else {
        
        header("Location: ../views/usuario/login.php?erro=1");
        exit();
    }
}

// --- LÓGICA DE REGISTRO (sem alterações) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    
    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha'])) {
        header("Location: ../views/usuario/registro.php?erro=2");
        exit();
    }
    
    // Pega os dados básicos
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Pega e limpa os dados com máscara
    $cpf_mascarado = $_POST['cpf'] ?? '';
    $tel_mascarado = $_POST['tel'] ?? '';
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf_mascarado);
    $tel_limpo = preg_replace('/[^0-9]/', '', $tel_mascarado);

    // Passa todos os dados para o método de criação
    $sucesso = Usuario::criar($conn, $nome, $email, $cpf_limpo, $tel_limpo, $senha);
    
    if ($sucesso) {
        // A linha com echo alert() não funciona antes de um header, pois o redirecionamento acontece antes de qualquer output.
        // A mensagem de sucesso já é tratada na própria página de login com o parâmetro ?cadastrado=1
        header("Location: ../views/usuario/login.php?cadastrado=1");
    } else {
        // Este erro geralmente significa que o e-mail já existe
        header("Location: ../views/usuario/registro.php?erro=3"); 
    }
    exit();
}

header("Location: ../views/usuario/login.php");
exit();
?>