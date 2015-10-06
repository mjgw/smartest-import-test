<?php

class SmartestDropdownOption extends SmartestBaseDropdownOption implements SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_dropdown = null;
    protected $_value_object;
    protected $_data_type;
    
    public function __toString(){
        
        // return $this->_properties['value'];
        return $this->getValueObject()->__toString();
        
    }
    
    public function hydrateByValueWithDropdownId($value, $dropdown_id){
        
        $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_dropdown_id='".$dropdown_id."' AND dropdownvalue_value='".$value."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }
    }
    
    public function searchForMatchingValue($value){
        
        $sql = "SELECT * FROM DropDownValues WHERE dropdownvalue_value='".$value."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }else{
            SmartestLog::getInstance("system")->log("Dropdown option with value of '".$value."' is missing.");
            return false;
        }
    }
    
    public function getDropdown(){
        
        $dropdown = new SmartestDropdown;
        
        if(!$this->_dropdown){
        
            if($dropdown->find($this->getDropdownId())){
                $this->_dropdown = $dropdown;
            }
        
        }
        
        return $dropdown;
        
    }
    
    public function getDataType(){
        // A few hoops to make sure new dropdown menu datatypes (as of r744) are never blank. SM_DATATYPE_SL_TEXT is the default datatype.
        
        if(!$this->getDropdownId()){
            // throw new SmartestException('No dropdown ID');
        }
        
        if(strlen($this->_data_type)){
            return $this->_data_type;
        }elseif(is_object($this->_dropdown)){
            if($this->_dropdown->getDatatype()){
                $this->_data_type = $this->_dropdown->getDatatype();
            }else{
                $this->_dropdown->setDatatype('SM_DATATYPE_SL_TEXT');
                $this->_dropdown->save();
                $this->_data_type = 'SM_DATATYPE_SL_TEXT';
            }
            return $this->_data_type;
        }else{
            $sql = "SELECT * FROM DropDowns WHERE dropdown_id='".$this->getDropdownId()."' LIMIT 1";
            $result = $this->database->queryToArray($sql);
            if(count($result)){
                $this->_dropdown = new SmartestDropdown;
                $this->_dropdown->hydrate($result[0]);
                if(strlen($result[0]['dropdown_datatype'])){
                    $this->_data_type = $result[0]['dropdown_datatype'];
                    return $this->_data_type;
                }else{
                    $this->database->rawQuery('UPDATE DropDowns SET dropdown_datatype="SM_DATATYPE_SL_TEXT" WHERE dropdown_id="'.$this->getDropdownId().'" LIMIT 1');
                    $this->_data_type = 'SM_DATATYPE_SL_TEXT';
                    return $this->_data_type;
                }
            }else{
                // Apparently the dropdown does not exist. This should probably be logged.
                return 'SM_DATATYPE_SL_TEXT';
            }
        }
    }
    
    public function getStorableFormat(){
        // return serialize(array('dropdown_id'=>$this->getDropdownId(), 'value'=>$this->getValue()));
        // return $this->getValue();
        return $this->getDropdownId().':'.$this->getValue();
    }
    
    public function hydrateFromStorableFormat($v, $dropdown_id=null){
        
        if(is_numeric($dropdown_id)){
            return $this->hydrateByValueWithDropdownId($v, $dropdown_id);
        }else if(preg_match('/^(\d+):(.+)/', $v, $matches)){
            return $this->hydrateByValueWithDropdownId($matches[2], $matches[1]);
        }
        
        $d = @unserialize($v);
        
        if(is_array($d)){
            return $this->hydrateByValueWithDropdownId($d['value'], $d['dropdown_id']);
        }else{
            return $this->searchForMatchingValue($v);
        }
        
    }
    
    public function getValueObject(){
        // Todo: Once dropdown menus are types, different classes will need to be returned here depending on type
        // $class = SmartestDataUtility::getClassForDataType($this->getDatatype());
        // return new $class($this->_properties['value']);
        // echo $this->getDatatype();
        return SmartestDataUtility::objectize($this->_properties['value'], $this->getDatatype());
    }
    
    public function hydrateFromFormData($v, $dropdown_id=null){
        // var_dump($v);
        // var_dump(preg_match('/^(\d+):(.+)/', $v, $matches));
        // var_dump($matches);
        // exit;
        if(is_numeric($dropdown_id)){
            if(preg_match('/^(\d+):(.+)/', $v, $matches)){
                return $this->hydrateByValueWithDropdownId($matches[2], $dropdown_id);
            }else{
                return $this->hydrateByValueWithDropdownId($v, $dropdown_id);
            }
        }else{
            if(preg_match('/^(\d+):(.+)/', $v, $matches)){
                return $this->hydrateByValueWithDropdownId($matches[2], $matches[1]);
            }else{
                return $this->searchForMatchingValue($v);
            }
        }
    }
    
    public function renderInput($params){
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "html":
            return '<option value="'.$this->_properties['value'].'">'.$this->_properties['label'].'</option>';
            
            case "html_selected":
            return '<option value="'.$this->_properties['value'].'" selected="selected">'.$this->_properties['label'].'</option>';
            
            case "value":
            // var_dump($this->_properties['value']);
            return $this->getValueObject();
            
            case "label":
            return new SmartestString($this->_properties['label']);
            
            case "option_value":
            return $this->getStorableFormat();
            
            case "dropdown":
            return $this->getDropdown();
            
            case "empty":
            return !is_object($this->getValueObject()) || !$this->getValueObject()->isPresent();
            
        }
        
        // var_dump($value = $this->getValueObject());
        
        if(is_object($this->getValueObject()) && $this->getValueObject()->offsetExists($offset)){
            // echo "return offset";
            return $this->getValueObject()->offsetGet($offset);
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getPreviousOption(){
        $sql = "SELECT * FROM DropDownValues WHERE DropDownValues.dropdownvalue_dropdown_id = '".$this->getDropdownId()."' AND DropDownValues.dropdownvalue_order <= '".$this->getOrder()."' AND DropDownValues.dropdownvalue_id != '".$this->getId()."' ORDER BY dropdownvalue_order DESC LIMIT 1";
        $results = $this->database->queryToArray($sql);
        
        if(count($results)){
            $ddv = new SmartestDropdownOption;
            $ddv->hydrate($results[0]);
            return $ddv;
        }
    }
    
    public function getNextOption(){
        $sql = "SELECT * FROM DropDownValues WHERE DropDownValues.dropdownvalue_dropdown_id = '".$this->getDropdownId()."' AND DropDownValues.dropdownvalue_order >= '".$this->getOrder()."' AND DropDownValues.dropdownvalue_id != '".$this->getId()."' ORDER BY dropdownvalue_order ASC LIMIT 1";
        $results = $this->database->queryToArray($sql);
        
        if(count($results)){
            $ddv = new SmartestDropdownOption;
            $ddv->hydrate($results[0]);
            return $ddv;
        }
    }
    
    public function getDropdownLastOrderIndex(){
        
        $sql = "SELECT dropdownvalue_order FROM DropDownValues WHERE dropdownvalue_dropdown_id = '".$this->getDropdownId()."' ORDER BY dropdownvalue_order DESC LIMIT 1";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            return $result[0]['dropdownvalue_order'];
        }else{
            return 0;
        }
        
    }
    
    public function moveUp(){
        if((int) $this->getOrder() > 0){
            if($previous_option = $this->getPreviousOption()){
                $new_order = ((int) $this->getOrder() - 1);
                $previous_option->setOrder((int) $this->getOrder());
                $this->setOrder($new_order);
                $previous_option->save();
                $this->save();
            }else{
                $this->setOrder(0);
                $this->save();
            }
        }else{
            if($previous_option = $this->getPreviousOption()){
                // option may be second from top but have an order index of 0 if another option also has an OI of 0 but comes first in the alphabet
                // in this case, we take that option and give it an OI of 1, which will force this one above it.
                // Not ideal, but if SmartestDropdown->fixOrderIndices() is called first, this should rarely if ever be executd.
                $previous_option->setOrder((int) $this->getOrder() + 1);
                $previous_option->save();
            }
        }
    }
    
    public function moveDown(){
        
        $last_order_index = $this->getDropdownLastOrderIndex();
        
        if((int) $this->getOrder() < $last_order_index){
            if($next_option = $this->getNextOption()){
                $new_order = ((int) $this->getOrder() + 1);
                $next_option->setOrder((int) $this->getOrder());
                $this->setOrder($new_order);
                $next_option->save();
                $this->save();
            }else{
                $this->setOrder(0);
                $this->save();
            }
        }else{
            if($next_option = $this->getNextOption()){
                // option may be second from bottom but have an order index the same as the last option if another option also has the same OI but comes later in the alphabet
                // in this case, we take that option and give it an OI of one less than this option, which will force this one below it.
                // Not ideal, but if SmartestDropdown->fixOrderIndices() is called first, this should rarely if ever be executd.
                $next_option->setOrder((int) $this->getOrder() - 1);
                $next_option->save();
            }
        }
        
    }
    
}