<?php

SmartestHelper::register('FileSystem');

define('SM_DIR_SCAN_ALL', 0);
define('SM_DIR_SCAN_FILES', 1);
define('SM_DIR_SCAN_DIRECTORIES', 2);

class SmartestFileSystemHelper extends SmartestHelper{

	public static function getDirectoryContents($directory, $show_invisible=false, $type=0){
	
		$files = array();
		
		if(is_dir($directory)){
		    
		    if(!SmartestStringHelper::endsWith($directory, '/')){
    		    $directory .= '/';
    		}
		    
			$res = opendir($directory);
			
			while (false !== ($file = readdir($res))) {
        		
        		if($file != '.' && $file != '..'){
        		    if($show_invisible || $file{0} != '.'){
        		        if($type===0 || ($type===1 && is_file($directory.$file)) || ($type===2 && is_dir($directory.$file))){
    		                $files[] = utf8_encode($file);
		                }
    		        }
        		}
        		
			}
			
			closedir($res);
			
			return $files;
		}
	}
	
	public static function include_all($directory, $recursive=false, $exclusions=''){
		$items = self::getDirectoryContents($directory);
	}
	
	public static function include_group(){
		
		$files = func_get_args();
		
        if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			
			foreach($files as $file){
				
				if(file_exists(SM_ROOT_DIR.$file)){
					require SM_ROOT_DIR.$file;
				}
				
			}
			
		}else{
		    
            // create consistent hash and filename
			$files_in_order = $files;
			sort($files_in_order);
			$hash = md5(implode('_', $files_in_order));
			$filename = $hash.'.cache.php';
            $full_filename = 'System/Cache/Includes/'.utf8_decode($filename);
            $cache_revision_name = 'last_built_revision_'.substr($hash, 0, 16);
            $last_built_revision = SmartestCache::load($cache_revision_name, true);
            
			if(!file_exists(SM_ROOT_DIR.$full_filename) || (!$last_built_revision || $last_built_revision < SmartestInfo::$revision)){
		        
                $singlefile = '';
		
				// create single include file
				foreach($files as $file){
					if(file_exists(utf8_decode($file))){
						$singlefile .= file_get_contents(utf8_decode($file));
					}else{
						// ERROR - file does not exist
                        SmartestLog::getInstance('system')->log("SmartestFileSystemHelper::include_group(): File ".$file." not found for inclusion.");
					}
				}
                
                $filenames = "\n\n/**\n  * This file is a cached combination of the following files, included as one file to improve speed:\n\n  * ".implode("\n  * ", $files)."\n\n  */";
		
				$singlefile = str_replace('<'.'?php', "\n\n", $singlefile);
				$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
				$singlefile = "<"."?php\n\n// Auto-generated by SmartestFileSystemHelper::include_group() - Do Not Edit".$filenames.$singlefile;
		        SmartestCache::save($cache_revision_name, SmartestInfo::$revision, -1, true);
                
                $success = file_put_contents(SM_ROOT_DIR.$full_filename, $singlefile);
		
			}
		
			require_once(SM_ROOT_DIR.$full_filename);
            return $full_filename;
		
		}
		
	}
	
	public static function getFileName($path, $s='/'){ // sysnonym for PHP's basename(), but works on non-existent files
	    $separator = in_array($s, array('/', '\\', ':')) ? $s : '/';
		$fp = explode($separator, $path);
		return end($fp);
	}
	
	public static function getDirectoryName($file){
		
	}
	
	public static function getFileSize($file_path){
	    if(file_exists(utf8_decode($file_path))){
	        $size = filesize(utf8_decode($file_path));
	        return $size;
	    }else{
	        return false;
	    }
	}
	
	public static function getFileSizeFormatted($file_path){
	    
	    $size = self::getFileSize($file_path);
	    
	    if($size === false){
	        return false;
	    }else{
	        // size is in bytes
	        return self::formatRawFileSize($size);
	    }
	}
    
    public static function formatRawFileSize($size){
        
        if($size >= 1024){
            // convert to kilobytes
            $new_size = $size/1024;
            
            if($new_size >= 1024){
                // convert to megabytes
                $new_size = $new_size/1024;
                
                if($new_size >= 1024){
	                // convert to gigabytes
	                $new_size = $new_size/1024;
	                
                    /* if($new_size >= 1024){
    	                // convert to terrabytes
    	                $new_size = $new_size/1024;
                        return number_format($new_size, 3, '.', ',').' TB'; */
                    // }else{
                        return number_format($new_size, 2, '.', ',').' GB';
                    //}
                    
                    // No point checking for terrabytes

                }else{
                    return number_format($new_size, 1, '.', ',').' MB';
                }
                
            }else{
                return number_format($new_size, 1, '.', ',').' KB';
            }
            
        }else{
            return $size.' Bytes';
        }
        
    }
	
	public static function isSafeFileName($file_path, $intended_dir=''){
	    
	    if(!strlen($intended_dir)){
	        $intended_dir = constant('SM_ROOT_DIR');
	    }
	    
	    $attempted_dir_path = dirname(realpath($file_path)); // for example, /etc for ../../../../../../../etc/passwd
	    $intended_dir_path = dirname(realpath($intended_dir));
	    
	    if(mb_strlen($intended_dir_path) >= mb_strlen($attempted_dir_path)){
	        
	        // /var/vsc/smartest/trunk/Presentation/ is longer that /etc/, so compare first five chars of both
	        
	        if(mb_substr($intended_dir_path, 0, mb_strlen($attempted_dir_path)) == $attempted_dir_path){
	            return true;
	        }else{
	            return false;
	        }
	    }else{
	        
	        // otherwise do the exact same thing but the other way around
	        
	        if(mb_substr($attempted_dir_path, 0, mb_strlen($intended_dir_path)) == $intended_dir_path){
	            return true;
	        }else{
	            return false;
	        }
	    }
	    
	}
	
	public static function load($path, $binary_safe=false){
	    
	    if(is_dir(utf8_decode($path))){
	        return self::getDirectoryContents($path);
	    }else if(is_file(utf8_decode($path))){
	        
	        if($binary_safe){
	            $mode = 'rb';
	        }else{
	            $mode = 'r';
	        }
	        
            if($handle = fopen(utf8_decode($path), $mode)){
                
                // echo $path.' ';
                // var_dump(is_file($path));
                
                $size = filesize(utf8_decode($path)) ? filesize(utf8_decode($path)) : 1;
                
                $content = fread($handle, $size);
                fclose($handle);
                return $content;
                
            }else{
                // return "file could not be read";
                return false;
            }
	    }
	}
	
	public static function copy($old_path, $new_path){
	    if(@copy(utf8_decode($old_path), $new_path)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public static function move($old_path, $new_path){
	    if(@copy(utf8_decode($old_path), $new_path)){
	        return unlink($old_path);
	    }else{
	        return false;
	    }
	}
	
	public static function save($path, $data, $binary_safe=false){
	    
	    if($binary_safe){
            $mode = 'wb';
        }else{
            $mode = 'w';
        }
        
        $handle = fopen(utf8_decode($path), $mode);
        
        if($handle){
            
            $result = fwrite($handle, $data);
            fclose($handle);
            @chmod(utf8_decode($path), 0666);
            return $result;
            
        }else{
            return false;
        }
	}
	
	public static function saveToNewFile($path, $data, $binary_safe=false){
	    
	    $file_name = self::getUniqueFileName($path);
	    
	    if(self::save($file_name, $data, $binary_safe)){
	        return $file_name;
	    }else{
	        return false;
	    }
	    
	}
	
	public static function getUniqueFileName($full_path){
	    
	    if(!strlen($full_path)){
	        $full_path = SM_ROOT_DIR.'Public'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'Assets'.DIRECTORY_SEPARATOR.'Untitled_File.tmp';
	    }
	    
	    if(!SmartestStringHelper::startsWith($full_path, '/') && !SmartestStringHelper::startsWith($full_path, '\\')){
	        $full_path = SM_ROOT_DIR.$full_path;
	    }
	    
	    if(!file_exists(utf8_decode($full_path))){
	        
	        // THE FILE DOESN'T ALREADY EXIST, SO JUST GIVE THE NAME BACK
	        return $full_path;
	        
	    }else{
	        
	        // THE FILE ALREADY EXISTS! MAKE A NEW ONE
	        
	        // break up the directories
    	    $path_parts = preg_split('/(\/|\\\)/', $full_path);

    	    // the fastest way of getting the filename without the directory
    	    $actual_file = end($path_parts);
    	    
    	    // the reason we do it like this is because dirname() and basename() issue errors if the files don't exist - this way we can control that.
    	    $negative_start = (mb_strlen($actual_file) * -1);
    	    $directory = mb_substr($full_path, 0, $negative_start);
    	    
    	    $use_suffix = self::hasDotSuffix($full_path);
    	    
    	    if($use_suffix){
    	    
    	        $file_without_suffix = SmartestStringHelper::removeDotSuffix($actual_file);
    	        $file_suffix = SmartestStringHelper::getDotSuffix($actual_file);
    	    
	        }else{
	            
	            $file_without_suffix = $actual_file;
	            
	        }
    	    
    	    if(preg_match('/(.+)-(\d+)$/', $file_without_suffix, $matches)){
    	        $number = $matches[2];
    	        $file_name_trunk = $matches[1];
    	        $try_file = $actual_file;
    	    }else{
    	        if($use_suffix){
    	            $try_file = $file_without_suffix.'-1.'.$file_suffix;
    	            $file_name_trunk = $file_without_suffix;
	            }else{
	                $try_file = $file_without_suffix.'-1';
    	            $file_name_trunk = $file_without_suffix;
	            }
    	    }
	        
	        if(is_dir($directory)){
                
                $contents = self::getDirectoryContents($directory);
                
                $max_tries = 10000;
                $counter = 1;
                
                while($counter < $max_tries && in_array($try_file, $contents)){
                    $counter++;
                    if($use_suffix){
                        $try_file = $file_name_trunk.'-'.$counter.'.'.$file_suffix;
                    }else{
                        $try_file = $file_name_trunk.'-'.$counter;
                    }
                }
                
                return $directory.$try_file;
                
            }else{
                return null;
            }
        
        }
	    
	}
	
	public static function removeDotSuffix($file){
	    return SmartestStringHelper::removeDotSuffix($file);
	}
	
	public static function getDotSuffix($file){
		return SmartestStringHelper::getDotSuffix($file);
	}
	
	public static function hasDotSuffix($file){
	    return (bool) strlen(SmartestStringHelper::getDotSuffix($file));
	}
	
	public static function baseName($file_path, $separator='/'){
	    $parts = explode($separator, $file_path);
	    return end($parts);
	}
	
	public static function saveRemoteBinaryFile($file_uri, $local_path, $referer=null){
	    
	    if(!function_exists('curl_init')){
            throw new SmartestException('Tried to create an image from a remote URL, but failed because cURL is not installed.');
        }
	    
	    $ch = curl_init($file_uri);
    
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/600.1.17 (KHTML, like Gecko) Version/7.1 Safari/537.85.10');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($referer){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
    
        $rawdata = curl_exec($ch);
    
        curl_close ($ch);
    
        return self::saveToNewFile($local_path, $rawdata, true);
        
	}
	
	// note that this function will return directories that end with /, while PHP's dirname function does not
	public static function dirName($file_path){
	    $o = strlen(self::baseName($file_path));
	    $end = $o*-1;
	    return substr($file_path, 0, $end);
	}
	
	public static function setSuffix(){
		
	}

}