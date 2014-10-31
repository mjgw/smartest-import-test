<?php

class SmartestNotFoundPage extends SmartestPage{
    
    protected $_requested_url;
    
    public function setRequestedUrl($url){
        $this->_requested_url = $url;
    }
    
    public function getRequestedUrl(){
        return $this->_requested_url;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "request":
            case "requested_url":
            return $this->_requested_url;
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}