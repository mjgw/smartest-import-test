<?php

class SmartestBoolean implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue, SmartestJsonCompatibleObject{
    
    protected $_value;
    
    public function __construct($v=''){
        $this->setValue($v);
    }
    
    public function setValue($v){
        $this->_value = SmartestStringHelper::toRealBool($v);
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function isPresent(){
        return !is_null($this->_value);
    }
    
    public function __toString(){
        return $this->_value ? 'TRUE' : 'FALSE';
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->__toString();
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // And SmartestSubmittableValue
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function renderInput($params){
        
    }
    
    public function getHtmlFormFormat(){
        return $this->_value;
    }
    
    public function stdObjectOrScalar(){
        return $this->_value;
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('value', 'bool', 'raw', 'storedValue', 'stored_value', 'truefalse', 'int', 'numeric', 'bool', 'string', 'cssdisplayblock', 'cssdisplayinline', 'english', 'not', 'invert'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "value":
            case "bool":
            case "raw":
            return $this->getValue();
            case 'storedValue':
            case 'stored_value':
            case 'string':
            return $this->__toString();
            case 'english':
            case 'yesno':
            return $this->getValue() ? 'Yes' : 'No';
            case 'truefalse':
            return $this->getValue() ? 'true' : 'false';
            case 'int':
            case 'numeric':
            return (int) $this->_value;
            case 'cssdisplayblock':
            return 'display:'.$this->_value ? 'block' : 'none';
            case 'cssdisplayinline':
            return 'display:'.$this->_value ? 'inline' : 'none';
            case 'not':
            case 'invert':
            return new SmartestBoolean(!$this->_value);
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
}