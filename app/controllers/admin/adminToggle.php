<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    http_response_code(403); exit();
}
require_once '../../config/database.php';

$id = intval($_POST['id']);
if ($id == $_SESSION['usuario_id']) { // Previna auto-desprivilegiar
    echo "erro:Não pode alterar o próprio privilégio!";
    exit();
}
$is_admin = ($_POST['is_admin'] == 1) ? 1 : 0;
$stmt = $conn->prepare("UPDATE usuario SET usuario_tipo=? WHERE usuario_id=?");
$stmt->bind_param("ii", $is_admin, $id);
$stmt->execute();
echo "ok";
