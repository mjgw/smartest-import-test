<?php

function smarty_function_blocklist($params, &$smartest_engine){
    
    $name = isset($params['name']) ? SmartestStringHelper::toVarName($params['name']) : 'default';
    
    echo '<p>BlockList name: '.$name.'</p>';
    
}