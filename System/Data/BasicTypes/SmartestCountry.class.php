<?php

class SmartestCountry implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_countrycode;
    protected $_name;
    
    public function __construct($value=null){
        $this->setValue($value);
    }
    
    public function __toString(){
        return $this->_name ? $this->_name : 'Unknown';
    }
    
    public function getValue(){
        return $this->_countrycode;
    }
    
    public function setValue($value){
        
        if($value instanceof SmartestCountry){
            $this->_name = $value->getName();
            $this->_countrycode = $value->getCountryCode();
        }
        
        $all = SmartestDataUtility::getCountries();
        if(isset($all[$value])){
            $this->_countrycode = $value;
            $this->_name = $all[$value];
            return true;
        }else{
            return false;
        }
        
    }
    
    public function getCountryCode(){
        return $this->_countrycode;
    }
    
    public function getName(){
        return $this->_name;
    }
    
    public function hydrateFromStorableFormat($stored_format){
        return $this->setValue($stored_format);
    }
    
    public function getStorableFormat(){
        return $this->_countrycode;
    }
    
    public function hydrateFromFormData($data){
        return $this->setValue($data);
    }
    
    public function isPresent(){
        return (bool) strlen($this->_name);
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('name', 'code', 'label'));
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'code':
            return $this->_countrycode;
            
            case 'name':
            case 'label':
            return new SmartestString($this->_name);
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    
}