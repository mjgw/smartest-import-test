<?php

class SmartestUserGroup extends SmartestSet{

    public function __objectConstruct(){
        $this->_membership_type = 'SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP';
        parent::__objectConstruct();
    }
    
    public function getMembers($draft_mode /* $sort='' */){
        
        /* if(!$sort){
            $sort = SM_MTM_SORT_GROUP_ORDER;
        } */
        
        if($refresh || !count($this->_members)){
        
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
            $q->addSortField('Users.user_lastname');
	    
            $this->_members = $q->retrieve(true);
            
            if($draft_mode){
                
                foreach($this->_members as $user){
                    $user->setDraftMode($draft_mode);
                }
                
            }
        
        }
        
        return $this->_members;
        
    }
    
    public function getMemberIds(){
        $q = new SmartestManyToManyQuery($this->_membership_type);
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->addSortField('Users.user_lastname');
        return $q->retrieveIds();
    }
    
    public function addUserById($user_id, $strict_checking=false){
        // TODO: Do this more gracefully - a new class is probably needed
        if(!$strict_checking || ($strict_checking && in_array($this->getMemberIds($user_id)))){
            $sql = "INSERT INTO ManyToManyLookups (`mtmlookup_type`, `mtmlookup_entity_1_foreignkey`, `mtmlookup_entity_2_foreignkey`) VALUES ('SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP', '".$user_id."', '".$this->getId()."')";
            $this->database->rawQuery($sql);
        }
        
    }
    
    public function removeUserById($user_id){
        // TODO: Do this more gracefully - a new class is probably needed
        $sql = "DELETE FROM ManyToManyLookups WHERE mtmlookup_type='SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP' AND mtmlookup_entity_1_foreignkey='".$user_id."' AND mtmlookup_entity_2_foreignkey='".$this->getId()."'";
        $this->database->rawQuery($sql);
    }
    
    public function getAcceptedUserType(){
        return $this->getSettingValue('set_accepted_user_type');
    }
    
    public function setAcceptedUserType($type){
        $this->setSettingValue('set_accepted_user_type', $type);
    }
    
    public function setName($starting_name){
        
        $name = SmartestStringHelper::toVarName($starting_name);
        
	    if($this->_properties['id']){
	       // $sql = "SELECT item_slug FROM Items WHERE (item_site_id='".$this->getSiteId()."' OR item_shared='1') AND item_id != '".$this->getId()."' AND item_itemclass_id='".$this->_properties['itemclass_id']."'"; 
	       $sql = "SELECT set_name FROM Sets WHERE set_site_id='".$this->getSiteId()."' AND set_id != '".$this->getId()."' AND set_type='SM_SET_USERGROUP'"; 
	    }else{
	        if($this->getSiteId()){
	            // $sql = "SELECT item_slug FROM Items WHERE (item_site_id='".$site_id."' OR item_shared='1')"; 
	            $sql = "SELECT set_name FROM Sets WHERE set_site_id='".$this->getSiteId()."'"; 
	            if($this->_properties['itemclass_id']){
	                $sql .= " AND set_type='SM_SET_USERGROUP'";
	            }
	        }else{
	            $sql = "SELECT set_name FROM Sets";
	            if($this->_properties['itemclass_id']){
	                $sql .= " WHERE set_type='SM_SET_USERGROUP'";
	            }
	        }
	    }
    
	    $fields = $this->database->queryFieldsToArrays(array('set_name'), $sql);
        $name = SmartestStringHelper::guaranteeUnique($name, $fields['set_name'], '_');
        $this->setField('name', $name);
        
    }
    
    public function save(){
        
        // if(!$this->getType()){
            $this->setType('SM_SET_USERGROUP');
        // }
        
        if(!strlen($this->_properties['name'])){
            $this->setName($this->_properties['label']);
        }
        
        return parent::save();
    }
    
    public function getQuickCount(){
        if(count($this->_members)){
            return count($this->_members);
        }else{
            
            $q = new SmartestManyToManyQuery($this->_membership_type);
            $q->setTargetEntityByIndex(1);
            $q->addQualifyingEntityByIndex(2, $this->getId());
    	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	        
            return $q->quickCount(true);
        }
    }
    
    public function getNonMembers(){
        
        if($this->getAcceptedUserType() == 'SM_USERTYPE_SYSTEM_USER'){
            $sql = "SELECT DISTINCT Users.* FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=21 AND (UsersTokensLookup.utlookup_site_id='".$this->getSiteId()."' OR UsersTokensLookup.utlookup_is_global='1')";
        }else if($this->getAcceptedUserType() == 'SM_USERTYPE_ORDINARY_USER'){
            $sql = "SELECT DISTINCT Users.* FROM `Users` WHERE Users.user_type='SM_USERTYPE_ORDINARY_USER'";
        }else{
            $sql = "SELECT DISTINCT Users.* FROM `Users`, `UsersTokensLookup` WHERE ((UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=21 AND (UsersTokensLookup.utlookup_site_id='".$this->getSiteId()."' OR UsersTokensLookup.utlookup_is_global='1')) OR Users.user_type='SM_USERTYPE_ORDINARY_USER')";
        }
        
        $sql .= " AND user_id NOT IN ('".implode("','", $this->getMemberIds())."') ";
        
        $sql .= 'ORDER BY user_lastname';
        
        $result = $this->database->queryToArray($sql);
        $users = array();
        $class = $this->getAcceptedUserType() == 'SM_USERTYPE_SYSTEM_USER' ? 'SmartestSystemUser' : 'SmartestUser';
        
        foreach($result as $r){
            $u = new $class;
            $u->hydrate($r);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case '_count':
            return $this->getQuickCount();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
}