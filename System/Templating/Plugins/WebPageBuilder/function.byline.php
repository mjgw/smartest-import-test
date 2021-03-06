<?php

function smarty_function_byline($params, &$smartest_engine){
    
    $authors = array_values($smartest_engine->_tpl_vars['this']['authors']);
    
    $num_authors = count($authors);
    $byline = '';
    $request_data = SmartestPersistentObject::get('request_data');
    $use_links = (isset($params['links']) && SmartestStringHelper::toRealBool($params['links']));
    $links_possible = false;
    
    if($smartest_engine->getDraftMode()){
        if($use_links && is_object($GLOBALS['_site']) && $GLOBALS['_site']->getUserPageId()){
            $user_page = new SmartestPage;
            if($user_page->find($GLOBALS['_site']->getUserPageId())){
                $links_possible = true;
            }
        }
    }else{
        $links_possible = $use_links && is_object($GLOBALS['_site']) && $GLOBALS['_site']->getUserPageId();
    }
    
    if($num_authors){
        
        for($i=0;$i<$num_authors;$i++){
            
            if($links_possible){
                if($smartest_engine->getDraftMode()){
                    if(isset($_GET['hide_newwin_link']) && SmartestStringHelper::toRealBool($_GET['hide_newwin_link'])){
                        $byline .= '<a rel="author" href="'.$request_data->getParameter('domain').'website/renderEditableDraftPage?page_id='.$user_page->getWebId().'&amp;author_id='.$authors[$i]['id'].'&amp;hide_newwin_link=true" target="_top" class="sm-link-internal sm-link-byline">'.$authors[$i]['full_name'].'</a>';
                    }else{
                        $byline .= '<a rel="author" href="'.$request_data->getParameter('domain').'websitemanager/preview?page_id='.$user_page->getWebId().'&amp;author_id='.$authors[$i]['id'].'" target="_top" class="sm-link-internal sm-link-byline">'.$authors[$i]['full_name'].'</a>';
                    }
                }else{
                    $byline .= '<a rel="author" href="'.$request_data->getParameter('domain').'author/'.$authors[$i]['username'].'" class="sm-link-internal sm-link-byline">'.$authors[$i]['full_name'].'</a>';
                }
            }else{
                $byline .= $authors[$i]['full_name'];
            }
            
            if(isset($authors[$i+2])){
                $byline .= ', ';
            }else if(isset($authors[$i+1])){
                // var_dump($authors[$i+1]);
                $byline .= ' and ';
            }
            
        }
        
        return $byline;
    }
    
}