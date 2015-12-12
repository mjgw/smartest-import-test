<?php

class SmartestPageDownload extends SmartestManyToManyLookup{
    
    protected $_asset;
    protected $_page;
    
    public function hydrate($raw_data){
        
        if(isset($raw_data['asset_id'])){
            $asset = new SmartestRenderableAsset;
            $asset->hydrate($raw_data);
            $this->_asset = $asset;
        }
        
        if(isset($raw_data['page_id'])){
            $page = new SmartestPage;
            $page->hydrate($raw_data);
            $this->_page = $page;
        }
        
        return parent::hydrate($raw_data);
        
    }
    
    public function getAsset(){
        
        if(!$this->_asset){
            $asset = new SmartestRenderableAsset;
            if($asset->find($this->getAssetId())){
                $this->_asset = $asset;
            }
        }
        
        return $this->_asset;
    }
    
    public function getAssetId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setAssetId($id){
        return $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getPage(){
        
        if(!$this->_page){
            $page = new SmartestPage;
            if($page->find($this->getPageId())){
                $this->_page = $page;
            }
        }
        
        return $this->_page;
    }
    
    public function getPageId(){
        return $this->getEntityForeignKeyValue(2);
    }
    
    public function setPageId($id){
        return $this->setEntityForeignKeyValue(2, (int) $id);
    }
    
    public function getLabel(){
        return $this->getContextDataField('label');
    }
    
    public function setLabel($label){
        return $this->setContextDataField('label', $label);
    }
    
    public function __toJson(){
        $obj = $this->__toSimpleObject();
        $obj->label = $this->getLabel();
        return json_encode($obj);
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_MTMLOOKUP_PAGE_DOWNLOADS');
        }
        
        return parent::save();
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "file":
            case "asset":
            return $this->getAsset();
            
            case "label":
            return new SmartestString($this->getLabel());
            
            case "position":
            return new SmartestNumeric($this->getOrderIndex() + 1);
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}