<?php
// public/api.php
// Este arquivo é o ponto de entrada público para as funções do backend.

// Ativa a exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de funções de forma segura, usando um caminho do servidor.
// O navegador nunca acessa o 'app/includes' diretamente.
require_once __DIR__ . '/../includes/funcoes.php';
?>