<?php

interface SmartestOAuthController{
    
    public function requestOAuth2AccessTokenWithAuthToken($token);
    public function assignClientAccount(SmartestUser $account);
    
}