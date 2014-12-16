<?php

class SmartestAssetUsageInstance implements ArrayAccess{
    
    protected $_placeholder;
    protected $_page;
    protected $_placeholder_definition;
    
    protected $_itemproperty;
    protected $_item;
    protected $_itempropertyvalue;
    
    protected $_attachment;
    
    protected $_type;
    
    protected $_asset_id = null;
    
    public function setAssetId($id){
        $this->_asset_id = $id;
    }
    
    public function getAssetId(){
        return $this->_asset_id;
    }
    
    public function setAttachment(SmartestTextFragmentAttachment $att){
        $this->_attachment = $att;
    }
    
    public function getAttachment(){
        return $this->_attachment;
    }
    
    public function setPlaceholder(SmartestPlaceholder $p){
        $this->_placeholder = $p;
    }
    
    public function getPlaceholder(){
        return $this->_placeholder;
    }
    
    public function setPage(SmartestPage $p){
        $this->_page = $p;
    }
    
    public function getPage(){
        return $this->_page;
    }
    
    public function setDefinition(SmartestPlaceholderDefinition $p){
        $this->_placeholder_definition = $p;
    }
    
    public function getDefinition(){
        return $this->_placeholder_definition;
    }
    
    public function setItemProperty(SmartestItemProperty $p){
        $this->_itemproperty = $p;
    }
    
    public function getItemProperty(){
        return $this->_itemproperty;
    }
    
    public function setItem(SmartestCmsItem $item){
        $this->_item = $item;
    }
    
    public function getItem(){
        return $this->_item;
    }
    
    public function setItemPropertyValue(SmartestItemPropertyValue $v){
        $this->_itempropertyvalue = $v;
    }
    
    public function getItemPropertyValue(){
        return $this->_itempropertyvalue;
    }
    
    public function getType(){
        return $this->_type;
    }
    
    public function setType($type){
        $this->_type = $type;
    }
    
    public function isDualModed(){
        if($this->_type == 'SM_ASSETUSAGETYPE_ITEMPROPERTY' || $this->_type == 'SM_ASSETUSAGETYPE_PLACEHOLDER'){
            return true;
        }else{
            return false;
        }
    }
    
    public function getMode(){
        
        if($this->_type == 'SM_ASSETUSAGETYPE_ITEMPROPERTY'){
            if($this->_itempropertyvalue->getField('content') == $this->_itempropertyvalue->getField('draft_content')){
                return 'PERFECT';
            }elseif($this->_itempropertyvalue->getField('content') == $this->getAssetId()){
                return 'LIVE';
            }else{
                return 'DRAFT';
            }
        }elseif($this->_type == 'SM_ASSETUSAGETYPE_PLACEHOLDER'){
            if($this->_placeholder_definition->getField('draft_asset_id') == $this->_placeholder_definition->getField('live_asset_id')){
                return 'PERFECT';
            }elseif($this->_placeholder_definition->getField('live_asset_id') == $this->getAssetId()){
                return 'LIVE';
            }else{
                return 'DRAFT';
            }
        }else{
            return 'PERFECT';
        }
        
    }
    
    public function getNiceType(){
        
        switch($this->getType()){
            
            case "SM_ASSETUSAGETYPE_ITEMPROPERTY":
            return "Item property value";
            
            case "SM_ASSETUSAGETYPE_PLACEHOLDER":
            return "Placeholder definition";
            
            case "SM_ASSETUSAGETYPE_ATTACHMENT":
            return "File attachment";
            
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "type":
            return $this->getType();
            
            case "nice_type":
            return $this->getNiceType();
            
            case "mode":
            return $this->getMode();
            
            case "placeholder":
            return $this->getPlaceholder();
            
            case "page":
            return $this->getPage();
            
            case "item":
            return $this->getItem();
            
            case "property":
            case "itemproperty":
            return $this->getItemProperty();
            
            case "host_file":
            return $this->getAttachment()->getAsset();
            
            case "attachment":
            return $this->getAttachment();
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}