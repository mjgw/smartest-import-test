<?php

function smartest_filter_ga($html, $filter){
    
    $ph = SmartestPersistentObject::get('prefs_helper');
    $preference_value = SmartestStringHelper::toRealBool($ph->getGlobalPreference('enable_eu_cookie_compliance', null, $filter->getCurrentSite()->getId()));
    
    $cookies_allowed = (!(bool) $preference_value) || ($preference_value && isset($_COOKIE['SMARTEST_COOKIE_CONSENT']) && $_COOKIE['SMARTEST_COOKIE_CONSENT'] == "1");
    $override_cookie_mode_for_ga = SmartestStringHelper::toRealBool($ph->getGlobalPreference('override_eu_cookie_compliance_ga', null, $filter->getCurrentSite()->getId()));
    
    $helper = new SmartestAuthenticationHelper;
    
    // Has the template plugin been used?
    if(strpos($html, '<!--SM_GA_TAG')){
        
        if($helper->getSystemUserIsLoggedIn() || isset($_COOKIE['SMARTEST_ANALYTICS_IGNORE'])){
            $html = preg_replace('/<!--SM_GA_TAG:ID=[\w-]+-->/', '<!--Google Analytics tags would have been placed here, but it is disabled for logged in admin users and Smartest users that have chosen to disable is with a cookie-->', $html);
        }else{
            
            // Yes, if so, are cookies allowed?
            if($cookies_allowed){
            
                preg_match('/<!--SM_GA_TAG:ID=([\w-]+)-->/', $html, $matches);
            
                $id = $matches[1];
            
                // Yes, replace the trace left by the template plugin with the proper GA code
    	        $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/google_analytics.tpl';
                $render_process_id = 'google_analytics_'.$id;
                $sm = new SmartyManager('BasicRenderer');
                $r = $sm->initialize($render_process_id);
                $r->assign('analytics_id', $id);
    	        $r->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
    	        $tag = $r->fetch($file);
            
                $html = str_replace('<!--SM_GA_TAG:ID='.$id.'-->', $tag, $html);
            
            }else{
                // No, replace the trace left by the plugin with a notice in an HTML comment
                $html = preg_replace('/<!--SM_GA_TAG:ID=[\w-]+-->/', '<!--Google Analytics tags would have been placed here, was there permission to set cookies on your machine-->', $html);
            }
            
        }
        
    }else{
        // No. Is there a value for the setting in Site settings?
        $id = $ph->getGlobalPreference('google_analytics_id', null, $filter->getCurrentSite()->getId());
        if(strlen($id)){
            
            // Check if analytics have been added to the page manually. If they have, issue a warning and do not proceed any further
            if(strpos($html, 'i,s,o,g,r,a,m') || strpos($html, 'gaJsHost')){
                if($filter->getDraftMode()){
                    $sm = new SmartyManager('BasicRenderer');
                    $render_process_id = 'google_analytics_error';
                    $r = $sm->initialize($render_process_id);
                    $r->assign('_error_text', 'Manually-added Google Analytics code detected. Aborting auto-inclusion.');
                    $error_tag = $r->fetch(SM_ROOT_DIR."System/Presentation/WebPageBuilder/markup_error.tpl");
                    $html = str_ireplace('</body>', $error_tag."\n\n</body>", $html);
                }else{
                    $html = str_ireplace('</body>', "<!--Manually-added Google Analytics code detected. Aborting auto-inclusion.-->\n\n</body>", $html);
                }
                return $html;
            }
            
            if($helper->getSystemUserIsLoggedIn() || isset($_COOKIE['SMARTEST_ANALYTICS_IGNORE'])){
                // Note - this protection against triggering your own Google Analytics will not work when you log into Smartest under a different host name than the website being served.
                $html = str_ireplace('</body>', '<!--Google Analytics tags would have been placed here, but it is disabled for logged in admin users and Smartest users that have chosen to disable is with a cookie-->'."\n".'</body>', $html);
            }else{
            
                // Yes, if so, are cookies allowed?
                if($cookies_allowed || $override_cookie_mode_for_ga){
                    // Yes, so use the closing body tag to insert the code if the page is not a draft
                
                    if($filter->getDraftMode()){
                        $html = str_ireplace('</body>', "<!--On a live page, Google Analytics will be placed here (".$id.")-->\n\n</body>", $html);
                    }else{
            	        $file = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/google_analytics.tpl';
                        $render_process_id = 'google_analytics_'.$id;
                        $sm = new SmartyManager('BasicRenderer');
                        $r = $sm->initialize($render_process_id);
                        $r->assign('analytics_id', $id);
            	        $r->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
            	        $tag = $r->fetch($file);
                    
                        $html = str_ireplace('</body>', $tag."\n\n</body>", $html);
                    }
                
                }else{
                    // No, So insert a notice in an HTML comment
                    if(!$filter->getDraftMode()){
                        $html = str_ireplace('</body>', "<!--Google Analytics tags would have been placed here, was there permission to set cookies on your machine-->\n\n</body>", $html);
                    }
                }
            
            }
              
        }else{
            // No value in site settings and no template use, so do nothing
        }
        
    }
      
    return $html;
    
}