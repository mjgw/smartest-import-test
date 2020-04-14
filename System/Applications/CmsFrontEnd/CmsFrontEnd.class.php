<?php

class CmsFrontEnd extends SmartestSystemApplication{

	protected $_page;
	
	protected function __smartestApplicationInit(){
	    
	    $this->manager = new SmartestRequestUrlHelper;
	    // define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	    
	}
	
	/* protected function lookupSiteDomain(){
	    
	    try{
	    
		    if($this->_site = $this->manager->getSiteByDomain($_SERVER['HTTP_HOST'], $this->url)){
		
    		    if(is_object($this->_site)){
    		        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
    		        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
    		    }
		    
    		    return true;
		
    	    }
	    
        }catch(SmartestRedirectException $e){
            $e->redirect();
        }
	    
	} */
	
	public function getPage(){
	    return $this->_page;
	}
	
	public function renderPageFromUrl(){
		
		define('SM_AJAX_CALL', false);
        define('SM_DRAFT_MODE', false);
        
        if($this->lookupSiteDomain()){
		    
            define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
            if(strlen($this->getRequest()->getRequestString())){
		        
		        try{
		            
                    if($this->_page = $this->_site->getContentByUrl($this->getRequest()->getRequestString())){
                        $this->renderPage();
                        return true;
                    }else{
            		    $this->renderNotFoundPage();
                        return false;
                    }
            	
        	    }catch(SmartestRedirectException $e){
        	        
        	        $e->redirect();
        	        
        	    }
		        
		    }else{
		        
		        // this is the home page
		        $this->_page = new SmartestPage;
		        $this->_page->find($this->_site->getTopPageId());
		        
		        $this->renderPage();
                return true;
		        
		    }
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
	
	public function renderPageFromId($get){
		
		define('SM_AJAX_CALL', false);
        define('SM_DRAFT_MODE', false);
        
        if($this->lookupSiteDomain()){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
		    
		    // var_dump($this->getRequestParameter('tag'));
		    
		    if($this->getRequestParameter('tag_name') && $this->getRequestParameter('tag')){
		        
		        // Page is a list of tagged content, not a real page.
		        
		        $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag_name'));
        	    $tag = new SmartestTag;
        	    
        	    if($tag->findBy('name', $tag_identifier)){
        	        
                    $tag_page_id = $this->_site->getTagPageId();
        	        
                    $p = new SmartestTagPage;
                    $p->find($tag_page_id);
                    
                    if($this->getRequestParameter('model_specific')){
                        
                        if($this->requestParameterIsSet('model_plural_name')){
                            
                            $varname = SmartestStringHelper::toVarName($this->getRequestParameter('model_plural_name'));
                            $model = new SmartestModel;
                        
                            if($model->findBy('varname', $varname, $this->_site->getId())){
                            
                                $p->assignTag($tag);
                                $p->assignModel($model);
                            
                                $this->_page = $p;
                                $this->renderPage();
                            
                            }else{
                                $this->renderNotFoundPage();
                            }
                            
                        }elseif($this->requestParameterIsSet('model_id') && is_numeric($this->getRequestParameter('model_id'))){
                            
                            $model = new SmartestModel;
                            
                            if($model->find($this->getRequestParameter('model_id'))){
                            
                                $p->assignTag($tag);
                                $p->assignModel($model);
                            
                                $this->_page = $p;
                                $this->renderPage();
                            
                            }else{
                                $this->renderNotFoundPage();
                            }
                            
                        }else{
                            $p->assignTag($tag);
                    
                            $this->_page = $p;
                            $this->renderPage();
                        }
                        
                    }else{
                        
                        $p->assignTag($tag);
                    
                        $this->_page = $p;
                        $this->renderPage();
                        
                    }

        	    }else{
        	        $this->renderNotFoundPage();
        	    }
		        
		    }else{
    		    
    		    $page_webid = $this->getRequestParameter('page_id');
    		    
    		    if($this->_page = $this->manager->getNormalPageByWebId($page_webid, false, $this->_site->getDomain())){
		        
		            // we are viewing a static page
		            $this->renderPage();
		        
		        }else if($this->getRequestParameter('item_id') && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $this->getRequestParameter('item_id'), false, $this->_site->getDomain())){
		        
		            // we are viewing a meta-page (based on an item from a data set)
		            $this->renderPage();
		        
		        }else{
        		
        		    // $this->send($this->renderNotFoundPage(), '_page');
        		    $this->renderNotFoundPage();
        		
        	    }
        	
    	    }
		    
	    }else{
        	    
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;
        	    
        }
        
	}
	
	public function renderEditableDraftPage($get){
		
		define('SM_AJAX_CALL', false);
        define('SM_DRAFT_MODE', true);
		
		define('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN', $this->getUser()->hasToken('edit_containers_in_preview', false));
		
		$page_webid = $this->getRequestParameter('page_id');
		
        $this->send($this->getApplicationPreference('hide_preview_bar', 0), 'sm_hide_preview_bar');
        
		if($this->_site = $this->manager->getSiteByPageWebId($page_webid)){
		    
		    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	        define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        
            if($this->_page = $this->manager->getNormalPageByWebId($page_webid, true)){
                
                if(in_array($this->_page->getId(), $this->_site->getSpecialPageIds()->getParameters())){
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('search_page_id') && $this->getRequestParameter('q')){
                        // Search page
                        $p = $this->_page->copy('SmartestSearchPage');
                        $p->setSearchQuery($this->getRequestParameter('q'));
                        $this->_page = $p;
                    }
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('tag_page_id') && $this->getRequestParameter('tag_name')){
                        
                        // Tag page
                        $p = $this->_page->copy('SmartestTagPage');
                        $t = new SmartestTag;
                        
                        if($this->requestParameterIsSet('model_id') && is_numeric($this->getRequestParameter('model_id'))){
                            
                            if($t->hydrateBy('name', $this->getRequestParameter('tag_name'))){
                            
                                $model = new SmartestModel;

                                if($model->find($this->getRequestParameter('model_id'))){

                                    $p->assignTag($t);
                                    $p->assignModel($model);
                                    $p->setDraftMode(true);

                                    $this->_page = $p;

                                }else{
                                    $this->renderNotFoundPage();
                                }
                            
                            }else{
                                $this->renderNotFoundPage();
                            }

                        }else{
                            
                            if($t->hydrateBy('name', $this->getRequestParameter('tag_name'))){
                                $p->assignTag($t);
                                $p->setDraftMode(true);
                                $this->_page = $p;
                            }
                            
                        }
                        
                    }
                    
                    if($this->_page->getId() == $this->_site->getSpecialPageIds()->g('user_page_id') && $this->getRequestParameter('author_id')){
                        
                        // User page
                        $p = $this->_page->copy('SmartestUserPage');
                        $u = new SmartestUser;
                        
                        if($u->find($this->getRequestParameter('author_id'))){
                            $p->assignUser($u);
                            $this->_page = $p;
                        }
                        
                    }
                    
                    // echo $this->_page->getId();
                    // echo $this->_site->getErrorPageId();
                    
                    if($this->_page->getId() == $this->_site->getErrorPageId()){
                        
                        $this->renderNotFoundPage();
                        
                    }
                    
                }
                
                if($this->getRequestParameter('hide_newwin_link')){
    	            $this->setFormReturnUri();
    	            $this->setFormReturnDescription('page preview');
    	        }
                
    	        $this->_page->setDraftMode(true);
    	        $this->renderPage(true);

    	    }else if($get['item_id'] && $this->_page = $this->manager->getItemClassPageByWebId($page_webid, $get['item_id'], true)){
                
                if($this->getRequestParameter('hide_newwin_link')){
    	            $this->setFormReturnUri();
    	            $this->setFormReturnDescription('page preview');
    	        }
                
    	        $this->_page->setDraftMode(true);
    	        $this->renderPage(true);

    	    }else{

        		$this->renderNotFoundPage();

        	}
		    
		}
		
	}
	
	public function searchDomain(){
	    
	    if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
            
            // search pages and all items
            $search_page_id = $this->_site->getSearchPageId();
	        
            $p = new SmartestSearchPage;
            
            if($p->find($search_page_id)){
                $p->setSearchQuery($this->getRequestParameter('q'));
                $this->_page = $p;
                $this->renderPage();
            }
            
        }else{

            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;

        }
	}
	
	/* public function tagListPage($get){
	    // echo $get['tag_name'];
	    if(is_object($this->_site)){
            
            $tag_page_id = $this->getSite()->getTagPageId();
            // echo $tag_page_id;
            $p = new SmartestPage;
            $p->hydrate($tag_page_id);
            $this->_page = $p;
            // $this->manager->getNormalPageByWebId($page_webid, $this->_site->getId())
            
        }else{
            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;
        }
        
	} */
	
	public function renderSiteTagSimpleRssFeed(){
	    
	    if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
	        
	        $tag_identifier = SmartestStringHelper::toSlug($this->getRequestParameter('tag_name'));
    	    $tag = new SmartestTag;
	    
    	    if($tag->findBy('name', $tag_identifier)){
    	        
    	        $objects = $tag->getObjectsOnSite($this->_site->getId(), true);
    	        
    	        $rss = new SmartestRssOutputHelper($objects);
    	        $rss->setTitle($this->_site->getName()." | ".$tag->getLabel());
                $rss->send();
    	        
    	    }else{
    	        // echo "page not found";
    	        $this->renderNotFoundPage();
    	    }
	    
	    }else{
        	    
        	include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
        	exit;
        	    
        }
	    
	}
    
    public function getDataSetFeed(){
        
        if($this->lookupSiteDomain()){
            
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
            
            $set = new SmartestCmsItemSet;
            if($set->findBy('name', SmartestStringHelper::toVarName($this->getRequestParameter('set_name')))){
                
                if($set->getFeedNonce() == $this->getRequestParameter('nonce')){
                    
                    switch($this->getRequestParameter('format')){
                    
                        case "rss":
                        
                        if($set->getSyndicateAsRSS()){
                            $members = $set->getMembers();
                            $rss = new SmartestRssOutputHelper($members);
            	            $rss->setTitle($this->_site->getName()." | ".$set->getLabel());
                            $rss->setAuthor($set->getFeedAuthor());
                            $rss->setDescription($set->getFeedDescription());
                            $rss->setSite($this->_site);
                            if(is_object($set->getRssChannelImage()) && $set->getRssChannelImageId()){
                                $rss->setImage($set->getRssChannelImage());
                            }
            	            $rss->send();
                        }else{
                            exit;
                        }
                        break;
                    
                        case "atom":
                        if($set->getSyndicateAsAtom()){
                            $members = $set->getMembers();
                            $atom = new SmartestRssOutputHelper($members);
            	            $atom->setTitle($this->_site->getName()." | ".$set->getLabel());
                            $atom->setAuthor($set->getFeedAuthor());
                            $atom->setDescription($set->getFeedDescription());
                            $atom->setSite($this->_site);
            	            $atom->sendAtom();
                        }else{
                            exit;
                        }
                        break;
                    
                        case "itunes":
                        if($set->getSyndicateAsITunes()){
                            $members = $set->getMembers();
                            $itunes = new SmartestRssOutputHelper($members);
            	            $itunes->setTitle($this->_site->getName()." | ".$set->getLabel());
                            $itunes->setAuthor($set->getFeedAuthor());
                            $itunes->setDescription($set->getFeedDescription());
                            $itunes->setSite($this->_site);
            	            $itunes->sendItunes();
                        }else{
                            exit;
                        }
                        break;
                        
                        case "json":
                        if($set->getSyndicateAsJson()){
                            $members = $set->getMembers();
                            $data = new stdClass;
                            $data->title = $this->_site->getName()." | ".$set->getLabel();
                            $data->description = $set->getFeedDescription()->__toString();
                            $data->site = $this->_site->__toSimpleObject();
                            if(is_object($set->getRssChannelImage()) && $set->getRssChannelImageId()){
                                $data->feed_image = $set->getRssChannelImage()->__toSimpleObjectForParentObjectJson();
                            }
                            $data->items = array();
                            foreach($members as $item){
                                $data->items[] = $item->__toSimpleObject();
                            }
                            header('Content-type: application/json');
                            if($this->requestParameterIsSet('pretty') && $this->getRequestParameter('pretty')){
                                echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode($data, JSON_UNESCAPED_SLASHES);
                            }
                            exit;
                        }else{
                            header('Content-type: application/json');
                            echo '{}';
                            exit;
                        }
                        break;
                    
                    }
                    
                }
                
            }else{
                echo "set ".$this->getRequestParameter('set_name')." not found";
                exit;
            }
        
        }
        
    }
	
	public function downloadAsset($get){
	    
	    if($this->lookupSiteDomain()){
	        
	        if($this->_site->getIsEnabled()){
	        
	            $database = SmartestDatabase::getInstance('SMARTEST');
	        
    	        $asset_url = addslashes(urldecode($this->getRequestParameter('url')));
    	        $asset_webid = $this->getRequestParameter('key');
	        
    	        $sql = "SELECT * FROM Assets WHERE (asset_site_id='".$this->_site->getId()."' OR asset_shared='1') AND asset_url='".$asset_url."' AND asset_webid='".$asset_webid."'";
    	        $result = $database->queryToArray($sql);
	        
    	        if(count($result)){
	            
    	            $asset = new SmartestAsset;
    	            $asset->hydrate($result[0]);
	            
    	            if($asset->usesLocalFile()){
        		        $download = new SmartestDownloadHelper($asset->getFullPathOnDisk());
        		    }else{
        		        $download = new SmartestDownloadHelper($asset->getTextFragment()->getContent());
        		        $download->setDownloadFilename($asset->getDownloadableFilename());
        		    }
                
                    $ua = $this->getUserAgent()->getAppName();
                
                    if($this->getRequestParameter('use_file_mime')){
                        $mime_type = $asset->getMimeType();
                    }else{
                		if($ua == 'Internet Explorer' || $ua == 'Opera'){
                		    $mime_type = 'application/octetstream';
                		}else{
                		    $mime_type = 'application/octet-stream';
                		}
            		}
        		
            		$download->setMimeType($mime_type);
            		$download->send();
	            
    	        }else{
    	            // echo "File not found";
    	            $this->renderNotFoundPage(SM_ERROR_FILE_NOT_FOUND);
    	        }
	        
            }else{
                
                /* header("HTTP/1.1 503 Service Unavailable");
                echo "Site not enabled"; */
                
                $this->renderNotAvailablePage();
                
            }
	    
        }else{

            include SM_ROOT_DIR."System/Response/ErrorPages/nosuchdomain.php";
            exit;

        }
        
        exit;
	    
	}
    
    public function renderDynamicStylesheet(){
        
        $asset = new SmartestRenderableAsset;
        
        if($asset->findBy('webid', $this->getRequestParameter('file_id'))){
    		if($this->requestParameterIsSet('draft') && SmartestStringHelper::toRealBool($this->getRequestParameter('draft')) && $this->_auth->getUserIsLoggedIn()){
    		    $draft_mode = true;
    		}else{
    		    $draft_mode = false;
    		}
            
            if($this->requestParameterIsSet('site_id')){
                $site = new SmartestSite;
                if(!$site->find($this->getRequestParameter('site_id'))){
                    $site = $this->_site;
                }
            }else{
                $site = $this->_site;
            }
            
            $raw_scss = $asset->getContent(true);
            $header = '$sm_domain: \''.$this->getRequest()->getDomain()."';\n";
            $header .= '$sm_url_base: \''.$this->getRequest()->getDomain()."';\n";
            
            // Loop through the site's global fields and add them as SCSS variables
            foreach($site->getGlobalFields($draft_mode) as $field_name => $global_field_value){
                if($global_field_value instanceof SmartestRgbColor){
                    $header .= '$field_'.$field_name.': #'.(string) $global_field_value.";\n";
                }else{
                    $header .= '$field_'.$field_name.': "'.(string) $global_field_value."\";\n";
                }
            }
            
            $raw_scss = $header.$raw_scss;
            $scss = new scssc();
            
            try{
                $css = $scss->compile($raw_scss);
            }catch(Exception $e){
                $css = "/** SCSS Error: ".$e->getMessage()." **/";
            }
            
            header('Content-type: text/css');
            echo $css;
            
        }else{
            $this->renderNotFoundPage(SM_ERROR_FILE_NOT_FOUND);
        }
        
        exit;
        
    }
	
	private function renderPage($draft_mode=false){
	    
        if($draft_mode || (is_object($this->_site) && (bool) $this->_site->getIsEnabled()) || ($this->_page->getId() == $this->_site->getHoldingPageId())){
	        
            $ph = new SmartestWebPagePreparationHelper($this->_page);
	    
    	    $overhead_finish_time = microtime(true);
    	    $overhead_time_taken = number_format(($overhead_finish_time - SmartestPersistentObject::get('timing_data')->getParameter('start_time'))*1000, 2, ".", "");
		
    		if($this->_page instanceof SmartestItemPage){
    		    $this->_page->addHit();
    		}
            
            if(!$draft_mode){
                $cron = new SmartestAutomationHelper($this->_site);
                $cron->internalPublicationTrigger();
            }
		
    		define("SM_OVERHEAD_TIME", $overhead_time_taken);
    		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time', microtime(true));
    		SmartestPersistentObject::get('timing_data')->setParameter('overhead_time_taken', $overhead_time_taken);
	    
    	    $html = $ph->fetch($draft_mode);
	        
	        ///// START FILTER CHAIN
    	    $fc = new SmartestFilterChain("WebPageBuilder");
    	    $fc->setDraftMode($draft_mode);
	        $html = $fc->execute($html);
	        
	        header('X-Powered-By: Smartest v'.SmartestInfo::$version.' ('.SmartestInfo::$revision.')');
	        $cth = 'Content-Type: '.$this->getRequest()->getContentType().'; charset='.$this->getRequest()->getCharSet();
	        header($cth);
	        
	        if($this->_page->getLastPublished()){
	            header("Last-Modified: ".date('D, j M Y H:i:s e', $this->_page->getLastPublished())); // Tue, 15 Nov 1994 12:45:26 GMT
	        }
	        
    	    echo $html;
            
            if(!$draft_mode){
                $cron->internalMaintenanceTrigger();
            }
            
	        exit;
	    
        }else{
            
            /* header("HTTP/1.1 503 Service Unavailable");
            echo "Site not enabled";
            exit; */
            
            $this->renderNotAvailablePage();
            
        }
	    
	}
	
	private function renderNotFoundPage(){
	    
        $draft_mode = ($this->getRequest()->getAction() == 'renderEditableDraftPage');
        
        $error_page_id = $this->_site->getErrorPageId();
        
        if($this->_page instanceof SmartestNotFoundPage){
            // Great
        }else{
            if(is_object($this->_page) && $this->_page->getId() == $error_page_id){
                $p = $this->_page->copy('SmartestNotFoundPage');
                $this->_page = $p;
            }else{
                $this->_page = new SmartestNotFoundPage;
                if(!$this->_page->find($error_page_id)){
                    // The page ID specified as the error page no longer seems to exist
                    throw new SmartestException('The page ID specified as the error page no longer seems to exist');
                }
            }
        }
        
        $this->_page->setDraftMode($draft_mode);
        if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
        }
        
        if($draft_mode){
            if($this->getRequestParameter('request')){
                // echo $this->getRequestParameter('request');
                $this->_page->setRequestedUrl($this->getRequestParameter('request'));
            }else{
                // echo $_SERVER['REQUEST_URI'];
                $this->_page->setRequestedUrl(substr($_SERVER['REQUEST_URI'], 1));
            }
        }else{
            $this->_page->setRequestedUrl(substr($_SERVER['REQUEST_URI'], 1));
        }
        
        header("HTTP/1.1 404 Not Found");
        $this->renderPage($draft_mode);
		
	}
    
    protected function renderNotAvailablePage(){
            
        // if($this->lookupSiteDomain()){
	        
            header("HTTP/1.1 503 Service Unavailable");
            
            $draft_mode = ($this->getRequest()->getAction() == 'renderEditableDraftPage');
	        $error_page_id = $this->_site->getHoldingPageId();
            
            if($error_page_id){
    	        
                $this->_page = new SmartestPage;
                
    	        if($this->_page->find($error_page_id) && $this->_page->isPublished()){
    	            $this->_page->setDraftMode($draft_mode);
                    define('SM_CMS_PAGE_SITE_ID', $this->_page->getSiteId());
                    $this->renderPage($draft_mode);
    	        }else{
    	            echo "Site not enabled.";
                    exit;
    	        }
    	        
            }else{
	            echo "Site not enabled.";
                exit;
            }
	        
            /* }else{
            
            // site domain not recognised, so we don't even knkow whether a page with this URL exists or not!
            
        } */
        
    }
    
    public function oEmbedFragment(){
        
        $url = $this->getRequestParameter('url');
        if(SmartestStringHelper::isValidExternalUri($url)){
            $urlobj = new SmartestExternalUrl($url);
            $hostname = $urlobj->getHostName();
            if($hostname == $_SERVER['HTTP_HOST']){
                $s = new SmartestSite;
                if($s->findBy('domain', $hostname)){
                    
                    $this->_site = $s;
                    define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
        		    define('SM_CMS_PAGE_SITE_UNIQUE_ID', $this->_site->getUniqueId());
                    
    		        try{
		            
                        if($this->_page = $this->_site->getContentByUrl($urlobj->getRequestString())){
                            
                            $oembed_data = array();
                            $oembed_data['version'] = '1.0';
                            $oembed_data['provider_name'] = $this->_site->getOrganizationNameOrSiteName();
                            $oembed_data['provider_url'] = $this->_site->getHomepageFullUrl();
                            $oembed_data['width'] = (int) $this->_site->getOEmbedWidth();
                            $oembed_data['height'] = (int) $this->_site->getOEmbedHeight();
                            $oembed_data['lang'] = $this->_site->getLanguageCode();
                            
                            if($this->_page instanceof SmartestItemPage){
                                $item = $this->_page->getPrincipalItem();
                                // echo "Retrieve OEmbed information for ".$item->getModel()->getName().": \"".$this->_page->getTitle().'" from site "'.$this->_site->getName().'"';
                                $oembed_data['type'] = $item->getModel()->getName();
                                $oembed_data['title'] = $item->getName();
                            }else{
                                // echo "Retrieve OEmbed information for static page: \"".$this->_page->getTitle().'" from site "'.$this->_site->getName().'"';
                                $oembed_data['type'] = "Page";
                                $oembed_data['title'] = $this->_page->getTitle();
                            }
                            
                            $oembed_data['html'] = $this->_page->getOembedIFrameMarkup();
                            
                            if($this->getRequestParameter('format', 'json') == 'xml'){
                                $xml = new SimpleXMLElement('<oembed/>');
                                foreach($oembed_data as $key=>$value){
                                    if($key == 'html'){
                                        $xml->addChild($key,'<![CDATA['.$value.']]>');
                                    }else{
                                        $xml->addChild($key,$value);
                                    }
                                }
                                header('Content-type: text/xml');
                                echo $xml->asXML();
                            }else{
                                header('Content-type: application/json');
                                echo json_encode($oembed_data);
                            }
                            
                            
                        }else{
                		    echo "Page not found on this site";
                        }
            	
            	    }catch(SmartestRedirectException $e){
            	        echo "Try again with URL ".$e->getRedirectUrl();
            	    }
                    
                }else{
                    echo "Hostname ".$urlobj->getHostName().' not recognised';
                }
            }else{
                echo "This OEmbed endpoint can only look up URLs starting with the hostname ".$_SERVER['HTTP_HOST'];
            }
        }else{
            echo "URL is invalid";
        }
        exit;
        
    }
    
    public function oEmbedIFrameContent(){
        
        if($this->lookupSiteDomain()){
            if($this->requestParameterIsSet('item_id')){
                if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                    // Item
                    $wph = new SmartestWebPagePreparationHelper;
        	        header('X-Powered-By: Smartest v'.SmartestInfo::$version.' ('.SmartestInfo::$revision.')');
        	        $cth = 'Content-Type: '.$this->getRequest()->getContentType().'; charset='.$this->getRequest()->getCharSet();
        	        header($cth);
                    echo $wph->getCmsItemOEmbedIframeContent($item, $this->getRequestParameter('width'), $this->getRequestParameter('height'));
                }else{
                    // Item not found
                }
            }else{
                if($this->requestParameterIsSet('page_id')){
                    $page = new SmartestPage;
                    if($page->smartFind($this->getRequestParameter('page_id'))){
                        if($page->getTYpe() == 'NORMAL'){
                            $width = 
                            $wph = new SmartestWebPagePreparationHelper;
                	        header('X-Powered-By: Smartest v'.SmartestInfo::$version.' ('.SmartestInfo::$revision.')');
                	        $cth = 'Content-Type: '.$this->getRequest()->getContentType().'; charset='.$this->getRequest()->getCharSet();
                	        header($cth);
                            echo $wph->getStaticPageOEmbedIframeContent($page, $this->getRequestParameter('width'), $this->getRequestParameter('height'));
                        }
                    }else{
                        // Page not found
                    }
                }
            }
        }else{
            // Site domain not recognised
            echo "<p>Site domain not recognised</p>";
        }
        
        exit;
        
    }
    
    public function setAnalyticsIgnoreCookie(){
        
        $v = setcookie('SMARTEST_ANALYTICS_IGNORE', '1', time()+3600*24*21, '/'); // Sets the cookie for three weeks
        header('Location:'.$this->getRequestParameter('returnTo'));
        exit;
        
    }
    
    public function setPrivacyCookieNoJS(){
        
        // TODO: A server-side setting of the SMARTEST_COOKIE_CONSENT cookie
        if($this->lookupSiteDomain()){
            
        }
    }
	
	public function addRating(){
	    
	}
	
	public function submitPageComment(){
	    
	}
	
	public function submitItemComment($get, $post){
	    
    	if($this->lookupSiteDomain()){
	        
	        define('SM_CMS_PAGE_SITE_ID', $this->_site->getId());
	        
    	    $item = new SmartestItem;
	    
    	    if($item->find((int) $post['item_id'])){
	            
	            $content = strip_tags($post['comment_content']);
	            
        	    $item->attachPublicComment($post['comment_author_name'], $post['comment_author_website'], $content);
    	        $item->save(); // this is needed so that the change to item_num_comments is updated
    	        
        	    $this->redirect($item->getUrl());
	    
            }
        
        }
	    
	}
	
	public function buildXmlSitemap(){
	    if($this->lookupSiteDomain()){
	        header('Content-type: application/xml');
	        $this->send($this->_site->getPagesList(false, false, true), 'pages');
	        $this->send($this->_site, 'site');
        }
	}
	
	public function getAuthorProfile(){
	    if($this->lookupSiteDomain()){
	        $u = new SmartestUser;
	        if($u->findBy('username', $this->getRequestParameter('username'))){
	            $p = new SmartestUserPage;
	            if($p->find($this->_site->getUserPageId())){
	                $p->assignUser($u);
	                $this->_page = $p;
	                $this->renderPage();
                }else{
                    // page designated as user page doesn't exist, or no page has been designated
                    $this->renderNotFoundPage();
                }
	        }else{
	            // User not recognised
                if($this->renderPageFromUrl()){
                    
                }else{
                    $this->renderNotFoundPage();
                }
	            
	        }
        }
	}
	
	/* public function imageServer(){
	    
	    $requested_file_name = $this->getRequestParameter('image_file');
	    $requested_format = $this->getRequestParameter('image_file', 'original');
	    
	} */
	
	public function buildRobotsTxtFile(){
	    header('Content-type: text/plain');
	}
	
	public function systemStatusAsXml(){
	    header('Content-type: application/xml');
        // TODO: Make automatic system status information available here as XML
        // TODO: SmartestSystemHelper::getSystemStatus()
	}
  
	public function systemStatusAsJson(){
	    header('Content-type: application/json');
        // TODO: Make automatic system status information available here as JSON
        // TODO: SmartestSystemHelper::getSystemStatus()
	}
    
	public function heartbeatAsJson(){
	    if($this->lookupSiteDomain()){
	        if($this->requestParameterIsSet('site_code')){
                if($this->getRequestParameter('site_code') == $this->_site->getUniqueId()){
                    header('Content-type: application/json');
                    $data = $this->_site->__toSimpleObject();
                    unset($data->id);
                    $data->is_https = false;
                    $data->revision = SmartestInfo::$revision;
                    $data->favicon_ico = '';
                    $data->favicon_png = '';
                    echo json_encode(array('smartest_heartbeat' => array(
                        'verify' => 'S'.substr(md5($this->_site->getUniqueId().$this->_site->getDomain()), 0, 31),
                        'site_data' => $data,
                        'site_feed' => ''
                    )), JSON_PRETTY_PRINT);
                    exit;
                }else{
                    $this->renderNotFoundPage();
                }
            }else{
                $this->renderNotFoundPage();
            }
        }else{
            $this->renderNotFoundPage();
        }
	}
    
    public function requestSyndicationToken(){
        $token = SmartestStringHelper::random(64, SM_RANDOM_ALPHANUMERIC);
        echo $token;
        // TODO
    }
	
	public function getCaptchaImage(){
	    
	    // Include Captcha Library
	    require SM_ROOT_DIR.'Library/Securimage/securimage.php';
	    
	    $img = new securimage();
	    $img->ttf_file        = SM_ROOT_DIR.'Library/Securimage/AHGBold.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        $img->image_height    = (is_numeric($this->getRequestParameter('height')) && $this->getRequestParameter('height') > 60) ? $this->getRequestParameter('height') : 90;                                // width in pixels of the image
        $img->image_width     = (is_numeric($this->getRequestParameter('width')) && $this->getRequestParameter('width') > 60 * M_E) ? $this->getRequestParameter('width') : $img->image_height * M_E;          // a good formula for image size
        //$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        $img->text_color      = new Securimage_Color("#444444");   // captcha text color
        //$img->num_lines       = 8;                                 // how many lines to draw over the image
        //$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
        //$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
        //$img->signature_color = new Securimage_Color(rand(0, 64),
        //                                             rand(64, 128),
        //                                             rand(128, 255));  // random signature color
	    $img->show();
	}
	
}