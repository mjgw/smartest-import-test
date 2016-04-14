<?php

class OAuthAccounts extends SmartestSystemApplication{
    
    public function __smartestApplicationInit(){
        if(!SmartestHttpRequestHelper::curlInstalled()){
            $this->addUserMessage("The cURL library is currently not installed. Most OAuth services will not work without this software.", SmartestUserMessage::WARNING, true);
        }
    }
    
    public function listClientAccounts(){
        
        $this->setTitle('OAuth Client Accounts');
        $this->setFormReturnUri();
        $this->setFormReturnDescription('OAuth accounts');
        $this->send($this->getUser()->hasToken('delete_oauth_accounts'), 'allow_delete');
        $h = new SmartestOAuthHelper;
        $this->send($h->getAccounts(), 'accounts');
        
    }
    
    public function addAccount(){
        
        $this->setTitle('Add an OAuth-based service client');
        $services = SmartestOAuthHelper::getServices();
        $this->send($services, 'services');
        
    }
    
    public function insertAccount(){
        
        $account = new SmartestOAuthAccount;
        $account->setLabel($this->getRequestParameter('oauth_service_label'));
        $account->setOAuthServiceId($this->getRequestParameter('oauth_service'));
        $account->setRegisterDate(time());
        $account->setUsername('oauth:'.SmartestStringHelper::randomFromFormat('LLNNNNLL'));
        $account->setPassword('x');
        $account->save();
        
        $this->formForward();
        
    }
    
    public function editAccount(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
            $this->send($account, 'account');
            $this->send($account->getService(), 'service');
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function updateAccount(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('oauth_account_id')) && $account->isOAuthClient()){
            
            $service = $account->getService();
            $oauth_version_int = (int) $service->getParameter('oauth_version');
            
            $account->setLabel($this->getRequestParameter('oauth_service_label'));
            
            if(strlen($this->getRequestParameter('oauth_consumer_token'))){
                if($this->getRequestParameter('oauth_consumer_token') == 'DEFAULT'){
                    if($oauth_version_int == 2 && strlen($service->getParameter('default_client_id'))){
                        $account->setOAuthConsumerToken($service->getParameter('default_client_id'));
                    }elseif(strlen($service->getParameter('default_consumer_key'))){
                        $account->setOAuthConsumerToken($service->getParameter('default_consumer_key'));
                    }
                }else{
                    $account->setOAuthConsumerToken($this->getRequestParameter('oauth_consumer_token'));
                }
            }
            
            if(strlen($this->getRequestParameter('oauth_consumer_secret'))){
                if($this->getRequestParameter('oauth_consumer_secret') == 'DEFAULT'){
                    if($oauth_version_int == 2 && strlen($service->getParameter('default_client_secret'))){
                        $account->setOAuthConsumerSecret($service->getParameter('default_client_secret'));
                    }elseif(strlen($service->getParameter('default_consumer_secret'))){
                        $account->setOAuthConsumerSecret($service->getParameter('default_consumer_secret'));
                    }
                }else{
                    $account->setOAuthConsumerSecret($this->getRequestParameter('oauth_consumer_secret'));
                }
            }
            
            // if(strlen($this->getRequestParameter('oauth_access_token'))){
                $account->setOAuthAccessToken($this->getRequestParameter('oauth_access_token'));
            // }
            
            // if(strlen($this->getRequestParameter('oauth_access_token_secret'))){
                $account->setOAuthAccessTokenSecret($this->getRequestParameter('oauth_access_token_secret'));
            // }
            
            $account->save();
            
            $this->addUserMessageToNextRequest("The account has been updated", SmartestUserMessage::SUCCESS);
            $this->formForward();
            
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function deleteClientAccount(){
        
        if($this->getUser()->hasToken('delete_oauth_accounts')){
            
            $account = new SmartestOAuthAccount;
        
            if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
                
                $account->delete();
                $this->addUserMessageToNextRequest("The account has been deleted.", SmartestUserMessage::INFO);
                $this->formForward();
                
            }else{
                $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
                $this->formForward();
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("You do not have permission to delete OAuth client accounts", SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
        
    }
    
    public function prepareAccessTokenRequestProcess(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
            
            $_SESSION['client_account_id'] = $account->getId();
            
            if($service = SmartestOAuthHelper::getService($account->getOAuthServiceId())){
                
                $oauth_version = (int) $service->getParameter('oauth_version');
                $this->send(($service->hasParameter('authorize_method') ? $service->getParameter('authorize_method') : 'get'), 'authorize_method');
                $this->send($account, 'account');
                $this->send($service, 'service');
                
                if($service->getParameter('token_request_url') == 'dynamic'){
                    $class = $service->getParameter('class');
                    $oauth_controller = new $class;
                    $oauth_controller->assignClientAccount($account);
                    $this->send($oauth_controller->getAuthorizeUri(), 'authorize_uri');
                }else{
                    if($oauth_version == 2){
                        $this->send($service->getCallbackUri(), 'redirect_uri');
                        $this->send($service->getFinalUri(), 'final_uri');
                    }else{
                        $this->send($service->getCallbackUri(), 'redirect_uri');
                        $this->send($service->getFinalUri(), 'final_uri');
                    }
                }
                
            }else{
                $this->addUserMessageToNextRequest("The specified OAuth service is not recognized.", SmartestUserMessage::ERROR);
                $this->redirect('@oauth_accounts');
            }
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function receiveOAuthCallback(){
        
        if(isset($_SESSION['client_account_id']) && is_numeric($_SESSION['client_account_id'])){
            $account = new SmartestOAuthAccount;
            
            if($account->find($_SESSION['client_account_id'])){
                
                $service = SmartestOAuthHelper::getService($this->getRequestParameter('service_shortname'));
                $class = $service->getParameter('class');
        
                $oauth_version = (int) $service->getParameter('oauth_version');
                $oauth_controller = new $class;
                $oauth_controller->assignClientAccount($account);
                unset($_SESSION['client_account_id']);
                
                if($oauth_version == 2){
                    if($access_token = $oauth_controller->requestOAuth2AccessTokenWithAuthToken($this->getRequestParameter('code'))){
                        $account->setOAuthAccessToken($access_token);
                        $account->save();
                        $this->addUserMessageToNextRequest("The ".$service->getParameter('label')." account has been successfully authorized.", SmartestUserMessage::SUCCESS);
                        $this->redirect('@oauth_accounts');
                    }else{
                        $this->addUserMessageToNextRequest("The ".$service->getParameter('label')." account has not been authorized.", SmartestUserMessage::WARNING);
                        $this->redirect('@oauth_accounts');
                    }
                }else{
                    if($keys = $oauth_controller->requestOAuthKeysWithAuthTokenAndVerifier($this->getRequestParameter('oauth_token'), $this->getRequestParameter('oauth_verifier'))){
                        $account->setOAuthAccessToken($keys->oauth_token);
                        $account->setOAuthAccessTokenSecret($keys->oauth_token_secret);
                        $account->save();
                        $this->addUserMessageToNextRequest("The ".$service->getParameter('label')." account has been successfully authorized.", SmartestUserMessage::SUCCESS);
                        $this->redirect('@oauth_accounts');
                    }else{
                        $this->addUserMessageToNextRequest("The ".$service->getParameter('label')." account has not been authorized.", SmartestUserMessage::WARNING);
                        $this->redirect('@oauth_accounts');
                    }
                }
                
            }
        }
    }
    
    public function testClientAccount(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
            
            $this->send($account, 'account');
            
        }
        
    }
    
    /* public function twitterSettings(){
        
        $this->setTitle('Site Twitter Account Settings');
        $this->send($this->getGlobalPreference('twitter_consumer_key'), 'twitter_consumer_key');
        $this->send($this->getGlobalPreference('twitter_consumer_secret'), 'twitter_consumer_secret');
        $this->send($this->getGlobalPreference('twitter_access_token'), 'twitter_access_token');
        $this->send($this->getGlobalPreference('twitter_access_token_secret'), 'twitter_access_token_secret');
        
        $token_request_callback_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->send($token_request_callback_url, 'callback_url');
        
        $token_request_signing_key = $this->getGlobalPreference('twitter_consumer_secret').'&';
        $this->send($token_request_signing_key, 'token_request_signing_key');
        
        $this->send(time(), 'timestamp');
        $this->send(SmartestStringHelper::random(32), 'nonce');
        
        $token_request_base_url = 'https://api.twitter.com/oauth/request_token?';
        
        $parameters = array();
        
        $parameters['oauth_consumer_key'] = $this->getGlobalPreference('twitter_consumer_key');
        $parameters['oauth_callback'] = $token_request_callback_url;
        $parameters['oauth_signature_method'] = 'HMAC-SHA1';
        $parameters['oauth_timestamp'] = time()-3600;
        $parameters['oauth_nonce'] = SmartestStringHelper::random(32);
        
        ksort($parameters);
        
        $base_string = 'GET&'.urlencode($token_request_base_url).'&'.urlencode(SmartestStringHelper::toUrlParameterString($parameters, false));
        $parameters['oauth_signature'] = base64_encode(SmartestStringHelper::toHmacSha1($base_string, $token_request_signing_key));
        ksort($parameters);
        
        $url = $token_request_base_url.SmartestStringHelper::toXmlEntities(SmartestStringHelper::toUrlParameterString($parameters));
        $this->send($url, 'token_request_url');
        
    } */
    
    /* public function updateTwitterSettings(){
        
        $this->setGlobalPreference('twitter_consumer_key', $this->getRequestParameter('twitter_consumer_key'));
        $this->setGlobalPreference('twitter_consumer_secret', $this->getRequestParameter('twitter_consumer_secret'));
        $this->setGlobalPreference('twitter_access_token', $this->getRequestParameter('twitter_access_token'));
        $this->setGlobalPreference('twitter_access_token_secret', $this->getRequestParameter('twitter_access_token_secret'));
        $this->formForward();
        
    } */
    
}