<?php

class SmartestItemPrivateComment extends SmartestComment{
    
    protected $_item;
    protected $_author;
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_COMMENTTYPE_ITEM_PRIVATE');
        }
        
        if(!$this->getStatus()){
            $this->setStatus('SM_COMMENTSTATUS_APPROVED');
        }
        
        parent::save();
        
    }
    
    public function setItemId($id){
        
        $this->setObjectId($id);
        
    }
    
    public function getItemId(){
        
        return $this->getObjectId();
        
    }
    
    public function getAuthor(){
        
        if(!is_object($this->_author)){
            
            $author = new SmartestUser;
            
            if($author->find($this->getAuthorUserId())){
                $this->_author = $author;
            }
            
        }
        
        return $this->_author;
        
    }
    
    public function hydrateWithSimpleItem($data){
        
        if($this->hydrate($data)){
            
            $item = new SmartestItem;
            
            if($item->hydrate($data)){
                $this->_simple_item = $item;
                return true;
            }else{
                return false;
            }
            
        }else{
            return false;
        }
        
    }
    
    public function getItem(){
        
        if($this->_item instanceof SmartestCmsItem){
        
            /* $item = new SmartestItem;
        
            if($item->find($this->getObjectId())){
                
                return $item;
            
            } */
            
            return $this->_item;
        
        }else{
            
            if($item = SmartestCmsItem::retrieveByPk($this->getObjectId())){
                $this->_item = $item;
                return $this->_item;
            }
            
        }
        
    }
    
    public function getSimpleItem(){
        
        if(!$this->_item instanceof SmartestCmsItem){
        
            $item = new SmartestItem;
        
            if($item->find($this->getObjectId())){
                
                return $item;
            
            }
        
        }else{
            
            return $this->_item->getItem();
            
        }
        
    }
    
    /* public function approve(){
        
        $this->setStatus('SM_COMMENTSTATUS_APPROVED');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) + 1);
        $item->save();
        
        $item->refreshCache();
        
    }
    
    public function makePending(){
        
        $old_status = $this->getStatus();
        
        $this->setStatus('SM_COMMENTSTATUS_PENDING');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) - 1);
        $item->save();
        
        if($old_status == 'SM_COMMENTSTATUS_APPROVED'){
            $item->refreshCache();
        }
        
    }
    
    public function reject(){
        
        $old_status = $this->getStatus();
        
        $this->setStatus('SM_COMMENTSTATUS_REJECTED');
        $this->save();
        
        $item = $this->getSimpleItem();
        $item->setNumComments(((int) $item->getNumComments()) - 1);
        $item->save();
        
        if($old_status == 'SM_COMMENTSTATUS_APPROVED'){
            $item->refreshCache();
        }
        
    } */
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "item":
            return $this->_item;
            
            case "author":
            case "user":
            return $this->getAuthor();
            
            case "posted_at":
            return new SmartestDateTime($this->getPostedAt());
            
            case "content":
            return new SmartestString($this->getContent());
            
        }
        
        if($this->_item instanceof SmartestCmsItem){
            
            if(strtolower($offset) == SmartestStringHelper::toVarName($this->_item->getModel())){
                
                return $this->_item;
                
            }
            
        }
        
        return parent::offsetGet($offset);
        
    }
  
}