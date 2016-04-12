<?php

class SmartestSession{
    
    const ALL = 100;
    const OBJECTS = 101;
    const NOTFALSE = 102;
    const NOT_FALSE = 102;
    
    final public static function start(){
        if(!self::isRegistered()){
            session_start();
            /* if(!defined('SM_SESSION_ACTIVE')){
                define('SM_SESSION_ACTIVE', true);
            } */
            SmartestInfo::$session_active = true;
        }
    }
    
    final public static function isRegistered(){
        
        if(SmartestInfo::$session_active){
            return true;
        }
        
        if(version_compare(PHP_VERSION, '5.4.0') >= 0){
            return session_status() != PHP_SESSION_NONE;
        }else{
            return session_id() != '';
        }
    }
    
	final public static function get($object_name){
		
        if(self::isRegistered()){
            
    		if(strlen($object_name)){
			
    			$key = 'smartest/';
			
    			$parts = explode(':', $object_name);
    			$name = implode('/', $parts);
			
    			$key .= $name;
			
    			if(array_key_exists($key, $_SESSION)){
    				return $_SESSION[$key];
    			}else{
    				return null;
    			}
    		}
            
        }else{
            return null;
        }
	}
	
	final public static function set($object_name, $data){
		
        if(self::isRegistered()){
        
    		if(strlen($object_name)){
			
    			$key = 'smartest/';
			
    			$parts = explode(':', $object_name);
    			$name = implode('/', $parts);
			
    			$key .= $name;
			
    			if(isset($_SESSION)){
    			    $_SESSION[$key] = &$data;
    			}else{
    			    throw new SmartestException('SmartestSession or SmartestPersistentObject used while session was not active');
    			}
    		}
        }
	}
	
	public static function clear($object_name){
        
        if(self::isRegistered()){
        
    	    if(strlen($object_name)){
			
    			$key = 'smartest/';
			
    			$parts = explode(':', $object_name);
    			$name = implode('/', $parts);
			
    			$key .= $name;
			
    			if(array_key_exists($key, $_SESSION)){
    			    unset($_SESSION[$key]);
    			    return true;
    		    }else{
    		        return false;
    		    }
    		}
        
        }else{
            return false;
        }
        
	}
	
	public static function hasData($object_name){
        
        if(self::isRegistered()){
        
    	    if(strlen($object_name)){
			
    			$key = 'smartest/';
			
    			$parts = explode(':', $object_name);
    			$name = implode('/', $parts);
			
    			$key .= $name;
			
    			if(array_key_exists($key, $_SESSION)){
    			    return true;
    		    }else{
    		        return false;
    		    }
    		}
        
        }else{
            return false;
        }
        
	}
	
	public static function clearAll($killNonSmartest=false){
	    
        if(self::isRegistered()){
        
    	    if($killNonSmartest){
    	        $killed = array_keys($_SESSION);
    	        session_destroy();
    	    }else{
    	        $killed = array();
    	        foreach($_SESSION as $name=>$value){
    	            if(substr($name, 0, 9) == 'smartest/'){
    	                $killed[] = $name;
    	                unset($_SESSION[$name]);
    	            }
    	        }
    	    }
	    
    	    return $killed;
        
        }else{
            return array();
        }
	    
	}
	
	public static function getRegisteredNames($type = 100){
	    
        if(self::isRegistered()){
        
    	    $vars = array();
	    
    	    switch($type){
    	        case self::ALL:
    	        foreach($_SESSION as $name=>$value){
    	            if(substr($name, 0, 9) == 'smartest/'){
    	                $vars[] = $name;
    	            }
    	        }
    	        break;
	        
    	        case self::OBJECTS:
    	        foreach($_SESSION as $name=>$value){
    	            if(substr($name, 0, 9) == 'smartest/' && is_object($value)){
    	                $vars[] = $name;
    	            }
    	        }
    	        break;
	        
    	        case self::NOTFALSE:
    	        foreach($_SESSION as $name=>$value){
    	            if(substr($name, 0, 9) == 'smartest/' && $value){
    	                $vars[] = $name;
    	            }
    	        }
    	        break;
    	    }
	    
    	    return $vars;
        
        }else{
            return array();
        }
        
	}
	
}