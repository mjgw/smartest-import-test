<?php

use Elasticsearch\ClientBuilder;

class SmartestElasticSearchHelper{
    
    public static $is_operational = null;
    
    public static function include_files(){
        
        if(SmartestSystemHelper::elasticSearchIsPossible()){
            
            require_once __DIR__.'/vendor/autoload.php';
            
        }
        
    }
    
    public static function elasticSearchIsOperational(){
        
        if(self::$is_operational === null){
            if(SmartestSystemHelper::elasticSearchIsPossible()){
                if(is_object(SmartestDataUtility::getCurrentSite())){
                    $site = SmartestDataUtility::getCurrentSite();
                    $ph = new SmartestPreferencesHelper;
                    $preference_value = $ph->getGlobalPreference('site_search_type', null, $site->getId());
                    $search_type = $preference_value == 'ELASTICSEARCH' ? 'ELASTICSEARCH' : 'BASIC';
                    self::$is_operational = ($search_type == 'ELASTICSEARCH');
                }else{
                    self::$is_operational = false;
                }
            }else{
                self::$is_operational = false;
            }
            
        }
        
        return self::$is_operational;
        
    }
    
    /* public static function init(SmartstSite $site){
        
    } */
    
    public static function indexExists($index_name){
        $client = self::getClient();
        $indexParams['index'] = $index_name;
        return $client->indices()->exists($indexParams);
    }
    
    public static function deleteIndex($index_name){
        if(self::elasticSearchIsOperational()){
            $client = self::getClient();
            $response = $client->indices()->delete(array(
                'index' => $index_name
            ));
            return $response['acknowledged'];
        }
    }
    
    public static function itemIsIndexed($params){
        try{
            if(self::elasticSearchIsOperational()){
                $client = self::getClient();
                $response = $client->exists($params);
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        // var_dump($response);
        return $response;
    }
    
    public static function addItemToIndex($params){
        try{
            if(self::elasticSearchIsOperational()){
                $client = self::getClient();
                $response = $client->index($params);
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        return $response;
    }
    
    public static function addBulkItemsToIndex($params){
        try{
            if(self::elasticSearchIsOperational()){
                $client = self::getClient();
                $response = $client->bulk($params);
                return $response;
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        return $response;
    }
    
    public static function updateItem($params){
        try{
            if(self::elasticSearchIsOperational()){
                $client = self::getClient();
                $response = $client->update($params);
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        return $response;
    }
    
    public static function deleteItem($params){
        try{
            if(self::elasticSearchIsOperational()){
                $client = self::getClient();
                $response = $client->delete($params);
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        return $response;
    }
    
    public static function getItemsMatchingQuery($params){
        
        try{
            if(self::elasticSearchIsOperational()){
                
                // $params = array();
                
                $client = self::getClient();
                $response = $client->search($params);
            }
        }catch(ServerErrorResponseException $e){
            $response = array(
                'error'=>true,
                'error_msg'=>$e->getMessage()
            );
        }
        return $response;
        
    }
    
    public static function addPageToIndex(){
        
        if(self::elasticSearchIsOperational()){
            
        }
        
    }
    
    public static function isRunning(){
        if(SmartestSystemHelper::elasticSearchIsPossible()){
            try{
                $client = ClientBuilder::create()->build();
                
                try{
                    $client->indices()->status(array(
                        'index'=>'_all'
                    ));
                    return true;
                }catch(Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
                    return false;
                }
                
            }catch(Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
                return false;
            }
        }else{
            return false;
        }
    }
    
    public static function getClient(){
        try{
            
            $client = ClientBuilder::create()->build();
            
            try{
                return $client;
            }catch(Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
                return false;
            }
            
        }catch(Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
            return false;
        }
    }
    
    public static function getSystemInfo(){
        if($client = self::getClient()){
            $info = $client->info(array());
            $ph = new SmartestParameterHolder('Elasticsearch system info');
            $ph->loadArray($info);
            return $info;
        }else{
            return array();
        }
    }
    
    public static function getIndices($site_id=null){
        if($client = self::getClient()){
            $params = array(
                'index'=>'_all'
            );
            
            $raw_indices = $client->indices()->status($params);
            
            $indices = array();
            
            foreach($raw_indices['indices'] as $k=>$ind){
                $index = new SmartestElasticSearchIndex($k, $ind);
                $indices[$k] = $index;
            }
            
            return $indices;
        }else{
            return array();
        }
    }
    
    public static function init(){
        self::include_files();
    }
    
}