<?php

function smarty_function_stylesheet($params, &$smartest_engine){
    
    if(isset($params['file']) && strlen($params['file'])){
        
        $file = $params['file'];
        
        if(!$smartest_engine->getStylesheetIncluded($file)){
            
            $smartest_engine->setStylesheetIncluded($file);
            
            $a = new SmartestRenderableAsset;
            
            if($a->findBy('url', $file)){
                $a->setDraftMode($smartest_engine->getDraftMode());
                return $a->render();
            }else{
                if(is_file(SM_ROOT_DIR.'Public/Resources/Stylesheets/'.$file)){
                    $hash = substr(md5_file(SM_ROOT_DIR.'Public/Resources/Stylesheets/'.$file), 0, 8);
                }
                return '<link rel="stylesheet" href="'.SmartestPersistentObject::get('request_data')->g('domain').'Resources/Stylesheets/'.$file.'?hash='.$hash.'" />'."\n";
            }
            
        }
        
    }else{
        
        return $smartest_engine->raiseError('You must specify a stylesheet to include with the file="" parameter.');
        
    }
}