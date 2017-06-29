<?php 

// A class to extend for classes that just need offsetGet() without having to implement other methods that won't be used, simply to be compliant with ArrayAccess interface

class SmartestObject implements ArrayAccess{
    
    public function __toString(){
        return 'SmartestObject object: '.serialize($this);
    }
    
    public function __toJson(){
        return json_encode($this->__toSimpleObject());
    }
    
    public function __toSimpleObject(){
        return new stdClass;
    }
    
    public function offsetGet($offset){}
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}
