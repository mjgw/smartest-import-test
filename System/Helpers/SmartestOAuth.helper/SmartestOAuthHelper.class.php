<?php

class SmartestOAuthHelper{
    
    private $database;
    
    public function __construct(){
        $this->database = SmartestDatabase::getInstance('SMARTEST');
    }
    
    public static function getServices(){
        
        $data = self::getServicesRaw();
        $services = array();
        
        foreach($data as $rs){
            $s = new SmartestOAuthService($rs);
            $services[$rs['id']] = $s;
        }
        
        return $services;
        
    }
    
    public static function getServicesRaw(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/oauth_services.yml');
        return $data['services'];
        
    }
    
    public static function getService($id_or_shortname){
        
        $rs = self::getServicesRaw();
        if(isset($rs[$id_or_shortname])){
            return new SmartestOAuthService($rs[$id_or_shortname]);
        }else{
            foreach($rs as $as){
                if($id_or_shortname == $as['shortname']){
                    return new SmartestOAuthService($as);
                }
            }
        }
        
    }
    
    public function getAccounts($service_id=null, $random_order=false){
        
        $order = $random_order ? 'RAND()' : 'user_firstname';
        
        if(isset($service_id)){
            if($service = self::getService($service_id)){
                $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' AND user_oauth_service_id='".$service->getParameter('id')."' ORDER BY ".$order);
            }else{
                // Service name not recognised
                $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY ".$order);
            }
        }else{
            $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY ".$order);
        }
        
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestOAuthAccount;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getServiceAccount($service, $username){
        
        $full_username = "oauth:".$service.':'.$username;
        $sql = "SELECT Users.* FROM Users WHERE username = '".$full_username."' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' LIMIT 1";
        // echo $sql;
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $acct = new SmartestOAuthAccount;
            $acct->hydrate($result[0]);
            return $acct;
        }else{
            return null;
        }
        
    }
    
}