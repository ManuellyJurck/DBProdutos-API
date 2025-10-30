<?php

// Ativar exibição de erros para debug (útil em ambiente de desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir o script de conexão existente
require_once 'conexao.php';

// Definir o charset da conexão para garantir a correta manipulação de caracteres
$con->set_charset("utf8");

// Decodificar a entrada JSON, se houver (mantido, embora não esteja sendo usado para esta consulta)
json_decode(file_get_contents('php://input'), true);

//  Novo SQL: Selecionar todos os campos da tabela 'produto'
$sql = "SELECT idProduto, nmProduto, nrQuantidade, nrIdentificacao, Valor, Disponivel, idMarca FROM produto";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    //  Se houver resultados, adiciona cada linha ao array de resposta
    while ($row = $result->fetch_assoc()) {
        $response[] = $row; // Adiciona o array associativo da linha
    }
} else {
    //  Se não houver resultados, retorna uma estrutura de produto vazia
    $response[] = [
        "idProduto" => 0,
        "nmProduto" => "",
        "nrQuantidade" => "",
        "nrIdentificacao" => "",
        "Valor" => "",
        "Disponivel" => "", // 'char(1)', vazio é a representação mais simples
        "idMarca" => 0
    ];
}

//  Definir o cabeçalho para indicar que a resposta é JSON e usa UTF-8
header('Content-Type: application/json; charset=utf-8');

//  Codificar a resposta para JSON. JSON_UNESCAPED_UNICODE é vital para preservar acentos e caracteres especiais.
echo json_encode($response, JSON_UNESCAPED_UNICODE);

//  Fechar a conexão com o banco de dados
$con->close();
/*
http://localhost/consultaProduto.php -- link post insomnia
não tem códio json só clicar em send
*/
?>