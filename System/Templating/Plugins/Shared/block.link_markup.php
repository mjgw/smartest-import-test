<?php

function smarty_block_link_markup($params, $content, &$smartest_engine, &$repeat){
    
	if(isset($params['to']) && strlen($params['to'])){
	    
		$ph = new SmartestParameterHolder('Raw Link Params: '.$params['to']);
	    $ph->loadArray($params);
	    
		$link = SmartestCmsLinkHelper::createLink($params['to'], $ph);
		
		if(isset($GLOBALS['CURRENT_PAGE'])){
		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
		}
        
		if(isset($params['highlightpage']) && isset($params['highlightclass'])){
		    // First check that destination has been found and that a page to highlight and a class to use have been specified
		    if(is_object($link->getDestination()) && is_object($params['highlightpage']) && strlen($params['highlightclass'])){
				// Then, is the page being linked to the page that should be highlighted
		        if($link->getDestination()->getId() == $params['highlightpage']->getId()){
					// if so, add the class name to highlight it
		            $link->addClass($params['highlightclass']);
		        }
		    }
	    }
		
		if($link->hasError()){
		    return $smartest_engine->raiseError($link->getErrorMessage());
		}
		
        // echo $content;
        
		return $link->render($smartest_engine->getDraftMode(), null, $content);
        
        // return $content;
    
	}else{
		return $smartest_engine->raiseError('Link could not be built. "to" field not properly defined.');
	}
    
}