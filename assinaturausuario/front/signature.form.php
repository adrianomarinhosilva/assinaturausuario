<?php
/**
 * Processamento do formulário de assinatura
 */

include ('../../../inc/includes.php');

Session::checkLoginUser();

if (isset($_POST['update'])) {
    $users_id = (int) $_POST['users_id'];
    
    // Verificar se o usuário pode alterar sua própria assinatura
    if ($users_id != $_SESSION['glpiID']) {
        Session::addMessageAfterRedirect(
            'Você só pode alterar sua própria assinatura.', 
            false, 
            ERROR
        );
        Html::back();
        exit;
    }
    
    $signature = $_POST['signature'] ?? '';
    
    $result = PluginAssinaturausuarioSignature::saveUserSignature($users_id, $signature);
    
    if ($result) {
        Session::addMessageAfterRedirect(
            'Assinatura salva com sucesso!', 
            false, 
            INFO
        );
    } else {
        Session::addMessageAfterRedirect(
            'Erro ao salvar assinatura.', 
            false, 
            ERROR
        );
    }
}

Html::back();