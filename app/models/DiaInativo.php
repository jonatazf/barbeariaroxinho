<?php
class DiaInativo {
    public static function listar($conn) {
        return $conn->query("SELECT * FROM dia_inativo ORDER BY diaInativo_data_inativa DESC");
    }
    public static function criar($conn, $data, $hora_inicio, $hora_fim, $motivo) {
        $stmt = $conn->prepare("INSERT INTO dia_inativo (diaInativo_data_inativa, diaInativo_hora_inicio, diaInativo_hora_fim, diaInativo_motivo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $data, $hora_inicio, $hora_fim, $motivo);
        return $stmt->execute();
    }
}
?>
