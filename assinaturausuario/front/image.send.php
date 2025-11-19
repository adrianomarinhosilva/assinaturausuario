<?php
/**
 * Endpoint personalizado para servir imagens de assinatura
 */

include ('../../../inc/includes.php');

// Verificar se usuário está logado
Session::checkLoginUser();

if (!isset($_GET['docid']) || !is_numeric($_GET['docid'])) {
    http_response_code(404);
    echo "Document ID not provided or invalid";
    exit;
}

$doc_id = (int) $_GET['docid'];

// Carregar documento
$doc = new Document();
if (!$doc->getFromDB($doc_id)) {
    http_response_code(404);
    echo "Document not found: " . $doc_id;
    exit;
}

// Log para debug
error_log("Image.send.php: Tentando servir documento ID: $doc_id");
error_log("Document data: " . print_r($doc->fields, true));

// Verificar se é uma imagem de assinatura (verificar pela tag ou comentário)
$is_signature_image = false;
if (strpos($doc->fields['tag'], 'signature_') === 0 || 
    strpos($doc->fields['comment'], 'assinatura') !== false ||
    strpos($doc->fields['name'], 'signature_image_') === 0) {
    $is_signature_image = true;
}

// Para imagens de assinatura, permitir acesso mais amplo
if (!$is_signature_image) {
    // Se não é imagem de assinatura, usar verificações padrão
    if (!$doc->canViewFile()) {
        http_response_code(403);
        echo "Access denied to document";
        exit;
    }
}

// Verificar se é uma imagem
if (strpos($doc->fields['mime'], 'image/') !== 0) {
    http_response_code(400);
    echo "Document is not an image";
    exit;
}

// Construir caminho do arquivo
$file_path = GLPI_DOC_DIR . "/" . $doc->fields['filepath'];

error_log("Image.send.php: Caminho do arquivo: $file_path");

if (!file_exists($file_path)) {
    http_response_code(404);
    echo "File not found on disk: " . $file_path;
    exit;
}

// Servir a imagem
header('Content-Type: ' . $doc->fields['mime']);
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: public, max-age=31536000'); // Cache por 1 ano
header('Content-Disposition: inline; filename="' . $doc->fields['filename'] . '"');

readfile($file_path);
exit;