<?php

class SmartestParameterHolder extends SmartestObject implements IteratorAggregate, Countable, SmartestBasicType, SmartestJsonCompatibleObject{
    
    protected $_data = array();
    protected $_name;
    protected $_read_only = false;
    protected $_aliases = array();
    
    public function __construct($name, $read_only=false){
        $this->_name = $name;
        $this->_read_only = $read_only;
    }
    
    public function loadArray($array, $create_phobjects=true){
        if(is_array($array)){
            foreach($array as $key=>$value){
                if(is_array($value)){
                    if($create_phobjects){
                        $data = new SmartestParameterHolder('Param: '.$key, $this->_read_only);
                        $data->loadArray($value, true);
                        $this->setParameter($key, $data);
                    }else{
                        $this->setParameter($key, $value);
                    }
                }else{
                    $this->setParameter($key, $value);
                }
            }
        }else{
            // tried to load something that wasn't an array. Log?
        }
    }
    
    public function getArray(){
        return $this->_data;
    }
    
    public function absorb(SmartestParameterHolder $d){
        $this->loadArray($d->getParameters());
    }
    
    // This function is part of the SmartestBasicType API
    public function setValue($value){
        if(is_array($value)){
            $this->_data = $value;
        }else{
            throw new SmartestException("SmartestArray::setValue() expects an array; ".gettype($value)." given.");
        }
    }
    
    // This function is part of the SmartestBasicType API
    public function getValue(){
        return $this->getData();
    }
    
    public function __toString(){
        return 'SmartestParameterHolder: '.$this->_name;
    }
    
    public function __toSimpleObject(){
        $obj = new stdClass;
        foreach($this->_data as $k=>$v){
            if($v instanceof SmartestJsonCompatibleObject){
                $obj->$k = $v->stdObjectOrScalar();
            }else{
                $obj->$k = $v;
            }
        }
        return $obj;
    }
    
    public function stdObjectOrScalar(){
        return $this->__toSimpleObject();
    }
    
    public function debug(){
        return "<pre>".print_r($this->_data, true)."</pre>";
    }
    
    public function isPresent(){
        return $this->hasData();
    }
    
    public function getParameter($n, $default=null){
        if(isset($this->_data[$n])){
            return $this->_data[$n];
        }elseif(isset($this->_aliases[$n])){
            if(isset($this->_data[$this->_aliases[$n]])){
                return $this->_data[$this->_aliases[$n]];
            }else{
                SmartestLog::getInstance('Alias \''.$alias_name.'\' exists but  original offset\''.$original_name.'\' is missing, in SmartestParameterHOlder \''.$this->_name.'\'.');
                if(isset($default)){
                    return $default;
                }else{
                    return null;
                }
            }
        }else{
            if(isset($default)){
                return $default;
            }else{
                return null;
            }
        }
    }
    
    public function getParameterOrFalse($n){
        $v = $this->getParameter($n);
        if(is_object($v) && $v instanceof SmartestBasicType){
            return $v->isPresent() ? $v : false;
        }else{
            return $v;
        }
    }
    
    public function getParameterOrNull($n){
        $v = $this->getParameter($n);
        if(is_object($v) && $v instanceof SmartestBasicType){
            return $v->isPresent() ? $v : null;
        }else{
            return $v;
        }
    }
    
    public function addAlias($alias_name, $original_name){
        if(!isset($this->_data[$original_name])){
            SmartestLog::getInstance('Alias \''.$alias_name.'\' created for original offset\''.$original_name.'\' that is not set, in SmartestParameterHOlder \''.$this->_name.'\'.');
        }
        // echo "added alias ".$alias_name;
        $this->_aliases[$alias_name] = $original_name;
    }
    
    public function removeAlias($alias_name){
        if(isset($this->_aliases[$alias_name])){
            unset($this->_aliases[$alias_name]);
        }
    }
    
    public function getAliases(){
        return array_keys($this->_aliases);
    }
    
    public function g($n, $d=null){
        return $this->getParameter($n, $d);
    }
    
    public function getParameters(){
        return $this->getArray();
    }
    
    public function getParameterNames(){
        return array_keys($this->_data);
    }
    
    public function d(){ // D for Data
        return $this->getParameters();
    }
    
    public function __toArray(){
        $a = array();
        foreach($this->_data as $k=>$v){
            if($v instanceof SmartestParameterHolder){
                $a[$k] = $v->__toArray();
            }else{
                $a[$k] = $v;
            }
        }
        return $a;
    }
    
    public function toArray(){
        return $this->__toArray();
    }
    
    public function a(){
        return $this->__toArray();
    }
    
    public function hasParameter($n){
        return $this->h($n);
    }
    
    public function h($n){
        return isset($this->_data[$n]);
    }
    
    public function setParameter($n, $v, $to_ph=false){
        if(is_array($v)){
            $ph = new SmartestParameterHolder($n);
            $ph->loadArray($v);
            $this->_data[$n] = $ph;
        }else{
            $this->_data[$n] = $v;
        }
        return true;
    }
    
    public function s($n, $v){
        return $this->setParameter($n, $v);
    }
    
    public function clearParameter($n){
        if(isset($this->_data[$n])){
            unset($this->_data[$n]);
            return true;
        }else{
            return false;
        }
    }
    
    public function hasData(){
        return (bool) count($this->_data);
    }
    
    public function getSimpleObject(){
        $o = new stdClass;
        foreach($this->_data as $n => $v){
            if($v instanceof SmartestParameterHolder){
                $o->$n = $v->getSimpleObject();
            }else{
                $o->$n = stripslashes($v);
            }
        }
        return $o;
    }
    
    public function o(){
        return $this->getSimpleObject();
    }
    
    public function __toJson(){
        return json_encode($this->getSimpleObject());
    }
    
    public function j(){
        return $this->toJson();
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "_count":
            return count($this->_data);
            case "_first":
            return reset($this->_data);
            case "_last":
            return end($this->_data);
            case "_json":
            return $this->toJson();
            case "_keys":
            return array_keys($this->_data);
            case "_has":
            return new SmartestParameterHolderValuePresenceChecker(array_keys($this->_data));
            case "_debug":
            return "<code>".print_r($this->_data, true)."</code>";
        }
        
        return $this->getParameter($offset);
        
    }
    
    public function offsetExists($offset){
        return $this->hasParameter($offset);
    }
    
    public function offsetSet($offset, $value){
        if(!$this->_read_only){
            return $this->setParameter($offset, $value);
        }
    }
    
    public function offsetUnset($offset){
        if(!$this->_read_only){
            return $this->clearParameter($offset);
        }
    }
    
    public function &getIterator(){
        return new ArrayIterator($this->_data);
    }
    
    public function count(){
        return count($this->_data);
    }
    
    public function hasValue($v){
        return in_array($v, $this->_data);
    }
    
}