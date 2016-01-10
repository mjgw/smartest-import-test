<?php

function smarty_function_reorder_page_group($params, $smartest_engine){
    
    if(isset($params['name'])){
        
        return $smartest_engine->renderReorderPageGroupButton($params['name']);
        
    }
    
}