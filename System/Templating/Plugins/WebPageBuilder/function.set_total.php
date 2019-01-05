<?php

function smarty_function_set_total($params, &$smartest_engine){
    
    if(isset($params['setname'])){
        
        $name = SmartestStringHelper::toVarName($params['setname']);
        $total = count($smartest_engine->getDataSetItemsByName($name));
        if(isset($params['assign']) && strlen($params['assign'])){
            $variable_name = SmartestStringHelper::toVarName($params['assign']);
            $smartest_engine->assign($variable_name, $total);
        }else{
            return $total;
        }
        
    }else{
        
        return $smartest_engine->raiseError('Function set_total needs a set_name parameter');
        
    }
    
}