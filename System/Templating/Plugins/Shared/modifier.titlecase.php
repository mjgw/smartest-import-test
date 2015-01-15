<?php

function smarty_modifier_titlecase($string){
    
    return SmartestStringHelper::toTitleCase($string, true);
    
}