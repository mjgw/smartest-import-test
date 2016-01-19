<?php

class SmartestSet extends SmartestBaseSet{
    
    protected $_membership_type;
    protected $_set_settings;
    
    public function __objectConstruct(){
        
	    
         
    }
    
    public function __postHydrationAction(){
        
        $s = unserialize($this->getInfo());
		
	    if(!is_object($this->_set_settings)){
	        $this->_set_settings = new SmartestParameterHolder("Settings for Set '".$this->_properties['label']."'");
        }
        
        if(is_array($s)){
		    $this->_set_settings->loadArray($s);
	    }else{
	        $this->_set_settings->loadArray(array());
	    }   
        
    }
    
    public function save(){
        
        if(!strlen($this->getWebId())){
            $this->setWebId(SmartestStringHelper::random(32));
        }
        
        parent::save();
        
    }
    
	public function setSettingValue($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    // URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here:
	    if(is_object($this->_set_settings)){
            $this->_set_settings->setParameter($field, rawurlencode(utf8_decode($new_data)));
	        $this->_modified_properties['info'] = SmartestStringHelper::sanitize(serialize($this->_set_settings->getArray()));
        }
	    
	}
	
	public function getSettingValue($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
        
        if(is_object($this->_set_settings) && $this->_set_settings->hasParameter($field)){
	        return utf8_encode(stripslashes(rawurldecode($this->_set_settings->getParameter($field))));
	    }else{
	        return null;
	    }
	}

}
