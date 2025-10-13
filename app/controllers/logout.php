<?php
// controllers/logout.php

// 1. Inicia ou continua a sessão existente.
// É necessário para poder modificar a sessão.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpa todas as variáveis da sessão.
// Isso remove 'usuario_id', 'usuario_nome', etc.
$_SESSION = array();

// 3. Destrói a sessão completamente.
// Isso remove o cookie de sessão do navegador do usuário.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 4. Redireciona o usuário para a página de login com uma mensagem.
// O '?logout=1' pode ser usado na página de login para mostrar uma mensagem "Você saiu com sucesso".
header("Location: ../public/index.php");
exit(); // Garante que nenhum outro código seja executado após o redirecionamento.
?>