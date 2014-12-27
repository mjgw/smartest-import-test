<?php

function smarty_block_url_for($params, $content, &$smartest_engine, &$repeat){
    
    if(isset($params['assign'])){
        $smartest_engine->assign(SmartestStringHelper::toVarName($params['assign']), $smartest_engine->getUrlFor($content));
    }else{
        return $smartest_engine->getUrlFor($content);
    }
    
}

