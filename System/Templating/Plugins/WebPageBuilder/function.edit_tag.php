<?php

function smarty_function_edit_tag($params, $smartest_engine){
    
    return $smartest_engine->renderEditTagButton($params);
    
}