<?php

// set up required paths and environmental constants

function debug_time(){
    return number_format(microtime(true)*1000, 0, ".", "");
}

// If your host does not allow the use of ini_set(), comment out these lines and see Public/.htaccess
ini_set('session.name', 'SMARTEST_SESSION');
ini_set('session.auto_start', 0);

class SmartestInit{

	public static function setRootDir(){
	
		if(!defined('SM_ROOT_DIR')){
		
			chdir('../');
			define("SM_ROOT_DIR", getcwd().DIRECTORY_SEPARATOR);
		
		}
	}
	
	public static function setIncludePaths(){
		
		$existing_include_path = get_include_path();
		
		if(!defined('PATH_SEPARATOR')){
			define('PATH_SEPARATOR', ':');
		}
		
		$ip_array = explode(constant('PATH_SEPARATOR'), $existing_include_path);
		
		$new_array = array('.');
		
		$new_array[] = SM_ROOT_DIR."System/Library/Smarty2/";
		$new_array[] = SM_ROOT_DIR."System/Library/";
		$new_array[] = SM_ROOT_DIR."Library/Smarty/";
		$new_array[] = SM_ROOT_DIR."Library/Pear/";
		$new_array[] = SM_ROOT_DIR."Library/";
		
		foreach($ip_array as $path){
			if($path != '.'){
				$new_array[] = $path;
			}
		}
		
		$new_include_path = implode(constant('PATH_SEPARATOR'), $new_array);
		
		set_include_path($new_include_path);
		
	}
	
	public static function go(){
	    
	    self::setRootDir();
		self::setIncludePaths();
        
        if(is_file(SM_ROOT_DIR.'Configuration/phpsettings.ini')){
            $user_settings = parse_ini_file(SM_ROOT_DIR.'Configuration/phpsettings.ini');
            $user_settings_exist = true;
        }else{
            $user_settings_exist = false;
        }
        
        $default_settings = parse_ini_file(SM_ROOT_DIR.'System/Core/Info/phpsettings.ini');
        
        $display_errors = ($user_settings_exist && isset($user_settings['display_errors'])) ? (bool) $user_settings['display_errors'] : (bool) $default_settings['display_errors'];
        $developer_mode = ($user_settings_exist && isset($user_settings['developer_mode'])) ? (bool) $user_settings['developer_mode'] : (bool) $default_settings['developer_mode'];
        
        // set the debug level for the controller
        define("SM_DEVELOPER_MODE", $developer_mode);
		
		// error reporting control
        error_reporting(E_WARNING|E_ERROR|E_PARSE);
        // error_reporting(E_ALL ^ E_STRICT);
        
        if(is_writable(SM_ROOT_DIR.'System/Logs/')){
            // If PHP error messages can be logged, they should be.
            // If your host does not allow the use of ini_set(), comment out these lines and add these settings manually in Public/.htaccess
            ini_set('error_log', SM_ROOT_DIR.'System/Logs/php_errors_no_date.log');
            ini_set('log_errors', true);
            ini_set('display_errors', $display_errors);  // Sergiy: Totally breaks displaying of pages on PHP 5.4 when uncommented,
                                                
            // since files so many E_STRICT and E_NOTICE and their details.
            // IMHO, it should be enabled only in optional debug/dev mode up to
            // admin or developer preference and probably better from php.ini only
            // Or to implement option whether set it here or use php.ini defaults
            
            // This has now been disabled as a hard-coded option. To edit/reveal PHP errors, enable this in Configuration/phpsettings.ini
            

        }
	    
	    require SM_ROOT_DIR.'System/Base/constants.php';
		require SM_ROOT_DIR.'System/Response/SmartestResponse.class.php';
        
        // as the Donny Hathaway song says, $everything is everything
		$everything = new SmartestResponse;
		$everything->init();
		$everything->build();
		$everything->finish();
		
	}

}