<?php

class SmartestDatabase{
    
    private static $dbc = array();
    
    public static function getInstance($connection_name, $throw_db_exception=false){
        
        // $connection_name .= "_new";
        
        // if(!isset(self::$dbc[$connection_name])){
            
            $config = self::readConfiguration($connection_name);
            $class = $config['class'];
        
            if(strlen($class)){
                
                try{
                    $object = new $class($config);
                }catch(SmartestDatabaseException $e){
                    // SmartestCache::clear('dbc_'.$connection_name, true);
                    if($throw_db_exception){
                        throw $e;
                    }else{
                        throw new SmartestException($e->getMessage(), SM_ERROR_DB);
                    }
                }
                
                self::$dbc[$connection_name] = $object;
                
            }else{
                SmartestCache::clear('dbc_'.$connection_name, true);
                throw new SmartestException("Database connection '".$connection_name."' does not have a valid class, e.g. SmartestMysql", SM_ERROR_CONFIG);
            }
        
        // }
        
        return self::$dbc[$connection_name];
        
    }
    
    public static function testConnection($connection_name){
        
        $config = self::readConfiguration($connection_name);
        $class = $config['class'];
        
        if(strlen($class)){
            
            try{
                $object = new $class($config);
            }catch(SmartestDatabaseException $e){
                throw $e;
            }
            
        }
        
    }
    
    public static function readConfiguration($connection_name){
        
        if(is_file(SM_ROOT_DIR."Configuration/database.yml")){
            $actual_md5 = md5_file(SM_ROOT_DIR."Configuration/database.yml");
            $stored_md5 = SmartestCache::load('db_config_yaml_file_md5', true);
        }else{
            $actual_md5 = md5_file(SM_ROOT_DIR."Configuration/database.ini");
            $stored_md5 = SmartestCache::load('db_config_file_md5', true);
            if(is_writable(SM_ROOT_DIR."Configuration/")){
                if(!date_default_timezone_get()){
                    $sd = SmartestYamlHelper::fastLoad(SYSTEM_INFO_FILE);
                    date_default_timezone_set($sd['system']['info']['default_timezone']);
                }
                file_put_contents(SM_ROOT_DIR."Configuration/database.yml", '# Migrated database config info at '.date('Y-m-d, H:i')." (default timezone)\n#\n#\n".SmartestYamlHelper::create(parse_ini_file(SM_ROOT_DIR."Configuration/database.ini", true)));
            }
        }
        
        $config_file_changed = ($actual_md5 != $stored_md5);
        
        if(!$config_file_changed && $d = SmartestCache::load('dbc_'.$connection_name, true)){ // SmartestCache::clear('dbc_'.$connection_name, true);
            
            return $d;
            
        }else{
            
            if(is_file(SM_ROOT_DIR."Configuration/database.yml")){
                $dbconfig = SmartestYamlHelper::load(SM_ROOT_DIR."Configuration/database.yml");
                $mode = "yaml";
            }else{
                $dbconfig = parse_ini_file(SM_ROOT_DIR."Configuration/database.ini", true);
                $mode = "ini";
            }
            
            if(isset($dbconfig[$connection_name])){
        
    		    $ph = new SmartestParameterHolder($dbconfig[$connection_name]['name']);
		
    		    foreach($dbconfig[$connection_name] as $key => $value){
    		        $ph->setParameter($key, $value);
    		    }
		    
    		    $ph->setParameter('short_name', $connection_name);
		        
		        SmartestCache::save('dbc_'.$connection_name, $ph, -1, true);
                
                if($mode == 'yaml'){
                    SmartestCache::save('db_config_yaml_file_md5', $actual_md5, -1, true);
                }else{
                    SmartestCache::save('db_config_file_md5', $actual_md5, -1, true);
                }
		        
    		    return $ph;
		
    	    }else{
	        
    	        throw new SmartestDatabaseException("Unknown database connection configuration name: ".$connection_name, SmartestDatabaseException::INVALID_CONNECTION_NAME);
	        
    	    }
    	    
        }
    }
}