<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type
header('Content-Type: application/json');

// Inclua a conexão compartilhada com o BD
// Certifique-se de que 'conexao.php' inicializa a variável $con
require_once 'conexao.php';
$con->set_charset("utf8");

// Obtém a entrada JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// Extrai e valida os dados para a tabela 'marca'
// Assumindo que o campo 'Logo' virá codificado em base64 no JSON
$nmMarca      = trim($jsonParam['nmMarca'] ?? '');
$LogoBase64   = $jsonParam['Logo'] ?? ''; // Mantemos como está (base64) para decodificação
$Qualidade    = trim($jsonParam['Qualidade'] ?? '');
$idFornecedor = intval($jsonParam['idFornecedor'] ?? 0);

// --- Validações Adicionais ---
if (empty($nmMarca) || empty($Qualidade) || $idFornecedor <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados obrigatórios (nmMarca, Qualidade, idFornecedor) estão faltando ou são inválidos.']);
    exit;
}

// Decodifica a string base64 para o formato binário (BLOB)
// Se não houver logo ou a decodificação falhar, define como null ou uma string vazia
$Logo = null;
if (!empty($LogoBase64)) {
    // Tenta remover cabeçalhos comuns de base64 (ex: "data:image/png;base64,")
    $data = explode(',', $LogoBase64);
    $encodedData = count($data) > 1 ? $data[1] : $data[0];
    $Logo = base64_decode($encodedData, true);
    
    // Verifica se a decodificação foi bem-sucedida
    if ($Logo === false) {
        // Tratar erro ou continuar dependendo da sua necessidade
        $Logo = ''; // Define como string vazia se a decodificação falhar
    }
} else {
    // Se o campo for NOT NULL na DB, você deve fornecer um valor ou garantir que o MySQL aceite uma string vazia
    $Logo = ''; 
}


// Prepara a consulta
$stmt = $con->prepare("
    INSERT INTO marca (nmMarca, Logo, Qualidade, idFornecedor)
    VALUES (?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

// O tipo 'b' é para BLOBs (pacotes de dados binários), 's' para string e 'i' para integer
$stmt->bind_param("sssi", $nmMarca, $Logo, $Qualidade, $idFornecedor);


// Executa e retorna o resultado
if ($stmt->execute()) {
    // Retorna o ID da marca recém-inserida
    $idMarca = $con->insert_id;
    echo json_encode(['success' => true, 'message' => 'Marca inserida com sucesso!', 'idMarca' => $idMarca]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no registro da marca: ' . $stmt->error]);
}

$stmt->close();
$con->close();
/*
{
    "nmMarca": "Exemplo Roupas Ltda",
    "Logo": "iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdzFjAAAABHNCS",
    "Qualidade": "Premium",
    "idFornecedor": 5    //-- precisa ser um id existente
}
http://localhost/cadMarca.php -- link do arquivo no insomnia
*/
?>

