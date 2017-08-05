<?php

class SmartestArray implements ArrayAccess, IteratorAggregate, Countable, SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_data = array();
    
    public function __construct(){
        
        $d = func_get_args();
        
        if(count($d) == 1 && is_array($d[0])){
            $this->_data = $d[0];
        }else{
            $this->_data = $d;
        }
        
    }
    
    public function setValue($value){
        if(is_array($value)){
            $this->_data = $value;
        }else{
            throw new SmartestException("SmartestArray::setValue() expects an array; ".gettype($value)." given.");
        }
    }
    
    public function getValue(){
        return $this->getData();
    }
    
    public function __toString(){
        if(count($this->_data)){
            return SmartestStringHelper::toCommaSeparatedList(array_slice($this->_data, 0, 10), true, true);
        }else{
            return 'Empty SmartestArray';
        }
    }
    
    public function isPresent(){
        return (bool) count($this->_data);
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return serialize($this->_value);
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue(unserialize($v));
        return true;
    }
    
    // and these two for the SmartestSubmittableValue interface
    public function hydrateFromFormData($v){
        if(is_array($v)){
            $this->_data = $v;
            return true;
        }else{
            return false;
        }
    }
    
    public function renderInput($params){
        return "SmartestArray does not have a direct input.";
    }
    
    public function getHtmlFormFormat(){
        return $this->_value;
    }
    
    public function getData(){
        return $this->_data;
    }
    
    public function getIds(){
        $ids = array();
        foreach($this->_data as $item){
            if(is_object($item) && method_exists($item, 'getId')){
                $ids[] = $item->getId();
            }
        }
        return $ids;
    }
    
    public function add($value, $offset=null){
        if($offset){
            $this->_data[$offset] = $value;
        }else{
            $this->_data[] = $value;
        }
    }
    
    public function count(){
        return count($this->_data);
    }
    
    // This method can only be used when the array contains scalar values or objects that have string conversion (including any SmartestObject)
    public function getSummary($char_length=100){
    
        if(count($this->_data)){
    
            $string = '';
            $overspill_buffer_base_length = 15;
            $last_key = count($this->_data) - 1;
    
            foreach($this->_data as $k=>$element){
                
                // is there room in the string
                $len = strlen($element);
                $next_key = $k+1;
        
                if(isset($this->_data[$next_key])){
                    $digit_len = strlen(count(array_slice($this->_data, $next_key)));
                    // 2 is for quotes
                    $remaining_space = $char_length - $digit_len - $overspill_buffer_base_length - strlen($string) - 2;
                }else{
                    $remaining_space = $char_length - strlen($string) - 2;
                }
        
                if($k > 0 && $k < $last_key){
                    // 2 is for comma and space
                    $remaining_space = $remaining_space - 2;
                }else if($k == $last_key){
                    // 5 is for " and "
                    $remaining_space = $remaining_space - 5;
                }
        
                if($len <= $remaining_space){
                    if($k > 0 && $k < $last_key){
                        $string .= ', ';
                    }else if($k == $last_key){
                        if(count($this->_data) > 1){
                            $string .= ' and ';
                        }
                    }
                    $string .= $element;
                }else{
                    $num_left = count(array_slice($this->_data, $k));
                    $string .= ' and '.$num_left.' more...';
                    break;
                }
        
                  // if there are still more after this one, then buffer space is needed
                    // amount of buffer space depends on how many digits this figure is
            }
        
            return $string;
    
        }else{
        
            return '<span class="null-notice">No items selected.</span>';
        
        }
    
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "_ids":
            return $this->getIds();
            case "_data":
            case "_items":
            case "_objects":
            case "_values":
            return $this->getData();
            case "_count":
            return count($this->_data);
            case "_keys":
            return array_keys($this->_data);
            case "_first":
            return reset($this->_data);
            case "_last":
            return end($this->_data);
            case "_empty":
            return empty($this->_data);
            case "_php_class":
            return __CLASS__;
            
        }
        
        if(preg_match('/_first_(\d+)/', $offset, $matches)){
            return new SmartestArray(array_slice($this->_data, 0, $matches[1]));
        }
        
        if(preg_match('/_last_(\d+)/', $offset, $matches)){
            return new SmartestArray(array_slice($this->_data, $matches[1]*-1));
        }
        
        return $this->_data[$offset];
    }
    
    public function offsetExists($offset){
        return isset($this->_data[$offset]);
    }
    
    public function offsetSet($offset, $value){
        if($offset){
            $this->_data[$offset] = $value;
        }else{
            $this->_data[] = $value;
        }
    }
    
    public function offsetUnset($offset){
        unset($this->_data[$offset]);
    }
    
    /* public function next(){
        return next($this->_data);
    }
    
    public function seek($index){
        
        $this->rewind();
        $position = 0;
        
        while($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }
        
        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid seek position');
        }
        
    } */
    
    public function getIterator(){
        return new ArrayIterator($this->_data);
    }
    
    /* public function current(){
        return current($this->_data);
    }
    
    public function key(){
        return array_search(current($this->_data), $this->_data);
    }
    
    public function rewind(){
        reset($this->_data);
    } */
    
    public function append($value){
        $this->push($value);
    }
    
    public function push($value){
        $this->_data[] = $value;
    }
    
    public function asort(){
        sort($this->_data);
    }
    
    public function ksort(){
        ksort($this->_data);
    }
    
    public function natcasesort(){
        natcasesort($this->_data);
    }
    
    public function natsort(){
        natsort($this->_data);
    }
    
    public function reverse(){
        return array_reverse($this->_data);
    }
    
}