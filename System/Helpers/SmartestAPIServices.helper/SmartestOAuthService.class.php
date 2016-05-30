<?php

class SmartestOAuthService extends SmartestParameterHolder{

    public function __construct($type_data){
        
        if(is_array($type_data) && isset($type_data['id'])){
            $this->_name = 'OAuth account type: '.$type_data['label'];
            $this->loadArray($type_data);
        }else{
            throw new SmartestException("Data given to SmartestOAuthService::__construct() is not an array or does not contain an 'id' offset");
        }
        
    }
    
    public function getCallbackUri(){
        
        return 'http://oauth.smartestproject.org/callback/'.$this->getParameter('shortname');
        
    }
    
    public function getFinalUri(){
        
        $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
        $request = SmartestPersistentObject::get('request_data');
        return $protocol.$_SERVER['HTTP_HOST'].$request->getParameter('domain').'smartest/oauth/callback/'.$this->getParameter('shortname');
        
    }

}