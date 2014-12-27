<?php

class SmartestUserProfilePicsGroup extends SmartestAssetGroup{
    
    public function getMembersForUser($user_id, $site_id=null){
        
        $this->fixMemberships();
        
        $result = $this->_getMemberships($user_id, $site_id);
        $assets = array();
        
        foreach($result as $m){
            $assets[] = $m->getAsset();
        }
        
        return $assets;
        
    }
    
    public function _getMemberships($user_id=null, $site_id=null){
        
        if(!is_numeric($site_id)){
            $site_id = $this->getCurrentSiteId();
        }
        
        $q = new SmartestManyToManyQuery($this->getMembershipType());
        $q->setTargetEntityByIndex(1);
        $q->addQualifyingEntityByIndex(2, $this->getId());
	    $q->addForeignTableConstraint('Assets.asset_deleted', 0);
        
        if(is_numeric($user_id)){
            $q->addForeignTableConstraint('Assets.asset_user_id', $user_id);
        }
	    
	    $q->addForeignTableConstraint('Assets.asset_is_hidden', 1);
	    
	    if(is_numeric($site_id)){
	        $q->addForeignTableOrConstraints(
	            array('field'=>'Assets.asset_site_id', 'value'=>$site_id),
	            array('field'=>'Assets.asset_shared', 'value'=>'1')
	        );
	    }
	    
	    $q->addSortField('Assets.asset_id');
	    
	    if($approved_only){
	        $q->addForeignTableConstraint('Asset.asset_is_approved', 'TRUE');
	    }
	    
	    $result = $q->retrieve(true, null, true);
        
        return $result;
        
    }
    
    public function getMemberIds($site_id='', $refresh=false){
        
        if($refresh || !count($this->_member_ids)){
        
            $ids = array();
        
            foreach($this->_getMemberships(null, $site_id) as $m){
                $ids[] = $m->getAssetId();
            }
            
            $this->_member_ids = $ids;
        
        }
        
        return $this->_member_ids;
        
    }
    
    public function hasAssetId($asset_id){
        return in_array($asset_id, $this->getMemberIds());
    }
    
    public function fixMemberships(){
        
        $user_profile_pic_ids = array();
        $sql = "SELECT * FROM Users WHERE user_type='SM_USERTYPE_SYSTEM_USER'";
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $r){
            if($r['user_profile_pic_asset_id']){
                $user_profile_pic_ids[] = $r['user_profile_pic_asset_id'];
                $this->database->rawQuery('UPDATE Assets SET asset_user_id="'.$r['user_id'].'" WHERE asset_id="'.$r['user_profile_pic_asset_id'].'" LIMIT 1');
            }
        }
        
        if(count($user_profile_pic_ids)){
            $this->database->rawQuery("UPDATE Assets SET asset_deleted=0 WHERE asset_id IN ('".implode("','", $user_profile_pic_ids)."')");
            foreach($user_profile_pic_ids as $id){
                $this->addAssetById($id, true);
            }
        }
        
    }
    
}