<?php
/**
 * Plugin Assinatura Usuario
 */

define('PLUGIN_ASSINATURAUSUARIO_VERSION', '1.0.0');

/**
 * Init hooks of the plugins - Needed
 */
function plugin_init_assinaturausuario() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['assinaturausuario'] = true;
    
    if (Plugin::isPluginActive('assinaturausuario')) {
        // Register classes
        Plugin::registerClass('PluginAssinaturausuarioSignature');
        Plugin::registerClass('PluginAssinaturausuarioPreference', [
            'addtabon' => ['Preference']
        ]);

        // Hook para interceptar ANTES da inserção/atualização
        $PLUGIN_HOOKS['pre_item_add']['assinaturausuario'] = [
            'ITILFollowup' => 'plugin_assinaturausuario_pre_item_add'
        ];

        $PLUGIN_HOOKS['pre_item_update']['assinaturausuario'] = [
            'ITILFollowup' => 'plugin_assinaturausuario_pre_item_update'
        ];
    }
}

/**
 * Get the name and the version of the plugin
 */
function plugin_version_assinaturausuario() {
    return [
        'name'           => 'Assinatura de Usuário',
        'version'        => PLUGIN_ASSINATURAUSUARIO_VERSION,
        'author'         => 'Seu Nome',
        'license'        => 'GPLv2+',
        'homepage'       => '',
        'requirements'   => [
            'glpi' => [
                'min' => '10.0',
                'dev' => false
            ]
        ]
    ];
}

/**
 * Check configuration process
 */
function plugin_assinaturausuario_check_config() {
    return true;
}

/**
 * Check if prerequisites are satisfied
 */
function plugin_assinaturausuario_check_prerequisites() {
    return true;
}