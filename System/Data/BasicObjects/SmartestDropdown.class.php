<?php

class SmartestDropdown extends SmartestBaseDropdown{
    
    protected $_options = array();
    
    public function getOptions($refresh=false){
        
        if(!count($this->_options) || $refresh){
        
            $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order, dropdownvalue_label ASC";
            $result = $this->database->queryToArray($sql, true);
        
            $options = array();
        
            foreach($result as $opt){
                $option = new SmartestDropdownOption;
                $option->hydrate($opt);
                $options[] = $option;
            }
            
            $this->_options = $options;
        
        }
        
        return $this->_options;
        
    }
    
    public function getOptionsForIdentifier($refresh=false){
        
        // if(!count($this->_options) || $refresh){
        
            $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_value ASC";
            $result = $this->database->queryToArray($sql, true);
        
            $options = array();
        
            foreach($result as $opt){
                $option = new SmartestDropdownOption;
                $option->hydrate($opt);
                $options[] = $option;
            }
        
        return $options;
        
    }
    
    public function getIdentifier(){
        $val = '';
        // Will return the same identifier from the same values, regardless of how they are ordered by the user
        foreach($this->getOptionsForIdentifier() as $opt){
            $val .= $opt->getValue();
        }
        $hex = md5($val);
	    $hex{12} = 4;
        $digits = array('a','b',8,9);
	    $hex{16} = $digits[rand(0,3)];
        return substr($hex, 0, 8).'-'.substr($hex, 8, 4).'-'.substr($hex, 12, 4).'-'.substr($hex, 16, 4).'-'.substr($hex, 20, 12);
    }
    
    public function __postHydrationAction(){
        // Correcting earlier versions where the 'datatype' field was not used
        if($this->_came_from_database){
            if(!strlen($this->getDatatype())){
                $this->setDatatype('SM_DATATYPE_SL_TEXT');
                $this->save();
            }
        }
    }
    
    public function getOptionsAsArrays(){
        
        $options = $this->getOptions();
        $arrays = array();
        
        foreach($options as $opt){
            $arrays[] = $opt->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getOptionSlugs(){
        
        $slugs = array();
        
        foreach($this->getOptions() as $option){
            $slugs[] = $option->getvalue();
        }
        
        return $slugs;
        
    }
    
    public function getOptionsForRender(){
        
        $options = $this->getOptions();
        $arrays = array();
        $i = 0;
        
        foreach($options as $opt){
            $arrays[$i]['value'] = $opt-getValue();
            $arrays[$i]['label'] = $opt-getLabel();
            $i++;
        }
        
        return $arrays;
        
    }
    
    public function getNextOptionOrderIndex(){
        
        $index = 0;
        
        $sql = "SELECT DISTINCT dropdownvalue_order FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$this->getId()."' ORDER BY dropdownvalue_order DESC LIMIT 1";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $index = $result[0]['dropdownvalue_order']+1;
        }
        
        return $index;
        
    }
    
    public function getDatatype(){
        
        if(array_key_exists('datatype', $this->_properties)){
            return $this->_properties['datatype'];
        }else{
            return 'SM_DATATYPE_SL_TEXT';
        }
        
    }
    
    public function fixOrderIndices(){
        
        $new_index = 0;
        
        foreach($this->getOptions() as $opt){
            $opt->setOrder($new_index);
            $opt->save();
            ++$new_index;
        }
        
    }
    
    public function getFieldsWhereUsed(){
        
    }
    
    public function getItemPropertiesWhereUsed(){
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "values":
            case "options":
            return $this->getOptions();
            
            case "num_values":
            case "num_options":
            return count($this->getOptions());
            
            case "render_options":
            return $this->getOptionsForRender();
            
            case "identifier":
            return $this->getIdentifier();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function addOption($label, $slug=null){
        
        $slugs = $this->getSlugs();
        
        if(!$slug){
            $slug = SmartestStringHelper::toSlug($label);
        }
        
        if(in_array($slug, $slugs)){
            return false;
        }else{
            $option = new SmartestDropdownOption;
            $option->setLabel($label);
            $option->setValue($slug);
            $option->setDropdownId($this->getId());
            $option->setOrderIndex($this->getNextOptionOrderIndex());
            $option->save();
            return $option();
        }
        
    }
    
}

