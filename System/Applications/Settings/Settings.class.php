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
    
	public function startPage(){
		
        $this->setTitle('System settings');
        
	}
    
    public function getPreferencePanels(){
        
        // $c = SmartestPersistentObject::get('controller');
        // print_r($c->getAllModulesById());
        // print_r(SmartestSystemHelper::getSystemApplicationDirectories());
        
    }
    
    public function editSite(){
	    
        $this->requireOpenProject();
        
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
                
                $this->send(!(bool) $this->getSite()->getIsEnabled(), 'site_disabled');
                
                $this->send($this->getSite()->getOrganizationName(), 'site_organisation');
                
                $this->send($logos, 'logo_assets');
            
                $this->setTitle("Edit site settings");
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
	    
        $this->requireOpenProject();
        
	    if($this->getSite() instanceof SmartestSite){
	        
	        $site = $this->getSite();
	        
	        if($this->getUser()->hasToken('modify_site_parameters')){
	        
    	        $site->setName($this->getRequestParameter('site_name'));
    	        $site->setInternalLabel($this->getRequestParameter('site_internal_label'));
    	        $site->setTitleFormat($this->getRequestParameter('site_title_format'));
    	        $site->setDomain(SmartestStringHelper::toValidDomain(preg_replace('/^https?:\/\//i', '', $this->getRequestParameter('site_domain'))));
    	        $site->setAdminEmail($this->getRequestParameter('site_admin_email'));
                $site->setOrganisationName($this->getRequestParameter('site_organisation_name'));
                $site->setLanguageCode($this->getRequestParameter('site_language'));
    	        $this->addUserMessageToNextRequest('Your site settings have been updated.', SmartestUserMessage::SUCCESS);
    	        $site->save();
	        
            }else{
                
                $this->addUserMessageToNextRequest('You don\'t have permission to edit site settings', SmartestUserMessage::ACCESS_DENIED);
                
            }
            
            if($site->getIsEnabled() == '1' && SmartestStringHelper::toRealBool($this->getRequestParameter('site_is_disabled'))){
	            if($this->getUser()->hasToken('disable_site')){
	                $site->setIsEnabled(0);
                }else{
                    $this->addUserMessageToNextRequest('You don\'t have permission to disable sites', SmartestUserMessage::ACCESS_DENIED);
                }
	        }
	        
            if($site->getIsEnabled() == '0' && !SmartestStringHelper::toRealBool($this->getRequestParameter('site_is_disabled'))){
	            if($this->getUser()->hasToken('enable_site')){
	                $site->setIsEnabled(1);
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
	        
	        /* if($this->getRequestParameter('site_user_page') == 'NEW' && !is_numeric($site->getUserPageId())){
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
	        } */
	        
	        SmartestCache::clear('site_pages_tree_'.$site->getId(), true);
	        
            $this->redirect('@site_settings');
            
		    // $this->formForward();
	    }
	}
    
    public function editCmsSettings(){
        
        $this->requireOpenProject();
        
	    if($this->getUser()->hasToken('modify_site_parameters')){
	    
    	    if($this->getSite() instanceof SmartestSite){
		    
    		    $site_id = $this->getSite()->getId();
		    
    		    $main_page_templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/Masters/');
                
                $default_suffix = $this->getGlobalPreference('default_url_suffix', 'html');
                if($default_suffix{0} == '.'){
                    $default_suffix = substr($default_suffix, 1);
                }
                $this->send($default_suffix, 'site_pageurl_default_suffix');
                $this->send(!in_array($default_suffix, array('html', 'php', 'shtml', '_NONE')), 'site_pageurl_default_suffix_custom');
                
                $pmh = new SmartestPageManagementHelper;
                
                $this->send($pmh->getPagePresets($this->getSite()->getId()), 'page_presets');
                
                $default_page_preset_id = $this->getGlobalPreference('site_default_page_preset_id');
                $this->send($default_page_preset_id, 'default_page_preset_id');
                
                $ach = new SmartestAssetClassesHelper;
                $this->send($ach->getContainers(), 'containers');
                $this->send($this->getSite()->getPrimaryContainerId(), 'primary_container_id');
                $this->send($ach->getTextPlaceholders(), 'text_placeholders');
                $this->send($this->getSite()->getPrimaryTextPlaceholderId(), 'primary_text_placeholder_id');
                
                $eu_cookie_compliance = $this->getGlobalPreference('enable_eu_cookie_compliance');
                $this->send(SmartestStringHelper::toRealBool($eu_cookie_compliance), 'eu_cookie_compliance');
                
                $site_responsive_mode = $this->getGlobalPreference('enable_site_responsive_mode');
                $this->send(SmartestStringHelper::toRealBool($site_responsive_mode), 'site_responsive_mode');
                
                $override_eu_cookie_compliance_ga = $this->getGlobalPreference('override_eu_cookie_compliance_ga', 1);
                $this->send(SmartestStringHelper::toRealBool($override_eu_cookie_compliance_ga), 'override_eu_cookie_compliance_ga');
                
                $ga_id = $this->getGlobalPreference('google_analytics_id');
                $this->send($ga_id, 'site_ga_id');
                
                $this->send(!(bool) $this->getSite()->getIsEnabled(), 'site_disabled');
                
                // if(SmartestStringHelper::toRealBool($site_responsive_mode)){
                    $distinguish_mobiles = $this->getGlobalPreference('site_responsive_distinguish_mobile');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_mobiles), 'responsive_distinguish_mobiles');
                    
                    $distinguish_tablets = $this->getGlobalPreference('site_responsive_distinguish_tablet');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_tablets), 'responsive_distinguish_tablets');
                    
                    $distinguish_old_pcs = $this->getGlobalPreference('site_responsive_distinguish_oldpcs');
                    $this->send(SmartestStringHelper::toRealBool($distinguish_old_pcs), 'responsive_distinguish_old_pcs');
                // }
                
                $this->setTitle("Edit CMS settings");
                
            }
            
        }else{
            
            $this->formForward();
            
        }
        
    }
    
    public function updateCmsSettings(){
        
        $this->requireOpenProject();
        
	    if($this->getUser()->hasToken('modify_site_parameters')){
	    
    	    if($this->getSite() instanceof SmartestSite){
		    
    		    $site_id = $this->getSite()->getId();
                $site = $this->getSite();
		    
    		    $main_page_templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/Masters/');
                
                if(is_numeric($this->getRequestParameter('site_default_page_preset_id'))){
                    $this->setGlobalPreference('site_default_page_preset_id', $this->getRequestParameter('site_default_page_preset_id'));
                }else{
                    $this->setGlobalPreference('site_default_page_preset_id', '0');
                }
                
                $suff = $this->getRequestParameter('site_default_url_suffix');
                
                if(is_numeric($this->getRequestParameter('site_default_container_id'))){
                    $site->setPrimaryContainerId($this->getRequestParameter('site_default_container_id'));
                }else{
                    $site->setPrimaryContainerId(0);
                }
                
                if(is_numeric($this->getRequestParameter('site_default_text_placeholder_id'))){
                    $site->setPrimaryTextPlaceholderId($this->getRequestParameter('site_default_text_placeholder_id'));
                }else{
                    $site->setPrimaryTextPlaceholderId(0);
                }
                
                if($suff == '_CUSTOM'){
                    $custom_suffix = $this->getRequestParameter('site_default_url_suffix_custom');
                    if($custom_suffix{0} == '.'){
                        $custom_suffix = substr($custom_suffix, 1);
                    }
                    $this->setGlobalPreference('default_url_suffix', $custom_suffix);
                }else{
                    $this->setGlobalPreference('default_url_suffix', $suff);
                }
                
                if($this->requestParameterIsSet('site_responsive_mode')){
                    $this->setGlobalPreference('site_responsive_distinguish_mobile', ($this->requestParameterIsSet('site_responsive_distinguish_mobile') ? 1 : 0));
                    $this->setGlobalPreference('site_responsive_distinguish_tablet', ($this->requestParameterIsSet('site_responsive_distinguish_tablet') ? 1 : 0));
                    $this->setGlobalPreference('site_responsive_distinguish_oldpcs', ($this->requestParameterIsSet('site_responsive_distinguish_oldpcs') ? 1 : 0));
                }
                
                $this->setGlobalPreference('google_analytics_id', $this->getRequestParameter('site_ga_id'));
                $this->setGlobalPreference('enable_eu_cookie_compliance', $this->getRequestParameter('site_eu_cookie_compliance'));
                $this->setGlobalPreference('enable_site_responsive_mode', SmartestStringHelper::toRealBool($this->getRequestParameter('site_responsive_mode')) ? 1 : 0);
    	        $this->setGlobalPreference('override_eu_cookie_compliance_ga', SmartestStringHelper::toRealBool($this->getRequestParameter('site_override_eu_cookie_compliance_ga')) ? 1 : 0);
                
                $this->addUserMessageToNextRequest('Your CMS settings have been updated.', SmartestUserMessage::SUCCESS);
                
                $site->save();
                
            }
            
            $this->redirect('@cms_settings');
            
        }else{
            
            $this->formForward();
            
        }
        
    }
    
    public function editSiteSpecialPages(){
        
        $this->requireOpenProject();
        
	    if($this->getSite() instanceof SmartestSite){
	        
	        $site = $this->getSite();
            $pages = $this->getSite()->getPagesListWithSpecialPages(true);
            $this->send($pages, 'pages');
            $this->setTitle("Reconfigure site special pages");
		    $this->send($this->getSite(), 'site');
            
        }
        
    }
    
    public function updateSiteSpecialPages(){
        
        $this->requireOpenProject();
        
	    if($this->getSite() instanceof SmartestSite){
	        
	        $site = $this->getSite();
        
            if($this->getUser()->hasToken('modify_site_parameters')){
        
    	        $site->setTagPageId($this->getRequestParameter('site_tag_page'));
    	        $site->setSearchPageId($this->getRequestParameter('site_search_page'));
    	        $site->setErrorPageId($this->getRequestParameter('site_error_page'));
                
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
    	        }else if(is_numeric($this->getRequestParameter('site_user_page'))){
    	            $site->setUserPageId($this->getRequestParameter('site_user_page'));
    	        }
                
    	        if($this->getRequestParameter('site_holding_page') == 'NEW' && !is_numeric($site->getHoldingPageId())){
    	            $p = new SmartestPage;
    	            $p->setTitle('Holding page');
    	            $p->setName('error-503');
    	            $p->setSiteId($site->getId());
    	            $p->setParent($site->getTopPageId());
            	    $p->setWebid(SmartestStringHelper::random(32));
            	    $p->setCreatedbyUserid($this->getUser()->getId());
            	    $p->setOrderIndex(1019);
            	    $p->save();
            	    $site->setHoldingPageId($p->getId());
    	        }else if(is_numeric($this->getRequestParameter('site_holding_page'))){
    	            $site->setHoldingPageId($this->getRequestParameter('site_holding_page'));
    	        }
    	        
                $this->addUserMessageToNextRequest('Your site settings have been updated.', SmartestUserMessage::SUCCESS);
    	        $site->save();
                
    	        SmartestCache::clear('site_pages_tree_'.$site->getId(), true);
	        
    		    $this->formForward();
        
            }else{
            
                $this->addUserMessageToNextRequest('You don\'t have permission to edit site settings', SmartestUserMessage::ACCESS_DENIED);
            
            }
        
        }
        
    }
    
    public function editCustomizationSettings(){
        
        
        
    }
    
    public function updateCustomizationSettings(){
        
        
        
    }
    
    public function listTags(){
	    
	    $this->setFormReturnUri();
        $this->setFormReturnDescription('tags list');
        
	    $du = new SmartestDataUtility;
	    $tags = $du->getTagsAsArrays();
	    $this->send($tags, 'tags');
        $this->send($this->getUser()->hasToken('delete_tags'), 'allow_delete_tags');
        $this->send($this->getUser()->hasToken('edit_tags'), 'allow_edit_tags');
	    
	}
    
	public function addTag(){
	    
	    if(is_numeric($this->getRequestParameter('item_id'))){
	        $item = new SmartestItem;
	        if($item->find($this->getRequestParameter('item_id'))){
	            $this->send($item, 'item');
	            if($this->getRequestParameter('page_webid')){
	                $this->send($this->getRequestParameter('page_webid'), 'page_webid');
	            }
	        }
	    }
	    
	    if(is_numeric($this->getRequestParameter('page_id'))){
	        $page = new SmartestPage;
	        if($page->find($this->getRequestParameter('page_id'))){
	            $this->send($page, 'page');
	        }
	    }
	    
	    if(is_numeric($this->getRequestParameter('asset_id'))){
	        $asset = new SmartestAsset;
	        if($asset->find($this->getRequestParameter('asset_id'))){
	            $this->send($asset, 'asset');
	        }
	    }
	    
	}
	
	public function insertTag(){
	    
	    $proposed_tags = SmartestStringHelper::fromSeparatedStringList($this->getRequestParameter('tag_label')); // Separates by commas or semicolons
	    
	    $num_new_tags = 0;
	    $tag_item = false;
	    
	    if($this->getRequestParameter('tag_item') && is_numeric($this->getRequestParameter('item_id'))){
	        $item = new SmartestItem;
	        if($item->find($this->getRequestParameter('item_id'))){
	            $tag_item = true;
	        }
	    }
	    
	    if($this->getRequestParameter('tag_page') && is_numeric($this->getRequestParameter('page_id'))){
	        $page = new SmartestPage;
	        if($page->find($this->getRequestParameter('page_id'))){
	            $tag_page = true;
	        }
	    }
	    
	    if($this->getRequestParameter('tag_asset') && is_numeric($this->getRequestParameter('asset_id'))){
	        $asset = new SmartestAsset;
	        if($asset->find($this->getRequestParameter('asset_id'))){
	            $tag_asset = true;
	        }
	    }
	    
	    foreach($proposed_tags as $tag_label){
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_label, true);
	        
	        if(strlen($tag_label) && strlen($tag_name)){
	        
        	    $tag = new SmartestTag;
        	    $existing_tags = array();
	    
        	    if($tag->hydrateBy('name', $tag_name)){
        	        // $this->addUserMessageToNextRequest("A tag with that name already exists.", SmartestUserMessage::WARNING);
        	        $existing_tags[] = "'".$tag_label."'";
        	    }else{
        	        $tag->setName($tag_name);
        	        $tag->setLabel(SmartestStringHelper::toTitleCase($tag_label)); // Capitalises first letter of words for neatness
        	        $tag->save();
        	        
        	        if($tag_item){
        	            $item->tag($tag->getId());
        	        }
        	        if($tag_page){
        	            $page->tag($tag->getId());
        	        }
        	        if($tag_asset){
        	            $asset->tag($tag->getId());
        	        }
        	        $num_new_tags++;
        	    }
    	    
	        }
	    
        }
        
        $message = $num_new_tags.' tag successfully added.';
        
        if(count($existing_tags)){
            $message .= ' Tags '.SmartestStringHelper::toCommaSeparatedList($existing_tags).' already existed.';
            $type = SmartestUserMessage::INFO;
        }else{
            $type = SmartestUserMessage::SUCCESS;
        }
        
        $this->addUserMessageToNextRequest($message, $type);
        
        if($tag_item){
            $url = '/datamanager/itemTags?item_id='.$item->getId();
            if($this->getRequestParameter('page_webid')){
                $url .= '&page_id='.$this->getRequestParameter('page_webid');
            }
            $this->redirect($url);
        }
        
        if($tag_page){
            // $page->tag($tag->getId());
            $this->redirect('/websitemanager/pageTags?page_id='.$page->getWebId());
        }
        
        if($tag_asset){
            // $asset->tag($tag->getId());
            $this->redirect('/assets/assetTags?asset_id='.$asset->getId());
        }
        
        $this->formForward();
	    
	}
    
    public function editTag(){
        
        $tag = new SmartestTag;
        
        if($this->getUser()->hasToken('edit_tags')){
            if($tag->find($this->getRequestParameter('tag_id'))){
                
                $this->send($tag, 'tag');
                $this->setTitle('Edit tag: '.$tag->getLabel());
                $this->send($tag->getDescriptionTextAssetForEditor(), 'desc_text_editor_content');
                
                if($this->requestParameterIsSet('page_id')){
                    
                    $page = new SmartestTagPage;
                    
                    if($page->smartFind($this->getRequestParameter('page_id'))){
                        if($page->getId() == $this->getSite()->getTagPageId()){
                            $this->send(true, 'show_edit_tabs');
                            $this->send($page, 'page');
                            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
                            $this->send($this->getUser()->hasToken('edit_tags'), 'allow_tag_edit');
                        }else{
                            echo "not tag page";
                        }
                    }else{
                        echo "page not found";
                    }
                    
                }else{
                    echo "no page ID";
                }
                
            }
        }
        
    }
    
    public function updateTag(){
        
        $tag = new SmartestTag;
        
        if($this->getUser()->hasToken('edit_tags')){
            if($tag->find($this->getRequestParameter('tag_id'))){
                
                $tag->setLabel(strip_tags($this->getRequestParameter('tag_label')));
                $tag->setName(SmartestStringHelper::toSlug($this->getRequestParameter('tag_name')));
                $tag->setIconImageAssetId($this->getRequestParameter('tag_icon_image'));
                
                $tag->updateDescriptionTextAssetFromEditor($this->getRequestParameter('tag_description'));
                
                $tag->save();
                
                $this->addUserMessageToNextRequest('The tag has been updated.', SmartestUserMessage::SUCCESS);
                
            }
        }else{
            $this->addUserMessageToNextRequest('YOu do not have permission to modify tags.', SmartestUserMessage::ACCESS_DENIED);
        }
        
        $this->handleSaveAction();
        
    }
	
	public function getTaggedObjects(){
	    
	    $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag'));
	    $tag = new SmartestTag;
	    
	    if($tag->findBy('name', $tag_identifier)){
	        $this->send($tag, 'tag');
	        // $objects = $tag->getObjectsOnSite($this->getSite()->getId(), true);
	        $this->send(new SmartestArray($tag->getSimpleItems($this->getSite()->getId(), true)), 'items');
	        $this->send(new SmartestArray($tag->getPages($this->getSite()->getId())), 'pages');
	        $this->send(new SmartestArray($tag->getAssets($this->getSite()->getId())), 'assets');
            $this->send(new SmartestArray($tag->getUsers($this->getSite()->getId())), 'users');
            // echo count($tag->getUsers($this->getSite()->getId()));
	    }else{
	        $objects = array();
	        $this->addUserMessage("This tag does not exist.", SmartestUserMessage::WARNING);
	    }
	    
	}
	
	/* function checkForUpdates(){
		
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