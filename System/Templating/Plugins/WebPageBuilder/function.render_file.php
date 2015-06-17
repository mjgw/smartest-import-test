<?php

function smarty_function_render_file($params, &$smartest_engine){
    
    $edit_button_in_draft = (isset($params['editbutton']) && !SmartestStringHelper::toRealBool($params['editbutton'])) ? false : true;
    
    if(isset($params['asset']) && ($params['asset'] instanceof SmartestRenderableAsset)){
    
        // $smartest_engine->_renderAssetObject($params['object'], $params);
        $asset = $params['asset'];
        
        foreach($params as $key=>$value){
            if($key != 'id' && $key != 'asset'){
                $asset->setSingleAdditionalRenderDataParameter($key, $value);
            }
        }
        
        return $asset->render($smartest_engine->getDraftMode(), $edit_button_in_draft);
    
    }elseif(isset($params['id']) && is_numeric($params['id'])){
        
        $asset = new SmartestRenderableAsset;
        
        if($asset->find($params['id'])){
            
            foreach($params as $key=>$value){
                if($key != 'id' && $key != 'asset'){
                    $asset->setSingleAdditionalRenderDataParameter($key, $value);
                }
            }
            
            return $asset->render($smartest_engine->getDraftMode(), $edit_button_in_draft);
            
        }else{
            return $smartest_engine->raiseError('&lt;?sm:render_file:?&gt; must be provided with a SmartestRenderableAsset object or valid asset ID. Unknown asset ID given.');
        }
    
    }elseif(isset($params['name'])){
        
        $asset = new SmartestRenderableAsset;
        
        if($asset->findBy('stringid', $params['name'])){
            
            foreach($params as $key=>$value){
                if($key != 'name' && $key != 'asset'){
                    $asset->setSingleAdditionalRenderDataParameter($key, $value);
                }
            }
            
            return $asset->render($smartest_engine->getDraftMode());
            
        }else{
            return $smartest_engine->raiseError('Unknown asset name given.');
        }
    
    }else{
        
        $type = gettype($params['object']);
        
        if($type == 'object'){
            $type = get_class($params['object']).' Object';
        }
        
        return $smartest_engine->raiseError('&lt;?sm:render_file:?&gt; must be provided with a SmartestRenderableAsset object. Object of class '.$type.' given.');
      
    }
    
}