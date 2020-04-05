<?php

class SmartestWebPageBuilder extends SmartestBasicRenderer{
    
    protected $templateHelper;
	protected $page;
	protected $_page_rendering_data = array();
	protected $_page_rendering_data_retrieved = false;
	protected $_items = array();
	protected $_single_item_template_path;
    
	public $_RSS_parse_res; // For RSS printouts
	
	public function __construct($pid){
	    
	    parent::__construct($pid);
	    
	    $this->_context = SM_CONTEXT_CONTENT_PAGE;
	    
	    if(!SmartestPersistentObject::get('template_layer_data:sets')){
		    SmartestPersistentObject::set('template_layer_data:sets', new SmartestParameterHolder("Template Layer Datasets"));
		}
		
		if(!SmartestPersistentObject::get('template_layer_data:items')){
		    SmartestPersistentObject::set('template_layer_data:items', new SmartestParameterHolder("Template Layer Items"));
		}
		
		if(!defined('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN')){
		    define('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN', true);
		}
		
		if(!defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS')){
		    define('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS', true);
		}
		
		$this->caching = false;

	}
	
	public function __destruct(){
	    
	    if($this->page && !$this->draft_mode){
	        
	        $p = $this->page->copy();
	        $p->setLastBuilt(time());
	        $p->save();
	        
	    }
	    
	}
	
	public function getPage(){
        return $this->page;
    }
    
    public function isMetaPage(){
        return ($this->getPage() instanceOf SmartestItemPage);
    }
    
    public function getItem(){
        if($this->isMetaPage()){
            return $this->getPage()->getPrincipalItem();
        }
    }
    
    public function assignPage($page){
        $this->page = $page;
        if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $page->getSiteId());
        }
    }
    
    public function setPageRenderingData($data){
        // $this->_page_rendering_data = &$data;
        $this->_tpl_vars['this'] = new SmartestPageRenderingDataRequestHandler($this->page); // $this->_page_rendering_data;
        $this->_page_rendering_data_retrieved = true;
    }
    
    public function getDraftMode(){
        return $this->draft_mode;
    }
    
    public function setDraftMode($mode){
        
        $this->draft_mode = SmartestStringHelper::toRealBool($mode);
        $this->_tpl_vars['sm_draft_mode'] = $this->draft_mode;
        
        if($this->page){
            $this->page->setDraftMode($mode);
        }
        
    }
    
    public function getDataSetsHolder(){
        return SmartestPersistentObject::get('template_layer_data:sets');
    }
    
    public function getItemsHolder(){
        return SmartestPersistentObject::get('template_layer_data:items');
    }
    
    public function startChildProcess($pid, $type=''){
        
        $pid = SmartestStringHelper::toVarName($pid);
        
        // if($this->_page_rendering_data_retrieved){
        
	        $cp = parent::startChildProcess($pid);
	        $cp->setDraftMode($this->getDraftMode());
	        $cp->assignPage($this->page);
	        $cp->setPageRenderingData($this->_page_rendering_data);
	        
            return $this->_child_processes[$pid];
        
        // }
	}
	
	public function prepareForRender(){
        
        $this->page->loadAssetClassDefinitions();
	    $this->page->loadItemSpaceDefinitions();
	    // $this->setPageRenderingData($this->page->fetchRenderingData());
	    $this->_tpl_vars['this'] = new SmartestPageRenderingDataRequestHandler($this->page);
	    
    }
    
    public function renderPage($page, $draft_mode=false){
	    
        $this->page = $page;
	    $this->setDraftMode($draft_mode);
	    
	    $GLOBALS['CURRENT_PAGE'] = $page;
	    
	    if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $page->getSiteId());
        }
	    
	    $this->prepareForRender();
	    
	    if($draft_mode){
	        $safe_template = "Presentation/Masters/".$page->getDraftTemplate();
	    }else{
	        $safe_template = "Presentation/Masters/".$page->getLiveTemplate();
	    }
	    
	    $template = SM_ROOT_DIR.$safe_template;
	    
	    if(!defined('SM_CMS_PAGE_ID')){
		    define('SM_CMS_PAGE_ID', $this->page->getId());
		}
	    
	    if(!is_file($template)){
	        
	        if(is_dir($template)){
                
                // no template is set at all. show "you need to create one" message.
                $this->assign('required_template', $safe_template);
	            $template = SM_ROOT_DIR.'System/Presentation/Error/_pageHasNoMasterTemplate.tpl';
                
            }else{
                
                // page refers to a non-existent template.
                $this->assign('required_template', $safe_template);
	            $template = SM_ROOT_DIR.'System/Presentation/Error/_websiteTemplateNotFound.tpl';
                
            }
	        
	        ob_start();
	        $this->run($template, array());
	        $content = ob_get_contents();
	        ob_end_clean();
	    
	        return $content;
	        
	    }else{
	    
	        ob_start();
	        $this->run($template, array());
	        $content = ob_get_contents();
	        ob_end_clean();
	    
	        return $content;
        }
	}
    
    public function renderContainer($container_name, $params, $parent){
        
        if($this->_context == SM_CONTEXT_CONTENT_PAGE){
            
            $instance_name = isset($params['instance']) ? SmartestStringHelper::toVarName($params['instance']) : 'default';
            
            if($this->getPage()->hasContainerDefinition($container_name, $instance_name)){
                
                $container_def = $this->getPage()->getContainerDefinition($container_name, $instance_name);
                
                if($this->getDraftMode()){
                    echo "<!--Smartest: Begin container template ".$container_def->getTemplateFilePathInSmartest()." -->\n";
                }
                
                $this->_tpl_vars['this'] = new SmartestPageRenderingDataRequestHandler($this->page);
                $this->_tpl_vars['sm_draft_mode'] = $this->getDraftMode();
                
                if($instance_name == 'default'){
                    $this->run($container_def->getTemplateFilePath(), array());
                }else{
                    $this->run($container_def->getTemplateFilePath(), array('__parent_container_instance'=>$instance_name));
                }
                
                if($this->getDraftMode()){
                    echo "\n<!--Smartest: End container template ".$container_def->getTemplateFilePathInSmartest()." -->\n";
                }
                
                if($this->_request_data->g('action') == "renderEditableDraftPage"){
		            
                    $edit_link = '';
    			    
    			    if(constant("SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN")){
    			        
		                if(is_object($container_def->getTemplate()) && is_file($container_def->getTemplateFilePath()) && is_writable($container_def->getTemplateFilePath())){
        			        $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit template: ".$container_def->getTemplate()->getUrl()."\" href=\"".$this->_request_data->g('domain')."templates/editTemplate?template=".$container_def->getTemplate()->getId()."&amp;type=SM_ASSETTYPE_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px";
                            if($this->_hide_edit_buttons) $edit_link .= ';display:none;';
                            $edit_link .= "\" target=\"_top\">&nbsp;<img src=\"".$this->_request_data->g('domain')."Resources/System/Images/edit-pencil-red.png\" alt=\"edit\" style=\"display:inline;border:0px;width:16px;height:16px\" /><!-- Edit this template--></a>";
        			    }
	                    
                        $edit_link .= $this->renderEditContainerButton($container_name);

			        }
		    
    		    }else{
    			    $edit_link = "<!--edit link-->";
    		    }
    		    
    		    return $edit_link;
                
            }else{
                
                // container is undefined
                
                if($this->getDraftMode()){ // $this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"
		    
    			    $edit_link = '';
    			    
    			    if(defined('SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN') && (bool) constant("SM_OPTIONS_ALLOW_CONTAINER_EDIT_PREVIEW_SCREEN")){
    			        
                        /* $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit definition for container: '".$container_name."'\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview";
                        if($this->getPage() instanceOf SmartestItemPage) $edit_link .= "&amp;item_id=".$this->getPage()->getSimpleItem()->getId();
                        $edit_link .= " style=\"text-decoration:none;font-size:11px";
                        if($this->_hide_edit_buttons) $edit_link .= 'display:none';
                        $edit_link .= "\" target=\"_top\">&nbsp;<img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_red.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
                        
		                if($this->getPage() instanceOf SmartestItemPage){
    			            $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit definition for container: ".$container_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;item_id=".$this->getPage()->getSimpleItem()->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\">&nbsp;<img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_red.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
			            }else{
			                $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit definition for container: ".$container_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\">&nbsp;<img src=\"".$this->_request_data->g('domain')."Resources/Icons/arrow_refresh_red.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
			            } */
                        
                        $edit_link .= $this->renderEditContainerButton($container_name);

			        }
		    
    		    }else{
    			    $edit_link = "<!--edit link-->";
    		    }
    		    
    		    return $edit_link;
                
            }
            
            /* $container = new SmartestContainerDefinition;
        
            if($container->load($container_name, $this->getPage(), $this->getDraftMode())){
            
                if($container->getTemplateFilePath()){
                    // $this->_smarty_include(array('smarty_include_tpl_file'=>$container->getTemplateFilePath(), 'smarty_include_vars'=>array()));
                    $this->run($container->getTemplateFilePath(), array());
                }
            
                if($this->_request_data->g('action') == "renderEditableDraftPage"){
			    
    			    $edit_link = '';
			    
    			    if(is_object($container->getTemplate())){
    			        // TODO: Make it an admin-controlled setting as to whether containers are changeable in the preview screen
    			        // $edit_link .= "<a title=\"Click to edit template: ".$container->getTemplate()->getUrl()."\" href=\"".SM_CONTROLLER_DOMAIN."templates/editTemplate?template_id=".$container->getTemplate()->getId()."&amp;type=SM_CONTAINER_TEMPLATE&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Edit this template--></a>";
    			    }
			    
    			    // $edit_link .= "<a title=\"Click to edit definition for container: ".$container_name."\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    
    		    }else{
    			    // $edit_link = "<!--edit link-->";
    		    }
		    
    		    return $edit_link;
            
            } */
        
        }else{
            
            return $this->raiseError('Container tag can only be used in page context.');
            
        }
        
    }
    
    public function renderEditContainerButton($container_name){
        
        if($this->getDraftMode()){
            
            $edit_link = "<a class=\"sm-edit-button marker1\" title=\"Click to edit definition for container: ".$container_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineContainer?assetclass_id=".$container_name."&amp;page_id=".$this->page->getWebid()."&amp;from=pagePreview";
            if($this->getPage() instanceOf SmartestItemPage) $edit_link .= "&amp;item_id=".$this->getPage()->getSimpleItem()->getId();
            $edit_link .= '" style="text-decoration:none;font-size:11px';
            if($this->_hide_edit_buttons) $edit_link .= ';display:none';
            $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/container-switch.png\" alt=\"edit\" style=\"width:16px;height:16px;display:inline;border:0px;\" /></a>";
            return $edit_link;
            
        }else{
            
            return '';
            
        }
        
    }
    
    public function renderTemplateTag($requested_file){
        
        if(SmartestStringHelper::getDotSuffix($requested_file) != 'tpl'){
	        $requested_file .= '.tpl';
	    }
        
        $directories = array('Presentation/Layouts/');
        
        $file_found = false;
        
        foreach($directories as $dir){
            if(is_file(SM_ROOT_DIR.$dir.$requested_file)){
                $file_found = true;
                $template = SM_ROOT_DIR.$dir.$requested_file;
                continue;
            }
        }
        
        if($file_found){
	        $render_process_id = SmartestStringHelper::toVarName('template_'.SmartestStringHelper::removeDotSuffix($requested_file).'_'.substr(microtime(true), -6));
	        $child = $this->startChildProcess($render_process_id);
	        $child->caching = false;
	        $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
	        $child->assign('this', $this->_tpl_vars['this']);
	        $content = $child->fetch($template);
	        $this->killChildProcess($child->getProcessId());
	        return $content;
        }else{
            return $this->raiseError('Template \''.$requested_file.'\' not found');
        }
        
    }
    
    public function renderPlaceholder($placeholder_name, $params, $parent){
        
        $html = '';
        
        // does definition exist?
        if($this->getPage()->hasPlaceholderDefinition($placeholder_name, $params['instance'])){ // If the Placeholder is defined
            
            $display = (isset($params['display']) && in_array(strtolower($params['display']), array('file', 'filename', 'full', 'path', 'normal', 'download', 'size', 'false'))) ? $params['display'] : 'normal';
            
            // If display not set, or explicitly set to something other than false, display file appropriately
            
            if(!SmartestStringHelper::isFalse($display)){
                
                $placeholder_def = $this->getPage()->getPlaceholderDefinition($placeholder_name, $params['instance']);
                $asset = $placeholder_def->getAsset($this->getDraftMode());
                
                if($asset instanceof SmartestRenderableAsset){
                
                    $type_info = $asset->getTypeInfo();
                
                    switch($display){
                        
                        case "file":
                        case "filename":
                        
                        return $asset->getUrl();
                        
                        case "full":
                        case "path":
                        
                        if($asset->usesLocalFile()){
                            
                            // Return is used because an edit button is not used when alternative displays are requested
                            return $asset->getFullWebPath();
                        
                        }else{
                        
                            return $this->raiseError('display="'.$display.'" used on asset type that does not have a local file: '.$asset->getType());
                        
                        }
                        
                        break;
                        
                        case "download":
                        return $asset->getAbsoluteDownloadUri();
                        
                        case "size":
                        
                        if($asset->usesLocalFile()){
                            return $asset->getSize();
                        }else{
                            return $this->raiseError('display="size" used on asset type that does not have a local file: '.$asset->getType());
                        }
                        
                        default:
                        
                        $render_data = array();
                    
                        if(isset($params['transform'])){
                            $transform_param_values = SmartestStringHelper::parseNameValueString($params['transform']);
                            // TODO: Allow inline transformations on certain asset types - resize, (rotate?)
                        }
                    
                        // apply render data from the placeholder definition
                        if($this->getDraftMode()){
                            $rd = $placeholder_def->getDraftRenderData();
                        }else{
                            $rd = $placeholder_def->getLiveRenderData();
                        }
                    
                        if($data = unserialize($rd)){
                            $external_render_data = $data;
                            $asset->setAdditionalRenderData($data, true);
                        }else{
                            $external_render_data = array();
                        }
                    
                        foreach($external_render_data as $key => $value){
                            $render_data[$key] = $value;
                        }
                    
                        // Lastly, apply any render data from the template
                        foreach($params as $key => $value){
                            if($key != 'name'){
        	                    if(isset($params[$key])){
                	                $render_data[$key] = $value;
                	            }else{
                	                if(!isset($render_data[$key])){
                	                    $render_data[$key] = '';
            	                    }
                	            }
            	            }
        	            }
                    
                        // Second parameter here is for whether blank values should be skipped when passing render data. As of revision 716, blank values entered on the define placeholder creen DO override other values, including asset and type defaults
                        $asset->setAdditionalRenderData($render_data);
                    
                        $html = $asset->render($this->getDraftMode());
    	            
        	            // This code makes sure that if internal link codes are input as values on an image placeholder, the link will be built
        	            if($asset->isImage() && isset($render_data['link_href']) && strlen($render_data['link_href'])){
    	                
        	                $link_properties = SmartestLinkParser::parseSingle($render_data['link_href']);
        	                $link = new SmartestCmsLink($link_properties, array());
        	                $image = $asset->getImage();
                            $image->setAdditionalRenderData($render_data);
        	                $link->setImageAsContent($image);
    	                
        	                if($GLOBALS['CURRENT_PAGE']){
                    		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
                    		}
    	                
        	                $html = $link->render($this->getDraftMode());
    	                
        	            }
                        
                        break;
                        
                    } // end of $display switch statement
                
                } // if the asset is present and an instance of SmartestRenderableAsset
            
            } // end of if $display is not false
          
        }else{ // placeholder is not defined.
            
            $display = (isset($params['display']) && in_array(strtolower($params['display']), array('file', 'filename', 'full', 'path', 'normal', 'download', 'size', 'false'))) ? $params['display'] : 'normal';
            
            switch($display){
                
                case "file":
                case "filename":
                case "full":
                case "path":
                return;
                
                default:
                $html = '<!--Placeholder "'.$placeholder_name.'" not defined-->';
                
            }
            
        }
        
        $html .= $this->renderEditPlaceholderButton($placeholder_name, $params);
        
        return $html;
        
    }
    
    public function renderEditPlaceholderButton($placeholder_name, $params){
        
        $placeholder_def = $this->getPage()->getPlaceholderDefinition($placeholder_name, $this->getDraftMode());
        // $type_code = $placeholder_def->getType();
        $placeholder = new SmartestPlaceholder;
        
        $instance_name = isset($params['instance']) ? SmartestStringHelper::toVarName($params['instance']) : 'default';
        
        if($placeholder->findBy('name', SmartestStringHelper::toVarName($placeholder_name))){
            
            $type_code = $placeholder->getType();
            $assetclass_types = SmartestDataUtility::getAssetClassTypes();
            $edit_link = '';
        
            if(isset($assetclass_types[$type_code])){
            
                if((isset($assetclass_types[$type_code]['setfrompreview']) && SmartestStringHelper::isFalse($assetclass_types[$type_code]['setfrompreview'])) || (isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol']))){ 
                
                    if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
                        $edit_link = "<!--This placeholder cannot be set from preview mode-->";
                    }
                
                }else{
                
                    $show_edit_link = ($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment");
                    
                    if($show_edit_link){
            
                        if($this->getDraftMode()){
                        
                            if($this->getPage() instanceOf SmartestItemPage){
                                $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder_def->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid().'&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
                                $delete_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder_def->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid().'&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
                            }else{
                                $edit_url = $this->_request_data->g('domain')."websitemanager/definePlaceholder?assetclass_id=".$placeholder_def->getPlaceholder()->getName()."&amp;page_id=".$this->page->getWebid();
                            }
                            
                            $edit_url .= '&amp;instance='.$instance_name;
                        
                            if($this->getPage()->hasPlaceholderDefinition($placeholder_name, $instance_name)){
                        
            			        $edit_link = "<a class=\"sm-edit-button\" title=\"Click to edit definition for placeholder: ".$placeholder_def->getPlaceholder()->getLabel()." (".$placeholder_def->getPlaceholder()->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px";
                                if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                                $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/placeholder-switch.png\" alt=\"edit\" style=\"width:16px;height:16px;display:inline;border:0px;\" /><!-- Swap this file--></a>";
                        
                            }else{
                            
                                if($this->getPage()->getPlaceholderDefinition($placeholder_name, $this->getDraftMode())->getPlaceholder()->getType() == "SM_ASSETCLASS_RICH_TEXT"){
                                    $edit_link = "<p class=\"sm-edit-area sm-edit-empty-paragraph\"";
                                    if($this->_hide_edit_buttons) $edit_link .= ' style="display:none"';
                                    $edit_link .= "><a class=\"sm-edit-button\" title=\"Click to choose or upload a file for for placeholder: ".$placeholder_def->getPlaceholder()->getLabel()." (".$placeholder_def->getPlaceholder()->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px";
                                    if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                                    $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/fill-empty-placeholder-text.png\" alt=\"edit\" style=\"width:16px;height:16px;display:inline;border:0px;\" /><!-- Upload new text--></a></p>";
                                }else{
                                    $edit_link = "<a class=\"sm-edit-button\" title=\"Click to choose or upload a file for for placeholder: ".$placeholder_def->getPlaceholder()->getLabel()." (".$placeholder_def->getPlaceholder()->getType().")\" href=\"".$edit_url."\" style=\"text-decoration:none;font-size:11px";
                                    if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                                    $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/fill-empty-placeholder.png\" alt=\"edit\" style=\"width:16px;height:16px;display:inline;border:0px;\" /><!-- Upload new file--></a>";
                                }
                            
                            }
                        
                        }else{
        			        $edit_link = "<!--edit link-->";
        		        }
	        
    	            }else{
    	                // Not draft mode, so display nothing
                        $edit_link = "<!--Not draft mode, so display nothing-->";
    	            }
                }
            
            }else{
                // placeholder type does not exist
                $edit_link = "<!--placeholder type '".$type_code."' does not exist-->";
            }
        
        }else{
            
            // placeholder does not exist, so the type cannot be known
            
            if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
            
                if(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol'])){
                    $edit_link = "<!--placeholder '".$placeholder_name."' does not exist-->";
                }else{
                    $edit_link = "<a class=\"sm-edit-button\" title=\"Placeholder ".$placeholder_name." does not exist. Click to create.\" href=\"".$this->_request_data->g('domain')."websitemanager/addPlaceholder?placeholder_name=".$placeholder_name."\" style=\"text-decoration:none;font-size:11px";
                    if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                    $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/placeholder-warning.png\" alt=\"edit\" style=\"width:16px;display:inline;border:0px;\" /></a>";
                }
            
            }
            
        }
        
        return $edit_link;
        
    }
    
    public function renderItemSpace($itemspace_name, $params){
        
        if($this->_context == SM_CONTEXT_CONTENT_PAGE){
            
            $itemspace_name = SmartestStringHelper::toVarName($itemspace_name);
            
            if($this->getPage()->hasItemSpaceDefinition($itemspace_name, $this->getDraftMode())){
            
                $def = $this->getPage()->getItemSpaceDefinition($itemspace_name, $this->getDraftMode());
                
                $item_name = isset($params['item_name']) ? SmartestStringHelper::toVarName($params['item_name']) : 'item';
                
                $item_name = isset($params['assign']) ? SmartestStringHelper::toVarName($params['assign']) : $item_name;
                
                // Tell Smartest that this particular item appears on this page.
                // Strictly speaking, this information is already stored as the itemspace def, 
                // but we want to standardise this information so that it can be processed efficiently
                $dah = new SmartestDataAppearanceHelper;
                $dah->setItemAppearsOnPage($def->getSimpleItem($this->getDraftMode())->getId(), $this->getPage()->getId());
                
                if($def->getItemspace()->usesTemplate()){
                    
                    $template_id = $def->getItemspace()->getTemplateAssetId();
                    $template = new SmartestTemplateAsset;
                    
                    if($template->find($template_id)){
                        
                        $template_path = SM_ROOT_DIR.'Presentation/Layouts/'.$template->getUrl();
                        $render_process_id = SmartestStringHelper::toVarName('itemspace_template_'.SmartestStringHelper::removeDotSuffix($template->getUrl()).'_'.substr(microtime(true), -6));
            	        
                        $child = $this->startChildProcess($render_process_id);
            	        $child->setContext(SM_CONTEXT_ITEMSPACE_TEMPLATE);
            	        $item = $def->getItem(false, $this->getDraftMode());
            	        $item->setDraftMode($this->getDraftMode());
            	        $child->assign($item_name, $item); // set above
            	        
                        if($this->getDraftMode()){
                            $content = "<!--Smartest: Begin Itemspace template ".$template->getFilePathInSmartest()." -->\n";
                        }else{
                            $content = '';
                        }
                        
            	        $content .= $child->fetch($template_path);
            	        $content .= $this->renderItemEditButton($item->getId());
            	        $content .= $this->renderItemSpaceDefineButton($itemspace_name);
                        
                        if($this->getDraftMode()){
                            $content .= "<!--Smartest: Begin Itemspace template ".$template->getFilePathInSmartest()." -->\n";
                        }
                        
            	        $this->killChildProcess($child->getProcessId());
            	        
            	        return $content;
            	        
                    }else{
                        return $this->raiseError("Problem rendering itemspace with template ID ".$template_id.": template not found.");
                    }
                
                }else{
                
                    // itemspace doesn't use template, but data is still loaded
                    $this->_comment("ItemSpace '".$itemspace_name."' does not use a template.");
                    $item = $def->getItem(false, $this->getDraftMode());
        	        $item->setDraftMode($this->getDraftMode());
        	        $item_name = isset($params['item_name']) ? SmartestStringHelper::toVarName($params['item_name']) : $itemspace_name.'_item';
                    $item_name = isset($params['assign']) ? SmartestStringHelper::toVarName($params['assign']) : $item_name;
        	        $this->assign($item_name, $item);
        	        return $this->renderItemEditButton($item->getId()).$this->renderItemSpaceDefineButton($itemspace_name);
                
                }
            
            }else{
                
                // item space is not defined
                $this->_comment("ItemSpace '".$itemspace_name."' is not defined.");
                return $this->renderItemSpaceDefineButton($itemspace_name);
                
            }
        
        }else{
            
            return $this->raiseError("ItemSpace '".$itemspace_name."' used outside page context.");
            
        }
        
    }
    
    public function renderItemEditButton($item_id){
        
        if($this->getDraftMode()){
            $url = $this->_request_data->g('domain').'datamanager/openItem?item_id='.$item_id;
            if($this->page){
                $url .= '&amp;from=pagePreview&amp;page_webid='.$this->page->getWebid();
            }else if($this->_request_data->g('request_parameters')->g('page_id')){
                $url .= '&amp;from=pagePreview&amp;page_webid='.$this->_request_data->g('request_parameters')->g('page_id');
            }
            $html = '<a class="sm-edit-button" href="'.$url.'" target="_top" title="Edit item ID '.$item_id.'"';
            if($this->_hide_edit_buttons) $html .= ' style="display:none"';
            $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/edit-pencil-magenta.png" style="width:16px;height:16px" alt="Edit item ID '.$item_id.'" /></a>';
            
        }else{
            $html = '';
        }
        
        return $html;
        
    }
    
    public function renderReorderSetButton($identifier){
        
        if($this->getDraftMode()){
            
            if($identifier instanceof SmartestCmsItemSet){
                $set = $identifier;
            }else{
                $set = new SmartestCmsItemSet;
        
                if(is_numeric($identifier)){
                    if(!$set->find($identifier)){
                        return $this->raiseError("Item set with ID '".$identifier."' could not be found.");
                    }
                }else{
                    if(!$set->findBy('name', $identifier)){
                        return $this->raiseError("Item set with name '".$identifier."' could not be found.");
                    }
                }
            }
            
            $url = $this->_request_data->g('domain').'sets/editStaticSetOrder?set_id='.$set->getId();
            
            if($this->page){
                if(isset($_GET['hide_newwin_link']) && $_GET['hide_newwin_link'] = 'true'){
                    $url .= '&amp;from=fullPreview&amp;page_id='.$this->page->getWebId();
                }else{
                    $url .= '&amp;from=pagePreview&amp;page_id='.$this->page->getWebId();
                }
            }
            
            if($this->getPage() instanceof SmartestItemPage){
                $url .= '&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
            }
            
            if($set->getType('STATIC')){
                $html = '<a class="sm-edit-button" href="'.$url.'" target="_top"';
                if($this->_hide_edit_buttons) $html .= ' style="display:none"';
                $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/switch-order-magenta.png" style="width:16px;height:16px" alt="" /></a>';
                return $html;
            }else{
                return $this->raiseError("Item set '".$set->getLabel()."' is not static and cannot be ordered manually.");
            }
        
        }else{
            
            return '';
            
        }
        
    }
    
    public function renderReorderPageGroupButton($identifier){
        
        if($this->getDraftMode()){
        
            $g = new SmartestPageGroup;
        
            if(is_numeric($identifier)){
                if(!$g->find($identifier)){
                    return $this->raiseError("Page group with ID '".$identifier."' could not be found.");
                }
            }else{
                if(!$g->findBy('name', $identifier)){
                    return $this->raiseError("Page group with name '".$identifier."' could not be found.");
                }
            }
            
            // First, the URL that the user should be taken to on clicking the button
            $url = $this->_request_data->g('domain').'websitemanager/editPageGroupOrder?group_id='.$g->getId().'&amp;from=pagePreview&amp;page_id='.$this->getPage()->getWebId();
            if($this->getPage() instanceof SmartestItemPage){
                $url .= '&amp;item_id='.$this->getPage()->getSimpleItem()->getId();
            }
            
            // And then the HTML for the button itself
            $html = '<a class="sm-edit-button" href="'.$url.'" target="_top"';
            if($this->_hide_edit_buttons) $html .= ' style="display:none"';
            $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/switch-order-teal.png" style="width:16px;height:16px" alt="" /></a>';
            
            return $html;
            
        
        }else{
            
            return '';
            
        }
        
    }
    
    public function renderEditGalleryButton($identifier){
        
        if($this->getDraftMode()){
        
            $g = new SmartestAssetGroup;
        
            if(is_numeric($identifier)){
                if(!$g->find($identifier)){
                    return $this->raiseError("Gallery with ID '".$identifier."' could not be found.");
                }
            }else{
                if(!$g->findBy('name', $identifier)){
                    return $this->raiseError("Gallery with name '".$identifier."' could not be found.");
                }
            }
        
            if($g->getIsGallery()){
                
                if(isset($_GET['hide_newwin_link']) && $_GET['hide_newwin_link'] = 'true'){
                    $previewtype = 'fullPreview';
                }else{
                    $previewtype = 'pagePreview';
                }
                
                $arrange_url = $this->_request_data->g('domain').'assets/arrangeAssetGallery?group_id='.$g->getId().'&amp;from='.$previewtype.'&amp;page_id='.$this->getPage()->getWebId();
                if($this->isMetaPage()){
                    $arrange_url .= '&amp;item_id='.$this->getItem()->getId();
                }
                $html = '<a class="sm-edit-button" href="'.$arrange_url.'" target="_top"';
                if($this->_hide_edit_buttons) $html .= ' style="display:none"';
                $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/switch-order-yellow.png" alt="" style="width:16px;height:16px" /></a>';
                
                $add_url = $this->_request_data->g('domain').'smartest/file/new?group_id='.$g->getId().'&amp;from='.$previewtype.'&amp;page_id='.$this->getPage()->getWebId();
                if($this->isMetaPage()){
                    $add_url .= '&amp;item_id='.$this->getItem()->getId();
                }
                $html .= '&nbsp;<a class="sm-edit-button" href="'.$add_url.'" target="_top"';
                if($this->_hide_edit_buttons) $html .= ' style="display:none"';
                $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/file-add.png" alt="" style="width:16px;height:16px" /></a>';
                
                return $html;
            }else{
                return $this->raiseError("File group '".$set->getLabel()."' is not a gallery.");
            }
        
        }else{
            return '';
        }
        
    }
    
    public function renderEditTagButton($params){
        
        if($this->getDraftMode()){
            
            $url = $this->_request_data->g('domain').'settings/editTag?';
            
            if(isset($params['tag'])){
                
            }elseif($this->page instanceof SmartestTagPage){
                
            }elseif(isset($params['id'])){
                
            }elseif(isset($params['name']) || isset($params['slug'])){
                
            }
            
        }
        
    }
    
    public function renderItemSpaceDefineButton($itemspace_name){
        
        if($this->getDraftMode()){
            
            $url = $this->_request_data->g('domain').'websitemanager/defineItemspace?assetclass_id='.$itemspace_name;
            
            if($this->page){
                $url .= '&amp;from=pagePreview&amp;page_id='.$this->page->getWebid();
            }else if($this->_request_data->g('request_parameters')->g('page_id')){
                $url .= '&amp;from=pagePreview&amp;page_id='.$this->_request_data->g('request_parameters')->g('page_id');
            }
            
            $html = '<a class="sm-edit-button" href="'.$url.'" target="_top" title="Edit itemspace '.$itemspace_name.'"';
            if($this->_hide_edit_buttons) $html .= ' style="display:none"';
            $html .= '><img src="'.$this->_request_data->g('domain').'Resources/System/Images/itemspace-switch.png" style="width:16px;height:16px" alt="Edit itemspace'.$itemspace_name.'" /></a>';
            
        }else{
            $html = '';
        }
        
        return $html;
        
    }
    
    public function renderField($field_name, $params){
        
        // if($this->_page_rendering_data['fields']->hasParameter($field_name)){
            $fields = $this->_tpl_vars['this']->getFieldDefinitions();
            $value = $fields[$field_name];
        
            $show_edit_link = !(isset($params['showcontrol']) && SmartestStringHelper::isFalse($params['showcontrol']));
            
            if($this->getDraftMode() && $show_edit_link){
			    $edit_link = $this->renderEditFieldButton($field_name, $params);
		    }else{
			    $edit_link = '';
		    }

            $value .= $edit_link;
    
            return $value;
    
        /* }else{
        
            return $this->raiseError('Field \''.$field_name.'\' does not exist on this site.');
        
        } */
        
    }
    
    public function renderEditFieldButton($field_name, $params){
        
        if($this->_tpl_vars['this']->getFieldDefinitions() instanceof SmartestParameterHolder){
        
            $markup = '<!--edit field button-->';
        
            if($this->getPage()->getSite()->fieldExists($field_name)/* $this->_tpl_vars['this']->getFieldDefinitions()->hasParameter($field_name) */){
        
                if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
    		        $markup = "<a class=\"sm-edit-button\" title=\"Click to define field: ".$field_name."\" href=\"".$this->_request_data->g('domain')."metadata/defineFieldOnPage?page_id=".$this->getPage()->getWebid()."&amp;assetclass_id=".$field_name."\" style=\"text-decoration:none;font-size:11px";
                    if($this->_hide_edit_buttons) $markup .= ';display:none';
                    $markup .= "\" target=\"_top\">&nbsp;<img src=\"".$this->_request_data->g('domain')."Resources/System/Images/edit-pencil-blue.png\" alt=\"edit\" style=\"width:16px;height:16px;display:inline;border:0px;\" /></a>";
    	        }
	    
            }
        
            return $markup;
        
        }
        
    }
    
    public function renderEditSetButton($set_identifier, $params){
        
        if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
        
            $set = new SmartestCmsItemSet;
        
            if(is_numeric($set_identifier)){
                $found_set = $set->find($set_identifier);
            }else{
                $found_set = $set->findBy('name', $name, $this->getPage()->getSiteId());
            }
        
            if($found_set){
                $markup = "<a class=\"sm-edit-button\" title=\"click to edit set: ".$set->getLabel()."\" href=\"".$this->_request_data->g('domain')."sets/editSet?set_id=".$set->getId()."\"";
                if($this->_hide_edit_buttons) $markup .= ' style="display:none"';
                $markup .= "><img src=\"".$this->_request_data->g('domain')."Resources/Icons/pencil.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /></a>";
            }
        
        }
        
    }
    
    public function renderList($list_name, $params){
        
        $list = new SmartestCmsItemList;
        
        // If a definition for this list exists on this page
        if($list->load($list_name, $this->getPage(), $this->getDraftMode())){
            
            $content = '';
            
            // if($list->hasRepeatingTemplate($this->getDraftMode())){
                
                $limit = $list->getMaximumLength() > 0 ? $list->getMaximumLength() : null;
                
                try{
                    $data = $list->getItems($this->getDraftMode(), $limit);
                }catch(SmartestException $e){
                    $content .= $this->raiseError($e->getMessage());
                }
                
                if($list->getType() == 'SM_LIST_TAG'){
                    
                    $tag = new SmartestTag;
                    
                    if($this->getDraftMode()){
                        $tag_id = $list->getDraftSetId();
                    }else{
                        $tag_id = $list->getLiveSetId();
                    }
                    
                    if($tag_id){
                        if($tag->find($tag_id)){
                        
                        }else{
                            $content .= $this->raiseError("A tag with ID ".$tag_id.' could not be found.');
                        }
                    }else{
                        $content .= $this->raiseError('No tag ID could be found.');
                    }
                    
                }elseif($list->getType() == 'SM_LIST_SIMPLE'){
                    
                    try{
                        $set = $list->getSet($this->getDraftMode());
                    }catch(SmartestException $e){
                        return $this->raiseError($e->getMessage());
                    }
                    
                }elseif($list->getType() == 'SM_LIST_ARTICULATED'){
                    // These lists are no longer supported
                    echo $this->raiseError("Articulated lists are no longer supported");
                    return;
                }
                
                $model = new SmartestModel;
                $header_image = new SmartestRenderableAsset;
                
                // The list uses a simple list template
                
                // If the user has chosen to assign the set's items to a variable instead of display the template:
                if(isset($params['assign']) && strlen($params['assign'])){
                    $this->assign(SmartestStringHelper::toVarName($params['assign']), $data);
                }else{
                    // Display the simple list template, having defined the necessary preset variables
                    $child = $this->startChildProcess(substr(md5($list->getName().microtime()), 0, 8));
    	            
                    $child->assign('items', $data);
    	            $child->assign('num_items', count($data));
    	            $child->assign('title', $list->getTitle());
                    
                    if($list->getType() == 'SM_LIST_SIMPLE'){
                        $child->assign('set', $set);
                    }elseif($list->getType() == 'SM_LIST_TAG'){
                        $child->assign('tag', $tag);
                    }
                    
                    if($this->getDraftMode()){
                        $model_id = $list->getDraftSetFilter();
                        $header_image_id = $list->getDraftHeaderImageId();
                    }else{
                        $model_id = $list->getLiveSetFilter();
                        $header_image_id = $list->getLiveHeaderImageId();
                    }
                    
                    if($model->find($model_id)){
                        $child->assign('model', $model);
                    }
                    
                    if($header_image->find($header_image_id)){
                        $child->assign('header_image', $header_image);
                        $child->assign('has_header_image', true);
                    }else{
                        $child->assign('has_header_image', false);
                    }
                    
                    $child->assign('list', $list);
    	            $child->setContext(SM_CONTEXT_COMPLEX_ELEMENT);
    	            $child->setDraftMode($this->getDraftMode());
    	            $child->caching = false;
                    
                    if($this->getDraftMode()){
                        $content .= "<!--Smartest: Begin list template ".$list->getRepeatingTemplateInSmartest($this->getDraftMode())." -->\n";
                    }
                    
                    $content .= $child->fetch($list->getRepeatingTemplate($this->getDraftMode()));
                    $this->killChildProcess($child->getProcessId());
                    
                    if($this->getDraftMode()){
                        $content .= "<!--Smartest: End list template ".$list->getRepeatingTemplateInSmartest($this->getDraftMode())." -->\n";
                    }
                    
	            }
            
            // }else{
            //     // no template
            // } // end if: the list has repeating template
            
            $edit_link = '';
            
            if($list->getType() == 'SM_LIST_SIMPLE' && $set->getType() == 'STATIC'){
                $edit_link .= $this->renderReorderSetButton($set);
            }
            
            if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
			    $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit definitions for embedded list: ".$list->getName()."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineList?assetclass_id=".$list->getName()."&amp;page_id=".$this->getPage()->getWebid()."\" style=\"text-decoration:none;font-size:11px";
                if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/list-switch.png\" alt=\"edit\" style=\"display:inline;border:0px;width:16px;height:auto\" /><!-- Edit this list--></a>\n\n";
		    }else{
			    $edit_link = "<!--edit link-->";
		    }
        
            $content .= $edit_link;
            
            return $content;
            
        }else{
            
            // No definition exists for this list on this page
            
            if($this->getDraftMode()){
                if($this->_request_data->g('action') == "renderEditableDraftPage" || $this->_request_data->g('action') == "pageFragment"){
    			    $edit_link = "<a class=\"sm-edit-button\" title=\"Click to edit definitions for embedded list: ".$list_name."\" href=\"".$this->_request_data->g('domain')."websitemanager/defineList?assetclass_id=".$list_name."&amp;page_id=".$this->getPage()->getWebid()."\" style=\"text-decoration:none;font-size:11px";
                    if($this->_hide_edit_buttons) $edit_link .= ';display:none';
                    $edit_link .= "\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/list-switch.png\" alt=\"edit\" style=\"display:inline;border:0px;width:16px;height:auto\" /><!-- Edit this list--></a>\n\n";
    		    }else{
    			    $edit_link = "<!--edit link-->";
    		    }
        
                $content = $edit_link;
            
                return $content;
            }
            
        }
    
    }
    
    public function renderBlockList($style, $params){
        
        echo '<p>BlockList style: <strong>'.$style.'</strong></p>';
        
    }
    
    public function renderBreadcrumbs($params){
        
        if($this->_tpl_vars['this']['navigation']['_breadcrumb_trail']){

    		$breadcrumbs = $this->_tpl_vars['this']['navigation']['_breadcrumb_trail'];
    		$separator = (isset($params['separator'])) ? $params['separator'] : "&gt;";
    		$string = "";
    		
    		$link_params = array();

    		if(isset($params['linkclass'])){
    		    $link_params['class'] = $params['linkclass'];
    		}

    		$link_params['goCold'] = 'true';
    		
    		$last_breadcrumb_index = (count($breadcrumbs) - 1);
            
            $markup_index = 0;
    		
    		foreach($breadcrumbs as $key => $page){
                
                if(!$this->getDraftMode() && !SmartestStringHelper::toRealBool($page->getIsPublished())){
                    continue;
                }
                
                // TODO: Create savings by making the link use the object rather than the link code
                if($page->getType() == 'ITEMCLASS'){
                    
                    if($key == $last_breadcrumb_index){
                        
                        $id = $this->page->getPrincipalItem()->getId();
			            $to = 'metapage:webid='.$page->getWebid().':id='.$id;
                        
                    }else{
                    
    			        if($page->hasPrincipalItem()){
    			            $id = $page->getPrincipalItem()->getId();
    			            $to = 'metapage:webid='.$page->getWebid().':id='.$id;
    			        }else{
    			            $to = 'page:webid='.$page->getWebid();
    			        }
    			    
			        }

    			}else{
    			    $to = 'page:webid='.$page->getWebid();
    		    }
                
                /* if($page->getType() == 'ITEMCLASS' && !$page instanceof SmartestItemPage){
                    $text = $page->getTitle();
                }else{
    			    $text = $this->renderLink($to, $link_params);
			    } */
			    
                $ph = new SmartestParameterHolder("Link Attributes: [".$to."]");
			    $ph->loadArray($link_params);
			    
			    $link = SmartestCmsLinkHelper::createLink($to, $ph);
			    $link->setHostPage($this->getPage());
                $link->addClass('sm-link-breadcrumb');
                $link->addClass('sm-link-breadcrumb-level-'.$markup_index);
			    $text = $link->render($this->getDraftMode());
                $markup_index++;

    			if($key > 0){
    				$string .= ' '.$separator.' ';
    			}

    			$string .= $text;
    		}

    		return $string;
    	}else{
    		return $this->raiseError("Automatic breadcrumbing failed - navigation data not present.");
    	}
    }
    
    public function renderUrl($to, $params){
        
        // used by the tinymce url helper, as well as the {url} template helper.
        
        if(strlen($to)){
            
            $preview_mode = ($this->_request_data->g('action') == "renderEditableDraftPage") ? true : false;
            
            $link_helper = new SmartestCmsLinkHelper($this->getPage(), $params, $this->getDraftMode(), $preview_mode);
            $link_helper->parse($to);
            
            return $link_helper->getUrl();
        
        }
        
    }
    
    public function loadItem($id){
        
        list($model_name, $item_id) = explode(':', $id);
        
        $item = SmartestCmsItem::retrieveByPk($item_id);
        
        return $item;
        
        /* if(isset($this->_models[$model_name])){
            if(isset($this->_models[$model_name])){
                
            }
        } */
        
        // $item = new SmartestItem;
        
        
    }
    
    public function loadItemAsArray($id){
        $item = $this->loadItem($id);
        return $item->__toArray($this->getDraftMode());
    }
    
    public function getRepeatBlockData($params){
        
        $this->caching = false;
        $this->_repeat_char_length_aggr = 0;
        
        if(is_array($params['from']) || $params['from'] instanceof SmartestArray || $params['from'] instanceof SmartestCmsItemSet || ($params['from'] instanceof SmartestAssetGroup && $params['from']->isGallery())){
            return $params['from'];
        }
        
        if(count(explode(':', $params['from'])) > 1){
            $parts = explode(':', $params['from']);
            $type = $parts[0];
            $name = $parts[1];
            if(isset($parts[2])){
                $subname = $parts[2];
            }
        }else{
            if($params['from'] == '_authors'){
                $type = 'authors';
                $uh = new SmartestUsersHelper;
                return $uh->getCreditableUsersOnSite($this->page->getSiteId());
            }else{
                $type = 'set';
                $name = $params['from'];
            }
        }
        
        switch($type){
            
            case "tag":
                
                if(count(explode(';', $params['from'])) > 1){
                    $sub_type_def = end(explode(';', $params['from']));
                    $sub_type = substr($params['from'], 0, 5);
                }else{
                    $sub_type = 'page';
                }
                
                break;
            
            case "gallery":
            $g = new SmartestAssetGroup;
            if($g->findBy('name', $name, $this->page->getSiteId())){
                
                if($g->getIsGallery()){
                    if(isset($params['skip_memberships']) && SmartestStringHelper::toRealBool($params['skip_memberships'])){
                        return $g->getMembers();
                    }else{
                        return $g->getMemberships();
                    }
                }else{
                    // the file group is not a gallery
                    return $this->raiseError('Specified file group \''.$name.'\' is not a gallery.');
                }
            }else{
                // no file group with that name
                return $this->raiseError('No file group exists with the name \''.$name.'\'.');
            }
            
            case "pagegroup":
            case "page_group":
            
            $g = new SmartestPageGroup;
            if($g->findBy('name', $name, $this->page->getSiteId())){
                if(isset($params['assignhighlight'])){
                    $highlighted_page = $g->determineHighlightedMemberOnPage($this->page, $this->getDraftMode());
                    if($highlighted_page){
                        $this->assign($params['assignhighlight'], $highlighted_page);
                    }
                }
                return $g->getMembers($this->getDraftMode());
            }else{
                // no file group with that name
                return $this->raiseError('No page group exists with the name \''.$name.'\'.');
            }
            
            break;
            
            case "usergroup":
            case "user_group":
            
            $g = new SmartestUserGroup;
            
            if($g->findBy('name', $name, $this->page->getSiteId())){
                return $g->getMembers($this->getDraftMode());
            }else{
                echo $this->raiseError('No user group exists with the name \''.$name.'\'.');
            }
                
            break;
            
            case "set_feed_Items":
            
            $set = new SmartestCmsItemSet;
            
            if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h($name)){
                if($set->isAggregable()){
                    
                    if(isset($params['limit']) && is_numeric($params['limit'])){
                        
                        $limit = $params['limit'];
                        $items = $set->getFeedItems();
                        
                        if(is_array($items)){
                            return array_slice($items, 0, $limit);
                        }else{
                            return array();
                        }
                        
                    }else{
                        return $set->getFeedItems();
                    }
                    
                }else{
                    return $this->raiseError("Data set with name '".$name."' does not have feed properties.");
                }
            }
            
            break;
            
            case "instagram":
            $oh = new SmartestAPIServicesHelper;
            
            if($acct = SmartestOAuthRateLimitDistributionHelper::getAccountForService('instagram')){
                
                $ih = new SmartestInstagramHelper;
                $ih->assignClientAccount($acct);
                
                if($user = $ih->getUserFromUsername($name)){
                    return $ih->getUserFeed($name, 20);
                }else{
                    echo $this->raiseError("Instagram user with name '@".$name."' does not exist.");
                    return;
                }
            
            }else{
                echo $this->raiseError("No authenticated Instagram account is available to make this request.");
            }
            
            break;
            
            case "set":
            case "dataset":
            default:
                
                if(isset($params['query_vars'])){
                    $query_vars = SmartestStringHelper::parseNameValueString($params['query_vars']);
                }else{
                    $query_vars = array();
                }
                
                $set = new SmartestCmsItemSet;
                
                if(isset($params['limit']) && is_numeric($params['limit'])){
                    $limit = $params['limit'];
                }else{
                    $limit = null;
                }
                
                if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h((string) $name)){
        		    
        		    $dah = new SmartestDataAppearanceHelper;
                    $dah->setDataSetAppearsOnPage($set->getId(), $this->getPage()->getId());
                    $start = (isset($params['start']) && is_numeric($params['start'])) ? $params['start'] : 1;
                    
                    $set_mode = $this->getDraftMode() ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT ;
        		    // $items = $set->getMembers($set_mode, $limit, $start, $query_vars);
        		    $items = $set->getMembersPaged($set_mode, $limit, $start, $query_vars, $this->page->getSiteId());
        		    
        		}else if(preg_match('/^all_/', $name)){
        		    $model_varname = substr($name, 4);
        		    // TODO: if the set name is not found, but begins with 'all_', get all the items of the model whose name follows, if any
        		}else{
        		    $items = array();
        		}
                
                // $this->caching = true;
         		return $items;
         		
        }
 		
    }
    
    public function getDataSetItemsByName($name){
        
        $set = new SmartestCmsItemSet;
        
        if($set->findBy('name', $name, $this->page->getSiteId()) || $this->getDataSetsHolder()->h($name)){
		    
		    $set_mode = $this->getDraftMode() ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT ;
		    $items = $set->getMembersPaged($set_mode, null, 0, $query_vars, $this->page->getSiteId());
		    return $items;
		    
		}else{
		    
		    $this->raiseError("Data set with name '".$name."' could not be found.");
		    return array();
		    
		}
        
    }
    
    public function renderAssetById($asset_id, $params, $path='none'){
        
        if(strlen($asset_id) && SmartestStringHelper::toRealBool($asset_id)){
            
            if(is_numeric($asset_id)){
                $hydrateField = 'id';
            }else{
                $hydrateField = 'stringid';
            }
            
            $asset = new SmartestRenderableAsset;
            
            if($asset->hydrateBy($hydrateField, $asset_id)){
                
                $render_data = array();
                
                if($asset->isImage()){
                    $render_data['width'] = $asset->getWidth();
                    $render_data['height'] = $asset->getHeight();
                }
                
                foreach($params as $key => $value){
                    if($key != 'name'){
	                    if(isset($params[$key])){
        	                $render_data[$key] = $value;
        	            }else{
        	                if(!isset($render_data[$key])){
        	                    $render_data[$key] = '';
    	                    }
        	            }
    	            }
	            }
	            
	            $asset->setAdditionalRenderData($render_data, true);
                return $asset->render($this->getDraftMode());
                
            }else{
                
                return $this->raiseError("No asset found with ID or Name: ".$asset_id);
                
            }

        }else{
            
            return $this->raiseError("Could not render asset. Neither of attributes 'id' and 'name' are properly defined.");
            
        }
    }
    
    /* public function _renderAssetObject($asset, $params, $render_data='', $path='none'){
        
        $sm = new SmartyManager('AssetRenderer');
        $r = $sm->initialize($asset->getStringId());
        $r->assignAsset($asset);
        $r->setDraftMode($this->getDraftMode());
        // $content = $r->render($params, $render_data, $path);
        return $r->render($params, $render_data, $path);
        
    } */
    
    public function _renderAssetObject($asset, $markup_params, $render_data='', $path='none'){
        
        $asset_type_info = $asset->getTypeInfo();
        $render_template = SM_ROOT_DIR.$asset_type_info['render']['template'];
        
        if(!is_array($render_data) && !$render_data instanceof SmartestParameterHolder){
            $render_data = array();
        }
        
        if(isset($path)){
            $path = (!in_array($path, array('file', 'full'))) ? 'none' : $path;
        }else{
            $path = 'none';
        }
        
        if(file_exists($render_template)){
            
            $asset->setAdditionalRenderData($render_template);
            $content = $asset->render($this->getDraftMode());
            
        }else{
            $content = $this->raiseError("Render template '".$render_template."' not found.");
        }
        
        return $content;
        
    }
    
    public function renderItemPropertyValue($params){
        
        if(isset($params['path'])){
            $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
        }else{
            $path = 'none';
        }
        
        if(isset($params["name"]) && strlen($params["name"])){
            
            $property_name_parts = explode(':', $params["name"]);
            $requested_property_name = $property_name_parts[0];
            array_shift($property_name_parts);
            $display_type = (isset($property_name_parts[0]) && strlen($property_name_parts[0])) ? implode(':', $property_name_parts) : null;
            $params['_display_type'] = $display_type;
            
        }else{
            return $this->raiseError("&lt;?sm:property:?&gt; tag missing required 'name' attribute.");
        }
            
            
        // for rendering the properties of the principal item of a meta-page
        if(!isset($params['context']) || isset($params['principal_item'])){
        
            if(is_object($this->page) && $this->page instanceof SmartestItemPage){
                
                if(is_object($this->page->getPrincipalItem())){
                    
                    if(in_array($requested_property_name, $this->page->getPrincipalItem()->getModel()->getPropertyVarNames())){
                    
                        $lookup = $this->page->getPrincipalItem()->getModel()->getPropertyVarNamesLookup();
                        $property = $this->page->getPrincipalItem()->getPropertyByNumericKey($lookup[$requested_property_name]);
                        $property_type_info = $property->getTypeInfo();
                        
                        $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];
                        
                        if(is_file($render_template)){
                
                            if($this->getDraftMode()){
                                $value = $property->getData()->getDraftContent();
                            }else{
                                $value = $property->getData()->getContent();
                            }
                            
                            // TODO: It's more direct to do this, though not quite so extensible. We can update this later.
                            if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                
                                foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                    $params[$key] = $param_value;
                                }
                                
                                if(is_object($value)){
                                    // return $this->_renderAssetObject($value, $params, $params, $path);
                                    $value->setAdditionalRenderData($params);
                                    return $value->render($this->getDraftMode());
                                }else{
                                    return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$this->page->getPrincipalItem()->getId());
                                }
                                
                            }else{
                                $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                            }
                    
                        }else{
                            return $this->raiseError("Render template '".$render_template."' is missing.");
                        }
                        
                    }else if($requested_property_name == "name"){
                        return new SmartestString($this->page->getPrincipalItem()->getName());
                    }else{
                        return $this->raiseError("Unknown Property: ".$requested_property_name);
                    }
                }else{
                    return $this->raiseError("Page Item failed to build.");
                }
            }else{
                
                return $this->raiseError("&lt;?sm:property:?&gt; tag used on static page.");
                
            }
        
        // for rendering the properties of an item loaded using item spaces
        }else if(isset($params['context']) && ($params['context'] == 'itemspace' || $this->_context == SM_CONTEXT_ITEMSPACE_TEMPLATE)){
            
            // you have to tell it which itemspace you are referring to
            if(isset($params['itemspace']) && strlen($params['itemspace'])){
                
                // print_r($this->getPage()->getItemSpaceDefinitionNames());
                
                if($this->getPage()->hasItemSpaceDefinition($params['itemspace'], $this->getDraftMode())){
		
		            $def = $this->getPage()->getItemSpaceDefinition($params['itemspace'], $this->getDraftMode());
                    $object = $def->getItem(false, $this->getDraftMode());
                    
                    if(is_object($object)){
                        
                        if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){

                            $lookup = $object->getModel()->getPropertyVarNamesLookup();
                            $property = $object->getPropertyByNumericKey($lookup[$requested_property_name]);
                            $property_type_info = $property->getTypeInfo();
                            
                            $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];

                            if(is_file($render_template)){

                                if($this->getDraftMode()){
                                    $value = $property->getData()->getDraftContent();
                                }else{
                                    $value = $property->getData()->getContent();
                                }
			                    
			                    if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                                    
                                     foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                        $params[$key] = $param_value;
                                     }
                                    
                                    if(is_object($value)){
                                        // return $this->_renderAssetObject($value, $params, $params, $path);
                                        $value->setAdditionalRenderData($params);
                                        $value->render($this->getDraftMode());
                                    }else{
                                        return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                                    }
                                    
                                    
                                }else{
                                    $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                                }

                            }else{
                                return $this->raiseError("Render template '".$render_template."' is missing.");
                            }
                            
                        }else if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){
                            
                            // $array = $object->__toArray(true);
                            // return $array[$requested_property_name];
                            return $object->getPropertyValueByVarName($requested_property_name);
                            
                        }else if($requested_property_name == "name"){
                            return new SmartestString($object->getName());
                        }else{
                            
                            return $this->raiseError("Unknown Property: ".$requested_property_name);
                            
                        }
                        
                    }else{
                        
                        // item space is not defined
                        return $this->raiseError("&lt;?sm:property:?&gt; tag used in itemspace context, but itemspace '".$params['itemspace']."' has no object.");
                        
                    }

                }else{

                    // item space is not defined
                    // return $this->raiseError("Itemspace '".$params['itemspace']."' not defined yet.");
                    if($this->getDraftMode()){
                        echo "Itemspace '".$params['itemspace']."' not defined yet.";
                    }

                }
                
            }else{
                return $this->raiseError("&lt;?sm:property:?&gt; tag must have itemspace=\"\" attribute when used in itemspace context.");
            }
        
        // for rendering the properties of an item in a list
        }else if(isset($params['context']) && ($params['context'] == 'other') && isset($params['item'])){
            
            $object = $params['item'];
                    
            if(is_object($object)){
                        
                if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){

                    $lookup = $object->getModel()->getPropertyVarNamesLookup();
                    $property = $object->getPropertyByNumericKey($lookup[$requested_property_name]);
                    $property_type_info = $property->getTypeInfo();
                    
                    $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];

                    if(is_file($render_template)){

                        if($this->getDraftMode()){
                            $value = $property->getData()->getDraftContent();
                        }else{
                            $value = $property->getData()->getContent();
                        }
	                    
	                    if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                            
                            foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                $params[$key] = $param_value;
                            }
                            
                            if(is_object($value)){
                                $value->setAdditionalRenderData($params);
                                return $value->render($this->getDraftMode());
                            }else{
                                return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }

                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                }else if(in_array($requested_property_name, $object->getModel()->getPropertyVarNames())){
                    
                    // $array = $object->__toArray(true);
                    // return $array[$requested_property_name];
                    return $object->getPropertyValueByVarName($requested_property_name);
                    
                }else if($requested_property_name == "name"){
                    return new SmartestString($object->getName());
                }else{
                    
                    return $this->raiseError("Unknown Property: ".$requested_property_name);
                    
                }
                
            }else{
                
                // $object is not an object
                // if($this->getDraftMode()){
                    return $this->raiseError("Item is not an object");
                // }
                
            }

        
        // for rendering the properties of an item in a list
        }else if(isset($params['context']) && ($params['context'] == 'repeat' || $params['context'] == 'list')){
            
            if(is_object($this->_tpl_vars['repeated_item_object'])){
                
                if(in_array($requested_property_name, $this->_tpl_vars['repeated_item_object']->getPropertyVarNames())){
                
                    $lookup = $this->_tpl_vars['repeated_item_object']->getModel()->getPropertyVarNamesLookup();
                    $property = $this->_tpl_vars['repeated_item_object']->getPropertyByNumericKey($lookup[$requested_property_name]);
                    $property_type_info = $property->getTypeInfo();
                
                    $render_template = SM_ROOT_DIR.$property_type_info['render']['template'];
                    
                    if(is_file($render_template)){
                    
                        if($this->getDraftMode()){
                            $value = $property->getData()->getDraftContent();
                        }else{
                            $value = $property->getData()->getContent();
                        }
                        
                        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
                            
                            foreach($property->getData()->getInfo($this->getDraftMode()) as $key=>$param_value){
                                $params[$key] = $param_value;
                            }
                            
                            if(is_object($value)){
                                $value->setAdditionalRenderData($params);
                                return $value->render($this->getDraftMode());
                            }else{
                                return $this->_comment('No asset selected for property: '.$property->getVarname().' on item ID '.$object->getId());
                            }
                            
                        }else{
                            $this->run($render_template, array('raw_value'=>$value, 'render_data'=>$params));
                        }
                        
                    }else{
                        return $this->raiseError("Render template '".$render_template."' is missing.");
                    }
                    
                
                }else if($requested_property_name == "name"){
                    return new SmartestString($this->_tpl_vars['repeated_item_object']->getName());
                }else{
                    return $this->raiseError("Unknown Property: ".$requested_property_name.".");
                }
                
            }else{
                
                return $this->raiseError("Repeated item is not an object.");
                
            }
            
        }
        
    }
    
	public function renderSiteMap($params){
	    
	    $pagesTree = $this->page->getSite()->getPagesTree(true);
	    $this->_tpl_vars['site_tree'] = $pagesTree;
	    $file = SM_ROOT_DIR."Presentation/Special/sitemap.tpl";
	    $this->run($file, array());

	}
	
	public function renderGoogleAnalyticsTags($params){
	    
	    if($this->getDraftMode()){
	        
	        if(isset($params['id'])){
	            return '<!--On a live page, Google Analytics will be placed here ('.$params['id'].')-->';
            }
	    
        }else{
	        
            if(isset($params['id'])){
                return '<!--SM_GA_TAG:ID='.$params['id'].'-->';
    	    }else{
    	        return $this->raiseError("Google Analytics ID not supplied.");
    	    }
        }
	}
	
	public function renderIframe($params){
	    
	    if(isset($params['url'])){
	    
	        $allowed_iframe_attributes = array('width', 'height', 'id', 'class', 'style');
        
            $iframe_render_data = array("src"=>$params['url']);
        
            foreach($params as $name => $value){
                if(in_array($name, $allowed_iframe_attributes)){
                    $iframe_render_data[$name] = $value;
                }
            }
        
            $iframe_attributes = SmartestStringHelper::toAttributeString($iframe_render_data);
        
            $render_process_id = SmartestStringHelper::toVarName('iframe_'.$params['url'].'_'.substr(microtime(true), -6));
            $child = $this->startChildProcess($render_process_id);
            $child->assign('iframe_attributes', $iframe_attributes);
            $child->setContext(SM_CONTEXT_DYNAMIC_TEXTFRAGMENT);
            $content = $child->fetch($file);
            $this->killChildProcess($child->getProcessId());
            return $content;
        
        }else{
            
            return $this->raiseError("Could not build iframe. The required 'url' parameter was not specified.");
            
        }
        
	}
	
	public function assignSingleItemTemplate($tpl_path){
        $this->_single_item_template_path = $tpl_path;
    }
    
    public function renderSingleItemTemplate(){
        
        ob_start();
        $this->run($this->_single_item_template_path, array());
        $content = ob_get_contents();
        ob_end_clean();
        
        if($this->_request_data->g('action') == "renderEditableDraftPage" && $path == 'none' && $show_preview_edit_link){
		    
		    if(isset($asset_type_info['editable']) && SmartestStringHelper::toRealBool($asset_type_info['editable'])){
		        $edit_link .= "<a class=\"sm-edit-button\" title=\"Click to edit file: ".$this->_asset->getUrl()." (".$this->_asset->getType().")\" href=\"".$this->_request_data->g('domain')."assets/editAsset?asset_id=".$this->_asset->getId()."&amp;from=pagePreview\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".$this->_request_data->g('domain')."Resources/System/Images/edit-pencil-red.png\" alt=\"edit\" style=\"display:inline;border:0px;width:16px;height:16px\" /><!-- Swap this asset--></a>";
		    }else{
		        $edit_link = "<!--edit link-->";
	        }
	    
        }
	    
	    $content .= $edit_link;
	    
	    return $content;
        
    }
	
	/* public function getListData($listname){
		$result = $this->templateHelper->getList($listname);
		return $result;
	}
	
	public function getList($listname){
		
		$result = $this->getListData($listname);
		$header="ListItems/".$result['header'];
		$footer="ListItems/".$result['footer'];
		$items=$result['items'];
		$tpl_filename="ListItems/".$result['tpl_name'];
		
		if($result['header']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['header'])){
			$header = "ListItems/".$result['header'];
			$this->run($header, array());
		}
		
		if (is_array($items)){ 
		
			foreach ($items as $item){
 				$item_name=$item['item_name'];
				$properties=$item['property_details'];	
				$this->assign('name', $item_name);
				$this->assign('properties', $properties);
				$this->run($tpl_filename, array());
			}
			
		}
		
		if($result['footer']!="" && is_file(SM_ROOT_DIR."Presentation/ListItems/".$result['footer'])){
			$footer="ListItems/".$result['footer'];
			$this->run($footer, array());
		}
		
		return $result['html'];
	} */
	
	/* public function getLink($params){
		return $this->templateHelper->getLink($params);
	}
	
	public function getImage($params){
		return $this->templateHelper->getImage($params);
	}
	
	public function getStylesheet($params){
		return $this->templateHelper->getStylesheet($params);
	}
	
	public function getImagePath($params){
		return $this->templateHelper->getImagePath($params);
	} */
    
}