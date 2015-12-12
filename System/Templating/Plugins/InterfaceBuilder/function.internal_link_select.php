<?php

function smarty_function_internal_link_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        $name = $params['name'];
    }else{
        $name = 'unnamed_smartest_internal_link_input_'.SmartestStringHelper::random(6);
    }
    
    $_input_data = new SmartestParameterHolder('Input Parameters: '.$name);
    $_input_data->setParameter('name', $name);
    
    if(isset($params['id'])){
        $_input_data->setParameter('id', $params['id']);
    }else{
        $_input_data->setParameter('id', SmartestStringHelper::toSlug($name));
    }
    
    if(isset($params['value'])){
        $_input_data->setParameter('value', $params['value']);
    }else{
        $_input_data->setParameter('value', array());
    }
    
    if(isset($params['required'])){
        $_input_data->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
    }else{
        $_input_data->setParameter('required', false);
    }
    
    $smartest_engine->assign('_input_data', $_input_data);
    $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/internal_link.tpl', $_input_data);
    
}