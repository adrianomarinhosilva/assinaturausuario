<?php
/**
 * Plugin Assinatura Usuario - Hooks
 */

/**
 * Plugin install process
 */
function plugin_assinaturausuario_install() {
    global $DB;
    
    // Criar tabela para armazenar assinaturas
    $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_assinaturausuario_signatures` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `users_id` INT(11) NOT NULL DEFAULT '0',
        `signature` LONGTEXT DEFAULT NULL,
        `date_creation` TIMESTAMP NULL DEFAULT NULL,
        `date_mod` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `users_id` (`users_id`),
        KEY `date_creation` (`date_creation`),
        KEY `date_mod` (`date_mod`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $DB->query($query) or die("Erro ao criar tabela: " . $DB->error());
    
    return true;
}

/**
 * Plugin uninstall process
 */
function plugin_assinaturausuario_uninstall() {
    global $DB;
    
    // Remove table
    $query = "DROP TABLE IF EXISTS `glpi_plugin_assinaturausuario_signatures`";
    $DB->query($query);
    
    return true;
}

/**
 * Função chamada antes de adicionar um followup
 */
function plugin_assinaturausuario_pre_item_add(CommonDBTM $item) {
    if (isset($item->input['content']) && isset($_SESSION['glpiID'])) {
        $signature = PluginAssinaturausuarioSignature::getUserSignature($_SESSION['glpiID']);
        if (!empty($signature)) {
            // Verificar se a assinatura já não está presente no conteúdo
            if (strpos($item->input['content'], $signature) === false) {
                $item->input['content'] = $item->input['content'] . "\n\n---\n" . $signature;
            }
        }
    }
}

/**
 * Função chamada antes de atualizar um followup
 */
function plugin_assinaturausuario_pre_item_update(CommonDBTM $item) {
    if (isset($item->input['content']) && isset($_SESSION['glpiID'])) {
        $signature = PluginAssinaturausuarioSignature::getUserSignature($_SESSION['glpiID']);
        if (!empty($signature)) {
            // Verificar se a assinatura já não está presente no conteúdo
            if (strpos($item->input['content'], $signature) === false) {
                $item->input['content'] = $item->input['content'] . "\n\n---\n" . $signature;
            }
        }
    }
}