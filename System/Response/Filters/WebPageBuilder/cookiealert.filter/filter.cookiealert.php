<?php

function smartest_filter_cookiealert($html, $filter){
    
    $ph = SmartestPersistentObject::get('prefs_helper');
    $preference_value = $ph->getGlobalPreference('enable_eu_cookie_compliance', null, $filter->getCurrentSite()->getId());
    
    if(SmartestStringHelper::toRealBool($preference_value)){
        
        if(isset($_COOKIE['SMARTEST_COOKIE_CONSENT']) && SmartestStringHelper::toRealBool($_COOKIE['SMARTEST_COOKIE_CONSENT'])){
            
            $html = str_replace('</body>', "\n<!--EU Directive 2009/136/EC compliance mode on. Consent to set cookies has been given-->\n\n</body>", $html);
            
        }else{
        
            $sm = new SmartyManager('BasicRenderer');
            $r = $sm->initialize('cookie_warning_html');
            
            if(is_file($filter->getCurrentSite()->getDirectory().'Presentation/Special/eu_cookie_warning.tpl')){
                $tpl = $filter->getCurrentSite()->getDirectory().'Presentation/Special/eu_cookie_warning.tpl';
            }else if(is_file(SM_ROOT_DIR.'Presentation/Special/eu_cookie_warning.tpl')){
                $tpl = SM_ROOT_DIR.'Presentation/Special/eu_cookie_warning.tpl';
            }else{
                $tpl = $filter->getDirectory().'default_eu_cookie_warning.tpl';
            }
            
            $phtml = $r->fetch($tpl);
            
            $html = str_replace('</body>', $phtml.'</body>', $html);
            
        }
    
    }
    
    return $html;
    
}