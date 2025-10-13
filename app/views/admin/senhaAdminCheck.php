<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    http_response_code(403); exit("erro");
}
require_once '../../config/database.php';
$id = $_SESSION['usuario_id'];
$senha = $_POST['senha'] ?? '';
$stmt = $conn->prepare("SELECT usuario_senha FROM usuario WHERE usuario_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hash = ($stmt->get_result())->fetch_assoc()['usuario_senha'];
if (password_verify($senha, $hash)) {
    echo "ok";
} else {
    echo "erro";
}
