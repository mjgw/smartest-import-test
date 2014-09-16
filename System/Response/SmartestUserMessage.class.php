<?php

class SmartestUserMessage implements ArrayAccess{
	
	private $_message;
	private $_type;
	private $_sticky = false;
	
	const INFO = 1;
	const SUCCESS = 2;
	const WARNING = 4;
	const ERROR = 8;
	const FAIL = 8;
	const ACCESSDENIED = 16;
	const ACCESS_DENIED = 16;
	
	public function __construct($message, $type, $sticky=false){
		$this->_message = $message;
		$this->_type = $type;
		$this->_sticky = (bool) $sticky;
	}
	
	public function getMessage(){
		return $this->_message;
	}
	
	public function getType(){
		return $this->_type;
	}
	
	public function getTypeName(){
		
		switch($this->getType()){
		    
		    case self::INFO:
		    return "info";
		    
		    case self::SUCCESS:
		    return "success";
		    
		    case self::WARNING:
		    return "warning";
		    
		    case self::ERROR:
		    return "error";
		    
		    case self::ACCESSDENIED:
		    return "access";
		    
		}
		    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "message":
	        case "content":
	        return $this->getMessage();
	        
	        case "type_id":
	        return $this->getType();
	        
	        case "type":
	        return $this->getTypeName();
	        
	        case "sticky":
	        case "is_sticky":
	        return $this->_sticky;
	        
	    }
	    
	}
	
	public function offsetSet($o, $v){}
	public function offsetUnset($o){}
	public function offsetExists($o){}
	
}