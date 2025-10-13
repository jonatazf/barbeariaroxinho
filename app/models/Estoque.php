<?php
class Estoque {
    public static function listar($conn) {
        return $conn->query("SELECT * FROM estoque");
    }
    public static function criar($conn, $nome, $qtd) {
        $stmt = $conn->prepare("INSERT INTO estoque (est_nome, est_qtd) VALUES (?, ?)");
        $stmt->bind_param('si', $nome, $qtd);
        return $stmt->execute();
    }
}
?>
    