<?php

class SmartestInstagramUser{
    
    protected $_username, $_account_id, $_bio, $_website_url, $_profile_picture_url, $_profile_picture_image;
    
    public function __construct($raw_instagram_data){
        
        $this->_account_id = $raw_instagram_data->id;
        $this->_username = $raw_instagram_data->username;
        $this->_full_name = $raw_instagram_data->full_name;
        $this->_bio = new SmartestString($raw_instagram_data->bio);
        $this->_website_url = new SmartestExternalUrl($raw_instagram_data->website);
        $this->_profile_picture_url = new SmartestExternalUrl($raw_instagram_data->profile_picture);
        
    }
    
    public function getId(){
        return $this->_account_id;
    }
    
    public function getUsername(){
        return $this->_username;
    }
    
    public function getFullName(){
        return $this->_full_name;
    }
    
    public function getBio(){
        return $this->_bio;
    }
    
    public function getWebsiteUrl(){
        return $this->_website_url;
    }
    
    public function getProfilePictureUrl(){
        return $this->_profile_picture_url;
    }
    
    public function getProfilePicture(){
        if(is_file(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/instagram_'.$this->_username.'.jpg')){
            $this->_profile_picture_image = new SmartestImage;
            $this->_profile_picture_image->loadFile(SM_ROOT_DIR.'Public/Resources/System/Cache/Images/instagram_'.$this->_username.'.jpg');
            return $this->_profile_picture_image;
        }elseif($img_file_name = $this->_profile_picture_url->downloadContentTo('Public/Resources/System/Cache/Images/instagram_'.$this->_username.'.jpg')){
            $this->_profile_picture_image = new SmartestImage;
            $this->_profile_picture_image->loadFile($img_file_name);
            return $this->_profile_picture_image;
        }else{
            return null;
        }
    }

}