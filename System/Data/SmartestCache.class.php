<?php

class SmartestCache{

	static function load($token, $is_smartest=false){
		
		if($is_smartest){
			$file_name = 'scd_'.md5($token).'.tmp';
		}else{
			$file_name = 'ucd_'.md5($token).'.tmp';
		}
		
		$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
	
		if(file_exists($file_path)){
            if(filemtime($file_path) > SM_CACHE_LAST_MTIME){ // Check that the cache is not more than a week old, and delete if it is
                return unserialize(file_get_contents($file_path));
            }else{
                unlink($file_path);
                return null;
            }
		}else{
			return null;
		}
	}
	
	static function save($token, $data, $expire=-1, $is_smartest=false){
		
		if($is_smartest){
			$file_name = 'scd_'.md5($token).'.tmp';
		}else{
			$file_name = 'ucd_'.md5($token).'.tmp';
		}
		
		$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
	    
	    if(file_put_contents($file_path, serialize($data))){
			return true;
		}else{
			return false;
		}
	}
	
	static function hasData($token, $is_smartest=false){
		
		if($is_smartest){
			$file_name = 'scd_'.md5($token).'.tmp';
		}else{
			$file_name = 'ucd_'.md5($token).'.tmp';
		}
		
		$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
	    
	    if(file_exists($file_path) && filemtime($file_path) > SM_CACHE_LAST_MTIME){
			return true;
		}else{
			return false;
		}
	}
	
	static function clear($token="", $is_smartest=false){
		
		// clear just one thing
		if(strlen($token)){
			
			if($is_smartest){
				$file_name = 'scd_'.md5($token).'.tmp';
			}else{
				$file_name = 'ucd_'.md5($token).'.tmp';
			}
			
			$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
			
			// delete the file
			if(file_exists($file_path)){
				$success = unlink($file_path);
				// echo "deleted ".$file_path."<br />";
				return $success;
			}else{
				return false;
			}
			
		}else{
		
			return false;
			
		}
	}
	
	static function getFileName($token="", $is_smartest=false){
	    if(strlen($token)){
			
			if($is_smartest){
				$file_name = 'scd_'.md5($token).'.tmp';
			}else{
				$file_name = 'ucd_'.md5($token).'.tmp';
			}
			
			$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
			
			return $file_path;
			
		}else{
		
			return false;
			
		}
	}
	
}