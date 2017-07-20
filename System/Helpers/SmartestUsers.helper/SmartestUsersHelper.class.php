<?php

SmartestHelper::register('Users');

class SmartestUsersHelper extends SmartestHelper{
    
    protected $database;
    
    public function __construct(){
		$this->database = SmartestPersistentObject::get('db:main');
    }
    
    public static function getTokenData(){
        
        $tokens = SmartestPffrHelper::getContentsFast(SM_ROOT_DIR.'System/Core/Types/usertokens.pff');
        return $tokens;
        
    }
    
    public function getTokens(){
        
        $data = self::getTokenData();
        $tokens = array();
        
        foreach($data as $rt){
            $t = new SmartestUserToken_New($rt);
            $tokens[$rt['id']] = $t;
        }
        
        return $tokens;
        
    }
    
    public function getUsers(){
        
        $raw_users = $this->database->queryToArray("SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users WHERE username != 'smartest' AND user_type!='SM_USERTYPE_OAUTH_CLIENT_INTERNAL' ORDER BY user_fullname");
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestUser;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getSystemUsers(){
        
        $raw_users = $this->database->queryToArray("SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_SYSTEM_USER' ORDER BY user_fullname");
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestSystemUser;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getOrdinaryUsers(){
        
        $raw_users = $this->database->queryToArray("SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users WHERE username != 'smartest' AND user_type='SM_USERTYPE_ORDINARY_USER' ORDER BY user_fullname");
        $users = array();
        
        foreach($raw_users as $ru){
            $u = new SmartestUser;
            $u->hydrate($ru);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getTokenId($token){
        
        $all_tokens = self::getTokenData();
        
        foreach($all_tokens as $t){
            if($t['code'] == $token){
                return $t['id'];
            }
        }
        
        return null;
        
    }
    
    public function getUsersThatHaveToken($token, $site_id=''){
        
	    if(is_array($token)){
	        $sql = "SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id IN ('".implode("', '", $token)."')";
        }else{                                                                                                                   
	        $sql = 'SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id='.$token."'";
	    }
	    
        if(is_numeric($site_id)){
            $sql .= " AND UsersTokensLookup.utlookup_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY user_fullname";
        
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function getUsersThatHaveTokenAsArrays($token, $site_id){
    
        $users = $this->getUsersThatHaveToken($token, $site_id='');
        $arrays = array();
        
        foreach($users as $u){
            
            $arrays[] = $u->__toArray();
            
        }
        
        return $arrays;
    
    }
    
    public function applyTokensToUser(SmartestUser $user, $tokens, $site_id, $delete_existing_tokens=false){
        $this->applyTokensToUserId($user->getId(), $tokens);
    }
    
    public function applyTokensToUserId($user_id, $tokens, $site_id, $delete_existing_tokens=false){
        
        if($delete_existing_tokens){
            $this->deleteUserTokensFromUserId($user_id, $site_id);
        }
        
        if(is_numeric($user_id)){
            foreach($tokens as $t){
                $this->applySingleTokenToUserById($user_id, $t->getId(), $site_id, !$delete_existing_tokens);
            }
        }
    }
    
    public function applySingleTokenToUserById($user_id, $token_id, $site_id, $avoid_duplicates=false){
	    
	    $utl = new SmartestUserTokenLookup;
		$utl->setUserId($user_id);
	    $utl->setTokenId($token_id);
	    $utl->setGrantedTimestamp(time());
	    
	    if(is_object(SmartestSession::get('user'))){
	        $utl->setGrantedByUserId(SmartestSession::get('user')->getId());
	    }
	    
	    if($site_id == "GLOBAL"){
	        $utl->setIsGlobal(1);
	        
	        // Remove any non-global assignments of the same token
	        if($avoid_duplicates){
	            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_is_global != '1' AND utlookup_user_id='".$user_id."'";
            }
            
	        $this->database->rawQuery($sql);
	        
	    }else if(is_numeric($site_id)){
	        
	        if($avoid_duplicates){
	            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_token_id='".$token_id."' AND utlookup_is_global != '1' AND utlookup_site_id='".$site_id."' AND utlookup_user_id='".$user_id."'";
	            $this->database->rawQuery($sql);
            }
            
	        $utl->setSiteId($site_id);
	    }
	    
	    return $utl->save();
	    
	}
    
    public function deleteUserTokensFromUserId($user_id, $site_id, $all=false){
        
        $user_id = (int) $user_id;
        
        if($all){
            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id='".$user_id."'";
        }else{
            if($site_id == "GLOBAL"){
                $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_is_global == '1' AND utlookup_user_id='".$user_id."'";
            }else{
                $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_is_global != '1' AND utlookup_user_id='".$user_id."' AND utlookup_site_id='".$site_id."'";
            }
        }
        
        $this->database->rawQuery($sql);
    }
    
    public function applyRoleToUserById(SmartestRole $role){
        
    }
    
	public function getUsersOnSite($site_id){
        
        $site_id = (int) $site_id;
        $sql = "SELECT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND UsersTokensLookup.utlookup_token_id=21 AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1') ORDER BY user_fullname";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
            
        }
        
        return $users;
        
    }
    
    public function getUserIdsOnSite($site_id){
        
        $users = $this->getUsersOnSite($site_id);
        $user_ids = array();
        
        foreach($users as $u){
            $user_ids[] = $u->getId();
        }
        
        return $user_ids;
        
    }
    
    public function getUsersOnSiteAsArrays($site_id){
        
        $users = $this->getUsersOnSite($site_id);
        $arrays = array();
        
        foreach($users as $u){
            $arrays[] = $u->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getCreditableUsersOnSite($site_id){
        
        $site_id = (int) $site_id;
        $sql = "SELECT DISTINCT Users.*, CONCAT(Users.user_lastname,Users.user_firstname) AS user_fullname FROM `Users`, `UsersTokensLookup` WHERE UsersTokensLookup.utlookup_user_id=Users.user_id AND (UsersTokensLookup.utlookup_token_id=1 OR UsersTokensLookup.utlookup_token_id=0) AND (UsersTokensLookup.utlookup_site_id='".$site_id."' OR UsersTokensLookup.utlookup_is_global='1') ORDER BY user_fullname";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $record){
            
            $u = new SmartestUser;
            $u->hydrate($record);
            $users[] = $u;
            
        }
        
        return $users;
        
    }
    
    public function getRoles($include_system_roles=true){
        
        $result = $this->database->queryToArray("SELECT * FROM Roles");
	    
	    if($include_system_roles){
	        $roles = $this->getSystemRoles();
	    }else{
	        $roles = array();
	    }
	    
	    foreach($result as $role_array){
	        $role = new SmartestRole;
	        $role->hydrate($role_array);
	        $roles[] = $role;
	    }
	    
	    return $roles;
        
    }
    
    public function getSystemRoles(){
        
        $data = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/roles.yml');
        $raw_roles = $data['roles'];
        $roles = array();
        
        foreach($raw_roles as $k=>$rr){
            $r = new SmartestNonDbRole;
            $r->hydrate($rr);
            $r->setId($k);
            $roles[$k] = $r;
        }
        
        return $roles;
        
    }
    
    public function distributeAuthorCreditTokenFromPage(SmartestPage $page){
        $author_ids = $page->getAuthorIds();
        $this->addTokenToMultipleUsersByUserIdsArray('author_credit', $author_ids, $page->getSiteId());
    }
    
    public function distributeAuthorCreditTokenFromItem(SmartestItem $item, $site_id=null){
        $author_ids = $item->getAuthorIds();
        $this->addTokenToMultipleUsersByUserIdsArray('author_credit', $author_ids, $site_id);
    }
    
    public function addTokenToMultipleUsersByUserIdsArray($token_code, $ids, $site_id=null){
        
        $token_id = $this->getTokenId($token_code);
        
        foreach($this->getUsersArrayFromIdsArray($ids, true) as $user){
            $user->addTokenById($token_id, $site_id, true);
        }
        
    }
    
    public function getUsersArrayFromIdsArray($ids, $create_system_users=false){
        
        $users = array();
        
        if(count($ids)){
            
            $sql = "SELECT * FROM Users WHERE user_id IN (".implode(', ', $ids).")";
            $result = $this->database->queryToArray($sql);
            
            foreach($result as $u){
                
                if($create_system_users){
                    $user = new SmartestSystemUser;
                }else{
                    $user = new SmartestUser;
                }
                
                $user->hydrate($u);
                $users[] = $user;
            }
        }
        
        return $users;
        
    }
    
    /* public function getRolesAsArrays(){
        
        $roles = $this->getRoles();
        $arrays = array();
        
        foreach($roles as $r){
            $arrays[] = $r->__toArray();
        }
        
        return $arrays;
        
    } */
    
    // older code, prior to SmartestApplication->getUser()->hasToken()
    /* public function getUserHasToken($token, $db=false){
    	if($db==true){
			
			$sql = "SELECT * FROM UsersTokensLookup, UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id AND UserTokens.token_code=$token";
			$count = $this->database->howMany($sql);
		
			if($count>0){
				$has_token = 0;
			}else{
				$has_token = 1;
			}
			
		}else{
			$has_token=in_array($token,$_SESSION["user"]["tokens"]);
		}
		
		return $has_token;
    } */

    /* public function getUserTokens(){  
    		
    	/* if($db==true){
			
			$sql = "SELECT UserTokens.token_code FROM UsersTokensLookup,UserTokens WHERE UsersTokensLookup.utlookup_user_id='".$_SESSION["user"]["user_id"]."' AND UsersTokensLookup.utlookup_token_id=UserTokens.token_id";
			$result = $this->database->queryToArray($sql);
			
			foreach($result as $key=>$token){
				$tokens[$key]=$token['token_code'];
			}
			
		}else{
			// $tokens = $_SESSION["user"]["tokens"];
		}
		
		return $tokens;
    } */
    
    public function getUserProfilePicsGroupId(){
        
        $ph = new SmartestPreferencesHelper;
        
        // does the setting exist?
        if($ph->getGlobalPreference('default_user_profile_pic_asset_group_id', null, null, true)){

            // if so, what is it's value?
            return $ph->getGlobalPreference('default_user_profile_pic_asset_group_id', null, null);

        }else{

            // if not, create the asset and set the value of the preference to the id of the new asset
            $g = new SmartestAssetGroup;
            $g->setIsSystem(1);
            $g->setName('user_profile_pictures');
            $g->setLabel('User Profile Pictures');
            $g->setType('SM_SET_ASSETGROUP');
            $g->setSiteId(1);
            $g->setShared(1);
            $g->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
            $g->setFilterValue('SM_ASSETCLASS_STATIC_IMAGE');
            $g->save();

            $p = $g->getId();

            $ph->setGlobalPreference('default_user_profile_pic_asset_group_id', $p, null, null);
            return $p;

        }
        
    }
    
    public function getUserProfilePicsGroup(){
        
        $g = new SmartestUserProfilePicsGroup;
        
        if($g->find($this->getUserProfilePicsGroupId())){
            return $g;
        }
        
    }
    
    public function usernameExists($username, $except_user_id=null){
        
        $sql = "SELECT * FROM Users WHERE username='".SmartestStringHelper::sanitize($username)."'";
        
        if(is_numeric($except_user_id)){
            $sql .= " AND user_id != ".$except_user_id;
        }
        
        $result = $this->database->queryToArray($sql);
        
        return (bool) count($result);
        
    }
    
    public function emailExists($email, $except_user_id=null){
        
        if(SmartestStringHelper::isEmailAddress($email)){
        
            $sql = "SELECT * FROM Users WHERE user_email='".$email."'";
        
            if(is_numeric($except_user_id)){
                $sql .= " AND user_id != ".$except_user_id;
            }
        
            $result = $this->database->queryToArray($sql);
        
            return (bool) count($result);
        
        }
        
    }
    
    public function twitterHandleExists($twitter_handle, $except_user_id=null){
        
        $sql = "SELECT * FROM Users WHERE LOWER(user_twitter_handle)='".SmartestStringHelper::toVarName($twitter_handle)."'";
        
        if(is_numeric($except_user_id)){
            $sql .= " AND user_id != ".$except_user_id;
        }
        
        $result = $this->database->queryToArray($sql);
        
        return (bool) count($result);
        
    }
    
    public function roleNameExists($role){
        
        $role_names = array();
        
        foreach($this->getRoles() as $r){
            $role_names[] = $r->getLabel();
        }
        
        return in_array($role, $role_names);
        
    }
    
    /////////////////////////// GROUPS STUFF ///////////////////////////////
    
    public function getUserGroups($site_id=null, $filter=null){ // TODO: $filter needs implementing
        
        $sql = "SELECT * FROM Sets WHERE Sets.set_type='SM_SET_USERGROUP'";
        
        if(is_numeric($site_id)){
            $sql .= " AND Sets.set_site_id='".$site_id."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $groups = array();
        
        foreach($result as $r){
            $g = new SmartestUserGroup;
            $g->hydrate($r);
            $groups[] = $g;
        }
        
        return $groups;
        
    }
    
    //////////////////// MEDIA OWNERSHIP AND ASSOCIATION ///////////////////
    
    public function getPagesCreatedByUserId($user_id, $site_id=null){
        $sql = "SELECT * FROM Pages WHERE page_createdby_userid='".$user_id."' AND page_deleted='FALSE'";
        if(is_numeric($user_id)){
            $sql .= ' AND page_site_id="'.$site_id.'"';
        }
        $result = $this->database->queryToArray($sql);
        $pages = array();
        foreach($result as $r){
            $p = new SmartestPage;
            $p->hydrate($r);
            $pages[] = $p;
        }
        return $pages;
    }
    
    public function getPagesHeldByUserId($user_id, $site_id=null){
        $sql = "SELECT * FROM Pages WHERE page_is_held='1' AND page_held_by='".$user_id."' AND page_deleted='FALSE'";
        if(is_numeric($user_id)){
            $sql .= ' AND page_site_id="'.$site_id.'"';
        }
        $result = $this->database->queryToArray($sql);
        $pages = array();
        foreach($result as $r){
            $p = new SmartestPage;
            $p->hydrate($r);
            $pages[] = $p;
        }
        return $pages;
    }
    
    public function getItemsHeldByUserId($user_id, $site_id=null){
        $sql = "SELECT * FROM Items WHERE item_is_held='1' AND 	item_held_by='".$user_id."' AND item_deleted='0'";
        if(is_numeric($user_id)){
            $sql .= ' AND item_site_id="'.$site_id.'"';
        }
        $sql .= "ORDER BY Items.item_name ASC";
        $result = $this->database->queryToArray($sql);
        $ids = array();
        foreach($result as $r){
            $ids[] = $r['item_id'];
        }
        $h = new SmartestCmsItemsHelper;
        return $h->hydrateMixedListFromIdsArrayPreservingOrder($ids);
    }
    
    public function getItemsCreatedByUserId($user_id, $site_id=null){
        $sql = "SELECT * FROM Items WHERE item_createdby_userid='".$user_id."' AND item_deleted='0'";
        if(is_numeric($user_id)){
            $sql .= ' AND item_site_id="'.$site_id.'"';
        }
        $sql .= "ORDER BY Items.item_name ASC";
        $result = $this->database->queryToArray($sql);
        $ids = array();
        foreach($result as $r){
            $ids[] = $r['item_id'];
        }
        $h = new SmartestCmsItemsHelper;
        return $h->hydrateMixedListFromIdsArrayPreservingOrder($ids);
    }
    
    public function getAssetsCreatedByUserId($user_id, $site_id=null){
        $tlh = new SmartestTemplatesLibraryHelper;
        $sql = "SELECT * FROM Assets WHERE asset_user_id='".$user_id."' AND asset_deleted='0' AND asset_is_archived=0 AND asset_is_hidden=0 AND asset_is_system=0";
        if(is_numeric($user_id)){
            $sql .= ' AND asset_site_id="'.$site_id.'"';
        }
        $sql .= " AND Assets.asset_type NOT IN ('".implode("','", $tlh->getTypeCodes())."') ORDER BY Assets.asset_stringid ASC";
        $result = $this->database->queryToArray($sql);
        $assets = array();
        foreach($result as $r){
            $a = new SmartestAsset;
            $a->hydrate($r);
            $assets[] = $a;
        }
        return $assets;
    }
    
    public function getTemplatesCreatedByUserId($user_id, $site_id=null){
        $tlh = new SmartestTemplatesLibraryHelper;
        $sql = "SELECT * FROM Assets WHERE asset_user_id='".$user_id."' AND asset_deleted='0' AND asset_is_archived=0 AND asset_is_hidden=0 AND asset_is_system=0";
        if(is_numeric($user_id)){
            $sql .= ' AND asset_site_id="'.$site_id.'"';
        }
        $sql .= " AND Assets.asset_type IN ('".implode("','", $tlh->getTypeCodes())."') ORDER BY Assets.asset_stringid ASC";
        $result = $this->database->queryToArray($sql);
        $assets = array();
        foreach($result as $r){
            $a = new SmartestAsset;
            $a->hydrate($r);
            $assets[] = $a;
        }
        return $assets;
    }
    
    //////////////////////// NEW USER PROFILE STUFF/////////////////////////
    
    public function getProfileServices(){
        // Array of objects
    }
    
    public function getProfileServiceNames(){
        // Array of strings
    }
    
    public function userHasProfileForService($user_id, $service_name){
        // Boolean
    }
    
    public function getDefaultService(){
        
        // Object
        // retrieves the default service or creates it if it does not exist
        // default service is never site-specific
        
    }
    
}