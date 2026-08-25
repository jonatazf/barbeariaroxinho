<?php
// app/includes/validador.php

class Validador {
    
    // Valida o formato do e-mail
    public static function isEmailValido(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Valida a força da senha
    public static function isSenhaForte(string $senha): array {
        $erros = [];
        if (strlen($senha) < 8) $erros[] = "A senha deve ter no mínimo 8 caracteres.";
        if (!preg_match('/[A-Z]/', $senha)) $erros[] = "A senha deve conter pelo menos uma letra maiúscula.";
        if (!preg_match('/[a-z]/', $senha)) $erros[] = "A senha deve conter pelo menos uma letra minúscula.";
        if (!preg_match('/[0-9]/', $senha)) $erros[] = "A senha deve conter pelo menos um número.";
        if (!preg_match('/[\W_]/', $senha)) $erros[] = "A senha deve conter pelo menos um caractere especial (ex: !@#$%).";
        return $erros;
    }

    // Valida o formato do telefone
    public static function isTelefoneValido(string $telefone): bool {
        $telefone = preg_replace('/\D/', '', $telefone);
        return strlen($telefone) >= 10 && strlen($telefone) <= 11;
    }

    // Valida o CPF, incluindo os dígitos verificadores
    public static function isCPFValido(string $cpf): bool {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }
}
?>