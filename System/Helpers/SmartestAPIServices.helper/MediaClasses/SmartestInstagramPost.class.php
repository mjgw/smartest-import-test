<?php

class SmartestInstagramPost implements ArrayAccess{
    
    protected $_post_id, $_type, $_longitude, $_latitude, $_location_name, $_created_at, $_instagram_url, $_num_likes, $_file_url, $_image, $_caption, $_user;
    
    public function __construct($raw_instagram_data){
        
        $this->_post_id = $raw_instagram_data->id;
        // $this->_user = new SmartestInstagramUser
        $this->_type = $raw_instagram_data->type == 'image' ? 'SM_INSTAGRAMPOSTTYPE_IMAGE' : 'SM_INSTAGRAMPOSTTYPE_MOVIE';
        
        if($raw_instagram_data->location){
            $this->_longitude = $raw_instagram_data->location->longitude;
            $this->_latitude  = $raw_instagram_data->location->latitude;
            $this->_location_name = $raw_instagram_data->location->name;
        }
        
        $this->_created_at = new SmartestDateTime($raw_instagram_data->created_time);
        $this->_instagram_url = new SmartestExternalUrl($raw_instagram_data->link);
        $this->_num_likes = $raw_instagram_data->likes->count;
        $this->_file_url = new SmartestExternalUrl($raw_instagram_data->images->standard_resolution->url);
        
        if($raw_instagram_data->caption){
            $this->_caption = $raw_instagram_data->caption->text;
        }else{
            $this->_caption = '';
        }
        
    }
    
    public function getImage(){
        // echo $this->_file_url;
        if(is_file(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/instagram_post_'.$this->_post_id.'.jpg')){
            $this->_image = new SmartestImage;
            $this->_image->loadFile(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/instagram_post_'.$this->_post_id.'.jpg');
            return $this->_image;
        }elseif($img_file_name = $this->_file_url->downloadContentTo('Public/Resources/System/Cache/Images/instagram_post_'.$this->_post_id.'.jpg')){
            $this->_image = new SmartestImage;
            $this->_image->loadFile($img_file_name);
            return $this->_image;
        }else{
            return null;
        }
    }
    
    public function saveAsInstagramPost($label='DEFAULT', $create_thumbnail_jpg=true){
        
        $ach = new SmartestAssetCreationHelper('SM_ASSETTYPE_INSTAGRAM_IMAGE');
        $default_label = strlen($this->_caption) ? $this->_caption : 'Untitled Instagram Image';
        $asset_label = ($label == 'DEFAULT') ? $default_label : $label;
        
        try{
            if($ach->createNewAssetFromUrl($this->_instagram_url, $asset_label, false)){
                $asset = $ach->finish();
                if($create_thumbnail_jpg){
                    if($jpg_asset = $this->saveAsJPGImage($label)){
                        $asset->setThumbnailId($jpg_asset->getId());
                        $asset->save();
                    }
                }
                return $asset;
            }
        }catch(SmartestAssetCreationException $e){
            SmartestLog::getInstance('system')->log($e->getMessage(), SmartestLog::ERROR);
            return null;
        }
    }
    
    public function saveAsJPGImage($label='DEFAULT'){
        
        if($img_file_name = $this->_file_url->downloadContentTo('Public/Resources/Images/imported_instagram_post_'.$this->_post_id.'.jpg')){
            
            $filename = basename($img_file_name);
            $default_label = strlen($this->_caption) ? $this->_caption : 'Untitled Instagram Image as JPG';
            $asset_label = ($label == 'DEFAULT') ? $default_label : $label;
            $ach = new SmartestAssetCreationHelper('SM_ASSETTYPE_JPEG_IMAGE');
            
            try{
                if($ach->createNewAssetFromUnImportedFile($filename, $asset_label.' (JPEG)')){
                    $jpg_asset = $ach->finish();
                    return $jpg_asset;
                }
            }catch(SmartestAssetCreationException $e){
                SmartestLog::getInstance('system')->log($e->getMessage(), SmartestLog::ERROR);
                return null;
            }
            
        }
        
    }
    
    public function offsetExists($offset){}
    public function offsetUnset($offset){}
    public function offsetSet($offset, $value){}
        
    public function offsetGet($offset){
        
        switch($offset){
            
            case "id":
            case "post_id":
            return $this->_post_id;
            
            case "type":
            return $this->_type;
            
            case "longitude":
            return $this->_longitude;
            
            case "latitude":
            return $this->_latitude;
            
            case "location_name":
            return $this->_location_name;
            
            case "created_at":
            return $this->_created_at;
            
            case "instagram_url":
            case "igurl":
            return $this->_instagram_url;
            
            case "num_likes":
            return $this->_num_likes;
            
            case "file_url":
            case "remote_file_url":
            case "remote_image_url":
            return $this->_file_url;
            
            case "image":
            return $this->getImage();
            
            case "caption":
            return new SmartestString($this->_caption);
            
        }
        
    }

}