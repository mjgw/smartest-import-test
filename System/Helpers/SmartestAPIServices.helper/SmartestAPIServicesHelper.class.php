<?php

class SmartestAPIServicesHelper{
    
    private $database;
    
    public function __construct(){
        $this->database = SmartestDatabase::getInstance('SMARTEST');
    }
    
    public static function getOAuthServices(){
        
        $data = self::getOAuthServicesRaw();
        $services = array();
        
        foreach($data as $rs){
            $s = new SmartestOAuthService($rs);
            $services[$rs['id']] = $s;
        }
        
        return $services;
        
    }
    
    public static function getOEmbedServices(){
        
        $data = self::getOEmbedServicesRaw();
        $services = array();
        
        foreach($data as $rs){
            $s = new SmartestOEmbedService($rs);
            $services[$rs['id']] = $s;
        }
        
        return $services;
        
    }
    
    public static function getOAuthServicesRaw(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/oauth_services.yml');
        return $data['services'];
        
    }
    
    public static function getOEmbedServicesRaw(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/oembed_services.yml');
        return $data['oembed'];
        
    }
    
    public static function getOAuthService($id_or_shortname){
        
        $rs = self::getOAuthServicesRaw();
        if(isset($rs[$id_or_shortname])){
            return new SmartestOAuthService($rs[$id_or_shortname]);
        }else{
            foreach($rs as $as){
                if($id_or_shortname == $as['shortname']){
                    return new SmartestOAuthService($as);
                }
            }
        }
        
    }
    
    public static function getOEmbedService($id_or_shortname){
        
        $rs = self::getOEmbedServicesRaw();
        if(isset($rs[$id_or_shortname])){
            return new SmartestOEmbedService($rs[$id_or_shortname]);
        }else{
            foreach($rs as $as){
                if($id_or_shortname == $as['shortname']){
                    return new SmartestOEmbedService($as);
                }
            }
        }
        
    }
    
    public function getAccounts($service_id=null, $random_order=false){
        
        $order = $random_order ? 'RAND()' : 'user_firstname';
        
        if(isset($service_id)){
            if($service = self::getOAuthService($service_id)){
                $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' AND user_oauth_service_id='".$service->getParameter('id')."' ORDER BY ".$order);
            }else{
                // Service name not recognised
                $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY ".$order);
            }
        }else{
            $raw_users = $this->database->queryToArray("SELECT Users.* FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY ".$order);
        }
        
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestOAuthAccount;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getServiceAccount($service, $username){
        
        $full_username = "oauth:".$service.':'.$username;
        $sql = "SELECT Users.* FROM Users WHERE username = '".$full_username."' AND user_type='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' LIMIT 1";
        // echo $sql;
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $acct = new SmartestOAuthAccount;
            $acct->hydrate($result[0]);
            return $acct;
        }else{
            return null;
        }
        
    }
    
    public function getOEmbedMarkupFromUrl($url){
        if($s = $this->getServiceFromUrl($url)){
            
            $url_hash = md5($url);
            $cache_filename = SM_ROOT_DIR.'System/Cache/TextFragments/OEmbed/oembed_'.$url_hash.'.html';
            
            if(SM_DEVELOPER_MODE){
                $last_ok_mtime = time() - 1;
            }else{
                // TODO: Make this a setting
                $last_ok_mtime = time() - 86400;
            }
            
            if(is_file($cache_filename) && filemtime($cache_filename) >= $last_ok_mtime){
                // Retrieve from cache
                return file_get_contents($cache_filename);
            }else{
                    
                $request_url = $s->getRequestUrlWithContentUrl($url);
                $content = SmartestHttpRequestHelper::getContent($request_url, false);
            
                if($s->getResponseType() == 'json'){
                    $data = json_decode($content);
                }elseif($s->getResponseType() == 'xml'){
                    // TODO: Decode XML response
                }
            
                if($s->getProvidesHtml()){
                    if(isset($data->html)){
                        // Write to cache
                        file_put_contents($cache_filename, $data->html);
                        return $data->html;
                    }else{
                        // HTML not provided in this case
                        return "<p class=\"sm-oembed-warning\">oEmbed error: HTML expected but not provided by ".$s->getParameter('provider_name')."</p>";
                    }
                }else{
                    // service does not provide HTML, so deal with this case
                    return "<p class=\"sm-oembed-warning\">Service does not provide HTML</p>";
                }
            }
            
        }else{
            throw new SmartestOEmbedUrlNotSupportedException("URL ".$url.' does not match any supported OEmbed services.');
        }
    }
    
    public function getSmartestOEmbedMarkupFromUrl($url, $width=null, $height=null){
        
        $url_hash = md5($url);
        if(!$width && !$height){
            $cache_filename = SM_ROOT_DIR.'System/Cache/TextFragments/OEmbed/oembed_local_'.$url_hash.'.html';
        }else{
            $cache_filename = SM_ROOT_DIR.'System/Cache/TextFragments/OEmbed/oembed_local_w'.$width.'_h'.$height.'_'.$url_hash.'.html';
        }
        
        if(SM_DEVELOPER_MODE){
            $last_ok_mtime = time() - 1;
        }else{
            // TODO: Make this a setting
            $last_ok_mtime = time() - 86400;
        }
        
        if(is_file($cache_filename) && filemtime($cache_filename) >= $last_ok_mtime){
            return file_get_contents($cache_filename);
        }else{
        
            $urlobj = new SmartestExternalUrl($url);
            $host = $urlobj->getHostName();
            $site = new SmartestSite();
            
            if($site->findBy('domain', $host)){
                if($page = $site->getContentByUrl($urlobj->getRequestString())){
                    // Access local oEmbed content without making a further HTTP request
                    $string = $page->getOembedIFrameMarkup($width, $height);
                    // Write to cache
                    file_put_contents($cache_filename, $string);
                }else{
                    // Fallback to accessing via HTTP, in case URL has moved to another server
                    $protocol = SmartestStringHelper::isSecureUrl($url) ? 'https://' : 'http://';
                    $request_url = $protocol.$host.'/embed?url='.urlencode($url);
                    $content = SmartestHttpRequestHelper::getContent($request_url, false);
                    
                    if($data = json_decode($content)){
                        $string = $data->html;
                        // Write to cache
                        file_put_contents($cache_filename, $data->html);
                    }else{
                        $string = '<!--Data from '.$request_url.' could not be retrieved-->';
                    }
                }
                
            }else{
                $string = '<p>No Smartest site could be found with the hostname '.$host.'.</p>';
            }
        
        }
        
        return $string;
        
    }
    
    public function urlIsValidService($url){
        $u = new SmartestExternalUrl($url);
        $test_url = $u->getWithoutProtocol();
        foreach($this->getOEmbedUrlPatterns() as $pattern){
            if(preg_match('/^'.$pattern.'/', $test_url, $matches)){
                return true;
            }
        }
        return false;
    }
    
    public function getServiceFromUrl($url){
        $u = new SmartestExternalUrl($url);
        $test_url = $u->getWithoutProtocol();
        foreach(self::getOEmbedServices() as $service){
            if(preg_match('/^'.$service->getUrlPattern().'/', $test_url, $matches)){
                return $service;
            }
        }
        return false;
    }
    
    public function getOEmbedUrlPatterns($url){
        $services_data = self::getOEmbedServicesRaw();
        $url_patterns = array();
        foreach($services_data as $sd){
            if(is_array($sd) && isset($sd['url_pattern'])){
                $url_patterns[] = $sd['url_pattern'];
            }
        }
        return $url_patterns;
    }
    
}