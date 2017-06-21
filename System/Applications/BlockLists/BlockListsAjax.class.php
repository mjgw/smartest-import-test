<?php

class BlockListsAjax extends SmartestSystemApplication{
    
    public function setBlockListStyleLabelFromInPlaceEditField(){
        
        $style = new SmartestBlockListStyle;
        
        if($style->find($this->getRequestParameter('style_id'))){
            $new_label = strip_tags($this->getRequestParameter('new_label'));
            $style->setLabel($new_label);
            $style->save();
            echo $new_label;
            exit;
        }else{
            
        }
        
    }
    
    public function insertBlock(){
        
        $blocklist = new SmartestBlockList;
        $blocklist_id = $this->getRequestParameter('blocklist_id');
        
        if($blocklist->find($blocklist_id)){
            // $this->send($blocklist, 'blocklist');
            if($style = $blocklist->getStyle()){
                if($style->isValidTemplateId($this->getRequestParameter('template_id'))){
                    
                    $template_id = $this->getRequestParameter('blocklist_id');
                    
                    $block = new SmartestBlock;
                    $block->setDraftAssetId($template_id);
                    $block->setBlockListId($blocklist->getId());
                    $block->setWebId(SmartestStringHelper::random(64));
                    $block->setCreated(time());
                    $block->setStatus('SM_BLOCKSTATUS_DRAFT');
                    $block->setType('SM_BLOCKTYPE_TOPLEVEL');
                    $block->setOrderIndex($blocklist->getNextBlockOrderIndex());
                    
                    if(strlen($this->getRequestParameter('block_title'))){
                        $block->setTitle($this->getRequestParameter('block_title'));
                        $block->setName(SmartestStringHelper::toVarName($this->getRequestParameter('block_title')).'_'.SmartestStringHelper::random(8));
                    }else{
                        $title = 'Unnamed block '.SmartestStringHelper::random(8);
                        $block->setTitle($title);
                        $block->setName(SmartestStringHelper::toVarName($title));
                    }
                    
                    $block->save();
                    
                }
            }
            // $this->send($style, 'style');
            // $this->send($style->getBlockTemplates(), 'style_templates');
            // echo $blocklist->getNextBlockOrderIndex();
            
        }
        
        // exit;
        
    }
    
    public function listOrderableBlocksForEditor(){
        
        $blocklist = new SmartestBlockList;
        $blocklist_id = $this->getRequestParameter('blocklist_id');
        
        if($blocklist->find($blocklist_id)){
            $this->send($blocklist, 'blocklist');
            $style = $blocklist->getStyle();
            $this->send($style, 'style');
            $this->send($style->getBlockTemplates(), 'style_templates');
            $this->send($blocklist->getBlocks(true), 'blocks');
        }
        
    }
    
    public function updateBlockOrder(){
        
        $blocklist = new SmartestBlockList;
        $blocklist_id = $this->getRequestParameter('blocklist_id');
        
        if($blocklist->find($blocklist_id)){
            if($this->getRequestParameter('new_order')){
                $blocklist->setNewOrderFromString($this->getRequestParameter('new_order'));
                header('HTTP/1.1 200 OK');
            }
        }
        
    }
    
}