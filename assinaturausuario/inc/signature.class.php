<?php
/**
 * Classe para gerenciar assinaturas de usuários
 */

class PluginAssinaturausuarioSignature extends CommonDBTM {
    
    static protected $forward_entity_to = ['User'];
    
    static function getTypeName($nb = 0) {
        return _n('Assinatura', 'Assinaturas', $nb);
    }
    
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        return '';
    }
    
    /**
     * Obtém a assinatura de um usuário
     */
    static function getUserSignature($users_id) {
        global $DB;
        
        $iterator = $DB->request([
            'FROM'  => 'glpi_plugin_assinaturausuario_signatures',
            'WHERE' => ['users_id' => $users_id]
        ]);
        
        if (count($iterator)) {
            $row = $iterator->current();
            return $row['signature'];
        }
        
        return '';
    }
    
    /**
     * Salva a assinatura de um usuário
     */
    static function saveUserSignature($users_id, $signature) {
        global $DB;
        
        error_log("Plugin Assinatura: Salvando para usuário $users_id");
        
        // Verificar se já existe uma assinatura para este usuário
        $iterator = $DB->request([
            'FROM'  => 'glpi_plugin_assinaturausuario_signatures',
            'WHERE' => ['users_id' => $users_id]
        ]);
        
        $now = date('Y-m-d H:i:s');
        
        if (count($iterator)) {
            error_log("Plugin Assinatura: Atualizando assinatura existente");
            $result = $DB->update(
                'glpi_plugin_assinaturausuario_signatures',
                [
                    'signature' => $signature,
                    'date_mod'  => $now
                ],
                ['users_id' => $users_id]
            );
        } else {
            error_log("Plugin Assinatura: Criando nova assinatura");
            $result = $DB->insert(
                'glpi_plugin_assinaturausuario_signatures',
                [
                    'users_id'      => $users_id,
                    'signature'     => $signature,
                    'date_creation' => $now,
                    'date_mod'      => $now
                ]
            );
        }
        
        return $result;
    }
    
    // Métodos de permissão padrão
    function canViewItem() {
        return true;
    }
    
    function canCreateItem() {
        return true;
    }
    
    function canUpdateItem() {
        return true;
    }
    
    function canDeleteItem() {
        return true;
    }
    
    function canPurgeItem() {
        return true;
    }
    
    static function canCreate() {
        return true;
    }
    
    static function canView() {
        return true;
    }
    
    static function canUpdate() {
        return true;
    }
    
    static function canDelete() {
        return true;
    }
    
    static function canPurge() {
        return true;
    }
}