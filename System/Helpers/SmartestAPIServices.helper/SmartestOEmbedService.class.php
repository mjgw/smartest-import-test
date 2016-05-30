<?php

class SmartestOEmbedService extends SmartestParameterHolder{

    public function __construct($type_data){
        
        if(is_array($type_data) && isset($type_data['id'])){
            $this->_name = 'OEmbed service: '.$type_data['label'];
            $this->loadArray($type_data);
        }else{
            throw new SmartestException("Data given to SmartestOAuthService::__construct() is not an array or does not contain an 'id' offset");
        }
        
    }
    
    public function getUrlPattern(){
        return $this->getParameter('url_pattern');
    }
    
    public function getEndpointUrl(){
        return $this->getParameter('url');
    }
    
    public function getRequestUrlWithContentUrl($content_url){
        $bare_url = $this->getEndpointUrl();
        $final_url = str_replace('$URL', urlencode($content_url), $bare_url);
        return $final_url;
    }
    
    public function getResponseType(){
        return $this->getParameter('response_type');
    }
    
    public function getProvidesHtml(){
        return (bool) $this->getParameter('provides_html');
    }
    
    public function providesHtml(){
        return $this->getProvidesHtml();
    }

}