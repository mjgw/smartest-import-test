<?php

class SmartestInstallation extends SmartestObject{
    
    protected $database;
    
    public function __construct(){
        if(is_object(SmartestPersistentObject::get('db:main'))){
            $this->database = SmartestPersistentObject::get('db:main');
        }else{
            $this->database = SmartestDatabase::getInstance('SMARTEST');
        }
    }
    
	public function getSites(){
	    
	    $sql = "SELECT * FROM Sites";
	    $result = $this->database->queryToArray($sql);
	    $sites = array();
	    
	    foreach($result as $s){
	        $site = new SmartestSite;
	        $site->hydrate($s);
	        $sites[] = $site;
	    }
	    
	    return $sites;
	    
	}
    
	public function getSystemUsers(){
	    $uh = new SmartestUsersHelper;
        return $uh->getSystemUsers();
	}
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'sites':
            return $this->getSites();
            
            case 'system_users':
            return $this->getSystemUsers();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}