<?php
    
class SmartestTagPresenceChecker implements ArrayAccess{
    
    protected $_tags;
    
    public function __construct($tags){
        // $this->_tags = $tags;
        $this->_tags = array();
        
        foreach($tags as $t){
            if($t instanceof SmartestTag){
                $this->_tags[$t->getName()] = true;
            }
        }
        
    }
    
    public function offsetGet($offset){
        if(isset($this->_tags[$offset]) && $this->_tags[$offset] == true){
            return true;
        }else{
            return false;
        }
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
        
}