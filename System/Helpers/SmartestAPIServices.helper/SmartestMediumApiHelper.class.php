<?php

class SmartestMediumApiHelper implements SmartestOAuthController{
    
    public function __construct(){
        $this->_service = SmartestAPIServicesHelper::getOAuthService('SM_OAUTHSERVICE_MEDIUM');
    }
    
    public function requestOAuthKeysWithAuthTokenAndVerifier($token, $verifier){
        if(SmartestSession::get('medium_oauth_token') && SmartestSession::get('medium_oauth_token') == $token){
            $connection = $this->getConnectionWithSessionTokens();
            $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $verifier));
            $keys = new stdClass;
            $keys->oauth_token = $access_token['oauth_token'];
            $keys->oauth_token_secret = $access_token['oauth_token_secret'];
            $this->_account->setInfoValue('username', $access_token['screen_name']);
            $this->_account->setInfoValue('account_id', $access_token['user_id']);
            $this->_account->setUsername('oauth:medium:'.$access_token['screen_name']);
            $this->_account->save();
            return $keys;
        }else{
            return false;
        }
    }
    
    public function requestOAuth2AccessTokenWithAuthToken($token){
        
        $curl_uri = 'https://api.medium.com/v1/tokens';
        $curl_content_type = 'application/x-www-form-urlencoded';
        $redirect_uri = $this->_service->getCallbackUri();
        $client_id = $this->_account->getOAuthConsumerToken();
        $client_secret = $this->_account->getOAuthConsumerSecret();
        
        $string = 'code='.$token.'&client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=authorization_code&redirect_uri='.urlencode($redirect_uri);
        
        $response = SmartestHttpRequestHelper::rawCurlRequest($curl_uri, 'POST', $string);
        
        if($data = json_decode($response)){
            if(isset($data->access_token)){
                return $data->access_token;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    
    public function assignClientAccount(SmartestUser $account){
        $this->_account = $account;
    }
    
    public function getConnectionWithSessionTokens(){
        
    }
    
}