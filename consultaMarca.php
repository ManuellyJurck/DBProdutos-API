<?php

// Ativar exibição de erros para debug (útil em ambiente de desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir o script de conexão existente
require_once 'conexao.php'; // Certifique-se de que este arquivo existe e funciona

// Definir o charset da conexão
$con->set_charset("utf8");

// Novo SQL: Selecionar todos os campos visíveis da tabela 'marca'
// (Geralmente o campo BLOB/Logo é omitido em consultas gerais de listagem)
$sql = "SELECT idMarca, nmMarca, Qualidade, idFornecedor FROM marca ORDER BY nmMarca";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    // Se houver resultados, adiciona cada linha ao array de resposta
    while ($row = $result->fetch_assoc()) {
        $response[] = $row; // Adiciona o array associativo da linha
    }
} else {
    // Se não houver resultados, retorna uma estrutura de marca vazia/padrão
    $response[] = [
        "idMarca" => 0,
        "nmMarca" => "",
        "Qualidade" => "",
        "idFornecedor" => 0
    ];
}

// Definir o cabeçalho para indicar que a resposta é JSON e usa UTF-8
header('Content-Type: application/json; charset=utf-8');

// Codificar a resposta para JSON. JSON_UNESCAPED_UNICODE preserva acentos.
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Fechar a conexão com o banco de dados
$con->close();
?>