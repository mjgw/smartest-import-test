<?php

// This is the class that is accessed via the $this variable in the template on web pages

class SmartestPageRenderingDataRequestHandler implements ArrayAccess{

    protected $_page;
    protected $_all_tags = null;
    protected $_site;
    protected $_navigation_handler = null;
    protected $_search_info = null;
    protected $_preferences_helper;
    protected $_cached_global_preferences;
    
    public function __construct(SmartestPage $page){
        
        $this->assignPage($page);
        
        if(SmartestPersistentObject::get('prefs_helper')){
            $this->_preferences_helper = SmartestPersistentObject::get('prefs_helper');
        }
        
        $this->_cached_global_preferences = new SmartestParameterHolder('Cached global preferences');
		
    }
    
    public function assignPage(SmartestPage $page){
        $this->_page = $page;
    }
    
    public function getNavigationDataRequestHandler(){
        if(!$this->_navigation_handler){
            $this->_navigation_handler = new SmartestPageNavigationDataRequestHandler($this->_page);
        }
        return $this->_navigation_handler;
    }
    
    public function getSite(){
        if(!$this->_site){
            $this->_site = $this->_page->getParentSite();
        }
        return $this->_site;
    }
    
    public function getAllTags(){
        if(!$this->_all_tags){
            $du = new SmartestDataUtility;
    	    $this->_all_tags = $du->getTags();
        }
        return $this->_all_tags;
    }
    
    public function getPrincipalItem(){
        if($this->isItem()){
            return $this->_page->getPrincipalItem();
        }else{
            return null;
        }
    }
    
    public function isItem(){
        return $this->_page instanceof SmartestItemPage;
    }
    
    public function getFieldDefinitions(){
        return $this->_page->getPageFieldDefinitions();
    }
    
    public function getSearchInfo(){
        
        if($this->_page instanceof SmartestSearchPage){
            
            if(!$this->_search_info){
                
                $this->_search_info = new SmartestParameterHolder('Search query and results');
                $this->_search_info->setParameter('results', new SmartestArray($this->_page->getResults()));
                $this->_search_info->setParameter('num_results', count($this->_page->getResults()));
                $this->_search_info->setParameter('query', strip_tags($this->_page->getSearchQuery()));
                $this->_search_info->setParameter('time_taken', new SmartestNumeric($this->_page->getLastSearchTimeTaken()));
                
            }
            
            return $this->_search_info;
            
        }
        
    }
    
	protected function getUser(){
	    return SmartestSession::get('user');
	}
	
	protected function getUserIdOrZero(){
        if(is_object($this->getUser())){
            return $this->getUser()->getId();
        }else{
            return '0';
        }
    }
    
    protected function getGlobalPreference($preference_name){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        
        if($this->_cached_global_preferences->hasParameter($name)){
            return $this->_cached_global_preferences->getParameter($name);
        }else{
            $value = $this->_preferences_helper->getGlobalPreference($name, $this->getUserIdOrZero(), $this->_page->getSiteId());
            $this->_cached_global_preferences->setParameter($name, $value);
            return $value;
        }
        
    }
    
    public function offsetGet($offset){
        
        if($this->_page instanceof SmartestItemPage){
            $model_varname = SmartestStringHelper::toVarName($this->_page->getPrincipalItem()->getModel()->getName());
        }else{
            $model_varname = '_X_';
        }
        
        switch($offset){
            
            case "tag":
            if($this->_page instanceof SmartestTagPage){
                return $this->_page->getTag();
            }else{
                return null;
            }
            
            case "page":
            return $this->_page;
            
            case "is_page":
            return ($this instanceof SmartestItemPage) ? false : true;
        
            case "site":
            return $this->getSite();
        
            case "all_tags":
            return $this->getAllTags();
            
            case "author":
            case "user":
            if($this->_page instanceof SmartestUserPage){
                $user = $this->_page->getUser();
                $user->setDraftMode($this->_page->getDraftMode());
                return $user;
                // return $this->_page->getUser();
            }else{
                return null;
            }
        
            case "authors":
            case "users":
            return $this->_page->getAuthors();
        
            case "fields":
            return $this->_page->getPageFieldDefinitions();
            
            case "placeholders":
            // print_r($this->_page->getPlaceholderDefinitions()->getAliases());
            return $this->_page->getPlaceholderDefinitions();
            
            case "containers":
            return $this->_page->getContainerDefinitions();
        
            case "request":
            return SmartestPersistentObject::get('request_data');
        
            case "navigation":
            return $this->getNavigationDataRequestHandler();
        
            case "principal_item":
            case "item":
            case $model_varname:
            return $this->getPrincipalItem();
            
            case "search_results":
            if($this->_page instanceof SmartestSearchPage){
                return new SmartestArray($this->_page->getResults());
            }else{
                return null;
            }
            
            case "num_search_results":
            if($this->_page instanceof SmartestSearchPage){
                return count($this->_page->getResults());
            }else{
                return null;
            }
            
            case "search_time_taken":
            if($this->_page instanceof SmartestSearchPage){
                return new SmartestNumeric(number_format($this->_page->getLastSearchTimeTaken(), 2));
            }else{
                return null;
            }
            
            case "raw_search_time_taken":
            if($this->_page instanceof SmartestSearchPage){
                return $this->_page->getLastSearchTimeTaken();
            }else{
                return null;
            }
            
            case "search":
            if($this->_page instanceof SmartestSearchPage){
                return $this->getSearchInfo();
            }else{
                return null;
            }
        
            case "is_item":
            return $this->isItem();
            
            case "is_404":
            return $this->_page instanceof SmartestNotFoundPage;
            
            case "is_503":
            return $this->_page->isHoldingPage();
        
            case "has_item":
            return $this->getPrincipalItem() instanceof SmartestCmsItem;
            
            case "_php_class":
            return __CLASS__;
            
            case "user_agent":
            if(! (bool) $this->getGlobalPreference('enable_site_responsive_mode') && (bool) $this->_page->getCacheAsHTML()){
                if(!$this->_page->getDraftMode()){
                    SmartestLog::getInstance('renderer')->log('User agent information is being accessed on a page when responsive mode is not activated, meaning the wrong user agent info may be saved in cache for later users. Enable responsive mode in Site settings to turn off this message.');
                }
            }
            return SmartestPersistentObject::get('userAgent');
        
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetExists($offset){}
    public function offsetUnset($offset){}

}