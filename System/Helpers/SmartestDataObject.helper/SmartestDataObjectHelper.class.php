<?php

class SmartestDataObjectHelper{
    
    protected $_dbconfig;
    protected $database;
    
    public function __construct(SmartestParameterHolder $dbconfig){
        $this->_dbconfig = $dbconfig;
        // $this->database = new SmartestMysql($this->_dbconfig['host'], $this->_dbconfig['username'], $this->_dbconfig['database'], $this->_dbconfig['password']);
    }
    
    static function getSchemaYamlData($file_path){
        $raw_data = SmartestYamlHelper::fastLoad($file_path);
        $data = $raw_data['types'];
        return $data;
    }
    
    static function getSchemaXmlData($file_path){
	    
	    $cache_name_hash = sha1_file($file_path).'_xml_file_hash';
	    $cache_name_data = sha1_file($file_path).'_xml_file_data';
        
	    $yml_cache_name_hash = sha1_file($file_path).'_yml_file_hash';
	    $yml_cache_name_data = sha1_file($file_path).'_yml_file_data';
	    
	    if(SmartestCache::hasData($yml_cache_name_hash, true)){
	        
	        $old_hash = SmartestCache::load($yml_cache_name_hash, true);
	        $new_hash = md5_file($file_path);
	        
	        if($old_hash != $new_hash){
	            SmartestCache::save($yml_cache_name_hash, $new_hash, -1, true);
	            $raw_data = SmartestYamlHelper::load($file_path);
	            $data = $raw_data['types'];
	            SmartestCache::save($yml_cache_name_data, $data, -1, true);
            }else{
                $data = SmartestCache::load($yml_cache_name_data, true);
            }
            
        }else{
            $new_hash = md5_file($file_path);
            SmartestCache::save($yml_cache_name_hash, $new_hash, -1, true);
            $raw_data = SmartestYamlHelper::load($file_path);
            $data = $raw_data['types'];
            SmartestCache::save($yml_cache_name_data, $data, -1, true);
        }
        
        return $data;
        
	}
	
	static function getBasicObjectSchemaXmlData(){
	    return self::getSchemaXmlData(SM_ROOT_DIR.'System/Core/Types/basicobjecttypes.xml');
	}
    
	static function getBasicObjectSchemaYamlData(){
	    return self::getSchemaYamlData(SM_ROOT_DIR.'System/Core/Types/basicobjecttypes.yml');
	}
	
	static function getBasicObjectSchemaInfo(){
	    
	    $data = self::getBasicObjectSchemaYamlData();
	    
	    $raw_types = $data;
	    $types = array();
	    
	    foreach($raw_types as $raw_type){
	        
	        $types[$raw_type['name']] = $raw_type;
	        
	        if(isset($types[$raw_type['name']]['noprefix'])){
	        
	            if(!is_array($types[$raw_type['name']]['noprefix'])){
	            
	                $types[$raw_type['name']]['noprefix'] = array($types[$raw_type['name']]['noprefix']);
	        
                }
            
            }else{
                
                $types[$raw_type['name']]['noprefix'] = array();
                
            }
            
	    }
	    
	    return $types;
	    
	}
	
	public function buildBasicObjects(){
	    
        $tables = self::getBasicObjectSchemaInfo();
        
        $last_revision_objects_built = SmartestCache::load('last_revision_objects_built', true);
        
        if(!is_numeric($last_revision_objects_built) || $last_revision_objects_built < SmartestInfo::$revision){
            $force = true;
            SmartestCache::save('last_revision_objects_built', SmartestInfo::$revision, -1, true);
        }else{
            $force = false;
        }
        
	    foreach($tables as $t){
	        
	        $this->buildBaseDataObjectFile($t, true, $force);
	        $this->buildDataObjectFile($t, true);
	        
	    }
	    
	}
	
	public static function buildDataObjectFile($table_info, $is_smartest=false){
	    
	    $class_name = $is_smartest ? 'Smartest'.$table_info['class'] : $table_info['class'];
	    $base_class_name = $is_smartest ? 'SmartestBase'.$table_info['class'] : 'Base'.$table_info['class'];
	    $directory = $is_smartest ? SM_ROOT_DIR.'System/Data/BasicObjects/' : SM_ROOT_DIR.'Library/ObjectModel/DataObjects/';
	    $file_name = $directory.$class_name.'.class.php';
	    
	    if(!is_dir($directory)){
	        if(@mkdir($directory)){
	            
	        }else{
	            throw new SmartestException("Couldn't create new directory: ".$directory);
	        }
	    }
	    
	    if(!file_exists($file_name)){
	        
	        $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/dataobject_template.txt');
	        $sp = $is_smartest ? 'Smartest' : '';
	        
	        $file_contents = str_replace('__CLASSNAME__', $class_name, $file_contents);
	        $file_contents = str_replace('__BASECLASSNAME__', $base_class_name, $file_contents);
	        
			SmartestFileSystemHelper::save($file_name, $file_contents);
	    }
	    
	}
	
	public static function buildBaseDataObjectFile($table_info, $is_smartest=false, $force=false){
	    
	    $class_name = $is_smartest ? 'SmartestBase'.$table_info['class'] : 'Base'.$table_info['class'];
	    $directory = $is_smartest ? SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/' : SM_ROOT_DIR.'Library/ObjectModel/DataObjects/Base/';
	    $file_name = $directory.$class_name.'.class.php';
        
        if(!is_dir($directory)){
	        if(@mkdir($directory)){
	            
	        }else{
	            throw new SmartestException("Couldn't create new directory: ".$directory);
	        }
	    }
	    
	    if($force || !file_exists($file_name)){
	        
	        $dbTableHelper = new SmartestDatabaseTableHelper('SMARTEST');
	        $columns = $dbTableHelper->getColumnNames($table_info['name']);
	        $offset = strlen($table_info['prefix']);
	        $pns = array();
	        
	        $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/basedataobject_template.txt');
	        $sp = $is_smartest ? 'Smartest' : '';
	        
	        $file_contents = str_replace('__CLASSNAME__', $class_name, $file_contents);
	        $file_contents = str_replace('__BASE_CLASS__', $table_info['class'], $file_contents);
            $file_contents = str_replace('__REVISION__', SmartestInfo::$revision, $file_contents);
	        
	        foreach($columns as $column){
		    
			    if(in_array($column, $table_info['noprefix'])){
				    $pn = $column;
				}else{
					$pn = substr($column, $offset);
				}
				
				$pns[] = $pn;
				
			}
			
			$file_contents = str_replace('__IS_SMARTEST__', $is_smartest ? 'true' : 'false', $file_contents);
			$file_contents = str_replace('__TABLE_PREFIX__', $table_info['prefix'], $file_contents);
			$file_contents = str_replace('__TABLE_NAME__', $table_info['name'], $file_contents);
			$file_contents = str_replace('__NO_PREFIX_FIELDS__', (isset($table_info['noprefix']) && count($table_info['noprefix'])) ? self::getNoPrefixFieldsAsString($table_info) : '', $file_contents);
			$file_contents = str_replace('__ORIGINAL_FIELDS__', "'".implode("', '", $columns)."'", $file_contents);
            if(isset($table_info['private'])){
                if($table_info['private'] == 'all'){
                    $private_fields = '\'_all\' => 1';
                }else{
                    $private_fields = count($table_info['private']) ? self::getPrivateFieldsAsString($table_info) : '';
                }
            }else{
                $private_fields = '';
            }
            $file_contents = str_replace('__PRIVATE_FIELDS__', $private_fields, $file_contents);
			
			$pac = self::buildBasePropertiesArray($pns);
			$file_contents = str_replace('__PROPERTIES_ARRAY__', $pac, $file_contents);
			
			$fc = self::buildBaseFunctions($pns);
			$file_contents = str_replace('__FUNCTIONS__', $fc, $file_contents);
			
	        SmartestFileSystemHelper::save($file_name, $file_contents);
	    }
	    
	}
	
	public static function buildBasePropertiesArray($pns){
	    
	    $pac = '    protected $_properties = array('."\n";
		
		$i = count($pns);
		
		foreach($pns as $pn){
		    
		    if(strlen($pn)){
		        
		        $pac .= "        '".$pn."' => ''";
		        
		        if($i>1){
		            $pac .= ",";
		        }
		        
		        $i--;
		        
		        $pac .= "\n";
		        
	        }
		    
		}
		
		$pac .= '    );'."\n";
		
		return $pac;
	    
	}
	
	public static function buildBaseFunctions($pns){
	    
	    $file_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/dataobject_datafunctions.txt');
	    
        $fc = '';
        
	    foreach($pns as $pn){
	        
		    if(strlen($pn)){
		        $f = str_replace('__PROPNAME__', $pn, $file_contents);
		        $f = str_replace('__FUNCNAME__', SmartestStringHelper::toCamelCase($pn), $f);
		        $fc .= $f;
	        }
		    
		}
		
		return $fc;
	    
	}
	
	public static function getNoPrefixFieldsAsString($table_info){
	    
	    $string = '';
	    
        if(isset($table_info['noprefix'])){
	        $i = count($table_info['noprefix']);
	        
	        foreach($table_info['noprefix'] as $npf){
	            $string .= "'".$npf."' => 1";
	            
	            if($i>1){
		            $string .= ", ";
		        }
		        
		        $i--;
	        }
        }
	    
	    return $string;
	}
    
	public static function getPrivateFieldsAsString($table_info){
	    
	    $string = '';
	    
        if(isset($table_info['private'])){
            $i = count($table_info['private']);
            foreach($table_info['private'] as $npf){
    	        $string .= "'".$npf."' => 1";
        
    	        if($i>1){
    		        $string .= ", ";
    		    }
	    
    		    $i--;
    	    }
        }
	    
	    return $string;
	}
	
	public static function loadInterfaces(){
	    
	    $directory = SM_ROOT_DIR.'System/Data/Interfaces/';
	    $files = SmartestFileSystemHelper::load($directory);
	    
	    foreach($files as $f){
	        include $directory.$f;
	    }
	    
	}
	
	public function loadBasicObjects(){
		
		$available_objects = SmartestCache::load('smartest_available_objects', true);
        
		$singlefile = '';
		
		$this->buildBasicObjects();
		
		// find the tables info if this hasn't already been done
		
		$tables = self::getBasicObjectSchemaInfo();
		
		$object_types = array();
		
		$object_type_cache_string = '';
		
		foreach($tables as $t){
		    $base_class_name = 'SmartestBase'.$t['class'];
		    $class_name = 'Smartest'.$t['class'];
    	    $directory = SM_ROOT_DIR.'System/Data/BasicObjects/';
    	    $base_directory = SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/';
    	    $file_name = $directory.$class_name.'.class.php';
    	    $base_file_name = $base_directory.$base_class_name.'.class.php';
		    $object_type_cache_string .= sha1_file($file_name);
		    $object_type_cache_string .= sha1_file($base_file_name);
		}
		
		$basic_object_cache_hash = sha1($object_type_cache_string);
		
		$use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_basic_objects_hash', true) != $basic_object_cache_hash) || ($use_cache && !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php')));
	
	    if($use_cache){
	        if($rebuild_cache){
	            $singlefile .= file_get_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'BasicObjects'.DIRECTORY_SEPARATOR.'SmartestDataObject.class.php');
	        }
	    }else{
	        include SM_ROOT_DIR.'System/Data/BasicObjects/SmartestDataObject.class.php';
	    }
	
		foreach($tables as $t){
		    
		    $base_class_name = 'SmartestBase'.$t['class'];
		    $class_name = 'Smartest'.$t['class'];
    	    $directory = SM_ROOT_DIR.'System/Data/BasicObjects/';
    	    $base_directory = SM_ROOT_DIR.'System/Cache/ObjectModel/DataObjects/';
    	    $file_name = $directory.$class_name.'.class.php';
    	    $base_file_name = $base_directory.$base_class_name.'.class.php';
		    
			if(is_file($file_name) && is_file($base_file_name)){
				if($use_cache){
				    if($rebuild_cache){
				        $singlefile .= file_get_contents($base_file_name);
				        $singlefile .= file_get_contents($file_name);
			        }else{
    			        // don't need to include anything because types are already in cache
    			    }
				}else{
				    // Include the original file rather than the cache
				    require $base_file_name;
				    require $file_name;
				}
			}else{
				// File was there amoment ago but has now disappeared (???)
			}
		}
		
		if($rebuild_cache){
	        
	        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<"."?php\n\n// Cache of Basic Data Objects\n\n// Auto-generated by SmartestDataObjectHelper - Do Not Edit".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_basic_objects_hash', $basic_object_cache_hash, -1, true);
		    
		}
		
        if($use_cache){
	        include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestBasicObjects.cache.php';
	    }
	
	}
	
	static function loadExtendedObjects($directory='__SYSTEM_DEFAULT'){
	    // echo "LEO";
	    if($directory == '__SYSTEM_DEFAULT'){
	        $directory = SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Data'.DIRECTORY_SEPARATOR.'ExtendedObjects'.DIRECTORY_SEPARATOR;
	    }
		
		$available_objects = SmartestCache::load('smartest_available_extended_objects', true);
		
		$singlefile = '';
		
		// find the helpers if this hasn't already been done
		$object_types = array();
		
		$object_type_cache_string = '';
		
		if($res = opendir($directory)){
			
			while (false !== ($file = readdir($res))) {
    		
    			if(preg_match('/^([A-Z][A-Za-z0-9]+)\.class\.php$/', $file, $matches)){
    				$object_type = array();
    				$object_type['name'] = $matches[1];
    				$object_type['file'] = $directory.$matches[0];
    				$object_type_cache_string .= sha1_file($object_type['file']);
    				$object_types[] = $object_type;
    			}
    		
			}
		
			closedir($res);
			
			$extended_object_cache_hash = sha1($object_type_cache_string);
			
			// SmartestCache::save('smartest_available_extended_objects', $object_types, -1, true);
	
		}
		
		$use_cache = (defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')) ? false : true;
		$rebuild_cache = ($use_cache && (SmartestCache::load('smartest_extended_objects_hash', true) != $extended_object_cache_hash || !is_file(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php')));
	    // var_dump($use_cache);
	    foreach($object_types as $h){
			if(is_file($h['file'])){
				if($use_cache){
				    if($rebuild_cache){
				        $singlefile .= file_get_contents($h['file']);
			        }else{
    			        // don't need to include anything because types are already in cache
    			    }
				}else{
				    // echo "including ".$h['file'];
				    // Include the original file rather than the cache
				    include $h['file'];
				    
				    // echo "<br />";
				}
			}else{
				// File was there a moment ago but has now disappeared (???)
			}
		}
		
		if($rebuild_cache){
		    
	        $singlefile = str_replace('<'.'?php', "\n", $singlefile);
			$singlefile = str_replace('?'.'>', "\n\n", $singlefile);
			$singlefile = "<"."?php\n\n// Cache of Extended Data Objects\n\n// Auto-generated by SmartestDataUtility - Do Not Edit".$singlefile;
		    
		    file_put_contents(SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php', $singlefile);
		    
		    SmartestCache::save('smartest_extended_objects_hash', $extended_object_cache_hash, -1, true);
		}
		
		if($use_cache){
	        include SM_ROOT_DIR.'System'.DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'SmartestExtendedObjects.cache.php';
	    }
	
	}
    
}