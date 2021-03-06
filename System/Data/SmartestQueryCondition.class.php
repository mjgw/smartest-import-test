<?php

class SmartestQueryCondition{
    
    protected $_value;
    protected $_property;
    protected $_operator = 0;
    protected $_ids_array = array();
    
    public function __construct($value, $property, $operator){
        
        if($value instanceof SmartestStorableValue){
            $this->_value = $value;
        }elseif(is_array($value)){
            if($operator == SmartestQuery::IN){
                $this->_value = $value;
            }else{
                throw new SmartestException("SmartestQueryCondition can only accept array values when the operator is the 'IN' (SmartestQuery::IN) operator");
            }
        }else{
            throw new SmartestException("SmartestQueryCondition can only accept values that implement SmartestStorableValue");
        }
        
        if($property instanceof SmartestItemProperty || $property instanceof SmartestPseudoItemProperty){
            $this->_property = $property;
        }
        
        $this->_operator = (int) $operator;
        
    }
    
    public function getOperator(){
        return $this->_operator;
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function getValueAsString(){
        return $this->_value->__toString();
    }
    
    public function getProperty(){
        return $this->_property;
    }
    
    public function getIdsArray(){
        return $this->_ids_array;
    }
    
    public function setIdsArray($array){
        $this->_ids_array = $array;
    }
    
    public function getSql(){
        
        switch($this->_operator){

		    case 0:
			return "='".$this->_value->getStorableFormat()."'";

			case 1:
			return " != '".$this->_value->getStorableFormat()."'";

			case 2:
			return " LIKE '%".$this->_value->getStorableFormat()."%'";

			case 3:
			return " NOT LIKE '%".$this->_value->getStorableFormat()."%'";

			case 4:
			return " LIKE '".$this->_value->getStorableFormat()."%'";

			case 5:
			return " LIKE '%".$this->_value->getStorableFormat()."'";
		
			case 6:
			// for dates that are always now
			if($this->_value->getStorableFormat() == '%NOW%'){
			    return " > '".time()."'";
		    }else{
		        return " > '".$this->_value->getStorableFormat()."'";
		    }
		
			case 7:
			// for dates that are always now
			if($this->_value->getStorableFormat() == '%NOW%'){
			    return " < '".time()."'";
		    }else{
		        return " < '".$this->_value->getStorableFormat()."'";
		    }
            
          //  case 20:
          //  // Today or In the past - ie less than midnight tomorrow - Only used for chronological values
          //  return " < '".time()."'";
          //  
          //  case 21:
          //  // Today or in the future - ie greater than midnight today - Only used for chronological values
          //  // figure out midnight today
          //  return " >= '".time()."'";
            
            case 22:
            // In the past - ie less than time() Only used for chronological values
            return " < '".time()."'";
            
            case 23:
            // Right now or in the future - ie great than or equal to time() Only used for chronological values
            return " >= '".time()."'";
            
            case SmartestQuery::IN:
            return " IN ('".implode("','", $this->sqlIzeArray($this->_value))."') ";
			
        }
        
    }
    
    public function sqlIzeArrayValue($value){
        
        if(is_object($value) && $value instanceof SmartestStorableValue){
            return $value->getStorableFormat();
        }else{
            return $value;
        }
        
    }
    
    public function sqlIzeArray($value){
        
        $sqlized_values = array();
        
        foreach($value as $v){
            $sqlized_values[] = $this->sqlIzeArrayValue($v);
        }
        
        return $sqlized_values;
        
    }
    
}