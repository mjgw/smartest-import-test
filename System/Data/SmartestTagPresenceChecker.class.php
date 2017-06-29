<?php
    
class SmartestTagPresenceChecker extends SmartestObject{
    
    protected $_tags;
    protected $_tag_var_names;
    
    public function __construct($tags){
        
        $this->_tags = array();
        
        foreach($tags as $t){
            if($t instanceof SmartestTag){
                $this->_tags[$t->getName()] = true;
                $this->_tag_var_names[SmartestStringHelper::toVarName($t->getLabel())] = true;
            }
        }
        
    }
    
    public function offsetGet($offset){
        
        if(isset($this->_tags[$offset]) && $this->_tags[$offset] == true){
            return true;
        }elseif(isset($this->_tag_var_names[$offset]) && $this->_tag_var_names[$offset] == true){
            return true;
        }else{
            return false;
        }
    }
        
}