<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Segurança: Apenas admins podem realizar estas ações.
// Esta verificação no topo protege todas as ações dentro do arquivo.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 1) {
    // Se não for admin, redireciona para o login com erro.
    header("Location: ../views/usuario/login.php?erro=acessonegado");
    exit();
}

require_once '../../config/database.php';

// Determina a ação a ser executada, vinda de um formulário (POST) ou de um link (GET)
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// Usa uma estrutura switch para direcionar para a lógica correta
switch ($acao) {

    // Caso a ação seja 'criar' (vinda do formulário criarProduto.php)
    case 'criar':
        $nome = trim($_POST['produto_nome']);
        $qtd = filter_input(INPUT_POST, 'produto_qtd', FILTER_VALIDATE_INT);

        // Validação simples para garantir que os dados não estão vazios/inválidos
        if (!empty($nome) && $qtd !== false && $qtd >= 0) {
            $stmt = $conn->prepare("INSERT INTO estoque (est_nome, est_qtd) VALUES (?, ?)");
            $stmt->bind_param("si", $nome, $qtd);
            $stmt->execute();
            $stmt->close();
        }
        break;

    // Caso a ação seja 'atualizar_qtd' (vinda dos botões + e - na lista de estoque.php)
    case 'atualizar_qtd':
        $id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $quantidade_mod = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT); // Será 1 para adicionar ou -1 para remover

        if ($id && $quantidade_mod) {
            // Prepara a query de atualização.
            // A função GREATEST(0, ...) é um truque do SQL para garantir que a quantidade nunca fique negativa.
            // Se (est_qtd + -1) resultar em -1, GREATEST(0, -1) retornará 0.
            $stmt = $conn->prepare("UPDATE estoque SET est_qtd = GREATEST(0, est_qtd + ?) WHERE est_id = ?");
            $stmt->bind_param("ii", $quantidade_mod, $id);
            $stmt->execute();
            $stmt->close();
        }
        break;

    // Caso a ação seja 'excluir' (vinda do link da lixeira em estoque.php)
    case 'excluir':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM estoque WHERE est_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        break;
}

// Fecha a conexão com o banco de dados
$conn->close();

// Após qualquer ação, redireciona o usuário de volta para a página de estoque para ver o resultado
header("Location: ../../views/admin/estoque.php");
exit();
?>