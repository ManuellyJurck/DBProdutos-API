<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o script de conexão existente
require_once 'conexao.php';
$con->set_charset("utf8");

// Decodifica a entrada JSON (se houver), mas ignora seu conteúdo neste GET all
json_decode(file_get_contents('php://input'), true);

// Novo SQL: seleciona todos os campos da tabela 'fornecedor'
$sql = "SELECT idFornecedor, Nome, Cnpj, nmResponsavel, nrContato, flListaNegra FROM fornecedor";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    // Se houver resultados, adiciona cada linha ao array de resposta
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    // Se não houver resultados, retorna uma estrutura de objeto vazio com valores padrão
    $response[] = [
        "idFornecedor" => 0,
        "Nome" => "",
        "Cnpj" => "",
        "nmResponsavel" => "",
        "nrContato" => "",
        "flListaNegra" => "" // CHAR(1)
    ];
}

// Define o cabeçalho para JSON e assegura a codificação UTF-8
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE); // JSON_UNESCAPED_UNICODE preserva caracteres UTF-8

$con->close();
/*
 Vai fazer a consulta de todos os produtos cadastrados -- não precisa de comando json é só clicar em send
 http://localhost/consultaProduto.php -- link do arquivo no insomnia 
*/

?>