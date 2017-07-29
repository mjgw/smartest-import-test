<?php

mb_http_output("UTF-8");
mb_http_input("UTF-8");
mb_internal_encoding("UTF-8");

require SM_ROOT_DIR.'System/Data/SmartestCache.class.php';
require SM_ROOT_DIR.'System/Helpers/SmartestHelper.class.php';
require SM_ROOT_DIR.'System/Base/Exceptions/SmartestException.class.php';
require SM_ROOT_DIR.'System/Base/SmartestError.class.php';
require SM_ROOT_DIR.'System/Base/SmartestErrorStack.class.php';
require SM_ROOT_DIR.'System/Data/SmartestDatabase.class.php';
require SM_ROOT_DIR.'System/Data/SmartestDataUtility.class.php';
require SM_ROOT_DIR.'System/Data/SmartestObject.class.php';

require 'PEAR.php';
require 'XML/Unserializer.php';
require 'XML/Serializer.php';

class SmartestResponse{
    
    // Request object returned by the controller
    private $_request;
    
    // Templating object
    private $_smarty;
    
    // The settings from Configuration/database.ini
	private $_dbconfig;
	
	// Main system configuration data
	private $_configuration;
	
	// The settings from Configuration/database.ini
   	private $_error_stack;
    
    // The database object
	private $database;
	
	// The settings from Configuration/database.ini
   	private $_timing_data;
   	
   	// Authentication object
	private $_authentication;
	
	// An instance of SmartestUserAgentHelper for browser sniffing
	private $_browser;
	
	// Presentation variables defined in build() and used in finish()
	private $_main_template;
	private $_ui_template;
	private $_display_enabled = true;
	
	// Preferences management
	protected $_cached_global_preferences;
	protected $_preferences_helper;
	
	// User messages holder - must be ousite controller object
	public static $user_messages = array();
	
	public function __construct(){
	    
	    $this->_error_stack = new SmartestErrorStack();
        SmartestInfo::$cache_last_mtime = (time() - 259200); // three days ago
	    
	    if(is_file(SM_ROOT_DIR."System/Core/Info/system.yml")){
	        define('SYSTEM_INFO_FILE', SM_ROOT_DIR."System/Core/Info/system.yml");
            SmartestInfo::$system_info_file = SM_ROOT_DIR."System/Core/Info/system.yml";
	    }else if(is_file(SM_ROOT_DIR."System/Core/Info/.system.yml")){
	        define('SYSTEM_INFO_FILE', SM_ROOT_DIR."System/Core/Info/.system.yml");
            SmartestInfo::$system_info_file = SM_ROOT_DIR."System/Core/Info/.system.yml";
	    }
	    
	    try{
            SmartestHelper::loadAll();
        }catch(SmartestException $e){
            $this->error($e->getMessage());
            $this->_error_stack->display();
        }
        
        SmartestDataObjectHelper::loadInterfaces();
        
        SmartestFileSystemHelper::include_group(

        	'System/Response/SmartestUserMessage.class.php',
        	'System/Data/SmartestSession.class.php',
        	'System/Data/SmartestPersistentObject.class.php',
            'System/Data/SmartestMysql.class.php',
        	'System/Data/SmartestSqllite.class.php',
            'System/Data/SmartestGenericListedObject.interface.php',
            'System/Data/SmartestCmsItem.class.php',
            'System/Install/SmartestInstallation.class.php',
            'System/Base/Exceptions/SmartestWebPageBuilderException.class.php',
        	'System/Base/Exceptions/SmartestInterfaceBuilderException.class.php',
        	'System/Base/Exceptions/SmartestRedirectException.class.php',
        	'System/Base/Exceptions/SmartestAuthenticationException.class.php',
        	'System/Base/Exceptions/SmartestAssetCreationException.class.php',
            'System/Base/Exceptions/SmartestOEmbedUrlNotSupportedException.class.php'

        );
        
        // Temporary
        include_once(SM_ROOT_DIR.'Library/SimplePie/autoloader.php');
        include_once(SM_ROOT_DIR.'Library/SimplePie/idn/idna_convert.class.php');
        
        SmartestDataUtility::loadBasicTypes();
        
        $td = new SmartestParameterHolder("Smartest System Response Times");
    	$td->setParameter('start_time', microtime(true));
        
        SmartestFileSystemHelper::include_group(

        	'System/Data/ExtendedTypes/SmartestCmsItemsCollection.class.php',
        	'System/Data/ExtendedTypes/SmartestAssetsCollection.class.php',
        	'System/Data/ExtendedTypes/SmartestTwitterAccountName.class.php',
        	'System/Data/ExtendedTypes/SmartestExternalFeed.class.php',
            'System/Data/ExtendedTypes/SmartestDbStorageParameterHolder.class.php',
        	'System/Data/ExtendedTypes/SmartestEmailAddress.class.php',
            'System/Data/ExtendedTypes/SmartestAssetClassDefinitionsHolder.class.php'

        );
        
        SmartestFileSystemHelper::include_group(
            'System/Response/SmartestLog.class.php',
        	'System/Response/SmartestLogType.class.php'
        );
        
        try{
            
            SmartestInstallationStatusHelper::checkStatus();
            
            // If we get this far, Smartest is installed, so test the database connection
            try{
    	        SmartestDatabase::testConnection('SMARTEST');
    	    }catch(SmartestDatabaseException $e){
                $error_message = $e->getMessage();
                include SM_ROOT_DIR.'System/Response/ErrorPages/database_error.php';
                die;
    	    }
            
	    }catch(SmartestNotInstalledException $e){
	        // If we get here, Smartest isn't installed, so show installer
	        if(!class_exists('SmartestInstaller')){
	            require SM_ROOT_DIR.'System/Install/SmartestInstaller.class.php';
            }
	        require SM_ROOT_DIR.'System/Install/Screens/index.php';
	        exit;
	    }
	    
	    $filename = SmartestFileSystemHelper::include_group(

        	'System/Data/SmartestQuery.class.php',
        	'System/Data/SmartestQueryCondition.class.php',
        	'System/Data/SmartestPseudoItemProperty.class.php',
        	'System/Data/SmartestSortableItemReferenceSet.class.php',
        	'System/Data/SmartestManyToManyQuery.class.php',
        	'System/Data/SmartestObjectModelHelper.class.php',
        	'System/Library/Quince/Quince.class.php',
        	'System/Templating/SmartestEngine.class.php',
        	'System/Templating/SmartestInterfaceBuilder.class.php',
        	'System/Templating/SmartyManager.class.php',
        	'System/Controller/SmartestController.class.php',
        	'System/Templating/SmartestTemplateHelper.class.php',
        	'System/Base/SmartestBaseProcess.class.php',
        	'System/Base/SmartestBaseApplication.class.php',
        	'System/Base/SmartestSystemApplication.class.php',
        	'System/Base/SmartestUserApplication.class.php',
        	'System/Data/SmartestParameterHolderValuePresenceChecker.class.php',
        	'System/Data/SmartestTagPresenceChecker.class.php',
        	'System/Data/SmartestDataObjectHelper.class.php',
        	'System/Data/SmartestRandomNumberGenerator.class.php',
            'System/Data/SmartestTemplateNumberCalculator.class.php',
        	'System/Base/SmartestSiteActions.class.php',
        	'System/Data/SmartestPageRenderingDataRequestHandler.class.php',
        	'System/Data/SmartestPageNavigationDataRequestHandler.class.php',
            'System/Data/SmartestFrontEndSystemInfoQueryService.class.php',
        	'System/Templating/SmartestBasicRenderer.class.php',
        	'System/Templating/SmartestSingleItemTemplateRenderer.class.php',
        	'System/Templating/SmartestWebPageBuilder.class.php',
        	'System/Templating/SmartestUserAppBuilder.class.php',
        	'System/Response/SmartestFilterChain.class.php',
        	'System/Response/SmartestFilter.class.php'

        );
        
        // General system information
    	$sd = SmartestYamlHelper::fastLoad(SmartestInfo::$system_info_file);
    	// Constants need to be phased out as they are slow!!
		// define('SM_INFO_REVISION_NUMBER', $sd['system']['info']['revision']);
		SmartestInfo::$revision = $sd['system']['info']['revision'];
        // define('SM_INFO_VERSION_NUMBER', $sd['system']['info']['version']);
        SmartestInfo::$version = $sd['system']['info']['version'];
        /* define('SM_INFO_BUILD_NUMBER', $sd['system']['info']['build']);
        SmartestInfo::$build = $sd['system']['info']['build']; */
        
        try{
	        
	        // load database connection settings
            $c = SmartestDatabase::readConfiguration('SMARTEST');
            $d = new SmartestDataObjectHelper($c);
            $d->loadBasicObjects();
            $d->loadExtendedObjects();
            
            SmartestFileSystemHelper::include_group(

            	'Library/API/SmartestApplication.class.php',
            	'Library/API/myUser.class.php'

            );
            
	    }catch(SmartestException $e){
    		$this->_error_stack->recordError($e, false);
    	}
    	
    	SmartestPersistentObject::set('timing_data', $td);
        
		// Instantiate browser object
		$this->_browser = new SmartestUserAgentHelper();
		SmartestPersistentObject::set('userAgent', $this->_browser);
    	
    	$this->_error_stack->display();
	    
	}
    
    public function init(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SmartestInfo::$system_info_file);
		
        if(version_compare(PHP_VERSION, $sd['system']['info']['minimum_php_version']) === -1){
            $this->error("This version of PHP is too old to run Smartest. You need to have version ".$sd['system']['info']['minimum_php_version'].' or later.');
        }
        
        try{
    	    $ph = new SmartestPreferencesHelper;
    	    SmartestPersistentObject::set('prefs_helper', $ph);
    	    $this->_cached_global_preferences = new SmartestParameterHolder('Cached global preferences');
  	    }catch(SmartestException $e){
  	        $this->errorFromException($e);
  	    }
        
        $this->_error_stack->display();
        
        if(version_compare(PHP_VERSION, '5.3.0') >= 0){
            if(!ini_get('date.timezone')){
                if($tz = $this->getGlobalPreference('default_timezone')){
                    date_default_timezone_set($tz);
                }else{
                    date_default_timezone_set($sd['system']['info']['default_timezone']);
                    $this->setGlobalPreference('default_timezone', $sd['system']['info']['default_timezone']);
                    SmartestLog::getInstance('system')->log("Default timezone must be set for PHP Version 5.3.0 and later. This value was not found in php.ini. New system preference Was automatically set to ".$sd['system']['info']['default_timezone'].' (taken from system.yml).', SmartestLog::WARNING);
                }
            }
        }
		
		// Now that the default timezone has been set, Logs can be saved into a daily log file
		ini_set('error_log', SM_ROOT_DIR.'System/Logs/php_errors_'.date('Ymd').'.log');
	    SmartestPersistentObject::set('errors:stack', $this->_error_stack);
	    
	    $sh = new SmartestSystemHelper;
        
        SmartestElasticSearchHelper::include_files();
	    
	    try{
	        $sh->checkRequiredExtensionsLoaded();
        }catch(SmartestException $e){
            $this->_error_stack->recordError($e, false);
        }
        
        try{
            SmartestDatabase::handleConfigurationMigration();
		    $sh->checkRequiredFilesExist();
		}catch(SmartestException $e){
		    $this->_error_stack->recordError($e, false);
		}
        
        try{
		    $sh->checkWritablePermissions();
		}catch(SmartestException $e){
		    $this->_error_stack->recordError($e, false);
		}
		
		// instantiate database object
		try{
		    $this->database = SmartestDatabase::getInstance('SMARTEST');
			SmartestPersistentObject::set('db:main', $this->database);
		} catch(SmartestException $e){
		    $this->errorFromException($e);
	    }
	    
	    try{
	        // Instantiate user auth object
		    $this->_authentication = new SmartestAuthenticationHelper();
        }catch(SmartestException $e){
            $this->errorFromException($e);
        }
        
        $this->_error_stack->display();
		
		if($this->_browser->isExplorer() && $this->_browser->getPlatform() == 'Macintosh' && !$this->isWebsitePage()){
		    include(SM_ROOT_DIR.'System/Response/ErrorPages/mac_ie.php');
		    exit();
		}
	    
	}
	
	protected function getUser(){
	    return SmartestSession::get('user');
	}
	
	protected function getUserIdOrZero(){
        if(is_object($this->getUser())){
            return $this->getUser()->getId();
        }else{
            return '0';
        }
    }
    
    protected function getSiteIdOrZero(){
        
        $rh = new SmartestRequestUrlHelper;
        
        if(isset($GLOBALS['_site'])){
            return $GLOBALS['_site']->getId();
        }elseif(is_object(SmartestSession::get('current_open_project')) && !$this->isWebsitePage()){
            return SmartestSession::get('current_open_project')->getId();
        }elseif($this->isWebsitePage() && $site = $rh->getSiteByDomain(SmartestStringHelper::toValidDomain($_SERVER['HTTP_HOST']))){
            return $site->getId();
        }else{
            return '0';
        }
    }
    
    protected function getPreferencesHelper(){
        return SmartestPersistentObject::get('prefs_helper');
    }
	
	protected function getGlobalPreference($preference_name){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        
        if($this->_cached_global_preferences->hasParameter($name)){
            return $this->_cached_global_preferences->getParameter($name);
        }else{
            $value = $this->getPreferencesHelper()->getGlobalPreference($name, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
            $this->_cached_global_preferences->setParameter($name, $value);
            return $value;
        }
        
    }
    
    protected function setGlobalPreference($preference_name, $preference_value){
        
        $name = SmartestStringHelper::toVarName($preference_name);
        return $this->getPreferencesHelper()->setGlobalPreference($name, $preference_value, $this->getUserIdOrZero(), $this->getSiteIdOrZero());
        
    }
	
	public function build(){
	    
        SmartestHelper::loadAllLaterFiles();
        
        // Start Application Controller
	    $this->_controller = new Quince(SM_ROOT_DIR, 'Configuration/quince.yml');
	    
	    try{
	        $this->_controller->prepare();
	    }catch(QuinceException $e){
	        $this->_error_stack->recordError(new SmartestException('Quince error: '.$e->getMessage()), false);
	    }
	    
	    $this->_error_stack->display();
	    
	    SmartestPersistentObject::set('controller', $this->_controller);
	    $this->_controller->getCurrentRequest()->getUserActionObject()->give('_auth', $this->_authentication);
	    
        if($this->isWebsitePage()){
            
            // if compliance mode is on, make starting the session and setting the cookie contingent on having permission to do so
            if(SmartestStringHelper::toRealBool($this->getGlobalPreference('enable_eu_cookie_compliance'))) {
                if(isset($_COOKIE['SMARTEST_COOKIE_CONSENT']) && $_COOKIE['SMARTEST_COOKIE_CONSENT'] == '1'){
                    // extend the life of the cookie
                    setcookie("SMARTEST_COOKIE_CONSENT", '1', time()+90*24*60*60);
                    // start the session
                    SmartestSession::start();
                }
            }else{
                // Otherwise, as EU compliance is not activated, start the session
                SmartestSession::start();
            }
            
        }else if($this->isSystemClass()){
            
            if(is_file(SM_ROOT_DIR.'Configuration/admin_domains.yml')){
                $rd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'Configuration/admin_domains.yml');
                
                if(isset($rd['domains']) && is_array($rd['domains'])){
                    if(!in_array($_SERVER['HTTP_HOST'], $rd['domains'])){
                        SmartestLog::getInstance('auth')->log('Attempted access to Smartest via hostname \''.$_SERVER['HTTP_HOST'].'\' from IP address '.$_SERVER['REMOTE_ADDR'].' was prevented because of backend hostname restrictions in Configuration/admin_domains.yml.');
                        include SM_ROOT_DIR.'System/Response/ErrorPages/admindomainnotpermitted.php';
                        exit;
                    }
                }else{
                    // The file is there, but is incorrectly formatted
                }
            }
            
            // The session is always started for anything where you have to log in to Smartest
            SmartestSession::start();
        }else{
            // It's not a system class or a public web page - so let the developer decide what to do with the session, and do nothing for now.
        }
        
        $this->_error_stack->display();
        
	    try{
	        $this->checkAuthenticationStatus();
        }catch(SmartestAuthenticationException $e){
            $e->lockOut();
            exit;
        }
        
        if($this->isSystemClass() && !$this->isPublicMethod() && !$this->_authentication->getSystemUserIsLoggedIn()){
            // Non-system user trying to access Smartest
            $e = new SmartestAuthenticationException;
            $e->lockOut('unauthorized');
        }
	    
	    $rp = new SmartestParameterHolder("Smartest Controller Information");
	    
	    $metas = new SmartestParameterHolder('Application metadata');
		$metas->loadArray($this->_controller->getCurrentRequest()->getMetas());
		
		$rp->setParameter('action', $this->_controller->getCurrentRequest()->getAction());
		$rp->setParameter('domain', $this->_controller->getCurrentRequest()->getDomain());
        $rp->setParameter('namespace', $this->_controller->getCurrentRequest()->getNamespace());
		$rp->setParameter('request_string', $_SERVER['REQUEST_URI']);
		
		$params = new SmartestParameterHolder('Request parameters');
		$params->loadArray($this->_controller->getCurrentRequest()->getRequestParameters());
		$rp->setParameter('request_parameters', $params);
		
		$module = new SmartestParameterHolder($this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('name', $this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('long_name', $this->_controller->getCurrentRequest()->getMeta('_module_longname'));
		$module->setParameter('identifier', $this->_controller->getCurrentRequest()->getMeta('_module_identifier'));
		$module->setParameter('directory', $this->_controller->getCurrentRequest()->getMeta('_module_dir'));
		$module->setParameter('class', $this->_controller->getCurrentRequest()->getMeta('_module_php_class'));
		$module->setParameter('metas', $metas);
        $module->setParameter('is_system', $this->isSystemClass());
		$rp->setParameter('application', $module);
		
		SmartestPersistentObject::set('request_data', $rp);
	    $h = new SmartestRequestUrlHelper;
	    
        try{
	    
    	    // Make sure site is always looked up
    	    if($this->isWebsitePage()){
		        
                if(!isset($GLOBALS['_site']) || !($GLOBALS['_site'] instanceof SmartestSite)){
                    
        		    if($this->_controller->getCurrentRequest()->getAction() == 'renderEditableDraftPage'){
        		        if($site = $h->getSiteByPageWebId($_GET['page_id'])){
        	                $GLOBALS['_site'] = $site;
        	            }else{
        	                // unknown page id
        	            }
        	        }else{
        	            if($site = $h->getSiteByDomain($_SERVER['HTTP_HOST'])){
        	                $GLOBALS['_site'] = $site;
        	            }else{
        	                // unknown site domain
        	            }
        	        }
                    
                }
	    
    	    }else if($this->isSystemClass() && SmartestSession::hasData('current_open_project')){
    	        // Logged in to Smartest and working with objects in the backend
    		    $GLOBALS['_site'] =& SmartestSession::get('current_open_project');
    		}else if($site = $h->getSiteByDomain($_SERVER['HTTP_HOST'], $this->_controller->getCurrentRequest()->getRequestStringWithVars())){
    		    // Anything else - just look up the domain
    		    $GLOBALS['_site'] =& $site;
    		}
		
		}catch(SmartestRedirectException $e){
            $e->redirect();
        }
        
        if($GLOBALS['_site'] instanceof SmartestSite){
            define('SM_PROTOCOL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://');
            define('SM_SITE_HOST', $GLOBALS['_site']->getDomain());
            define('SM_QUINCE_DOMAIN', $this->_controller->getCurrentRequest()->getDomain());
        }
        
		try{
		    if(isset($GLOBALS['_site']) && is_object($GLOBALS['_site'])){
		        SmartestQuery::init(true, $GLOBALS['_site']->getId());
	        }else{
	            SmartestQuery::init(true);
	        }
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
        
		try{
		    if(SmartestElasticSearchHelper::elasticSearchIsOperational()){
		        SmartestElasticSearchHelper::init($GLOBALS['_site']);
	        }
		}catch(SmartestException $e){
			$this->errorFromException($e);
		}
        
        // Start Smarty
	    if($this->isSystemClass()){
		    $templateLayerContext = 'InterfaceBuilder';
		}else{
		    if(isset($GLOBALS['user_action_has_page']) && $GLOBALS['user_action_has_page'] == true){
		        // echo "web page";
		        $templateLayerContext = 'WebPageBuilder';
	        }else{
	            // echo "not a web page";
	            $templateLayerContext = 'UserAppBuilder';
	        }
		}
		
		$smarty_manager = new SmartyManager($templateLayerContext);
		
		try{
			$this->_smarty = $smarty_manager->initialize();
		} catch(SmartestException $e){
		    $this->_error_stack->recordError($e->getMessage(), false);
		}
		
		$this->_smarty->assign("sm_user_agent_json", $this->_browser->getSimpleClientSideObjectAsJson());
		$this->_smarty->assign("sm_user_agent", $this->_browser);
		
        SmartestPersistentObject::set('presentationLayer', $this->_smarty);
		
        if(is_dir($this->_controller->getCurrentRequest()->getMeta('_module_dir').'Library/')){
	        $existing_include_path = get_include_path();
	        $ip_array = explode(constant('PATH_SEPARATOR'), $existing_include_path);
	        $ip_array[] = $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Library/';
	        $new_include_path = implode(constant('PATH_SEPARATOR'), $ip_array);
            set_include_path($new_include_path);
	    }
        
        $this->_controller->getCurrentRequest()->getUserActionObject()->lock();
	    
	    SmartestHelper::loadApplicationHelpers();
        // Push controller and execute the user action
		try{
		    $this->_controller->dispatch(Quince::CURRENT_URL, false);
		}catch(QuinceException $e){
            $this->errorFromException(new SmartestException('Quince error: '.$e->getMessage()));
		}catch(SmartestException $e){
		    $this->errorFromException($e);
		}
        
        SmartestPersistentObject::get('timing_data')->setParameter('overhead_time', microtime(true));
		
		// Once things like forwarding have calmed down, initialize the templates that are actually going to be used, and make final controller data available for template layer
		
        $this->initializeTemplates();
		
		$metas = new SmartestParameterHolder('Application metadata');
		$metas->loadArray($this->_controller->getCurrentRequest()->getMetas());
		
        $rp->setParameter('action', $this->_controller->getCurrentRequest()->getAction());
        $rp->setParameter('domain', $this->_controller->getCurrentRequest()->getDomain());
        $rp->setParameter('namespace', $this->_controller->getCurrentRequest()->getNamespace());
		
		$params = new SmartestParameterHolder('Request parameters');
		$params->loadArray($this->_controller->getCurrentRequest()->getRequestParameters());
		$rp->setParameter('request_parameters', $params);
		
		$module = new SmartestParameterHolder($this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('name', $this->_controller->getCurrentRequest()->getModule());
		$module->setParameter('long_name', $this->_controller->getCurrentRequest()->getMeta('_module_longname'));
		$module->setParameter('identifier', $this->_controller->getCurrentRequest()->getMeta('_module_identifier'));
		$module->setParameter('directory', $this->_controller->getCurrentRequest()->getMeta('_module_dir'));
		$module->setParameter('class', $this->_controller->getCurrentRequest()->getMeta('_module_php_class'));
		$module->setParameter('metas', $metas);
		$rp->setParameter('application', $module);
		
		SmartestPersistentObject::set('request_data', $rp);
	    
	}
	
	protected function checkAuthenticationStatus(){
	    
        if($this->isSystemClass() && !$this->isPublicMethod()){
		    
		    if(!$this->_authentication->getUserIsLoggedIn()){
				if($this->_controller->getCurrentRequest()->getRequestString() != "smartest/login"){
					
					throw new SmartestAuthenticationException;
					
				}
			}
		}
	}
	
	protected function checkForUpdateScripts(){
	    
	    if($this->isLoggedInRootUser()){
		    
		    // TODO: Run code in here that will redirect the user to views that will carry out urgent updates when they are necessary
		    return null;
		    
		}
	}
	
	public function isLoggedInRootUser(){
	    return (!$this->isPublicMethod() && $this->isSystemClass() && $this->_authentication->getUserIsLoggedIn() && $this->getUser()->hasToken('root_permission'));
	}
	
	public function isPublicMethod(){
	    
	    $sd = SmartestYamlHelper::fastLoad(SmartestInfo::$system_info_file);
		$publicMethodNames = $sd['system']['public_methods'];
		$method = $this->_controller->getCurrentRequest()->getModule().'/'.$this->_controller->getCurrentRequest()->getAction();
		return in_array($method, $publicMethodNames);
	    
	}
	
	public function isWebsitePage(){
	    
        // This function is called even when the controller has not been set up yet, so needs to be able to respond when it doesn't yet know
        if(isset($this->_controller) && is_object($this->_controller)){
    	    $sd = SmartestYamlHelper::fastLoad(SmartestInfo::$system_info_file);
    		$websiteMethodNames = $sd['system']['content_interaction_methods'];
    		$method = $this->_controller->getCurrentRequest()->getModule().'/'.$this->_controller->getCurrentRequest()->getAction();
    		return in_array($method, $websiteMethodNames);
        }else{
            return null;
        }
	    
	}
	
	protected function isSystemClass(){
		
		if($this->_controller->getCurrentRequest()->getMeta('system')){
			
			if(!defined("SM_SYSTEM_IS_BACKEND_MODULE")){
				define("SM_SYSTEM_IS_BACKEND_MODULE", true);
			}
			
			return true;
		}else{
		    
			if(!defined("SM_SYSTEM_IS_BACKEND_MODULE")){
				define("SM_SYSTEM_IS_BACKEND_MODULE", false);
			}
			
			return false;
		}
	}
	
	protected function initializeTemplates(){
		
        if($subfolder = $this->_controller->getCurrentRequest()->getMeta('presentation_subfolder')){
		    if(!SmartestStringHelper::endsWith($subfolder, '/')){
		        $subfolder .= '/';
		    }
		}else{
		    $subfolder = '';
		}
		
		define('SM_CONTROLLER_MODULE_PRES_DIR', $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/');
		$sc = SmartestYamlHelper::fastLoad(SmartestInfo::$system_info_file);
		define('SM_SYSTEM_SYS_TEMPLATES_DIR', $sc['system']['places']['templates_dir']);
		
        if($this->_controller->getCurrentRequest()->hasMeta('presentation')){
            $this->_ui_template = (strlen($this->_controller->getCurrentRequest()->getMeta('presentation'))) ? $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/'.$this->_controller->getCurrentRequest()->getMeta('presentation') : null;
        }else{
            $this->_ui_template = (strlen($this->_controller->getCurrentRequest()->getAction())) ? $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/'.$subfolder.$this->_controller->getCurrentRequest()->getAction().".tpl" : null;
        }
        
        if(is_file($this->_ui_template)){
			
		}else{
		    
		    $this->_smarty->assign("sm_intended_interface", $this->_ui_template);
		    
		    if($this->_controller->getCurrentRequest()->getAction() == "preferences"){
			    $this->_ui_template = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_prefsTemplateNotFound.tpl";
		    }else{
                if($this->isSystemClass()){
		            $this->_ui_template = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_subTemplateNotFound.tpl";
                }else{
                    $this->_ui_template = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_userClassSubTemplateNotFound.tpl";
                }
		    }
			
		}
		
		$this->_smarty->assign("sm_interface", $this->_ui_template);
		
		if($this->_controller->getCurrentRequest()->getMeta('template') == 'none'){
		    $this->_display_enabled = false;
		}else if(!$default_tpl = $this->_controller->getCurrentRequest()->getMeta('template')){
		    $default_tpl = '_default.tpl';
		}
		
		if($this->_controller->getCurrentRequest()->getNamespace() == 'plain'){
		    $this->_main_template = $this->_ui_template;
		}else if($this->_controller->getCurrentRequest()->getNamespace() == 'modal'){
		    if($subfolder == ''){
		        if(is_file($this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/Modals/'.$this->_controller->getCurrentRequest()->getAction().".tpl")){
		            $this->_main_template = $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/Modals/'.$this->_controller->getCurrentRequest()->getAction().".tpl";
		        }else{
		            $this->_main_template = $this->_ui_template;
		        }
		    }else{
		        $this->_main_template = $this->_ui_template;
		    }
		}else{
		    $this->_main_template = $this->_controller->getCurrentRequest()->getMeta('_module_dir').'Presentation/'.$subfolder.$default_tpl;
		}
		
		/* if(!is_file($this->_main_template)){
			$this->_smarty->assign("sm_main_interface", $this->_main_template);
			$this->_main_template = SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."Error/_templateNotFound.tpl";
		} */
		
		// $this->_smarty->assign("template", $this->_main_template);
		$this->_smarty->assign("sm_app_templates_dir", SM_CONTROLLER_MODULE_PRES_DIR);
		$this->_smarty->assign("sm_navigation", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/navigation.tpl");
		$this->_smarty->assign("sm_header", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/header.tpl");
		$this->_smarty->assign("sm_frame", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/frame.tpl");
		$this->_smarty->assign("sm_footer", SM_ROOT_DIR.SM_SYSTEM_SYS_TEMPLATES_DIR."InterfaceBuilder/footer.tpl");
		
	}
	
	public function finish(){
	    
	    // Pass user messages to Smarty
	    if($this->_controller->getCurrentRequest()->getUserActionObject() instanceof SmartestSystemApplication){
		    $this->_smarty->assign('sm_messages', self::$user_messages);
	    }
        
        $cth = 'Content-Type: '.$this->_controller->getCurrentRequest()->getContentType().'; charset='.$this->_controller->getCurrentRequest()->getCharSet();
        header($cth);
	    
        if($this->_display_enabled){
	        echo $this->fetch();
        }
        
        exit;
	    
	}
	
	public function fetch($fragment_only=false){
	    
	    // Last chance to display any errors before trying to render the page
		$this->_error_stack->display();
		
		$output = $this->getUnfilteredOutput($fragment_only);
		$output = $this->executeFilterChain($output);
			
		return $output;
	}
	
	private function getUnfilteredOutput($fragment_only=false){
		
	    try{
	        if($fragment_only){
		        $output = $this->_smarty->fetch($this->_ui_template);
	        }else{
	            $output = $this->_smarty->fetch($this->_main_template);
	        }
        }catch (SmartestException $e){
            $this->errorFromException($e);
        }
		
		return $output;
	}
	
	private function executeFilterChain($html){
        
        if($this->isSystemClass()){
            $filterchain = "InterfaceBuilder";
        }else{
            $filterchain = "ApplicationFilters";
        }
        
        $fc = new SmartestFilterChain($filterchain);
        $html = $fc->execute($html);
	    
	    return $html;
	    
	}
	
	public function error($message="", $type=100){
	    $e = new SmartestException($message, $type);
    	$this->_error_stack->recordError($e, false);
	}
	
	public function errorFromException($e){
        $this->_error_stack->recordError($e, false);
    }
    
}