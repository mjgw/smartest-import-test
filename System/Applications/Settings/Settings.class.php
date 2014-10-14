<?php

/**
 * Contains the Settings module for website
 *
 * PHP versions 5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Marcus Gilroy-Ware <marcus@vsccreative.com>
 */


class Settings extends SmartestSystemApplication{
    
    public function editSite(){
	    
	    if($this->getUser()->hasToken('modify_site_parameters')){
	    
    	    if($this->getSite() instanceof SmartestSite){
		    
    		    $site_id = $this->getSite()->getId();
		    
    		    $main_page_templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/Masters/');
		    
    		    $sitedetails = $this->getSite();
    		    $pages = $this->getSite()->getPagesList();
                $this->send($pages, 'pages');
                
                $site_logos_group = new SmartestAssetGroup;
                if($site_logos_group->find(SmartestSystemSettingHelper::getSiteLogosFileGroupId())){
                    $logos = $site_logos_group->getMembers();
                }else{
                    $logos = array();
                }
                
                $eu_cookie_compliance = $this->getGlobalPreference('enable_eu_cookie_compliance');
                $this->send(SmartestStringHelper::toRealBool($eu_cookie_compliance), 'eu_cookie_compliance');
                
                $site_responsive_mode = $this->getGlobalPreference('enable_site_responsive_mode');
                $this->send(SmartestStringHelper::toRealBool($site_responsive_mode), 'site_responsive_mode');
                
                // if(SmartestStringHelper::toRealBool($site_responsive_mode)){
                    $distinguish_mobiles = $this->getGlobalPreference('site_responsive_distinguish_mobile');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_mobiles), 'responsive_distinguish_mobiles');
                    
                    $distinguish_tablets = $this->getGlobalPreference('site_responsive_distinguish_tablet');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_tablets), 'responsive_distinguish_tablets');
                    
                    $distinguish_old_pcs = $this->getGlobalPreference('site_responsive_distinguish_oldpcs');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_old_pcs), 'responsive_distinguish_old_pcs');
                // }
                
                $this->send($logos, 'logo_assets');
            
                $this->setTitle("Edit Site Settings");
    		    $this->send($sitedetails, 'site');
		    
    	    }else{
	        
    	        $this->addUserMessageToNextRequest('You must have an open site to open edit settings.', SmartestUserMessage::INFO);
    	        $this->redirect('/smartest');
	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permission to edit site settings.', SmartestUserMessage::ACCESS_DENIED);
	        $this->redirect('/smartest');
            
        }
		
	}
	
	public function updateSiteDetails(){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        $site = $this->getSite();
	        
	        if($this->getUser()->hasToken('modify_site_parameters')){
	        
    	        $site->setName($this->getRequestParameter('site_name'));
    	        $site->setInternalLabel($this->getRequestParameter('site_internal_label'));
    	        $site->setTitleFormat($this->getRequestParameter('site_title_format'));
    	        $site->setDomain(SmartestStringHelper::toValidDomain(preg_replace('/^https?:\/\//i', '', $this->getRequestParameter('site_domain'))));
    	        $site->setTagPageId($this->getRequestParameter('site_tag_page'));
    	        $site->setSearchPageId($this->getRequestParameter('site_search_page'));
    	        $site->setErrorPageId($this->getRequestParameter('site_error_page'));
    	        $site->setAdminEmail($this->getRequestParameter('site_admin_email'));
    	        $this->addUserMessageToNextRequest('Your site settings have been updated.', SmartestUserMessage::SUCCESS);
    	        $site->save();
	        
            }else{
                
                $this->addUserMessageToNextRequest('You don\'t have permission to edit site settings', SmartestUserMessage::ACCESS_DENIED);
                
            }
	        
	        $site->setLanguageCode($this->getRequestParameter('site_language'));
            
            $this->setGlobalPreference('enable_eu_cookie_compliance', $this->getRequestParameter('site_eu_cookie_compliance'));
            
            $this->setGlobalPreference('enable_site_responsive_mode', ($this->requestParameterIsSet('site_responsive_mode') ? 1 : 0));
            
            if($this->requestParameterIsSet('site_responsive_mode')){
                $this->setGlobalPreference('site_responsive_distinguish_mobile', ($this->requestParameterIsSet('site_responsive_distinguish_mobile') ? 1 : 0));
                $this->setGlobalPreference('site_responsive_distinguish_tablet', ($this->requestParameterIsSet('site_responsive_distinguish_tablet') ? 1 : 0));
                $this->setGlobalPreference('site_responsive_distinguish_oldpcs', ($this->requestParameterIsSet('site_responsive_distinguish_oldpcs') ? 1 : 0));
            }
	        
	        if($site->getIsEnabled() == '1' && $this->getRequestParameter('site_is_enabled') == '0'){
	            if($this->getUser()->hasToken('disable_site')){
	                $site->setIsEnabled((int) (bool) $this->getRequestParameter('site_is_enabled'));
                }else{
                    $this->addUserMessageToNextRequest('You don\'t have permission to disable sites', SmartestUserMessage::ACCESS_DENIED);
                }
	        }
	        
	        if($site->getIsEnabled() == '0' && $this->getRequestParameter('site_is_enabled') == '1'){
	            if($this->getUser()->hasToken('enable_site')){
	                $site->setIsEnabled((int) (bool) $this->getRequestParameter('site_is_enabled'));
                }else{
                    $this->addUserMessageToNextRequest('You don\'t have permission to enable sites', SmartestUserMessage::ACCESS_DENIED);
                }
	        }
	        
	        if(SmartestUploadHelper::uploadExists('site_logo')){
	            
	            $alh = new SmartestAssetsLibraryHelper;
	            $upload = new SmartestUploadHelper('site_logo');
                $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
                $types = $alh->getPossibleTypesBySuffix($upload->getDotSuffix());
                
                if(count($types)){
                    $t = $types[0]['type']['id'];
                    
                    $ach = new SmartestAssetCreationHelper($t);
                    $ach->createNewAssetFromFileUpload($upload, "Logo for ".$site->getInternalLabel().' - '.date('M d Y'));
                    
                    $file = $ach->finish();
                    $file->setShared(1);
                    $file->setIsSystem(1);
                    $file->setIsHidden(1);
                    $file->save();
                    
                    $site->setLogoImageAssetId($file->getId());
                    $site->save();
                    
                    $site_logos_group = new SmartestAssetGroup;
                    
                    if($site_logos_group->find(SmartestSystemSettingHelper::getSiteLogosFileGroupId())){
                        $site_logos_group->addAssetById($file->getId());
                    }
                }
	        }else{
	            $site->setLogoImageAssetId($this->getRequestParameter('site_logo_image_asset_id'));
	            $site->save();
	        }
	        
	        if($this->getRequestParameter('site_user_page') == 'NEW' && !is_numeric($site->getUserPageId())){
	            $p = new SmartestPage;
	            $p->setTitle('User Profile');
	            $p->setName('user');
	            $p->setSiteId($site->getId());
	            $p->setParent($site->getTopPageId());
        	    $p->setWebid(SmartestStringHelper::random(32));
        	    $p->setCreatedbyUserid($this->getUser()->getId());
        	    $p->setOrderIndex(1020);
        	    $p->save();
        	    $site->setUserPageId($p->getId());
	        }
	        
	        SmartestCache::clear('site_pages_tree_'.$site->getId(), true);
	        
		    $this->formForward();
	    }
	}
    
    public function getPreferencePanels(){
        
        // $c = SmartestPersistentObject::get('controller');
        // print_r($c->getAllModulesById());
        // print_r(SmartestSystemHelper::getSystemApplicationDirectories());
        
    }
    
	/* function startPage(){
		
	}
	
	function checkForUpdates(){
		
		// latest
		$contents = file_get_contents("http://update.visudo.net/smartest");
		$unserializer = new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$latest = $unserializer->getUnserializedData();

		//current
		$contents = file_get_contents("System/CoreInfo/package.xml");
		$unserializer = new XML_Unserializer(); 
		$status = $unserializer->unserialize($contents); 		
		
		if (PEAR::isError($status)) { 
			 die($status->getMessage()); 
		}
		
		$current = $unserializer->getUnserializedData();
		
		$release = false; 
		
		if($latest['release']['version'] > $current['release']['version']){
			$release = $latest;
		}else if($latest['release']['version'] < $current['release']['version']){
			$release = "downgrade";
		}
		
		return (array("release"=>$release,"settings"=>$this->manager->getSettings())); 
	}

	function updateGeneral($get, $post){
    
		$post = array_filter($post, array($this->manager, "filterSubmit"));
		return $this->manager->setSettings($post);
    
	}

	/* function cartSettings(){
		return $this->manager->getSettings();    
	} */
  
    /* function showModels($get){
		
		$user_id = $get['user_id'];
		$sql = "SELECT * FROM ItemClasses WHERE itemclass_userid = '$user_id'";
		$models = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("models" =>$models, "username"=>$username, "itemClassCount"=>count($models));
		
	}

	function showPages($get){

		$user_id = $get['user_id'];
		$sql = "SELECT * FROM Pages WHERE page_createdby_userid = '$user_id'";
		$pages = $this->database->queryToArray($sql);
		$username = $this->database->specificQuery("username", "user_id", $user_id, "Users");
		return array("pages" =>$pages, "username"=>$username, "pageCount"=>count($pages));
	} */

}