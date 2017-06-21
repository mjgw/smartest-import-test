<?php

class SmartestBlockListStyle extends SmartestTag{
    
    public function save(){
        
        if(!$this->getType() || $this->getType() != 'SM_TAGTYPE_BLOCKLISTSTYLE'){
            $this->setType('SM_TAGTYPE_BLOCKLISTSTYLE');
        }
        
        return parent::save();
    }
    
    public function getBlockTemplates(){
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_BLOCKLISTSTYLE_TPL');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
	    
	    if(!$this->getIsSystem()){
	        $q->addForeignTableConstraint('Assets.asset_is_hidden', 0);
	    }
        
        $q->addForeignTableConstraint('Assets.asset_is_archived', '0');
        $q->addSortField('Assets.asset_label');
        $result = $q->retrieve();
        
        return $result;
        
    }
    
    public function isValidTemplateId($id){
        return in_array($id, $this->getTemplateIds());
    }
    
    public function getTemplateIds(){
        
        $templates = $this->getBlockTemplates();
        $ids = array();
        
        foreach($templates as $t){
            $ids[] = $t->getId();
        }
        
        return $ids;
        
    }
    
    public function addBlockTemplate(SmartestTemplateAsset $template){
        if($template->getId()){
            return $this->addBlockTemplateById($template->getId());
        }else{
            return false;
        }
    }
    
    public function addBlockTemplateById($template_id){
        $mtmh = new SmartestManyToManyHelper;
        if($assoc = $mtmh->createLookupObject('SM_MTMLOOKUP_BLOCKLISTSTYLE_TPL', $this->getId(), $template_id)){
            return true;
        }
    }
    
    public function removeBlockTemplateById($template_id){
        
    }
    
    public function getStylesheets(){
        
    }
    
    public function addStylesheetById($stylesheet_id){
        $mtmh = new SmartestManyToManyHelper;
        if($assoc = $mtmh->createLookupObject('SM_MTMLOOKUP_BLOCKLISTSTYLE_CSS', $this->getId(), $stylesheet_id)){
            return true;
        }
    }
    
    public function removeStylesheetById(){
        
    }
    
    public function getNextStylesheetOrderIndex(){
        
        if($this->getIsGallery()){
            
            $sql = "SELECT ManyToManyLookups.mtmlookup_order_index FROM Assets, Sets, ManyToManyLookups WHERE ManyToManyLookups.mtmlookup_type='SM_MTMLOOKUP_BLOCKLISTSTYLE_CSS' AND ManyToManyLookups.mtmlookup_entity_2_foreignkey=Assets.asset_id AND (ManyToManyLookups.mtmlookup_entity_1_foreignkey='".$this->getId()."' AND ManyToManyLookups.mtmlookup_entity_1_foreignkey=Tags.tag_id) AND Assets.asset_deleted ='0' AND Assets.asset_is_hidden ='0' AND Assets.asset_is_archived ='0' ORDER BY ManyToManyLookups.mtmlookup_order_index DESC";
            $result = $this->database->queryToArray($sql, true);
            // echo $sql;
            if(count($result)){
                $current_highest = (int) $result[0]['mtmlookup_order_index'];
                return $current_highest+1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
        
    }
    
    public function getScripts(){
        
    }
    
    public function addScriptById(){
        
    }
    
    public function removeScriptById(){
        
    }
    
    public function getNextScriptOrderIndex(){
        
    }
    
}