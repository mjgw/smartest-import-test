<?php

class SmartestBlockList extends SmartestSet{
    
    protected $_draft_mode = false;
    protected $_blocks = array();
    
    public function __objectConstruct(){
        // This prevents SmartestSet::__objectConstruct from being called, so do not delete.
        if(!strlen($this->getWebId())){
            $this->setWebId(SmartestStringHelper::random(32));
        }
        
        if(!$this->getType() || $this->getType() != 'SM_SET_BLOCKLIST'){
            $this->setType('SM_SET_BLOCKLIST');
        }
        
    }
    
    public function save(){
        
        if(!$this->getType() || $this->getType() != 'SM_SET_BLOCKLIST'){
            $this->setType('SM_SET_BLOCKLIST');
        }
        
        return parent::save();
    }
    
    public function getBlocks($override_non_draft_mode=false){
        
        $blocks = array();
        $sql = "SELECT * FROM Blocks WHERE Blocks.block_blocklist_id='".$this->getId()."'";
        
        if(!$this->getDraftMode() && !$override_non_draft_mode){
            $sql .= " AND block_status='SM_BLOCKSTATUS_PUBLISHED'";
        }
        
        $sql .= " ORDER BY block_order_index ASC";
        
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $raw_record){
            $block = new SmartestBlock;
            $block->hydrate($raw_record);
            $blocks[] = $block;
        }
        
        return $blocks;
        
    }
    
    public function getBlocksForReOrder(){
        
        $blocks = array();
        $sql = "SELECT * FROM Blocks WHERE Blocks.block_blocklist_id='".$this->getId()."' ORDER BY block_order_index ASC";
        
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $raw_record){
            $block = new SmartestBlock;
            $block->hydrate($raw_record);
            $blocks[$block->getId()] = $block;
        }
        
        return $blocks;
        
    }
    
    public function getNextBlockOrderIndex(){
        
        $sql = "SELECT block_order_index FROM Blocks WHERE Blocks.block_blocklist_id='".$this->getId()."' ORDER BY block_order_index DESC LIMIT 1";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            return (int) $result[0]['block_order_index']+1;
        }else{
            return 0;
        }
        
    }
    
    public function getMembers(){
        return $this->getBlocks();
    }
    
    public function getDraftMode(){
        return $this->_draft_mode;
    }
    
    public function setDraftMode($mode){
        $this->_draft_mode = (bool) $mode;
    }
    
    public function setStyleId(){
        
    }
    
    public function getStyleId(){
        
        if(!$this->getFilterValue()){
            $default_style = $this->getCurrentSite()->getDefaultBlockListStyle();
            $this->setFilterType('SM_SET_FILTERTYPE_BLOCKLISTSTYLE');
            $this->setFilterValue($default_style->getId());
            $this->save();
        }
        
        return $this->getFilterValue();
        
    }
    
    public function getStyle(){
        if($style_id = $this->getStyleId()){
            $style = new SmartestBlockListStyle;
            if($style->find($style_id)){
                return $style;
            }else{
                // style not found
            }
        }else{
            // this should be impossible, as default style should be used, and created if necessary, if there is none chosen
        }
    }
    
    public function setNewOrderFromString($string){
        
        $blocks = $this->getBlocksForReOrder();
        $ids = explode(',', $string);
        $oi = 0;
        
        foreach($ids as $id){
            $blocks[$id]->setOrderIndex($oi);
            $blocks[$id]->save();
            $oi++;
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'style_id':
            return $this->getStyleId();
            
            case 'style':
            return $this->getStyle();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}