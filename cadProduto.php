<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o tipo de conteúdo como JSON para a resposta
header('Content-Type: application/json');

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php';
// Define o charset para UTF-8
$con->set_charset("utf8");

// Obtém o input JSON da requisição
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// --- Extração e Validação dos Dados para a Tabela 'produto' ---

// Assume-se que todos os campos string (varchar) no seu DDL são não-nulos.
// Os campos são: nmProduto, nrQuantidade, nrIdentificacao, Valor.
$nmProduto       = trim($jsonParam['nmProduto'] ?? '');
$nrQuantidade    = trim($jsonParam['nrQuantidade'] ?? '');
$nrIdentificacao = trim($jsonParam['nrIdentificacao'] ?? '');
$Valor           = trim($jsonParam['Valor'] ?? '');
// O campo Disponivel é CHAR(1). 
// Pode-se assumir 'S' ou 'N' ou você pode ajustar a lógica se houver um padrão específico.
$Disponivel      = strtoupper(trim($jsonParam['Disponivel'] ?? 'N')); // Padrão 'N'
// O campo idMarca é INT, chave estrangeira.
$idMarca         = intval($jsonParam['idMarca'] ?? 0);

// Uma validação básica para garantir que campos obrigatórios não estejam vazios
if (empty($nmProduto) || empty($nrQuantidade) || empty($nrIdentificacao) || empty($Valor) || $idMarca <= 0) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios (nmProduto, nrQuantidade, nrIdentificacao, Valor, idMarca) não podem estar vazios.']);
    exit;
}

// --- Preparação da Consulta INSERT ---

$stmt = $con->prepare("
    INSERT INTO produto (nmProduto, nrQuantidade, nrIdentificacao, Valor, Disponivel, idMarca)
    VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

// Tipos de dados para bind_param:
// s: string (nmProduto, nrQuantidade, nrIdentificacao, Valor, Disponivel)
// i: integer (idMarca)
$stmt->bind_param("sssssi", $nmProduto, $nrQuantidade, $nrIdentificacao, $Valor, $Disponivel, $idMarca);

// --- Execução da Consulta e Retorno do Resultado ---

if ($stmt->execute()) {
    // Retorna o ID do produto inserido (opcional)
    $idProduto = $con->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Produto inserido com sucesso!',
        'idProduto' => $idProduto
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no registro do produto: ' . $stmt->error]);
}

// --- Fechamento da Conexão ---

$stmt->close();
$con->close();
/*
{
    "nmProduto": "Smart TV 55 polegadas 4K",
    "nrQuantidade": "50",
    "nrIdentificacao": "STV-55K4-ABC",
    "Valor": "3500.00",
    "Disponivel": "S",
    "idMarca": 2
}

http://localhost/cadProduto.php --- Linik Insomnia

*/
?>