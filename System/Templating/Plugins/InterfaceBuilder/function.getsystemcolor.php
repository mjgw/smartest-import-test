<?php

function smarty_function_getsystemcolor($params, &$smartest_engine){
    
    $yamldata = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/colours.yml');
    $colors = $yamldata['colours'];
    $num_colors = count($colors);
    
    // return print_r($colors, 1);
    
    if(isset($GLOBALS['_last_system_color_index'])){
        $lastcolorindex = $GLOBALS['_last_system_color_index'];
        if($lastcolorindex >= $num_colors){
            $nextcolorindex = 0;
        }else{
            $nextcolorindex = $lastcolorindex+1;
        }
    }else{
        $nextcolorindex = mt_rand(0,$num_colors-1);
    }
    
    $color = new SmartestRgbColor($colors[$nextcolorindex]);
    
    $GLOBALS['_last_system_color_index'] = $nextcolorindex;
    
    if(isset($params['assign'])){
        $smartest_engine->assign(SmartestStringHelper::toVarName($params['assign']), $color);
    }
    
    // return $color;
    
}