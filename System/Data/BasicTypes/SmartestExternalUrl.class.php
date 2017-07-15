<?php

class SmartestExternalUrl extends SmartestObject implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue, SmartestJsonCompatibleObject{
    
    protected $_value;
    protected $_curl_handle;
    protected $_api_helper;
    protected $_media_info;
    protected $_html_metadata;
    
    public function __construct($v=''){
        if(strlen($v)){
            $this->_value = $v;
        }
    }
    
    protected function getApiHelper(){
        if(!is_object($this->_api_helper)){
            $this->_api_helper = new SmartestAPIServicesHelper;
        }
        return $this->_api_helper;
    }
    
    public function setValue($v){
        $this->_value = SmartestStringHelper::toValidExternalUrl($v);
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function __toString(){
        return ''.$this->_value;
    }
    
    public function stdObjectOrScalar(){
        return $this->_value;
    }
    
    public function isPresent(){
        return (bool) strlen($this->_value);
    }
    
    public function getHostName(){
        preg_match('/^https?:\/\/([\w\.-]+)/', $this->_value, $matches);
        return $matches[1];
    }
    
    public function getRequestString(){
        preg_match('/^https?:\/\/([\w\.-]+)(\/(.*))?/', $this->_value, $matches);
        return $matches[2];
    }
    
    public function isOEmbedCompatible(){
        return $this->getApiHelper()->urlIsValidService($this->_value);
    }
    
    public function getOEmbedService(){
        if($this->isOEmbedCompatible()){
            return $this->getApiHelper()->getServiceFromUrl($this->_value);
        }
    }
    
    public function getOEmbedMarkup(){
        if($this->isOEmbedCompatible()){
            return $this->getApiHelper()->getOEmbedMarkupFromUrl($this->_value);
        }else{
            return 'No service available';
        }
    }
    
    public function getExternalMediaInfo(){
        
        if(!$this->_media_info instanceof SmartestParameterHolder){
            
            $alh = new SmartestAssetsLibraryHelper;
            $urls = $alh->getValidExternalUrlPatternsWithServices();
            $this->_media_info = new SmartestParameterHolder('URL Media Info for '.$this->_value);
            
            foreach($urls as $service){
                
                if(preg_match('/^'.$service['url_pattern'].'/', $this->_value, $matches)){
                    $this->_media_info->setParameter('data', $service, true);
                    $this->_media_info->setParameter('valid', true);
                    $this->_media_info->setParameter('url', $this->_value);
                    return $this->_media_info;
                }
            }
    
            $du = new SmartestDataUtility;
            $sites = $du->getSites();
            $hostname = $this->getHostName();
    
            foreach($sites as $s){
                if($s->getDomain() == $hostname){
                    if($s->getOEmbedEnabled()){
                        $this->_media_info->setParameter('data', array(
                            'label'=>'Content from your Smartest website \''.$s->getInternalLabel().'\'',
                            'type'=>'OEMBED_SMARTEST_SITE',
                            'service_id'=>'OEMBED_SMARTEST_SITE:'.$s->getId(),
                            'url_pattern'=>'^https?:\/\/'.str_replace('.', '\.', $s->getDomain()).'\/*',
                            'type_code' => 'SM_ASSETTYPE_OEMBED_URL'
                        ), true);
                        $this->_media_info->setParameter('valid', true);
                        $this->_media_info->setParameter('url', $this->_value);
                        return $this->_media_info;
                    }else{
                        $this->_media_info->setParameter('valid', false);
                        $this->_media_info->setParameter('url', $this->_value);
                        $this->_media_info->setParameter('message', 'The site \''.$s->getInternalLabel().'\' currently does not have OEmbed enabled.');
                        return $this->_media_info;
                    }
                }
            }
    
            $this->_media_info->setParameter('valid', false);
            $this->_media_info->setParameter('url', $this->_value);
            $this->_media_info->setParameter('message', 'URL is not a supported media provider.');
            
        }
        
        return $this->_media_info;
        
    }
    
    public function getHtmlMetaData(){
        
        if(!$this->_html_metadata){
            
            $this->_html_metadata = new SmartestParameterHolder('Html Metadata for '.$this->_value);
            
            if(SmartestHttpRequestHelper::curlInstalled()){
                
                $metas = SmartestHttpRequestHelper::getMetas($this->_value);
                $this->_html_metadata->setParameter('title', null);
                
                foreach($metas as $name => $value){
                    $this->_html_metadata->setParameter('meta_'.str_replace(':', '_', $name), $value);
                }
                
                if($this->_html_metadata->hasParameter('meta_og_title')){
                    $title = $this->_html_metadata->getParameter('meta_og_title')->g('value');
                }else{
                    $title = SmartestHttpRequestHelper::getTitle($this->_value);
                }
                
                $this->_html_metadata->setParameter('title', $title);
                
                $og_metas = SmartestHttpRequestHelper::getOpenGraphMetas($this->_value);
    
                if(count($og_metas) && isset($og_metas['og:image'])){
                    
                    $full_url = html_entity_decode($og_metas['og:image']);
                    $parts = explode('?', $og_metas['og:image']);
                    $part1 = $parts[0];
                    $og_url = new SmartestExternalUrl($part1);
                    $parts = explode('/', $og_url->getValue());
                    $filename = 'url_og_'.substr(md5($this->_value), 0, 16).'.'.SmartestStringHelper::getDotSuffix($og_url->getValue());
                    
                    if(file_exists(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/'.$filename)){
                        $img = new SmartestImage(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/'.$filename);
                        $this->_html_metadata->setParameter('og_image_file', $img);
                    }else{
                        if($saved_thumbnail_file = SmartestFileSystemHelper::saveRemoteBinaryFile($full_url, SM_ROOT_DIR.'Public/Resources/System/Cache/Images/'.$filename)){
                            $img = new SmartestImage($saved_thumbnail_file);
                            $this->_html_metadata->setParameter('og_image_file', $img);
                        }
                    }
                }
            
            }
        }
        
        return $this->_html_metadata;
        
    }
    
    public function getExternalMediaMarkup(){
        if($this->getExternalMediaInfo()->g('valid')){
            if($this->isOEmbedCompatible()){
                return $this->getApiHelper()->getOEmbedMarkupFromUrl($this->_value);
            }elseif($this->getExternalMediaInfo()->g('data')->g('type') == 'type'){
                // TODO: render URL as though it were externally translated asset
                return '[External translated asset markup]';
            }
        }else{
            return '<!--No embed code available: '.$this->getExternalMediaInfo()->g('message').'-->';
        }
    }
    
    public function getPreviewMarkup(){
	    $sm = new SmartyManager('InterfaceBuilder');
        $r = $sm->initialize(md5($this->_value));
        $content = $r->renderUrlPreview($this, $this->getHtmlMetaData());
        echo $content;
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_value;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // and two from SmartestSubmittableValue
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function renderInput($params){
        
    }
    
    /////////////////////////////
    
    public function getWithoutProtocol(){
        $str = preg_replace('/^(https?|feed|rss|atom|itunes):\/\//', '', $this->_value);
        return $str;
    }
    
    public function offsetExists($offset){
        return in_array($offset, array('_host', '_request', '_protocol', 'encoded', 'urlencoded', 'empty', 'url', 'string', 'is_valid'));
    }
    
    public function offsetGet($offset){
        switch($offset){
            case "_host":
            return $this->getHostName();
            case '_request':
            return $this->getValue();
            case '_protocol':
            return $this->getValue();
            case "encoded":
            case "urlencoded":
            return urlencode($this->getValue());
            case 'qr_code_url':
            return $this->getQrCodeUri();
            case 'qr_code_image':
            return $this->getQrCodeImage();
            case 'empty':
            return !strlen($this->getValue());
            case 'url':
            case 'string':
            return $this->__toString();
            case 'is_valid':
            return SmartestStringHelper::isValidExternalUri($this->_value);
            case 'is_supported_oembed_service':
            return $this->isOEmbedCompatible();
            case 'oembed_service':
            return $this->getOEmbedService();
            case 'external_media_info':
            return $this->getExternalMediaInfo();
            case 'external_media_embed_code':
            return $this->getExternalMediaMarkup();
            case 'preview_markup':
            return $this->getPreviewMarkup();
        }
    }
    
    public function offsetSet($offset, $value){}
    
    public function offsetUnset($offset){}
    
    public function getCurlHandle(){
        if(!$this->_curl_handle){
            $this->_curl_handle = curl_init($this->_value);
        }
        return $this->_curl_handle;
    }
    
    public function getCurlInfo(){
        $p = new SmartestParameterHolder("Curl Info for ".$this->_value);
        ob_start();
        $this->getCurlHandle();
        curl_exec($this->_curl_handle);
        ob_end_clean();
        $info = curl_getinfo($this->_curl_handle);
        curl_close($this->_curl_handle);
        $p->loadArray($info);
        return $p;
    }
    
    public function getHttpStatusCode(){
        $info = $this->getCurlInfo();
        return $info->g('http_code');
    }
    
    public function getQrCodeUri($encode=true, $size=200){
        if($encode){
            return 'http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&cht=qr&chld='.urlencode('L|0').'&chl='.urlencode($this->_value);
        }else{
            return 'http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&cht=qr&chld=L|0&chl='.$this->_value;
        }
    }
    
    public function getQrCodeImage($size=200){
        
        $local_filename = SM_ROOT_DIR.'Public/Resources/System/Cache/Images/qr_code_'.md5($this->_value).'.png';
        
        if(!is_file($local_filename)){
        
            try{
                SmartestFileSystemHelper::saveRemoteBinaryFile($this->getQrCodeUri(true, $size), $local_filename);
            }catch(SmartestException $e){
                SmartestLog::getInstance('system')->log('Remote PNG file could not be saved: '.$e->getMessage());
                return false;
            }
        
        }
        
        $img = new SmartestImage;
        $img->loadFile($local_filename);
        
        return $img;
        
    }
    
    public function downloadContentTo($local_file_path_without_root){
        if(is_dir(dirname(SM_ROOT_DIR.$local_file_path_without_root)) && is_writable(dirname(SM_ROOT_DIR.$local_file_path_without_root))){
            try{
                $file_name = SmartestFileSystemHelper::saveRemoteBinaryFile($this->_value, SM_ROOT_DIR.$local_file_path_without_root);
                return $file_name;
            }catch(SmartestException $e){
                SmartestLog::getInstance('system')->log('Remote file could not be saved: '.$e->getMessage());
                return false;
            }
        }
    }
    
}