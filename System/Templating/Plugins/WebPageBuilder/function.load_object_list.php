<?php

function smarty_function_load_object_list($params, &$smartest_engine){
    
	if(is_string($params['from'])){
	    if($params['from'] == '_authors'){
            $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "authors";
        }else if(substr($params['from'], 0, 6) == 'pagegr' || substr($params['from'], 0, 6) == 'page_g'){
            $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "pages_list";
        }else if(substr($params['from'], 0, 7) == 'gallery'){
            $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "gallery_list";
        }else{
	        $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "items_list";
        }
    }else{
        if($params['from'] instanceof SmartestPageGroup){
            $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "pages_list";
        }else if($params['from'] instanceof SmartestAssetGroup){
            $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "gallery_list";
        }else{
	        $assign_name = (isset($params['assign']) && strlen($params['assign'])) ? SmartestStringHelper::toVarName($params['assign']) : "items_list";
        }
    }
	
	$limit = (isset($params['limit']) && is_numeric($params['limit'])) ? $params['limit'] : 0;
    
	$items = $smartest_engine->getRepeatBlockData($params);
	
	if($items instanceof SmartestArray){
	    $items = $items->getValue();
	}
	
	if($items instanceof SmartestAssetGroup){
	    $items = $items->getMemberships();
	}
	
	if($items instanceof SmartestCmsItemSet){
	    $items = $items->getMembers();
	}
    
	if($limit > 0){
		$items = array_slice($items, 0, $limit);
	}
    
    $items = array_values($items);
    
    $smartest_engine->assign($assign_name, $items);
    
}