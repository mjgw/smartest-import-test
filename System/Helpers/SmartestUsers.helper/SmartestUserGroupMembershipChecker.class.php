<?php

class SmartestUserGroupMembershipChecker implements ArrayAccess{
    
    protected $_user;
    protected $_site_id;
    protected $_group_ids_map = array();
    protected $_group_names_map = array();
    
    public function __construct(SmartestUser $user, $site_id=null){
        
        $this->_user = $user;
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->_user->getId());
	    $q->addSortField('Sets.set_name');
        
        if(is_numeric($site_id)){
            $this->_site_id = $site_id;
            $q->addForeignTableConstraint('Sets.set_site_id', $this->_site_id);
        }
    
        $groups = $q->retrieve(true);
        
        foreach($groups as $g){
            $this->_group_ids_map[$g->getId()] = true;
            $this->_group_names_map[$g->getName()] = true;
        }
        
    }
    
    public function belongsToGroup($group_identifier){
        
        if(is_numeric($group_identifier)){
            return isset($this->_group_ids_map[$group_identifier]);
        }
        
        if($group_identifier instanceof SmartestUserGroup){
            return isset($this->_group_ids_map[$group_identifier->getId()]);
        }
        
        return isset($this->_group_names_map[SmartestStringHelper::toVarName($group_identifier)]);
        
    }
    
    public function offsetGet($offset){
        
        if(is_numeric($offset)){
            return isset($this->_group_ids_map[$offset]);
        }
        
        return isset($this->_group_names_map[SmartestStringHelper::toVarName($offset)]);
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}

}