<?php
// app/controllers/admin/cortesController.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// BLOQUEIO DE SEGURANÇA: Apenas admins logados podem executar ações
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    // Sai de admin > controllers > app > entra em views/usuario/login.php
    header("Location: ../../views/usuario/login.php?erro=acessonegado");
    exit();
}

// Sai de admin > controllers > app > entra em config/database.php
require_once '../../config/database.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// --- LÓGICA PARA CRIAR UM NOVO CORTE ---
if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['corte_nome']);
    
    // CORREÇÃO: Limpeza robusta do preço (Remove R$, espaços e letras)
    $precoInput = $_POST['corte_preco'];
    $precoLimpo = preg_replace('/[^0-9,.]/', '', $precoInput); // Mantém apenas números, ponto e vírgula
    $preco = str_replace(',', '.', $precoLimpo); // Troca vírgula por ponto
    
    $descricao = trim($_POST['corte_descricao']);
    
    if (empty($nome) || !is_numeric($preco) || $preco < 0) {
        header("Location: ../../views/admin/cortes.php?erro=dados_invalidos");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO corte (corte_nome, corte_preco, corte_descricao) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $nome, $preco, $descricao);
    
    if ($stmt->execute()) {
        header("Location: ../../views/admin/cortes.php?sucesso=criado");
    } else {
        header("Location: ../../views/admin/cortes.php?erro=generico");
    }
    $stmt->close();
    $conn->close();
    exit();
}

// --- LÓGICA PARA ATUALIZAR (EDITAR) UM CORTE ---
if ($acao === 'atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'corte_id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['corte_nome']);
    
    // CORREÇÃO: Limpeza robusta do preço na edição também
    $precoInput = $_POST['corte_preco'];
    $precoLimpo = preg_replace('/[^0-9,.]/', '', $precoInput);
    $preco = str_replace(',', '.', $precoLimpo);
    
    $descricao = trim($_POST['corte_descricao']);

    if (!$id || empty($nome) || !is_numeric($preco) || $preco < 0) {
        // Retorna para o formulário com erro
        header("Location: ../../views/admin/forms/editarCortes.php?id=$id&erro=dados_invalidos");
        exit();
    }

    $stmt = $conn->prepare("UPDATE corte SET corte_nome = ?, corte_preco = ?, corte_descricao = ? WHERE corte_id = ?");
    $stmt->bind_param("sdsi", $nome, $preco, $descricao, $id);

    if ($stmt->execute()) {
        header("Location: ../../views/admin/cortes.php?sucesso=alterado");
    } else {
        header("Location: ../../views/admin/forms/editarCortes.php?id=$id&erro=generico");
    }
    $stmt->close();
    $conn->close();
    exit();
}

// --- LÓGICA PARA EXCLUIR UM CORTE ---
if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$id) {
        header("Location: ../../views/admin/cortes.php?erro=id_invalido");
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM corte WHERE corte_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: ../../views/admin/cortes.php?sucesso=excluido");
    } else {
        header("Location: ../../views/admin/cortes.php?erro=excluir_falhou");
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Se nenhuma ação válida for encontrada, redireciona de volta para a lista
header("Location: ../../views/admin/cortes.php");
exit();
?>