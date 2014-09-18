<?php

function smarty_function_group_sets($params, &$smartest_engine){
    
    if(isset($params['sets'])){
        
        $set_names = explode(',', $params['sets']);
        $sets = array();
        
        foreach($set_names as $set_name){
            
            $s = new SmartestCmsItemSet;
            
            if($s->findBy('name', trim($set_name))){
                
                if($smartest_engine->getDraftMode()){
                    $s->setRetrieveMode(SM_QUERY_ALL_DRAFT_CURRENT);
                }else{
                    $s->setRetrieveMode(SM_QUERY_PUBLIC_LIVE_CURRENT);
                }
                
                $sets[] = $s;
            }
            
        }
        
        if(isset($params['randomize']) && SmartestStringHelper::toRealBool($params['randomize'])){
            shuffle($sets);
        }
        
        if(isset($params['limit'])){
            $sets = array_slice($sets, 0, $params['limit']);
        }
        
        if(isset($params['assign'])){
            $smartest_engine->assign(SmartestStringHelper::toVarName($params['assign']), new SmartestArray($sets));            
        }
        
    }
    
}