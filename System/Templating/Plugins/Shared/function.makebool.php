<?php

function smarty_function_makebool($params, &$smartest_engine){
    
    if(isset($params['value'])){
        
        $b = new SmartestBoolean(SmartestStringHelper::toRealBool($params['value']));
        
        if(isset($params['assign'])){
            $smartest_engine->assign(SmartestStringHelper::toVarName($params['assign']), $b);
        }else{
            return $b;
        }
        
    }
    
}