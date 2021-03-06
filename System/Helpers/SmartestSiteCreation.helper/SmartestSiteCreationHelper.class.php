<?php

class SmartestSiteCreationHelper{

    public function createNewSite(SmartestParameterHolder $p, $initial_user=''){
        
        if($initial_user instanceof SmartestUser){
            $u = $initial_user;
        }else if(SmartestSession::get('user') instanceof SmartestUser){
            $u = SmartestSession::get('user');
        }else{
            SmartestLog::getInstance('system')->log("Could not create new site without valid user. None given.", SM_LOG_ERROR);
            throw new SmartestException("Tried to create site without logged in user or valid user object");
        }
        
        $ph = new SmartestPreferencesHelper;
        
        $site = new SmartestSite;
        $site->setName($p->getParameter('site_name'));
        $site->setInternalLabel($p->getParameter('site_internal_label', $p->getParameter('site_name')));
        $site->setTitleFormat('$page | $section | $site');
        $site->setDomain($p->getParameter('site_domain'));
        $site->setAdminEmail($p->getParameter('site_admin'));
        $site->setAutomaticUrls('OFF');
	    $site->save();
	    $site->getUniqueId();
	    SmartestLog::getInstance('system')->log("User {$u->__toString()} created a new site record: '{$site->getName()}/{$site->getDomain()}'", SM_LOG_DEBUG);
	    
        $ph->setGlobalPreference('enable_site_responsive_mode', '1', '0', $site->getId());
        $ph->setGlobalPreference('site_responsive_distinguish_mobile', '1', '0', $site->getId());
        $ph->setGlobalPreference('site_responsive_distinguish_tablet', '1', '0', $site->getId());
        $ph->setGlobalPreference('site_responsive_distinguish_oldpcs', '0', '0', $site->getId());
        
        if($p->getParameter('site_master_template') == '_DEFAULT'){
	        $master_template = '';
	    }else if($p->getParameter('site_master_template') == '_BLANK'){
	        
            $colours = array(
                "background: rgb(85,3,131);
    background: linear-gradient(60deg, rgba(85,3,131,1) 0%, rgba(11,1,57,1) 29%, rgba(22,96,145,1) 62%, rgba(30,246,235,1) 100%);",
                "background: rgb(131,58,180);
    background: linear-gradient(45deg, rgba(131,58,180,1) 0%, rgba(253,29,29,1) 50%, rgba(252,176,69,1) 100%);",
                "background: rgb(5,3,42);
    background: linear-gradient(45deg, rgba(5,3,42,1) 0%, rgba(107,1,100,1) 26%, rgba(222,0,122,1) 68%, rgba(255,177,0,1) 100%);",
                "background: rgb(106,169,252);
    background: linear-gradient(135deg, rgba(106,169,252,1) 0%, rgba(227,68,205,1) 34%, rgba(245,78,78,1) 70%, rgba(247,164,35,1) 98%);"
            );
            
            $index = rand(0, 3);
            $colour = $colours[$index];
            
            $ach = new SmartestAssetCreationHelper('SM_ASSETTYPE_STYLESHEET');
            $intended_file_name = SmartestStringHelper::toVarName($p->getParameter('site_name')).'.css';
            $intended_file_path = SM_ROOT_DIR.'Public/Resources/Stylesheets/'.$intended_file_name;
            $css = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Install/Samples/default.css');
            $css = str_replace('%TIME%', date('r'), $css);
            $css = str_replace('%COLOUR%', $colour, $css);
            $actual_file_path = SmartestFileSystemHelper::getUniqueFileName($intended_file_path);
            $actual_file_name = SmartestFileSystemHelper::baseName($actual_file_path);
            
            if(SmartestFileSystemHelper::save($actual_file_path, $css)){
                $css_success = true;
                $ach->createNewAssetFromUnImportedFile($actual_file_name, 'Main CSS file for '.$p->getParameter('site_name'));
                $npg = new SmartestPageGroup;
    	        $npg->setName('main_nav');
    	        $npg->setLabel('Main Navigation');
    	        $npg->setSiteId($site->getId());
    	        $npg->save();
            }else{
                $css_success = false;
            }
            
	        $master_template_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Masters/'.SmartestStringHelper::toVarName($site->getName()).'.tpl'));
	        $master_template_contents = str_replace('%DEFAULTTEMPLATENAME%.tpl', $master_template_name, file_get_contents(SM_ROOT_DIR.'System/Install/Samples/default.tpl'));
	        
            if($css_success){
	            $master_template_contents = str_replace('%CSSLINK%', '<?sm:stylesheet file="'.$actual_file_name.'":?>'."\n", $master_template_contents); 
	        }else{
	            $master_template_contents = str_replace('%CSSLINK%', '', $master_template_contents); 
	        }
            
	        if(file_put_contents(SM_ROOT_DIR.'Presentation/Masters/'.$master_template_name, $master_template_contents)){
	            
	            $master_template = $master_template_name;
	            
	            // Add the template to to the templates database
	            $t = new SmartestTemplateAsset;
	            $t->setUserId($u->getId());
	            $t->setSiteId($site->getId());
	            $t->setStringId(SmartestFileSystemHelper::removeDotSuffix($master_template_name));
	            $t->setUrl($master_template_name);
	            $t->setCreated(time());
	            $t->setWebId(SmartestStringHelper::random(32));
	            $t->setType('SM_ASSETTYPE_MASTER_TEMPLATE');
	            $t->save();
                
        		$container = new SmartestContainer;
		
        		if($container->exists('page_layout')){
        	        $site->setPrimaryContainerId($container->getId());
        	    }
	            
	        }else{
	            $master_template = '';
	            SmartestLog::getInstance('system')->log("Could not create ".SM_ROOT_DIR.'Presentation/Masters/'.$master_template_name.": Permission denied", SM_LOG_WARNING);
	        }
	        
	    }else{
	        if(is_file(SM_ROOT_DIR.'Presentation/Masters/'.$p->getParameter('site_master_template'))){
	            $master_template = $p->getParameter('site_master_template');
	        }else{
	            $master_template = '';
	            SmartestLog::getInstance('system')->log("Could not set ".SM_ROOT_DIR.'Presentation/Masters/'.$p->getParameter('site_master_template')." as master template for new site: File does not exist", SM_LOG_WARNING);
	        }
	    }
    
	    $home_page = new SmartestPage;
	    $home_page->setTitle('Home');
	    $home_page->setName('home');
	    $home_page->setDraftTemplate($master_template);
	    $home_page->setWebid(SmartestStringHelper::random(32));
	    $home_page->setSiteId($site->getId());
	    $home_page->setCreatedbyUserid($u->getId());
	    $home_page->setOrderIndex(0);
	    $home_page->save();
	    $home_page->addAuthorById($u->getId());
	    $site->setTopPageId($home_page->getId());
        if(isset($npg) && $npg instanceof SmartestPageGroup){
            $npg->addPageById($home_page->getId(), false);
        }
	    SmartestLog::getInstance('system')->log("Created home page for new site (page ID {$home_page->getId()})", SM_LOG_DEBUG);
    
	    $error_page = new SmartestPage;
	    $error_page->setTitle('Page not found');
	    $error_page->setName('error-404');
	    $error_page->setSiteId($site->getId());
	    $error_page->setDraftTemplate($master_template);
	    $error_page->setLiveTemplate($master_template);
	    $error_page->setParent($home_page->getId());
	    $error_page->setWebid(SmartestStringHelper::random(32));
	    $error_page->setCreatedbyUserid($u->getId());
	    $error_page->setOrderIndex(1024);
	    $error_page->setIsPublished('TRUE');
        $error_page->setMetaDescription('The page you requested could not be found.');
	    $error_page->save();
	    $site->setErrorPageId($error_page->getId());
	    SmartestLog::getInstance('system')->log("Created and connected 404 page to new site (page ID {$error_page->getId()})", SM_LOG_DEBUG);
        
        $search_page = new SmartestPage;
	    $search_page->setTitle('Search Results');
	    $search_page->setName('search');
	    $search_page->setSiteId($site->getId());
	    $search_page->setDraftTemplate($master_template);
	    $search_page->setLiveTemplate($master_template);
	    $search_page->setParent($home_page->getId());
	    $search_page->setWebid(SmartestStringHelper::random(32));
	    $search_page->setCreatedbyUserid($u->getId());
	    $search_page->setOrderIndex(1022);
	    $search_page->save();
	    $site->setSearchPageId($search_page->getId());
	    SmartestLog::getInstance('system')->log("Created and connected search page to new site (page ID {$search_page->getId()})", SM_LOG_DEBUG);
	    
	    $tag_page = new SmartestPage;
	    $tag_page->setTitle('Tagged Content');
	    $tag_page->setName('tag');
	    $tag_page->setSiteId($site->getId());
	    $tag_page->setDraftTemplate($master_template);
	    $tag_page->setLiveTemplate($master_template);
	    $tag_page->setParent($home_page->getId());
	    $tag_page->setWebid(SmartestStringHelper::random(32));
	    $tag_page->setCreatedbyUserid($u->getId());
	    $tag_page->setOrderIndex(1023);
	    $tag_page->save();
	    $site->setTagPageId($tag_page->getId());
	    SmartestLog::getInstance('system')->log("Created and connected tag page to new site (page ID {$tag_page->getId()})", SM_LOG_DEBUG);
	    
	    $user_page = new SmartestPage;
	    $user_page->setTitle('User Profile');
	    $user_page->setName('user');
	    $user_page->setSiteId($site->getId());
	    $user_page->setDraftTemplate($master_template);
	    $user_page->setLiveTemplate($master_template);
	    $user_page->setParent($home_page->getId());
	    $user_page->setWebid(SmartestStringHelper::random(32));
	    $user_page->setCreatedbyUserid($u->getId());
	    $user_page->setOrderIndex(1020);
	    $user_page->save();
	    $site->setUserPageId($user_page->getId());
	    SmartestLog::getInstance('system')->log("Created and connected user page to new site (page ID {$user_page->getId()})", SM_LOG_DEBUG);
	    
	    $holding_page = new SmartestPage;
	    $holding_page->setTitle('Holding page');
	    $holding_page->setName('error-503');
	    $holding_page->setSiteId($site->getId());
	    $holding_page->setDraftTemplate($master_template);
	    $holding_page->setLiveTemplate($master_template);
	    $holding_page->setParent($home_page->getId());
	    $holding_page->setWebid(SmartestStringHelper::random(32));
	    $holding_page->setCreatedbyUserid($u->getId());
	    $holding_page->setOrderIndex(1019);
	    $holding_page->save();
	    $site->setHoldingPageId($holding_page->getId());
	    SmartestLog::getInstance('system')->log("Created and connected holding page to new site (page ID {$holding_page->getId()})", SM_LOG_DEBUG);
        
	    $site->save();
    
	    self::createSiteDirectory($site);
	
		if(!$u->hasGlobalPermission('site_access')){
		    $u->addToken('site_access', $site->getId());
		}
	
		if(!$u->hasGlobalPermission('modify_user_permissions')){
		    $u->addToken('modify_user_permissions', $site->getId());
		}
		
		return $site;
        
    }
    
    public static function createSiteDirectory(SmartestSite $site){
        
        $site_dir = SM_ROOT_DIR.'Sites/'.substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64).'/';
	    
	    if(is_dir($site_dir)){
	        $site_dir = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Sites/'.substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64).'/');
	    }
	    
	    mkdir($site_dir);
	    
	    if(!is_dir($site_dir.'Presentation')){mkdir($site_dir.'Presentation');}
        if(!is_dir($site_dir.'Presentation/Layouts')){mkdir($site_dir.'Presentation/Layouts');}
        if(!is_dir($site_dir.'Presentation/Special')){mkdir($site_dir.'Presentation/Special');}
	    if(!is_dir($site_dir.'Configuration')){mkdir($site_dir.'Configuration');}
	    if(!is_file($site_dir.'Configuration/site.yml')){file_put_contents($site_dir.'Configuration/site.yml', '');}
	    if(!is_dir($site_dir.'Library')){mkdir($site_dir.'Library');}
	    if(!is_dir($site_dir.'Library/Actions')){mkdir($site_dir.'Library/Actions');}
	    
	    $actions_class_name = SmartestStringHelper::toCamelCase($site->getName()).'Actions';
	    $class_file_contents = file_get_contents(SM_ROOT_DIR.'System/Base/ClassTemplates/SiteActions.class.php.txt');
	    $class_file_contents = str_replace('__TIMESTAMP__', time('Y-m-d h:i:s'), $class_file_contents);
	    if(!is_file($site_dir.'Library/Actions/SiteActions.class.php')){file_put_contents($site_dir.'Library/Actions/SiteActions.class.php', $class_file_contents);}
	    chmod($site_dir.'Library/Actions/SiteActions.class.php', 0666);
	    $site->setDirectoryName(substr(SmartestStringHelper::toCamelCase($site->getName()), 0, 64));
		
		$site->save();
        
    }

}