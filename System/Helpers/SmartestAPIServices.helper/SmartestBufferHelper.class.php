<?php

class SmartestBufferHelper implements SmartestOAuthController{

    protected $_account;
    protected $_service;
    
    public function __construct(){
        $this->_service = SmartestAPIServicesHelper::getOAuthService('SM_OAUTHSERVICE_BUFFER');
    }
    
    public function assignClientAccount(SmartestUser $account){
        $this->_account = $account;
    }
    
    public function requestOAuth2AccessTokenWithAuthToken($token){
        
        /* $i = new Instagram(array(
          'apiKey'      => $this->_account->getOAuthConsumerToken(),
          'apiSecret'   => $this->_account->getOAuthConsumerSecret(),
          'apiCallback' => $this->_service->getCallbackUri()
        ));
        
        $data = $i->getOAuthToken($token);
        $this->_account->setUsername('oauth:'.$this->_service->getParameter('shortname').':'.$data->user->username);
        $this->_account->setInfoValue('username', $data->user->username);
        $this->_account->setInfoValue('account_id', $data->user->id);
        
        return $data->access_token; */
            
        
        
    }

}