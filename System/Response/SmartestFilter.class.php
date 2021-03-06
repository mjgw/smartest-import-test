<?php

class SmartestFilter{
    
    protected $_function_file;
    protected $_directory;
    protected $_name;
    protected $_filter_chain;
    protected $_request_data;
    
    public function __construct(){
        // $e = new Exception;
        // print_r($e->getTrace());
        $this->_request_data = SmartestPersistentObject::get('request_data');
    }
    
    public function getName(){
        return $this->_name;
    }
    
    public function setName($n){
        $this->_name = $n;
    }
    
    public function getFunctionFile(){
        return $this->_function_file;
    }
    
    public function setFunctionFile($f){
        $this->_function_file = $f;
    }
    
    public function getDirectory(){
        return $this->_directory;
    }
    
    public function setDirectory($d){
        $this->_directory = $d;
    }
    
    public function attachChain(SmartestFilterChain $c){
        $this->_filter_chain = $c;
    }
    
    public function getFilterChain(){
        return $this->_filter_chain;
    }
    
    public function getDraftMode(){
        return $this->_filter_chain->getDraftMode();
    }
    
    public function getRequestData(){
        return $this->_request_data;
    }
    
    public function getCurrentSite(){
        
        if(isset($this->_site) && is_object($this->_site)){
            
            return $this->_site;
            
        }else{
            
            if($this->getRequestData()->g('application')->g('name') == 'website'){
                
                if(isset($GLOBALS['_site']) && $GLOBALS['_site'] instanceof SmartestSite){
                    $this->_site = $GLOBALS['_site'];
                }else{
                    $site_id = constant('SM_CMS_PAGE_SITE_ID');
                
                    $site = new SmartestSite;
        
                    if($site->find($site_id)){
                        $this->_site = $site;
                    }
                }
                
            }elseif(isset($GLOBALS['_site']) && $GLOBALS['_site'] instanceof SmartestSite){
                
                $this->_site = $GLOBALS['_site'];
                
            }else if(is_object(SmartestSession::get('current_open_project'))){
                // This is mostly for when objects are used within the Smartest backend
                // make sure the site object exists
                $site = SmartestSession::get('current_open_project');
                $this->_site = $site;
            }
        
            return $this->_site;
            
        }
        
    }
    
    public function execute($html){
        
        $function_name = 'smartest_filter_'.$this->_name;
        
        if(!function_exists($function_name)){
            
            if(is_file($this->_function_file)){
                
                require $this->_function_file;
        
                if(function_exists($function_name)){
            
                    $html = call_user_func($function_name, $html, $this);
                    return $html;
            
                }else{
                    SmartestLog::getInstance('system')->log('Filter '.$this->_name.' expects a function called '.$function_name.' to be defined in file '.$this->_function_file.', but none exists.', SmartestLog::WARNING);
                }
            
            }else{
                
                SmartestLog::getInstance('system')->log('Filter '.$this->_name.' is supposed to contain a file called '.$this->_function_file.', but none exists.', SmartestLog::WARNING);
                
            }
            
        }
        
    }
    
}