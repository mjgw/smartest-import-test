<?php

function smarty_function_license_select($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $input = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $input->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $input->setParameter('id', $params['id']);
        }else{
            $input->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            if($params['value'] instanceof SmartestLicense){
                $input->setParameter('value', $params['value']);
            }else{
                $input->setParameter('value', new SmartestLicense($params['value']));
            }
        }else{
            $input->setParameter('value', null);
        }
        
        if(isset($params['options']) && is_array($params['options'])){
            $input->setParameter('options', $params['options']);
        }else{
            $input->setParameter('options', SmartestLicense::getLicenseData());
        }
        
        $smartest_engine->assign('_input_data', $input);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/license.tpl', array());
        
    }else{
        
    }
    
}