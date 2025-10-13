<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BLOQUEIO DE SEGURANÇA: Apenas admins logados podem executar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}

// Inclui a conexão
require_once '../../config/database.php';

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: usuarios.php");
    exit();
}

// Pega e valida o ID do usuário vindo do formulário
$usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

if (!$usuario_id) {
    header("Location: usuarios.php?erro=id_invalido");
    exit();
}

// MEDIDA DE SEGURANÇA: Impede que um admin remova o próprio acesso
if ($usuario_id == $_SESSION['usuario_id']) {
    header("Location: usuarios.php?erro=demote_self");
    exit();
}

// Determina o novo tipo de usuário
// Se o checkbox 'is_admin' foi marcado, ele vem no POST. Se não, ele não vem.
// Assumindo: 1 = Admin, 2 = Usuário Padrão
$novo_tipo = isset($_POST['is_admin']) ? 1 : 2;

// Prepara e executa a atualização no banco de dados
try {
    $stmt = $conn->prepare("UPDATE usuario SET usuario_tipo = ? WHERE usuario_id = ?");
    // "ii" -> o primeiro parâmetro é um integer, o segundo também
    $stmt->bind_param("ii", $novo_tipo, $usuario_id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?sucesso=admin_status");
    } else {
        header("Location: usuarios.php?erro=db_update_failed");
    }
    $stmt->close();
} catch (Exception $e) {
    // Em caso de erro de banco, redireciona com uma mensagem genérica
    header("Location: usuarios.php?erro=db_exception");
}

$conn->close();
exit();