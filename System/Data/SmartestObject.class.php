<?php 

// A class to extend for classes that just need offsetGet() without having to implement other methods that won't be used, simply to be compliant with ArrayAccess interface

class SmartestObject implements ArrayAccess, JsonSerializable{
    
    public function __toString(){
        return 'SmartestObject object: '.serialize($this);
    }
    
    public function __toJson(){
        return json_encode($this->jsonSerialize());
    }
    
    public function __toSimpleObject(){
        return new stdClass;
    }
    
    public function stdObjectOrScalar(){
        return $this->__toSimpleObject();
    }
    
    public function jsonSerialize() {
        return $this->stdObjectOrScalar();
    }
    
    public function offsetGet($offset){}
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}
