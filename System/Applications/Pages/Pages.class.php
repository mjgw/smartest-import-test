<?php

/**
 *
 * PHP versions 4,5
 *
 * @category   WebApplication
 * @package    Smartest
 * @subpackage Pages
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 * @author     Eddie Tejeda <eddie@visudo.com>
 */
 
// include_once "Managers/AssetsManager.class.php";
// include_once "Managers/SetsManager.class.php";
// include_once "Managers/TemplatesManager.class.php";
// include_once "System/Applications/MetaData/MetaDataManager.class.php";

class Pages extends SmartestSystemApplication{
	
	// protected $setsManager;
	// protected $templatesManager;
	// protected $propertiesManager;
	
	protected function __smartestApplicationInit(){
	    // $this->setsManager = new SetsManager;
	    // $this->templatesManager = new TemplatesManager;
	    // $this->propertiesManager = new PagePropertiesManager;
	}
	
	public function startPage(){
		// No code is needed here, just a function definition
		$this->setTitle("Welcome to Smartest");
	}
	
	public function openPage($get){
	    
	    if($this->getRequestParameter('page_id')){
	        
	        $page = new SmartestPage;
	        
	        if($page->smartFind($this->getRequestParameter('page_id'))){
	            
	            $page->setDraftMode(true);
                
                if($this->getUser()->hasToken('modify_page_properties')){
	            
	                if($page->getIsHeld() && $page->getHeldBy() && $page->getHeldBy() != $this->getUser()->getId() && !$this->getUser()->hasToken('edit_held_pages')){
    	                
    	                // page is already being edited by another user
    	                $editing_user = new SmartestUser;
	                    
    	                if($editing_user->hydrate($page->getHeldBy())){
    	                    $this->addUserMessageToNextRequest($editing_user->__toString().' is already editing this page.', SmartestUserMessage::ACCESS_DENIED);
    	                }else{
    	                    $this->addUserMessageToNextRequest('Another user is already editing this page.', SmartestUserMessage::ACCESS_DENIED);
    	                }
	                
    	                $this->redirect('/smartest/pages');
    	                
    	            }else{
	                    
	                    // page is available to edit
			            SmartestSession::set('current_open_page', $page->getId());
			            
			            if($page->getIsHeld() && $this->getUser()->hasToken('edit_held_pages') && $page->getHeldBy() != $this->getUser()->getId()){
			                
			                $editing_user = new SmartestUser;
                            
                            if($editing_user->hydrate($page->getHeldBy())){
        	                    $this->addUserMessageToNextRequest('Careful: '.$editing_user->__toString().' has not yet released this page.', SmartestUserMessage::INFO);
        	                }else{
        	                    $this->addUserMessageToNextRequest('Careful: another user has not yet released this page.', SmartestUserMessage::INFO);
        	                }
        	                
			            }else{
			                // lock it against being edited by other people
    			            $page->setIsHeld(1);
    			            $page->setHeldBy($this->getUser()->getId());
    			            $page->save();
			            
    			            /* if(!$this->getUser()->hasTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId())){
    			                $this->getUser()->assignTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId(), 0);
    		                } */
	                    }
			            
			            $page->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
        			    $this->getUser()->addRecentlyEditedPageById($page->getId(), $this->getSite()->getId());
		            
			            // $this->redirect('/'.$this->getRequest()->getModule().'/editPage?page_id='.$page->getWebid());
                        $this->redirect('@websitemanager:basic_info?page_id='.$page->getWebid());
    			        
    		        }
		        
	            }else{
	                
	                $this->addUserMessageToNextRequest('You don\'t have permission to edit pages.', SmartestUserMessage::ACCESS_DENIED);
	                
	                if(SmartestSession::hasData('current_open_project')){
	                    $this->redirect('/smartest/pages');
                    }else{
                        $this->redirect('/smartest');
                    }
	                
	            }
		        
		    }else{
                $this->redirect('/smartest');
		    }
		}
	}
	
	public function closeCurrentPage($get){
	    
	    if($this->getRequestParameter('release') && $this->getRequestParameter('release') == 1){
	        $page = new SmartestPage;
	        
	        if($page->hydrate(SmartestSession::get('current_open_page'))){
	            $page->setIsHeld(0);
	            $page->setHeldBy('');
	            $page->save();
	        }
	    }
	    
	    SmartestSession::clear('current_open_page');
	    $this->redirect('/smartest/pages');
	}
	
	public function releasePage($get){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	        
	        $page->setDraftMode(true);
	        
	        if($page->getIsHeld() == '1'){
	            
	            if($page->getHeldBy() == $this->getUser()->getId()){
                    
                    $page->setIsHeld(0);
                    $page->setHeldBy('');
                    $page->save();
                    $this->addUserMessageToNextRequest("The page has been released.", SmartestUserMessage::SUCCESS);
                    
                    if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId())){
		                $todo->complete();
	                }
	                
                }else{
                    //  the page is being edited by another user
                    $this->addUserMessageToNextRequest("You can't release this page because another user is editing it.", SmartestUserMessage::INFO);
                }
            }else{
                $this->addUserMessageToNextRequest("The page has been released.", SmartestUserMessage::SUCCESS);
                // $this->addUserMessageToNextRequest("The page is not currently held by any user.", SmartestUserMessage::INFO);
            }
            
        }
	    
	    // SmartestSession::clear('current_open_page');
	    
	    if($this->getRequestParameter('from') && $this->getRequestParameter('from') == 'todoList'){
	        $this->redirect('/smartest/todo');
        }else{
            SmartestSession::clear('current_open_page');
            $this->redirect('/smartest/pages');
        }
	}
	
	public function clearPagesCache(){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if($this->getUser()->hasToken('clear_pages_cache')){
            
                /* $page_prefix = 'site'.$this->getSite()->getId().'_';
            
                $cache_files = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/');
            
                if(is_array($cache_files)){
                
                    $deleted_files = array();
                    $failed_files = array();
                    $untouched_files = array();
                
                    foreach($cache_files as $f){
                    
                        $path = SM_ROOT_DIR.'System/Cache/Pages/'.$f;
                    
                        if(strlen($f) && $page_prefix == substr($f, 0, strlen($page_prefix))){
                            // echo "deleting ".$path.'...<br />';
                            if(@unlink($path)){
                                $deleted_files[] = $f;
                            }else{
                                $failed_files[] = $f;
                            }
                        }else{
                            $untouched_files = $f;
                        }
                    }
                    
                    SmartestLog::getInstance('site')->log("{$this->getUser()} cleared the pages cache. ".count($deleted_files)." files were removed.", SmartestLog::USER_ACTION);
                    
                    $this->send(true, 'show_result');
                    $this->send($deleted_files, 'deleted_files');
                    $this->send(count($deleted_files), 'num_deleted_files');
                    $this->send($failed_files, 'failed_files');
                    $this->send($untouched_files, 'untouched_files');
                    $this->send(SM_ROOT_DIR.'System/Cache/Pages/', 'cache_path');
                
                }else{
                
                    $this->send(false, 'show_result');
                
                } */
            
            }else{
                
                SmartestLog::getInstance('site')->log("{$this->getUser()} tried to cleared the pages cache but did not have permission.", SmartestLog::ACCESS_DENIED);
                $this->addUserMessageToNextRequest('You don\'t have permission to clear the page cache for this site.', SmartestUserMessage::ACCESS_DENIED);
                $this->redirect('/smartest/pages');

            }
            
        }else{
            $this->addUserMessageToNextRequest('No site selected.', SmartestUserMessage::ERROR);
            $this->redirect('/smartest');
        }
	    
	}
  
	public function editPage($get){
		
		// $this->addUserMessage('This is a really long test message with more than one line of text.');
		// $this->addUserMessage('You are on thin ice, Mr. Gilroy-Ware.');
		
		if(!$this->requestParameterIsSet('from')){
		    // $this->setFormReturnUri();
		}
		
		$page_webid = $this->getRequestParameter('page_id');
		
        $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
		if(isset($type_index[$page_webid])){
		    if(($type_index[$page_webid] == 'ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_DATASET') && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->smartFind($page_webid)){
    	    
    	    if($page->getDeleted() == 'TRUE'){
                $this->send(true, 'show_deleted_warning');
            }
            
            $this->send($this->getSite()->pageIdIsSpecial($page->getId()), 'is_special_page');
            
            $page->setDraftMode(true);
    	    
    	    if(($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET') && (!$this->getRequestParameter('item_id') || !is_numeric($this->getRequestParameter('item_id')))){
            
                $this->send(true, 'allow_edit');
            
                $model = new SmartestModel;
                
                if($model->hydrate($page->getDatasetId())){
                    $items = $model->getSimpleItems($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send($page, 'page');
                    $this->send(true, 'require_item_select');
                    $this->send('Please choose an item to continue editing.', 'chooser_message');
                    $this->send('websitemanager/editPage', 'continue_action');
                }
                
                $this->send($this->getUser()->hasToken('edit_page_name'), 'allow_edit_page_name');
                $editable = $page->isEditableByUserId($this->getUser()->getId());
        		$this->send($editable, 'page_is_editable');
        		$parent_pages = $page->getOkParentPages();
        		$this->send($parent_pages, "parent_pages");
        		$this->send($this->getSite(), "site");
        		$this->send(new SmartestBoolean(false), 'link_urls');
            
            }else{
    	        
    	        if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
    	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    	                $page->setPrincipalItem($item);
    	                $this->send($item, 'item');
    	            }
	            }else{
	                $this->send($page->getTags(), 'page_tags');
	            }
    	        
    	        $this->send(false, 'require_item_select');
    	        $editorContent = $page;
    	        $this->send(new SmartestBoolean(true), 'link_urls');
                
                if($this->getUser()->hasToken('modify_page_properties')){
		
            		$site_id = $this->getSite()->getId();
            		$page_id = $page->getId();
		
            		$ishomepage = ($this->getSite()->getTopPageId() == $page->getId());
            			
		            $parent_pages = $page->getOkParentPages();
                    // echo "Parent pages set here";
                    // var_dump($parent_pages[0]);
    		        
            		if($page->getIsHeld() == '1' && $page->getHeldBy() == $this->getUser()->getId()){
            		    $allow_release = true;
            		}else{
            		    $allow_release = false;
            		}
        		
            		$this->send($allow_release, 'allow_release');
            		$this->send($this->getUser()->hasToken('edit_page_name'), 'allow_edit_page_name');
            		$editable = $page->isEditableByUserId($this->getUser()->getId());
            		$this->send($editable, 'page_is_editable');
		            
    		        $available_icons = $page->getAvailableThumbnailImages();
    		        
    		        $this->send($available_icons, 'available_icons');
		        
            		if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
                
                        $model = new SmartestModel;
                        
                        if($model->find($page->getDatasetId())){
                            $editorContent['model_name'] = $model->getName();
                            
                            if($page->getParent() && ($type_index[$page->getParent()] == 'ITEMCLASS' || $type_index[$page->getParent()] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page->getParent()] == 'SM_PAGETYPE_DATASET')){
                                
                                $parent_indicator_properties = $model->getForeignKeyPropertiesForModelId($page->getParentPage(false)->getDatasetId(), (int) $this->getRequestParameter('item_id'));
                            
                                $this->send(true, 'show_parent_meta_page_property_control');
                                $this->send($model, 'model');
                                
                                if($page->getParentPage(false)->getDatasetId() == $page->getDatasetId()){
                                    
                                    // parent metapage has same model as this one
                                    $parent_model = &$model;
                                    $this->send(true, 'show_self_option');
                                    
                                }else{
                                    
                                    // quickly fetch parent meta-page's model
                                    $parent_model = new SmartestModel;
                                
                                    if($parent_model->hydrate($page->getParentPage(false)->getDatasetId())){
                                        
                                    }else{
                                        $this->addUserMessage("The parent of this page is a meta-page, but not linked to any existing model", SmartestUserMessage::WARNING);
                                    }
                                    
                                    $this->send(false, 'show_self_option');
                                    
                                }
                            
                                if(count($parent_indicator_properties) > 0){
                                    // there is a choice as to which property should be used to indicate which is the 'parent' item
                                    // convert to arrays and send to form
                                    
                                    $arrays = array();
                                    
                                    foreach($parent_indicator_properties as $p){
                                        
                                        $property_array = $p->__toArray();
                                        
                                        if($p instanceof SmartestItemPropertyValueHolder){
                                            
                                            $foreign_item = new SmartestItem;
                                            
                                            if($parent_indicator_properties[0]->getData()->getDraftContent() instanceof SmartestCmsItem){
                                                $property_array['selected_item_name'] = $parent_indicator_properties[0]->getData()->getDraftContent()->getName();
                                            // if($foreign_item->hydrate($p->getData()->getDraftContent())){
                                            //     $property_array['selected_item_name'] = $foreign_item->getName();
                                            }else{
                                                $property_array['selected_item_name'] = "Not Selected";
                                            }

                                        }else{
                                            $property_array['selected_item_name'] = "Unknown";
                                        }
                                        
                                        $arrays[] = $property_array;
                                        
                                    }
                                    
                                    $this->send($page->getParentMetaPageReferringPropertyId(), 'parent_data_source_property_id');
                                    $this->send('dropdown', 'parent_mpp_control_type');
                                    $this->send($arrays, 'parent_meta_page_property_options');
                                    
                                    /* }else if(count($parent_indicator_properties) > 0){
                                    
                                    // the parent meta-page must be defined by a single foreign-key property of the model of this meta-page.
                                    // Display it, but there is no choice.
                                    
                                    if(!$page->getParentMetaPageReferringPropertyId()){
                                        $page->setParentMetaPageReferringPropertyId($parent_indicator_properties[0]->getId());
                                    }
                                    
                                    $this->send('text', 'parent_mpp_control_type');
                                    $property_array = $parent_indicator_properties[0]->__toArray();
                                    
                                    if($parent_indicator_properties[0] instanceof SmartestItemPropertyValueHolder){
                                        
                                        $foreign_item = new SmartestItem;
                                        
                                        // print_r($parent_indicator_properties[0]->getData()->getDraftContent());
                                        
                                        if($parent_indicator_properties[0]->getData()->getDraftContent() instanceof SmartestCmsItem){
                                            $property_array['selected_item_name'] = $parent_indicator_properties[0]->getData()->getDraftContent()->getName();
                                        // if($foreign_item->hydrate()){
                                            // $property_array['selected_item_name'] = $foreign_item->getName();
                                        }else{
                                            $property_array['selected_item_name'] = "Not Selected";
                                        } 
                                        
                                    }else{
                                        $property_array['selected_item_name'] = "Unknown";
                                    }
                                    
                                    $this->send($property_array, 'parent_meta_page_property'); */
                                    
                                }else{
                                    
                                    // there are no properties in this meta-page that point to the data type of the parent meta-page. this is a problem so we notify the user.
                                    if($page->getParentPage(false)->getDatasetId() == $page->getDatasetId()){
                                        $this->addUserMessage("This ".$model->getName()." meta-page is the child of a meta-page that is also used to represent ".$model->getPluralName().", but the ".$model->getName()." model has no foreign-key properties that refer to other ".$model->getPluralName().". This page will assign its own item to its parent meta-page.", SmartestUserMessage::WARNING, true);
                                        $page->setParentMetaPageReferringPropertyId('_SELF');
                                        $this->send('_SELF', 'parent_meta_page_property');
                                        $this->send('text', 'parent_mpp_control_type');
                                    }else{
                                        $this->addUserMessage("This ".$model->getName()." meta-page is the child of a meta-page used for model ".$parent_model->getName().", but the ".$model->getName()." model (that this page refers to) has no foreign-key properties that refer to ".$parent_model->getPluralName().".", SmartestUserMessage::WARNING);
                                    }
                                    
                                }
                                
                            }
                            
                        }else{
                            
                            $this->addUserMessage("This page is a meta-page, but not linked to any existing model", SmartestUserMessage::WARNING);
                            
                        }
                        
                    }elseif($page->getId() == $this->getSite()->getTagPageId()){
                        
                        if($this->getRequestParameter('tag')){
                            
                            $tag = new SmartestTag;
                            
                            if($tag->findBy('name', $this->getRequestParameter('tag'))){
                                
                                // $this->send(true, 'show_iframe');
                		        // $this->send($domain, 'site_domain');
                		        // $this->setTitle('Page Preview | Tag | '.$tag->getLabel());
                		        // $this->send(false, 'show_edit_item_option');
                                // $this->send(false, 'show_publish_item_option');
                                // $this->send(false, 'show_item_list');
                                // $this->send(false, 'show_tag_list');
                                // $this->send(false, 'show_search_box');
                                // $this->send(false, 'show_author_list');
                                // $preview_url .= '&amp;tag_name='.$tag->getName();
                                
                                /* if($this->requestParameterIsSet('model_id')){
                                    $preview_url .= '&amp;model_id='.$this->getRequestParameter('model_id');
                                } */
                                
                        		// $du = new SmartestDataUtility;
                        		// $models = $du->getModelsWithMetapageOnSiteId($this->getSite()->getId());
                                // $this->send($models, 'models');
                                
                                $this->send($tag, 'tag');
                                $this->send(true, 'is_tag_page');
                                $this->send($this->getUser()->hasToken('edit_tags'), 'allow_tag_edit');
                                
                            }else{
                                // tag does not exist - require tag select
                                // $this->send('The selected tag does not exist. Please choose another tag to preview on this page.', 'chooser_message');
                                // $this->send(false, 'show_item_list');
                                // $this->send(true, 'show_tag_list');
                                // $this->send(false, 'show_search_box');
                                // $this->send(false, 'show_author_list');
                                // $this->send('websitemanager/preview', 'continue_action');
                                // $du  = new SmartestDataUtility;
                	            // $tags = $du->getTags();
                	            // $this->send($tags, 'tags');
                            }
                            
                        }else{
                            // require tag select
                            // $this->send('Please choose a tag to preview on this page.', 'chooser_message');
                            // $this->send(false, 'show_item_list');
                            // $this->send(true, 'show_tag_list');
                            // $this->send(false, 'show_search_box');
                            // $this->send(false, 'show_author_list');
                            // $this->send('websitemanager/preview', 'continue_action');
                            // $du  = new SmartestDataUtility;
            	            // $tags = $du->getTags();
            	            // $this->send($tags, 'tags');
                        }
                        
                    }
                
            		$this->setTitle("Edit Page | ".$page->getTitle());
    		
            		$this->send($editorContent, "page");
            		$this->send($parent_pages, "parent_pages");
            		$this->send($ishomepage, "ishomepage");
            		$this->send($this->getSite(), "site");
            		$this->send(true, 'allow_edit');
		
        	    }else{
	        
        	        $this->addUserMessageToNextRequest('You don\'t have permission to modify page properties.', SmartestUserMessage::ACCESS_DENIED);
        	        $this->redirect('/smartest/pages');
        	        $this->send($editorContent, "pageInfo");
        	        $this->send(false, 'allow_edit');
	        
        	    }
        	    
        	}
	    
        }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized.', SmartestUserMessage::ERROR, true);
            $this->redirect("/smartest/pages");
        }
		
	}
	
	function approvePageChanges($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
        $page = new SmartestPage;
        
        if($page->smartFind($page_webid)){
	    
	        if($this->getUser()->hasToken('approve_page_changes')){
	        
	            $page->setChangesApproved(1);
	            $this->addUserMessageToNextRequest("The changes to this page have been approved.", SmartestUserMessage::SUCCESS);
	            $page->save();
	        
	        }else{
	            $this->addUserMessageToNextRequest("You don't have sufficient permissions to approve pages.", SmartestUserMessage::ACCESS_DENIED);
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function addPlaceholder($get){
		
		$h = new SmartestAssetClassesHelper;
		$asset_class_types = $h->getTypes();
		
		$placeholder_name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_name'));
		$selected_type = ($this->getRequestParameter('placeholder_type') && in_array($this->getRequestParameter('placeholder_type'), $h->getTypeCodes())) ? $this->getRequestParameter('placeholder_type') : '';
		$label = ($this->getRequestParameter('placeholder_label') && strlen($this->getRequestParameter('placeholder_label'))) ? $this->getRequestParameter('placeholder_label') : SmartestStringHelper::toTitleCaseFromVarName($placeholder_name);
		
		if(!$this->requestParameterIsSet('placeholder_type')){
		
    		if(strpos($placeholder_name, 'img') !== false || strpos($placeholder_name, 'image') !== false || strpos($placeholder_name, 'banner') !== false || strpos($placeholder_name, 'graphic') !== false || strpos($placeholder_name, 'picture') !== false){
    		    $suggested_type = 'SM_ASSETCLASS_STATIC_IMAGE';
    		    $this->send(true, 'type_suggestion_automatic');
                $a = new SmartestArray($h->getAssetTypeNamesFromAssetClassType($suggested_type));
                $this->send($a->getSummary(200), 'types_list');
    		}else if(strpos($placeholder_name, 'css') !== false || strpos($placeholder_name, 'stylesheet') !== false){
    		    $suggested_type = 'SM_ASSETCLASS_STYLESHEET';
    		    $this->send(true, 'type_suggestion_automatic');
                $a = new SmartestArray($h->getAssetTypeNamesFromAssetClassType($suggested_type));
                $this->send($a->getSummary(200), 'types_list');
    		}else if(strpos($placeholder_name, '_js') !== false || strpos($placeholder_name, 'javascript') !== false){
        		$suggested_type = 'SM_ASSETCLASS_JAVASCRIPT';
        		$this->send(true, 'type_suggestion_automatic');
                $a = new SmartestArray($h->getAssetTypeNamesFromAssetClassType($suggested_type));
                $this->send($a->getSummary(200), 'types_list');
        	}else if(strpos($placeholder_name, '_txt') !== false || strpos($placeholder_name, 'text') !== false || strpos($placeholder_name, 'txt') !== false || strpos($placeholder_name, 'quote') !== false){
            	$suggested_type = 'SM_ASSETCLASS_RICH_TEXT';
            	$this->send(true, 'type_suggestion_automatic');
                $a = new SmartestArray($h->getAssetTypeNamesFromAssetClassType($suggested_type));
                $this->send($a->getSummary(200), 'types_list');
        	}else if(strpos($placeholder_name, 'video') !== false || strpos($placeholder_name, 'movie') !== false || strpos($placeholder_name, 'clip') !== false){
            	$suggested_type = 'SM_ASSETCLASS_MOVIE';
            	$this->send(true, 'type_suggestion_automatic');
                $a = new SmartestArray($h->getAssetTypeNamesFromAssetClassType($suggested_type));
                $this->send($a->getSummary(200), 'types_list');
            }else{
        	    $suggested_type = null;
        	    $this->send(false, 'type_suggestion_automatic');
                $this->send('', 'types_list');
        	}
    	
	    }else{
	        $suggested_type = null;
	        $this->send(false, 'type_suggestion_automatic');
            $this->send('', 'types_list');
	    }
    	
    	if($selected_type){
		    $groups = $h->getAssetGroupsForPlaceholderType($selected_type, $this->getSite()->getId());
            $final_type = $selected_type;
	    }else if($suggested_type){
	        $groups = $h->getAssetGroupsForPlaceholderType($suggested_type, $this->getSite()->getId());
            $final_type = $suggested_type;
	    }else{
	        $groups = array();
            $final_type = null;
	    }
		
		$this->send($suggested_type, 'suggested_type');
		$this->send($groups, 'groups');
		$this->send($label, 'label');
		$this->send($selected_type, 'selected_type');
		$this->send($placeholder_name, 'name');
		$this->send($asset_class_types, 'types');
        $this->send($final_type, 'final_type');
		
	}
	
	public function addContainer($get){
		
		$container_name = SmartestStringHelper::toVarName($this->getRequestParameter('name'));
		$container_label = SmartestStringHelper::toTitleCaseFromVarName($container_name);
		
		$tlh = new SmartestTemplatesLibraryHelper;
		$groups = $tlh->getTemplateGroups('SM_ASSETTYPE_CONTAINER_TEMPLATE', $this->getSite()->getId());
        
        $this->send(new SmartestArray($groups), 'groups');
        $this->send($container_label, 'label');
		$this->send($container_name, 'name');
		$this->send($asset_class_types, 'types');
		
	}
	
	public function insertPlaceholder($get, $post){
		
		$placeholder = new SmartestPlaceholder;
		
		if($this->getRequestParameter('placeholder_name')){
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_name'));
		}else{
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_label'));
		}
		
		if($placeholder->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A placeholder with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
	        
		    $placeholder->setLabel($this->getRequestParameter('placeholder_label'));
		    $placeholder->setName($name);
		    $placeholder->setSiteId($this->getSite()->getId());
		    $placeholder->setType($this->getRequestParameter('placeholder_type'));
            $placeholder_label = strlen($this->getRequestParameter('placeholder_label')) ? $this->getRequestParameter('placeholder_label') : $name;
		    
		    if($this->getRequestParameter('placeholder_filegroup') == 'NONE'){
		        $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
            }elseif($this->getRequestParameter('placeholder_filegroup') == 'NEW'){
                $new_filegroup_name = strlen($this->getRequestParameter('new_group_name')) ? $this->getRequestParameter('new_group_name') : 'Files for '.$placeholder_label;
                $group = new SmartestAssetGroup;
                $group->setLabel($new_filegroup_name);
                $group->setName(SmartestStringHelper::toVarName($new_filegroup_name));
                $group->setSiteId($this->getSite()->getId());
                $group->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
                $group->setFilterValue($this->getRequestParameter('placeholder_type'));
                $group->setWebId(SmartestStringHelper::random(32, SM_RANDOM_ALPHANUMERIC));
                $group->save();
		        $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_ASSETGROUP');
		        $placeholder->setFilterValue($group->getId());
		    }else if(is_numeric($this->getRequestParameter('placeholder_filegroup'))){
		        $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_ASSETGROUP');
		        $placeholder->setFilterValue($this->getRequestParameter('placeholder_filegroup'));
		    }
		    
		    $placeholder->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
		}
		
		$this->formForward();
	}
	
	public function insertContainer($get, $post){
		
		if($this->getRequestParameter('container_name')){
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('container_name'));
		}else{
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('container_label'));
		}
		
		$container = new SmartestContainer;
		
		if($container->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A container with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
		    $container->setLabel($this->getRequestParameter('container_label'));
		    $container->setName($name);
		    $container->setSiteId($this->getSite()->getId());
		    $container->setType('SM_ASSETCLASS_CONTAINER');
		    
		    if($this->getRequestParameter('container_group') == 'NONE'){
		        $container->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
		    }else if(is_numeric($this->getRequestParameter('container_group'))){
		        $container->setFilterType('SM_ASSETCLASS_FILTERTYPE_TEMPLATEGROUP');
		        $container->setFilterValue($this->getRequestParameter('container_group'));
		    }
		    
		    $container->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
	    }
		
		$this->formForward();
	}
	
    public function placeholders(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('placeholders');
	    
	    $placeholders = $this->getSite()->getPlaceholders();
	    $this->send($placeholders, 'placeholders');
	    
	}
	
	public function editPlaceholder($get){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($this->requestParameterIsSet('assetclass_id')){
	        $found = $placeholder->findBy('name', $this->getRequestParameter('assetclass_id'));
	    }else{
	        $found = $placeholder->find($placeholder_id);
	    }
	    
	    if($found){
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($placeholder->getPossibleFileGroups($this->getSite()->getId()), 'possible_groups');
	        $definitions = $placeholder->getDefinitions(true, $this->getSite()->getId());
	        $this->send((count($definitions) == 0), 'allow_type_change');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The placeholder ID wasn't recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function editContainer(){
	    
	    $container = new SmartestContainer;
	    
	    if($this->requestParameterIsSet('assetclass_id')){
	        $found = $container->findBy('name', $this->getRequestParameter('assetclass_id'));
	    }else{
	        $container_id = (int) $this->getRequestParameter('container_id');
	        $found = $container->find($container_id);
	    }
	    
	    if($found){
	        
	        $this->send($container, 'container');
	        $tlh = new SmartestTemplatesLibraryHelper;
    		// $groups = $tlh->getTemplateGroups('SM_ASSETTYPE_CONTAINER_TEMPLATE', $this->getSite()->getId());
	        $this->send($tlh->getTemplateGroups('SM_ASSETTYPE_CONTAINER_TEMPLATE', $this->getSite()->getId()), 'possible_groups');
	        // $definitions = $placeholder->getDefinitions(true, $this->getSite()->getId());
	        // $this->send((count($definitions) == 0), 'allow_type_change');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The container ID wasn't recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function updateContainer(){
	    
	    $container_id = (int) $this->getRequestParameter('container_id');
	    $container = new SmartestContainer;
	    
	    if($container->find($container_id)){
	        
	        $container->setLabel($this->getRequestParameter('container_label'));
	        
	        if($this->getRequestParameter('container_filter')){
	            if($this->getRequestParameter('container_filter') == 'NONE'){
	                $container->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
	                $container->setFilterValue('');
	            }else{
	                
	                $group = new SmartestTemplateGroup;
	                
	                if($group->find($this->getRequestParameter('container_filter'))){
	                    if($group->getFilterValue() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	                        $group->setShared(1);
	                        $group->save();
	                        $container->setFilterType('SM_ASSETCLASS_FILTERTYPE_TEMPLATEGROUP');
	                        $container->setFilterValue($this->getRequestParameter('container_filter'));
                        }
                    }
	            }
	        }
	        
	        $container->save();
	        $this->addUserMessageToNextRequest("The container was updated.", SmartestUserMessage::SUCCESS);
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The container ID wasn't recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function placeholderDefinitions($get){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $mode = ($this->getRequestParameter('mode') && $this->getRequestParameter('mode') == 'live') ? "live" : "draft";
	        
	        $draft_mode = ($mode == "draft");
	        
	        $definitions = $placeholder->getDefinitions($draft_mode, $this->getSite()->getId());
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($definitions, 'definitions');
	        $this->send($mode, 'mode');
	    
	    }
	    
	}
	
	public function updatePlaceholder($get, $post){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $placeholder->setLabel($this->getRequestParameter('placeholder_label'));
	        
	        if($this->getRequestParameter('placeholder_filter')){
	            if($this->getRequestParameter('placeholder_filter') == 'NONE'){
	                $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
	                $placeholder->setFilterValue('');
	            }else{
	                $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_ASSETGROUP');
	                $placeholder->setFilterValue($this->getRequestParameter('placeholder_filter'));
	            }
	        }
	        
	        $placeholder->save();
	        $this->addUserMessageToNextRequest("The placeholder was updated.", SmartestUserMessage::SUCCESS);
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The placeholder ID wasn't recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function movePageUp($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    $page = new SmartestPage();
	    $page->setDraftMode(true);
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        $page->moveUp();
	        // $this->addUserMessageToNextRequest("The page has been moved up.", SmartestUserMessage::SUCCESS);
	        SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognised.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	}
	
	public function movePageDown($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    $page = new SmartestPage();
	    $page->setDraftMode(true);
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        $page->moveDown();
	        // $this->addUserMessageToNextRequest("The page has been moved down.", SmartestUserMessage::SUCCESS);
	        SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognised.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	}
	
	public function preview(){
		
		// if(!$this->getRequestParameter('from')){
		    $this->setFormReturnUri();
		    $this->setFormReturnDescription('page preview');
	    // }
		
		// $content = array();
        
        // echo $this->getRequestParameter('page_id');
        
        $page_webid = $this->getRequestParameter('page_id');
		$page = new SmartestPage;
		
		if($page->smartFind($page_webid)){
		    
		    $page->setDraftMode(true);
		    $this->send($page, 'page');
		    
		    // $domain = 'http://'.$page->getParentSite()->getDomain();
		    
		    /* if(!SmartestStringHelper::endsWith('/', $domain)){
		        $domain .= '/';
		    } */
                
            $domain = $this->getRequest()->getDomain();
            $preview_url = $domain.'website/renderEditableDraftPage?page_id='.$page->getWebId();
            
            // {$domain}website/renderEditableDraftPage?page_id={$page.webid}
            // {if $item}&amp;item_id={$item.id}{/if}
            // {if $request_parameters.author_id}&amp;author_id={$request_parameters.author_id}{/if}
            // {if $request_parameters.search_query}&amp;q={$request_parameters.search_query}{/if}
            // {if $request_parameters.tag}&amp;tag_name={$request_parameters.tag}{/if}
            // {if $request_parameters.hash}#{$request_parameters.hash}{/if}
		    
		    if($page->getDraftTemplate() && is_file(SM_ROOT_DIR.'Presentation/Masters/'.$page->getDraftTemplate())){
		    
		        if($page->getType() == 'NORMAL'){
		            
                    if($page->getId() == $this->getSite()->getTagPageId()){
                        
                        $this->send(true, 'is_special_page');
                        
                        if($this->getRequestParameter('tag')){
                            $tag = new SmartestTag;
                            if($tag->findBy('name', $this->getRequestParameter('tag'))){
                                $this->send(true, 'show_iframe');
                		        $this->send($domain, 'site_domain');
                		        $this->setTitle('Page Preview | Tag | '.$tag->getLabel());
                		        $this->send(false, 'show_edit_item_option');
                                $this->send(false, 'show_publish_item_option');
                                $this->send(false, 'show_item_list');
                                $this->send(false, 'show_tag_list');
                                $this->send(false, 'show_search_box');
                                $this->send(false, 'show_author_list');
                                $preview_url .= '&amp;tag_name='.$tag->getName();
                                if($this->requestParameterIsSet('model_id')){
                                    $preview_url .= '&amp;model_id='.$this->getRequestParameter('model_id');
                                }
                                
                        		$du = new SmartestDataUtility;
                        		$models = $du->getModelsWithMetapageOnSiteId($this->getSite()->getId());
                                $this->send($models, 'models');
                                
                                $this->send($tag, 'tag');
                                $this->send(true, 'is_tag_page');
                                
                            }else{
                                // tag does not exist - require tag select
                                $this->send('The selected tag does not exist. Please choose another tag to preview on this page.', 'chooser_message');
                                $this->send(false, 'show_item_list');
                                $this->send(true, 'show_tag_list');
                                $this->send(false, 'show_search_box');
                                $this->send(false, 'show_author_list');
                                $this->send('websitemanager/preview', 'continue_action');
                                $du  = new SmartestDataUtility;
                	            $tags = $du->getTags();
                	            $this->send($tags, 'tags');
                            }
                            
                        }else{
                            // require tag select
                            $this->send('Please choose a tag to preview on this page.', 'chooser_message');
                            $this->send(false, 'show_item_list');
                            $this->send(true, 'show_tag_list');
                            $this->send(false, 'show_search_box');
                            $this->send(false, 'show_author_list');
                            $this->send('websitemanager/preview', 'continue_action');
                            $du  = new SmartestDataUtility;
            	            $tags = $du->getTags();
            	            $this->send($tags, 'tags');
                        }
                        
                        $this->send($this->getUser()->hasToken('edit_tags'), 'allow_tag_edit');
                    
                    }elseif($page->getId() == $this->getSite()->getUserPageId()){
                        
                        $this->send(true, 'is_special_page');
                        
                        if($this->getRequestParameter('author_id')){
                            
                            $author = new SmartestSystemUser;
                            
                            if($author->find($this->getRequestParameter('author_id'))){
                                $this->send(true, 'show_iframe');
                		        $this->send($domain, 'site_domain');
                		        $this->setTitle('Page Preview | Author | '.$author->getFullName());
                		        $this->send(false, 'show_edit_item_option');
                                $this->send(false, 'show_publish_item_option');
                                $this->send(false, 'show_item_list');
                                $this->send(false, 'show_tag_list');
                                $this->send(false, 'show_search_box');
                                $preview_url .= '&amp;author_id='.$author->getId();
                            }else{
                                // tag does not exist - require tag select
                                $this->send('The selected user does not exist. Please choose another user to preview on this page.', 'chooser_message');
                                $this->send(false, 'show_item_list');
                                $this->send(true, 'show_author_list');
                                $this->send(false, 'show_tag_list');
                                $this->send(false, 'show_search_box');
                                $this->send('websitemanager/preview', 'continue_action');
                                $uhelper = new SmartestUsersHelper;
                                $this->send($uhelper->getCreditableUsersOnSite($this->getSite()->getId()), 'authors');
                            }
                            
                        }else{
                            
                            // require author select
                            $this->send('Please choose a user to preview this page.', 'chooser_message');
                            $this->send(false, 'show_item_list');
                            $this->send(false, 'show_tag_list');
                            $this->send(false, 'show_search_box');
                            $this->send(true, 'show_author_list');
                            $this->send('websitemanager/preview', 'continue_action');
                            $uhelper = new SmartestUsersHelper;
                            $this->send($uhelper->getCreditableUsersOnSite($this->getSite()->getId()), 'authors');
                            
                        }
                        
                    }elseif($page->getId() == $this->getSite()->getSearchPageId()){
                        
                        $this->send(true, 'is_special_page');
                        
                        if($this->getRequestParameter('search_query')){
                            
                            $this->send(true, 'show_iframe');
                		    $this->send($domain, 'site_domain');
                		    $this->setTitle('Page Preview | Search | '.$this->getRequestParameter('search_query'));
                		    $this->send(false, 'show_edit_item_option');
                            $this->send(false, 'show_publish_item_option');
                            $this->send(false, 'show_item_list');
                            $this->send(false, 'show_tag_list');
                            $this->send(false, 'show_search_box');
                            $preview_url .= '&amp;q='.urlencode($this->getRequestParameter('search_query'));
                            
                        }else{
                            
                            // require search input
                            $this->send('Please submit a search query to preview this page.', 'chooser_message');
                            $this->send(false, 'show_iframe');
                            $this->send(false, 'show_item_list');
                            $this->send(false, 'show_tag_list');
                            $this->send(false, 'show_author_list');
                            $this->send(true, 'show_search_box');
                            $this->send('websitemanager/preview', 'continue_action');
                            
                        }
                        
        		    }else{
        		        
                        if($page->getId() == $this->getSite()->getHoldingPageId()){
                            $this->send(true, 'is_special_page');
                        }
                        
                        if($page->isErrorPage()){
                            $this->send(true, 'is_special_page');
                            if($this->getRequestParameter('requested_page')){
                                $preview_url .= '&amp;request='.urlencode($this->getRequestParameter('requested_page'));
                            }
                        }
                        
                        $this->send(true, 'show_iframe');
        		        $this->send($domain, 'site_domain');
        		        $this->setTitle('Page Preview | '.$page->getTitle());
        		        $this->send(false, 'show_edit_item_option');
                        $this->send(false, 'show_publish_item_option');
                        // $this->send($page->getMasterTemplate()->getImportedStylesheets(), 'stylesheets');
                        $this->send(new SmartestArray($page->getStylesheets()), 'stylesheets');
        		        
        		    }
		        
    		    }else if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
    		            $item_id = $this->getRequestParameter('item_id');
		            
    		            $item = SmartestCmsItem::retrieveByPk($item_id);
		            
    		            if(is_object($item)){
    		                $this->send($item, 'item');
    		                $this->send(true, 'show_iframe');
    		                // $this->send(new SmartestArray($page->getMasterTemplate()->getImportedStylesheets()), 'stylesheets');
    		                // echo "stylesheets";
    		                // print_r($page->getStylesheets());
                            $this->send(new SmartestArray($page->getStylesheets()), 'stylesheets');
    		                
    		                $this->send($domain, 'site_domain');
    		                $this->setTitle('Meta-Page Preview | '.$item->getName());
    		                
    		                if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved() == 1) || $this->getUser()->hasToken('publish_all_items')){
                    	        $this->send(true, 'show_publish_item_option');
                    	    }else{
                    	        $this->send(false, 'show_publish_item_option');
                    	    }
                    	    
                    	    if($this->getUser()->hasToken('modify_items')){
                    	        $this->send(true, 'show_edit_item_option');
                    	    }else{
                    	        $this->send(true, 'show_false_item_option');
                    	    }
                            
                            $preview_url .= '&amp;item_id='.$item->getId();
    		                
    		            }else{
		                    
		                    $this->send(false, 'show_edit_item_option');
		                    $this->send(false, 'show_publish_item_option');
		                    
    		                $this->send(false, 'show_iframe');
		                
    		                /* $set = new SmartestCmsItemSet;

        	                if($set->hydrate($page->getDatasetId())){

        	                    $items = $set->getMembersAsArrays(true);
        	                    $this->send($items, 'set_members');
        	                    $this->addUserMessage("Please choose an item to preview this page.");
        	                    $this->send(true, 'show_item_list');

        	                } */
                            
                            $this->send(true, 'show_item_list');

        	                $model = new SmartestModel;

        	                /* if($model->hydrate($page->getDatasetId())){
        	                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
        	                    $this->send($items, 'items');
        	                    $this->send($model->__toArray(), 'model');
        	                }else{
        	                    $this->send(array(), 'items');
        	                } */
        	                
        	                if($model->hydrate($page->getDatasetId())){
	                            $items = $model->getSimpleItemsAsArrays($this->getSite()->getId());
	                            $this->send($items, 'items');
	                            $this->send($model, 'model');
	                            $this->send($page, 'page');
	                        }else{
	                            $this->send(array(), 'items');
	                        }
        	                
        	                $this->setTitle('Meta-Page Preview | Choose '.$model->getName().' to Continue');
    		            }
		            
    	            }else{
	                
    	                $this->send(false, 'show_iframe');
	                
    	                $this->send(true, 'show_item_list');
	                
    	                $model = new SmartestModel;
	                
    	                if($model->hydrate($page->getDatasetId())){
    	                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
    	                    $this->send($items, 'items');
    	                    $this->send($model, 'model');
    	                    $this->send('Please choose an item to preview on this page.', 'chooser_message');
                            $this->send('websitemanager/preview', 'continue_action');
    	                }else{
    	                    $this->send(array(), 'items');
    	                }
    	                
    	                $this->setTitle('Meta-Page Preview | Choose '.$model->getName().' to Continue');
    	            }
    		    }
    		    
    	    }else{
    	        
    	        $this->send(false, 'show_iframe');
    	        $this->addUserMessage("The preview of this page cannot be displayed because no master template is chosen.", SmartestUserMessage::WARNING);
    	        
    	    }
		    
		    if($this->getUser()->hasToken('approve_page_changes') && $page->getChangesApproved() != 1){
    	        $this->send(true, 'show_approve_button');
    	    }else{
    	        $this->send(false, 'show_approve_button');
    	    }
    	    
    	    if(($this->getUser()->hasToken('publish_approved_pages') && $page->getChangesApproved() == 1) || $this->getUser()->hasToken('publish_all_pages')){
    	        $this->send(true, 'show_publish_button');
    	    }else{
    	        $this->send(false, 'show_publish_button');
    	    }
    	    
    	    $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
    	    $this->send(($page->getIsHeld() && $page->getHeldBy() == $this->getUser()->getId()), 'show_release_page_option');
		    
		}else{
		    $this->addUserMessage("The page ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->send(false, 'show_iframe');
		}
        
        if(isset($preview_url)){
            if($this->getRequestParameter('hash')){
                $preview_url .= '#'.$this->getRequestParameter('hash');
            }
            $this->send($preview_url, 'preview_url');
            $this->send($preview_url.'&amp;hide_newwin_link=true', 'full_page_preview_url');
        }
		
		/* if($content["page"] = $this->manager->getPage($page_id)){
			return $content;
		}else{
			return array("page"=>array());
		}*/
	}
	
	public function pageComments($get){
	    
	    $id = $this->getRequestParameter('page_id');
	    $page = new SmartestPage;
		
		if($page->hydrate($id)){
		    $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		}else{
		    $this->addUserMessage('The page ID has not been recognized.', SmartestUserMessage::ERROR);
		}
		
	}
	
	function deletePage($get){
		
		$id = $this->getRequestParameter('page_id');
		/* $sql = "UPDATE Pages SET page_deleted='TRUE' WHERE Pages.page_webid='$id'";
		$id = $this->database->rawQuery($sql);
		$title = $this->database->specificQuery('page_title', 'page_id', $id, 'Pages'); */
		
		if($this->getUser()->hasToken('remove_pages')){
		
		    $page = new SmartestPage;
		
    		if($page->hydrate($id)){
		    
    		    // retrieve site id for cache deletion
    		    $site_id = $page->getSiteId();
		    
    		    // set the page to deleted and save
    		    $page->setDeleted('TRUE');
    		    $page->save();
		    
    		    // clear cache
    		    SmartestCache::clear('site_pages_tree_'.$site_id, true);
		    
    		    // make sure user is notified
    		    $this->addUserMessageToNextRequest("The page has been successfully moved to the trash.", SmartestUserMessage::SUCCESS);
		    
    		    // log deletion
        		SmartestLog::getInstance('site')->log("Page '".$title."' was deleted by user '".$this->getUser()->getUsername()."'", SmartestLog::USER_ACTION);
		    
    		}else{
    		    $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have sufficient permissions to delete pages.", SmartestUserMessage::ACCESS_DENIED);
	        
	    }
		
		// forward
		$this->formForward();
	}
	
	public function sitePages($get){
		
		$this->requireOpenProject();
		$this->setFormReturnUri();
		$this->setFormReturnDescription('site tree');

        $site_id = $this->getSite()->getId();
        
        $site = new SmartestSite;
        $site->find($site_id);
        
        if($this->getRequestParameter('refresh') == 1){
            SmartestCache::clear('site_pages_tree_'.$site_id, true);
        }
        
        $pagesTree = $site->getPagesTree(true);
        
        $this->setTitle($this->getSite()->getInternalLabel()." | Site Tree");
        
        $this->send($pagesTree, "tree");
        $this->send($site_id, "site_id");
        $this->send($site, "site");
        $this->send($site->getHomePage(true), "home_page");
        $this->send(true, "site_recognised");
        
        $recent = $this->getUser()->getRecentlyEditedPages($this->getSite()->getId());
	    $this->send($recent, 'recent_pages');
		    
	}
    
    public function siteSpecialPages(){
        
        if($this->getSite() instanceof SmartestSite){
            
            $du = new SmartestDataUtility;
            $this->send($this->getSite(), 'site');
		    $pages = $this->getSite()->getPagesList();
            $this->send($pages, 'pages');
            $this->send(SmartestStringHelper::random(8, SM_RANDOM_ALPHANUMERIC).'.html', 'unknown_url');
            $this->send($this->getSite(), 'site');
            $this->send($du->getTags(), 'tags');
            $uhelper = new SmartestUsersHelper;
            $this->send($uhelper->getCreditableUsersOnSite($this->getSite()->getId()), 'authors');
            
            $this->send($this->getSite()->getErrorPage(), 'error_page');
            $this->send($this->getSite()->getHoldingPage(), 'holding_page');
            $this->send($this->getSite()->getSearchPage(), 'search_page');
            $this->send($this->getSite()->getTagPage(), 'tag_page');
            $this->send($this->getSite()->getUserPage(), 'author_page');
            
        }
        
    }
	
	public function releaseCurrentUserHeldPages(){
	    
	    $this->requireOpenProject();
	    
	    $num_held_pages = $this->getUser()->getNumHeldPages($this->getSite()->getId());
	    $this->getUser()->releasePages($this->getSite()->getId());
	    
	    if($num_held_pages == 0){
	        $this->addUserMessageToNextRequest("No pages were released, as none were held.", SmartestUserMessage::INFO);
	    }else if($num_held_pages == 1){
	        $this->addUserMessageToNextRequest("One page was released.", SmartestUserMessage::SUCCESS);
        }else{
            $this->addUserMessageToNextRequest($num_held_pages." pages were released.", SmartestUserMessage::SUCCESS);
        }
        
	    $this->redirect('/smartest/pages');
	    
	}
	
	public function addPage($get, $post){
		
		$this->requireOpenProject();
		
		$user_id = $this->getUser()->getId(); //['user_id'];
		
		$helper = new SmartestPageManagementHelper;
		
		if($this->getRequestParameter('stage') && is_numeric($this->getRequestParameter('stage')) && is_object(SmartestSession::get('__newPage'))){
		    $stage = $this->getRequestParameter('stage');
		}else{
		    $stage = 1;
		}
		
		/* if($this->getRequestParameter('page_id')){
			$page_id = $this->getRequestParameter('page_id');
			$parent = new SmartestPage;
			$parent->hydrate($page_id);
			$parent_info = $parent;
		}else{
		    if(is_object(SmartestSession::get('__newPage')) && SmartestSession::get('__newPage')->getParent()){
			    $parent = new SmartestPage;
			    $parent->hydrate(SmartestSession::get('__newPage')->getParent());
			    $parent_info = $parent;
			}
		} */
		
		// $templates = $helper->getMasterTemplates($site_id);
		$tlh = new SmartestTemplatesLibraryHelper;
		$templates = $tlh->getMasterTemplates($this->getSite()->getId());

		switch($stage){
			
			////////////// STAGE 2 //////////////
			
			case "2":
			
            if($this->requestParameterIsSet('form_submitted')){
                
                $type = in_array($this->getRequestParameter('page_type'), array('NORMAL', 'ITEMCLASS', 'LIST', 'TAG')) ? $this->getRequestParameter('page_type') : 'NORMAL';
                
                SmartestSession::get('__newPage')->setType($type);
            
    			if($this->getRequestParameter('page_type') == 'ITEMCLASS'){
    			    $model = new SmartestModel;
    			    if($model->find($this->getRequestParameter('page_model'))){
    			        SmartestSession::get('__newPage')->setDatasetId($this->getRequestParameter('page_model'));
    		        }else{
    		            $this->setRequestParameter('stage', 1);
    		            $this->forward('websitemanager', 'addPage');
    		        }
    			}else{
    			    
    			}
			
    			if(!strlen(SmartestSession::get('__newPage')->getTitle()) && !$this->getRequestParameter('page_title')){
			    
    			    $this->setRequestParameter('stage', 1);
    			    $p = new SmartestPage;
			    
    			    if($p->find($this->getRequestParameter('page_parent'))){
    			        $this->setRequestParameter('page_id', $p->getWebId());
    		        }
		        
    		        $this->addUserMessage("You must enter a title for your new page", SmartestUserMessage::WARNING);
    		        $this->forward('websitemanager', 'addPage');
		        
    			}else{
    			    // SmartestSession::get('__newPage')->setTitle(htmlentities($this->getRequestParameter('page_title'), ENT_COMPAT, 'UTF-8'));
    			    SmartestSession::get('__newPage')->setTitle($this->getRequestParameter('page_title'));
    			}
			
    			$this->send($this->getRequestParameter('page_parent'), 'page_parent');
			
    			SmartestSession::get('__newPage')->setParent($this->getRequestParameter('page_parent'));
    			$suggested_url = SmartestSession::get('__newPage')->getStrictUrl();
			
    			if(!$this->getSite()->urlExists($suggested_url)){
    			    $this->send($suggested_url, 'suggested_url');
    			}
			
    			$pages = $helper->getSerialisedPageTree($helper->getPagesTree($this->getSite()->getId()));
    			
                $this->send('TRUE', 'chooseParent');
    			$this->send($pages, 'pages');
			
    			if(!SmartestSession::get('__newPage')->getCacheAsHtml()){
    			    SmartestSession::get('__newPage')->setCacheAsHtml('TRUE');
    			}
			
    			if(SmartestSession::get('__newPage')->getType() == 'ITEMCLASS'){
				
    				$this->send($this->getSite()->getModels(), 'models');
				
    			}else if(SmartestSession::get('__newPage')->getType() == 'TAG'){
			    
    			    $du = new SmartestDataUtility;
    			    $tags = $du->getTagsAsArrays();
    			    $this->send($tags, 'tags');
			    
    			}
			
    			// $this->send($parent_info, 'parentInfo');
     			$this->send($this->getSite(), 'siteInfo');
 			
     			$this->send($templates, 'templates');
     		
     			$newPage = SmartestSession::get('__newPage');
                
                // echo "Forward";
                if(is_object(SmartestSession::get('__newPage_temporary_text')) && SmartestSession::get('__newPage_temporary_text') instanceof SmartestTemporaryTextAsset){
                    $this->send(SmartestSession::get('__newPage_temporary_text')->getContentForEditor(), 'text_editor_content');
                }else{
                    $this->send('Add your the text for your new page here.', 'text_editor_content');
                }
            
            }elseif(is_object(SmartestSession::get('__newPage')) && SmartestSession::get('__newPage') instanceof SmartestPage){
                
                $this->send($this->getSite(), 'siteInfo');
                $this->send(SmartestSession::get('__newPage')->getParent(), 'page_parent');
                $url_array = SmartestSession::get('__newPage')->getUnsavedUrls();
                $this->send($url_array[0], 'suggested_url');
                $newPage = SmartestSession::get('__newPage');
                $this->send($templates, 'templates');
                
                // echo "Back";
                if(is_object(SmartestSession::get('__newPage_temporary_text')) && SmartestSession::get('__newPage_temporary_text') instanceof SmartestTemporaryTextAsset){
                    $this->send(SmartestSession::get('__newPage_temporary_text')->getContentForEditor(), 'text_editor_content');
                }else{
                    $this->send('Test text 1234', 'text_editor_content');
                }
                
            }
            
            $this->send(is_writable(SM_ROOT_DIR.'Presentation/Masters/'), 'master_templates_dir_writable');
            $this->send(is_writable(SM_ROOT_DIR.'Presentation/Layouts/'), 'layout_templates_dir_writable');
            
            ////// Presets stuff //////
            
			$page_presets = $helper->getPagePresets($this->getSite()->getId());
            $this->send($page_presets, 'presets');
            
            $preset = new SmartestPagePreset;
		    
            if($preset_id = SmartestSession::get('__newPage_preset_id') && $preset->find(SmartestSession::get('__newPage_preset_id'))){
 			    
                // if there is already a choice of preset in the session, send that
                $newPage['draft_template'] = $preset->getMasterTemplateName();
                $this->send($preset->getId(), 'selected_preset_id');
                $this->send(true, 'hide_template_dropdown');
                $this->send((bool) SmartestSession::get('__newPage_preset_id'), 'hide_template_dropdown');
                
                if($this->getSite()->getPrimaryTextPlaceholderId()){
                    if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryTextPlaceholderId())){
                        $this->send(false, 'show_main_text_input');
                    }else{
                        $this->send(true, 'show_main_text_input');
                    }
                    $this->send(true, 'primary_text_placeholder_known');
                    $this->send($this->getSite()->getPrimaryTextPlaceholderId(), 'primary_text_placeholder_id');
                }else{
                    $this->send(false, 'primary_text_placeholder_known');
                    $this->send(false, 'show_main_text_input');
                }
                
                if($this->getSite()->getPrimaryContainerId()){
                    
                    $container = new SmartestContainer;
                    
                    if($container->find($this->getSite()->getPrimaryContainerId())){
                        if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryContainerId())){
                            $this->send(false, 'show_template_selector');
                        }else{
                            $this->send(true, 'show_template_selector');
                        }
                        $this->send(true, 'primary_container_known');
                        $this->send($this->getSite()->getPrimaryContainerId(), 'primary_container_id');
                        $this->send($container->getPossibleAssets(), 'layout_templates');
                        
                        if(is_numeric(SmartestSession::get('__newPage_layout_template_id'))){
                            $this->send(SmartestSession::get('__newPage_layout_template_id'), 'selected_layout_template_id');
                        }
                        
                        if(strlen(SmartestSession::get('__newPage_layout_template_name'))){
                            $this->send(SmartestSession::get('__newPage_layout_template_name'), 'new_layout_template_name');
                        }else{
                            $try_name = SmartestStringHelper::toVarName(SmartestSession::get('__newPage')->getTitle()).'.tpl';
                            $initial_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.$try_name));
                            $this->send($initial_name, 'new_layout_template_name');
                        }
                        
                    }else{
                        $this->send(false, 'primary_container_known');
                        $this->send(false, 'show_template_selector');
                    }
                    
                }else{
                    $this->send(false, 'show_template_selector');
                    $this->send(false, 'primary_container_known');
                }
            
            }elseif(SmartestSession::hasData('__newPage_preset_id') && SmartestSession::get('__newPage_preset_id') === false){
                
                if($this->getSite()->getPrimaryContainerId()){
                    
                    $container = new SmartestContainer;
                    
                    if($container->find($this->getSite()->getPrimaryContainerId())){
                        if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryContainerId())){
                            $this->send(false, 'show_template_selector');
                        }else{
                            $this->send(true, 'show_template_selector');
                        }
                        $this->send(true, 'primary_container_known');
                        $this->send($this->getSite()->getPrimaryContainerId(), 'primary_container_id');
                        $this->send($container->getPossibleAssets(), 'layout_templates');
                        
                        if(is_numeric(SmartestSession::get('__newPage_layout_template_id'))){
                            $this->send(SmartestSession::get('__newPage_layout_template_id'), 'selected_layout_template_id');
                        }elseif(SmartestSession::get('__newPage_layout_template_id') == "NEW"){
                            $this->send("NEW", 'selected_layout_template_id');
                        }
                        
                        if(strlen(SmartestSession::get('__newPage_layout_template_name'))){
                            $this->send(SmartestSession::get('__newPage_layout_template_name'), 'new_layout_template_name');
                        }else{
                            $try_name = SmartestStringHelper::toVarName(SmartestSession::get('__newPage')->getTitle()).'.tpl';
                            $initial_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.$try_name));
                            $this->send($initial_name, 'new_layout_template_name');
                        }
                        
                    }else{
                        $this->send(false, 'primary_container_known');
                        $this->send(false, 'show_template_selector');
                    }
                    
                }else{
                    $this->send(false, 'show_template_selector');
                    $this->send(false, 'primary_container_known');
                }
                
                if($this->getSite()->getPrimaryTextPlaceholderId()){
                    if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryTextPlaceholderId())){
                        $this->send(false, 'show_main_text_input');
                    }else{
                        $this->send(true, 'show_main_text_input');
                    }
                    $this->send(true, 'primary_text_placeholder_known');
                    $this->send($this->getSite()->getPrimaryTextPlaceholderId(), 'primary_text_placeholder_id');
                }else{
                    $this->send(false, 'primary_text_placeholder_known');
                    $this->send(false, 'show_main_text_input');
                }
                
		    }else{
                
		        // no preset has been selected, so go by settings
                $preset_id = $this->getGlobalPreference('site_default_page_preset_id');
                $this->send($this->getGlobalPreference('site_default_page_preset_id'), 'selected_preset_id');
                $this->send((bool) $this->getGlobalPreference('site_default_page_preset_id'), 'hide_template_dropdown');
                
                if($preset_id && $preset->find($preset_id)){
                    
                    if($this->getSite()->getPrimaryTextPlaceholderId()){
                        if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryTextPlaceholderId())){
                            $this->send(false, 'show_main_text_input');
                        }else{
                            $this->send(true, 'show_main_text_input');
                        }
                        $this->send($this->getSite()->getPrimaryTextPlaceholderId(), 'primary_text_placeholder_id');
                        $this->send(true, 'primary_text_placeholder_known');
                    }else{
                        $this->send(false, 'show_main_text_input');
                        $this->send(false, 'primary_text_placeholder_known');
                    }
                    
                    if($this->getSite()->getPrimaryContainerId()){
                        
                        $container = new SmartestContainer;
                    
                        if($container->find($this->getSite()->getPrimaryContainerId())){
                            if($preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryContainerId())){
                                $this->send(false, 'show_template_selector');
                            }else{
                                $this->send(true, 'show_template_selector');
                            }
                            $this->send($this->getSite()->getPrimaryContainerId(), 'primary_container_id');
                            $this->send(true, 'primary_container_known');
                            $this->send($container->getPossibleAssets(), 'layout_templates');
                            
                            if(is_numeric(SmartestSession::get('__newPage_layout_template_id'))){
                                $this->send(SmartestSession::get('__newPage_layout_template_id'), 'selected_layout_template_id');
                            }
                            
                            if(strlen(SmartestSession::get('__newPage_layout_template_name'))){
                                $this->send(SmartestSession::get('__newPage_layout_template_name'), 'new_layout_template_name');
                            }else{
                                $try_name = SmartestStringHelper::toVarName(SmartestSession::get('__newPage')->getTitle()).'.tpl';
                                $initial_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.$try_name));
                                $this->send($initial_name, 'new_layout_template_name');
                            }
                            
                        }else{
                            $this->send(false, 'show_template_selector');
                            $this->send(false, 'primary_container_known');
                        }
                        
                    }else{
                        $this->send(false, 'show_template_selector');
                        $this->send(false, 'primary_container_known');
                    }
                    
                }else{
                    
                    if($this->getSite()->getPrimaryTextPlaceholderId()){
                        $this->send(true, 'show_main_text_input');
                        $this->send($this->getSite()->getPrimaryTextPlaceholderId(), 'primary_text_placeholder_id');
                        $this->send(true, 'primary_text_placeholder_known');
                    }else{
                        $this->send(false, 'show_main_text_input');
                        $this->send(false, 'primary_text_placeholder_known');
                    }
                    
                    if($this->getSite()->getPrimaryContainerId()){
                        $this->send(true, 'show_template_selector');
                        $this->send($this->getSite()->getPrimaryContainerId(), 'primary_container_id');
                        // echo $this->getSite()->getPrimaryContainerId();
                        $this->send(true, 'primary_container_known');
                    }else{
                        $this->send(false, 'show_template_selector');
                        $this->send(false, 'primary_container_known');
                    }
                    
                }
                
		    }
            
            $template = "addPage.stage2.tpl";
            $this->send($newPage, 'newPage');
			
			break;
			
			////////////// STAGE 3 //////////////
			
			case "3":
			
			// verify the page details
			if($this->getRequestParameter('page_title') && SmartestSession::get('__newPage')->getTitle() == "Untitled Page"){
			    SmartestSession::get('__newPage')->setTitle($this->getRequestParameter('page_title'));
			}
			
			SmartestSession::get('__newPage')->setName(strlen(SmartestSession::get('__newPage')->getTitle()) ? SmartestStringHelper::toSlug(SmartestSession::get('__newPage')->getTitle()) : SmartestStringHelper::toSlug('Untitled Smartest Web Page'));
			SmartestSession::get('__newPage')->setCacheAsHtml($this->getRequestParameter('page_cache_as_html'));
			SmartestSession::get('__newPage')->setCacheInterval($this->getRequestParameter('page_cache_interval'));
			SmartestSession::get('__newPage')->setIsPublished('FALSE');
			SmartestSession::get('__newPage')->setChangesApproved(0);
			SmartestSession::get('__newPage')->setSearchField(htmlentities(strip_tags($this->getRequestParameter('page_search_field')), ENT_COMPAT, 'UTF-8'));
			
            if(SmartestSession::get('__newPage')->getType() == 'NORMAL'){
			    SmartestSession::get('__newPage')->setDraftTemplate($this->getRequestParameter('page_draft_template'));
			    SmartestSession::get('__newPage')->setDescription(strip_tags($this->getRequestParameter('page_description')));
			    SmartestSession::get('__newPage')->setMetaDescription(strip_tags($this->getRequestParameter('page_meta_description')));
			    SmartestSession::get('__newPage')->setKeywords(strip_tags($this->getRequestParameter('page_keywords')));
            }else if(SmartestSession::get('__newPage')->getType() == 'ITEMCLASS'){
                SmartestSession::get('__newPage')->setDraftTemplate($this->getRequestParameter('page_draft_template'));
            }
            
            if((bool) $this->getRequestParameter('save_textarea_contents')){
                if(is_object(SmartestSession::get('__newPage_temporary_text')) && SmartestSession::get('__newPage_temporary_text') instanceof SmartestTemporaryTextAsset){
                    SmartestSession::get('__newPage_temporary_text')->setContent($this->getRequestParameter('page_text_contents'));
                }else{
                    $temp_text_object = new SmartestTemporaryTextAsset('Main text for page '.SmartestSession::get('__newPage')->getTitle(), $this->getRequestParameter('page_text_contents'));
                    $temp_text_object->setCreated(time());
                    SmartestSession::set('__newPage_temporary_text', $temp_text_object);
                }
            }
			
			if($this->getRequestParameter('page_id')){
				SmartestSession::get('__newPage')->setParent($this->getRequestParameter('page_id'));
			}
			
			/* if($this->getRequestParameter('page_preset')){
				SmartestSession::set('__newPage_preset_id', $this->getRequestParameter('page_preset'));
			} */
			
			/* if($this->getRequestParameter('page_model')){
				SmartestSession::get('__newPage')->setDatasetId($this->getRequestParameter('page_model'));
				$model = new SmartestModel;
				$model->hydrate($this->getRequestParameter('page_model'));
			} 
			
			if($this->getRequestParameter('page_tag')){
				SmartestSession::get('__newPage')->setDatasetId($this->getRequestParameter('page_tag'));
				$tag = new SmartestTag;
				$tag->hydrate($this->getRequestParameter('page_tag'));
			} */
			
			if(SmartestSession::get('__newPage')->getType() == 'ITEMCLASS'){
			    if(is_numeric(SmartestSession::get('__newPage')->getDatasetId())){
			        $model = new SmartestModel;
			        if($model->find(SmartestSession::get('__newPage')->getDatasetId())){
			            $this->send($model, 'new_page_model');
			        }
			    }
			}
			
			$type_template = strtolower(SmartestSession::get('__newPage')->getType());
			$newPage = SmartestSession::get('__newPage')->__toArray();
            
            if($newPage['parent']){
                $newPage['parent'] = SmartestSession::get('__newPage')->getParentPage();
            }
			
			// Deal with submitted URL and send to summary if available
            if(strlen($this->getRequestParameter('page_url')) && substr($this->getRequestParameter('page_url'), 0, 18) != 'website/renderPage'){
    		    
                $url = $this->getRequestParameter('page_url');
                
                if($this->getSite()->urlExists($url)){
                    $this->send(false, 'chosen_url_available');
                    $this->send('page/'.SmartestSession::get('__newPage')->getWebId(), 'new_page_url');
                }else{
    			    SmartestSession::get('__newPage')->clearUnsavedUrls();
                    SmartestSession::get('__newPage')->addUrl($url); 
    			    $this->send($url, 'new_page_url');
                    $this->send(true, 'chosen_url_available');
                }
                
		    }else{
		        $this->send(false, 'chosen_url_available');
		    }
            
			// should the page have a preset?
            if($this->getRequestParameter('page_preset_id') == 'NONE'){
                
                // No page preset used
                $this->send(false, 'use_preset');
                SmartestSession::set('__newPage_preset_id', false);
                
            }else{
                
                $preset = new SmartestPagePreset;
                $preset_id = (int) $this->getRequestParameter('page_preset_id');
                
                SmartestSession::set('__newPage_preset_id', $preset_id);
                
                // if so, apply those definitions
                if($preset->find($preset_id)){
                    SmartestSession::get('__newPage')->setDraftTemplate($preset->getMasterTemplateName());
                    $this->send($preset, 'page_preset');
                    $this->send(true, 'use_preset');
    				$newPage['draft_template'] = SmartestSession::get('__newPage')->getDraftTemplate();
    				$this->send(SmartestSession::get('__newPage')->getDraftTemplate(), 'page_draft_template');
                }else{
                    $this->send(false, 'use_preset');
                }
            }
            
			$primary_container = new SmartestContainer;
            
            if($this->getSite()->getPrimaryContainerId() && $primary_container->find($this->getSite()->getPrimaryContainerId())){
                
                if($this->getRequestParameter('page_preset_id') == 'NONE' && (is_numeric($this->getRequestParameter('layout_template_id')) || $this->getRequestParameter('layout_template_id') == 'NEW')){
                    
                    if($this->getRequestParameter('layout_template_id') == 'NEW'){
                        
                        SmartestSession::set('__newPage_layout_template_id', 'NEW');
                        
                        if(strlen($this->getRequestParameter('new_layout_template_name'))){
                            if(SmartestFileSystemHelper::getDotSuffix(SM_ROOT_DIR.'Presentation/Layouts/'.$this->getRequestParameter('new_layout_template_name')) == 'tpl'){
                                $entered_name = $this->getRequestParameter('new_layout_template_name');
                                $entered_name_wo_suffix = SmartestFileSystemHelper::removeDotSuffix($entered_name);
                                $name = SmartestStringHelper::toVarName($entered_name_wo_suffix).'.tpl';
                                SmartestSession::set('__newPage_layout_template_name', $name);
                            }else{
                                SmartestSession::set('__newPage_layout_template_name', SmartestStringHelper::toVarName($this->getRequestParameter('new_layout_template_name')).'.tpl');
                            }
                        }else{
                            $try_name = SmartestStringHelper::toVarName(SmartestSession::get('__newPage')->getTitle()).'.tpl';
                            $initial_name = SmartestFileSystemHelper::getFileName(SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.$try_name));
                            SmartestSession::set('__newPage_layout_template_name', $initial_name);
                        }
                        
                        $this->send(true, 'new_layout_template');
                        $this->send(true, 'show_layout_template');
                        
                    }elseif(is_numeric($this->getRequestParameter('layout_template_id'))){
                        
                        $template = new SmartestAsset;
                    
                        if($template->find($this->getRequestParameter('layout_template_id'))){
                            SmartestSession::set('__newPage_layout_template_id', $this->getRequestParameter('layout_template_id'));
                            $this->send($template, 'layout_template');
                            $this->send(true, 'show_layout_template');
                            $this->send(false, 'new_layout_template');
                        }else{
                            // template doesn't exist or wasn't selected
                        }
                    }
                    
                    $this->send(false, 'use_preset_for_layout');
                    $this->send(true, 'show_layout_template');
                    
                }elseif(is_object($preset) && $template = $preset->getDefinitionForAssetClassId($this->getSite()->getPrimaryContainerId())){
                    
                    $this->send($template, 'layout_template');
                    $this->send(true, 'show_layout_template');
                    $this->send(true, 'use_preset_for_layout');
                    
                }else{
                    $this->send(false, 'show_layout_template');
                    $this->send(false, 'use_preset_for_layout');
                    SmartestSession::get('__newPage_layout_template_id');
                }
                
            }
			
			/* if(SmartestSession::get('__newPage')->getPreset()){
				
				$newPage['preset'] = SmartestSession::get('__newPage')->getPreset();
				$preset = new SmartestPagePreset;
				$preset->hydrate(SmartestSession::get('__newPage')->getPreset());
				// SmartestSession::get('__newPage')->setPresetLabel($preset->getLabel());
				SmartestSession::get('__newPage')->setDraftTemplate($preset->getMasterTemplateName());
				$newPage['preset_label'] = SmartestSession::get('__newPage')->getPresetLabel();
				$newPage['draft_template'] = SmartestSession::get('__newPage')->getDraftTemplate();
				
			} */
			
			// print_r($newPage);
			
 			$this->send($newPage, 'newPage');
			$template = "addPage.stage3.tpl";
			
            break;
			
			
			////////////// DEFAULT //////////////
			
			default:
			
            $page = new SmartestPage;
            $page->setWebId(SmartestStringHelper::random(32, SM_RANDOM_ALPHANUMERIC));
            $page->setCreatedbyUserid($this->getUser()->getId());
            $page->setSiteId($this->getSite()->getId());
            SmartestSession::set('__newPage_preset_id', null);
            
			if($this->getRequestParameter('page_id')){
    			
    			$page_id = $this->getRequestParameter('page_id');
    			$parent = new SmartestPage;
    			
    			if($parent->findby('webid', $page_id)){
    			    $this->send($parent, 'parent_page');
                    $page->setParent($parent->getId());
			    }else{
			        // The page selected as a parent was not found.
    			    $parent_pages = $this->getSite()->getPagesList(true);
    			    $this->send($parent_pages, 'parent_pages');
			    }
			    
    		}else{
    		    
    		    if(is_object(SmartestSession::get('__newPage')) && SmartestSession::get('__newPage')->getParent()){
    			    $parent = new SmartestPage;
    			    
                    if($parent->find(SmartestSession::get('__newPage')->getParent())){
    			        $page->setParent($parent->getId());
                        // $this->send($parent, 'parent_page');
                        $parent_pages = $this->getSite()->getPagesList(true);
                        $this->send($parent_pages, 'parent_pages');
    			    }
    			    
    			}else{
    			    // fetch list of site pages for dropdown
    			    $parent_pages = $this->getSite()->getPagesList(true);
    			    $this->send($parent_pages, 'parent_pages');
    			}
    		}
    		
            // echo "blah";
            
    		if($this->getUser()->hasToken('see_private_models')){
                $this->send($this->getSite()->getModels(false), 'models');
            }else{
                $du = new SmartestDataUtility;
                $models = $du->getMetaPageModels($this->getSite()->getId());
                $this->send($models, 'models');
            }
			
			SmartestSession::set('__newPage', $page);
			$template = "addPage.start.tpl";
			
			break;
		}
		
		$this->send($template, "_stage_template");
		$this->setTitle("Create a new page");
 		
	}
	
	public function insertPage($get, $post){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if(SmartestSession::get('__newPage') instanceof SmartestPage){
	            
	            $page =& SmartestSession::get('__newPage');
	            
	            $page->setOrderIndex($page->getParentPage()->getNextChildOrderIndex());
	            $page->setCreated(time());
	            
	            $page->save();
	            
	            if($page->getType() == 'NORMAL'){
	                $page->addAuthorById($this->getUser()->getId());
                }elseif($page->getType() == 'ITEMCLASS'){
                    $model = new SmartestModel;
                    $found_model = $model->find($page->getDatasetId());
                    if(!$model->hasMetaPageOnSiteId($this->getSite()->getId())){
                        $model->setDefaultMetaPageId($this->getSite()->getId(), $page->getId());
                    }
                }
	            
	            // should the page have a preset?
	            if($preset_id = SmartestSession::get('__newPage_preset_id')){
	                
	                $preset = new SmartestPagePreset;
	                // if so, apply those definitions
	                if($preset->hydrate($preset_id)){
	                    $preset->applyToPage($page);
	                }
	            }
                
                if(is_numeric(SmartestSession::get('__newPage_layout_template_id')) && $this->getSite()->getPrimaryContainerId() && (!is_object($preset) || !$preset->hasDefinitionForAssetClassId($this->getSite()->getPrimaryContainerId()))){
                    $t = new SmartestTemplateAsset;
                    if($t->find(SmartestSession::get('__newPage_layout_template_id'))){
                        $c = new SmartestContainer;
                        if($c->find($this->getSite()->getPrimaryContainerId())){
                            $d = new SmartestContainerDefinition();
                            if(!$d->loadWithObjects($c, $page, true)){
                                $d->setPageId($page->getId());
                                $d->setAssetClassId($c->getId());
                            }
                            $d->setDraftAssetId($t->getId());
                            $d->save();
                        }else{
                            // "primary container ID not found";
                        }
                    }else{
                        // "template not found";
                    }
                }elseif(SmartestSession::get('__newPage_layout_template_id') == "NEW" && $this->getSite()->getPrimaryContainerId() && strlen(SmartestSession::get('__newPage_layout_template_name')) && SmartestFileSystemHelper::getDotSuffix(SM_ROOT_DIR.'Presentation/Layouts/'.SmartestSession::get('__newPage_layout_template_name')) == 'tpl'){
                    
                    if($page->getType() == 'NORMAL'){
                    
                        $full_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.SmartestSession::get('__newPage_layout_template_name'));
                        $file_name = SmartestFileSystemHelper::getFileName($full_path);
                        $file = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Install/Samples/static_container_template.tpl');
                        $file = str_replace('%%NAME%%', $file_name, $file);
                        $file = str_replace('%%PAGENAME%%', $page->getTitle(), $file);
                    
                        if($primary_text_placeholder_id = $this->getSite()->getPrimaryTextPlaceholderId()){
                            $placeholder = new SmartestPlaceholder;
                            if($placeholder->find($primary_text_placeholder_id)){
                                $file = str_replace('%%MAINTEXTCODE%%', "<article>\n  <?sm:placeholder name=\"".$placeholder->getName()."\":?>\n</article>", $file);
                            }
                        }
                    
                        $file = str_replace('%%MAINTEXTCODE%%', '', $file);
                        SmartestFileSystemHelper::save($full_path, $file);
                        @chmod($full_path, 0666);
                    
                        $t = new SmartestAsset;
                        $t->setSiteId($this->getSite()->getId());
                        $t->setUrl($file_name);
                        $t->setType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
                        $t->setWebId(SmartestStringHelper::random(36, SM_RANDOM_ALPHANUMERIC));
                        $t->setStringId(SmartestStringHelper::toVarName($page->getTitle().'_layout_template'));
                        $t->setLabel($page->getTitle().' layout template');
                        $t->setUserId($this->getUser()->getId());
                        $t->setCreated(time());
                        $t->save();
                    
                        $c = new SmartestContainer;
                        if($c->find($this->getSite()->getPrimaryContainerId())){
                            $d = new SmartestContainerDefinition();
                            if(!$d->loadWithObjects($c, $page, true)){
                                $d->setPageId($page->getId());
                                $d->setAssetClassId($c->getId());
                            }
                            $d->setDraftAssetId($t->getId());
                            $d->save();
                        
                            if($c->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_TEMPLATEGROUP'){
                                $tg = new SmartestTemplateGroup;
                                if($tg->find($c->getFilterValue())){
                                    $tg->addTemplateById($t->getId());
                                }
                            }
                        
                        }
                    
                    }elseif($page->getType() == 'ITEMCLASS' && $found_model){
                        
                        $full_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/'.SmartestSession::get('__newPage_layout_template_name'));
                        $file_name = SmartestFileSystemHelper::getFileName($full_path);
                        $file = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Install/Samples/metapage_container_template.tpl');
                        $file = str_replace('%%NAME%%', $file_name, $file);
                        $file = str_replace('%%MODELNAME%%', $model->getName(), $file);
                        $model_varname = SmartestStringHelper::toVarName($model->getName());
                        $this_item = '$this.'.$model_varname;
                        
                        $main_text_code = '<?sm:*'."\n\nAvailable properties for model ".$model->getName().":\n\n";
                        $main_text_code .= $this_item.'.'.SmartestStringHelper::toVarName($model->getItemNameFieldName()).' (object type SmartestString)'."\n";
                        
                        foreach($model->getProperties() as $property){
                            $main_text_code .= $this_item.'.'.$property->getVarName().' (object type '.$property->getClass().')'."\n";
                        }
                        
                        $main_text_code .= "\n*:?>";
                        
                        $file = str_replace('%%MAINTEXTCODE%%', $main_text_code, $file);
                        
                        SmartestFileSystemHelper::save($full_path, $file);
                        @chmod($full_path, 0666);
                        
                        $t = new SmartestAsset;
                        $t->setSiteId($this->getSite()->getId());
                        $t->setUrl($file_name);
                        $t->setType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
                        $t->setWebId(SmartestStringHelper::random(36, SM_RANDOM_ALPHANUMERIC));
                        $t->setStringId(SmartestStringHelper::toVarName($page->getTitle().'_layout_template'));
                        $t->setLabel($page->getTitle().' layout template');
                        $t->setUserId($this->getUser()->getId());
                        $t->setCreated(time());
                        $t->setModelId($model->getId());
                        $t->save();
                        
                        $c = new SmartestContainer;
                        if($c->find($this->getSite()->getPrimaryContainerId())){
                            $d = new SmartestContainerDefinition();
                            if(!$d->loadWithObjects($c, $page, true)){
                                $d->setPageId($page->getId());
                                $d->setAssetClassId($c->getId());
                            }
                            $d->setDraftAssetId($t->getId());
                            $d->save();
                        
                            if($c->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_TEMPLATEGROUP'){
                                $tg = new SmartestTemplateGroup;
                                if($tg->find($c->getFilterValue())){
                                    $tg->addTemplateById($t->getId());
                                }
                            }
                        
                        }
                        
                    }
                    
                    SmartestSession::clear('__newPage_layout_template_id');
                    SmartestSession::clear('__newPage_layout_template_name');
                    
                }
                
                if(SmartestSession::get('__newPage_temporary_text') && is_object(SmartestSession::get('__newPage_temporary_text')) && is_numeric($this->getSite()->getPrimaryTextPlaceholderId())){
                    // TODO: Deal with temporary text asset here
                    $p = new SmartestPlaceholder;
                    if($p->find($this->getSite()->getPrimaryTextPlaceholderId())){
                        
                        $tta = SmartestSession::get('__newPage_temporary_text');
                        $pta = $tta->savePermanentTextAsset();
                        
                        $d = new SmartestPlaceholderDefinition();
                        
                        if(!$d->loadWithObjects($p, $page)){
                            $d->setPageId($page->getId());
                            $d->setAssetClassId($p->getId());
                        }
                        
                        $d->setDraftAssetId($pta->getId());
                        $d->save();
                        
                    }else{
                        // "placeholder not found";
                    }
                }
	            
	            $page_webid = $page->getWebId();
    		    $site_id = $page->getSiteId();
    		    
    		    // clear session and cached page tree
    		    SmartestCache::clear('site_pages_tree_'.$site_id, true);
	            SmartestSession::clear('__newPage');
                SmartestSession::clear('__newPage_preset_id');
                SmartestSession::clear('__newPage_temporary_text');
	    
	            switch($this->getRequestParameter('destination')){
			
        			case "SITEMAP":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/smartest/pages');
        			break;
			
        			case "ELEMENTS":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
        			break;
			
        			case "EDIT":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/openPage?page_id='.$page_webid);
        			break;
			
        			case "PREVIEW":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/preview?page_id='.$page_webid);
    			    break;
    			
    		    }
    		
		    }else{
		        
		        $this->addUserMessageToNextRequest("The new page expired from the session.", SmartestUserMessage::WARNING);
    		    $this->redirect('/smartest');
		        
		    }
		
		}else{
		    
		    $this->addUserMessageToNextRequest("You must select a site before adding pages.", SmartestUserMessage::INFO);
		    $this->redirect('/smartest');
		    
		}
		
	}
	
	public function updatePage($get, $post){    
        
        $page = new SmartestPage;
        
        if($page->smartFind($this->getRequestParameter('page_id'))){
            
            $page->setTitle($this->getRequestParameter('page_title'));
            
            if($this->getRequestParameter('page_name') && strlen($this->getRequestParameter('page_name')) && $this->getUser()->hasToken('edit_page_name')){
                $page->setName(SmartestStringHelper::toSlug($this->getRequestParameter('page_name')));
            }
            
            if(!$this->getSite()->pageIdIsSpecial($page->getId())){
                $page->setParent($this->getRequestParameter('page_parent'));
                $page->setIsSection(($this->getRequestParameter('page_is_section') && ($this->getRequestParameter('page_is_section') == 'true')) ? 1 : 0);
                $page->setCacheAsHtml($this->getRequestParameter('page_cache_as_html'));
                $page->setCacheInterval($this->getRequestParameter('page_cache_interval'));
            }
            
            $page->setModified(time());
            
            if($page->getType() == 'NORMAL'){
                if(!$this->getSite()->pageIdIsSpecial($page->getId())){
                    $page->setSearchField(strip_tags($this->getRequestParameter('page_search_field')));
                    $page->setKeywords(strip_tags($this->getRequestParameter('page_keywords')));
                    $page->setDescription(strip_tags($this->getRequestParameter('page_description')));
                    $page->setMetaDescription(strip_tags($this->getRequestParameter('page_meta_description')));
                    $page->setIconImageId($this->getRequestParameter('page_icon_image_id'));
                }else if($page->getId() == $this->getSite()->getErrorPageId()){
                    $page->setMetaDescription(strip_tags($this->getRequestParameter('page_meta_description')));
                }else if($page->getId() == $this->getSite()->getHoldingPageId()){
                    $page->setIconImageId($this->getRequestParameter('page_icon_image_id'));
                }
            }
            
            if($page->getType() == 'ITEMCLASS'){
                
                $page->setForceStaticTitle(($this->getRequestParameter('page_force_static_title') && ($this->getRequestParameter('page_force_static_title') == 'true')) ? 1 : 0);
                
                if($this->getRequestParameter('page_parent_data_source') && strlen($this->getRequestParameter('page_parent_data_source'))){
                    $page->setParentMetaPageReferringPropertyId($this->getRequestParameter('page_parent_data_source'));
                }
                
            }
            
            $page->save();
            SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
            $this->addUserMessageToNextRequest('The page was successfully updated.', SmartestUserMessage::SUCCESS);
            
        }else{
            $this->addUserMessageToNextRequest('There was an error updating page ID '.$this->getRequestParameter('page_id').'.', SmartestUserMessage::ERROR);
        }
        
		// $this->formForward();
        $this->handleSaveAction();

	}

	public function pageAssets($get){
	    
	    if($this->getUser()->hasToken('modify_draft_pages')){
	        
	        $page_webid = $this->getRequestParameter('page_id');
	        
	        $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());

    		if(isset($type_index[$page_webid])){
    		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		        $page = new SmartestItemPage;
    		    }else{
    		        $page = new SmartestPage;
    		    }
    		}else{
    		    $page = new SmartestPage;
    		}
    		
    		if($page->smartFind($page_webid)){
	            
	            if($page->getDeleted() == 'TRUE'){
	                $this->send(true, 'show_deleted_warning');
	            }
                
                $this->send($this->getSite()->pageIdIsSpecial($page->getId()), 'is_special_page');
	            
	            $editable = $page->isEditableByUserId($this->getUser()->getId());
        		$this->send($editable, 'page_is_editable');
	            
	            if($page->getType() == 'ITEMCLASS' && (!$this->getRequestParameter('item_id') || !is_numeric($this->getRequestParameter('item_id')))){
	            
    	            $model = new SmartestModel;
            
                    if($model->hydrate($page->getDatasetId())){
                        $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                        $this->send($items, 'items');
                        $this->send($model, 'model');
                        $this->send('Please choose an item to edit the elements on this page.', 'chooser_message');
                        $this->send('websitemanager/pageAssets', 'continue_action');
                        $this->send(true, 'allow_edit');
                        $this->send($page, 'page');
                    }else{
                        $this->send(array(), 'items');
                    }
                
                    $this->send(true, 'require_item_select');
	            
    	        }else{
	                
	                $this->send(false, 'require_item_select');
	                
	                if($page->getType() == 'ITEMCLASS'){
        	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
        	                
        	                $page->setPrincipalItem($item);
        	                $recent_items = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $item->getItem()->getItemclassId());
        	                $model = $item->getItem()->getModel();
        	                $metapages = $item->getItem()->getModel()->getMetaPages();
        	                
        	                $default_metapage_id = $item->getItem()->getMetapageId();
        	                
        	                if($default_metapage_id){
        	                    if($default_metapage_id != $page->getId()){
        	                        $default_metapage = new SmartestPage;
            	                    $default_metapage->find($default_metapage_id);
            	                    $default_metapage_webid = $default_metapage->getWebId();
            	                    $this->send($default_metapage_webid, 'default_metapage_webid');
        	                        $this->send(true, 'show_metapage_warning');
        	                    }else{
        	                        $this->send(false, 'show_metapage_warning');
    	                        }
        	                }else{
        	                    $this->send(false, 'show_metapage_warning');
        	                }
                            
                		    $this->send((bool) count($metapages), 'show_metapages');
        	                $this->send($metapages, 'metapages');
        	                $this->send($recent_items, 'recent_items');
        	                $this->send($model, 'model');
        	                $this->send(true, 'show_recent_items');
        	                $this->send($item, 'item');
        	            }
                        
    	            }elseif($page->getId() == $this->getSite()->getTagPageId()){
                        
                        $tag = new SmartestTag;
                        
                        if($tag->findBy('name', $this->getRequestParameter('tag'))){
                            
                            $this->send($tag, 'tag');
                            $this->send(true, 'is_tag_page');
                            $this->send($this->getUser()->hasToken('edit_tags'), 'allow_tag_edit');
                            
                        }else{
                            // tag does not exist - require tag select?
                            
                        }
                        
    	            }
	                
    		        $this->setFormReturnUri();
    		        $this->setFormReturnDescription('page elements tree');
		            
            		$version = ($this->getRequestParameter('version') && $this->getRequestParameter('version') == "live") ? "live" : "draft";
            		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		            
		            if($page->getType() == 'ITEMCLASS'){
            		    $assetClasses = $this->manager->getPageTemplateAssetClasses($this->getRequestParameter('page_id'), $version, $item->getId());
                        $assetClasseslist = $this->manager->getSerialisedAssetClassTree($assetClasses['tree']);
    		        }else{
    		            $assetClasses = $this->manager->getPageTemplateAssetClasses($this->getRequestParameter('page_id'), $version);
                        $assetClasseslist = $this->manager->getSerialisedAssetClassTree($assetClasses['tree']);
    		        }
            		
            		$site_id = $this->getSite()->getId();
            		$tlh = new SmartestTemplatesLibraryHelper;
            		$templates = $tlh->getMasterTemplates($this->getSite()->getId());
            		
            		$this->setTitle("Page elements");
    		
    		        if($version == 'live'){
            		    $template_name = $page->getLiveTemplate();
            		}else{
            		    $template_name = $page->getDraftTemplate();
            		}
            		
            		$template_object = $tlh->hydrateMasterTemplateByFileName($template_name, $this->getSite()->getId());
            		
            		$this->send((!$tlh->getMasterTemplateHasBeenImported($page->getDraftTemplate()) && $version == 'draft' && strlen($page->getDraftTemplate())), 'show_template_warning');
    		
            		if($page->getIsHeld() == '1' && $page->getHeldBy() == $this->getUser()->getId()){
            		    $allow_release = true;
            		}else{
            		    $allow_release = false;
            		}
    		
            		$this->send($allow_release, 'allow_release');
		
            		$mode = 'advanced';
                    
                    if($mode == 'basic'){
                        $sub_template = "getPageAssets.basic.tpl";
                    }else{
                        $sub_template = "getPageAssets.advanced.tpl";
                    }
            		
            		$this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
            		$this->send($assetClasses["tree"], "elements_tree");
                    $this->send($assetClasseslist, "elements_list");
                    // $this->send(isset($definedAssets) ? $definedAssets : array(), "definedAssets");
            		$this->send($page, "page");
            		$this->send($template_object, "page_template");
            		$this->send($templates, "templates");
            		$this->send($template_name, "templateMenuField");
            		$this->send($site_id, "site_id");
            		$this->send($version, "version");
            		$this->send($sub_template, "sub_template");
            		$this->send(true, 'allow_edit');
    		
    		    }
		    
	        }else{
	            
	            $this->addUserMessageToNextRequest("The page ID was not recognized", SmartestUserMessage::ERROR);
	            
	        }
		
	    }else{
	        
	        $page_webid = $this->getRequestParameter('page_id');
	        
	        $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());

    		if(isset($type_index[$page_webid])){
    		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		        $page = new SmartestItemPage;
    		    }else{
    		        $page = new SmartestPage;
    		    }
    		}else{
    		    $page = new SmartestPage;
    		}
    		
    		if($page->smartFind($page_webid)){
    		    $this->send($page, "page");
    		    $this->addUserMessage('You don\'t have permission to modify page elements.', SmartestUserMessage::ACCESS_DENIED);
    		    $this->send(true, 'allow_edit');
    		    $this->send('getPageAssets.disallowed.tpl', "sub_template");
    		    $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
    		    $this->send($this->getUser()->hasToken('modify_user_permissions'), 'provide_tokens_link');
    		}else{
    		    $this->addUserMessageToNextRequest("The page ID was not recognized", SmartestUserMessage::ERROR);
    		    $this->formForward();
    		}
	        
	    }
	}
	
	public function pageTags($get){
	    
	    $this->setFormReturnUri();
	    
	    $this->setTitle('Page Tags');
	    
	    $page_id = $this->getRequestParameter('page_id');
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        if($page->getType() == 'ITEMCLASS'){
	            
	            // Page is an Object meta page - force them to pick a specific item
	            $this->send(false, 'show_tags');
	            
	            $model = new SmartestModel;

                if($model->hydrate($page->getDatasetId())){
                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send('Please choose which '.$model->getName().' you would like to tag:', 'chooser_message');
                    $this->send('datamanager/itemTags', 'continue_action');
                }else{
                    $this->send(array(), 'items');
                }
                
                $this->send($page, 'page');
                
                $this->setTitle('Meta-Page Tags | Choose '.$model->getName().' to Continue');
	            
	        }else{
	            
	            // Page is a normal web page
	            $du  = new SmartestDataUtility;
	            $tags = $du->getTags();
	        
	            $page_tags = array();
	            $i = 0;
	        
	            foreach($tags as $t){
	            
	                $page_tags[$i] = $t->__toArray();
	            
	                if($t->hasPage($page->getId())){
	                    $page_tags[$i]['attached'] = true;
	                }else{
	                    $page_tags[$i]['attached'] = false;
	                }
	            
	                $i++;
	            }
	            
	            $tag_ids = $page->getTagIdsArray();
	            
	            $this->send($tag_ids, 'used_tags_ids');
	            $this->send($tags, 'tags');
	            $this->send(true, 'show_tags');
	            $this->send($page, 'page');
	            
	            $this->setTitle($page->getTitle().' | Tags');
	        
            }
            
            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
	        
	    }else{
	        $this->addUserMessage('The page ID has not been recognized.', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function updatePageTags($get, $post){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	    
	        $du  = new SmartestDataUtility;
            $tags = $du->getTags();
        
            if(is_array($this->getRequestParameter('tags'))){
                
                $page_new_tag_ids = array_keys($this->getRequestParameter('tags'));
                $page_current_tag_ids = $page->getTagIdsArray();
                
                foreach($tags as $t){
                    
                    if(in_array($t->getId(), $page_new_tag_ids) && !in_array($t->getId(), $page_current_tag_ids)){
                        $page->tag($t->getId());
                    }
                    
                    if(in_array($t->getId(), $page_current_tag_ids) && !in_array($t->getId(), $page_new_tag_ids)){
                        $page->untag($t->getId());
                    }
                    
                }
                
                $this->addUserMessageToNextRequest('The tags on this page were successfully updated.', SmartestUserMessage::SUCCESS);
                
            }else{
                // clear all page tags
                $page->clearTags();
                $this->addUserMessageToNextRequest('The tags on this page were successfully removed.', SmartestUserMessage::SUCCESS);
            }
        
        }else{
            
            // page ID wasn't recognised
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
            
        }
	    
	    $this->formForward();
	    
	}
	
	public function relatedContent($get){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->smartFind($page_webid)){
	        
	        $this->setFormReturnUri();
	        
	        if($page->getType() == 'ITEMCLASS'){
	            
	            $model = new SmartestModel;
            
                if($model->hydrate($page->getDatasetId())){
                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send('Please choose an item to attache related content.', 'chooser_message');
                    $this->send('datamanager/relatedContent', 'continue_action');
                    $this->send($page, 'page');
                }else{
                    $this->send(array(), 'items');
                }
                
                $this->send(true, 'require_item_select');
	            
	        }else{
	        
	            $this->setTitle($page->getTitle()." | Related Content");
    	        $related_pages = $page->getRelatedPages();
	        
    	        $du = new SmartestDataUtility;
    	        $models = $du->getModels(false, $this->getSite()->getId());
	        
    	        foreach($models as $k=>$m){
    	            $models[$k]->setTemporaryRelatedItems($page->getRelatedItems($m['id'], true));
    	        }
	        
    	        $this->send($page, 'page');
    	        $this->send($related_pages, 'related_pages');
        	    $this->send($models, 'models');
        	    $this->send(false, 'require_item_select');
    	    
	        }
	        
	        $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/pages');
	    }
	    
	}
	
	public function editRelatedContent($get){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->smartFind($page_webid)){
	        
	        if($this->getRequestParameter('model_id')){
	            
	            $model_id = (int) $this->getRequestParameter('model_id');
	            $model = new SmartestModel;
	            
	            if($model->hydrate($model_id)){
	                $mode = 'items';
	            }else{
	                $mode = 'pages';
	            }
            }
	        
	        $this->send($mode, 'mode');
	        
	        if($mode == 'items'){
	            $this->setTitle($page->getTitle()." | Related ".$model->getPluralName());
	            $this->send($page->__toArray(), 'page');
	            $this->send($model->__toArray(), 'model');
	            $related_ids = $page->getRelatedItemIds($model->getId());
	            $all_items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
	            $this->send($all_items, 'items');
	            $this->send($related_ids, 'related_ids');
            }else{
                $this->setTitle($page->getTitle()." | Related pages");
    	        $this->send($page->__toArray(), 'page');
    	        $related_ids = $page->getRelatedPageIds(true);
    	        $helper = new SmartestPageManagementHelper;
    	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        $this->send($pages, 'pages');
    	        $this->send($related_ids, 'related_ids');
            }
	        
	        $related_pages = $page->getRelatedPagesAsArrays();
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/pages');
	    }
	    
	}
	
	public function updateRelatedPageConnections($get, $post){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    // print_r($this->getRequestParameter('pages'));
	    
	    if($page->smartFind($page_webid)){
	        
	        if($this->getRequestParameter('pages') && is_array($this->getRequestParameter('pages'))){
	            
	            $new_related_ids = array_keys($this->getRequestParameter('pages'));
	            
	            if(count($new_related_ids)){
	            
	                $old_related_ids = $page->getRelatedPageIds(true);
        	        $helper = new SmartestPageManagementHelper;
        	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        
        	        foreach($pages as $p){
    	            
        	            if(in_array($p['id'], $new_related_ids) && !in_array($p['id'], $old_related_ids)){
        	                // add connection
        	                $page->addRelatedPage($p['id']);
        	            }
    	            
        	            if(in_array($p['id'], $old_related_ids) && !in_array($p['id'], $new_related_ids)){
        	                // remove connection
        	                $page->removeRelatedPage($p['id']);
        	            }
        	        }
    	        
	            }else{
	                
	                $page->removeAllRelatedPages();
	                
	            }
    	        
            }else{
                // No pages have been submitted, so remove all related pages
                $page->removeAllRelatedPages();
                // $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
            }
        }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function updateRelatedItemConnections($get, $post){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $model = new SmartestModel;
        
        if($model->find($this->getRequestParameter('model_id'))){
	    
    	    if($page->smartFind($page_webid)){
	        
    	        if($this->getRequestParameter('items') && is_array($this->getRequestParameter('items'))){
	            
    	            $new_related_ids = array_keys($this->getRequestParameter('items'));
	            
    	            if(count($new_related_ids)){
            
    	                $old_related_ids = $page->getRelatedItemIds($model->getId());
            	        $items = $model->getSimpleItemsAsArrays($this->getSite()->getId());
        	        
            	        foreach($items as $item){
	            
            	            if(in_array($item['id'], $new_related_ids) && !in_array($item['id'], $old_related_ids)){
            	                // add connection
            	                $page->addRelatedItem($item['id']);
            	            }
	            
            	            if(in_array($item['id'], $old_related_ids) && !in_array($item['id'], $new_related_ids)){
            	                // remove connection
            	                $page->removeRelatedItem($item['id']);
            	            }
            	        }
	        
    	            }else{
                
    	                $page->removeAllRelatedItems($model->getId());
                
    	            }
    	        
                }else{
                    // No pages have been submitted, so remove all related items
                    $page->removeAllRelatedItems($model->getId());
                    // $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
                }
            }else{
                $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
            }
        
        }else{
            $this->addUserMessageToNextRequest('The model ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function authors($get){
	    
	    if(!$this->getRequestParameter('from')){
	        $this->setFormReturnUri();
	    }
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    
	    if($page->smartFind($page_webid)){
	        
            $editable = $page->isEditableByUserId($this->getUser()->getId());
    		$this->send($editable, 'page_is_editable');
            
            if($page->getType() == 'ITEMCLASS' && (!$this->getRequestParameter('item_id') || !is_numeric($this->getRequestParameter('item_id')))){
                
                $this->send(true, 'require_item_select');
                
                $model = new SmartestModel;
                
                if($model->hydrate($page->getDatasetId())){
                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send('You need to choose an item to change authors.', 'chooser_message');
                    $this->send('websitemanager/pageAssets', 'continue_action');
                    $this->send(true, 'allow_edit');
                    $this->send($page, 'page');
                }else{
                    $this->send(array(), 'items');
                }
                
            }else{
            
	            $uhelper = new SmartestUsersHelper;
	            // $users = $uhelper->getUsersOnSiteAsArrays($this->getSite()->getId());
	            $uhelper->distributeAuthorCreditTokenFromPage($page);
	            $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
	            $this->send($users, 'users');
	            $author_ids = $page->getAuthorIds();
	            $this->send($author_ids, 'author_ids');
	            $this->send($page, 'page');
	            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
	            $this->send($this->getUser()->hasToken('modify_user_permissions'), 'provide_tokens_link');
                $this->send(false, 'require_item_select');
            
            }
	        
	    }else{
            $this->addUserMessage('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	}
	
	public function updateAuthors($get, $post){
	    
	    $page_id = (int) $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    
	    if($page->hydrate($page_id)){
	        
	        if($this->getRequestParameter('users') && count($this->getRequestParameter('users'))){
	            
	            $uhelper = new SmartestUsersHelper;
                $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
            
                $new_author_ids = array_keys($this->getRequestParameter('users'));
                $old_author_ids = $page->getAuthorIds();
                
                foreach($users as $u){
                    
                    if(in_array($u->getId(), $old_author_ids) && !in_array($u->getId(), $new_author_ids)){
                        // remove connection
                        $page->removeAuthorById($u->getId());
                    }
                    
                    if(in_array($u->getId(), $new_author_ids) && !in_array($u->getId(), $old_author_ids)){
                        // add connection
                        $page->addAuthorById($u->getId());
                    }
                }
                
                $this->addUserMessageToNextRequest('The authors of this page were sucessfully updated.', SmartestUserMessage::SUCCESS);
            
            }else{
                
                $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_AUTHORS');
        	    $q->setTargetEntityByIndex(1);
        	    $q->addQualifyingEntityByIndex(2, $page->getId());

        	    $q->addSortField('Users.user_lastname');

        	    $q->delete();
        	    
        	    $this->addUserMessageToNextRequest('The authors of this page were sucessfully removed.', SmartestUserMessage::SUCCESS);
                
            }
	        
	    }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	    $this->formForward();
	    
	}
	
	public function structure($get){
	
		$this->setFormReturnUri();
		
		$version = ($this->getRequestParameter('version') == "live") ? "live" : "draft";
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		
		$elements = $this->manager->getPageElements($this->getRequestParameter('page_id'), $version);
		
	}
	
	public function layoutPresetForm($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());

		if(isset($type_index[$page_webid])){
		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->smartFind($page_webid)){
		    
		    if($page->getType() == 'ITEMCLASS'){
	            if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
	                $page->setPrincipalItem($item);
	                $this->send($item, 'item');
	                $item_id = $this->getRequestParameter('item_id');
	            }else{
	                $item_id = false;
	            }
            }
		    
		    $page->setDraftMode(true);
		    
		    $this->setTitle('Create Preset');
		    
		    $assetClasses = $this->manager->getPageTemplateAssetClasses($page_webid, "draft", $item_id);
		    $assetClasseslist = $this->manager->getSerialisedAssetClassTree($assetClasses['tree']);
 		    
 		    $this->send($assetClasseslist, 'elements');
 		    $this->send($page, 'page');
 		
	    }
	}
	
	public function createLayoutPreset($get, $post){
	
		/* $page_id = $this->getRequestParameter('page_id');
		$user_id = $_SESSION['user']['user_id'];
		$plp_name = $this->getRequestParameter('layoutpresetname');
		$master_template =  $this->database->specificQuery("page_live_template", "page_id", $page_id, "Pages");
		$assets = $this->getRequestParameter('asset');
		
		$this->manager->setupLayoutPreset($plp_name, $assets, $master_template, $user_id, $page_id); */
		
		$num_elements = 0;
		
		$preset = new SmartestPagePreset;
		
		$preset->setOrigFromPageId($this->getRequestParameter('page_id'));
		$preset->setMasterTemplateName($preset->getOriginalPage()->getDraftTemplate());
		$preset->setCreatedByUserId($this->getUser()->getId());
		$preset->setLabel($this->getRequestParameter('preset_name'));
		$preset->setSiteId($this->getSite()->getId());
		$shared = $this->getRequestParameter('preset_shared') ? 1 : 0;
		$preset->setShared($shared);
		
		if($this->getRequestParameter('placeholder') && is_array($this->getRequestParameter('placeholder'))){
		    
		    $num_elements += count($this->getRequestParameter('placeholder'));
		    
		    foreach($this->getRequestParameter('placeholder') as $placeholder_id){
		        $preset->addPlaceholderDefinition($placeholder_id);
		    }
		    
		}
		
		if($this->getRequestParameter('container') && is_array($this->getRequestParameter('container'))){
		    $num_elements += count($this->getRequestParameter('container'));
		    
		    foreach($this->getRequestParameter('container') as $container_id){
		        $preset->addContainerDefinition($container_id);
		    }
		    
		}
		
		if($this->getRequestParameter('field') && is_array($this->getRequestParameter('field'))){
		    
		    $num_elements += count($this->getRequestParameter('field'));
		    
		    foreach($this->getRequestParameter('field') as $field_id){
		        $preset->addFieldDefinition($field_id);
		    }
		    
		}
		
		if($num_elements > 0){
		    $preset->save();
		    $this->addUserMessageToNextRequest("The new preset has been created.", SmartestUserMessage::SUCCESS);
		}
		
		$this->formForward();
	}
	
	public function defineContainer($get){
	    
	    $container_name = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
	    
	    $this->setTitle('Define container');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		$this->send($this->getApplicationPreference('define_container_list_view', 'grid'), 'list_view');
        
        $this->send($instance_name, 'instance');
	    
	    if(isset($type_index[$page_webid])){
		    
		    if($type_index[$page_webid] == 'ITEMCLASS'){
		        
		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
		            $item_id = (int) $this->getRequestParameter('item_id');
		            
    		        $page = new SmartestItemPage;
		        
    		        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    	                $page->setPrincipalItem($item);
    	                $this->send($item, 'item');
    	                $this->send(true, 'show_item_options');
    	                $this->send(false, 'require_choose_item');
    	            }else{
    	                $this->send(true, 'require_choose_item');
    	                $require_item = true;
    	            }
    	            
	            
                }else{
                    // this is a meta page, but the item id is problematic
                    $page = new SmartestItemPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
                    $this->send(true, 'require_choose_item');
                    $require_item = true;
                }
		        
		    }else{
		        // this is just a normal static page
		        $item_id = '';
		        $page = new SmartestPage;
		        $this->send(false, 'require_choose_item');
		    }
		}else{
		    $page = new SmartestPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
		}
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        if(isset($require_item) && $require_item){
                
                $model = new SmartestModel;
                
                if($model->hydrate($page->getDatasetId())){
                    $items = $model->getSimpleItems($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send($page, 'page');
                }
                
            }
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_name)){
	            
	            $this->setTitle('Define container | '.$container_name);
	            
	            $page_definition = new SmartestContainerDefinition;
	            
	            if($page_definition->load($container_name, $page, true, null, $instance_name)){
	                
                    if($type_index[$page_webid] == 'ITEMCLASS'){
	                    
	                    $item_definition = new SmartestContainerDefinition;
	                    
	                    if($item_definition->load($container_name, $page, true, $item_id, $instance_name)){
	                        
	                        if($page_definition->getDraftAssetId() == $item_definition->getDraftAssetId()){
	                            $item_uses_default = true;
	                        }else{
	                            $item_uses_default = false;
	                        }
	                        
	                        $this->send($item_definition->getDraftAssetId(), 'selected_template_id');
	                        
	                    }else{
	                        
	                        $this->send($page_definition->getDraftAssetId(), 'selected_template_id');
	                        $item_uses_default = true;
	                        
	                    }
	                    
	                    $this->send($item_uses_default, 'item_uses_default');
	                    
	                }else{
	                
	                    // container has live definition
    	                $this->send($page_definition->getDraftAssetId(), 'selected_template_id');
    	                $this->send(true, 'is_defined');
	                
                    }
	                
	            }else{
	                // container has no live definition
	                $this->send(0, 'selected_template_id');
	                $this->send(false, 'is_defined');
	            }
	            
                $assets = $container->getPossibleAssets($this->getSite()->getId());
	            
	            $this->send($assets, 'templates');
	            $this->send(count($assets), 'num_templates');
	            $this->send($page, 'page');
	            $this->send($container, 'container');
	            
	        }else{
	            
                $this->addUserMessageToNextRequest('Smartest needs to know more about the container \''.$container_name.'\' before you can define it.', SmartestUserMessage::INFO);
                $this->redirect('/websitemanager/addContainer?name='.$container_name.'&continueTo=define');
                
	        }
	    
        }else{
            // page not found
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
            $this->redirect('/smartest/pages');
        }
	    
	}
	
	public function updateContainerDefinition($get, $post){
	    
	    $container_id = $this->getRequestParameter('container_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $asset_id = $this->getRequestParameter('asset_id');
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
	    if(isset($type_index[$page_id])){
		    if($type_index[$page_id] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
	    
	    if($page->smartFind($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrate($container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            /* if($definition->loadForUpdate($container->getName(), $page)){
	                
	                // update container
	                $definition->setDraftAssetId($asset_id);
	                $definition->save();
	                
	            }else{
	                
	                // wasn't already defined
	                $definition->setDraftAssetId($asset_id);
	                $definition->setAssetclassId($container_id);
	                $definition->setInstanceName('default');
	                $definition->setPageId($page->getId());
	                $definition->save();
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time()); */
                
                if($type_index[$page_id] == 'NORMAL' || ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') != 'THIS')){
	                
	                if($definition->loadForUpdate($container->getName(), $page, true, null, $instance_name)){
	                    
	                    // update container
	                    $definition->setDraftAssetId($asset_id);
	                    $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on page '".$page->getTitle(true)."' to use asset ID ".$asset_id.".";
	                
	                }else{
	                    
	                    // wasn't already defined
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($container_id);
	                    $definition->setInstanceName($instance_name);
	                    $definition->setPageId($page->getId());
	                    $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on page '".$page->getTitle(true)."' with asset ID ".$asset_id.".";
	                
	                }
	            
	                if($this->getRequestParameter('definition_scope') == 'ALL'){
	                    
	                    // DELETE ALL PER-ITEM DEFINITIONS
	                    $pmh = new SmartestPageManagementHelper;
	                    $pmh->removePerItemDefinitions($page->getId(), $container_id);
	                    
	                }
	                
	                $definition->save();
	            
                }else if($type_index[$page_id] == 'ITEMCLASS' && ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS')){
                    
                    if($definition->loadForUpdate($container->getName(), $page, true, null, $instance_name)){ // looks for all-items definition
	                    
	                    $item_def = new SmartestContainerDefinition;
	                    
	                    // template chosen is same as all-items definition
	                    if($definition->getDraftAssetId() == $asset_id){ 
	                        
	                        // if there is already a per-item definitions for this item
	                        if($item_def->loadForUpdate($container->getName(), $page, false, $this->getRequestParameter('item_id'), $instance_name)){
	                            
	                            $item_def->delete();
                                
	                        }
	                        
	                        $log_message = $this->getUser()->__toString()." set container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." (which is the same as the all-items definition) when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                    }else{
	                        
	                        if($item_def->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'), $instance_name)){
	                            // just update container
	                            $item_def->setDraftAssetId($asset_id);
	                            $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }else{
	                            $item_def->setDraftAssetId($asset_id);
        	                    $item_def->setAssetclassId($container_id);
        	                    $item_def->setItemId($this->getRequestParameter('item_id'));
        	                    $item_def->setInstanceName($instance_name);
        	                    $item_def->setPageId($page->getId());
	                            $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }
	                        
	                        $item_def->save();
	                        
	                    }
	                
	                }else if($definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'), $instance_name) && $this->getRequestParameter('definition_scope') == 'THIS'){
	                    
	                    // all-items definition doesn't exist but per-item for this item does
	                    $definition->setDraftAssetId($asset_id);
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
                        
                        $definition->save();
    	                $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }else{
	                    
	                    // wasn't already defined for any items at all. Define for this item
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($container_id);
	                    if($this->getRequestParameter('definition_scope') == 'THIS'){$definition->setItemId($this->getRequestParameter('item_id'));}
	                    $definition->setInstanceName($instance_name);
	                    $definition->setPageId($page->getId());
                        
                        if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }
	                
                }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
                SmartestLog::getInstance('site')->log($log_message, SM_LOG_USER_ACTION);
	            
	            $this->addUserMessageToNextRequest('The container was updated.', SmartestUserMessage::SUCCESS);
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function definePlaceholder($get){
	    
	    if($this->getUser()->hasToken('modify_draft_pages')){
	    
    	    $placeholder_name = $this->getRequestParameter('assetclass_id');
    	    $page_webid = $this->getRequestParameter('page_id');
	    
    	    $this->setTitle('Define Placeholder');
	    
    	    $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
            
            $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
            // $instance_name = 'default';
            $this->send($instance_name, 'instance');
            
    	    if(isset($type_index[$page_webid])){
		    
    		    if($type_index[$page_webid] == 'ITEMCLASS'){
		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
    		            $item_id = (int) $this->getRequestParameter('item_id');
		            
        		        $page = new SmartestItemPage;
		        
        		        if($item = SmartestCmsItem::retrieveByPk($item_id)){
        	                $page->setPrincipalItem($item);
        	                $this->send($item, 'item');
        	                $this->send(true, 'show_item_options');
        	                $this->send(false, 'require_choose_item');
        	            }else{
        	                $this->send(true, 'require_choose_item');
        	                $require_item = true;
        	            }
	            
                    }else{
                        // this is a meta page, but the item id is problematic
                        $page = new SmartestItemPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
                        $this->send(true, 'require_choose_item');
                        $require_item = true;
                    }
		        
    		    }else{
    		        // this is just a normal static page
    		        $item_id = '';
    		        $page = new SmartestPage;
    		        $this->send(false, 'require_choose_item');
    		    }
    		}else{
    		    $page = new SmartestPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
    		}
		
    		if($page->hydrateBy('webid', $page_webid)){
	        
    	        $page->setDraftMode(true);
                $item_uses_default = false;
	        
    	        if(isset($require_item) && $require_item){
                
                    $model = new SmartestModel;
                
                    if($model->hydrate($page->getDatasetId())){
                        $items = $model->getSimpleItems($this->getSite()->getId());
                        $this->send($items, 'items');
                        $this->send($model, 'model');
                        $this->send($page, 'page');
                    }
                
                }
	        
    	        $placeholder = new SmartestPlaceholder;
	        
    	        if($placeholder->hydrateBy('name', $placeholder_name)){
	            
    	            $this->setTitle('Define placeholder | '.$placeholder_name);
                    
                    $this->send($placeholder->onlyAcceptsImages(), 'only_accepts_images');
                    
                    $types_array = SmartestDataUtility::getAssetTypes();
                    $params = array();
                    $page_definition = new SmartestPlaceholderDefinition;
                
                    if($page_definition->load($placeholder_name, $page, true, $this->getRequestParameter('item_id'), $instance_name)){
	                
    	                $is_defined = true;
	                
    	                if($type_index[$page_webid] == 'ITEMCLASS'){
	                    
    	                    $item_definition = new SmartestPlaceholderDefinition;
    	                    if($item_definition->load($placeholder_name, $page, true, $item_id)){
    	                        if($page_definition->getDraftAssetId() == $item_definition->getDraftAssetId()){
    	                            $item_uses_default = true;
    	                        }else{
    	                            $item_uses_default = false;
    	                        }
    	                    }else{
    	                        $item_uses_default = true;
    	                    }
    	                }
    	                
    	                $params = array();
    	                
    	                if($existing_render_data = unserialize($page_definition->getDraftRenderData())){
    	                    
    	                    if(is_array($existing_render_data)){
	                        
    	                        foreach($params as $key => $value){
    	                            if(isset($existing_render_data[$key])){
    	                                $params[$key] = $existing_render_data[$key];
    	                            }
    	                        }
                            }
                        }else{
                            $existing_render_data = array();
                        }
	                
    	                $this->send($page_definition->getDraftAssetId(), 'draft_asset_id');
    	                $this->send($page_definition->getLiveAssetId(), 'live_asset_id');
	                
    	            }else{
    	                $item_uses_default = false;
    	                $is_defined = false;
    	                $this->send($page_definition->getDraftAssetId(), 'draft_asset_id');
    	                $existing_render_data = array();
    	            }
	            
    	            $this->send($item_uses_default, 'item_uses_default');
    	            $this->send($is_defined, 'is_defined');
                    
                    $asset = new SmartestAsset;
                
                    if($this->getRequestParameter('chosen_asset_id')){
                    
                        $chosen_asset_id = (int) $this->getRequestParameter('chosen_asset_id');
                        $chosen_asset_exists = $asset->find($chosen_asset_id);
                    
            	    }else{
        	        
            	        if($is_defined){
        	            
            	            // if file is chosen
            	            if($type_index[$page_webid] == 'ITEMCLASS' && $item_definition->load($placeholder_name, $page, true, $item_id, $instance_name)){
            	                $chosen_asset_id = $item_definition->getDraftAssetId();
            	            }else{
            	                $chosen_asset_id = $page_definition->getDraftAssetId();
        	                }
    	                
            	            $chosen_asset_exists = $asset->find($chosen_asset_id);
            	            
            	        }else{
            	            // No file chosen. don't show params or 'continue' button
            	            $chosen_asset_id = 0;
            	            $chosen_asset_exists = false;
            	        }
            	    }
        	    
            	    if($chosen_asset_exists){
        	        
            	        $this->send($asset, 'asset');
        	        
            	        $type = $types_array[$asset->getType()];
        	        
            	        // Merge values for render data
        	        
            	        if(isset($type['param'])){

                	        $raw_xml_params = $type['param'];
                            $params = array();
                            
                	        foreach($raw_xml_params as $rxp){
            	            
                	            if(isset($rxp['default'])){
                	                $params[$rxp['name']]['yml_default'] = $rxp['default'];
                	                $params[$rxp['name']]['value'] = $rxp['default'];
                                }else{
                                    $params[$rxp['name']]['yml_default'] = '';
                                    $params[$rxp['name']]['value'] = '';
                                }
                            
                                $params[$rxp['name']]['type'] = $rxp['type'];
                                $params[$rxp['name']]['asset_default'] = '';
                	        }
            	        
                	        $this->send($type, 'asset_type');

                	    }else{
                	        $params = array();
                	    }
                        
                        $asset_params = $asset->getEditorParams();
                        
                        foreach($params as $key=>$p){
                	        // default values from xml are set above.
            	        
                	        // next, set values from asset
                	        if(isset($asset_params[$key]) && strlen($asset_params[$key]['value'])){
                	            // $params[$key]['value'] = $asset_params[$key];
                	            // $params[$key]['asset_default'] = $asset_params[$key];
                	        }
            	        
                	        // then, override any values that already exist
                	        if(isset($existing_render_data[$key])){
                	            $params[$key]['value'] = $existing_render_data[$key];
                	        }
                                
            	        }
                        
                        $this->send($asset_params, 'asset_params');
        	        
                	    $this->send(true, 'valid_definition');
            	    
        	        }else{
    	            
        	            $this->send(false, 'valid_definition');
    	            
        	        }
	            
    	            $this->send($params, 'params');
	            
    	            $assets = $placeholder->getPossibleAssets($this->getSite()->getId());
	            
    	            $this->send($assets, 'assets');
    	            $this->send($page, 'page');
    	            $this->send($placeholder, 'placeholder');
	            
    	        }else{
    	            
                    $this->addUserMessageToNextRequest('Smartest needs to know more about the placeholder \''.$placeholder_name.'\' before you can define it.', SmartestUserMessage::INFO);
                    $this->redirect('/websitemanager/addPlaceholder?placeholder_name='.$placeholder_name.'&continueTo=define');
                    
    	        }
	    
            }else{
                $this->addUserMessageToNextRequest("The page ID was not recognized", SM_USER_MESSAGE_WARNING);
                $this->redirect('/smartest/pages');
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to update placeholders.", SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
        
	}
	
	public function definePlaceholderWithNewFile(){
	    
	    $placeholder_name = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
        
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
        // $instance_name = 'default';
        $this->send($instance_name, 'instance');
        
	    if(isset($type_index[$page_webid])){
	    
		    if($type_index[$page_webid] == 'ITEMCLASS'){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		    
		}else{
		    $page = new SmartestPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
		}
		
		if($page->hydrateBy('webid', $page_webid)){
        
	        $placeholder = new SmartestPlaceholder;
        
	        if($placeholder->hydrateBy('name', $placeholder_name)){
	            
	            $redirect_url = '/assets/startNewFileCreationForPlaceholderDefinition?placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
	            
	            if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
	                $redirect_url .= '&item_id='.$this->getRequestParameter('item_id');
	            }
	            
	            $this->redirect($redirect_url);
	            
	        }else{
	            
	            // placeholder with that name was not found
	            
	            $redirect_url = '/websitemanager/pageAssets?page_id='.$page->getWebid();
	            
	            if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
	                $redirect_url .= '&item_id='.$this->getRequestParameter('item_id');
	            }
	            
	            $this->addUserMessageToNextRequest('A placeholder with that name was not found', SmartestUserMessage::ERROR);
	            $this->redirect($redirect_url);
	            
	        }
	    
	    }else{
	        // page with that webid was not found
	        
	        $this->addUserMessageToNextRequest('A page with that ID was not found', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/pages');
	        
	    }
	    
	}
	
	public function updatePlaceholderDefinition($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('placeholder_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $asset_id = $this->getRequestParameter('asset_id');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
        
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
		// $instance_name = 'default';
        
	    if(isset($type_index[$page_id])){
		    if($type_index[$page_id] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrate($placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if($type_index[$page_id] == 'NORMAL' || ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') != 'THIS')){
	                
                    if($definition->loadForUpdate($placeholder->getName(), $page, null, $instance_name)){
	                
	                    // update placeholder
	                    $definition->setDraftAssetId($asset_id);
	                    $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' to use asset ID ".$asset_id.".";
	                
	                }else{
	                
	                    // wasn't already defined
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($placeholder_id);
	                    $definition->setInstanceName($instance_name);
	                    $definition->setPageId($page->getId());
	                    $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' with asset ID ".$asset_id.".";
	                
	                }
                    
                    if(is_array($this->getRequestParameter('params'))){
                        // TODO: Go through these parameters, eliminating illegal values such as blanks where a value is required
	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
	                }
	                
	                if($this->getRequestParameter('definition_scope') == 'ALL'){
	                    
	                    // DELETE ALL PER-ITEM DEFINITIONS
	                    $pmh = new SmartestPageManagementHelper;
	                    $pmh->removePerItemDefinitions($page->getId(), $placeholder->getId());
	                    
	                }
	                
	                $definition->save();
	            
                }else if($type_index[$page_id] == 'ITEMCLASS' && ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS')){
                    
                    if($definition->loadForUpdate($placeholder->getName(), $page)){ // looks for all-items definition
	                    
	                    $item_def = new SmartestPlaceholderDefinition;
	                    
	                    // item chosen is same as all-items definition
	                    if($definition->getDraftAssetId() == $asset_id){ 
	                        
	                        if(is_array($this->getRequestParameter('params'))){
	                            $now_prms = $this->getRequestParameter('params'); // copy needs to be made here because ksort() does not return
	                            $ex_prms = $definition->getRenderData(true);
	                            $default_def_params_hash = md5(serialize($ex_prms));
	                            $this_item_params_hash = md5(serialize($now_prms));
	                            $has_params = true;
                            }else{
                                $has_params = false;
                            }
	                        
	                        // if there is already a per-item definitions for this item
	                        if($item_def->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                            
	                            if($has_params && ($default_def_params_hash != $this_item_params_hash)){
	                                // don't delete, because display params are different to default.
                                    // TODO: Go through these parameters, eliminating illegal values such as blanks where a value is required
	                                $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
	                                $item_def->save();
	                            }else{
	                                $item_def->delete();
                                }
                                
	                        }else{ // No per-item definition found for this one so create *IF* the params are different.
	                            if($has_params && ($default_def_params_hash != $this_item_params_hash)){
	                                $item_def->setDraftAssetId($asset_id);
        	                        $item_def->setAssetclassId($placeholder_id);
        	                        $item_def->setItemId($this->getRequestParameter('item_id'));
        	                        $item_def->setInstanceName('default');
        	                        $item_def->setPageId($page->getId());
                                    // TODO: Go through these parameters, eliminating illegal values such as blanks where a value is required
	                                $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
                                    $item_def->save();
                                }
                                
	                        }
	                        
	                        $log_message = $this->getUser()->__toString()." set placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." (which is the same as the all-items definition) when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                    }else{
	                        
	                        if($item_def->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                            // just update placeholder
	                            $item_def->setDraftAssetId($asset_id);
	                            $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }else{
	                            $item_def->setDraftAssetId($asset_id);
        	                    $item_def->setAssetclassId($placeholder_id);
        	                    $item_def->setItemId($this->getRequestParameter('item_id'));
        	                    $item_def->setInstanceName('default');
        	                    $item_def->setPageId($page->getId());
	                            $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }
	                        
	                        if(is_array($this->getRequestParameter('params'))){
                                // TODO: Go through these parameters, eliminating illegal values such as blanks where a value is required
        	                    $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
        	                }
        	                
        	                $item_def->save();
	                        
	                    }
	                
	                }else if($definition->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                    
	                    // all-items definition doesn't exist but per-item for this item does
	                    $definition->setDraftAssetId($asset_id);
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }else{
	                    
	                    // wasn't already defined for any items at all. Define for this item
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($placeholder_id);
	                    $definition->setItemId($this->getRequestParameter('item_id'));
	                    $definition->setInstanceName('default');
	                    $definition->setPageId($page->getId());
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }
	                
                }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	            $this->addUserMessageToNextRequest('The placeholder was updated.', SmartestUserMessage::SUCCESS);
	            SmartestLog::getInstance('site')->log($log_message, SM_LOG_USER_ACTION);
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefinePlaceholder($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $item_id = $this->getRequestParameter('item_id') ? $this->getRequestParameter('item_id') : false;
        
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
        // $instance_name = 'default';
	    
	    $this->setTitle('Un-Define Placeholder');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrateBy('name', $placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if(is_numeric($item_id) && $definition->loadForUpdate($placeholder->getName(), $page, $item_id, $instance_name)){
	                
	                // update placeholder
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The placeholder definition was removed for this item.', SmartestUserMessage::SUCCESS);
	            
	            }else if($definition->loadForUpdate($placeholder->getName(), $page, null, $instance_name)){
	                
	                // update placeholder
	                $definition->setDraftAssetId(null);
	                $definition->save();
	                $this->addUserMessageToNextRequest('The draft placeholder definition was removed. Next time the page is published, the placeholder will be emptied.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The placeholder wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefinePlaceholderOnItemPage($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $item_id = $this->getRequestParameter('item_id');
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
	    
	    $this->setTitle('Un-Define Placeholder');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrateBy('name', $placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if($definition->loadForUpdate($placeholder->getName(), $page, $item_id, $instance_name)){
	                
	                // update placeholder
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The placeholder definition was removed for this item.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The placeholder wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefineContainer($get, $post){
	    
	    $container_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            if($this->getRequestParameter('item_id') && $definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'), $instance_name)){
	            
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	            
	            }else if($definition->loadForUpdate($container->getName(), $page, true, null, $instance_name)){
	                
	                // update placeholder
	                // $definition->delete();
	                $definition->setDraftAssetId('');
	                $definition->save();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The container wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefineContainerOnItemPage($get, $post){
	    
	    $container_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
        $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            if($this->getRequestParameter('item_id') && $definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'), $instance_name)){
	            
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	            
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The container wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function editAttachment($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $parts = explode(':', $id);
	    $asset_stringid = $parts[0];
	    $attachment = $parts[1];
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrateBy('stringid', $asset_stringid, $this->getSite()->getId())){
	        $this->redirect('/assets/defineAttachment?attachment='.$attachment.'&asset_id='.$asset->getId());
	    }else{
	        
	        if(strlen($page_webid) == 32){
	            $this->addUserMessageToNextRequest("The attachment ID was not recognized.", SmartestUserMessage::ERROR);
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid); 
	        }else{
	            $this->redirect('/smartest/pages');
	        }
	    }
	}
	
	public function editFile($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrateBy('stringid', $id, $this->getSite()->getId())){
            $this->redirect('/assets/editAsset?assettype_code='.$asset->getType().'&asset_id='.$asset->getId().'&from=pageAssets');
        }else{
            if(strlen($page_webid) == 32){
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
	            $this->addUserMessageToNextRequest("The file ID was not recognized.", SmartestUserMessage::ERROR);
	        }else{
	            $this->redirect('/smartest/pages');
	        }
        }
	}
    
    public function editBlocklist(){
        
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
		if(isset($type_index[$page_webid])){
		    if(($type_index[$page_webid] == 'ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_DATASET') && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
        
        $page_webid = $this->getRequestParameter('page_id');
        
        if($page->smartFind($page_webid)){
            
            $blocklist_name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
            $this->redirect('/blocklists/editBlockList?page_id='.$page->getId().'&blocklist_name='.$blocklist_name);
            
        }
        
    }
	
	public function editTemplate($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $asset = new SmartestTemplateAsset;
        
        if($asset->findBy('stringid', $id, $this->getSite()->getId())){
            $this->redirect('/templates/editTemplate?type=SM_ASSETTYPE_CONTAINER_TEMPLATE&template='.$asset->getId().'&from=pageAssets');
        }else{
            if(strlen($page_webid) == 32){
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
	            $this->addUserMessageToNextRequest("The template ID was not recognized.", SmartestUserMessage::ERROR);
	        }else{
	            $this->redirect('/smartest/pages');
	        }
        }
	}
	
	public function setPageTemplate($get){
		
		$template_name = $this->getRequestParameter('template_name');
		$page_id = $this->getRequestParameter('page_id');
		
		if(is_file(SM_ROOT_DIR.'Presentation/Masters/'.$template_name)){
		    SmartestDatabase::getInstance('SMARTEST')->query("UPDATE Pages SET Pages.page_draft_template='$template_name' WHERE Pages.page_webid='$page_id'");
	    }else if(!strlen($template_name)){
	        SmartestDatabase::getInstance('SMARTEST')->query("UPDATE Pages SET Pages.page_draft_template='' WHERE Pages.page_webid='$page_id'");
	    }
	    
	    $this->formForward();
		
	}
	
	/* function setPageTemplateForLists($get){
		$template_name = $get["template_name"];
		$version = ($get["version"] == "live") ? "live" : "draft";
		$field = ($get["version"] == "live") ? "page_live_template" : "page_draft_template";
		$page_id = $get["page_id"];
		$this->database->query("UPDATE Pages SET $field='$template_name' WHERE page_webid='$page_id'");
		header("Location:".$this->domain.$this->module."/getPageLists?page_id=$page_id&version=$version");
	} */
	
	public function setDraftAsset($get){

		$this->manager->setDraftAsset($this->getRequestParameter('page_id'), $this->getRequestParameter('assetclass_id'), $this->getRequestParameter('asset_id'));
		$this->formForward();
		
	}
	
	function setLiveAsset($get){
		
		$this->manager->setLiveAsset($this->getRequestParameter('page_id'), $this->getRequestParameter('assetclass_id'));
		
		$page_pk = $this->manager->database->specificQuery("page_id", "page_webid", $this->getRequestParameter('page_id'), "Pages");
		
		if(is_numeric($this->getRequestParameter('assetclass_id')) && $this->getRequestParameter('assetclass_id')){
			$assetclass = $this->manager->database->specificQuery("assetclass_name", "assetclass_id", $this->getRequestParameter('assetclass_id'), "AssetClasses");
		}else{
			$assetclass = $this->getRequestParameter('assetclass_id');
		}
		
		
		// This code clears the cached placeholders
		$cache_filename = "System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".$page_pk.".tmp";
		
		if(is_file($cache_filename) && SM_OPTIONS_CACHE_ASSETCLASSES){
			@unlink($cache_filename);
		}
		
		$this->formForward();
	}
	
	/* function publishPageContainersConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedContainerClasses=$this->manager->publishPageContainersConfirm($page_webid,$version);
		$count=count($undefinedContainerClasses);
		return array ("undefinedContainerClasses"=>$undefinedContainerClasses,"page_id"=>$page_webid,"count"=>$count);
	}
	
	function publishPageContainers($get){
		$page_webid=$this->getRequestParameter('page_id');
// 		echo $page_webid;
		$this->manager->publishPageContainers($page_webid);
		$this->formForward();
	}
	
	function publishPagePlaceholdersConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedPlaceholderClasses=$this->manager->publishPagePlaceholdersConfirm($page_webid,$version);
		$count=count($undefinedPlaceholderClasses);
		return array ("undefinedPlaceholderClasses"=>$undefinedPlaceholderClasses,"page_id"=>$page_webid,"count"=>$count);
			
	}
	
	function publishPagePlaceholders($get){
		$page_webid=$this->getRequestParameter('page_id');
		$this->manager->publishPagePlaceholders($page_webid);
		$this->formForward();
	} */
	
	public function publishPageConfirm($get){
		
		// display to the user a list of any placeholders or containers that are undefined in the draft page that is about to be published,
		// so that the user is warned before publishing undefined placeholders or containers that may cause the page to display incorrectly
		// the user should be able to publish either way - the notice will be just a warning.
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		$page_webid = $this->getRequestParameter('page_id');
		
	    if(isset($type_index[$page_webid])){
		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->smartFind($page_webid)){
		    
		    $this->send($this->getSite()->getIsEnabled(), 'site_enabled');
            $this->send(!($this->getSite()->getIsEnabled() || $page->getId() == $this->getSite()->getHoldingPageId()), 'show_site_disabled_warning');
		    
		    if($page->getType() == 'ITEMCLASS'){
                if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                    
                    $page->setPrincipalItem($item);
                    $this->send($item, 'item');
                    $item_id = $this->getRequestParameter('item_id');
                    
                    $user_can_publish_item = ($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items');
                    
                    $this->send($user_can_publish_item, 'user_can_publish_item');
                    
                }else{
                    $item_id = false;
                }
            }
            
            $page->setDraftMode(true);
		    
		    if(( (boolean) $page->getChangesApproved() && $this->getUser()->hasToken('publish_approved_pages')) || $this->getUser()->hasToken('publish_all_pages')){
		        
		        $version = "draft";
		        $undefinedAssetsClasses = $this->manager->getUndefinedElements($page_webid, 'draft', $item_id);
		        
                $count = count($undefinedAssetsClasses);
		        $this->send(true, 'allow_publish');
		        $this->send($undefinedAssetsClasses, "undefined_asset_classes");
		        // $this->send($page->getWebId(), "page_id");
				$this->send($page, 'page');
				$this->send($page->isHomePage(), 'is_homepage');
		        $this->send(count($undefinedAssetsClasses), "count");
		        
		        $changed_itemspaces_containing_unpublished_items = $page->getChangedItemspaceDefinitions(true, $item_id);
		        
		        if(count($changed_itemspaces_containing_unpublished_items)){
    		        $this->send(true, 'show_itemspace_publish_warning');
    		        $this->send($changed_itemspaces_containing_unpublished_items, 'itemspaces');
		        }else{
    		        $this->send(false, 'show_itemspace_publish_warning');
		        }
		    
	        }else{
	            
	            $this->send(false, 'allow_publish');
	            $this->send($page->getWebId(), "page_id");
	            
	            if((boolean) $page->getChangesApproved()){
		            $this->addUserMessage('You can\'t publish this page because you don\'t have permission to publish pages.', SmartestUserMessage::ACCESS_DENIED);
		        }else{
		            $this->addUserMessage('You can\'t publish this page because the changes on it haven\'t yet been approved and you don\'t have permission to override approval.', SmartestUserMessage::ACCESS_DENIED);
		        }
	            
	        }
		
	    }else{
	        
	        $this->addUserMessage('The page could not be found');
	        
	    }
			
	}
	
	public function publishPage($get, $post){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
        
	    if($this->getRequestParameter('item_id')){$item_id = (int) $this->getRequestParameter('item_id');}else{$item_id = false;}
	    
	    if($page->smartFind($page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        if(((boolean) $page->getChangesApproved() || $this->getUser()->hasToken('approve_page_changes')) && ($this->getUser()->hasToken('publish_approved_pages')) || $this->getUser()->hasToken('publish_all_pages')){
		        
		        $page->publish($item_id);
		        $changed_itemspaces_containing_unpublished_items = $page->getChangedItemspaceDefinitions(true, $item_id);
		        
		        if(count($changed_itemspaces_containing_unpublished_items)){
    		        if($this->getRequestParameter('itemspace_action') == 'publish'){
        		        $published_unpublished_items = true;
    		        }else{
        		        $published_unpublished_items = false;
    		        }
		        }else{
    		        $published_unpublished_items = false;
		        }
				
				if($this->getRequestParameter('clear_parent_from_cache') && !$page->isHomePage() && $parent = $page->getParentPage()){
					$parent->clearCachedCopies();
				}
		        
		        $page->publishItemSpaces($published_unpublished_items, $item_id);
		        $page->setModified(time());
		        $page->save();
		        
		        SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} published page: {$page->getTitle()}.", SmartestLog::USER_ACTION);
		        
		        if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                    
                    $user_can_publish_item = ($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items');
                    
                    if($user_can_publish_item){
                        if($this->getRequestParameter('publish_item') == 'PUBLISH'){
                            $item->publish();
                            $this->addUserMessageToNextRequest('The page and the item '.$item->getName().' have both been successfully published.', SmartestUserMessage::SUCCESS);
                        }else{
                            $this->addUserMessageToNextRequest('The page has been successfully published.', SmartestUserMessage::SUCCESS);
                        }
                    }else{
                        $this->addUserMessageToNextRequest('The page has been successfully published, but the item could not be published.', SmartestUserMessage::INFO);
                    }
                    
                }else{
                    
                    $this->addUserMessageToNextRequest('The page has been successfully published.', SmartestUserMessage::SUCCESS);
                    
                }
		        
	        }else{
	            
	            if((boolean) $page->getChangesApproved()){
		            $this->addUserMessageToNextRequest('The page could not be published because you don\'t have permission to publish pages', SmartestUserMessage::ACCESS_DENIED);
		        }else{
		            $this->addUserMessageToNextRequest('The page could not be published because the changes on it haven\'t yet been approved and you don\'t have permission to approve pages', SmartestUserMessage::ACCESS_DENIED);
		        }
	            
	        }
        }
        
        $this->formForward();
	}
	
	public function unPublishPage($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
		$page = new SmartestPage;
		
		if($page->smartFind($page_webid)){
		    
		    $page->setDraftMode(true);
		    $page->unpublish();
            SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
		    
		    SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} un-published page: {$page->getTitle()}.", SmartestLog::USER_ACTION);
		    
		}
		
		$this->addUserMessageToNextRequest('The page has been un-published. No other changes have been made.', SmartestUserMessage::SUCCESS);
		$this->formForward();
		
	}

	public function getPageLists($get){
		
		$this->setFormReturnUri();
		
		$page_webid = $this->getRequestParameter('page_id');
		$version = ($this->getRequestParameter('version') == "live") ? "live" : "draft";
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		$site_id = $this->database->specificQuery("page_site_id", "page_webid", $this->getRequestParameter('page_id'), "Pages");
		$page = $this->manager->getPage($this->getRequestParameter('page_id'));
		$pageListNames = $this->manager->getPageLists($page_webid, $version);
 		
 		return array("pageListNames"=>$pageListNames,"page"=>$page,"version"=>$version,"templateMenuField"=>$page[$field],"site_id"=>$site_id);	
	}
	
	public function defineList($get){
        
        $templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/ListItems/');
        
        $list_name = $this->getRequestParameter('assetclass_id');
        
        $page_webid = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->smartFind($page_webid)){
            
            $page->setDraftMode(true);
            $this->setTitle('Define list parameters');
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
                $already_defined = true;
            }else{
                // this is a new list
                $already_defined = false;
            }
            
            // $this->send($list->getDraftHeaderTemplate(), 'header_template');
            // $this->send($list->getDraftFooterTemplate(), 'footer_template');
            $this->send($list->getDraftTemplateFile(), 'main_template');
            $this->send($list->getDraftSetId(), 'set_id');
            $this->send($list, 'list');
            $this->send($list_name, 'list_name');
            $this->send(is_writable(SM_ROOT_DIR.'Presentation/Layouts/'), 'can_create_template');
            
            $alh = new SmartestAssetsLibraryHelper;
            $this->send($alh->getAssetsByTypeCode('SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE', $this->getSite()->getId()), 'list_templates');
            
            $tlh = new SmartestTemplatesLibraryHelper;
            $this->send($tlh->getArticulatedListTemplates($this->getSite()->getId()), 'art_list_templates');
            
            $du = new SmartestDataUtility;
            
            // $sets = $datautil->getDataSetsAsArrays(false, $this->getSite()->getId());
            
            $models = $du->getVisibleModels($this->getSite()->getId());
            $tags = $du->getTags();
            
            if($already_defined){
                
                $model = new SmartestModel;
                $model_id = $list->getDraftSetFilter();
                
                if(is_numeric($model_id) && $model->find($model_id)){
                    $this->send($model, 'chosen_model');
                    $model_defined = true;
                }else{
                    $models2 = $models;
                    $model = array_shift($models2);
                    $model_defined = false;
                    $this->send($model, 'chosen_model');
                }
                
                $this->send($model_defined, 'model_defined');
                $sets = $model->getDataSets($this->getSite()->getId());
                
                if($list->getType() == 'SM_LIST_TAG'){
                    
                    $tag = new SmartestTag;
                    $tag_id = $list->getDraftSetId();
                    
                    if(is_numeric($tag_id) && $tag->find($tag_id)){
                        $this->send($tag, 'chosen_tag');
                        $this->send($tag->getId(), 'chosen_tag_id');
                        $this->send(true, 'tag_defined');
                    }else{
                        $this->send(false, 'tag_defined');
                        $this->send(null, 'chosen_tag_id');
                    }
                    
                    // TODO: Add second tag functionality
                    // $tag_2 = new SmartestTag;
                    // $tag_id = $list->getDraftSecondarySetId();
                    // 
                    // if(is_numeric($tag_id) && $tag2->find($tag_id)){
                    //     $this->send($tag2, 'chosen_secondary_tag');
                    //     $this->send(true, 'secondary_tag_defined');
                    // }else{
                    //     $this->send(false, 'secondary_tag_defined');
                    // }
                    
                }elseif($list->getType() == 'SM_LIST_SIMPLE'){
                    
                }
                
            }else{
                
                $models2 = $models;
                $model = array_shift($models2);
                $this->send($model, 'chosen_model');
                $sets = $model->getDataSets($this->getSite()->getId());
                $this->send(false, 'model_defined');
                
            }
            
            $this->send($tags, 'tags');
            $this->send($models, 'models');
            $this->send($sets, 'sets');
            $this->send($page, 'page');
            $this->send($templates, 'templates');
            
        }else{
            // page was not found
            $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
		/* $page_id = $this->manager->getPageIdFromPageWebId($this->getRequestParameter('page_id'));
		$list_name = $this->getRequestParameter('list_id');

		$page = $this->manager->getPage($page_id);
		$sets = $this->setsManager->getSets();
		// $path = 'Presentation/ListItems'; 
		// $listitemtemplates = $this->templatesManager->getTemplateNames($path);
		
		$sql = "SELECT * FROM Lists WHERE list_page_id = '$page_id' AND list_name = '$list_name'";
		$result = $this->database->queryToArray($sql);
		$items = $this->manager->managePageData($result);
 		
 		$list_setid = $result[0]['list_draft_set_id'];
		$list_template = $result[0]['list_draft_template_file'];
		$list_header = $result[0]['list_draft_header_template'];
		$list_footer = $result[0]['list_draft_footer_template']; */
		
		// return array("page"=>$page, "sets"=>$sets, "listitemtemplates"=>$templates, "list_setid"=>$list_setid, "list_template"=>$list_template, "list_header"=>$list_header, "list_footer"=>$list_footer,"list_name"=>$list_id);
	
	}
	
	public function saveList($get, $post){
	    
	    $list_name = $this->getRequestParameter('list_name');
        
        $page_id = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->hydrate($page_id)){
            
            $page->setDraftMode(true);
            
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was updated successfully.", SmartestUserMessage::SUCCESS);
                $log_message = "{$this->getUser()->getFullname()} updated list '{$list->getName()}' on page '{$page->getTitle()}'.";
            }else{
                // this is a new list
                $list->setName($this->getRequestParameter('list_name'));
                $list->setPageId($page->getId());
                $log_message = "{$this->getUser()->getFullname()} created a list entitled '{$list->getName()}' on page '{$page->getTitle()}'.";
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was defined successfully.", SmartestUserMessage::SUCCESS);
            }
            
            $list_type = in_array($this->getRequestParameter('list_type'), array('SM_LIST_TAG', 'SM_LIST_SIMPLE')) ? $this->getRequestParameter('list_type') : 'SM_LIST_SIMPLE';
            
            $list->setType($list_type);
            $list->setMaximumLength((int) $this->getRequestParameter('list_maximum_length'));
            $list->setTitle($this->getRequestParameter('list_title'));
            
            /* if($list_type == 'SM_LIST_ARTICULATED'){
            
                $templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/ListItems/');
            
                if(is_numeric($this->getRequestParameter('dataset_id'))){
                    $list->setDraftSetId($this->getRequestParameter('dataset_id'));
                }
            
                if(in_array($this->getRequestParameter('header_template'), $templates)){
                    $list->setDraftHeaderTemplate($this->getRequestParameter('header_template'));
                }
            
                if(in_array($this->getRequestParameter('footer_template'), $templates)){
                    $list->setDraftFooterTemplate($this->getRequestParameter('footer_template'));
                }
            
                if(in_array($this->getRequestParameter('main_template'), $templates)){
                    $list->setDraftTemplateFile($this->getRequestParameter('main_template'));
                }
            
            }else{ */
                
                if($list_type == 'SM_LIST_SIMPLE' && is_numeric($this->getRequestParameter('dataset_id'))){
                    
                    $dataset_id = $this->getRequestParameter('dataset_id');
                    $set = new SmartestCmsItemSet;
                    
                    if($set->find($dataset_id)){
                        $list->setDraftSetId((int) $this->getRequestParameter('dataset_id'));
                        $model = $set->getModel();
                    }else{
                        $this->addUserMessageToNextRequest('The selected set or items could not be found.', SmartestUserMessage::ERROR);
                    }
                    
                }elseif($list_type == 'SM_LIST_TAG' && is_numeric($this->getRequestParameter('tag_id'))){
                    
                    $tag_id = $this->getRequestParameter('tag_id');
                    $tag = new SmartestTag;
                    
                    if($tag->find($tag_id)){
                        $list->setDraftSetId((int) $tag_id);
                    }else{
                        $this->addUserMessageToNextRequest('The selected set or items could not be found.', SmartestUserMessage::ERROR);
                    }
                    
                }
                
                $m = new SmartestModel;
                
                if(is_numeric($this->getRequestParameter('list_member_filter_id')) && $m->find($this->getRequestParameter('list_member_filter_id'))){
                    $list->setDraftSetFilter($this->getRequestParameter('list_member_filter_id'));
                }else{
                    $this->addUserMessageToNextRequest('The model ID was not recognised', SmartestUserMessage::ERROR, true);
                }
                
                $a = new SmartestAsset;
                
                if(is_numeric($this->getRequestParameter('list_header_image_id')) && $a->find($this->getRequestParameter('list_header_image_id'))){
                    $list->setDraftHeaderImageId($this->getRequestParameter('list_header_image_id'));
                }else{
                    $this->addUserMessageToNextRequest('The header image ID was not recognised', SmartestUserMessage::ERROR, true);
                }
                
                if($this->getRequestParameter('art_main_template') == 'NEW'){
                    
                    $template_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Install/Samples/simple_list_template.tpl');
                    $template_intended_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/list_'.$list->getName().'.tpl');
                    $template_contents = str_replace('%LOCATION%', $template_intended_path, $template_contents);
                    $template_contents = str_replace('%LISTNAME%', $list->getName(), $template_contents);
                    SmartestFileSystemHelper::save($template_intended_path, $template_contents);
                    
                    // Create template record
                    $t = new SmartestTemplateAsset;
                    $t->setType('SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE');
                    $t->setSiteId($this->getSite()->getId());
                    $t->setCreated(time());
                    $t->setUserId($this->getUser()->getId());
                    $t->setWebId(SmartestStringHelper::random(32, SM_RANDOM_ALPHANUMERIC));
                    $t->setLabel('Template for list '.$list->getName());
                    $t->setStringId('list_'.$list->getName());
                    if(is_object($model)) $t->setModelId($model->getId());
                    $t->setUrl(SmartestFileSystemHelper::baseName($template_intended_path));
                    $t->save();
                    
                    $list->setDraftTemplateFile($t->getUrl());
                    
                }else{
                    $list->setDraftTemplateFile($this->getRequestParameter('art_main_template'));
                }
                
            // }
            
            SmartestLog::getInstance('site')->log($log_message, SmartestLog::USER_ACTION);
            
            $list->save();
            
            $this->formForward();
            
            // print_r($list->__toArray());
            /* $this->send($list->getDraftHeaderTemplate(), 'header_template');
            $this->send($list->getDraftFooterTemplate(), 'footer_template');
            $this->send($list->getDraftTemplateFile(), 'main_template');
            $this->send($list->getDraftSetId(), 'set_id');
            $this->send($list->__toArray(), 'list');
            $this->send($list_name, 'list_name');
            
            $sets = $this->getSite()->getDataSetsAsArrays();
            $this->send($sets, 'sets');
            $this->send($page->__toArray(), 'page');
            $this->send($templates, 'templates'); */
            
        }else{
            // page was not found
            $this->addUserMessageToNextRequest("The page ID was not recognizsed.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
	    
	}
	
	public function clearList($get){
	    
	    $list_name = $this->getRequestParameter('assetclass_id');
        
        $page_id = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->hydrate($page_id)){
            
            $page->setDraftMode(true);
            
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
                $list->delete();
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was updated successfully.", SmartestUserMessage::SUCCESS);
            }else{
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was not defined.", SmartestUserMessage::INFO);
            }
            
            $this->formForward();
            
        }else{
            
            $this->addUserMessageToNextRequest("The page ID was not recognizsed.", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
	    
	}
	
	/* function insertList($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page_id=$this->manager->getPageIdfromPageWebId($page_webid);
		$list_name = $this->getRequestParameter('list_name');
		$set_id = $this->getRequestParameter('dataset');
		$list_template = $this->getRequestParameter('listtemplate_name');
		$header_template = $this->getRequestParameter('header_template');
		$footer_template = $this->getRequestParameter('footer_template');
		$this->manager->insertList($page_id,$list_name,$set_id,$list_template,$header_template,$footer_template);
		
		$this->formForward();
			
	} */
	
	public function publishListsConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedLists=$this->manager->publishListsConfirm($page_webid, $version);
		$count=count($undefinedLists);
		return array ("undefinedLists"=>$undefinedLists,"page_id"=>$page_webid,"count"=>$count);
	}
	
	public function publishPageLists($get){
		$page_webid=$this->getRequestParameter('page_id');
		$this->manager->publishPageLists($page_webid);
		$this->formForward();
	}
	
	public function addItemSpace($get){
	    
	    $new_name = SmartestStringHelper::toVarName($this->getRequestParameter('name'));
	    $item_space = new SmartestItemSpace;
	    
	    if($item_space->exists($new_name, $this->getSite()->getId())){
	        // item space already exists with this name
	        $this->send(false, 'allow_continue');
	    }else{
	        
            $this->send(SmartestStringHelper::toTitleCaseFromVarName($new_name), 'suggested_label');
            
	        // get templates
	        $assetshelper = new SmartestAssetsLibraryHelper;
	        $templates = $assetshelper->getAssetsByTypeCode('SM_ASSETTYPE_ITEMSPACE_TEMPLATE', $this->getSite()->getId());
	        $this->send($templates, 'templates');
	        
	        // get sets
	        $du = new SmartestDataUtility;
	        $sets = $du->getDataSets(false, $this->getSite()->getId());
	        $this->send($sets, 'sets');
            
            // Check other things
            $this->send(is_writable(SM_ROOT_DIR.'Presentation/Layouts/'), 'can_create_template');
	        
	        $this->send($new_name, 'name');
	        
	        $this->send(true, 'allow_continue');
	    }
	    
	}
	
	public function insertItemSpace($get, $post){
	    
	    $new_name = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_name'));
	    $item_space = new SmartestItemSpace;
	    
	    if(strlen($new_name)){
	    
	        if($item_space->exists($new_name, $this->getSite()->getId())){
	            // item space already exists with this name
	            $this->addUserMessageToNextRequest('An itemspace with that name already exists', SmartestUserMessage::WARNING);
	        }else{
	        
	            $item_space->setName($new_name);
	            $item_space->setLabel($new_name);
	            $item_space->setSiteId($this->getSite()->getId());
	        
	            $dataset_id = (int) $this->getRequestParameter('itemspace_dataset_id');
                $dataset = new SmartestCmsItemSet;
                
                if($dataset->find($dataset_id)){
                    $item_space->setDataSetId($dataset_id);
                    $model = $dataset->getModel();
                }else{
                    $this->addUserMessageToNextRequest('The chosen set was not recognized.', SmartestUserMessage::ERROR);
                    $this->formForward();
                }
	        
	            $use_template = $this->getRequestParameter('itemspace_use_template');
	            $item_space->setUsesTemplate($use_template);
	        
	            if($use_template){
                    
                    $template_id = $this->getRequestParameter('itemspace_template_id');
                    
                    if($template_id == 'NEW'){
                        
                        $template_contents = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Install/Samples/itemspace_template.tpl');
                        $template_intended_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Layouts/itemspace_'.$new_name.'.tpl');
                        $template_contents = str_replace('%LOCATION%', $template_intended_path, $template_contents);
                        $template_contents = str_replace('%MODELNAME%', $model->getName(), $template_contents);
                        SmartestFileSystemHelper::save($template_intended_path, $template_contents);
                        
                        // Create template record
                        $t = new SmartestTemplateAsset;
                        $t->setType('SM_ASSETTYPE_ITEMSPACE_TEMPLATE');
                        $t->setSiteId($this->getSite()->getId());
                        $t->setCreated(time());
                        $t->setUserId($this->getUser()->getId());
                        $t->setWebId(SmartestStringHelper::random(32, SM_RANDOM_ALPHANUMERIC));
                        $t->setLabel('Template for itemspace '.$item_space->getLabel());
                        $t->setStringId('itemspace_'.$item_space->getName());
                        $t->setModelId($model->getId());
                        $t->setUrl(SmartestFileSystemHelper::baseName($template_intended_path));
                        $t->save();
                        
                        $item_space->setTemplateAssetId($t->getId());
                        
                    }elseif(is_numeric($template_id)){
	                    $template_id = (int) $this->getRequestParameter('itemspace_template_id');
    	                $item_space->setTemplateAssetId($template_id);
                    }
	            }
	            
	            SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} created an itemspace with the name '{$item_space->getName()}'.", SmartestLog::USER_ACTION);
	        
	            $this->addUserMessageToNextRequest('An itemspace called \''.$new_name.'\' has been created.', SmartestUserMessage::SUCCESS);
	            $item_space->save();
	        }
	        
        }else{
            $this->addUserMessageToNextRequest('You didn\'t enter a name for the itemspace. Please try again.', SmartestUserMessage::WARNING);
        }
        
        $this->formForward();
        
	}
	
	public function editItemspace(){
	    
	    $item_space = new SmartestItemSpace;
	    $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
	    if($item_space->exists($name, $this->getSite()->getId())){
	        
	        // print_r($item_space);
	        $this->send($item_space, 'itemspace');
	        
	        $alh = new SmartestAssetsLibraryHelper;
            $this->send($alh->getAssetsByTypeCode("SM_ASSETTYPE_ITEMSPACE_TEMPLATE", $this->getSite()->getId()), 'templates');
            
            $du = new SmartestDataUtility;
	        $sets = $du->getDataSets(false, $this->getSite()->getId());
	        $this->send($sets, 'sets');
            
	        
	    }else{
	        $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
	    }
	    
	}
	
	public function updateItemspace(){
	    
	    $item_space = new SmartestItemSpace;
	    $id = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_id'));
	    
	    if($item_space->find($id)){
	        
	        if(strlen($this->getRequestParameter('itemspace_label'))){
	            $item_space->setLabel($this->getRequestParameter('itemspace_label'));
	        }
	        
	        if($this->getRequestParameter('itemspace_template_id') == "NONE"){
	            $item_space->setUsesTemplate(false);
	        }else if(is_numeric($this->getRequestParameter('itemspace_template_id'))){
	            $item_space->setUsesTemplate(true);
	            $item_space->setTemplateAssetId($this->getRequestParameter('itemspace_template_id'));
	        }
	        
	        if(is_numeric($this->getRequestParameter('itemspace_dataset_id'))){
	            $item_space->setDataSetId($this->getRequestParameter('itemspace_dataset_id'));
	        }
	        
	        SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} updated itemspace '{$item_space->getName()}'", SmartestLog::USER_ACTION);
	        
	        $item_space->save();
	        $this->addUserMessageToNextRequest('The itemspace was sucessfully updated.', SmartestUserMessage::SUCCESS);
	        $this->formForward();
	        
	    }else{
            $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
        }
	    
	}
	
	public function defineItemspace($get){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->smartFind($page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
    	    $item_space = new SmartestItemSpace;
            
            if($item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if($definition->load($name, $page, true)){
                    $definition_id = $definition->getItemId(true);
                }else{
                    $definition_id = 0;
                }
                
                $options = $item_space->getOptions();
                
                $this->send($definition_id, 'definition_id');
                $this->send($options, 'options');
                $this->send($item_space, 'itemspace');
                $this->send($item_space->getDataSet(), 'set');
                $this->send($item_space->getModel(), 'model');
                $this->send($page, 'page');
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
                $this->formForward();
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
	    
	}
	
	public function clearItemspaceDefinition($get, $post){
	    
	    $page = new SmartestPage;
	    $page_id = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
    	    $item_space = new SmartestItemSpace;
        
            if($exists = $item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if($definition->load($name, $page, true)){
                    // $definition->setItemSpaceId($item_space->getId());
                    // $definition->setPageId($page->getId());
                    $definition->delete();
                    $this->addUserMessageToNextRequest("The itemspace was successfully cleared", SmartestUserMessage::SUCCESS);
                }else{
                    $this->addUserMessageToNextRequest("The itemspace wasn't defined in the first place", SmartestUserMessage::INFO);
                }
                
                SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} cleared the definition of itemspace '{$item_space->getName()}' on page {$page->getTitle()}", SmartestLog::USER_ACTION);
                
                // $definition->setDraftItemId($this->getRequestParameter('item_id'));
                
                $definition->save();
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function updateItemspaceDefinition($get, $post){
	    
	    $page = new SmartestPage;
	    $page_id = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_name'));
	    
    	    $item_space = new SmartestItemSpace;
        
            if($exists = $item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if(!$definition->load($name, $page, true)){
                    $definition->setItemSpaceId($item_space->getId());
                    $definition->setPageId($page->getId());
                }
                
                $definition->setDraftItemId($this->getRequestParameter('item_id'));
                
                SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} updated the definition of itemspace '{$item_space->getName()}' on page {$page->getTitle()}", SmartestLog::USER_ACTION);
                
                $this->addUserMessageToNextRequest("The itemspace ID was successfully updated", SmartestUserMessage::SUCCESS);
                $definition->save();
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function openItem($get){
	    
	    $item = new SmartestItem;
	    
	    if($item->findBy('slug', $this->getRequestParameter('assetclass_id'), $this->getSite()->getId())){
	        
	        $this->redirect('/datamanager/openItem?item_id='.$item->getId());
	        
	    }
	    
	}
	
	public function addPageUrl($get){
	    
	    $page_webid=$this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    
	    if($page->smartFind($page_webid)){
		    
		    $page->setDraftMode(true);
		    
		    $page_type = $page->getType();
		    $is_valid_item = false;
		    
            if($page_type == 'ITEMCLASS' || $page_type == 'SM_PAGETYPE_ITEMCLASS' || $page_type == 'SM_PAGETYPE_DATASET'){
		        
                if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
                    if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
		                
		                if($page->getDatasetId() == $item->getModel()->getId()){
		                    $this->send($item, 'item');
		                    $is_valid_item = true;
	                    }
		                
		            }
		            
		        }
                
                $item_page = true;
		        
		    }else{
		        
                $item_page = false;
                
		    }
            
            // $ishomepage = $this->getRequestParameter('ishomepage');
		    $page_id = $page->getId();
		    $page_info = $page;
		    $site = $page->getSite();
		    $this->send($is_valid_item, 'is_valid_item');
            $this->send($item_page, 'is_item_page');
		    // $page_info['site'] = $page->getSite();
		    
		    $this->send($page_info, "pageInfo");
		    $this->send($site, "site");
		    $this->send($page->isHomePage(), "ishomepage");
		
	    }
	    
		// return array("pageInfo"=>$page_info, "msg"=>$msg, "ishomepage"=>$ishomepage );
	}
	
	public function insertPageUrl($get,$post){
		
		$url = new SmartestPageUrl;
		
		if(!$this->getRequestParameter('page_url')){
		    $this->addUserMessage("You didn't enter a URL.", SmartestUserMessage::WARNING);
		    $this->forward('websitemanager', 'addPageUrl');
		}else if($url->existsOnSite($this->getRequestParameter('page_url'), $this->getSite()->getId())){
		    $this->addUserMessage("That URL already exists for another page.", SmartestUserMessage::WARNING);
		    $this->forward('websitemanager', 'addPageUrl');
		}else{
		    
		    $page = new SmartestPage;
		    
		    if($page->hydrate($this->getRequestParameter('page_id'))){
		        
		        $url = new SmartestPageUrl;
		        $url->setPageId($page->getId());
		        $url->setIsDefault(0);
		        
		        $page_type = $page->getType();

    		    if($page_type == 'ITEMCLASS' || $page_type == 'SM_PAGETYPE_ITEMCLASS' || $page_type == 'SM_PAGETYPE_DATASET'){

    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){

    		            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){

    		                if($page->getDatasetId() == $item->getModel()->getId()){
    		                    
    		                    $url_string = SmartestStringHelper::sanitize($this->getRequestParameter('page_url'));
    		                    
    		                    if($this->getRequestParameter('page_url_type') == 'SINGLE_ITEM'){
    		                        $url_string = str_replace(':name', $item->getSlug(), $url_string);
    		                        $url_string = str_replace(':id', $item->getId(), $url_string);
    		                        $url_string = str_replace(':webid', $item->getWebid(), $url_string);
    		                        $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_ITEM_FORWARD' : 'SM_PAGEURL_SINGLE_ITEM');
		                        }else{
		                            $url_string = str_replace($item->getSlug(), ':name', $url_string);
    		                        $url_string = str_replace($item->getId(), ':id', $url_string);
    		                        $url_string = str_replace($item->getWebid(), ':webid', $url_string);
		                            $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_INTERNAL_FORWARD' : 'SM_PAGEURL_NORMAL');
		                        }
		                        
		                        if($this->getRequestParameter('forward_to_default') && $this->getRequestParameter('forward_to_default') == '1'){
		                            $url->setRedirectType($this->getRequestParameter('url_redirect_type'));
		                        }
		                        
		                        $url->setItemId($item->getId());
		                        $url->setUrl($url_string);
                                $url->save();
    		                    
    	                    }

    		            }

    		        }

    		    }else{
		            
		            $url->setUrl(SmartestStringHelper::sanitize($this->getRequestParameter('page_url')));
		            $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_INTERNAL_FORWARD' : 'SM_PAGEURL_NORMAL');
                    
                    if($this->getRequestParameter('forward_to_default') && $this->getRequestParameter('forward_to_default') == '1'){
                        $url->setRedirectType($this->getRequestParameter('url_redirect_type'));
                    }
                    
                    $url->save();
		        
	            }
	            
	            SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} added the URL '{$url->getUrl()}' for page '{$page->getTitle()}'.", SmartestLog::USER_ACTION);
	            
		        SmartestLog::getInstance('site')->log("{$this->getUser()} added URL '{$this->getRequestParameter('page_url')}' to page: {$page->getTitle()}.", SmartestLog::USER_ACTION);
		        $this->addUserMessageToNextRequest("The new URL was successully added.", SmartestUserMessage::SUCCESS);
		        
		    }else{
		        $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
		    }
		    
		}
		
		$this->formForward();
		
		/* $page_webid=$this->getRequestParameter('page_webid');
		$page_id = $this->manager->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		$page_url=$this->getRequestParameter('page_url');
		$url_count = $this->manager->checkUrl($page_url);
		
		if($url_count > 0){
			header("Location:".$this->domain.$this->module."/addPageUrl?page_id=$page_webid&msg=1");
		}else{
			$this->manager->insertNewUrl($page_id,$page_url);
			$this->formForward();
		} */
	}
	
	public function editPageUrl($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		
		$page = new SmartestPage;
		$url = new SmartestPageUrl;
		
		if($url->find($this->getRequestParameter('url_id'))){
		    
		    $this->send($url, "url");
		
    		if($page->find($url->getPageId())){
    		    
    		    if($page->getType() == "ITEMCLASS"){
    		        $model = new SmartestModel;
    		        $model->find($page->getDatasetId());
    		        $this->send($model, "model");
    		    }
    		    
    		    $page->setDraftMode(true);
    		    $site = $page->getSite();
    		    $this->send($site, 'site');
    		    $this->send($page->isHomepage(), "ishomepage");
    		    $this->send($page, "pageInfo");
    		    
    		    $b = (($this->getRequestParameter('responseTableLinks') && !SmartestStringHelper::toRealBool($this->getRequestParameter('responseTableLinks'))) ? false : true);
    		    
                // $this->send(new SmartestBoolean($b), 'responseTableLinks');
    		    
    	    }
	    }
	}
	
	public function updatePageUrl($get,$post){
		
		$page_webid = $this->getRequestParameter('page_webid');
		$page_url = $this->getRequestParameter('page_url');
		$url_id = $this->getRequestParameter('url_id');
		
		$url = new SmartestPageUrl;
		$url->find($url_id);
		
		if($this->getRequestParameter('forward_to_default') && $this->getRequestParameter('forward_to_default') == 1){
		    
		    if(in_array($url->getType(), array('SM_PAGEURL_ITEM_FORWARD', 'SM_PAGEURL_SINGLE_ITEM'))){
		        
		        if($url->getIsDefault()){
		            $url->setType('SM_PAGEURL_SINGLE_ITEM');
		            $this->addUserMessageToNextRequest("The default URL cannot also be an internal forward");
		        }else{
		            $url->setType('SM_PAGEURL_ITEM_FORWARD');
		        }
		        
		    }else{
		    
		        if($url->getIsDefault()){
		            $url->setType('SM_PAGEURL_NORMAL');
		            $this->addUserMessageToNextRequest("The default URL cannot also be an internal forward");
		        }else{
		            $url->setType('SM_PAGEURL_INTERNAL_FORWARD');
		        }
		    
	        }
	        
	        $url->setRedirectType($this->getRequestParameter('url_redirect_type'));
		    
		}else{
		    if(in_array($url->getType(), array('SM_PAGEURL_ITEM_FORWARD', 'SM_PAGEURL_SINGLE_ITEM'))){
		        $url->setType('SM_PAGEURL_SINGLE_ITEM');
	        }else{
	            $url->setType('SM_PAGEURL_NORMAL');
	        }
		}
		
		$url->setUrl($page_url);
		$url->save();
		
		// $pageurl_id = $this->manager->database->specificQuery("pageurl_id", "pageurl_url", $page_oldurl, "PageUrls");
		// $pageurl_id;
		// $page_id = $this->manager->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		// $this->manager->updatePageUrl($page_id,$pageurl_id,$page_url);
		
		$this->formForward();
	}
	
	public function transferPageUrl(){
        
        $page = new SmartestPage;
    	$url = new SmartestPageUrl;
    	
    	if($url->find($this->getRequestParameter('url_id'))){
    	    
    	    $this->send($url, "url");
    	    
    	    if($page->find($url->getPageId())){
     		    
     		    $page->setDraftMode(true);
     		    $site = $page->getSite();
     		    
     		    if($page->getType() == "ITEMCLASS"){
     		        $model = new SmartestModel;
     		        $model->find($page->getDatasetId());
     		        $this->send($model, "model");
     		    }else{
     		        $pages = $site->getPagesList(true);
                    $this->send($pages, 'pages');
     		    }
     		    
     		    $this->send($site, 'site');
     		    $this->send($page->isHomepage(), "ishomepage");
     		    $this->send($page, "pageInfo");
     		    
     		    $b = (($this->getRequestParameter('responseTableLinks') && !SmartestStringHelper::toRealBool($this->getRequestParameter('responseTableLinks'))) ? false : true);
     		    
     	    }
    	    
    	}
        
    }
    
    public function transferPageUrlAction(){
        
        $page_webid = $this->getRequestParameter('page_webid');
    	$page_url = $this->getRequestParameter('page_url');
    	$url_id = $this->getRequestParameter('url_id');
    	
    	$url = new SmartestPageUrl;
    	$url->find($url_id);
    	
    	$url->setIsDefault(0);
    	$url->setType(is_numeric($this->getRequestParameter('url_redirect_type')) ? 'SM_PAGEURL_INTERNAL_FORWARD' : 'SM_PAGEURL_NORMAL');
    	$url->setRedirectType(is_numeric($this->getRequestParameter('url_redirect_type')) ? $this->getRequestParameter('url_redirect_type') : '');
    	$url->setPageId($this->getRequestParameter('url_page_id'));
    	
    	$url->save();
        
    }
	
	public function deletePageUrl($get){
		
		$url = new SmartestPageUrl;
		$p = new SmartestPage;
		
		if($url->hydrate($this->getRequestParameter('url'))){
		    
		    $p->hydrate($url->getPageId());
		    
		    $u = $url->getUrl();
		    $url->delete();
		    SmartestLog::getInstance('site')->log("{$this->getUser()} deleted URL '$u' from page: {$p->getTitle()}.", SmartestLog::USER_ACTION);
		    $this->addUserMessageToNextRequest("The URL has been successfully deleted. It's recommended that you now clear the pages cache to avoid dead links.", SmartestUserMessage::SUCCESS);
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("The URL ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
		$this->formForward();
	}
	
	public function setPageDefaultUrl($get){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	        
	        $page->setDraftMode(true);
	        
	        $result = $page->setDefaultUrl($this->getRequestParameter('url'));
	        
	        if(!$result){
	            if($url == (int) $url){
	                $this->addUserMessageToNextRequest("The URL ID was not recognized.", SmartestUserMessage::ERROR);
                }else{
                    $this->addUserMessageToNextRequest("The URL is already in use for another page.", SmartestUserMessage::ERROR);
                }
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function editField($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/defineFieldOnPage?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function setLiveProperty($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/setLiveProperty?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function undefinePageProperty($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/undefinePageProperty?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function pageGroups(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('page groups');
	    
	    $pgh = new SmartestPageGroupsHelper;
	    $groups = $pgh->getSiteGroups($this->getSite()->getId());
	    $this->send($groups, 'groups');
	    $this->setTitle('Page groups');
	    
	}
	
	public function addPageGroup(){
	    
	    $this->send($this->getSite()->getNormalPagesList(true, true), 'pages');
	    
	}
	
	public function insertPageGroup(){
	    
	    $label = $this->getRequestParameter('pagegroup_label');
	    
	    if(strlen($label)){
	    
    	    $name = SmartestStringHelper::toVarName($label);
    
    	    $pg = new SmartestPageGroup;
    
    	    if(!$pg->findBy('name', $name, $this->getSite()->getId()) && !$pg->findBy('label', $name, $this->getSite()->getId())){
	        
    	        $pg->setName($name);
    	        $pg->setLabel($label);
    	        $pg->setSiteId($this->getSite()->getId());
    	        $pg->save();
    	        
    	        $this->addUserMessageToNextRequest('Your new page group menu was saved successfully.', SmartestUserMessage::SUCCESS);
	        
    	        if($this->getRequestParameter('continue_to_pages')){
    	            $this->redirect('/websitemanager/editPageGroup?group_id='.$pg->getId());
                }else{
                    $this->redirect('/smartest/pagegroups');
                }
            
    	    }else{
    	        $this->addUserMessage('A page group with that name already exists.', SmartestUserMessage::INFO);
    	        $this->forward('websitemanager', 'addPageGroup');
    	    }
	    
        }else{

            $this->addUserMessage('You must enter a valid label for your page group.', SmartestUserMessage::ERROR);
            $this->forward('websitemanager','addPageGroup');

        }
	    
	}
	
	public function editPageGroup(){
	    
	    $group = new SmartestPageGroup;
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('editing page grouop');
	    
	    if($group->find($this->getRequestParameter('group_id'))){
	        // $group->fixOrderIndices();
	        $this->send($group, 'group');
	        $this->send($group->getNonMembers(true), 'non_members');
	        $this->send($group->getMemberships(true, true), 'members');
	        $this->send($this->getUser()->hasToken('edit_pagegroup_name'), 'allow_name_edit');
	    }
	    
	}
	
	public function editPageGroupOrder(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('group_id'))){
	        // $group->fixOrderIndices();
	        $this->send($group, 'group');
	        $this->send($group->getMemberships(true, true), 'members');
	    }
	    
	}
	
	public function transferPages(){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($group_id)){
	        
	        if($this->getRequestParameter('transferAction') == 'add'){
	            
	            $page_ids = ($this->getRequestParameter('available_pages') && is_array($this->getRequestParameter('available_pages'))) ? $this->getRequestParameter('available_pages') : array();
	            
	            foreach($page_ids as $aid){
	                $group->addPageById($aid);
	            }
	            
	        }else{
	            
	            $page_ids = ($this->getRequestParameter('used_pages') && is_array($this->getRequestParameter('used_pages'))) ? $this->getRequestParameter('used_pages') : array();
	            
	            foreach($page_ids as $aid){
	                $group->removePageById($aid);
	            }
	            
	            $group->fixOrderIndices();
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
    
    public function pageDownloads(){
        
		$page_webid = $this->getRequestParameter('page_id');
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
		if(isset($type_index[$page_webid])){
		    if(($type_index[$page_webid] == 'ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_DATASET') && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->smartFind($page_webid)){
		    
            $this->send($page, 'page');
            $editable = $page->isEditableByUserId($this->getUser()->getId());
    		$this->send($editable, 'page_is_editable');
            
            $file_ids = array();
            
            $downloads = $page->getPageDownloads();
            $this->send($downloads, 'downloads');
            
            if(count($downloads)){
                foreach($downloads as $d){
                    $file_ids[] = $d->getAssetId();
                }
                $this->send(implode(',', $file_ids), 'connected_file_ids');
            }else{
                $this->send('', 'connected_file_ids');
            }
            
            // echo count($downloads);
            
            // $this->redirect('@websitemanager:basic_info?page_id='.$page->getWebid());
            
		}
        
    }
    
    public function addPageDownload(){
        
        
        
    }
    
    public function indexPages(){
        
        $num_pages = count($this->getSite()->getQuickPagesList());
        $this->send($num_pages, 'num_cms_pages');
        
    }

}