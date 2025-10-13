<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    header("Location: ../usuario/login.php?erro=acessonegado");
    exit();
}
$erro = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../../config/database.php';
    $id = $_SESSION['usuario_id'];
    $senha = $_POST['senha'] ?? '';
    // Supondo que a senha está hash no campo usuario_senha!
    $stmt = $conn->prepare("SELECT usuario_senha FROM usuario WHERE usuario_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_assoc()['usuario_senha'];
    if (password_verify($senha, $hash)) {
        $_SESSION['admin_senha_verificada'] = true;
        $next = isset($_GET['next']) ? $_GET['next'] : 'usuarios.php';
        header("Location: $next");
        exit();
    } else {
        $erro = "Senha incorreta!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirme a Senha | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-dark text-light d-flex justify-content-center align-items-center" style="height:100vh;">
<div class="card p-4 bg-secondary">
    <h3 class="mb-3">Confirme sua senha de administrador</h3>
    <?php if($erro): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>
    <form method="post">
        <input type="password" class="form-control mb-3" name="senha" placeholder="Senha de admin" required autofocus>
        <button class="btn btn-primary">Entrar</button>
    </form>
</div>
</body></html>
