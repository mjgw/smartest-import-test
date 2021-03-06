<?php

function smartest_filter_preview($html, $filter){
    
    if($filter->getDraftMode()){
        
        $request_data = SmartestPersistentObject::get('request_data');
        
        $preview_url = '/website/renderEditableDraftPage?page_id='.$request_data->getParameter('request_parameters')->getParameter('page_id').'&amp;hide_newwin_link=true';
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')) $preview_url .= '&amp;item_id='.$request_data->getParameter('request_parameters')->getParameter('item_id');
        if($request_data->getParameter('request_parameters')->hasParameter('search_query')) $preview_url .= '&amp;q='.$request_data->getParameter('request_parameters')->getParameter('search_query');
        if($request_data->getParameter('request_parameters')->hasParameter('author_id')) $preview_url .= '&amp;author_id='.$request_data->getParameter('request_parameters')->getParameter('author_id');
        if($request_data->getParameter('request_parameters')->hasParameter('tag_name')) $preview_url .= '&amp;tag_name='.$request_data->getParameter('request_parameters')->getParameter('tag_name');
        if($request_data->getParameter('request_parameters')->hasParameter('model_id')) $preview_url .= '&amp;model_id='.$request_data->getParameter('request_parameters')->getParameter('model_id');
        if($request_data->getParameter('request_parameters')->hasParameter('q')) $preview_url .= '&amp;q='.$request_data->getParameter('request_parameters')->getParameter('q');
        if($request_data->getParameter('request_parameters')->hasParameter('request')) $preview_url .= '&amp;request='.$request_data->getParameter('request_parameters')->getParameter('request');
        
        $smartest_preview_url = '/websitemanager/preview?page_id='.$request_data->getParameter('request_parameters')->getParameter('page_id');
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')) $smartest_preview_url .= '&amp;item_id='.$request_data->getParameter('request_parameters')->getParameter('item_id');
        if($request_data->getParameter('request_parameters')->hasParameter('q')) $smartest_preview_url .= '&amp;search_query='.$request_data->getParameter('request_parameters')->getParameter('q');
        if($request_data->getParameter('request_parameters')->hasParameter('author_id')) $smartest_preview_url .= '&amp;author_id='.$request_data->getParameter('request_parameters')->getParameter('author_id');
        if($request_data->getParameter('request_parameters')->hasParameter('tag_name')) $smartest_preview_url .= '&amp;tag='.$request_data->getParameter('request_parameters')->getParameter('tag_name');
        if($request_data->getParameter('request_parameters')->hasParameter('model_id')) $smartest_preview_url .= '&amp;model_id='.$request_data->getParameter('request_parameters')->getParameter('model_id');
        if($request_data->getParameter('request_parameters')->hasParameter('request')) $smartest_preview_url .= '&amp;requested_page='.$request_data->getParameter('request_parameters')->getParameter('request');

        $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize('preview_bar_html');
        $r->setDraftMode(true);
        
        $r->assign('overhead_time', SmartestPersistentObject::get('timing_data')->getParameter('overhead_time_taken'));
        $r->assign('build_time', SmartestPersistentObject::get('timing_data')->getParameter('smarty_time_taken'));
        $r->assign('total_time', SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken'));
        $r->assign('liberate_link_url', $preview_url);
        $r->assign('preview_link_url', $smartest_preview_url);
        $r->assign('page_webid', $request_data->getParameter('request_parameters')->getParameter('page_id'));
        $r->assign('hide_liberate_link', SmartestStringHelper::toRealBool(SmartestPersistentObject::get('request_data')->getParameter('request_parameters')->getParameter('hide_newwin_link')));
        
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')){
            $r->assign('has_item', true);
        }else{
            $r->assign('has_item', false);
        }
        
        // var_dump(SM_CMS_PAGE_SITE_ID);
        $ph = new SmartestPreferencesHelper();
        $hide_preview_bar = $ph->getApplicationPreference('hide_preview_bar', 'com.smartest.CmsFrontEnd', SmartestSession::get('user')->getId(), SM_CMS_PAGE_SITE_ID);
        $hide_preview_edit_buttons = $ph->getApplicationPreference('hide_preview_edit_buttons', 'com.smartest.CmsFrontEnd', SmartestSession::get('user')->getId(), SM_CMS_PAGE_SITE_ID);
        // var_dump($hide_preview_bar);
        $r->assign('hide_preview_bar', (bool) $hide_preview_bar);
        $r->assign('hide_preview_edit_buttons', new SmartestBoolean($hide_preview_edit_buttons));
        // var_dump($hide_preview_edit_buttons);
        
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')){
            $item_id = (int) $request_data->getParameter('request_parameters')->getParameter('item_id');
            $item = new SmartestItem;
            if($item->find($item_id)){
                $r->assign('item_id', $item_id);
                $r->assign('model_name', $item->getModel()->getName());
                $r->assign('show_item_edit_link', true);
            }else{
                $r->assign('show_item_edit_link', false);
            }
        }else{
            $r->assign('show_item_edit_link', false);
        }
        
        $phtml = $r->fetch($filter->getDirectory().'previewbar.tpl');
        
        preg_match('/<body[^>]*?'.'>/i', $html, $match);
		
		if(!empty($match[0])){
			$body_tag = $match[0];
		}else{
			$body_tag = '';
		}
		
		$pcss = SmartestFileSystemHelper::load($filter->getDirectory().'previewbar.stylehtml.txt');
		$pcss = str_replace('%DOMAIN%', $filter->getRequestData()->g('domain'), $pcss);
        $pcss = str_replace('%TIME%', time(), $pcss);
		
		$html = str_replace('</head>', $pcss.'</head>', $html);
		$html = str_replace($body_tag, $body_tag."\n".$phtml, $html);
        $html = str_replace('</body>', "<script type=\"text/javascript\">if(parent.showPreview){parent.showPreview();}</script>\n<!--Page was built in: ".SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken')."ms -->\n</body>", $html);
        
    }else{
        
        $heartbeat_id = SmartestPersistentObject::get('request_data')->getParameter('request_parameters')->getParameter('heartbeat_id');
        
        if($heartbeat_id == SM_CMS_PAGE_SITE_UNIQUE_ID){
            $html = str_replace('</head>', "<meta name=\"smartest:siteid\" content=\"".$heartbeat_id."\" />\n</head>", $html);
            $html = str_replace('</body>', "<!--SMARTEST HEARTBEAT-->\n</body>", $html);
        }
      
        /* 
        if(defined('SM_CMS_PAGE_SITE_UNIQUE_ID')){
            $sid = "<!--SMARTEST HEARTBEAT-->\n<!--SITEID: ".SM_CMS_PAGE_SITE_UNIQUE_ID."-->\n";
        }else{
            $sid = "<!--SMARTEST HEARTBEAT-->\n";
        }
        
        $creator = "\n<!--Powered by Smartest v".constant('SM_INFO_VERSION_NUMBER')." -->\n".$sid;
        $html = str_replace('</body>', $creator."<!--Page was returned in: ".SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken')."ms -->\n</body>", $html);
        */
        
    }
    
    return $html;
    
}