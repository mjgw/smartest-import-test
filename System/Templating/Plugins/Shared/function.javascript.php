<?php

function smarty_function_javascript($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getScriptIncluded($file)){
            
            $smartest_engine->setScriptIncluded($file);
            
            if(substr($file, 0, 4) == 'http'){
                return '<script type="text/javascript" src="'.$file.'"></script>';
            }else{
                if((isset($params['system']) && SmartestStringHelper::toRealBool($params['system'])) || substr($file, 0, 13) == 'Resources/Sys' || substr($file, 0, 13) == 'System/Javasc'){
                    return '<script type="text/javascript" src="'.$smartest_engine->getRequestData()->getParameter('domain').'Resources/System/Javascript/'.substr($file, 18).'"></script>';
                }else{
                    if(substr($file, 0, 13) == 'Resources/Jav'){
                        $file = substr($file, 21);
                    }
                    // Sergiy: +/Javascript
                    
                    if(is_file(SM_ROOT_DIR.'Public/Resources/Javascript/'.$file)){
                        $query_string_nonce = '?nonce='.substr(md5_file(SM_ROOT_DIR.'Public/Resources/Javascript/'.$file), 0, 8);
                    }else{
                        $query_string_nonce = '';
                    }
                    
                    return '<script type="text/javascript" src="'.$smartest_engine->getRequestData()->getParameter('domain').'Resources/Javascript/'.$file.$query_string_nonce.'"></script>';
                }
            }
        }
    }
}