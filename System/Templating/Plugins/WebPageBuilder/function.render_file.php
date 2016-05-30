<?php

function smarty_function_render_file($params, &$smartest_engine){
    
    $edit_button_in_draft = (isset($params['editbutton']) && !SmartestStringHelper::toRealBool($params['editbutton'])) ? false : true;
    
    if(isset($params['asset']) && ($params['asset'] instanceof SmartestRenderableAsset)){
        
        // $smartest_engine->_renderAssetObject($params['object'], $params);
        $asset = $params['asset'];
        $type_info = $asset->getTypeInfo();
        
        foreach($params as $key=>$value){
            if($key != 'id' && $key != 'asset'){
                $asset->setSingleAdditionalRenderDataParameter($key, $value);
            }
        }
        
        if(isset($params['manual_width']) && is_numeric($params['manual_width']) && $type_info['storage']['type'] == 'external_translated'){
            
            $markup = $asset->render($smartest_engine->getDraftMode(), $edit_button_in_draft);
            
            // $markup = html_entity_decode($markup);
            if(SmartestStringHelper::containsEscapedEntities($markup)){
                $markup = html_entity_decode($markup);
            }
            
            if($asset->getType() == 'SM_ASSETTYPE_OEMBED_URL' && $params['manual_width'] > 0){
                
                if(preg_match('/width="(\d+)"/', $markup, $matches)){
                    $original_width = $matches[1];
                    $new_markup = preg_replace('/width="(\d+)"/', 'width="'.$params['manual_width'].'"', $markup);
                    if(preg_match('/height="(\d+)"/', $markup, $hmatches)){
                        $original_height = $hmatches[1];
                        $new_height = ceil($params['manual_width']/$original_width*$original_height);
                        $new_markup = preg_replace('/height="(\d+)"/', 'height="'.$new_height.'"', $new_markup);
                    }
                    return $new_markup;
                }else{
                    // return $markup;
                }
            }else{
                // return $markup;
            }
        }else{
            $markup = $asset->render($smartest_engine->getDraftMode(), $edit_button_in_draft);
        }
        
        return $markup;
    
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