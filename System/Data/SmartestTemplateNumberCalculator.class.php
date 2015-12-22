<?php

class SmartestTemplateNumberCalculator implements ArrayAccess{
    
    protected $_number = null;
    
    public function __construct($number){
        $this->_number = (float) $number;
    }
    
    public function offsetGet($offset){
        
        if(preg_match('/^mod_(\d+)$/', $offset, $matches)){
            return $this->_number % $matches[1];
        }
        
        if(preg_match('/^rmod_(\d+)$/', $offset, $matches)){
            return floor($this->_number/$matches[1]);
        }
        
        switch($offset){
            
            case 'fibonacci':
            return null;
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
        
    public function __toString(){
        return (string) $this->_number;
    }
    
}