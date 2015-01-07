<?php

function smarty_function_image_select($params, $smartest_engine){
    
    if(isset($params['name'])){
        
        $asset = new SmartestParameterHolder('Input Parameters: '.$params['name']);
        $asset->setParameter('name', $params['name']);
        
        if(isset($params['id'])){
            $asset->setParameter('id', $params['id']);
        }else{
            $asset->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        }
        
        if(isset($params['value'])){
            $asset->setParameter('value', $params['value']);
        }else{
            $asset->setParameter('value', null);
        }
        
        $asset->setParameter('options', array());
        
        if(isset($params['required'])){
            $asset->setParameter('required', SmartestStringHelper::toRealBool($params['required']));
        }else{
            $asset->setParameter('required', false);
        }
        
        if(isset($params['for'])){
            
            $asset->setParameter('for', strtolower($params['for']));
            
            if($params['for'] == 'ipv'){
                
                if(isset($params['property']) && $params['property'] instanceof SmartestItemProperty){
                    $asset->setParameter('property_id', $params['property']->getId());
                }elseif(isset($params['property_id']) && is_numeric($params['property_id'])){
                    $asset->setParameter('property_id', $params['property_id']);
                }else{
                    $asset->setParameter('property_id', null);
                }
                
                if(isset($params['item_id']) && is_numeric($params['item_id'])){
                    $asset->setParameter('item_id', $params['item_id']);
                }
                
            }
            
            if($params['for'] == 'placeholder'){
                
                if(isset($params['placeholder']) && $params['placeholder'] instanceof SmartestPlaceholder){
                    $asset->setParameter('placeholder_id', $params['placeholder']->getId());
                }elseif(isset($params['placeholder_id']) && is_numeric($params['placeholder_id'])){
                    $asset->setParameter('placeholder_id', $params['placeholder_id']);
                }else{
                    $asset->setParameter('placeholder_id', null);
                }
                
                if(isset($params['page_id']) && is_numeric($params['page_id'])){
                    $asset->setParameter('page_id', $params['page_id']);
                }
                
            }
            
            if($params['for'] == 'user_profile_pic'){
                if(isset($params['user']) && $params['user'] instanceof SmartestUser){
                    $asset->setParameter('user_id', $params['user']->getId());
                }elseif(isset($params['user_id']) && is_numeric($params['user_id'])){
                    $asset->setParameter('user_id', $params['user_id']);
                }else{
                    $asset->setParameter('user_id', null);
                }
            }
            
            if($params['for'] == 'page_icon'){
                if(isset($params['page']) && $params['page'] instanceof SmartestUser){
                    $asset->setParameter('page_id', $params['page']->getId());
                }elseif(isset($params['page_id']) && is_numeric($params['page_id'])){
                    $asset->setParameter('page_id', $params['page_id']);
                }else{
                    $asset->setParameter('page_id', null);
                }
            }
            
        }else{
            $asset->setParameter('for', '');
        }
        
        $smartest_engine->assign('_input_data', $asset);
        $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/image.tpl', $asset);
        
    }else{
        
    }
    
}