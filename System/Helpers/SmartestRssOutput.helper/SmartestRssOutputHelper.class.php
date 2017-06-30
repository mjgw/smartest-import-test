<?php

// include 'XML/Serializer.php';

class SmartestRssOutputHelper{
    
    protected $_limit = 15;
    protected $_items = array();
    protected $_domObject;
    protected $_domRootTagElement;
    protected $_title;
    protected $_author;
    protected $_description;
    protected $_data_array = array();
    protected $_request;
    protected $_site;
    protected $_channel_image;
    
    public function __construct($data=null){
        
        if(is_array($data)){
            $this->_items = array_slice($data, 0, 20);
            $this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
        }else{
            // do nothing
        }
    }
    
    public function getXml(){
        
        if(class_exists('DOMDocument')){
			
			$this->_domObject = new DOMDocument('1.0');
	        $this->_domObject->formatOutput = true;
			$this->_domObject->loadXML('<?xml version="1.0" encoding="UTF-8" ?'.'><!-- generator="Smartest v'.SmartestInfo::$version.'" --><rss version="2.0" />');
	    
	        $this->_domRootTagElement = $this->_domObject->getElementsByTagName('rss')->item(0);
    	    $channel = $this->_domObject->createElement("channel");
    	    $this->_domRootTagElement->appendChild($channel);
	    
    	    $title = $this->_domObject->createElement("title");
    	    $title_text = $this->_domObject->createTextNode($this->getTitle());
    	    $title->appendChild($title_text);
            
            $description = $this->_domObject->createElement("description");
    	    $description_text = $this->_domObject->createCDATASection($this->getDescription());
    	    $description->appendChild($description_text);
            
    	    $link = $this->_domObject->createElement("link");
    	    $link_text = $this->_domObject->createTextNode($this->_site->getHomepageFullUrl());
    	    $link->appendChild($link_text);
            
    	    $image = $this->_domObject->createElement("image");
    	    $image_url = $this->_domObject->createElement("url");
            $image_title = $this->_domObject->createElement("title");
            $image_link = $this->_domObject->createElement("link");
            
            if($this->_channel_image instanceof SmartestImage){
                
                $image_url_text = $this->_domObject->createTextNode($this->_site->getTopLevelUrl().substr($this->_channel_image->getWebPath(), 1));
                $image_url->appendChild($image_url_text);
                $image->appendChild($image_url);
                
                $image_title_text = $this->_domObject->createTextNode($this->getTitle());
                $image_title->appendChild($image_title_text);
                $image->appendChild($image_title);
                
                $image_link_text = $this->_domObject->createTextNode($this->_site->getHomepageFullUrl());
                $image_link->appendChild($image_link_text);
                $image->appendChild($image_link);
                
            }
            
            $generator = $this->_domObject->createElement("generator");
    	    $generator_text = $this->_domObject->createTextNode('Smartest v'.SmartestInfo::$version);
    	    $generator->appendChild($generator_text);
            
            $ttl = $this->_domObject->createElement("ttl");
            $ttl_value = $this->_domObject->createTextNode('15');
            $ttl->appendChild($ttl_value);
	    
    	    $channel->appendChild($link);
    	    $channel->appendChild($title);
            $channel->appendChild($description);
            $channel->appendChild($ttl);
    	    $channel->appendChild($generator);
            if($this->_channel_image instanceof SmartestImage){
                $channel->appendChild($image);
            }
	    
    	    $this->addItems();
	    
    	    return $this->_domObject->saveXml();
	    
        }
        
    }
    
    public function getITunesXml(){
        
        if(class_exists('DOMDocument')){
			
			$this->_domObject = new DOMDocument('1.0');
	        $this->_domObject->formatOutput = true;
			$this->_domObject->loadXML('<?xml version="1.0" encoding="UTF-8" ?'.'><!-- generator="Smartest v'.SmartestInfo::$version.'" --><rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" />');
	    
	        $this->_domRootTagElement = $this->_domObject->getElementsByTagName('rss')->item(0);
    	    $channel = $this->_domObject->createElement("channel");
    	    $this->_domRootTagElement->appendChild($channel);
	    
    	    $author = $this->_domObject->createElement("author");
    	    $author_text = $this->_domObject->createTextNode($this->getAuthor());
    	    $author->appendChild($author_text);
            
            $description = $this->_domObject->createElement("description");
    	    $description_text = $this->_domObject->createCDATASection($this->getDescription());
    	    $description->appendChild($description_text);
	    
    	    $title = $this->_domObject->createElement("title");
    	    $title_text = $this->_domObject->createTextNode($this->getTitle());
    	    $title->appendChild($title_text);
	    
    	    $generator = $this->_domObject->createElement("generator");
    	    $generator_text = $this->_domObject->createTextNode('Smartest v'.SmartestInfo::$version);
    	    $generator->appendChild($generator_text);
            
            $ttl = $this->_domObject->createElement("ttl");
            $ttl_value = $this->_domObject->createTextNode('15');
            $ttl->appendChild($ttl_value);
	    
    	    $channel->appendChild($author);
    	    $channel->appendChild($title);
            $channel->appendChild($description);
    	    $channel->appendChild($ttl);
            $channel->appendChild($generator);
	    
    	    $this->addItems();
	    
    	    return $this->_domObject->saveXml();
	    
        }
        
    }
    
    public function send(){
        header("Cache-Control: public, must-revalidate\r\n");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\r\n");
        header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ). ' GMT'."\r\n");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/rss+xml; charset=utf-8');
        // header('Content-Type: text/xml; charset=utf-8');
        // header('Content-Type: text/plain; charset=utf-8');
        // $this->getXml();
        echo $this->getXml();
        exit;
    }
    
    public function sendAtom(){
        header("Cache-Control: public, must-revalidate\r\n");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\r\n");
        header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ). ' GMT'."\r\n");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: text/plain; charset=utf-8');
        // $this->getXml();
        echo $this->getXml();
        exit;
    }
    
    public function sendITunes(){
        header("Cache-Control: public, must-revalidate\r\n");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\r\n");
        header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ). ' GMT'."\r\n");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: text/plain; charset=utf-8');
        // $this->getXml();
        echo $this->getITunesXml();
        exit;
    }
    
    public function setLimit($limit){
        if(is_numeric($limit)){
            $this->_limit = ceil($limit);
        }
    }
    
    public function getSite(){
        return $this->_site;
    }
    
    public function setSite(SmartestSite $s){
        $this->_site = $s;
    }
    
    public function getLink(){
        return $this->_link;
    }
    
    public function setLink(SmartestExternalUrl $link){
        $this->_link = $link;
    }
    
    public function getTitle(){
        return $this->_title;
    }
    
    public function setTitle($t){
        $this->_title = $t;
    }
    
    public function getAuthor(){
        return $this->_author;
    }
    
    public function setAuthor($t){
        $this->_author = $t;
    }
    
    public function getDescription(){
        return $this->_description;
    }
    
    public function setDescription($t){
        $this->_description = $t;
    }
    
    public function getImage(){
        return $this->_channel_image;
    }
    
    public function setImage($image){
        if($image instanceof SmartestImage){
            $this->_channel_image = $image;
        }elseif($image instanceof SmartestAsset && $image->isBinaryImage()){
            $this->_channel_image = $image->getImage();
        }else{
            // Supplied value for $image was not compatible
        }
    }
    
    public function addItems(){
        
        // var_dump($this->_items);
        
        foreach($this->_items as $object){
            
            $channel = $this->_domObject->getElementsByTagName('channel')->item(0);
	        $item = $this->_domObject->createElement("item");
	        
	        $title = $this->_domObject->createElement("title");
	        $title_text = $this->_domObject->createTextNode($object->getTitle());
	        $title->appendChild($title_text);
	        
	        $description = $this->_domObject->createElement("description");
	        $description_text = $this->_domObject->createCDATASection($object->getDescription());
	        $description->appendChild($description_text);
	    
	        $pubDate = $this->_domObject->createElement("pubDate");
            // var_dump( date('r', $object->getDate()) );
	        $pubDate_text = $this->_domObject->createTextNode(date('r', $object->getDate()->getUnixFormat()));
	        $pubDate->appendChild($pubDate_text);
	        
	        $link = $this->_domObject->createElement("link");
            $guid = $this->_domObject->createElement("guid");
            
            $url = $this->_request->getUrlProtocol().$_SERVER['HTTP_HOST'].$object->getUrl();
            
	        $link_text = $this->_domObject->createTextNode($url);
            $guid_text = $this->_domObject->createTextNode($url);
            
	        $link->appendChild($link_text);
            $guid->appendChild($guid_text);
	        
	        $item->appendChild($title);
    	    $item->appendChild($description);
    	    $item->appendChild($link);
            $item->appendChild($guid);
    	    $item->appendChild($pubDate);
	        
	        $channel->appendChild($item);
	    
        }
        
    }
    
}