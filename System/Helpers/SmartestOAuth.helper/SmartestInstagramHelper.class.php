<?php

require_once SM_ROOT_DIR.'Library/InstagramOAuth/src/Instagram.php';
require_once SM_ROOT_DIR.'Library/InstagramOAuth/src/InstagramException.php';
use MetzWeb\Instagram\Instagram;

class SmartestInstagramHelper implements SmartestOAuthController{
    
    protected $_account;
    protected $_service;
    
    public function __construct(){
        $this->_service = SmartestOAuthHelper::getService('SM_OAUTHSERVICE_INSTAGRAM');
    }
    
    public function assignClientAccount(SmartestUser $account){
        $this->_account = $account;
    }
    
    public function requestOAuth2AccessTokenWithAuthToken($token){
        
        $i = new Instagram(array(
          'apiKey'      => $this->_account->getOAuthConsumerToken(),
          'apiSecret'   => $this->_account->getOAuthConsumerSecret(),
          'apiCallback' => $this->_service->getCallbackUri()
        ));
        
        $data = $i->getOAuthToken($token);
        $this->_account->setUsername('oauth:'.$this->_service->getParameter('shortname').':'.$data->user->username);
        $this->_account->setInfoValue('username', $data->user->username);
        $this->_account->setInfoValue('account_id', $data->user->id);
        
        return $data->access_token;
        
    }
    
    public function testAccessCredentials(){
        
        
        
    }
    
    public function getUserFeedFromId($id, $limit=20){
        
        $i = new Instagram(array(
          'apiKey'      => $this->_account->getOAuthConsumerToken(),
          'apiSecret'   => $this->_account->getOAuthConsumerSecret(),
          'apiCallback' => $this->_service->getCallbackUri()
        ));
        
        $i->setAccessToken($this->_account->getOAuthAccessToken());
        
        $data = $i->getUserMedia($id, $limit);
        $result = array();
        
        if(isset($data->data) && is_array($data->data)){
            foreach($data->data as $raw_post){
                $result[] = new SmartestInstagramPost($raw_post);
            }
        }
        
        return $result;
        
    }
    
    public function getUserFeed($username, $limit=20){
        
        $id = $this->getUserIdFromUsername($username);
        return $this->getUserFeedFromId($id, $limit);
        
    }
    
    public function getUserFromUsername($username){
        
        $i = new Instagram(array(
          'apiKey'      => $this->_account->getOAuthConsumerToken(),
          'apiSecret'   => $this->_account->getOAuthConsumerSecret(),
          'apiCallback' => $this->_service->getCallbackUri()
        ));
        
        $i->setAccessToken($this->_account->getOAuthAccessToken());
        
        $data = $i->searchUser($username);
        
        if(count($data->data)){
            if(strtolower($data->data[0]->username) == strtolower($username)){
                $user = new SmartestInstagramUser($data->data[0]);
                return $user;
            }
        }
        
    }
    
    public function getUserIdFromUsername($username){
        
        $i = new Instagram(array(
          'apiKey'      => $this->_account->getOAuthConsumerToken(),
          'apiSecret'   => $this->_account->getOAuthConsumerSecret(),
          'apiCallback' => $this->_service->getCallbackUri()
        ));
        
        // var_dump($this->_account->getOAuthAccessToken());
        $i->setAccessToken($this->_account->getOAuthAccessToken());
        // var_dump($i->getAccessToken());
        
        // 616712045
        // 8798111
        $data = $i->searchUser($username);
        
        if(strtolower($data->data[0]->username) == strtolower($username)){
            return $data->data[0]->id;
        }
        
    }

}