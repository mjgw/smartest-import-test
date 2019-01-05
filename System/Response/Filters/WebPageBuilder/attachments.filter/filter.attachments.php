<?php

function smartest_filter_attachments($html, $filter){
    
    if(strpos($html, '<!SM_ATT_HB>')){
        
        $dev_hash = SM_DEVELOPER_MODE ? '&amp;n='.substr(md5(time()), 0, 8) : '';
        $info = SmartestSystemHelper::getSmartestLocalVersionInfo();
        $html = str_replace('<!SM_ATT_HB>', '', $html);
        $html = str_ireplace('</head>', "<link rel=\"stylesheet\" href=\"".$filter->getRequestData()->g('domain')."Resources/System/Stylesheets/sm_attachments.css?r=".$info['revision'].$dev_hash."\" />\n  </head>", $html);
        
    }
    
    return $html;
    
}