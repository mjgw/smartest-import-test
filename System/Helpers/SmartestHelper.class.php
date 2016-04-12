<?php

class SmartestHelper{
	
    public static $load_later_files = array();
    
	static function register($helper_name){
	
		// echo 'Registering Helper: '.$helper_name.'<br />';
	
		/* if(SmartestCache::hasData('smartest_registered_helpers', true)){
			$registered_helpers = SmartestCache::load('smartest_registered_helpers', true);
		}else{
			$registered_helpers = array();
		}
		
		if(!in_array($helper_name, $registered_helpers)){
			$registered_helpers[] = $helper_name;
			sort($registered_helpers);
			SmartestCache::save('smartest_registered_helpers', $registered_helpers, -1, true);
		} */
	}
	
	static function getRegisteredHelpers(){
		// if(SmartestCache::hasData('smartest_registered_helpers', true)){
		//	return SmartestCache::load('smartest_registered_helpers', true);
		//}else{
			return array();
		//}
	}
	
	static function isLoaded($helper_name){
		/* if(SmartestCache::hasData('smartest_registered_helpers', true)){
			$helpers = SmartestCache::load('smartest_registered_helpers', true);
			
			if(in_array($helper_name, $helpers)){
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		} */
	}
	
	static function load($helper_name){
		/*if(!self::isLoaded($helper_name)){
			if(file_exists(SM_ROOT_DIR.'System/Helpers/Smartest'.$helper_name.'Helper.class.php')){
				include_once(SM_ROOT_DIR.'System/Helpers/Smartest'.$helper_name.'Helper.class.php');
			}
		} */
	}
    
    public static function loadAllLaterFiles(){
        
		foreach(self::$load_later_files as $file){
	        include $file;
	    }
        
    }
	
	public static function loadAll(){
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		
		$system_helpers = array();
		$system_helper_names = array();
		$later_autoloads = array();
		
		if($res = opendir(SM_ROOT_DIR.'System/Helpers/')){
		    
		    $system_helper_cache_string = '';
		    
			while (false !== ($file = readdir($res))) {
    		
    			if(is_dir(SM_ROOT_DIR.'System/Helpers/'.$file) && preg_match('/([A-Z]\w+)\.helper$/', $file, $matches) && is_file(SM_ROOT_DIR.'System/Helpers/'.$file.'/'.$matches[1].'Helper.class.php')){
    				$helper = array();
    				$helper['name'] = $matches[1];
    				$helper['file'] = 'System/Helpers/'.$matches[0].'/'.$matches[1].'Helper.class.php';
    				$helper['dir'] = 'System/Helpers/'.$matches[0].'/';
    				$system_helpers[] = $helper;
    				$system_helper_names[] = $helper['name'];
    				$system_helper_cache_string .= sha1_file($helper['file']);
    			}
    		
			}
			
			closedir($res);
			
			$system_helper_cache_hash = sha1($system_helper_cache_string);
			
		}else{
		    
		    throw new SmartestException(SM_ROOT_DIR.'System/Helpers/ could not be read.');
		    
		}
		
		$use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_system_helpers_hash', true) != $system_helper_cache_hash || !is_file(SM_ROOT_DIR.'System/Cache/Includes/SmartestSystemHelpers.cache.php')));
		
		foreach($system_helpers as $h){
			if(is_file($h['file'])){
				if($use_cache){
				    if($rebuild_cache){
				        $singlefile .= file_get_contents(SM_ROOT_DIR.$h['file']);
			        }else{
			            // don't need to include anything because helpers are already in cache
			        }
				}else{
				    // Include the original file rather than the cache
				    include SM_ROOT_DIR.$h['file'];
				}
			}else{
				// File was there amoment ago but has now disappeared (???)
			}
			
			if(is_file($h['dir'].'autoload.conf')){
			    $autoload_file = $h['dir'].'autoload.conf';
			    $autoload_file_contents = file_get_contents($autoload_file);
			    
			    preg_match_all('/^(\w+)\s+([^\s]+)$/m', $autoload_file_contents, $matches, PREG_SET_ORDER);
			    
			    foreach($matches as $m){
			        switch($m[1]){
                        
			            case 'load':
			            if(is_file(SM_ROOT_DIR.$h['dir'].$m[2])){
			                $later_autoloads[] = $h['dir'].$m[2];
			            }else{
			                throw new SmartestException('Failed opening autoloaded resource: '.$h['dir'].$m[2].'.');
			            }
			            break;
                        
                        case 'loadlater':
                        if(is_file(SM_ROOT_DIR.$h['dir'].$m[2])){
			                self::$load_later_files[] = SM_ROOT_DIR.$h['dir'].$m[2];
			            }else{
			                throw new SmartestException('Failed queuing non-existent helper dataobject: '.$h['dir'].$m[2].'.');
			            }
                        
			        }
			    }
			    
			}
			
		}
		
		foreach($later_autoloads as $file){
	        if($use_cache){
			    if($rebuild_cache){
			        $singlefile .= file_get_contents(SM_ROOT_DIR.$file);
		        }else{
		            // don't need to include anything because helpers are already in cache
		        }
			}else{
			    // Include the original file rather than the cache
			    include SM_ROOT_DIR.$file;
			}
	    }
	    
	    if($rebuild_cache){
			
			$singlefile = str_replace('<?php', "\n\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;
		    
			file_put_contents(SM_ROOT_DIR.'System/Cache/Includes/SmartestSystemHelpers.cache.php', $singlefile);
			
			if(is_writable(SM_ROOT_DIR.'System/Cache/Data/' || defined('SM_INSTALLATION_STATUS_CHECKED'))){
			    SmartestCache::save('smartest_system_helpers_hash', $system_helper_cache_hash, -1, true);
		    }
			
		}
		
		if($use_cache){
			include SM_ROOT_DIR.'System/Cache/Includes/SmartestSystemHelpers.cache.php';
		}
		
		// NOW LOOK FOR ANY USER-CONTRIBUTED HELPERS (in Library/Helpers/)
		if(is_dir(SM_ROOT_DIR.'Library/Helpers/')){
		    
		    $singlefile = '';
		
    		// find the user-added helpers if this hasn't already been done
			
    		$user_helpers = array();
			$user_helper_names = array();
			$later_autoloads = array();
		
			if($res = opendir(SM_ROOT_DIR.'Library/Helpers/')){
		        
		        $user_helper_cache_string = '';
		        
				while (false !== ($file = readdir($res))) {
    		
        			if(is_dir(SM_ROOT_DIR.'Library/Helpers/'.$file) && preg_match('/([A-Z]\w+)\.helper$/', $file, $matches) && is_file(SM_ROOT_DIR.'Library/Helpers/'.$file.'/'.$matches[1].'Helper.class.php')){
        				$helper = array();
        				$helper['name'] = $matches[1];
        				$helper['file'] = 'Library/Helpers/'.$matches[0].'/'.$matches[1].'Helper.class.php';
        				$helper['dir'] = 'Library/Helpers/'.$matches[0].'/';
        				$user_helpers[] = $helper;
        				$user_helper_names[] = $helper['name'];
        				$user_helper_cache_string .= sha1_file($helper['file']);
        			}
    		
				}
		
				closedir($res);
			    
			    $user_helper_cache_hash = sha1($user_helper_cache_string);
				
			}else{

    			throw new SmartestException(SM_ROOT_DIR.'Library/Helpers/ could not be read.');

    		}
	        
	        if(count($user_helpers)){
	        
		        $use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
        		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_user_helpers_hash', true) != $user_helper_cache_hash || !is_file(SM_ROOT_DIR.'System/Cache/Includes/UserHelpers.cache.php')));
                
                foreach($user_helpers as $h){
        			if(is_file($h['file'])){
        				if($use_cache){
        				    if($rebuild_cache){
        				        $singlefile .= file_get_contents(SM_ROOT_DIR.$h['file']);
        			        }else{
            			        // don't need to include anything because helpers are already in cache
            			    }
        				}else{
        				    // Include the original file rather than the cache
        				    include SM_ROOT_DIR.$h['file'];
        				}
        			}else{
        				// File was there amoment ago but has now disappeared (???)
        			}
        			
        			if(is_file(SM_ROOT_DIR.$h['dir'].'autoload.conf')){
        			    
        			    $autoload_file = SM_ROOT_DIR.$h['dir'].'autoload.conf';
        			    $autoload_file_contents = file_get_contents($autoload_file);

        			    preg_match_all('/^(\w+)\s+([^\s\/]+)$/m', $autoload_file_contents, $matches, PREG_SET_ORDER);

        			    foreach($matches as $m){
        			        switch($m[1]){
        			            
        			            case 'load':
        			            
        			            if(is_file(SM_ROOT_DIR.$h['dir'].$m[2])){
        			                $later_autoloads[] = SM_ROOT_DIR.$h['dir'].$m[2];
        			            }else{
        			                throw new SmartestException('Failed opening autoloaded resource: '.$h['dir'].$m[2].'.');
        			            }
        			            
        			            break;
        			        }
        			    }

        			}
        			
        		}
        		
        		foreach($later_autoloads as $file){
        	        if($use_cache){
        			    if($rebuild_cache){
        			        $singlefile .= file_get_contents(SM_ROOT_DIR.$file);
        		        }else{
        		            // don't need to include anything because helpers are already in cache
        		        }
        			}else{
        			    // Include the original file rather than the cache
        			    include SM_ROOT_DIR.$file;
        			}
        	    }

        		if($rebuild_cache){

        			$singlefile = str_replace('<?php', "\n\n", $singlefile);
        			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
        			$singlefile = "<?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;

        			file_put_contents(SM_ROOT_DIR.'System/Cache/Includes/UserHelpers.cache.php', $singlefile);
    			
        			SmartestCache::save('smartest_user_helpers_hash', $user_helper_cache_hash, -1, true);
        		}

        		if($use_cache){
        			include SM_ROOT_DIR.'System/Cache/Includes/UserHelpers.cache.php';
        		}
        		
		    }
		}
		
	}
	
	
	/// Loads any helpers that are part of the current application (Quince Module)
	public static function loadApplicationHelpers(){
	    
	    // print_r(SmartestPersistentObject::get('request_data')->g('application'));
	    
	    $application_name = SmartestPersistentObject::get('request_data')->g('application')->g('name');
	    $application_dir  = SmartestPersistentObject::get('request_data')->g('application')->g('directory');
	    $helpers_dir      = $application_dir.'Helpers/';
	    $cache_file_name = SM_ROOT_DIR.'System/Cache/Includes/'.SmartestStringHelper::toCamelCase($application_name).'Helpers.cache.php';
        $cache_name = 'app_'.SmartestStringHelper::toVarName($application_name).'_helpers_cache';
	    
	    if(is_dir($helpers_dir)){
		    
		    $singlefile = '';
		
    		// find the user-added helpers if this hasn't already been done
			
    		$user_helpers = array();
			$user_helper_names = array();
			$later_autoloads = array();
		
			if($res = opendir($helpers_dir)){
		        
		        $user_helper_cache_string = '';
		        
				while (false !== ($file = readdir($res))) {
				    
				    if(is_dir($helpers_dir.$file) && preg_match('/([A-Z]\w+)\.helper$/', $file, $matches) && is_file($helpers_dir.$file.'/'.$matches[1].'Helper.class.php')){
        				
        				$helper = array();
        				$helper['name'] = $matches[1];
        				$helper['file'] = $helpers_dir.$matches[0].'/'.$matches[1].'Helper.class.php';
        				$helper['dir'] = $helpers_dir.$matches[0].'/';
        				$user_helpers[] = $helper;
        				$user_helper_names[] = $helper['name'];
        				$user_helper_cache_string .= sha1_file($helper['file']);
        			}
    		
				}
		
				closedir($res);
			    
			    $user_helper_cache_hash = sha1($cache_name);
				
			}else{

    			throw new SmartestException($helpers_dir.' could not be read.');

    		}
	        
	        if(count($user_helpers)){
	        
		        $use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
        		$rebuild_cache = ($use_cache && (SmartestCache::load($cache_name, true) != $user_helper_cache_hash || !is_file($cache_file_name)));
                
                foreach($user_helpers as $h){
        			if(is_file($h['file'])){
        				if($use_cache){
        				    if($rebuild_cache){
        				        $singlefile .= file_get_contents($h['file']);
        			        }else{
            			        // don't need to include anything because helpers are already in cache
            			    }
        				}else{
        				    // Include the original file rather than the cache
        				    include $h['file'];
        				}
        			}else{
        				// File was there amoment ago but has now disappeared (???)
        			}
        			
        			if(is_file($h['dir'].'autoload.conf')){
        			    
        			    $autoload_file = $h['dir'].'autoload.conf';
        			    $autoload_file_contents = file_get_contents($autoload_file);

        			    preg_match_all('/^(\w+)\s+([^\s\/]+)$/m', $autoload_file_contents, $matches, PREG_SET_ORDER);

        			    foreach($matches as $m){
        			        switch($m[1]){
        			            
        			            case 'load':
        			            
        			            if(is_file($h['dir'].$m[2])){
        			                $later_autoloads[] = $h['dir'].$m[2];
        			            }else{
        			                throw new SmartestException('Failed opening autoloaded resource: '.$h['dir'].$m[2].'.');
        			            }
        			            
        			            break;
        			        }
        			    }

        			}
        			
        		}
        		
        		foreach($later_autoloads as $file){
        	        if($use_cache){
        			    if($rebuild_cache){
        			        $singlefile .= file_get_contents($file);
        		        }else{
        		            // don't need to include anything because helpers are already in cache
        		        }
        			}else{
        			    // Include the original file rather than the cache
        			    include $file;
        			}
        	    }

        		if($rebuild_cache){

        			$singlefile = str_replace('<?php', "\n\n", $singlefile);
        			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
        			$singlefile = "<?php\n\n// Auto-generated by SmartestHelper - Do Not Edit".$singlefile;

        			file_put_contents($cache_file_name, $singlefile);
    			
        			SmartestCache::save($cache_name, $user_helper_cache_hash, -1, true);
        		}

        		if($use_cache){
        			include $cache_file_name;
        		}
        		
		    }
		}
	    
	}
	
}