<?php

class SmartestWebPagePreparationHelper{
    
    protected $_page;
    protected $database;
    // protected $_controller;
    protected $_request;
    
    public function __construct($page=null){
        
        // $this->_controller = SmartestPersistentObject::get('controller');
        // $this->_request_data = SmartestPersistentObject::get('request_data');
        $this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
        
        if($page instanceof SmartestPage){
            $this->_page = $page;
        }
        
        $this->database = SmartestPersistentObject::get('db:main');
        
    }
    
    public function createBuilder(){
        
        $m = new SmartyManager("WebPageBuilder");
        $wpb = $m->initialize();
        return $wpb;
        
    }
    
    public function fetch($draft_mode=false){
        
        if($this->cachedPagesAllowed()){	
			return SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName(), true);
		}else{
			return $this->build($draft_mode);
		}
        
    }
    
    public function fetchContainer($container_name, $draft_mode=false){
        return $this->buildFromContainerDownwards($container_name, $draft_mode);
        
    }
    
    public function cachedPagesAllowed(){
        
        if(!$this->_page instanceof SmartestPage){
            throw new SmartestException("Supplied data is not a valid SmartestPage object.");
        }
        
        return (file_exists(SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName()) && $this->_page->getCacheAsHtml() == "TRUE" && $this->_page->getDraftMode() == false && ($this->_request->getAction() == 'renderPageFromId' || $this->_request->getAction() == 'renderPageFromUrl'));
        
    }
    
    public function build($draft_mode=null){
        
        if(!$this->_page instanceof SmartestPage){
            throw new SmartestException("Supplied data is not a valid SmartestPage object.");
        }
        
        $b = $this->createBuilder();
        
        $b->assign('domain', $this->_request->getDomain());
        $b->assign('method', $this->_request->getAction());
        $b->assign('section', $this->_request->getModule());
        $b->assign('is_ajax', defined('SM_AJAX_CALL') ? constant('SM_AJAX_CALL') : false);
        $b->assign('now', new SmartestDateTime(time()));
        
        if($ua = SmartestPersistentObject::get('userAgent')){
            $b->assign('sm_user_agent_json', $ua->getSimpleClientSideObjectAsJson());
            $b->assign('sm_user_agent', $ua);
        }
        
        if($draft_mode === true || $draft_mode === false){
            $b->setDraftMode($draft_mode);
        }else{
            $draft_mode = $this->_page->getDraftMode();
        }
        
        $b->assign('sm_draft_mode', $draft_mode);
        $b->assign('sm_draft_mode_obj', new SmartestBoolean($draft_mode));
        
        $content = $b->renderPage($this->_page, $draft_mode);
        
        if($this->_page->getCacheAsHtml() == "TRUE" && !$draft_mode){
        
            $filename = SM_ROOT_DIR.'System/Cache/Pages/'.$this->_page->getCacheFileName();
		
		    if(!SmartestFileSystemHelper::save($filename, $content, true)){
			    SmartestLog::getInstance('system')->log("Page cache failed to build for page: ".$this->_page->getTitle().'. Could not write file to '.$filename);
		    }
		
	    }
		
		return $content;
        
    }
    
    public function buildFromContainerDownwards($container_name, $draft_mode=''){
        
        if(!$this->_page instanceof SmartestPage){
            throw new SmartestException("Supplied data is not a valid SmartestPage object.");
        }
        
        $container_name = SmartestStringHelper::toVarName($container_name);
        
        $b = $this->createBuilder();
        
        $b->assign('domain', $this->_request->getDomain());
        $b->assign('method', $this->_request->getAction());
        $b->assign('section', $this->_request->getModule());
        $b->assign('is_ajax', defined('SM_AJAX_CALL') ? constant('SM_AJAX_CALL') : false);
        $b->assign('now', new SmartestDateTime(time()));
        $b->assignPage($this->_page);
        
        if($ua = SmartestPersistentObject::get('userAgent')){
            $b->assign('sm_user_agent_json', $ua->getSimpleClientSideObjectAsJson());
            $b->assign('sm_user_agent', $ua->__toArray());
        }
        
        if($draft_mode === true || $draft_mode === false){
            $b->setDraftMode($draft_mode);
        }else{
            $draft_mode = $this->_page->getDraftMode();
        }
        
        $b->prepareForRender();
        
        if($this->_page->hasContainerDefinition($container_name)){
            
            if($container_def = $this->_page->getContainerDefinition($container_name)){
                
                ob_start();
                $b->run($container_def->getTemplateFilePath(), array());
                $content = ob_get_contents();
                ob_end_clean();
            
                return $content;
                
            }else{
                echo 'container '.$container_name.' is not defined';
            }
            
        }else{
            
            if($draft_mode){
                header('HTTP/1.1 500 Internal Server Error');
                echo "<p>Smartest: Container '".$container_name."' not yet defined</p>";
            }   
            exit;
            
        }
        
    }
    
    public function getStaticPageOEmbedIframeContent(SmartestPage $page, $width=420, $height=140){
        
        $site = $page->getSite();
        $request_data = SmartestPersistentObject::get('request_data');
        
        if(is_file($site->getFullDirectoryPath().'Presentation/Special/oembed_page.tpl')){
            $oembed_template = $site->getFullDirectoryPath().'Presentation/Special/oembed_page.tpl';
        }elseif(is_file(SM_ROOT_DIR.'Presentation/Special/oembed_page.tpl')){
            $oembed_template = SM_ROOT_DIR.'Presentation/Special/oembed_page.tpl';
        }else{
            $oembed_template = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/oembed_page.tpl';
        }
        
        $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize('oembed_page');
        echo $r->_smarty_include(array('smarty_include_tpl_file'=>$oembed_template, 'smarty_include_vars'=>array(
            'page' => $page,
            'site' => $site,
            'request' => $request_data,
            'width'=> $width-4,
            'height'=> $height-4
        )));
    }
    
    public function getCmsItemOEmbedIframeContent(SmartestCmsItem $item, $width=420, $height=140){
        
        $site = $item->getSite();
        $model = $item->getModel();
        $request_data = SmartestPersistentObject::get('request_data');
        $model_varname = SmartestStringHelper::toVarName($model->getName());
        
        if(is_file($site->getFullDirectoryPath().'Presentation/Special/oembed_'.$model_varname.'.tpl')){
            $oembed_template = $site->getFullDirectoryPath().'Presentation/Special/oembed_'.$model_varname.'.tpl';
        }elseif(is_file($site->getFullDirectoryPath().'Presentation/Special/oembed_item.tpl')){
            $oembed_template = $site->getFullDirectoryPath().'Presentation/Special/oembed_item.tpl';
        }elseif(is_file(SM_ROOT_DIR.'Presentation/Special/oembed_'.$model_varname.'.tpl')){
            $oembed_template = SM_ROOT_DIR.'Presentation/Special/oembed_'.$model_varname.'.tpl';
        }elseif(is_file(SM_ROOT_DIR.'Presentation/Special/oembed_item.tpl')){
            $oembed_template = SM_ROOT_DIR.'Presentation/Special/oembed_item.tpl';
        }else{
            $oembed_template = SM_ROOT_DIR.'System/Presentation/WebPageBuilder/oembed_item.tpl';
        }
        
	    $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize('oembed_item');
        echo $r->_smarty_include(array('smarty_include_tpl_file'=>$oembed_template, 'smarty_include_vars'=>array(
            'item' => $item,
            'model' => $model,
            'site' => $site,
            'request' => $request_data,
            'width'=> $width-4,
            'height'=> $height-4
        )));
        
    }
    
}