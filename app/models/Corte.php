<?php
class Corte {
    public static function listar($conn) {
        return $conn->query("SELECT * FROM corte");
    }
    public static function criar($conn, $nome, $preco, $desc, $foto) {
        $stmt = $conn->prepare("INSERT INTO corte (corte_nome, corte_preco, corte_descricao, corte_foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sdss', $nome, $preco, $desc, $foto);
        return $stmt->execute();
    }
}
?>
