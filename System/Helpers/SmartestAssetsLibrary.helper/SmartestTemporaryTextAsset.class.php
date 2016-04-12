<?php

// A class designed to hold the information needed to create a text asset in the session until the creation of the asset is confirmed

class SmartestTemporaryTextAsset{

    protected $_content;
    protected $_created;
    protected $_site_id;
    protected $_shared = null;
    protected $_user_id;
    protected $_label;
    protected $_type = 'SM_ASSETTYPE_RICH_TEXT';
    
    public function __construct($label, $content){
        
        $this->setLabel($label);
        $this->setContent($content);
        $this->setCreated(time());
        
        if(is_object(SmartestSession::get('user'))){
            $this->setAuthor(SmartestSession::get('user')->getId());
        }
        
    }
    
    public function setContent($v){
        $this->_content = $v;
    }
    
    public function getContent(){
        return $this->_content;
    }
    
    public function getContentForEditor(){
        return htmlspecialchars($this->getContent(), ENT_COMPAT, 'UTF-8');
    }
    
    public function setCreated($v){
        $this->_created = (int) $v;
    }
    
    public function setSiteId($v){
        $this->_site_id = (int) $v;
    }
    
    public function setShared($v){
        $this->_shared = (int) (bool) $v;
    }
    
    public function setAuthor($v){
        $this->_user_id = (int) $v;
    }
    
    public function setLabel($v){
        $this->_label = $v;
    }
    
    public function setType($v){
        $this->_type = in_array($v, array('SM_ASSETTYPE_RICH_TEXT', 'SM_ASSETTYPE_TEXTILE_TEXT', 'SM_ASSETTYPE_PLAIN_TEXT')) ? $v : 'SM_ASSETTYPE_RICH_TEXT';
    }
    
    // Save a real text asset to the database
    public function savePermanentTextAsset($label=null){
        $l = $label ? $label : $this->_label;
        $ch = new SmartestAssetCreationHelper($this->_type);
        $ch->createNewAssetFromTextArea($this->_content, $l);
        $asset = $ch->finish();
        if(is_numeric($this->_user_id)) $asset->setUserId($this->_user_id);
        if(is_numeric($this->_created)) $asset->setCreated($this->_created);
        if(is_numeric($this->_site_id)) $asset->setSiteId($this->_site_id);
        if($this->_shared !== null) $asset->setShared((int) $this->_shared);
        $asset->save();
        return $asset;
    }

}