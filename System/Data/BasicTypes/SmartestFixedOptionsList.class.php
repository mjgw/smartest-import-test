<?php

class SmartestFixedOptionsList implements ArrayAccess, IteratorAggregate, Countable{

    protected $_options;
    protected $_type = 'SM_DATATYPE_SL_TEXT';
    
    public function __construct($options, $type='SM_DATATYPE_SL_TEXT'){
        if($this->setType($type)){
            if(is_array($options)){
                if(count($options)){
                    $fo = reset($options);
                    // If the first option is an object
                    if(is_object($fo)){
                        // Check that the object is the correct type
                        if(get_class($fo) == SmartestDataUtility::getClassForDataType($type)){
                            $this->_options = $options;
                        }else{
                            // ERROR: On object of the wrong type has been passed.
                        }
                    }else{
                        // Otherwise load the options so that they become objects
                        $this->loadRawOptions($options, $this->_type);
                    }
                }else{
                    // There are zero options. Hmmmm.
                }
            }else{
                // $options is not an array. SHould probably throw an exception.
            }
        }
    }
    
    public function setType($type){
        if(SmartestDataUtility::isValidType($type, null)){
            $this->_type = $type;
            return true;
        }else{
            return false;
        }
    }
    
    public function loadRawOptions($options, $type){
        if($this->setType($type)){
            $final_options = array();
            foreach($options as $key=>$value){
                $final_options[$key] = SmartestDataUtility::objectize($value, $this->_type);
            }
            $this->loadOptions($final_options);
        }
    }
    
    public function loadOptions($options){
        
        if(is_array($options)){
            
            $o = new SmartestParameterHolder('Fixed Options List Options');
            $o->loadArray($options);
            $this->_options = $o;
            
        }else if($options instanceof $options){
            
            $this->_options = $options;
            
        }
        
    }
    
    public function count(){
        return count($this->_options);
    }
    
    public function &getIterator(){
        return new ArrayIterator($this->_options->getArray());
    }
    
    public function getKeys(){
        return array_keys($this->_options->getArray());
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "_keys":
            case "_names":
            return array_keys($this->_options->getArray());
            case "_options":
            return $this->_options->getArray();
            case "_count":
            return count($this->_options->getArray());
            case "_first":
            return reset($this->_options->getArray());
            case "_last":
            return end($this->_options->getArray());
            case "_empty":
            return count($this->_options->getArray()) < 1;
            case "_php_class":
            return __CLASS__;
            
        }
        
        return $this->_data[$offset];
        
    }
    
    public function offsetExists($offset){
        return isset($this->_options[$offset]);
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}

}