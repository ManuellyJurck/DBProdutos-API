<?php

// Configuração de Erros (útil durante o desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// --- Inclusão e Conexão com o Banco de Dados ---
require_once 'conexao.php'; // Certifique-se de que 'conexao.php' retorna a variável $con
$con->set_charset("utf8");

// --- Processamento da Requisição JSON ---

// Obtém o corpo da requisição e decodifica o JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes na requisição.']);
    exit;
}

// Extrai, trata e valida os dados para a tabela 'fornecedor'
$Nome          = trim($jsonParam['Nome'] ?? '');
$Cnpj          = trim($jsonParam['Cnpj'] ?? '');
$nmResponsavel = trim($jsonParam['nmResponsavel'] ?? '');
$nrContato     = trim($jsonParam['nrContato'] ?? '');
// flListaNegra deve ser 'S' ou 'N' e tem 1 caractere (CHAR)
$flListaNegra  = strtoupper(trim($jsonParam['flListaNegra'] ?? 'N'));

// --- Preparação e Execução da Consulta ---

// Prepara a consulta SQL para a tabela `fornecedor`
$stmt = $con->prepare("
    INSERT INTO fornecedor (Nome, Cnpj, nmResponsavel, nrContato, flListaNegra)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

// O tipo de dados é: s=string, s=string, s=string, s=string, s=string/char
// Observação: 'flListaNegra' é CHAR(1), mas usamos 's' (string) no bind.
$stmt->bind_param("sssss", $Nome, $Cnpj, $nmResponsavel, $nrContato, $flListaNegra);

// Executa a consulta
if ($stmt->execute()) {
    $idInserido = $stmt->insert_id; // Pega o ID gerado automaticamente
    echo json_encode([
        'success' => true,
        'message' => 'Fornecedor inserido com sucesso!',
        'idFornecedor' => $idInserido
    ]);
} else {
    // Retorna o erro específico do banco de dados
    echo json_encode(['success' => false, 'message' => 'Erro no registro do fornecedor: ' . $stmt->error]);
}

// --- Fechamento da Conexão ---
$stmt->close();
$con->close();

/*
 * Cadastrar novos fornecedores comando insomnia web
 {
    "Nome": "Tech Suprimentos S.A.",
    "Cnpj": "00.000.000/0001-00",
    "nmResponsavel": "João da Silva",
    "nrContato": "(11) 98765-4321",
    "flListaNegra": "N"
}
*/
?>