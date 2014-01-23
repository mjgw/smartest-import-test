<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_link($params, &$smartest_engine){
    
	if(isset($params['to']) && strlen($params['to'])){
	    
	    $ph = new SmartestParameterHolder('Raw Link Params: '.$params['to']);
	    $ph->loadArray($params);
	    
		$link = SmartestCmsLinkHelper::createLink($params['to'], $ph);
		
		if($GLOBALS['CURRENT_PAGE']){
		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
		}
		
		if(isset($params['highlightpage']) && isset($params['highlightclass'])){
		    /* $hlpagevarname = SmartestStringHelper::toVarName($params['highlightpage']);
		    echo $hlpagevarname;
		    echo $smartest_engine->_tpl_vars[$hlpagevarname]; */
		    if(is_object($link->getDestination()) && is_object($params['highlightpage']) && strlen($params['highlightclass'])){
		        // echo $link->getDestination();
		        if($link->getDestination()->getId() == $params['highlightpage']->getId()){
		            // echo "current";
		            $link->addClass($params['highlightclass']);
		        }
		    }
	    }
		
		if($link->hasError()){
		    return $smartest_engine->raiseError($link->getErrorMessage());
		}
		
		return $link->render($smartest_engine->getDraftMode());
		
	}else{
		return $smartest_engine->raiseError('Link could not be built. "to" field not properly defined.');
	}
	
}