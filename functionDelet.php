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

// Extrai e valida o ID do fornecedor
$idFornecedor = intval($jsonParam['idFornecedor'] ?? 0);

if ($idFornecedor <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID do fornecedor inválido ou ausente.']);
    exit;
}

// --- Preparação e Execução da Consulta DELETE ---

// Prepara a consulta SQL para deletar um registro
$stmt = $con->prepare("DELETE FROM fornecedor WHERE idFornecedor = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

// Vincula o parâmetro: 'i' = integer (inteiro)
$stmt->bind_param("i", $idFornecedor);

// Executa a consulta
if ($stmt->execute()) {
    // Verifica se alguma linha foi realmente afetada (deletada)
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Fornecedor com ID {$idFornecedor} deletado com sucesso!"
        ]);
    } else {
        // Nenhuma linha afetada, provavelmente o ID não existe
        echo json_encode([
            'success' => false,
            'message' => "Nenhum fornecedor encontrado com o ID {$idFornecedor}."
        ]);
    }
} else {
    // Retorna o erro específico do banco de dados
    echo json_encode(['success' => false, 'message' => 'Erro ao deletar o fornecedor: ' . $stmt->error]);
}

// --- Fechamento da Conexão ---
$stmt->close();
$con->close();
/*
* Substitua '5' pelo ID que você deseja apagar - comando insomnia de delete
{
    "idFornecedor": 5 
}
*/
?>