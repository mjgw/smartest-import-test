<?php

class DropdownsAjax extends SmartestSystemApplication{
    
    public function updateValuesOrder(){
        
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
            $ids = explode(',', $this->getRequestParameter('value_ids'));
            
            $options = $dropdown->getOptions();
            $formatted_options = array();
            
            foreach($options as $value){
                $formatted_options[$value->getId()] = $value;
            }
            
            foreach($ids as $position=>$option_id){
                $formatted_options[$option_id]->setOrder($position);
                $formatted_options[$option_id]->save();
            }
            
	    }
        
    }
    
}