<?php

// The class for individual "tweets"

class SmartestTwitterStatus extends SmartestObject{
    
    protected $_status_id, $_retweeting_status_id, $_retweeting_user, $_is_retweet, $_created_at, $_text, $_num_retweets, $_num_favourites, $_full_url, $_urls, $_user, $_service_name, $_service_link, $_quoted_status, $_mentioned_users, $_media;
    
    public function __construct($raw_twitter_data){
        
        // print_r($raw_twitter_data);
        $this->_urls = array();
        $this->_mentioned_users = array();
        
        if(isset($raw_twitter_data->retweeted_status) && is_object($raw_twitter_data->retweeted_status) && $raw_twitter_data->retweeted_status->id){
            $this->_is_retweet = true;
            $this->_retweeting_status_id = $raw_twitter_data->id_str;
            $this->_retweeting_user = new SmartestTwitterUser($raw_twitter_data->user);
            $raw_twitter_data = $raw_twitter_data->retweeted_status;
        }else{
            $this->_is_retweet = false;
            $this->_retweeting_status_id = null;
        }
        
        $time = strtotime($raw_twitter_data->created_at);
        $this->_created_at = new SmartestDateTime($time);
        $this->_status_id = $raw_twitter_data->id_str;
        $this->_full_url = new SmartestExternalUrl('https://twitter.com/'.$raw_twitter_data->user->screen_name.'/status/'.$raw_twitter_data->id_str);
        $raw_text = $raw_twitter_data->text;
        
        if(count($raw_twitter_data->entities->urls) && is_array($raw_twitter_data->entities->urls)){
            foreach($raw_twitter_data->entities->urls as $twitter_url){
                $raw_text = str_replace($twitter_url->url, $twitter_url->expanded_url, $raw_text);
                $this->_urls[] = new SmartestExternalUrl($twitter_url->expanded_url);
            }
        }
        
        if(count($raw_twitter_data->entities->user_mentions) && is_array($raw_twitter_data->entities->user_mentions)){
            foreach($raw_twitter_data->entities->user_mentions as $twitter_mention){
                $this->_mentioned_users[] = $twitter_mention;
            }
        }
        
        $this->_text = $raw_text;
        $this->_user = new SmartestTwitterUser($raw_twitter_data->user);
        $this->_service_name = strip_tags($raw_twitter_data->source);
        $this->_service_link = str_replace(' rel="nofollow"', '', $raw_twitter_data->source);
        $this->_num_retweets = $raw_twitter_data->retweet_count;
        $this->_num_favourites = $raw_twitter_data->favorite_count;
        
        if(isset($raw_twitter_data->quoted_status) && is_object($raw_twitter_data->quoted_status) && $raw_twitter_data->quoted_status->id){
            $this->_quoted_status = new SmartestTwitterStatus($raw_twitter_data->quoted_status);
        }
        
        $this->_media = array();
        if(isset($raw_twitter_data->extended_entities->media) && isset($raw_twitter_data->extended_entities->media) && is_array($raw_twitter_data->extended_entities->media) && count($raw_twitter_data->extended_entities->media)){
            foreach($raw_twitter_data->extended_entities->media as $raw_media){
                $this->_media[] = new SmartestTwitterMediaFile($raw_media);
            }
        }
        
        // print_r($this);
        
    }

}