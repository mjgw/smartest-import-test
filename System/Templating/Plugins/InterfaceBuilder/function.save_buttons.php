<?php

function smarty_function_save_buttons($params, &$smartest_engine){
    
    $vars = array();
    
    if(SmartestSession::hasData("form:return:temp_description")){
        $description = SmartestSession::get("form:return:temp_description");
    }else{
        $description = SmartestSession::get("form:return:description");
    }
    
    $global_ui_filename = SM_ROOT_DIR.'System/Languages/SystemLocalizations/'.SmartestSession::get('user')->getPreferredUiLanguage().'/global.yml';
    $global_ui_filename_exists = is_file($global_ui_filename);

    if($global_ui_filename_exists){
        $global_ui_strings = SmartestYamlHelper::fastLoad($global_ui_filename);
    }else{
        $global_ui_strings = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Languages/global.yml');
    }
    
    $global_ui_strings = $global_ui_strings['strings'];
    
    $vars['_continue_message'] = isset($params['continue_message']) ? $params['continue_message'] : $global_ui_strings['system_wide_buttons']['save'];
    $vars['_quit_message'] = isset($params['quit_message']) ? $params['quit_message'] : $global_ui_strings['system_wide_buttons']['save_and_return']." ".$description;
    $vars['_cancel_message'] = isset($params['cancel_message']) ? $params['cancel_message'] : $global_ui_strings['system_wide_buttons']['cancel'];
    
    if(isset($params['publish_action'])){
        $vars['_publish_message'] = isset($params['publish_message']) ? $params['publish_message'] : $global_ui_strings['system_wide_buttons']['save_and_publish'];
        $vars['_publish_action'] = $params['publish_action'];
    }
    
    $smartest_engine->_smarty_include(array('smarty_include_tpl_file'=>SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/editform_buttons.tpl', 'smarty_include_vars'=>$vars));
    
}