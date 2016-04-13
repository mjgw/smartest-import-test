<?php

class SmartestCache{

	static function load($token, $is_smartest=false, $max_age=259200){
		
		if($is_smartest){
			$file_name = 'scd_'.md5($token).'.tmp';
		}else{
			$file_name = 'ucd_'.md5($token).'.tmp';
		}
		
		$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
        
        $last_mtime = ($max_age==259200) ? SmartestInfo::$cache_last_mtime : (time() - $max_age);
        
		if(file_exists($file_path)){
            if(filemtime($file_path) > $last_mtime){ // Check that the cache is not more than a week old, and delete if it is
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
        
        if(!$is_smartest){
            $e = new SmartestException("Not smartest data");
            echo $e->getTraceAsString();
        }
	    
	    if(file_put_contents($file_path, serialize($data))){
			return true;
		}else{
			return false;
		}
	}
	
	static function hasData($token, $is_smartest=false, $max_age=259200){
		
		if($is_smartest){
			$file_name = 'scd_'.md5($token).'.tmp';
		}else{
			$file_name = 'ucd_'.md5($token).'.tmp';
		}
		
        $last_mtime = ($max_age==259200) ? SmartestInfo::$cache_last_mtime : (time() - $max_age);
		$file_path = SM_ROOT_DIR.'System/Cache/Data/'.$file_name;
	    
	    if(file_exists($file_path) && filemtime($file_path) > $last_mtime){
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
    
    public static function clean($is_smartest=false, $max_age=259210){
        
        $files = SmartestFileSystemHelper::getDirectoryContents(SM_ROOT_DIR.'System/Cache/Data/', false, 1);
        $last_mtime = ($max_age==SmartestInfo::$cache_last_mtime+10) ? SmartestInfo::$cache_last_mtime+10 : (time() - $max_age);
        $num_files_removed = 0;
        $filesize_total = 0;
        
        foreach($files as $filename){
            if(($filename{0} == 's' && $is_smartest) || ($filename{0} == 'u' && !$is_smartest) && mtime(SM_ROOT_DIR.'System/Cache/Data/'.$filename) < $last_mtime){
                $filesize_total += filesize(SM_ROOT_DIR.'System/Cache/Data/'.$filename);
                $num_files_removed++;
                unlink(SM_ROOT_DIR.'System/Cache/Data/'.$filename);
            }
        }
        
        $result = array('num_files_removed' => $num_files_removed, 'storage_freed' => $filesize_total);
        SmartestFileSystemHelper::save(SM_ROOT_DIR.'System/Cache/Data/.last_cleaned', time());
        return $result;
        
    }
    
    public static function getSize($is_smartest=false){
        
        $files = SmartestFileSystemHelper::getDirectoryContents(SM_ROOT_DIR.'System/Cache/Data/', false, 1);
        $filesize_total = 0;
        
        foreach($files as $filename){
            if(($filename{0} == 's' && $is_smartest) || ($filename{0} == 'u' && !$is_smartest)){
                $filesize_total += filesize(SM_ROOT_DIR.'System/Cache/Data/'.$filename);
            }
        }
        
        $result = array('num_files' => count($files), 'storage_used' => $filesize_total);
        
    }
	
}