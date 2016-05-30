<?php

class OAuthAccountsAjax extends SmartestSystemApplication{

    public function getPlatformClientDefaults(){
        
        if($service = SmartestAPIServicesHelper::getOAuthService($this->getRequestParameter('platform_id'))){
            
            $result = new stdClass;
            
            if((int) $service->getParameter('oauth_version') == 1){
                $result->ID = $service->getParameter('default_consumer_key');
                $result->secret = $service->getParameter('default_consumer_secret');
            }else{
                $result->ID = $service->getParameter('default_client_id');
                $result->secret = $service->getParameter('default_client_secret');
            }
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($result);
            exit;
            
        }
        
    }

}