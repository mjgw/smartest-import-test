<?php

class SmartestUser extends SmartestBaseUser implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue{
	
	protected $_tokens = array();
	protected $_site_ids = array();
	protected $_model_plural_names = array();
	protected $_parameters; // Only useful when the user is being stored in the session
	protected $_user_info;
    protected $_user_info_modified;
    protected $_group_membership_checker;
    protected $_bio_text_asset;
	
	protected function __objectConstruct(){
	    
		if(method_exists($this, '__myConstructor')){
		    $args = func_get_args();
		    $this->__myConstructor($args);
		}
		
		$this->_preferences_helper = SmartestPersistentObject::get('prefs_helper');
		$this->_user_info = new SmartestDbStorageParameterHolder("User info");
		
	}
	
	public function hydrate($id, $bother_with_tokens=true){
		
		if(is_array($id)){
			
			if(array_key_exists('username', $id) && array_key_exists('password', $id) && array_key_exists('user_id', $id)){
				
				$this->_properties['username'] = $id['username'];
				$this->_properties['password'] = $id['password'];
			
				foreach($id as $key => $value){
					if(substr($key, 0, strlen($this->_table_prefix)) == $this->_table_prefix){
						$this->_properties[substr($key, strlen($this->_table_prefix))] = $value;
						$this->_came_from_database = true;
					}else if(isset($this->_no_prefix[$key])){
						$this->_properties[$key] = $value;
					}
					
				}
				
				if($bother_with_tokens && get_class($this) == 'SmartestSystemUser'){
				    $this->getTokens();
			    }
                
                if(method_exists($this, '__postHydrationAction')){
                    $this->__postHydrationAction();
                }
				
				return true;
			
			}
			
		}else{
		
			if(is_numeric($id)){
				// numeric_id
				$field = 'user_id';
			}else if(SmartestStringHelper::isEmailAddress($id)){
				// 'webid'
				$field = 'user_email';
			}else if(preg_match('/^[a-zA-Z0-9_-]+$/', $id)){
				// name
				$field = 'username';
			}
		
			$sql = "SELECT * FROM ".$this->_table_name." WHERE $field='$id'";
			
			$result = $this->database->queryToArray($sql);
		
			if(count($result)){
			
				foreach($result[0] as $name => $value){
					if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
						$this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
						$this->_properties_lookup[SmartestStringHelper::toCamelCase(substr($name, strlen($this->_table_prefix)))] = substr($name, strlen($this->_table_prefix));
					}else if(isset($this->_no_prefix[$name])){
					    $this->_properties[$name] = $value;
					    $this->_properties_lookup[SmartestStringHelper::toCamelCase($name)] = $name;
					}
				}
			
				$this->_came_from_database = true;
                if(method_exists($this, '__postHydrationAction')){
                    $this->__postHydrationAction();
                }
				return true;
				
			}else{
				return false;
			}
		}
	}
	
	public function __postHydrationAction(){
	    
	    if(!$this->_user_info){
	        $this->_user_info = new SmartestDbStorageParameterHolder("Info for user '".$this->_properties['username']."'");
        }
        
        $this->_user_info->loadArray(unserialize($this->_properties['info']));
	    
	}
    
    public function getUserContextSiteId(){
        
        if(defined('SM_USER_CONTEXT_SITE_ID')){
            return constant('SM_USER_CONTEXT_SITE_ID');
        }
        
        if(isset($GLOBALS['_site']) && is_object($GLOBALS['_site'])){
            $site_id = $GLOBALS['_site']->getId();
        }else if($this->getRequest()->getModule() == 'website' && defined('SM_CMS_PAGE_SITE_ID')){
	        // This is mostly for when objects are used on web pages
            $site_id = constant('SM_CMS_PAGE_SITE_ID');
        }else if(is_object(SmartestSession::get('current_open_project'))){
            // This is mostly for when objects are used within the Smartest backend
            // make sure the site object exists
            $site_id = SmartestSession::get('current_open_project')->getId();
        }
        
        define('SM_USER_CONTEXT_SITE_ID', $site_id);
        
        return $site_id;
        
    }
	
	public function getUsername(){
		return $this->_properties['username'];
	}
	
	// must have a length of between 4 and 40
	public function setUsername($username){
		if(strlen($username) > 3 && strlen($username) < 41){
		    $username = SmartestStringHelper::toUsername($username);
			$this->_properties['username'] = $username;
			$this->_modified_properties['username'] = $username;
		}
	}
	
	// returns hashed password for checking
	public function getPassword(){
		return $this->_properties['password'];
	}
	
	// must have a minimum length of 4
	public function setPassword($password){
		if(SmartestStringHelper::isMd5Hash($password)){
			$this->_properties['password'] = $password;
			$this->_modified_properties['password'] = $password;
			return true;
		}else{
			if(strlen($password) > 3){
				$this->_properties['password'] = md5($password);
				$this->_modified_properties['password'] = md5($password);
				return true;
			}else{
			    return false;
			}
		}
	}
	
	public function isAuthenticated(){
		
		// only works for the current logged in user
		if(SmartestSession::get('user:isAuthenticated')){
			return true;
		}else{
			return false;
		}
	}
	
	public function __toString(){
	    return $this->getFullName();
	}
    
    public function __toSimpleObject(){
        $obj = parent::__toSimpleObject();
        $obj->object_type = 'user';
        if($this->getType() != 'SM_USERTYPE_SYSTEM_USER'){
            $obj->profile_pic = (is_object($this->getProfilePic())) ? $this->getProfilePic()->__toSimpleObjectForParentObjectJson() : null;
            $obj->bio = ($this->getBioTextAsset() instanceof SmartestRenderableAsset) ? $this->getBioTextAsset()->__toSimpleObjectForParentObjectJson() : null;
        }
        $obj->invert_name_order = SmartestStringHelper::toRealBool($obj->invert_name_order);
        return $obj;
    }
    
    public function stdObjectOrScalar(){
        return $this->__toSimpleObject();
    }
	
	public function getFullName(){
	    
	    $full_name = $this->_properties['firstname'];
	    
	    if($this->_properties['firstname']){
	        $full_name .= ' ';
	    }
	    
	    if($this->_properties['lastname']){
	        $full_name .= $this->_properties['lastname'];
	    }
	    
	    return trim($this->_properties['firstname'].' '.$this->_properties['lastname']);
	    
	}
	
	public function __toArray(){
	    
	    $data = parent::__toArray();
	    $data['full_name'] = $this->getFullName();
	    
	    return $data;
	    
	}
	
	protected function getModelPluralNames(){
        
        if(!count($this->_model_plural_names)){
            $du = new SmartestDataUtility;
            $this->_model_plural_names = $du->getModelPluralNamesLowercase($this->getCurrentSiteId());
        }
        
        return $this->_model_plural_names;
    }
	
	public function offsetGet($offset){
	    
	    $offset = strtolower($offset);
	    
	    switch($offset){
	        case "password":
	        case "password_salt":
	        return null;
	        
	        case "full_name":
	        case "fullname":
	        return $this->getFullName();
	        
	        case "profile_pic":
	        return $this->getProfilePic();
	        
	        case "profile_pic_asset_id":
	        return $this->getProfilePicAssetId();
	        
	        case "bio":
	        return new SmartestString($this->getBio());
            
            case "bio_text":
            // var_dump($this->getBioTextAsset()->toRenderableAsset()->render());
            // var_dump($this->getBioTextAsset()->toRenderableAsset());
            return $this->getBioTextForRender();
	        
	        case "website":
	        case "website_url":
	        $url = new SmartestExternalUrl($this->_properties['website']);
	        return $url;
	        
	        case "email":
	        return new SmartestEmailAddress($this->_properties['email']);
            
            case "profile_initials":
            if(strlen($this->_properties['firstname']) && strlen($this->_properties['lastname'])){
                return $this->_properties['firstname']{0}.$this->_properties['lastname']{0};
            }else if(strlen($this->_properties['firstname'])){
                return substr($this->_properties['firstname'], 0, 2);
            }else if(strlen($this->_properties['lastname'])){
                return substr($this->_properties['lastname'], 0, 2);
            }else{
                return '?';
            }
	        
	        case "has_twitter_handle":
	        case "has_twitter_account":
	        case "has_twitter_acct":
	        case "has_twitter_username":
	        return (bool) strlen($this->getTwitterHandle());
	        
	        case "twitter_account_object":
	        case "twitter_handle_object":
	        if(strlen($this->getTwitterHandle())){
	            return new SmartestTwitterAccountName($this->getTwitterHandle());
            }else{
                break;
            }
            
            case "groups":
            return new SmartestArray($this->getGroups($this->getUserContextSiteId()));
            
            case "all_groups":
            return new SmartestArray($this->getGroups('ALL'));
            
            case "is_in_group":
            case "in_group":
            return $this->getGroupMembershipChecker($this->getUserContextSiteId());
            
            case "info":
            return $this->_user_info;
            
            case 'tags':
            return new SmartestArray($this->getTags());
            
            case 'action_url':
            return $this->getActionUrl();
            
            case 'small_icon':
            return $this->getSmallIconUrl();
	        
	        default:
            if(parent::offsetExists($offset)){
                return parent::offsetGet($offset);
            }else if($this->_user_info->hasParameter($offset)){
                return $this->_user_info->getParameter($offset);
            }else if(in_array($offset, array_keys($this->getModelPluralNames()))){
                return $this->getCreditedItemsOnCurrentSite($this->_model_plural_names[$offset]);
            }
	        
	    }
	    
	}
	
	public function setDraftMode($mode){
	    $this->_draft_mode = (bool) $mode;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	public function getCreditedItemsOnCurrentSite($model_id=null, $mode='DEFAULT_MODE'){
	    
	    if($mode == 'DEFAULT_MODE'){
	        if($this->getDraftMode()){
	            $mode = 0;
	        }else{
	            $mode = 9;
	        }
	    }
	    
	    if($site_id = $this->getCurrentSiteId()){
            return $this->getCreditedItems($site_id, $model_id, $mode);
        }else{
            return array();
        }
    }
    
    public function getProfilePicAssetId(){
        
        if($this->baseClassHasField('profile_pic_asset_id')){
            if($this->_properties['profile_pic_asset_id']){
                return (int) $this->_properties['profile_pic_asset_id'];
            }else{
                return (int) $this->getDefaultProfilePicAssetId();
            }
        }else{
            $this->__call('getProfilePicAssetId', null);
        }
        
    }
    
    public function getDefaultProfilePicAssetId(){
        
        $ph = new SmartestPreferencesHelper;
        $asset = new SmartestAsset;
        
        // does the setting exist?
        if($ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getCurrentSiteId(), true)){
            
            // if so, what is it's value?
            return (int) $ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getCurrentSiteId());
        
        }elseif($asset->findBy('url', 'default_user_profile_pic.jpg')){
        
            return (int) $asset->getId();
        
        }else{
            
            // if not, create the asset and set the value of the preference to the id of the new asset
            $a = new SmartestAsset;
            $a->setUrl('default_user_profile_pic.jpg');
            $a->setWebid(SmartestStringHelper::random(32));
            $a->setIsSystem(1);
            $a->setStringId('default_user_profile_pic_asset_id');
            $a->setLabel('Default User Profile Picture');
            $a->setType('SM_ASSETTYPE_JPEG_IMAGE');
            $a->setUserId('0');
            $a->setCreated(time());
            $a->setSiteId(1);
            $a->setShared(1);
            $a->save();
            
            $p = $a->getId();
            
            $ph->setGlobalPreference('default_user_profile_pic_asset_id', $p, null, $this->getCurrentSiteId());
            return (int) $p;
            
        }
        
    }
    
    public function getProfilePic(){
        
        if(is_object($this->_profile_pic_asset)){
            
            return $this->_profile_pic_asset;
            
        }else{
            
            $asset = new SmartestRenderableAsset;
            
            if($asset->find($this->getProfilePicAssetId())){
                $this->_profile_pic_asset = $asset;
            }
            
            if($this->getRequest()->getAction() == 'renderEditableDraftPage'){
                $asset->setDraftMode(true);
            }
            
            return $asset;
            
        }
        
    }
    
    public function refreshProfilePic(){
        
        $asset = new SmartestRenderableAsset;
        
        if($asset->find($this->getProfilePicAssetId(), true)){
            
            $this->_profile_pic_asset = $asset;
            
            if($this->getRequest()->getAction() == 'renderEditableDraftPage'){
                $asset->setDraftMode(true);
            }
            
        }
        
    }
	
	public function sendEmail($subject, $message, $from=""){
	    
	    if(!isset($from{0})){
	        $from = 'Smartest <smartest@'.$_SERVER['HTTP_HOST'].'>';
	    }
	    
	    $to = $this->_properties['email'];
	    
	    if(SmartestStringHelper::isEmailAddress($to)){
	        mail($to, $subject, $message, "From: ".$from."\r\nReply-to: ".$from);
	        return true;
        }else{
            SmartestLog::getInstance('system')->log("Could not send e-mail to invalid e-mail address: '".$to."'.");
        }
	    
	}
	
	public function getCreditedItems($site_id=null, $model_id=null, $mode=9){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ITEM_AUTHORS');
	    $q->setTargetEntityByIndex(2);
	    $q->addQualifyingEntityByIndex(1, $this->_properties['id']);
	    $q->addForeignTableConstraint('Items.item_deleted', '0');
	    $draft_mode = $mode < 6;
	    
	    if($mode > 5){
            $q->addForeignTableConstraint('Items.item_public', 'TRUE');
        }
        
        if(is_numeric($model_id)){
            $q->addForeignTableConstraint('Items.item_itemclass_id', $model_id);
        }
        
        if(is_numeric($site_id)){
            $q->addForeignTableOrConstraints(
	            array('field'=>'Items.item_site_id', 'value'=>$site_id),
	            array('field'=>'Items.item_shared', 'value'=>'1')
	        );
        }
        
        if(in_array($mode, array(1,4,7,10))){
	    
	        $q->addForeignTableConstraint('Items.item_is_archived', '1');
	    
        }else if(in_array($mode, array(2,5,8,11))){
            
            $q->addForeignTableConstraint('Items.item_is_archived', '0');
            
        }
        
        $ids = $q->retrieveIds();
        $ih = new SmartestCmsItemsHelper;
        
        if(is_numeric($model_id)){
            $items = $ih->hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode);
        }else{
            $items = $ih->hydrateMixedListFromIdsArray($ids, $draft_mode);
        }
        
        return new SmartestArray($items);
	    
	}
	
	public function getCreditedWorkOnSite($site_id='', $draft=false){
        
        /*  $master_array = array();
        
        $pages = $this->getPages($site_id, $draft);
        $items = $this->getItems($site_id, $draft);
        
        foreach($pages as $p){
            
            $key = $p->getDate();
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $p;
            
        }
        
        foreach($items as $i){
            
            $key = $i->getDate();
            if($key instanceof SmartestDateTime){
                $key = $key->getUnixFormat();
            }
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
            
        }
        
        krsort($master_array);
        
        return $master_array; */
        
    }
    
    public function getBioTextAsset(){
        
        if(is_object($this->_bio_text_asset) && $this->_bio_text_asset instanceof SmartestAsset){
            return $this->_bio_text_asset;
        }
        
        $id = $this->getField('bio_asset_id');
        $asset = new SmartestRenderableAsset;
        
        if(!is_numeric($id) || !$asset->find($id)){
            
            $this->_bio_text_asset = new SmartestRenderableAsset;
            
            $this->_bio_text_asset->setWebid(SmartestStringHelper::random(32));
            $this->_bio_text_asset->setLabel('User profile bio for '.$this->getFullName());
            $this->_bio_text_asset->setCreated(time());
            $this->_bio_text_asset->setModified(time());
            $this->_bio_text_asset->setStringId(SmartestStringHelper::toVarName('User profile bio for '.$this->getFullName()));
            $this->_bio_text_asset->setUrl(SmartestStringHelper::toVarName('User profile bio for '.$this->getFullName()).'.html');
            $this->_bio_text_asset->setUserId($this->getId());
            $this->_bio_text_asset->setSiteId(0);
            $this->_bio_text_asset->setShared(1);
            $this->_bio_text_asset->setType('SM_ASSETTYPE_RICH_TEXT');
            
            if($this->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                $this->_bio_text_asset->setPublicStatusTrusted(1);
            }else{
                $this->_bio_text_asset->setPublicStatusTrusted(0);
            }
            
            $this->_bio_text_asset->setIsHidden(1);
            $this->_bio_text_asset->setIsSystem(1);
            
            $this->_bio_text_asset->save();
            
            $this->_bio_text_asset->getTextFragment()->setContent(SmartestStringHelper::sanitize(SmartestStringHelper::parseTextile(stripslashes($this->_properties['bio']))));
            $this->_bio_text_asset->connectTextFragmentOnSave();
            $this->_bio_text_asset->save();
            
            $this->setField('bio_asset_id', $asset->getId());
            $sql = "UPDATE Users SET Users.user_bio_asset_id='".$this->_bio_text_asset->getId()."' WHERE Users.user_id='".$this->getId()."' LIMIT 1";
            $this->database->rawQuery($sql);
            
        }else{
            $this->_bio_text_asset = $asset;
        }
        
        return $this->_bio_text_asset;
        
    }
    
    public function getBio(){
        return stripslashes($this->_properties['bio']);
    }
    
    public function getBioForEditor(){
        return $this->getBioTextAsset()->getContentForEditor();
    }
    
    public function getBioTextForRender(){
        $a = $this->getBioTextAsset()->toRenderableAsset();
        if($this->getCurrentRequestData()->g('action') == "renderEditableDraftPage"){
            $a->setDraftMode(true);
        }
        return $a;
    }
    
    public function updateBioTextAssetFromEditor($content){
        
	    $content = SmartestStringHelper::unProtectSmartestTags($content);
	    $content = SmartestTextFragmentCleaner::convertDoubleLineBreaks($content);
        $content = SmartestStringHelper::sanitize($content);
        
        
        
	    $this->getBioTextAsset()->setContentFromEditor($content);
        $this->getBioTextAsset()->setModified(time());
        $this->getBioTextAsset()->save();
        $this->getBioTextAsset()->getTextFragment()->publish();
        
    }
    
    protected function instantiateParameters(){
        if(!is_object($this->_parameters)){
            $this->_parameters = new SmartestParameterHolder("Parameters for user: ".$this->__toString());
        }
    }
    
    public function getParameter($param){
        $this->instantiateParameters();
        return $this->_parameters->getParameter($param);
    }
    
    public function setParameter($param, $value){
        $this->instantiateParameters();
        $this->_parameters->setParameter($param, $value);
    }
    
    public function hasParameter($param){
        $this->instantiateParameters();
        return $this->_parameters->hasParameter($param);
    }

    
    public function passwordIs($password){
        
        return $this->getPassword() == md5($password.$this->getPasswordSalt());
        
    }
    
    public function setPasswordWithSalt($raw_password, $salt, $ignore_repeat_password=false){
        if($this->passwordIs($raw_password) && !$ignore_repeat_password){
            return false;
        }else{
            $this->setPasswordSalt($salt);
            $this->setField('password', md5($raw_password.$salt));
            $this->setPasswordLastChanged(time());
            return true;
        }
    }
    
    public function getOrganisationName(){
        return $this->getOrganizationName();
    }
    
    public function setOrganisationName($name){
        return $this->setOrganizationName($name);
    }
    
    public function setInfoValue($field, $new_data){
	    
	    $field = SmartestStringHelper::toVarName($field);
	    // URL Encoding is being used to work around a bug in PHP's serialize/unserialize. No actual URLS are necessarily in use here:
	    $this->_user_info->setParameter($field, rawurlencode(utf8_decode($new_data)));
        $this->_user_info_modified = true;
	    $this->_modified_properties['info'] = SmartestStringHelper::sanitize(serialize($this->_user_info->getArray()));
	    
	}
	
	public function getInfoValue($field){
	    
	    $field = SmartestStringHelper::toVarName($field);
        
        if($this->_user_info->hasParameter($field)){
            return $this->_user_info->getParameter($field);
	    }else{
	        return null;
	    }
	}
    
    public function setInfoField($field, $new_data){
        $this->setInfoValue($field, $new_data);
    }
    
    public function getInfoField($field){
        return $this->getInfoValue($field);
    }
    
    public function delete(){
        
        if($this->getId() > 0 && $this->getUsername() != 'smartest'){ // The Smartest user, ID zero, should never be deletable
        
            // release all pages, files and items
            $sql = "UPDATE Pages SET page_held_by='0', page_is_held='0', page_createdby_userid='0' WHERE page_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
            $sql = "UPDATE Items SET item_held_by='0', item_is_held='0', item_createdby_userid='0' WHERE item_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
            $sql = "UPDATE Assets SET asset_held_by='0', asset_is_held='0', asset_user_id='0' WHERE asset_held_by='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            // delete "recently edited" records
            $sql = "DELETE FROM ManyToManyLookups WHERE (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_PAGES' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_ITEMS' AND mtmlookup_entity_2_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_RECENTLY_EDITED_TEMPLATES' AND mtmlookup_entity_2_foreignkey='".$this->getId()."')";
            $this->database->rawQuery($sql);
        
            // delete authorship records
            $sql = "DELETE FROM ManyToManyLookups WHERE (mtmlookup_type='SM_MTMLOOKUP_ITEM_AUTHORS' AND mtmlookup_entity_1_foreignkey='".$this->getId()."') OR (mtmlookup_type='SM_MTMLOOKUP_PAGE_AUTHORS' AND mtmlookup_entity_1_foreignkey='".$this->getId()."')";
            $this->database->rawQuery($sql);
        
            // delete all tokens/permissions
            $sql = "DELETE FROM UsersTokensLookup WHERE utlookup_user_id='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            // delete all settings
            $sql = "DELETE FROM Settings WHERE setting_user_id='".$this->getId()."'";
            $this->database->rawQuery($sql);
        
            parent::delete();
        
        }
        
    }
    
    public function getStorableFormat(){
        return $this->getId();
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->find($v);
    }
    
    public function hydrateFromFormData($v){
        return $this->find($v);
    }
    
    public function renderInput($params){}
    
    public function setValue($v){
        if(is_numeric($v)){
            return $this->find($v);
        }else if(SmartestStringHelper::isValidUsername($v)){
            return $this->findBy('username', $v);
        }else{
            return false;
        }
    }
    
    public function getValue(){
        return $this->getId();
    }
    
    // Note - __toString() is above
    
    public function isPresent(){
        return $this->_came_from_database || count($this->_modified_properties);
    }
    
    ////////////////////////////////// Todo list stuff ///////////////////////////////////////
	
	public function assignTodo($type_code, $entity_id, $assigner_id=0, $input_message='', $send_email=false){
	    
	    /* $type = SmartestTodoListHelper::getType($type_code);
	    
	    if(isset($message{1})){
	        $input_message = SmartestStringHelper::sanitize($message);
	    }else{
	        $input_message = $type->getDescription();
	    } */
	    
	    $task = new SmartestTodoItem;
	    $task->setReceivingUserId((int) $this->_properties['id']);
	    $task->setAssigningUserId((int) $assigner_id);
	    $task->setForeignObjectId((int) $entity_id);
	    $task->setTimeAssigned(time());
	    $task->setDescription(strip_tags(SmartestStringHelper::sanitize($input_message)));
	    $task->setType($type_code);
	    $task->save();
	    
	    /* if($send_email){
	        // code goes in here to send notification email to user
	    } */
	    
	}
	
	public function hasTodo($type, $entity_id){
	    $id = (int) $entity_id;
	    $type = SmartestStringHelper::sanitize($type);
	    $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_foreign_object_id='".$id."' AND todoitem_type='".$type."' AND todoitem_is_complete !=1";
	    return (bool) count($this->database->queryToArray($sql));
	    
	}
	
	public function getTodo($type, $entity_id){
	    
	    $id = (int) $entity_id;
	    $type = SmartestStringHelper::sanitize($type);
	    $sql = "SELECT * FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_foreign_object_id='".$id."' AND todoitem_type='".$type."' AND todoitem_is_complete !=1";
	    $result = $this->database->queryToArray($sql);
	    
	    if(isset($result[0])){
	        $todo = new SmartestTodoItem;
	        $todo->hydrate($result[0]);
	        return $todo;
        }else{
            return false;
        }
	    
	}
	
	public function getNumTodoItems($get_assigned=false){
	    
	    if($get_assigned){
	        $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_assigning_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1";
	    }else{
	        $sql = "SELECT todoitem_id FROM TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1";
        }
	    
	    return count($this->database->queryToArray($sql));
	    
	}
	
	public function getTodoItems($get_assigned=false){
	    
	    if($get_assigned){
	        $sql = "SELECT * FROM Users, TodoItems WHERE todoitem_assigning_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1 AND TodoItems.todoitem_receiving_user_id=Users.user_id ORDER BY todoitem_time_assigned DESC";
	    }else{
	        $sql = "SELECT * FROM Users, TodoItems WHERE todoitem_receiving_user_id='".$this->_properties['id']."' AND todoitem_is_complete !=1 AND TodoItems.todoitem_assigning_user_id=Users.user_id ORDER BY todoitem_time_assigned DESC";
        }
	    
	    $result = $this->database->queryToArray($sql);
	    $tasks = array();
	    
	    if(count($result)){
	        foreach($result as $t){
	            $task = new SmartestTodoItem;
	            $task->hydrate($t);
	            $tasks[] = $task;
	        }
	    }
	    
	    return $tasks;
	    
	}
	
	public function getTodoItemsAsArrays($get_assigned=false, $get_foreign_object_info=false){
	    
	    $tasks = $this->getTodoItems($get_assigned);
	    $arrays = array();
	    
	    foreach($tasks as $t){
	        $arrays[] = $t->__toArray($get_foreign_object_info);
	    }
	    
	    return $arrays;
	    
	}
    
    public function clearCompletedTodos(){
	    
	    $sql = "DELETE FROM TodoItems WHERE todoitem_is_complete=1 AND todoitem_receiving_user_id=".$this->getId()."";
	    
	}
    
    
    //////////////////////// TAGS ////////////////////////
    
	public function getTags(){
	    
	    $sql = "SELECT * FROM Tags, TagsObjectsLookup WHERE TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_type='SM_USER_TAG_LINK' ORDER BY Tags.tag_name";
	    $result = $this->database->queryToArray($sql);
	    $ids = array();
	    $tags = array();
	    
	    foreach($result as $ta){
	        if(!in_array($ta['taglookup_tag_id'], $ids)){
	            $ids[] = $ta['taglookup_tag_id'];
	            $tag = new SmartestTag;
	            $tag->hydrate($ta);
	            $tags[] = $tag;
	        }
	    }
	    
	    return $tags;
	    
	}
	
	public function getTagsAsCommaSeparatedString(){
	    
	    $tags = $this->getTags();
	    $labels_array = array();
	    
	    foreach($tags as $t){
	        $labels_array[] = $t->getLabel();
	    }
	    
	    return implode(', ', $labels_array);
	    
	}
    
    public function tag($tag_identifier){
        
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->find($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->findBy('name', $tag_name)){
                // create tag
    	        $tag->setLabel($tag_identifier);
    	        $tag->setName($tag_name);
    	        $tag->save();
    	    }
	    }
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->_properties['id']."', 'SM_USER_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
        
    }
    
    public function untag($tag_identifier){
        
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                return false;
    	    }
	    }
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_USER_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
        
    }
    
	public function addTagWithId($tag_id){
	    
        $tag_id = (int) $tag_id;
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag_id."', '".$this->_properties['id']."', 'SM_USER_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
    
	public function removeTagWithId($tag_id){
	    
        $tag_id = (int) $tag_id;
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag_id."' AND taglookup_type='SM_USER_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
    
	public function hasTag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        $sql = "SELECT * FROM TagsObjectsLookup WHERE taglookup_type='SM_USER_TAG_LINK' AND taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."'";
	    }else{
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        $sql = "SELECT * FROM TagsObjectsLookup, Tags WHERE taglookup_type='SM_USER_TAG_LINK' AND taglookup_object_id='".$this->_properties['id']."' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND Tags.tag_name='".$tag_name."'";
	    }
	    
	    return (bool) count($this->database->queryToArray($sql));
	    
	}
    
	public function createOrConnectTags($new_tag_strings_array, $title_case=true){
	    
	    if(!is_array($new_tag_strings_array) || !count($new_tag_strings_array)){
	        return false;
	    }
	    
	    // 1. Find out which tags already exist
	    
	    // create array with slugs as keys, raw values as values
	    $useful_array = array();
	    
	    foreach($new_tag_strings_array as $string){
	        $useful_array[SmartestStringHelper::toSlug($string, true)] = $string;
	    }
	    
	    
	    $new_tag_slugs = array();
	    
	    foreach($new_tag_strings_array as $nts){
	        $new_tag_slugs[] = SmartestStringHelper::toSlug($nts, true);
	    }
	    
	    $sql = "SELECT * FROM Tags WHERE tag_type='SM_TAGTYPE_TAG' AND tag_name IN ('".implode("','", $new_tag_slugs)."')";
	    $result = $this->database->queryToArray($sql);
	    $existing_tags = array();
	    
	    foreach($result as $row){
	        $t = new SmartestTag;
	        $t->hydrate($row);
	        $existing_tags[$row['tag_name']] = $t;
	    }
	    
	    
	    // 2. Cycle through requested tags, attaching those which already exist and creating and attaching those that don't
	    foreach($useful_array as $requested_tag_slug => $requested_tag_label){
	        if(isset($existing_tags[$requested_tag_slug])){
	            // The tag exists
	            $this->addTagWithId($existing_tags[$requested_tag_slug]->getId());
	        }else{
	            
	            if(strlen($requested_tag_label) && strlen($requested_tag_slug)){ // Prevent blanks
	            
    	            // The tag does not exist, so make it
    	            $new_tag = new SmartestTag;
	            
    	            if($title_case){
    	                $new_tag->setLabel(SmartestStringHelper::toTitleCase($requested_tag_label));
                    }else{
                        $new_tag->setLabel($requested_tag_label);
                    }
                
    	            $new_tag->setName($requested_tag_slug);
    	            $new_tag->save();
    	            $this->addTagWithId($new_tag->getId());
	            
                }
	            
	        }
	    }
	    
	}
    
    public function getGroups($site_id=null){
        
        if($site_id != 'ALL' && !is_numeric($site_id)){
            $site_id = $this->getUserContextSiteId();
        }
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_USER_GROUP_MEMBERSHIP');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        // $q->addForeignTableConstraint('Sets.set_type', 'SM_SET_USERGROUP');
	    $q->addSortField('Sets.set_name');
        
        if(is_numeric($site_id)){
            $q->addForeignTableConstraint('Sets.set_site_id', $this->_site_id);
        }
    
        $groups = $q->retrieve(true);
        
        return $groups;
        
    }
    
    public function getGroupIds(){
        
        $group_ids = array();
        
        foreach($this->getGroups() as $g){
            $group_ids[] = $g->getId();
        }
        
        return $group_ids;
        
    }
    
    public function isInGroup($group_identifier, $site_id=null){
        if($site_id != 'ALL'){
            $site_id = $this->getUserContextSiteId();
        }
        return $this->getGroupMembershipChecker($site_id)->belongsToGroup($group_identifier);
    }
    
    public function getGroupMembershipChecker($site_id=null){
        
        if($site_id != 'ALL'){
            $site_id = $this->getUserContextSiteId();
        }
        
        if(!is_object($this->_group_membership_checker)){
            $this->_group_membership_checker = new SmartestUserGroupMembershipChecker($this, $site_id);
        }
        
        return $this->_group_membership_checker;
        
    }
    
	public function getActionUrl(){
	    
	    // return $this->_request->getDomain().'assets/editAsset?asset_id='.$this->getId();
	    return $this->_request->getDomain().'users/editUser?user_id='.$this->getId();
	    
	}
    
    public function getSmallIconUrl(){
        
        if($this->getProfilePic()->getId()){
            return $this->getProfilePic()->getImage()->getSquareVersion(16)->getWebPath();
        }else{
            return $this->_request->getDomain().'Resources/Icons/user.png';
        }
        
    }
    
    //////////////////////// NEW USER PROFILE STUFF/////////////////////////
    
    public function getProfile($service_name='_default'){
        
        
        
    }
	
}