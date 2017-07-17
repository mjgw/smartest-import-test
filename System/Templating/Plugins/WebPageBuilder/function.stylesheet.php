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
                return '<link rel="stylesheet" href="'.SmartestPersistentObject::get('request_data')->g('domain').'Resources/Stylesheets/'.$file.'" />'."\n";
            }
            
        }
        
    }else{
        
        return $smartest_engine->raiseError('You must specify a stylesheet to include with the file="" parameter.');
        
    }
}