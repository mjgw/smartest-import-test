<?php

class SmartestItemSpaceDefinition extends SmartestAssetIdentifier{
    
    protected $_itemspace;
    protected $_draft_item;
    protected $_item;
    protected $_draft_simple_item;
    protected $_simple_item;
    protected $_loaded = false;
    
    public function __objectConstruct(){
        
        $this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
        
    }
    
    public function load($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            $this->_asset_class = new SmartestItemSpace;
            
            if($this->_asset_class->exists($name, $page->getSiteId())){
                
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    $this->_loaded = false;
                    return true;
                    
                }else{
                    // Placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }
            
        }
        
    }
    
    public function getSimpleItem($draft=false){
        
        if(!$this->_simple_item || ($draft && !$this->_draft_simple_item)){
            if((!$draft && is_object($this->_item)) || ($draft && is_object($this->_draft_item))){
                if($draft){
                    $this->_draft_simple_item = $this->_draft_item->getItem();
                }else{
                    $this->_simple_item = $this->_item->getItem();
                }
            }else{
                
                $item = new SmartestItem;
                
                if($draft){
                    $field = 'draft_asset_id';
                }else{
                    $field = 'live_asset_id';
                }
                
                if($item->hydrate($this->_properties[$field])){
                    if($draft){
                        $this->_draft_simple_item = $item;
                    }else{
                        $this->_simple_item = $item;
                    }
                }else{
                    return $item;
                }
                
            }
        }
        
        if($draft){
            return $this->_draft_simple_item;
        }else{
            return $this->_simple_item;
        }
        
    }
    
    public function getItem($simple=true, $draft=false){
        
        if($simple){
            
            if($draft){
                return $this->getSimpleItem(true);
            }else{
                return $this->getSimpleItem();
            }
                    
        }else{
            
            if($draft){
                if(!$this->_draft_item){
                    $this->_draft_item = SmartestCmsItem::retrieveByPk($this->_properties['draft_asset_id']);
                }
            }else{
                if(!$this->_item){
                    $this->_item = SmartestCmsItem::retrieveByPk($this->_properties['live_asset_id']);
                }
            }
            
            if($draft){
                return $this->_draft_item;
            }else{
                return $this->_item;
            }
            
        }
        
    }
    
    public function hasItem($draft){
        if($draft){
            return is_object($this->_draft_item);
        }else{
            return is_object($this->_item);
        }
    }
    
    public function hasTemplate(){
        return $this->getItemSpace()->getUsesTemplate();
    }
    
    public function getItemSpaceId($id){
        
        return $this->_properties['assetclass_id'];
        
    }
    
    public function setItemSpaceId($id){
        
        $id = (int) $id;
        $this->setField('assetclass_id', $id);
        
    }
    
    public function setDraftItemId($id){
        
        $id = (int) $id;
        $this->setField('draft_asset_id', $id);
        
    }
    
    public function getItemId($draft){
        
        if($draft){
            return $this->getField('draft_asset_id');
        }else{
            return $this->getField('live_asset_id');
        }
        
    }
    
    public function getItemSpace(){
        
        if(!$this->_asset_class){
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_id='".$this->_properties['assetclass_id']."'";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
                $ac = new SmartestItemSpace;
                $ac->hydrate($result[0]);
                $this->_asset_class = $ac;
            }
            
        }
        
        return $this->_asset_class;
        
    }
    
    public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        if(isset($array['assetclass_id'])){
            
            $itemspace = new SmartestItemSpace;
            $itemspace->hydrate($array);
            $this->_asset_class = $itemspace;
        
        }
        
        if(isset($array['page_id'])){
            
            $page = new SmartestPage;
            $page->hydrate($array);
            $this->_page = $page;
        
        }
        
        if(isset($array['item_id'])){
            $this->_item = SmartestCmsItem::retrieveByPk($array['item_id']);
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "itemspace":
            return $this->getItemSpace();
            case "draft_item":
            return $this->getItem(false, true);
            case "item":
            return $this->getItem(false, false);
        }
        
        return parent::offsetGet($offset);
        
    }
    
}