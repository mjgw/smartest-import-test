<?php

class SmartestDynamicDataSetCondition extends SmartestBaseDynamicDataSetCondition{
    
    protected $_c_property;
    
    /* protected function __objectConstruct(){
		
		// $this->addPropertyAlias('ModelId', 'itemclass_id');
		// $this->_table_prefix = 'setrule_';
		// $this->_table_name = 'SetRules';
		
	} */
	
	public function getSet(){
	    
	    $s = new SmartestCmsItemSet;
	    if($s->find($this->getSetId())){
	        return $s;
	    }
	    
	}
    
    public function getProperty(){
        
        if(is_numeric($this->_properties['itemproperty_id']) && !is_object($this->_c_property)){
            
            $p = new SmartestItemProperty;
            if($p->find($this->_properties['itemproperty_id'])){
                $this->_c_property = $p;
            }
        }
        
        return $this->_c_property;
        
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "property":
            return $this->getProperty();
            
            case "type_info":
            if($this->getProperty()){
                return $this->getProperty()->getTypeInfo();
            }else{
                return array();
            }
            
            case "type":
            case "datatype":
            if($this->getProperty()){
                return $this->getProperty()->getDataType();
            }else{
                return 'Unknown';
            }
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}