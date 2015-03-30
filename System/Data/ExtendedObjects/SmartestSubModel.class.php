<?php

class SmartestSubModel extends SmartestModel{
    
    protected $_parent_model;
    
    public function getParentModel(){
        
        if(!is_object($this->_parent_model)){
            
            $parent_id = $this->getParentId();
            $m = new SmartestModel;
            
            if($m->find($parent_id)){
                $this->_parent_model = $m;
            }else{
                throw new SmartestException('Could not find parent model for sub model \''.$this->getPluralName().'\'');
            }
            
        }
        
        return $this->_parent_model;
        
    }
    
}