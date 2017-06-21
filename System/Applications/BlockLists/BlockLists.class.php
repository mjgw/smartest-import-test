<?php

class BlockLists extends SmartestSystemApplication{
    
    public function createBlock(){
        
        $blocklist = new SmartestBlockList;
        $blocklist_id = $this->getRequestParameter('blocklist_id');
        
        if($blocklist->find($blocklist_id)){
            $this->send($blocklist, 'blocklist');
            $style = $blocklist->getStyle();
            $this->send($style, 'style');
            $this->send($style->getBlockTemplates(), 'style_templates');
        }
        
    }
    
    public function editBlock(){
        
        
        
    }
    
    public function updateBlock(){
        
        
        
    }
    
    public function deleteBlock(){
        
        
        
    }
    
    //////////////// Functions below are temporary, for development purposes ///////////////
    
    public function listBlockLists(){
        
        
        
    }
    
    public function createBlockList(){
        
        $default_style = $this->getSite()->getDefaultBlockListStyle();
        $this->send($default_style, 'default_style');
        
        $styles = $this->getSite()->getBlockListStyles();
        $this->send($styles, 'blocklist_styles');
        
    }
    
    public function insertBlockList(){
        
        $b = new SmartestBlockList;
        $b->setLabel($this->getRequestParameter('blocklist_name'));
        $b->setName(SmartestStringHelper::toVarName($this->getRequestParameter('blocklist_name')));
        $b->setSiteId($this->getSite()->getId());
        $b->setStyleId($this->getRequestParameter('blocklist_style_id'));
        $b->save();
        $this->redirect('@edit_blocklist?blocklist_id='.$b->getId());
        
    }
    
    ///////////////// End temporary functions /////////////////
    
    public function editBlockList(){
        
        $blocklist = new SmartestBlockList;
        
        $styles = $this->getSite()->getBlockListStyles();
        $this->send($styles, 'blocklist_styles');
        
        if($blocklist->find($this->getRequestParameter('blocklist_id'))){
            $this->send($blocklist, 'blocklist');
            $this->send($blocklist->getBlocks(true), 'blocks');
        }
        
    }
    
    public function updateBlockList(){
        
        $blocklist = new SmartestBlockList;
        
        if($blocklist->find($this->getRequestParameter('blocklist_id'))){
            $blocklist->setLabel($this->getRequestParameter('blocklist_name'));
            $blocklist->save();
        }
        
        $this->redirect('@edit_blocklist?blocklist_id='.$blocklist->getId());
        
    }
    
    //////////////////////////// Blocklist Styles ///////////////////////////////
    
    public function createBlockListStyle(){
        
    }
    
    public function insertBlockListStyle(){
        
    }
    
    public function editBlockListStyle(){
        
        $style = new SmartestBlockListStyle;
        
        if($style->find($this->getRequestParameter('style_id'))){
            $this->send($style, 'style');
            $this->send(true, 'style_exists');
            $this->send($style->getBlockTemplates(), 'style_templates');
        }else{
            $this->send(false, 'style_exists');
        }
        
    }
    
    public function listBlockListStyles(){
        
        $default_style = $this->getSite()->getDefaultBlockListStyle();
        $this->send($default_style, 'default_style');
        
        $styles = $this->getSite()->getBlockListStyles();
        $this->send($styles, 'blocklist_styles');
        
    }
    
}