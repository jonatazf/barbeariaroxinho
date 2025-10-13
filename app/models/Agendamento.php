<?php
class Agendamento {
  public static function criar($conn, $usuario_id, $corte_id, $data, $hora) {
    $stmt = $conn->prepare("INSERT INTO agendamento (agen_data_a, agen_hora_a, usuario_id, corte_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssii', $data, $hora, $usuario_id, $corte_id);
    return $stmt->execute();
  }
  public static function listar($conn) {
    $sql = "SELECT a.*, u.usuario_nome, c.corte_nome FROM agendamento a
            JOIN usuario u ON a.usuario_id = u.usuario_id
            JOIN corte c ON a.corte_id = c.corte_id
            ORDER BY a.agen_data_a DESC, a.agen_hora_a DESC";
    return $conn->query($sql);
  }
}
?>
