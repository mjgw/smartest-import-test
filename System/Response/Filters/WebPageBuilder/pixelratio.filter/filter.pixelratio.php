<?php

function smartest_filter_pixelratio($html, $filter){
    
    if(!isset($_COOKIE['SMARTEST_DPRATIO'])){
        // This should only happen if cookies are permitted
        $script_tag = '<script type="text/javascript" src="'.$filter->getRequestData()->g('domain').'Resources/System/Javascript/smartest/device-pixel-ratio.js"></script>'."\n";
        $html = str_replace('</head>', $script_tag.'</head>', $html);
    }
    
    return $html;
    
}