<?php

class SmartestFrontEndSystemInfoQueryService extends SmartestObject{
    
    protected $_du = null;
    
    public function __construct(){
        $this->_du = new SmartestDataUtility;
    }
    
    public function getSiteId(){
        
        if(isset($GLOBALS['_site']) && is_object($GLOBALS['_site'])){
            $site_id = $GLOBALS['_site']->getId();
        }else if($this->getRequest()->getModule() == 'website' && defined('SM_CMS_PAGE_SITE_ID')){
	        // This is mostly for when objects are used on web pages
            $site_id = constant('SM_CMS_PAGE_SITE_ID');
        }else if(is_object(SmartestSession::get('current_open_project'))){
            // This is mostly for when objects are used within the Smartest backend
            // make sure the site object exists
            $site_id = SmartestSession::get('current_open_project')->getId();
        }
        
        return $site_id;
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('tags', 'users', 'system_users', 'asset_types', 'data_types', 'models', 'site_id'));
    }
    
    public function offsetGet($offset){
        
        switch($offset){
        
            case "tags":
            return $this->_du->getTags();
            
            case "users":
    	    $uh = new SmartestUsersHelper;
            return $uh->getUsers();
            
            case "system_users":
    	    $uh = new SmartestUsersHelper;
            return $uh->getSystemUsers();
            
            case "asset_types":
            return SmartestDataUtility::getAssetTypes();
            
            case "data_types":
            return SmartestDataUtility::getDataTypes();
            
            case "models":
            return $this->_du->getModels($this->getSiteId());
            
            case 'site_id':
            return $this->getSiteId();
        
        }
        
    }
    
}