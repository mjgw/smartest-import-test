<?php

require_once SM_ROOT_DIR.'Library/BitlyPHP/bitly.php';

class SmartestBitlyHelper implements SmartestOAuthController{

    protected $_account;
    protected $_service;
    
    public function __construct(){
        $this->_service = SmartestOAuthHelper::getService('SM_OAUTHSERVICE_BITLY');
    }
    
    public function assignClientAccount(SmartestUser $account){
        $this->_account = $account;
    }
    
    public function requestOAuth2AccessTokenWithAuthToken($token){
        
        $results = bitly_oauth_access_token($token, $this->_service->getCallbackUri(), $this->_account->getOAuthConsumerToken(), $this->_account->getOAuthConsumerSecret());
         
        if(isset($results['access_token'])){
            
            $this->_account->setUsername('oauth:'.$this->_service->getParameter('shortname').':'.$results['login']);
            $this->_account->setInfoValue('username', $results['login']);
            $this->_account->setInfoValue('bitly_api_key', $results['apiKey']);
            
            return $results['access_token'];
            
        }
        
    }

}