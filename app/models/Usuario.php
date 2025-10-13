<?php
// models/Usuario.php
class Usuario {
    
    // O método agora aceita os parâmetros $cpf e $tel
    public static function criar($conn, $nome, $email, $cpf, $tel, $senha) {
        
        // Verifica se o email já existe para evitar duplicatas
        $sqlCheck = "SELECT usuario_id FROM usuario WHERE usuario_email = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            return false; // Retorna falso se o email já estiver em uso
        }
        $stmtCheck->close();
        
        // Criptografa a senha para segurança
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        // A query SQL agora inclui as colunas usuario_cpf e usuario_tel
        $sql = "INSERT INTO usuario (usuario_nome, usuario_email, usuario_cpf, usuario_tel, usuario_senha, usuario_tipo) VALUES (?, ?, ?, ?, ?, 0)";
        
        $stmt = $conn->prepare($sql);
        // O bind_param agora tem 5 's' (sssss), um para cada variável de texto
        $stmt->bind_param("sssss", $nome, $email, $cpf, $tel, $senhaHash);
        
        $sucesso = $stmt->execute();
        $stmt->close();
        
        return $sucesso;
    }

    public static function autenticar($conn, $user_or_email, $senha) {
        $sql = "SELECT * FROM usuario WHERE usuario_email = ? OR usuario_nome = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_or_email, $user_or_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            if (password_verify($senha, $usuario['usuario_senha'])) {
                return $usuario;
            }
        }
        return false;
    }
}
?>