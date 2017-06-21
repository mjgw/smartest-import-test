<?php

function smarty_function_blocklist($params, &$smartest_engine){
    
    $style = isset($params['style']) ? SmartestStringHelper::toVarName($params['style']) : 'default';
    
    return $smartest_engine->renderBlockList($style, $params);
    
}