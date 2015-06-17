<?php

function smarty_modifier_xmlentities($string){
    return SmartestStringHelper::toXmlEntitiesSmart($string);
}