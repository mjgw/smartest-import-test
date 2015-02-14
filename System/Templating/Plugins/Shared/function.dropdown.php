<?php

function smarty_function_dropdown($params, $smartest_engine){
    
    $d = new SmartestDropdown;
    
    if(isset($params['id']) && $d->find($params['id'])){
        
    }elseif(isset($params['name']) && $d->findBy('name', SmartestStringHelper::toVarName($params['name']))){
        
    }else{
        return false;
    }
    
    $render_data = new SmartestParameterHolder('Dropdown menu options');
    
    if(isset($params['form_name'])){
        $render_data->setParameter('name', $params['form_name']);
    }else{
        $render_data->setParameter('name', $d->getName());
    }
    
    if(isset($params['allow_blank'])){
        $render_data->setParameter('allow_blank', true);
    }else{
        $render_data->setParameter('allow_blank', false);
    }
    
    if(isset($params['form_id'])){
        $render_data->setParameter('id', $params['form_id']);
        $render_data->setParameter('use_id', true);
    }else{
        $render_data->setParameter('use_id', false);
    }
    
    if(isset($params['value'])){
        $render_data->setParameter('selected_value', $params['value']);
    }else{
        $render_data->setParameter('selected_value', null);
    }
    
    $render_data->setParameter('options', $d->getOptions());
    
    $smartest_engine->assign('render_data', $render_data);
    $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/WebPageBuilder/dropdown_menu_generic.tpl', array());
    
}