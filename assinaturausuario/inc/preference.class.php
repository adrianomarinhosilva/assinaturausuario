<?php
/**
 * Classe para adicionar aba nas preferências do usuário
 */

class PluginAssinaturausuarioPreference extends CommonGLPI {
    
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        if (!$withtemplate) {
            switch ($item->getType()) {
                case 'Preference':
                    return 'Assinatura de Usuário';
            }
        }
        return '';
    }
    
    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        switch ($item->getType()) {
            case 'Preference':
                self::showSignatureTab();
                break;
        }
        return true;
    }
    
    /**
     * Mostra a aba de assinatura nas preferências
     */
    static function showSignatureTab() {
        global $CFG_GLPI;
        
        $users_id = $_SESSION['glpiID'];
        $signature = PluginAssinaturausuarioSignature::getUserSignature($users_id);
        
        echo "<div class='center spaced'>";
        echo "<form method='post' action='" . $CFG_GLPI['root_doc'] . "/plugins/assinaturausuario/front/signature.form.php' enctype='multipart/form-data'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr class='tab_bg_1'>";
        echo "<th colspan='2'>Configurar Assinatura de Usuário</th>";
        echo "</tr>";
        
        echo "<tr class='tab_bg_1'>";
        echo "<td colspan='2'>";
        
        echo "<div class='form-field row'>";
        echo "<label class='col-form-label col-xxl-2 text-xxl-end'>Assinatura:</label>";
        echo "<div class='col-xxl-10 field-container'>";
        
        // Usar sistema nativo do GLPI
        Html::textarea([
            'name'              => 'signature',
            'value'             => $signature,
            'rows'              => 15,
            'cols'              => 80,
            'enable_richtext'   => true,
            'enable_fileupload' => true,
            'enable_images'     => true,
            'uploads'           => [
                '_prefix'    => '_signature',
                '_itemtype'  => 'User',
                '_items_id'  => $users_id
            ]
        ]);
        
        echo "</div>";
        echo "</div>";
        
        echo "</td>";
        echo "</tr>";
        
        echo "<tr class='tab_bg_2'>";
        echo "<td class='center' colspan='2' style='padding: 15px;'>";
        echo Html::hidden('users_id', ['value' => $users_id]);
        echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);
        echo "<input type='submit' name='update' value='Salvar Assinatura' class='btn btn-primary'>";
        echo "</td>";
        echo "</tr>";
        
        echo "</table>";
        Html::closeForm();
        echo "</div>";
        
        // JavaScript para forçar carregamento correto SEMPRE
        echo "<script type='text/javascript'>";
        echo "$(document).ready(function() {";
        echo "    // Aguardar o GLPI terminar de carregar o TinyMCE";
        echo "    setTimeout(function() {";
        echo "        fixSignatureEditor();";
        echo "    }, 2000);";
        echo "    ";
        echo "    function fixSignatureEditor() {";
        echo "        if (typeof tinymce === 'undefined') {";
        echo "            setTimeout(fixSignatureEditor, 1000);";
        echo "            return;";
        echo "        }";
        echo "        ";
        echo "        // Encontrar o editor de assinatura";
        echo "        var signatureEditor = null;";
        echo "        tinymce.editors.forEach(function(editor) {";
        echo "            if (editor.getElement() && editor.getElement().name === 'signature') {";
        echo "                signatureEditor = editor;";
        echo "            }";
        echo "        });";
        echo "        ";
        echo "        if (!signatureEditor) {";
        echo "            // Se não encontrou, tentar novamente";
        echo "            setTimeout(fixSignatureEditor, 1000);";
        echo "            return;";
        echo "        }";
        echo "        ";
        echo "        // Verificar se tem toolbar";
        echo "        var editorContainer = signatureEditor.getContainer();";
        echo "        var hasToolbar = editorContainer && (";
        echo "            editorContainer.querySelector('.tox-toolbar') || ";
        echo "            editorContainer.querySelector('.mce-toolbar')";
        echo "        );";
        echo "        ";
        echo "        if (!hasToolbar) {";
        echo "            console.log('Toolbar não encontrada, recriando editor...');";
        echo "            ";
        echo "            // Salvar conteúdo atual";
        echo "            var currentContent = signatureEditor.getContent();";
        echo "            var targetElement = signatureEditor.getElement();";
        echo "            ";
        echo "            // Destruir editor atual";
        echo "            signatureEditor.destroy();";
        echo "            ";
        echo "            // Recriar com configuração completa";
        echo "            setTimeout(function() {";
        echo "                tinymce.init({";
        echo "                    target: targetElement,";
        echo "                    height: 300,";
        echo "                    menubar: false,";
        echo "                    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern imagetools',";
        echo "                    toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | image media table | code fullscreen',";
        echo "                    paste_data_images: true,";
        echo "                    automatic_uploads: true,";
        echo "                    images_upload_url: '" . $CFG_GLPI['root_doc'] . "/front/document.send.php',";
        echo "                    convert_urls: false,";
        echo "                    relative_urls: false,";
        echo "                    remove_script_host: false,";
        echo "                    image_advtab: true,";
        echo "                    branding: false,";
        echo "                    statusbar: false,";
        echo "                    setup: function(editor) {";
        echo "                        editor.on('init', function() {";
        echo "                            if (currentContent) {";
        echo "                                editor.setContent(currentContent);";
        echo "                            }";
        echo "                            console.log('Editor de assinatura recriado com sucesso');";
        echo "                        });";
        echo "                    }";
        echo "                });";
        echo "            }, 500);";
        echo "        } else {";
        echo "            console.log('Editor de assinatura carregado corretamente');";
        echo "            ";
        echo "            // Configurar para lidar melhor com imagens";
        echo "            signatureEditor.settings.paste_data_images = true;";
        echo "            signatureEditor.settings.automatic_uploads = true;";
        echo "            signatureEditor.settings.convert_urls = false;";
        echo "        }";
        echo "    }";
        echo "    ";
        echo "    // Verificação adicional após 10 segundos";
        echo "    setTimeout(function() {";
        echo "        var signatureEditor = null;";
        echo "        if (typeof tinymce !== 'undefined') {";
        echo "            tinymce.editors.forEach(function(editor) {";
        echo "                if (editor.getElement() && editor.getElement().name === 'signature') {";
        echo "                    signatureEditor = editor;";
        echo "                }";
        echo "            });";
        echo "        }";
        echo "        ";
        echo "        if (!signatureEditor) {";
        echo "            console.log('Última tentativa: Inicializando editor manualmente');";
        echo "            var textarea = $('textarea[name=\"signature\"]')[0];";
        echo "            if (textarea && typeof tinymce !== 'undefined') {";
        echo "                tinymce.init({";
        echo "                    target: textarea,";
        echo "                    height: 300,";
        echo "                    menubar: false,";
        echo "                    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern imagetools',";
        echo "                    toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | image media table | code fullscreen',";
        echo "                    paste_data_images: true,";
        echo "                    automatic_uploads: true,";
        echo "                    images_upload_url: '" . $CFG_GLPI['root_doc'] . "/front/document.send.php',";
        echo "                    convert_urls: false,";
        echo "                    relative_urls: false,";
        echo "                    branding: false,";
        echo "                    statusbar: false";
        echo "                });";
        echo "            }";
        echo "        }";
        echo "    }, 10000);";
        echo "});";
        echo "</script>";
        
        return true;
    }
}