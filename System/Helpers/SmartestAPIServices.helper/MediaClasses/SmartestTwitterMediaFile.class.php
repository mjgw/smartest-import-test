<?php

class SmartestTwitterMediaFile extends SmartestObject{

    protected $_media_id, $_file_url, $_file_url_https, $_twitter_url_short, $_twitter_url_long, $_type, $_image;
    
    public function __construct($raw_twitter_data){
        print_r($raw_twitter_data);
    }

}