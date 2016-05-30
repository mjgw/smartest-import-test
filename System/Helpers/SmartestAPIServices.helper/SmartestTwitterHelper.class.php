<?php

require_once SM_ROOT_DIR.'Library/TwitterOAuth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

class SmartestTwitterHelper implements SmartestOAuthController{
    
    protected $_account;
    protected $_service;
    protected $_connection;
    
    public function __construct(){
        $this->_service = SmartestAPIServicesHelper::getOAuthService('SM_OAUTHSERVICE_TWITTER');
    }
    
    public function requestOAuthKeysWithAuthTokenAndVerifier($token, $verifier){
        if(SmartestSession::get('twitter_oauth_token') && SmartestSession::get('twitter_oauth_token') == $token){
            $connection = $this->getConnectionWithSessionTokens();
            $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $verifier));
            $keys = new stdClass;
            $keys->oauth_token = $access_token['oauth_token'];
            $keys->oauth_token_secret = $access_token['oauth_token_secret'];
            $this->_account->setInfoValue('username', $access_token['screen_name']);
            $this->_account->setInfoValue('account_id', $access_token['user_id']);
            $this->_account->setUsername('oauth:twitter:'.$access_token['screen_name']);
            $this->_account->save();
            return $keys;
        }else{
            return false;
        }
    }
    
    public function requestOAuth2AccessTokenWithAuthToken($oauth_token){}
    
    public function assignClientAccount(SmartestUser $account){
        $this->_account = $account;
    }
    
    public function getAuthorizeUri(){
        $connection = $this->getConnection();
        $request_token = $this->getOAuthRequestToken();
        return $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    }
    
    public function getOAuthRequestToken(){
        $connection = $this->getConnection();
        $data = array(
            'oauth_callback'=>$this->_service->getCallbackUri().'?state='.$this->_account->getStateUri()
        );
        $request_token = $connection->oauth('oauth/request_token', $data);
        SmartestSession::set('twitter_oauth_token', $request_token['oauth_token']);
        SmartestSession::set('twitter_oauth_token_secret', $request_token['oauth_token_secret']);
        return $request_token;
    }
    
    public function getConnection(){
        if(!$this->_connection instanceof TwitterOAuth){
            if(is_object($this->_account)){
                if(strlen($this->_account->getOAuthAccessToken()) && strlen($this->_account->getOAuthAccessTokenSecret())){
                    $this->_connection = new TwitterOAuth($this->_account->getOAuthConsumerToken(), $this->_account->getOAuthConsumerSecret(), $this->_account->getOAuthAccessToken(), $this->_account->getOAuthAccessTokenSecret());
                }else{
                    $this->_connection = new TwitterOAuth($this->_account->getOAuthConsumerToken(), $this->_account->getOAuthConsumerSecret());
                }
            }else{
                // Account not provided!
                throw new SmartestException("Smartest client account info must be provided to Twitter OAuth controller with SmartestTwitterHelper::assignClientAccount()");
            }
            
        }
        return $this->_connection;
    }
    
    public function getConnectionWithSessionTokens(){
        $this->_connection = new TwitterOAuth($this->_account->getOAuthConsumerToken(), $this->_account->getOAuthConsumerSecret(), SmartestSession::get('twitter_oauth_token'), SmartestSession::get('twitter_oauth_token_secret'));
        return $this->_connection;
    }
    
    public function tweet($text){
        $connection = $this->getConnection();
        $connection->post("statuses/update", array("status" => $text));
    }
    
    public function getUser($id){
        $connection = $this->getConnection();
        if(is_numeric($id)){
            $user = $connection->get("users/show", array('user_id' => $id));
        }else{
            $user = $connection->get("users/show", array('screen_name' => $id));
        }
        $u = new SmartestTwitterUser($user);
        return $u;
    }
    
    public function getRecentTimeline($count=25, $exclude_replies=true){
        $connection = $this->getConnection();
        $tweets = $connection->get("statuses/home_timeline", array("count" => $count, "exclude_replies" => $exclude_replies));
        $translated_tweets = array();
        foreach($tweets as $rt){
            $translated_tweets[] = new SmartestTwitterStatus($rt);
        }
        return $translated_tweets;
    }
    
    public function getUserTimelineByUsername($username, $count=25, $exclude_replies=true){
        $connection = $this->getConnection();
        $tweets = $connection->get("statuses/user_timeline", array("screen_name"=> $username, "count" => $count, "exclude_replies" => $exclude_replies));
        $translated_tweets = array();
        foreach($tweets as $rt){
            $translated_tweets[] = new SmartestTwitterStatus($rt);
        }
        return $translated_tweets;
    }
    
}