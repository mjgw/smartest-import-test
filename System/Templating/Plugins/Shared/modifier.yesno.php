<?php

function smarty_modifier_yesno($string){
    
    $bool = SmartestStringHelper::toRealBool($string);
    return $bool ? 'Yes' : 'No';
    
}