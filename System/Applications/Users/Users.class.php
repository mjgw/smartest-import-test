<?php

class Users extends SmartestSystemApplication{
    
    public function startPage(){
		
		$this->setFormReturnUri();
		$this->setFormReturnDescription('user accounts');
		$this->setTitle('User accounts');
        $this->requireOpenProject();
		
		$h = new SmartestUsersHelper;
		
		if($this->getRequestParameter('_show_ordinary') && SmartestStringHelper::toRealBool($this->getRequestParameter('_show_ordinary'))){
		    $users = $h->getOrdinaryUsers();
            $this->send(false, 'show_site_access');
		    $active_tab = "ordinary";
		}else{
		    $users = $h->getSystemUsers();
            $this->send($h->getUserIdsOnSite($this->getSite()->getId()), 'user_ids_on_this_site');
            $this->send(true, 'show_site_access');
		    $active_tab = "system";
		}
		
		$this->send($active_tab, 'active_tab');
        
        $ph = new SmartestPreferencesHelper;
        $asset = new SmartestAsset;
        
        if($ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getSite()->getId(), true)){
            
            $dfuppi = $ph->getGlobalPreference('default_user_profile_pic_asset_id', null, $this->getSite()->getId());
        
        }elseif($asset->findBy('url', 'default_user_profile_pic.jpg')){
        
            $dfuppi = $asset->getId();
        
        }
        
        $this->send($dfuppi, 'default_user_profile_pic_id');
		
		// $database = SmartestDatabase::getInstance('SMARTEST');
		// $users = $database->queryToArray("SELECT * FROM Users WHERE username != 'smartest'");
		$this->send($users, 'users');
		$this->send(count($users), 'count');
		
	}
	
	///////////////////////////////// USERS ////////////////////////////////////
	
	public function addUser(){
	    
	    $uhelper = new SmartestUsersHelper;
	    $roles = $uhelper->getRoles();
	    $this->setTitle('Add a user');
	    
	    if($this->getUser()->hasToken('create_users')){
	        
	        $this->send(new SmartestArray($this->getUser()->getSitesWhereUserHasToken('create_users')), 'sites');
	        $this->send($roles, 'roles');
	        
	    }else{
	        $this->addUserMessageToNextRequest("You don't have permission to add new user accounts", SmartestUserMessage::ACCESS_DENIED);
	        $this->redirect('/smartest/users');
	    }
	    
	}
	
	public function insertUser($get, $post){
		
		if($this->getUser()->hasToken('create_users')){
		
    		$user = new SmartestSystemUser;
        
            $username = SmartestStringHelper::toUsername($this->getRequestParameter('username'));
        
            if($user->findBy('username', $username)){
            
                $this->addUserMessage("The username you entered is already is use.", SmartestUserMessage::WARNING);
                $this->forward('users', 'addUser');
            
            }else{
            
                // print_r($_POST);
            
    		    $password = $this->getRequestParameter('password');
        		$firstname = $this->getRequestParameter('user_firstname');
        		$lastname = $this->getRequestParameter('user_lastname');
        		$email = $this->getRequestParameter('user_email');
        		$website = $this->getRequestParameter('user_website');
        		$salt = SmartestStringHelper::random(40);
        		$hash = md5($this->getRequestParameter('password').$salt);
    		
        		if(!SmartestStringHelper::isValidExternalUri($website)){
        		    $website = 'http://'.$website;
        		}
		    
    		    $user->setUsername($username);
        		$user->setPassword($hash);
        		$user->setPasswordSalt($salt);
        		$user->setFirstname($firstname);
        		$user->setLastname($lastname);
        		$user->setEmail($email);
        		$user->setWebsite($website);
        		$user->setRegisterDate(time());
        		
        		if($this->getRequestParameter('user_service') == 'SMARTEST'){
        		    $user->setIsSmartestAccount(1);
        		    $user->setType('SM_USERTYPE_SYSTEM_USER');
    		    }else{
    		        $user->setIsSmartestAccount(0);
    		        $user->setType('SM_USERTYPE_ORDINARY_USER');
    		    }
    		
        		$user->save();
		    
        		// add user tokens
    		
        		// print_r($this->getRequestParameter('user_role'));
        		
        		if($this->getRequestParameter('user_service') == 'SMARTEST'){
    		
            		if(is_numeric($this->getRequestParameter('user_role'))){
                
                        // User-created role is being used to assign tokens
                        $role = new SmartestRole;
                
                        if($role->find($this->getRequestParameter('user_role'))){
                            $tokens = $role->getTokens();
                        }else{
                            $tokens = array();
                        }
                
                        $l = new SmartestManyToManyLookup;
            	        $l->setType('SM_MTMLOOKUP_USER_INITIAL_ROLE');
            	        $l->setEntityForeignKeyValue(1, $user->getId());
            	        $l->setEntityForeignKeyValue(2, $this->getRequestParameter('user_role'));
            	        $l->save();
            
                    }else if(substr($this->getRequestParameter('user_role'), 0, 7) == 'system:'){
                
                        $role_id = substr($this->getRequestParameter('user_role'), 7);
                        $h = new SmartestUsersHelper;
                        $system_roles = $h->getSystemRoles();
                
                        if(isset($system_roles[$role_id])){
                            $role = $system_roles[$role_id];
                            $tokens = $role->getTokens();
                        }else{
                            $tokens = array();
                        }
                
                    }else{
                
                        $tokens = array();
                
                    }
    		
            		if($this->getRequestParameter('global_site_access')){
            		    if($this->getUser()->hasToken('grant_global_permissions')){
    		        
            		        // Add tokens from role globally
            		        foreach($tokens as $t){
                                $user->addTokenById($t->getId(), 'GLOBAL');
                            }
                    
            		    }else{
            		        $this->addUserMessageToNextRequest('You do not have permission to grant global site access or other tokens');
            		    }
        		    }else{
		        
        		        $site_ids = $this->getRequestParameter('user_sites');
		        
        		        if(is_array($site_ids)){
		            
        		            // Add tokens from role for each site
        		            foreach($site_ids as $site_id){
		                
        		                foreach($tokens as $t){
        		                    $user->addTokenById($t->getId(), $site_id);
                                }
		                
        		            }
        		        }
        		    }
    		    
		        }
        
            }
        
        }else{
            $this->addUserMessageToNextRequest("You don't have permission to add new user accounts", SmartestUserMessage::ACCESS_DENIED);
        }
    
		$this->formForward(); 
		
	}

    public function editUser($get){
  
		if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
		    $user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
    		    $this->setTitle('Edit user | '.$user->__toString());
    		    $this->send($user, 'user');
                $this->send($user->getBioForEditor(), 'bio_text_editor_content');
                if($user->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                    $sys_user = new SmartestSystemUser;
                    $sys_user->hydrate($user->getOriginalDbRecord());
                    $this->send(new SmartestArray($sys_user->getAllowedSites()), 'user_sites');
                    $this->send(true, 'is_system_user');
                }else{
                    $this->send(false, 'is_system_user');
                }
            }else{
                $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
                $this->formForward();
            }
			
			$this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
            $this->send($this->getUser()->hasToken('require_user_password_change'), 'require_password_changes');
            $this->send($this->getUser()->hasToken('modify_usernames'), 'allow_username_change');
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.", SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
        
	}
	
	public function editUserTokens($get){
	    
	    $this->requireOpenProject();
	    
	    if($this->getUser()->hasToken('modify_user_permissions')){
	    
	        $permission_editable_sites = $this->getUser()->getPermissionEditableSites();
	        
	        $user_id = $this->getRequestParameter('user_id');	
        	$user = new SmartestSystemUser;
    	
        	if($user->find($user_id)){
    	
        	    $this->setFormReturnUri();
    	        
    	        $allow_global = ($this->getUser()->hasGlobalPermission('modify_user_permissions') || $this->getUser()->hasToken('grant_global_permissions')) ? true : false;
    	        
    	        if(isset($get['site_id']) && strlen($get['site_id'])){
    	            $site_id = $get['site_id'];
    	        }else{
    	            $site_id = $this->getSite()->getId();
    	        }
    	        
    	        $this->send(true, 'show_tokens_edit_tab');
    	        
    	        $utokens = $user->getTokensOnSite($site_id, true);
        	    
        	    $tokens = $user->getAvailableTokens($site_id);
    		    
    		    $this->send($user, 'user');
    		    $this->send($utokens, 'utokens');
    		    $this->send($tokens, 'tokens');
    		    $this->send($permission_editable_sites, 'sites');
    		    $this->send($site_id, 'site_id');
    		    $this->send($allow_global, 'allow_global');
    	
    	    }else{
    	        $this->addUserMessageToNextRequest("The user ID was not recognised", SmartestUserMessage::ERROR);
    	        $this->formForward();
    	    }
	    
        }else{
            $this->addUserMessageToNextRequest("You don't have permission to edit user permissions.", SmartestUserMessage::ACCESS_DENIED);
	        $this->formForward();
        }
    	
	}
	
// 	public function editUserProfilePic(){
// 	    
// 	    if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
// 		
// 		    $user = new SmartestUser;
// 		
//     		if($user->find($this->getRequestParameter('user_id'))){
//     		    
//     		    $this->send($user, 'user');
//     		    $uh = new SmartestUsersHelper;
//     		    
//     		    if($g = $uh->getUserProfilePicsGroup()){
//     		        $this->send($g->getMembersForUser($user->getId(), $this->getSite()->getId()), 'assets');
//     		    }else{
//     		        $helper = new SmartestAssetsLibraryHelper;
//             	    $this->send($helper->getAttachableFiles($this->getSite()->getId()), 'assets');
//     		    }
//                 
//                 if(!is_file(SM_ROOT_DIR.'Public/Resources/Images/default_user_profile_pic.jpg')){
//                     if(is_writable(SM_ROOT_DIR.'Public/Resources/Images/')){
//                         SmartestFileSystemHelper::copy(SM_ROOT_DIR.'System/Install/Samples/default_user_profile_pic.jpg', SM_ROOT_DIR.'Public/Resources/Images/default_user_profile_pic.jpg');
//                     }
//                 }
//     		    
//             }else{
//                 $this->addUserMessageToNextRequest("The user ID was not recognised.", SmartestUserMessage::ERROR);
//                 $this->formForward();
//             }
//             
//             $this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
//         
//         }else{
//             
//             $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.", SmartestUserMessage::ACCESS_DENIED);
//             $this->formForward();
//             
//         }
// 	    
// 	}
	
// 	public function saveUserProfilePic(){
// 	    
// 	    if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
// 		
// 		    $user = new SmartestUser;
// 		
//     		if($user->find($this->getRequestParameter('user_id'))){
//     		    
//     		    if($this->getRequestParameter('profile_pic_asset_id') == 'NEW' && SmartestUploadHelper::uploadExists('new_picture_input')){
//     		        
//     		        $alh = new SmartestAssetsLibraryHelper;
//     	            $upload = new SmartestUploadHelper('new_picture_input');
//                     $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
//                     $types = $alh->getPossibleTypesBySuffix($upload->getDotSuffix());
// 
//                     if(count($types)){
//                         $t = $types[0]['type']['id'];
// 
//                         $ach = new SmartestAssetCreationHelper($t);
//                         $ach->createNewAssetFromFileUpload($upload, "User profile picture for ".$user->getFullName().' - '.date('M d Y'));
// 
//                         $file = $ach->finish();
//                         $file->setShared(1);
//                         $file->setIsSystem(1);
//                         $file->setIsHidden(1);
//                         $file->setUserId($user->getId());
//                         $file->save();
// 
//                         $user->setProfilePicAssetId($file->getId());
//                         $user->save();
//                         
//                         $uh = new SmartestUsersHelper;
// 
//             		    if($g = $uh->getUserProfilePicsGroup()){
// 
//                             $g->addAssetById($file->getId(), false);    
//                         
//                         }
//                         
//                         $this->addUserMessageToNextRequest("Your profile picture was successfully uploaded", SmartestUserMessage::SUCCESS);
//                         
//                         $this->formForward();
//                         
//                     }
//     		        
//     		    }else if(is_numeric($this->getRequestParameter('profile_pic_asset_id'))){
//     		        $a = new SmartestAsset;
//     		        if($a->find($this->getRequestParameter('profile_pic_asset_id'))){
//     		            $user->setProfilePicAssetId($this->getRequestParameter('profile_pic_asset_id'));
//     		            $user->save();
//                         $this->addUserMessageToNextRequest('Your profile picture has been successfully changed.', SmartestUserMessage::SUCCESS);
//     		            $this->formForward();
//     		        }else{
//                         $this->addUserMessageToNextRequest('The file you selected could not be found.', SmartestUserMessage::WARNING);
//     		            $this->formForward();
//     		        }
// 		        }else{
// 		            
// 		        }
//     		    
//             }else{
//                 $this->addUserMessageToNextRequest("The user ID was not recognised.", SmartestUserMessage::ERROR);
//                 $this->formForward();
//             }
//             
//             $this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
//         
//         }else{
//             
//             $this->addUserMessageToNextRequest("You don't have permission to modify users other than yourself.", SmartestUserMessage::ACCESS_DENIED);
//             $this->formForward();
//             
//         }
// 	    
// 	}
	
	public function transferTokens($get, $post){
    	
    	if(($this->getRequestParameter('user_id') != $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_permissions')) || ($this->getRequestParameter('user_id') == $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_own_permissions'))){
    	    
    	    $user = new SmartestSystemUser;
    	    
    	    if($user->find($this->getRequestParameter('user_id'))){
    	        
    	        if($post['transferAction'] == 'add'){
			        foreach($post['tokens'] as $token_id){
			            $user->addTokenById($token_id, $this->getRequestParameter('site_id'));
			        }
		        }else{
		            foreach($post['sel_tokens'] as $token_id){
			            $user->removeTokenById($token_id, $this->getRequestParameter('site_id'));
			        }
		        }
		        
		        if($this->getRequestParameter('user_id') == $this->getUser()->getId()){
		            // if the user is editing his or her own permissions, refresh current user's tokens
		            $this->getUser()->reloadTokens();
		        }
		    
	        }else{
	            
	            $this->addUserMessageToNextRequest('The user ID was not recognized.', SmartestUserMessage::ERROR);
	            
	        }
		
	    }else{
	        
	        if($this->getRequestParameter('user_id') == $this->getUser()->getId()){
	            $this->addUserMessageToNextRequest('You do not have the permissions needed to modify your own permissions.', SmartestUserMessage::ACCESS_DENIED);
	        }else{
	            $this->addUserMessageToNextRequest('You do not have the permissions needed to modify the permissions of other users.', SmartestUserMessage::ACCESS_DENIED);
            }
	        
	    }
		
		$this->formForward();
		
	}
	
	public function deleteUser(){
	    
	    if($this->getUser()->hasToken('delete_users')){
	    
        	$user = new SmartestUser;
        	$user_id = (int) $this->getRequestParameter('user_id');
    	
        	if($user_id == $this->getUser()->getId()){
    	
        	    $this->addUserMessageToNextRequest("You can't delete your own account.", SmartestUserMessage::WARNING);
    	
        	}else{
    	
        	    if($user->find($user_id)){
    	        
        	        $this->addUserMessageToNextRequest("The user '".$user->getUsername()."' was successfully deleted.", SmartestUserMessage::SUCCESS);
    		        $user->delete();
		
    	        }else{
	            
    	            $this->addUserMessageToNextRequest("The user ID was not recognized.", SmartestUserMessage::ERROR);
	            
    	        }
	    
            }
        
        }else{
            
            $this->addUserMessageToNextRequest('You do not have the permissions needed to delete users.', SmartestUserMessage::ACCESS_DENIED);
            
        }
		
		$this->formForward();
	}
    
    public function updateUser($get, $post){
		
		if($this->getRequestParameter('user_id') == $this->getUser()->getId() || $this->getUser()->hasToken('modify_other_user_details')){
		
    		$user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
		    
    		    $user->setFirstname($this->getRequestParameter('user_firstname'));
    		    $user->setLastname($this->getRequestParameter('user_lastname'));
    		    $user->setEmail($this->getRequestParameter('email'));
    		    $user->setWebsite($this->getRequestParameter('user_website'));
                $user->setOrganizationName($this->getRequestParameter('user_organization_name'));
                
    		    // $user->setBio(addslashes($post['user_bio']));
                $user->updateBioTextAssetFromEditor($this->getRequestParameter('user_bio'));
    		    
    		    if($this->getRequestParameter('thing_that_aint_u5ern4me') && SmartestStringHelper::toUsername($this->getRequestParameter('thing_that_aint_u5ern4me')) && SmartestStringHelper::toUsername($this->getRequestParameter('thing_that_aint_u5ern4me')) != $user->getUsername()){
    		        if($this->getUser()->hasToken('modify_usernames')){
    		            $user->setUsername(SmartestStringHelper::toUsername($this->getRequestParameter('thing_that_aint_u5ern4me')));
    		        }else{
    		            // attempt at changing the username without having permission
    		        }
		        }
                
                if($this->requestParameterIsSet('user_profile_pic_id') && strlen($this->getRequestParameter('user_profile_pic_id'))){
                    
                    $file = new SmartestAsset;
                    
                    if( $file->find($this->getRequestParameter('user_profile_pic_id'))){
                        
                        $user->setProfilePicAssetId($file->getId());
                        
                        if($user->getId() == $this->getUser()->getId()){
                            $this->getUser()->setProfilePicAssetId($file->getId());
                            $this->getUser()->refreshProfilePic();
                        }
                        
                        $uh = new SmartestUsersHelper;
                        
                    }
                    
                }else{
                    $user->setProfilePicAssetId(null);
                    
                    if($user->getId() == $this->getUser()->getId()){
                        $this->getUser()->setProfilePicAssetId(null);
                        $this->getUser()->refreshProfilePic();
                    }
                }
    		    
    		    if(isset($post['password']) && strlen($post['password']) && $post['password'] == $post['passwordconfirm']){
    		        $user->setPasswordWithSalt($post['password'], SmartestStringHelper::random(40));
    		        $this->addUserMessageToNextRequest("The user has been updated, including a new password.", SmartestUserMessage::SUCCESS);
    		        $user->setPasswordChangeRequired(0);
    	        }else{
    		        $this->addUserMessageToNextRequest("The user has been updated.", SmartestUserMessage::SUCCESS);
    		        if($this->getUser()->hasToken('require_user_password_change')){
    		            if($this->getRequestParameter('require_password_change')){
        		            $user->setPasswordChangeRequired(1);
    		            }else{
    		                $user->setPasswordChangeRequired(0);
    		            }
        		    }
    	        }
	        
    	        $user->save();
                
                if($user->getId() == $this->getUser()->getId()){
	                $this->getUser()->refreshFromDatabase();
                }
            
    		}else{
    		    $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You do not have permission to edit other users.", SmartestUserMessage::ACCESS_DENIED);
	        
	    }
		
		$this->formForward();
		
	}
	
	/* public function uploadUserProfilePic(){
	    
	    if($this->getRequestParameter('user_id')){
	        
	        $user = new SmartestSystemUser;
	        
	        if($user->find($this->getRequestParameter('user_id'))){
	            
	            
	            
	        }else{
	            $this->addUserMessageToNextRequest("The user ID was not recognized.", SmartestUserMessage::ERROR);
	        }
	        
	    }
	    
	} */
    
    public function grantUserCurrentSiteAccess(){
        
        if(($this->getRequestParameter('user_id') != $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_permissions')) || ($this->getRequestParameter('user_id') == $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_own_permissions'))){
        
            $user = new SmartestSystemUser;
            
            if($user->find($this->getRequestParameter('user_id'))){
                if($user->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                    $user->addTokenById(21, $this->getSite()->getId());
                }
            }
        
        }
        
        $this->redirect('@users:home');
    }
    
    public function revokeUserCurrentSiteAccess(){
        
        if(($this->getRequestParameter('user_id') != $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_permissions')) || ($this->getRequestParameter('user_id') == $this->getUser()->getId() && $this->getUser()->hasToken('modify_user_own_permissions'))){
        
            $user = new SmartestSystemUser;
            
            if($user->find($this->getRequestParameter('user_id'))){
                if($user->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                    $user->removeTokenById(21, $this->getSite()->getId());
                }
            }
        
        }
        
        $this->redirect('@users:home');
    }
	
	///////////////////////////////// ROLES ////////////////////////////////////
	
	public function listRoles(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('roles');
	    $h = new SmartestUsersHelper;
		$roles = $h->getRoles(false);
		$this->send($roles, 'roles');
		$this->send(count($roles), 'num_roles');
	    
	}
	
	public function addRole(){
	    
	}
	
	public function insertRole($get, $post){
	    
	    $h = new SmartestUsersHelper;
	    
	    if($h->roleNameExists($post['role_label'])){
	        $this->addUserMessage('A role with that name already exists', SmartestUserMessage::WARNING);
	        $this->forward('users', 'addRole');
	    }else if(!strlen($post['role_label'])){
	        $this->addUserMessage('You must enter a name for the role', SmartestUserMessage::WARNING);
	        $this->forward('users', 'addRole');
	    }
	    
	    if(strlen($post['role_label'])){
	        
	        $role = new SmartestRole;
	        $role->setLabel($post['role_label']);
	        $role->save();
	        
	        $this->redirect('/'.$this->getRequest()->getModule().'/editRoleTokens?role_id='.$role->getId());
	        
	    }
	}
	
	public function deleteRole($get){
	    
	    $role = new SmartestRole;
	    
	    if($role->hydrate($get['role_id'])){
	        $role->delete();
	    }
	    
	    $this->formForward();
	    
	}
	
	public function editRoleTokens($get){
	    
	    $this->setFormReturnUri();
	    
    	$role_id = $get['role_id'];	
    	$role = new SmartestRole;
    	
    	if($role->find($role_id)){
    	
    	    $tokens = $role->getTokens();
		    $utokens = $role->getUnusedTokens();
		    
		    // $this->addUserMessage('Editing this role will not affect users created with it. To edit the permission of specific users, select the user and choose the \'Edit user tokens\' option.');
		    
		    $this->send($role, 'role');
		    $this->send($tokens, 'tokens');
		    $this->send($utokens, 'utokens');
		    
	    }else{
	        
	    }
    	
	}

	public function transferTokensToRole($get, $post){
    	
    	$role = new SmartestRole;
    	
    	if($role->find($this->getRequestParameter('role_id'))){
    	    if($this->getRequestParameter('transferAction') == 'add'){
    	        $role->addTokensById($this->getRequestParameter('tokens'));
    		}else{
    		    $role->removeTokensById($this->getRequestParameter('sel_tokens'));
    		}
    	}else{
    	    $this->addUserMessageToNextRequest("The role ID was not recognized.", SmartestUserMessage::ERROR);
    	}
    	
    	$this->formForward();
		
	}
    
    ////////////////////////////// USER's OWN PROFILE ///////////////////////////////
	
	public function editMyProfile(){
	    
	    $this->send($this->getUser(), 'user');
	    $this->send($this->getUser()->hasToken('allow_username_change'), 'allow_username_change');
	    $this->send($this->getUser()->getTwitterHandle(), 'twitter_handle');
        $this->send($this->getUser()->getBioForEditor(), 'bio_text_editor_content');
	    $this->setTitle('Edit your profile');
	    
	}
	
	public function updateMyProfile(){
	    
	    $username = str_replace(' ', '_', $this->getRequestParameter('username'));
	    
	    if($this->getUser()->hasToken('allow_username_change') && strlen($username) > 3 && strlen($username) < 41){
	        if($username != $this->getUser()->getUsername()){
	            $suh = new SmartestUsersHelper;
	            if(!$suh->usernameExists($username, $this->getUser()->getId())){
	                $this->getUser()->setUsername($this->getRequestParameter('username'));
                }
            }
	    }
	    
	    if($this->getUser()->hasToken('allow_username_change') && strlen($this->getRequestParameter('user_firstname')) > 3){
	        $this->getUser()->setFirstName($this->getRequestParameter('user_firstname'));
	    }
	    
	    $this->getUser()->setLastName($this->getRequestParameter('user_lastname'));
        $this->getUser()->setOrganizationName($this->getRequestParameter('user_organization_name'));
	    
	    if(SmartestStringHelper::isEmailAddress($this->getRequestParameter('user_email'))){
	        $this->getUser()->setEmail($this->getRequestParameter('user_email'));
        }
	    
	    $this->getUser()->setTwitterHandle($this->getRequestParameter('user_twitter'));
	    
	    if($this->getRequestParameter('user_website') != 'http://'){
	        $this->getUser()->setWebsite($this->getRequestParameter('user_website'));
        }
        
        if($this->requestParameterIsSet('profile_pic_asset_id') && strlen($this->getRequestParameter('profile_pic_asset_id'))){
            
            $file = new SmartestAsset;
            
            if($file->find($this->getRequestParameter('profile_pic_asset_id'))){
                $this->getUser()->setProfilePicAssetId($file->getId());
            }
            
        }else{
            $this->getUser()->setProfilePicAssetId(null);
        }
        
        $this->getUser()->setPreferredUiLanguage($this->getRequestParameter('user_language'));
        // $this->getUser()->setBio($this->getRequestParameter('user_bio'));
        $this->getUser()->updateBioTextAssetFromEditor($this->getRequestParameter('user_bio'));
	    $this->getUser()->save();
        $this->getUser()->refreshProfilePic();
	    
	    $this->addUserMessageToNextRequest('Your user profile has been updated.', SmartestUserMessage::SUCCESS);
	    $this->redirect('/smartest/profile');
	    
	}
	
	public function setMyPassword(){
	    
	    $this->setTitle('Change your password');
	    
	}
	
	public function updateMyPassword(){
	    
	    if(strlen($this->getRequestParameter('password_1')) < 8){
	        $this->addUserMessage("Your password must be eight or more characters.", SmartestUserMessage::WARNING);
	        $this->forward('users', 'setMyPassword');
	    }else if($this->getRequestParameter('password_1') != $this->getRequestParameter('password_2')){
	        $this->addUserMessage("The passwords you entered didn't match.", SmartestUserMessage::WARNING);
	        $this->forward('users', 'setMyPassword');
        }else if(preg_match('/^p[a4][s5][s5]w[o0]rd$/i', $this->getRequestParameter('password_1'))){
	        $this->addUserMessage("Your password can't be any drivation of the word 'password'. Come on, you can do better than that!", SmartestUserMessage::ERROR);
	        $this->forward('users', 'setMyPassword');
	    }else{
	        $salt = SmartestStringHelper::random(40);
	        
	        if($this->getUser()->setPasswordWithSalt($this->getRequestParameter('password_1'), $salt)){
	            $this->getUser()->setPasswordChangeRequired('0');
	            $this->getUser()->save();
    	        $this->addUserMessageToNextRequest("Your password has been successfully updated.", SmartestUserMessage::SUCCESS);
    	        $this->redirect('/smartest/profile');
    	    }else{
    	        $this->addUserMessage("That password is the same. Please try again.", SmartestUserMessage::WARNING);
    	        $this->forward('users', 'setMyPassword');
    	    }
	    }
	}
    
    //////////////////////////////////// USER GROUPS ////////////////////////////////////
    
    public function listUserGroups(){
        
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('user groups');
        
        $this->send('groups', 'active_tab');
        $uh = new SmartestUsersHelper;
        $this->send(new SmartestArray($uh->getUserGroups($this->getSite()->getId())), 'groups');
        
    }
    
    public function addUserGroup(){
        
        $this->send('Unnamed user group', 'start_name');
        
    }
    
    public function insertUserGroup(){
        
        $g = new SmartestUserGroup;
        $g->setSiteId($this->getSite()->getId());
        $g->setWebId(SmartestStringHelper::random(32));
        $g->setLabel(strip_tags($this->getRequestParameter('user_group_label')));
        $g->setAcceptedUserType($this->getRequestParameter('user_group_mode'));
        $g->setSortField('SM_USERGROUPSORT_LASTNAME');
        $g->save();
        
        $this->redirect('@edit_group?group_id='.$g->getId());
        
    }
    
    public function editUserGroup(){
        
        $g = new SmartestUserGroup;
        
        if($g->find($this->getRequestParameter('group_id'))){
            
            $this->send($g, 'group');
            
            $non_members = $g->getNonMembers();
            $members = $g->getMembers();
            
            $this->send($non_members, 'non_members');
            $this->send($members, 'members');
            
        }
        
    }
    
    public function transferUsers(){
        
        $g = new SmartestUserGroup;
        
        if($g->find($this->getRequestParameter('group_id'))){
            
            if($this->getRequestParameter('transferAction') == 'add'){
                if(is_array($this->getRequestParameter('non_members'))){
                    foreach($this->getRequestParameter('non_members') as $nmid){
                        // echo "Add user ID ".$nmid." to group ID ".$g->getId().'<br />';
                        $g->addUserById($nmid);
                    }
                }
            }elseif($this->getRequestParameter('transferAction') == 'remove'){
                if(is_array($this->getRequestParameter('members'))){
                    // print_r($this->getRequestParameter('members'));
                    foreach($this->getRequestParameter('members') as $mid){
                        // echo "Remove user ID ".$mid." from group ID ".$g->getId().'<br />';
                        $g->removeUserById($mid);
                    }
                }
            }
            
            $this->redirect('@edit_group?group_id='.$g->getId());
            
        }
        
    }
    
    public function deleteUserGroupConfirm(){
        
    }
    
    ///////////// New actions for converting users between 'system' and 'ordinary'
    public function upgradeOrdinaryUserConfig(){
        
        if($this->getUser()->hasToken('modify_user_permissions')){
        
    		$user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
                if($user->getType() == 'SM_USERTYPE_ORDINARY_USER'){
    		        $this->send($user, 'user');
            	    $uhelper = new SmartestUsersHelper;
            	    $roles = $uhelper->getRoles();
                    $this->send($roles, 'roles');
                    $this->send($this->getUser()->hasToken('grant_global_permissions'), 'allow_all_sites');
    		    }else{
                    $this->addUserMessageToNextRequest("The User was the wrong type of user to be upgraded.", SmartestUserMessage::INFO);
                    $this->formForward();
    		    }
            }else{
                $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
                $this->formForward();
            }
        
        }else{
            $this->addUserMessageToNextRequest("You do not have permission to edit other users' permissions.", SmartestUserMessage::ACCESSDENIED);
            $this->formForward();
        }
        
    }
    
    public function upgradeOrdinaryUserAction(){
        
		$user = new SmartestUser;
		
		if($user->find($this->getRequestParameter('user_id'))){
            
            $user->setType('SM_USERTYPE_SYSTEM_USER');
            $user->save();
            $h = new SmartestUsersHelper;
            
    		if(is_numeric($this->getRequestParameter('user_role'))){
        
                // User-created role is being used to assign tokens
                $role = new SmartestRole;
        
                if($role->find($this->getRequestParameter('user_role'))){
                    $tokens = $role->getTokens();
                }else{
                    $tokens = array();
                }
        
                $l = new SmartestManyToManyLookup;
    	        $l->setType('SM_MTMLOOKUP_USER_INITIAL_ROLE');
    	        $l->setEntityForeignKeyValue(1, $user->getId());
    	        $l->setEntityForeignKeyValue(2, $this->getRequestParameter('user_role'));
    	        $l->save();
    
            }else if(substr($this->getRequestParameter('user_role'), 0, 7) == 'system:'){
        
                $role_id = substr($this->getRequestParameter('user_role'), 7);
                $system_roles = $h->getSystemRoles();
        
                if(isset($system_roles[$role_id])){
                    $role = $system_roles[$role_id];
                    $tokens = $role->getTokens();
                }else{
                    $tokens = array();
                }
        
            }else{
        
                $tokens = array();
        
            }
	
    		if($this->getRequestParameter('site_permissions') == 'GLOBAL'){
    		    
                if($this->getUser()->hasToken('grant_global_permissions')){
	        
    		        // Add tokens from role globally
                    $h->applyTokensToUserId($user->getId(), $tokens, 'GLOBAL', false);
    		        /* foreach($tokens as $t){
                        $user->addTokenById($t->getId(), 'GLOBAL');
                    } */
            
    		    }else{
                    
                    $site_id = $this->getSite()->getId();
                    $h->applyTokensToUserId($user->getId(), $tokens, $site_id, false);
                    /* foreach($tokens as $t){
                        $user->addTokenById($t->getId(), $site_id);
                    } */
                    
    		        // $this->addUserMessageToNextRequest('You do not have permission to grant global site access or other tokens');
    		    }
                
		    }else{
                
                $site_id = $this->getSite()->getId();
                $h->applyTokensToUserId($user->getId(), $tokens, $site_id, false);
                
                /* foreach($tokens as $t){
                    $user->addTokenById($t->getId(), $site_id);
                } */
                
		    }
            
            $this->addUserMessageToNextRequest("The user has been successfully upgraded.", SmartestUserMessage::SUCCESS);
            $this->formForward();
            
        }else{
            $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function downgradeSystemUser(){
        
        if($this->getUser()->hasToken('modify_user_permissions')){
            
            $user = new SmartestUser;
		
    		if($user->find($this->getRequestParameter('user_id'))){
                if($user->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                    
                    $all_groups = $user->getGroups('ALL');
                    $system_groups = array();
                    
                    foreach($all_groups as $g){
                        if($g->getAcceptedUserType() == 'SM_USERTYPE_SYSTEM_USER'){
                            $system_groups[] = $g;
                        }
                    }
                    
                    $this->send(new SmartestArray($system_groups), 'system_groups');
    		        $this->send($user, 'user');
                    
    		    }else{
                    $this->addUserMessageToNextRequest("The user was the wrong type of user to be upgraded.", SmartestUserMessage::INFO);
                    $this->formForward();
    		    }
            }else{
                $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
                $this->formForward();
            }
        
        }else{
            $this->addUserMessageToNextRequest("You do not have permission to edit other users' permissions.", SmartestUserMessage::ACCESSDENIED);
            $this->formForward();
        }
        
    }
    
    public function downgradeSystemUserAction(){
        
		$user = new SmartestUser;
		
        if($this->getUser()->hasToken('modify_user_permissions')){
        
    		if($user->find($this->getRequestParameter('user_id'))){
            
                $user->setType('SM_USERTYPE_ORDINARY_USER');
                $user->save();
                $h = new SmartestUsersHelper;
                $h->deleteUserTokensFromUserId($user->getId(), null, true);
                
                $all_groups = $user->getGroups('ALL');
                $num_groups_removed = 0;
                
                foreach($all_groups as $g){
                    if($g->getAcceptedUserType() == 'SM_USERTYPE_SYSTEM_USER'){
                        $g->removeUserById($user->getId());
                        $num_groups_removed++;
                    }
                }
                
                if($num_groups_removed > 0){
                    $this->addUserMessageToNextRequest("The user has been successfully downgraded and removed from ".$num_groups_removed." groups.", SmartestUserMessage::SUCCESS);
                }else{
                    $this->addUserMessageToNextRequest("The user has been successfully downgraded.", SmartestUserMessage::SUCCESS);
                }
            
            }else{
                $this->addUserMessageToNextRequest("The user ID was not recognised.", SmartestUserMessage::ERROR);
            }
            
            $this->formForward();
        
        }else{
            $this->addUserMessageToNextRequest("You do not have permission to edit other users' permissions.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function convertUserToRole(){
        
    }
    
    public function convertUserToRoleAction(){
        
    }
    
    //////////////////////////////////// OTHER FUNCTIONALITY //////////////////////////////////
    
    public function userAssociatedContent(){
        
		$user = new SmartestUser;
		
		if($user->find($this->getRequestParameter('user_id'))){
		    $this->setTitle('Content associated to user: '.$user->getFullName());
		    $this->send($user, 'user');
        }else{
            $this->addUserMessageToNextRequest("The User ID was not recognised.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
		
		$this->send($this->getUser()->hasToken('modify_user_permissions'), 'show_tokens_edit_tab');
        
    }
    
}