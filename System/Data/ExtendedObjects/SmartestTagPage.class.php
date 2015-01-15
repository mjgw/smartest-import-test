<?php

class SmartestTagPage extends SmartestPage{
    
    protected $_tag;
    protected $_model;
    
    public function assignTag(SmartestTag $tag){
        $this->_tag = $tag;
        $this->_tag->setDraftMode($this->getDraftMode());
        if(is_object($this->_model)){
            $this->_tag->addFilter('model_id', $this->_model->getId());
        }
    }
    
    public function getTag(){
        $this->_tag->setDraftMode($this->getDraftMode());
        return $this->_tag;
    }
    
    public function getTitle($force_static=false){
        if(is_object($this->_tag) && !$force_static){
            if(is_object($this->_model)){
                return $this->_model->getPluralName().' tagged ‘'.$this->_tag->getLabel().'’';
            }else{
                return $this->_tag->getLabel();
            }
        }else{
            return $this->_properties['title'];
        }
    }
    
    public function assignModel(SmartestModel $model){
        $this->_model = $model;
        if(is_object($this->_tag)){
            $this->_tag->addFilter('model_id', $this->_model->getId());
        }
    }
    
    public function getModel(){
        return $this->_model;
    }
    
    public function getFormattedTitle(){
        $separator = $this->getParentSite()->getTitleFormatSeparator();
        if($this->_model instanceof SmartestModel){
            return $this->getParentSite()->getName().' '.$separator.' '.$this->_model->getPluralName().' tagged \''.$this->_tag->getLabel().'\'';
        }else{
            return $this->getParentSite()->getName().' '.$separator.' Tag '.$separator.' '.$this->_tag->getLabel();
        }
    }
    
    public function getDefaultUrl(){
        if($this->_model instanceof SmartestModel){
            return $this->_model->getVarName().'/tagged/'.$this->_tag->getName();
        }else{
            return 'tagged/'.$this->_tag->getName();
        }
    }
    
    public function fetchRenderingData(){
        
        $data = parent::fetchRenderingData();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), false);
        $this->_tag->setDraftMode($this->getDraftMode());
        $data->setParameter('tag', $this->_tag);
        if($this->_model instanceof SmartestModel){
            $data->setParameter('model', $this->_model);
        }
        return $data;
        
    }
    
    /* public function __toArray(){
        $data = parent::__toArray();
        // $data['tagged_objects'] = $this->_tag->getObjectsOnSiteAsArrays($this->getSite()->getId(), true);
        // $data['formatted_title'] = "";
        $data['title'] = $this->getTitle();
        $data['tag'] = $this->_tag->__toArray();
        return $data;
    } */
    
    public function offsetGet($offset){
        
        switch($offset){
            case "tag":
            return $this->_tag;
        }
        
        switch($offset){
            case "model":
            if($this->_model instanceof SmartestModel){
                return $this->_model;
            }else{
                break;
            }
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getCacheAsHtml(){
        return 'FALSE';
    }
    
}