<?php

class SmartestAsset extends SmartestBaseAsset implements SmartestSystemUiObject, SmartestStorableValue, SmartestSubmittableValue, SmartestDualModedObject{
    
    protected $_allowed_types = array();
    protected $_draft_mode = false;
    protected $_text_fragment;
    protected $_type_info = null;
    protected $_site;
    protected $_image;
    protected $_save_textfragment_on_save = false;
    protected $_set_textfragment_asset_id_on_save = false;
    protected $_set_textfragment_id_on_save = false;
    protected $_absolute_uri_object;
    protected $_thumbnail_asset = null;
    protected $_default_parameter_values = null;
    protected $_asset_info = null;
    
    protected function __objectConstruct(){
        $this->_asset_info = new SmartestDbStorageParameterHolder("Asset info");
    }
    
	public function __postHydrationAction(){
	    
	    if(!$this->_asset_info){
	        $this->_asset_info = new SmartestDbStorageParameterHolder("Info for asset ID '".$this->_properties['id']."'");
        }
        
        $this->_asset_info->loadArray(unserialize($this->_properties['info']));
	    
	}
    
    public function __toArray($include_object=false, $include_owner=false){
        
	    $data = parent::__toArray();
	    
	    $data['text_content'] = $this->getContent();
	    $data['type_info'] = $this->getTypeInfo();
	    $data['default_parameters'] = $this->getDefaultParams();
	    
	    if($data['type_info']['storage']['type'] == 'database'){
	        $data['full_path'] = $this->_properties['url'];
	    }else{
	        $data['full_path'] = $this->getFullPathOnDisk();
        }
        
        $data['size'] = $this->getSize();
	    
	    if($include_owner){
	        $o = new SmartestUser;
	        if($o->hydrate($this->_properties['user_id'])){
	            $data['owner'] = $o->__toArray();
            }else{
                $data['owner'] = array();
            }
        }
	    
	    // if($this->isImage()){
            $data['width'] = $this->getWidth();
            $data['height'] = $this->getHeight();
        // }
        
        if($include_object){
            $data['_object'] = $this;
        }
        
	    return $data;
	}
    
    public function __toSimpleObject(){
        
        $obj = parent::__toSimpleObject();
        $obj->type_info = $this->getTypeInfo();
        $obj->is_binary_image = $this->isBinaryImage();
        $obj->is_web_accessible = $this->isWebAccessible();
        $obj->mime_type = $this->getMimeType();
        if($this->isWebAccessible()){
            $obj->uri = $this->getAbsoluteUri();
        }
        if($this->getType() == 'SM_ASSETTYPE_OEMBED_URL' && is_object($this->getOembedService())){
            $obj->oembed_service = $this->getOembedService()->getArray();
        }
        return $obj;
    }
    
    public function __toSimpleObjectForParentObjectJson(){
        
        $obj = new stdClass;
        $obj->id = (int) $this->getId();
        $obj->webid = $this->getWebId();
        $obj->label = $this->getLabel();
        $obj->type = $this->getType();
        $obj->mime_type = $this->getMimeType();
        $obj->size = $this->getSize(true);
        $obj->object_type = 'file';
        
        if($this->isWebAccessible()){
            $obj->uri = $this->getAbsoluteUri(true);
        }elseif($this->isExternal()){
            $obj->uri = $this->getUrl();
        }
        
        $obj->download_uri = $this->getAbsoluteDownloadUri();
        
        if($this->usesTextFragment()){
            $obj->word_count = $this->getWordCount();
        }
        if($this->usesTextFragment() && $this instanceof SmartestRenderableAsset){
            $obj->content = $this->render();
        }elseif($this->isEditable()){
            if($this->usesTextFragment()){
                $obj->content = strip_tags($this->getContent(true));
            }else{
                $obj->content = $this->getContent(true);
            }
        }
        
        return $obj;
        
    }
	
	public function offsetGet($offset){
        
        switch($offset){
            
            case "text":
            case "text_content":
            return $this->getContent();
            
            case "description":
            case "caption":
            return $this->getDescription();
            
            case "text_fragment":
            return $this->getTextFragment();
            
            case "type_info":
            case "typeinfo":
            return $this->getTypeInfo();
            
            case "type":
            return $this->_properties['type'];
            
            case "label":
            return new SmartestString($this->getLabel());
            
            case "default_parameters":
            return $this->getDefaultParams();
            
            case "_editor_parameters":
            return $this->getEditorParams();
            
            case "full_path":
            $type_info = $this->getTypeInfo();
            if($type_info['storage']['type'] == 'database'){
    	        return $this->_properties['url'];
    	    }else{
    	        return $this->getFullPathOnDisk();
            }
            
            case "absolute_uri":
            case "absolute_url":
            return $this->getAbsoluteUri();
            
            case "storage_location":
            return $this->getStorageLocation();
            
            case "is_external":
            return $this->isExternal();
            
            case "web_path":
            if($this->usesLocalFile()){
                return $this->getFullWebPath();
            }
            break;
            
            case "encoded_url":
            return $this->getEncodedFileName();
            
            case "bg_css":
            if($this->isImage()){
                return 'background-image:url(\''.addslashes($this->getFullWebPath()).'\')';
            }
            break;
            
            case "size":
            return $this->getSize();
            
            case "raw_size":
            case "size_raw":
            return $this->getSize(true);
            
            case "modified":
            return $this->getModified();
            
            case "owner":
            $o = new SmartestSystemUser;
	        if($o->find($this->_properties['user_id'])){
	            return $o;
            }else{
                return array();
            }
            
            case "is_image":
            return $this->isImage();
            
            case "is_binary_image":
            return $this->isBinaryImage();
            
            case "thumbnail_image":
            return $this->getThumbnailImage();
            
            case "html_friendly":
            case "can_appear_on_web_page":
            return $this->isHtmlFriendly();
            
            case "is_web_accessible":
            return ($this->isExternal() || $this->isWebAccessible());
            
            case "image":
            case "img":
            return $this->isBinaryImage() ? $this->getImage() : null;
            
            case "width":
            return $this->getWidth();
            
            case "height":
            return $this->getHeight();
            
            case "dimensions":
            return $this->getWidth().' x '.$this->getHeight();
            
            case "word_count":
            case "wordcount":
            return $this->getWordCount();
            
            case "text_length":
            return $this->getTextLength();
            
            case "text_length_no_tags":
            case "text_length_no_html":
            case "text_length_without_html":
            return $this->getTextLengthWithoutHTML();
            
            case "credit":
            return $this->isImage() ? $this->getCredit() : null;
            
            case "groups":
            return $this->getGroups();
            
            case "small_icon":
            return $this->getSmallIcon();
            
            case "large_icon":
            return $this->getLargeIcon();
            
            case "label":
            return $this->getLabel();
            
            case "action_url":
            return $this->getActionUrl();
            
            case "site":
            return $this->getSite();
            
            case "download_link_contents":
            return 'download:'.$this->getUrl();
            
            case "download_uri":
            case "download_url":
            return $this->getAbsoluteDownloadUri();
            
            case "secure_download_uri":
            case "secure_download_url":
            return $this->getAbsoluteDownloadUri(true);
            
            case "link_contents":
            if($this->isImage()){
                return 'image:'.$this->getUrl();
            }else{
                return 'download:'.$this->getUrl();
            }
            break;
            
            case "file_size":
            return $this->getSize();
            
            case "raw_file_size":
            return $this->getSize(true);
            
            case "empty":
            return !is_numeric($this->getId()) || !strlen($this->getId());
            
            case "is_too_large":
            return $this->isTooLarge();
            
            case "mime_type":
            return $this->getMimeType();
            
            case "tags":
            return $this->getTags();
            
            case "fa_icon":
            case "icon_name":
            return $this->getFontAwesomeIcon();
            
            case "icon_code":
            return $this->getIconCode();
            
        }
        
        return parent::offsetGet($offset);
        
    }
	
	public function __toString(){
	    
	    if($this->_properties['id']){
	        return $this->_properties['label'].' ('.$this->_properties['url'].')';
        }else{
            return '';
        }
        
	}
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_properties['id'];
    }
    
    public function hydrateFromStorableFormat($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }
	
	public function getSite(){
	    
	    if($this->_site){
	        return $this->_site;
	    }else{
	        $site = new SmartestSite;
	        
            if($site->find($this->getSiteId())){
                // echo "site found";
	            $this->_site = $site;
	            return $this->_site;
	        }else{
	            return null;
	        }
	    }
	    
	}
	
	public function assignSiteFromObject(SmartestSite $site){
	    if($this->getSiteId() == $site->getId()){
	        $this->_site = $site;
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function getWidth(){
	    
	    if($this->isBinaryImage()){
            
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    
            return $this->_image->getWidth();
            
		}else{
            
            $dp = $this->getDefaultParams();
            
            if(is_array($dp) && array_key_exists('width', $dp) && (is_numeric($dp['width']) || $dp['width'] instanceof SmartestNumeric)){
                return (string) $dp['width'];
            }elseif(preg_match('/width="(\d+(\.\d+)?)"/i', $this->getContent(true), $matches)){
                if(strpos($matches[1], '.')){
                    return ceil((float) $matches[1]);
                }else{
                    return $matches[1];
                }
            }
		}
	    
	}
	
	public function getHeight(){
	    
	    if($this->isBinaryImage()){
            
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
            
		    return $this->_image->getHeight();
            
		}else{
            
            $dp = $this->getDefaultParams();
            
            if(is_array($dp) && array_key_exists('height', $dp) && (is_numeric($dp['height']) || $dp['height'] instanceof SmartestNumeric)){
                return (string) $dp['height'];
            }elseif(preg_match('/height="(\d+(\.\d+)?)"/i', $this->getContent(true), $matches)){
                if(strpos($matches[1], '.')){
                    return ceil((float) $matches[1]);
                }else{
                    return $matches[1];
                }
            }
            
		}
	}
	
	public function getWordCount(){
	    
	    if($this->usesTextFragment() && is_object($this->getTextFragment())){
	        return $this->getTextFragment()->getWordCount();
	    }else{
	        return 0;
	    }
	    
	}
	
	public function getTextLength(){
	    
	    if($this->usesTextFragment()){
	        return $this->getTextFragment()->getLength();
	    }else{
	        return 0;
	    }
	    
	}
    
	public function getTextLengthWithoutHTML(){
	    
	    if($this->usesTextFragment()){
	        return $this->getTextFragment()->getLengthWithoutHTML();
	    }else{
	        return 0;
	    }
	    
	}
	
	public function getTypeInfo(){
	    
	    if(!$this->_type_info){
	        
	        $asset_types = SmartestDataUtility::getAssetTypes();
	        
	        if(array_key_exists($this->getType(), $asset_types)){
	            $this->_type_info = $asset_types[$this->getType()];
	        }else{
	            // some sort of error? unsupported type
	        }
	        
	    }
	    
	    return $this->_type_info;
	    
	}
    
    public function getModified(){
        
        if($this->usesLocalFile() && is_file($this->getFullPathOnDisk())){
            
            $db_mtime = (int) parent::getModified();
            $file_mtime = (int) filemtime($this->getFullPathOnDisk());
            
            if($file_mtime > $db_mtime){
                $this->database->rawQuery('UPDATE Assets SET asset_modified='.$file_mtime.' WHERE asset_id="'.$this->getId().'" LIMIT 1');
            }
            
            return max($db_mtime, $file_mtime);
            
        }else{
            return parent::getModified();
        }
        
    }
    
    public function getCategory(){
        
        $info = $this->getTypeInfo();
        return isset($info['category']) ? $info['category'] : 'unknown';
        
    }
	
	public function getMimeType(){
	    
	    $info = $this->getTypeInfo();
	    
	    if(!isset($info['suffix'])){
	        // the file type doesn't have any suffixes, so is probably externally hosted or not a real file type
	        return null;
	    }
	    
	    $suffixes = $info['suffix'];
	    $mysuffix = $this->getDotSuffix();
	    
	    if(count($suffixes) == 1){
	        return $info['suffix'][0]['mime'];
	    }else{
	        foreach($info['suffix'] as $suffix){
	            if($suffix['_content'] == $mysuffix){
	                return $suffix['mime'];
	            }
	        }
	        // if the file's suffix doesn't match any of those listed against its type, there is a problem, but never mind
	        return $info['suffix'][0]['mime'];
	    }
	    
	}
	
	public function usesTextFragment(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($info['storage']['type'] == 'database'){
	        return true;
	    }else{
	        // this type of asset doesn't use table TextFragments
	        return false;
	    }
	    
	}
	
	public function usesLocalFile(){
	    $info = $this->getTypeInfo();
	    return (isset($info['storage']) && $info['storage']['type'] == 'file');
	}
    
    public function connectTextFragmentOnSave(){
        
        $this->_save_textfragment_on_save = true;
        $this->_set_textfragment_id_on_save = true;
        
    }
	
	public function getTextFragment(){
	    
	    // no text fragment has been retrieved
	    if(!$this->_text_fragment){
	        
	        if($this->usesTextFragment()){
    	        
    	        $tf = new SmartestTextFragment;
    	        
    	        if($this->getFragmentId()){
	                
	                if(!$tf->find($this->getFragmentId())){
	                    
	                    // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
                        $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        $this->setField('fragment_id', $tf->getId());
                        $this->_save_textfragment_on_save = true;
                        $this->_set_textfragment_id_on_save = true;
                        SmartestLog::getInstance('system')->log("Text asset '".$this->getLabel()."' with ID ".$this->getId()." did not have an associated TextFragment. A new one was created.");
                    }
                    
                    $this->_text_fragment = $tf;
                    
    	        }else{
    	            
    	            // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
    	            if($this->getId()){
	                    $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        $this->_save_textfragment_on_save = true;
                        $this->_set_textfragment_id_on_save = true;
	                    $this->_text_fragment = $tf;
	                    SmartestLog::getInstance('system')->log("Text asset '".$this->getLabel()."' with ID ".$this->getId()." did not have an associated TextFragment. A new one was created.");
                    }else{
                        // this is a new text asset, so it doesn't have an id yet.
                        $this->_text_fragment = $tf;
                        $this->_set_textfragment_asset_id_on_save = true;
                        $this->_set_textfragment_id_on_save = true;
                    }
    	        }
    	        
    	        $this->_text_fragment->setAsset($this);
    	        
    	    }else{
    	        return null;
    	    }
	    
        }
        
        return $this->_text_fragment;
	    
	}
    
    public function getOEmbedService(){
        if($this->getType() == 'SM_ASSETTYPE_OEMBED_URL'){
            
            $apih = new SmartestAPIServicesHelper;
                
            if(strlen($this->getInfoField('oembed_service_id'))){
                if($s = $apih->getOEmbedService($this->getInfoField('oembed_service_id'))){
                    return $s;
                }
            }
            
            try{
                if($s = $apih->getServiceFromUrl($this->getUrl())){
                    $this->setOEmbedServiceId($s->getParameter('id'));
                    return $s;
                }else{
                    return false;
                }
            }catch(SmartestOEmbedUrlNotSupportedException $e){
                return false;
            }
        }
    }
    
    public function getOEmbedServiceId(){
        
        if(strlen($this->getInfoField('oembed_service_id'))){
            return $this->getInfoField('oembed_service_id');
        }
        
        if($s = $this->getOEmbedService()){
            return $s->getParameter('id');
        }
    }
    
    public function setOEmbedServiceId($service_id){
        $this->setInfoField('oembed_service_id', $service_id);
    }
    
    public function setInfoField($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    // URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here:
	    $this->_asset_info->setParameter($field, rawurlencode(utf8_decode($new_data)));
        $this->_asset_info_modified = true;
	    $this->_modified_properties['info'] = SmartestStringHelper::sanitize(serialize($this->_asset_info->getArray()));
	    
	}
	
	public function getInfoField($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
        
        if($this->_asset_info->hasParameter($field)){
            return $this->_asset_info->getParameter($field);
	    }else{
	        return null;
	    }
	}
    
    public function hasContent(){
        return $this->usesTextFragment() || ($this->isEditable() && is_file($this->getFullPathOnDisk()));
    }
	
    // Will return exactly what is stored
	public function getContent($raw=false){
	    
	    if($this->getTextFragment()){
	        $string = $this->getTextFragment()->getContent();
	    }else if($this->isEditable() && is_file($this->getFullPathOnDisk())){
	        $string = SmartestFileSystemHelper::load($this->getFullPathOnDisk(), true);
        }else if($this->getType() == 'SM_ASSETTYPE_OEMBED_URL'){
            $apih = new SmartestAPIServicesHelper;
            
            if($this->getOEmbedServiceId() && substr($this->getOEmbedServiceId(), 0, 20) == 'OEMBED_SMARTEST_SITE'){
                
                try{
                    if($this instanceof SmartestRenderableAsset && $this->_render_data instanceof SmartestParameterHolder){
                        if($this->_render_data->hasParameter('width') && $this->_render_data->hasParameter('height')){
                            $string = $apih->getSmartestOEmbedMarkupFromUrl($this->getUrl(), $this->_render_data->getParameter('width'), $this->_render_data->getParameter('height'));
                        }else{
                            $string = $apih->getSmartestOEmbedMarkupFromUrl($this->getUrl());
                        }
                    }else{
                        $string = $apih->getSmartestOEmbedMarkupFromUrl($this->getUrl());
                    }
                    
                }catch(SmartestOEmbedUrlNotSupportedException $e){
                    $string = '<p>'.$e->getMessage().'</p>';
                }
                
            }else{
                try{
                    $string = $apih->getOEmbedMarkupFromUrl($this->getUrl());
                }catch(SmartestOEmbedUrlNotSupportedException $e){
                    $string = '<p>'.$e->getMessage().'</p>';
                }
            }
            
        }else{
    	    $string = null;
    	}
    	
        if($raw){
            return $string;
        }else{
            $s = new SmartestString($string);
    	    return $s;
        }
    	
    	
	}
    
    // Will preformat and process the content in a form suitable for both rendering in in editor and being edited (visually)
    public function getContentForEditor(){
        
        $asset_type = $this->getTypeInfo();
        
	    if($asset_type['storage']['type'] == 'database'){
	        if($this->usesTextFragment()){
	            $content = $this->getTextFragment()->getContentForEditor();
	        }else{
                // Assets that are stored in the database but not in a textfragment. Hmmm....
	        }
        }else{
            $file = SM_ROOT_DIR.$asset_type['storage'].$this->getUrl();
            $content = htmlspecialchars(SmartestFileSystemHelper::load($this->getFullPathOnDisk()), ENT_COMPAT, 'UTF-8');
        }
    
        $content = trim(SmartestStringHelper::protectSmartestTags($content));
    
        return $content;
        
    }
	
	public function setContent($raw_content, $escapeslashes=true){
	    
	    $info = $this->getTypeInfo();
	    
	    $content = $raw_content;
	    
	    if($this->getTextFragment()){
	        // save the text fragment in the database
	        $this->getTextFragment()->setContent($content);
	        $this->_save_textfragment_on_save = true;
	    }else if($this->usesLocalFile() && $this->isEditable()){
	        // save the file to its desired location
	        SmartestFileSystemHelper::save($this->getFullPathOnDisk(), $content, true);
	    }else{
	        // what happens here?
	        // probably nothing as it's just not the right type of asset. Just log and move on
	        SmartestLog::getInstance('system')->log('SmartestAsset::setContent() called on a non-editable asset ('.$this->getId().')');
	    }
	    
	}
    
	public function setContentFromEditor($raw_content, $escapeslashes=true){
	    
	    $info = $this->getTypeInfo();
	    
	    $content = $raw_content;
	    
	    if($this->getTextFragment()){
	        // save the text fragment in the database
	        $success = $this->getTextFragment()->setContentFromEditor($content);
            // echo $this->getTextFragment()->getContent();
	        $this->_save_textfragment_on_save = true;
	    }else if($this->usesLocalFile() && $this->isEditable()){
	        // save the file to its desired location
	        $success = SmartestFileSystemHelper::save($this->getFullPathOnDisk(), $content, true);
	    }else{
	        // what happens here?
	        // probably nothing as it's just not the right type of asset. Just log and move on
	        SmartestLog::getInstance('system')->log('SmartestAsset::setContent() called on a non-editable asset ('.$this->getId().')');
            $success = false;
	    }
        
        return $success;
	    
	}
    
    public function getContentHash(){
        return md5($this->getContent()->getValue());
    }
	
	public function isEditable(){
	    $info = $this->getTypeInfo();
	    return (isset($info['editable']) && SmartestStringHelper::toRealBool($info['editable']));
	}
	
	public function isParsable(){
	    $info = $this->getTypeInfo();
	    return (isset($info['parsable']) && SmartestStringHelper::toRealBool($info['parsable']));
	}
    
    public function getLiveCacheDirectory(){
        
        $info = $this->getTypeInfo();
        
        if(isset($info['storage']['live_cache'])){
            return str_replace('Public/', '', $info['storage']['live_cache']);
        }
        
    }
    
    public function getLiveCacheWebPath(){
        $info = $this->getTypeInfo();
        
        if(isset($info['storage']['live_cache'])){
            return $this->getLiveCacheDirectory().$this->getUrl().'&amp;nonce='.substr($this->getContentHash(), 0, 8);
        }
    }
	
	public function getFullPathOnDisk(){
	    
	    if($this->usesLocalFile()){
	        return SM_ROOT_DIR.$this->getStorageLocation().$this->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function getStorageLocation($include_smartest_root=false){
	    
	    $root = $include_smartest_root ? SM_ROOT_DIR : null;
	    
	    if($this->usesLocalFile()){
	        if($this->getDeleted()){
	            return $root.'Documents/Deleted/';
	        }else{
	            $info = $this->getTypeInfo();
	            return $root.$info['storage']['location'];
            }
	    }else{
	        return null;
	    }
	    
	}
	
	public function isWebAccessible(){
	    $info = $this->getTypeInfo();
	    return $this->usesLocalFile() && substr($info['storage']['location'], 0, strlen('Public/')) == 'Public/';
	}
    
    public function getEncodedFileName(){
        return rawurlencode($this->getUrl());
    }
	
	public function getFullWebPath(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($this->isWebAccessible()){
	        return $this->_request->getDomain().substr($info['storage']['location'], strlen('Public/')).$this->getEncodedFileName();
	    }else{
	        return null;
	    }
	    
	}
	
	public function isExternal(){
	    $info = $this->getTypeInfo();
	    return ($info['storage']['type'] == 'external_translated');
	}
	
	public function getAbsoluteUri($raw=false){
        
        if($this->isExternal()){
	        $url = $this->getUrl();
	    }else{
	        if($this->isWebAccessible()){
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
	            $url = $protocol.$this->getSite()->getDomain().$this->getFullWebPath();
	        }else{
	            return null;
	        }
	    }
        
        if($raw){
            return $url;
        }else{
            return new SmartestExternalUrl($url);
        }
	    
	}
	
	public function isImage(){
	    return in_array($this->getType(), array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETTYPE_SVG_IMAGE', 'SM_ASSETTYPE_INSTAGRAM_IMAGE'));
	}
    
	public function isBinaryImage(){
	    return in_array($this->getType(), array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'));
	}
	
	public function isHtmlFriendly(){
	    $info = $this->getTypeInfo();
	    return $info['html_friendly'] !== false;
	}
	
	public function isTemplate(){
	    $alh = new SmartestAssetsLibraryHelper;
	    return in_array($this->getType(), $alh->getTypeIdsInCategories('templates'));
	}
    
    public function toRenderableAsset(){
        $obj = new SmartestRenderableAsset;
        $obj->hydrate($this->getOriginalDbRecord());
        return $obj;
    }
	
	public function isTooLarge(){
	    if($this->isImage()){
	        return $this->getImage()->isTooLarge();
	    }
	}
	
	public function getImage(){
	    if($this->isImage()){
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    return $this->_image;
		}
	}
	
	public function getThumbnailImage(){
	    
	    if($this->isBinaryImage()){
	        return $this;
	    }else{
	        if($this->getThumbnailId()){
	            
	            if(is_object($this->_thumbnail_asset)){
	                return $this->_thumbnail_asset;
	            }else{
	                if(get_class($this) == 'SmartestRenderableAsset'){
    	                $a = new SmartestRenderableAsset;
    	            }else{
    	                $a = new SmartestAsset;
    	            }

    	            if($a->find($this->getThumbnailId())){
    	                $this->_thumbnail_asset = $a;
    	                return $a;
    	            }else{
    	                return null;
    	            }
	            }
	            
	        }else{
	            return null;
	        }
	    }
	    
	}
	
	public function getStoreMethodName(){
	    return 'store'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
	}
	
	public function getParseMethodName(){
	    return 'parse'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
	}
	
	public function getConvertMethodName(){
	    $info = $this->getTypeInfo();
	    
	    if($info['convert_to_smarty']){
	        return 'convert'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'AssetToSmartyFile';
        }else{
            return 'convert'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
        }
	}
	
	public function getDefaultParams(){
	    
	    $info = $this->getTypeInfo();
	    
	    $params = array();
        $type_lookups = array();
        $ratio = null;
	    
	    if(isset($info['param'])){
	        
	        $raw_xml_params = $info['param'];
            
            // print_r($raw_xml_params);
            
            foreach($raw_xml_params as $rxp){
                
                if($rxp['name'] == 'ratio'){
                    $ratio = $rxp['default'];
                }
                
                $type_lookups[$rxp['name']] = $rxp['type'];
                
                if(isset($rxp['default'])){
	                $sv = $rxp['default'];
                }else{
                    $sv = '';
                }
                
                $params[$rxp['name']] = SmartestDataUtility::objectize($sv, $rxp['type']);
                
	        }
            
            $default_serialized_data = $this->getParameterDefaults();
	        
	        if($asset_params = @unserialize($default_serialized_data)){
	            
	            if(is_array($asset_params)){
	                
                    // If a ratio exists, and either width or height is missing (from assets where both are needed, use the ratio to calculate the missing value)
                    if(isset($asset_params['ratio']) && is_numeric($asset_params['ratio'])){
                        $ratio = $asset_params['ratio'];
                    }
                    
                    if(isset($params['width']) && isset($params['height']) && is_numeric($ratio)){
                        
                        if((!isset($asset_params['width']) || !$asset_params['width']) && isset($asset_params['height']) && is_numeric($asset_params['height'])){
                            $asset_params['width'] = floor($asset_params['height']*$ratio);
                        }else if((!isset($asset_params['height']) || !$asset_params['height']) && isset($asset_params['width']) && is_numeric($asset_params['width'])){
                            $asset_params['height'] = floor($asset_params['width']/$ratio);
                        }
                        
                    }
                    
                    // data found. loop through params from xml, replacing values with those from asset
    	            foreach($asset_params as $key => $value){
    	                if(isset($params[$key])){
    	                    // $params[$key] = $value;
                            $params[$key] = SmartestDataUtility::objectize($value, $type_lookups[$key]);
                        }
    	            }
	            
                }
	            
	        }
	        
	    }
        
        return $params;
	    
	}
	
	public function getEditorParams(){
	    
	    $info = $this->getTypeInfo();
	    
	    $params = array();
	    
	    if(isset($info['param'])){
	        
	        $raw_xml_params = $info['param'];
	        
	        foreach($raw_xml_params as $rxp){
                
                if(isset($rxp['editable']) && !SmartestStringHelper::toRealBool($rxp['editable'])){
                }else{
                    
                    $params[$rxp['name']]['datatype'] = $rxp['type'];
                    
                    if(isset($rxp['default'])){
    	                $sv = $rxp['default'];
                    }else{
                        $sv = '';
                    }
                    
                    $params[$rxp['name']]['required'] = isset($rxp['required']) && SmartestStringHelper::toRealBool($rxp['required']);
                
                    $params[$rxp['name']]['value'] = SmartestDataUtility::objectize($sv, $rxp['type']);
                
                    if(isset($rxp['label'])){
                        $params[$rxp['name']]['label'] = $rxp['label'];
                    }else{
                        $params[$rxp['name']]['label'] = $rxp['name'];
                    }
                
                    if(isset($rxp['options'])){
                        $params[$rxp['name']]['has_options'] = true;
                        $params[$rxp['name']]['options'] = new SmartestFixedOptionsList($rxp['options'], $rxp['type']);
                    }else{
                        $params[$rxp['name']]['has_options'] = false;
                    }
                    
                    // print_r($params[$rxp['name']]);
                    
                }
                
                // TODO: Insert L10N stuff here
	        }
            
            $default_serialized_data = $this->getParameterDefaults();
	        
	        if($asset_params = @unserialize($default_serialized_data)){
	            
	            if(is_array($asset_params)){
	            
	                // data found. loop through params from xml, replacing values with those from asset
    	            foreach($asset_params as $key => $value){
                        if(isset($params[$key]) && $params[$key]['has_options']){
                            if(in_array($value, $params[$rxp['name']]['options']->getKeys())){
                                // A value that is one of the offered options has already been selected for this asset. Pass the value on to the form.
                                $params[$key]['value'] = $value;
                            }elseif(strlen($value) == 0){
                                // A blank value has been entered. If the property is not required, use the blank value, otherwise the default value will still be used
                                if(!$params[$key]['required']){
                                    $params[$key]['value'] = $value;
                                }
                            }else{
                                // A value other than the available options is saved. Revert to the default by not adding the value here (the default is already in use).
                            }
                        }elseif(isset($params[$key]) && $params[$key] !== null){
                            $value_obj = SmartestDataUtility::objectize($value, $params[$key]['datatype']);
                            $params[$key]['value'] = $value;
                        }
    	            }
	            
                }
	            
	        } // data not found, or not unserializable. just use defaults from above
	        
	        
	        
	    }
	    
	    return $params;
	    
	}
    
	public function getDefaultParameterValues(){
        
        if(!$this->_default_parameter_values instanceof SmartestParameterHolder){
            
            $ph = new SmartestParameterHolder('Asset default parameter values');
        
    	    if($data = unserialize($this->getParameterDefaults())){
                $ph->loadArray($data);
    	    }
        
            $this->_default_parameter_values = $ph;
            
        }
        
        return $this->_default_parameter_values;
        
	}
    
    public function getDefaultParameterValue($value_name){
        return $this->getDefaultParameterValues()->getParameter($value_name);
    }
    
    public function setDefaultParameterValue($name, $value){
        $this->getDefaultParameterValues()->setParameter($name, $value);
        $this->_modified_properties['parameter_defaults'] = SmartestStringHelper::sanitize(serialize($this->_default_parameter_values->getArray()));
    }
    
    public function getCredit(){
        return $this->getDefaultParameterValues()->getParameter('credit');
    }
    
    public function setCredit($credit){
        return $this->setDefaultParameterValue('credit', $credit);
    }
	
	public function getDescription(){
	    return $this->getField('search_field');
	}
	
	public function setDescription($description){
	    return $this->setField('search_field', $description);
	}
	
	public function getDownloadableFilename(){
	    
	    if($this->usesLocalFile()){
	        return $this->getUrl();
	    }else{
	        $info = $this->getTypeInfo();
	        
	        if(count($info['suffix'])){
	            $dot_suffix = $info['suffix'][0]['_content'];
	        }else{
	            // no suffix found - use txt and log this
	            $dot_suffix = 'txt';
	        }
	        
	        $file_name = strlen($this->getStringid()) ? $this->getStringid() : 'asset';
	        $file_name .= '.'.$dot_suffix;
	        
	        return $file_name;
	        
	    }
	}
	
	public function getDotSuffix(){
	    $alh = new SmartestAssetsLibraryHelper;
	    preg_match($alh->getSuffixTestRegex($this->getType()), $this->getUrl(), $matches);
	    return substr($matches[1], 1);
	}
	
	public function getDownloadUrl(){
	    return $this->_request->getDomain().'download/'.$this->getUrl().'?key='.$this->getWebid();
	}
	
	public function getAbsoluteDownloadUri($secure=false){
	    if(!$this->_absolute_uri_object){
	        $protocol = $secure ? 'https://' : 'http://';
	        if($this->isExternal()){
    	        $this->_absolute_uri_object = new SmartestExternalUrl($this->getUrl());
    	    }else{
                if($this->getSiteId() == 0){
                    $this->_absolute_uri_object = new SmartestExternalUrl(SM_PROTOCOL.SM_SITE_HOST.$this->getDownloadUrl());
                }else{
                    $this->_absolute_uri_object = new SmartestExternalUrl(SM_PROTOCOL.SM_SITE_HOST.$this->getDownloadUrl());
                }
            }
        }
        return $this->_absolute_uri_object;
	}
	
    public function save(){
	    
        if(!isset($this->_modified_properties['parent_id']) && !$this->_properties['parent_id']){
            $this->setParentId(0);
        }
        
        if(!isset($this->_modified_properties['model_id']) && !$this->_properties['model_id']){
            $this->setModelId(0);
        }
        
        if(!isset($this->_modified_properties['thumbnail_id']) && !$this->_properties['thumbnail_id']){
            $this->setThumbnailId(0);
        }
        
        if(!isset($this->_modified_properties['info']) && !$this->_properties['info']){
            $this->setField('info', null);
        }
        
        if(!isset($this->_modified_properties['is_subbed']) && !$this->_properties['is_subbed']){
            $this->setIsSubbed(0);
        }
        
        if(!isset($this->_modified_properties['is_hidden']) && !$this->_properties['is_hidden']){
            $this->setIsHidden(0);
        }
        
        if(!isset($this->_modified_properties['search_field']) && !$this->_properties['search_field']){
            $this->setSearchField('');
        }
        
        // This bit guarantees that new files are saved to the correct (current) site
        if($this->getCurrentSiteId() && !$this->getCameFromDatabase() && $this->getSiteId() != $this->getCurrentSiteId() && !isset($this->_modified_properties['site_id'])){
            $this->setSiteId($this->getCurrentSiteId());
        }
        
	    parent::save();
	    
        if(is_object(SmartestSession::get('current_open_project'))){
            $placeholders_usages = $this->getPlaceholderUsages(SmartestSession::get('current_open_project')->getId());
            $ipv_usages = $this->getItemPropertyUsages(SmartestSession::get('current_open_project')->getId());
        
            ////// Touch any pages where this asset is used, so they can be identified as potentially needing republishing //////
        
            if(count($ipv_usages)){
                foreach($ipv_usages as $ipvu){
                    $ipvu->getItem()->touch();
                }
            }
        
            if(count($placeholders_usages)){
                foreach($placeholders_usages as $phu){
                    $phu->getPage()->touch();
                }
            }
        }
        
	    if($this->usesTextFragment()){
	    
	        if($this->_set_textfragment_asset_id_on_save){
	            $this->getTextFragment()->setAssetId($this->getId());
	        }
	        
	        if($this->_set_textfragment_asset_id_on_save || $this->_save_textfragment_on_save || (is_object($this->getTextFragment()) && !$this->getTextFragment()->getId())){
	            $this->getTextFragment()->save();
	            if(($this->getFragmentId() != $this->getTextFragment()->getId() && $this->getTextFragment()->getId() > 0) || $this->_set_textfragment_id_on_save){
	                $this->setFragmentId($this->getTextFragment()->getId());
                    parent::save();
	            }
	        }
            
            /* if($tf->getId()){
	            // the textfragment already exists in the database
	            $this->setFragmentId($this->getTextFragment()->getId());
	            $tf->save();
	        }else{
	            // the textfragment is a new object
	            $tf->setAssetId($this->getId());
	            $tf->save();
	            $this->setFragmentId($tf->getId());
	            parent::save();
	        } */
	        
    	    /* if(!$this->getFragmentId()){
	            // this asset isn't linked
	            $this->getTextFragment()->setAssetId($this->getId());
	            $this->getTextFragment()->save();
	            
	            parent::save();
	        }else{
	            $this->getTextFragment()->save();
	        } */
    	    
	    
        }
	    
	}
	
	protected function addAllowedType($type){
	    if(!isset($this->_allowed_types[$type])){
	        $this->_allowed_types[] = $type;
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function getArrayForElementsTree($level){
	    
	    $info = array();
	    $info['asset_id'] = $this->getId();
	    $info['asset_webid'] = $this->getWebid();
	    $info['asset_type'] = $this->getType();
	    $info['assetclass_name'] = $this->getStringid();
	    $info['assetclass_id'] = $this->getStringid();
	    $info['defined'] = 'PUBLISHED';
	    $info['exists'] = 'true';
	    $info['filename'] = $this->getUrl();
	    $info['type'] = 'asset';
	    $level++;
	    return array('info'=>$info, 'level'=>$level, 'state'=>'closed');
	}
	
	public function getLiveInstances(){
	    
	    $sql = "SELECT DISTINCT Pages.*, Sites.*, AssetIdentifiers.*, AssetClasses.* FROM Pages, Sites, AssetIdentifiers, Assets, AssetClasses WHERE asset_id='".$this->getId()."' AND assetidentifier_live_asset_id=asset_id AND assetidentifier_page_id=page_id AND page_site_id=site_id AND assetidentifier_assetclass_id=assetclass_id AND page_is_published='TRUE'";
	    
	    if($this->getSite()){
	        $sql .= " AND asset_site_id=site_id AND site_id='".$this->getSite()->getId()."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $instances = array();
	    
	    foreach($result as $ri){
	        
	        $instance = array();
	        $page = new SmartestPage;
	        $page->hydrate($ri);
	        $instance['page'] = $page;
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site;
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass;
            
            $instances[] = $instance;
            
	    }
	    
	    // print_r($instances);
	    return $instances;
	}
	
	public function getDraftInstances(){
	    
	    $sql = "SELECT DISTINCT Pages.*, Sites.*, AssetIdentifiers.*, AssetClasses.* FROM Pages, Sites, AssetIdentifiers, Assets, AssetClasses WHERE asset_id='".$this->getId()."' AND assetidentifier_draft_asset_id=asset_id AND assetidentifier_page_id=page_id AND page_site_id=site_id AND assetidentifier_assetclass_id=assetclass_id AND page_is_published='TRUE'";
	    
	    if($this->getSite()){
	        $sql .= " AND asset_site_id=site_id AND site_id='".$this->getSite()->getId()."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $instances = array();
	    
	    foreach($result as $ri){
	        
	        $instance = array();
	        $page = new SmartestPage;
	        $page->hydrate($ri);
	        $instance['page'] = $page;
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site;
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass;
            
            $instances[] = $instance;
            
	    }
	    
	    // print_r($instances);
	    return $instances;
	}
	
	public function delete(){
	    
	    $this->setDeleted(1);
	    
	    if($this->usesLocalFile()){
	        // move the file to 
	        $deleted_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Documents/Deleted/'.$this->getUrl());
	        $deleted_filename = basename($deleted_path);
	        $this->setUrl($deleted_filename);
	        SmartestFileSystemHelper::move($this->getFullPathOnDisk(), $deleted_path);
	    }
	    
	    parent::save();
	    
	}
	
	public function duplicate($name, $site_id=null, $pointer_only=false){
	    
	    $dup = $this->duplicateWithoutSaving();
	    
	    // Check the name is unique
	    if(is_numeric($site_id)){
	        $sql = "SELECT asset_stringid, asset_label from Assets WHERE asset_site_id='".$site_id."' OR asset_shared='1'";
	    }else{
	        $sql = "SELECT asset_stringid, asset_label from Assets WHERE asset_site_id='".$this->getSiteId()."' OR asset_shared='1'";
	    }
	    
	    $dup->setWebId(SmartestStringHelper::random(32));
	    $existing_names_labels = $this->database->queryFieldsToArrays(array('asset_label', 'asset_stringid'), $sql);
	    
	    $stringid = SmartestStringHelper::guaranteeUnique(SmartestStringHelper::toVarName($name), $existing_names_labels['asset_stringid'], '_');
	    $label    = SmartestStringHelper::guaranteeUnique($name, $existing_names_labels['asset_label'], ' ');
	    
	    $dup->setStringId($stringid);
	    $dup->setLabel($label);
	    
	    $info = $this->getTypeInfo();
	    
	    if($info['storage']['type'] == 'file'){
	        // if the storage is a file on the disk, copy that file and get the new file's name on disk
	        if($pointer_only){
	            
	        }else{
	            $new_filename_full = SmartestFileSystemHelper::getUniqueFileName($this->getFullPathOnDisk());
	            
	            if(SmartestFileSystemHelper::copy($this->getFullPathOnDisk(), $new_filename_full)){
	                $new_filename = basename($new_filename_full);
	                $dup->setUrl($new_filename);
	            }else{
	                return false;
	            }
	            
	        }
	        
	    }else if($info['storage']['type'] == 'database'){
	        
	        // otherwise if the file is stored as a text fragment, copy that and get the new text fragment's ID
	        $textfragment = $this->getTextFragment()->duplicate();
	        
	        // Set the new textfragment ID
	        $this->setFragmentId($textfragment->getId());
	        $dup->setUrl($dup->getStringId().'.'.$info['suffix'][0]['_content']);
	        
	    }
	    
	    if(is_numeric($site_id) && $site_id != $this->getSiteid()){
	        $dup->setSiteId($site_id);
	    }
	    
	    // copy many-to-many data, such as authors
	    
	    // save the duplicate
	    $dup->save();
	    
	    if($info['storage']['type'] == 'database'){
	        $textfragment->setAssetId($dup->getId());
	        $textfragment->save();
        }
	    
	    // print_r($this->getGroupMemberships());
	    foreach($this->getGroupMemberships() as $membership){
	        
	        if($group = $membership->getGroup()){
	            if($group->getSiteId() != $site_id){
	                $group->setShared(1);
	            }
	        }
	        
	        $nm = $membership->duplicateWithoutSaving();
	        $nm->setAssetId($dup->getId());
	        $nm->save();
	        
	    }
	    
	    return $dup;
	    
	}
	
	public function getOtherPointers(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($info['storage']['type'] == 'file'){
	        $sql = "SELECT Assets.*, Sites.* FROM Assets, Sites WHERE Sites.site_id=Assets.asset_site_id AND Assets.asset_id != '".$this->getId()."' AND Assets.asset_url = '".addslashes($this->getUrl())."' AND Assets.asset_type = '".$this->getType()."' AND Assets.asset_deleted != '1' AND Assets.asset_is_hidden != '1'";
	        $result = $this->database->queryToArray($sql);
	        
	        $pointers = array();
	        
	        foreach($result as $r){
	            $a = new SmartestAsset;
	            $s = new SmartestSite;
	            $a->hydrate($r);
	            $s->hydrate($r);
	            $a->assignSiteFromObject($s);
	            $pointers[] = $a;
	        }
	        
	        return $pointers;
	        
	    }else{
	        return array();
	    }
	    
	}
	
	public function getSize($raw=false){
	    
	    $type_info = $this->getTypeInfo();
	    
	    if($type_info['storage']['type'] == 'database'){
	        
	        $size = mb_strlen($this->getContent());
	        
	        if(!$raw){
	            // size is in bytes
    	        if($size >= 1024){
    	            // convert to kilobytes
    	            $new_size = $size/1024;

    	            if($new_size >= 1024){
    	                // convert to megabytes
    	                $new_size = $new_size/1024;

    	                if($new_size >= 1024){
        	                // convert to gigabytes
        	                $new_size = $new_size/1024;

                            if($new_size >= 1024){
            	                // convert to terrabytes
            	                $new_size = $new_size/1024;
                                $size = number_format($new_size, 3, '.', ',').' TB';
                            }else{
                                $size = number_format($new_size, 2, '.', ',').' GB';
                            }

                        }else{
                            $size = number_format($new_size, 1, '.', ',').' MB';
                        }

                    }else{
                        $size = number_format($new_size, 1, '.', ',').' KB';
                    }

    	        }else{
    	            $size = $size.' Bytes';
    	        }
	        }
	    }else{
	        if($raw){
	            $size = SmartestFileSystemHelper::getFileSize($this->getFullPathOnDisk());
	        }else{
	            $size = SmartestFileSystemHelper::getFileSizeFormatted($this->getFullPathOnDisk());
            }
        }
        
        return $size;
	    
	}
	
	public function setIsDraft($draft){
        // non-standard method should call standard one
	    $this->setDraftMode($draft);
	}
    
    public function setDraftMode($draft){
        $this->_draft_mode = $draft ? true : false;
    }
	
	public function getIsDraft(){
        // non-standard method should call standard one
	    return $this->getDraftMode();
	}
    
    public function getDraftMode(){
        return $this->_draft_mode;
    }
	
	public function getPossibleOwners(){
	    // var_dump($this->getSiteId());
        if($this->getSiteId() == 0){
            return array();
        }else{
            return $this->getSite()->getUsersThatHaveAccess();
        }
	}
	
	public function getGroupMemberships($refresh=false, $mode=1, $approved_only=false){
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
	    
	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    
	    $result = $q->retrieve(true);
        
        return $result;
        
    }
    
    public function getGroupIds(){
        
        $memberships = $this->getGroupMemberships();
        $gids = array();
        
        foreach($memberships as $m){
	        $gids[] = $m->getGroupId();
	    }
        
        return $gids;
        
    }
	
	public function getGroups(){
	    
	    $memberships = $this->getGroupMemberships();
	    $groups = array();
	    
	    foreach($memberships as $m){
	        $groups[] = $m->getGroup();
	    }
	    
	    return $groups;
	    
	}
	
	public function getPossibleGroups(){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $groups = $alh->getAssetGroupsThatAcceptType($this->getType(), $this->getSiteId());
	    
	    $existing_group_ids = $this->getGroupIds();
	    
	    foreach($groups as $k => $g){
	        if(in_array($g->getId(), $existing_group_ids)){
	            unset($groups[$k]);
	        }
	    }
	    
	    return $groups;
	    
	}
	
	public function addToGroupById($gid, $force=false){
        
        if($force || !in_array($gid, $this->getGroupIds())){
            
            $m = new SmartestAssetGroupMembership;
            $m->setGroupId($gid);
            $m->setAssetId($this->getId());
            $m->save();
            
        }
        
    }
	
	public function getComments(){
	    
	    $sql = "SELECT * FROM Comments, Users WHERE comment_type='SM_COMMENTTYPE_ASSET_PRIVATE' AND comment_object_id='".$this->getId()."' AND comment_author_user_id=user_id ORDER BY comment_posted_at";
	    $result = $this->database->queryToArray($sql);
	    
	    $comments = array();
	    
	    foreach($result as $r){
	        $c = new SmartestAssetComment;
	        $c->hydrate($r);
	        $comments[] = $c;
	    }
	    
	    return $comments;
	    
	}
	
    public function addComment($content, $user_id){
        
        $comment = new SmartestAssetComment;
        $comment->setAuthorUserId($user_id);
        $comment->setContent($content);
        $comment->setAssetId($this->getId());
        $comment->setPostedAt(time());
        
        $comment->save();
        
    }
    
    public function clearRecentlyEditedInstances($site_id, $user_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS');
	    
	    $q->setTargetEntityByIndex(1);
	    
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        if(is_numeric($user_id)){
            $q->addQualifyingEntityByIndex(2, $user_id);
        }
        
        $q->delete();
	    
	}
	
	public function setStringId($stringid, $site_id=''){
	    
	    if($this->_properties['id']){
	        $sql = "SELECT asset_stringid FROM Assets WHERE (asset_site_id='".$this->getSiteId()."' OR asset_shared='1') AND asset_id != '".$this->getId()."'"; 
	    }else{
	        if($site_id){
	            $sql = "SELECT asset_stringid FROM Assets WHERE (asset_site_id='".$site_id."' OR asset_shared='1')"; 
	        }else{
	            $sql = "SELECT asset_stringid FROM Assets"; 
	        }
	    }
	    
	    $fields = $this->database->queryFieldsToArrays(array('asset_stringid'), $sql);
        $stringid = SmartestStringHelper::guaranteeUnique($stringid, $fields['asset_stringid'], '_');
        
        return parent::setStringId($stringid);
	    
	}
	
	// System UI calls
	
	public function getSmallIcon(){
	    
	    $info = $this->getTypeInfo();
	    
	    if(isset($info['icon']) && is_file(SM_ROOT_DIR.'Public/Resources/Icons/'.$info['icon'])){
	        return $this->_request->getDomain().'Resources/Icons/'.$info['icon'];
	    }else{
	        return $this->_request->getDomain().'Resources/Icons/page_white.png';
	    }
	    
	}
    
    public function getIconName(){
        // Newer Icon functionality that produces a fontawesome icon for the asset
        $type = $this->getTypeInfo();
        if(isset($type['fa_iconname'])){
            return $type['fa_iconname'];
        }else{
            return 'file-o';
        }
    }
    
    public function getIconCode(){
        return '<i class="fa fa-'.$this->getFontAwesomeIcon().'"></i>';
    }
    
    public function getFontAwesomeIcon(){
        if($this->getType() == 'SM_ASSETTYPE_OEMBED_URL'){
            if($s = $this->getOEmbedService()){
                return $s->getParameter('fa_iconname');
            }else{
                if(substr($this->getOEmbedServiceId(), 0, 20) == 'OEMBED_SMARTEST_SITE'){
                    return 'clone';
                }else{
                    return 'file-code-o';
                }
            }
        }else{
            $type_info = $this->getTypeInfo();
            return $this->getIconName();
        }
    }
	
	public function getLargeIcon(){
	    
	}
	
	public function getLabel(){
	    
	    return parent::getLabel() ? $this->_properties['label'] : $this->getStringId();
	    
	}
	
	public function getActionUrl(){
	    
	    // return $this->_request->getDomain().'assets/editAsset?asset_id='.$this->getId();
	    return $this->_request->getDomain().'smartest/file/edit/'.$this->getId();
	    
	}
	
	public function tag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->find($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->findBy('name', $tag_name)){
                // create tag
    	        $tag->setLabel($tag_identifier);
    	        $tag->setName($tag_name);
    	        $tag->save();
    	    }
	    }
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->_properties['id']."', 'SM_ASSET_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
    
	public function addTagWithId($tag_id){
	    
        $tag_id = (int) $tag_id;
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag_id."', '".$this->_properties['id']."', 'SM_ASSET_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function untag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                return false;
    	    }
	    }
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_ASSET_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
    
	public function hasTag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        $sql = "SELECT * FROM TagsObjectsLookup WHERE taglookup_type='SM_ASSET_TAG_LINK' AND taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."'";
	    }else{
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        $sql = "SELECT * FROM TagsObjectsLookup, Tags WHERE taglookup_type='SM_ASSET_TAG_LINK' AND taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND Tags.tag_name='".$tag_name."'";
	    }
	    
	    return (bool) count($this->database->queryToArray($sql));
	    
	}
    
	public function getTags(){
	    
	    $sql = "SELECT * FROM Tags, TagsObjectsLookup WHERE TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_type='SM_ASSET_TAG_LINK' ORDER BY Tags.tag_name";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    $tags = array();
	    
	    foreach($result as $ta){
	        if(!in_array($ta['taglookup_tag_id'], $ids)){
	            $ids[] = $ta['taglookup_tag_id'];
	            $tag = new SmartestTag;
	            $tag->hydrate($ta);
	            $tags[] = $tag;
	        }
	    }
	    
	    return $tags;
	    
	}
    
    public function getPlaceholderUsages($site_id=null){
        
        $du = new SmartestDataUtility;
        $placeholder_ids = $du->getPlaceholderIds($site_id);
        $placeholder_sql = "SELECT AssetIdentifiers.*, AssetClasses.*, Pages.* FROM AssetIdentifiers, AssetClasses, Pages WHERE (assetidentifier_assetclass_id IN ('".implode("','", $placeholder_ids)."') AND (assetidentifier_draft_asset_id='".$this->getId()."' OR assetidentifier_live_asset_id='".$this->getId()."')) AND assetidentifier_assetclass_id=AssetClasses.assetclass_id AND assetidentifier_page_id=Pages.page_id";
        
        if(is_numeric($site_id)){
            $placeholder_sql .= " AND page_site_id='".$site_id."'";
        }
        
        $placeholder_result = $this->database->queryToArray($placeholder_sql);
        
        if(count($placeholder_result)){
            
            $usages = array();
            
            foreach($placeholder_result as $p){
                
                $def = new SmartestPlaceholderDefinition;
                $def->hydrateFromGiantArray($p);
                
                $u = new SmartestAssetUsageInstance;
                $u->setType('SM_ASSETUSAGETYPE_PLACEHOLDER');
                $u->setAssetId($this->getId());
                $u->setPlaceholder($def->getPlaceholder());
                $u->setPage($def->getPage());
                $u->setDefinition($def);
                $usages[$def->getPlaceholder()->getId().$def->getPage()->getId().'ph'] = $u;
                
            }
            
            return $usages;
            
        }else{
            return array();
        }
        
    }
    
    public function getItemPropertyUsages($site_id=null){
        
        $du = new SmartestDataUtility;
        $file_property_ids = $du->getAssetItemPropertyIds($site_id);
        $ipv_sql = "SELECT Items.*, ItemProperties.*, ItemPropertyValues.* FROM ItemPropertyValues, Items, ItemProperties WHERE (itempropertyvalue_property_id IN ('".implode("','", $file_property_ids)."') AND (itempropertyvalue_draft_content='".$this->getId()."' OR itempropertyvalue_content='".$this->getId()."')) AND ItemPropertyValues.itempropertyvalue_property_id=ItemProperties.itemproperty_id AND ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND Items.item_deleted !=1";
        
        if(is_numeric($site_id)){
            $ipv_sql .= " AND item_site_id='".$site_id."'";
        }
        
        $ipv_result = $this->database->queryToArray($ipv_sql);
        
        if(count($ipv_result)){
            
            $usages = array();
            
            foreach($ipv_result as $r){
                
                $property = new SmartestItemProperty;
                $property->hydrate($r);
                
                $value = new SmartestItemPropertyValue;
                $value->hydrate($r);
                
                $item = SmartestCmsItem::retrieveByPk($r['item_id']);
                
                $u = new SmartestAssetUsageInstance;
                $u->setType('SM_ASSETUSAGETYPE_ITEMPROPERTY');
                $u->setAssetId($this->getId());
                $u->setItemProperty($property);
                $u->setItem($item);
                $u->setItemPropertyValue($value);
                
                $usages[$property->getId().$item->getId().'ipv'] = $u;
                
            }
            
            return $usages;
            
        }else{
            return array();
        }
        
    }
    
    public function getAttachmentsWhereUsed($site_id=null){
        
        if($this->isImage()){
            
            $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
        
            $attachments = $q->retrieve();
            
            $asset_ids = array();
            
            foreach($attachments as $key=>$att){
                $asset_ids[] = $att->getTextFragment()->getAssetId();
            }
            
            // This step eliminates the need for a call to the database for each asset where the file is used, and also ensures that usages on other sites are not shown.
            if(count($asset_ids)){
                
                $sql = "SELECT * FROM Assets WHERE Assets.asset_deleted !=1 AND asset_id IN ('".implode("','", $asset_ids)."')";
                
                if(is_numeric($site_id)){
                    $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1)";
                }
                
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $assets = array();
                    
                    foreach($result as $r){
                        $a = new SmartestAsset;
                        $a->hydrate($r);
                        $assets[$a->getId()] = $a;
                    }
                    
                    foreach($attachments as $k=>$att){
                        if(array_key_exists($att->getTextFragment()->getAssetId(), $assets)){
                            $attachments[$k]->setAttachmentName($k);
                            $attachments[$k]->setAsset($assets[$att->getTextFragment()->getAssetId()]);
                        }else{
                            unset($attachments[$k]);
                        }
                    }
                    
                    return $attachments;
                    
                }else{
                    return array();
                }
                
            }else{
                return array();
            }
            
        }
        
    }
    
    public function getAttachmentUsages($site_id=null){
        
        $attachments_where_used = $this->getAttachmentsWhereUsed($site_id);
        
        $usages = array();
        
        foreach($attachments_where_used as $att){
            
            /* $property = new SmartestItemProperty;
            $property->hydrate($r);
            
            $value = new SmartestItemPropertyValue;
            $value->hydrate($r);
            
            $item = SmartestCmsItem::retrieveByPk($r['item_id']); */
            
            $u = new SmartestAssetUsageInstance;
            $u->setType('SM_ASSETUSAGETYPE_ATTACHMENT');
            $u->setAssetId($this->getId());
            $u->setAttachment($att);
            
            $usages[$att->getId().'att'] = $u;
            
        }
        
        return $usages;
        
    }
    
    public function getUsage($site_id=null){
        
        $placeholders_usages = $this->getPlaceholderUsages($site_id);
        
        $ipv_usages = $this->getItemPropertyUsages($site_id);
        
        if($this->isImage()){
            $attachment_usages = $this->getAttachmentUsages($site_id);
        }else{
            $attachment_usages = array();
        }
        
        $all = array_merge($placeholders_usages, $ipv_usages, $attachment_usages);
        ksort($all);
        
        return $all;
        
    }

}