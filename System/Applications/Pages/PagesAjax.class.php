<?php

class PagesAjax extends SmartestSystemApplication{

    public function tagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	        }else{
	            header('HTTP/1.1 500 Internal Server Error');
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function unTagPage(){
	    
	    $page = new SmartestPage;
	    
	    if($page->find($this->getRequestParameter('page_id'))){
	        
	        if($page->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	        }else{
	            header('HTTP/1.1 500 Internal Server Error');
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function setPageGroupLabelFromInPlaceEditField(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('pagegroup_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $group->setLabel($this->getRequestParameter('new_label'));
	        $group->save();
	        echo $this->getRequestParameter('new_label');
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function setPageGroupNameFromInPlaceEditField(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('pagegroup_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $group->setName(SmartestStringHelper::toVarName($this->getRequestParameter('new_name')));
	        $group->save();
	        echo SmartestStringHelper::toVarName($this->getRequestParameter('new_name'));
	        exit();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function updatePageGroupOrder(){
	    
	    $group = new SmartestPageGroup;

            if($group->find($this->getRequestParameter('group_id'))){
                header('HTTP/1.1 200 OK');
                if($this->getRequestParameter('page_ids')){
                    $group->setNewOrderFromString($this->getRequestParameter('page_ids'));
                }
            }

            exit;
	    
	}
	
	public function pageUrls(){
	    
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
		
		$this->send(($this->getRequestParameter('responseTableLinks') && !SmartestStringHelper::toRealBool($this->getRequestParameter('responseTableLinks')) ? false : true), 'link_urls');
		
		if($page->hydrate($page_webid)){
		    
		    if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
	                $page->setPrincipalItem($item);
	                $this->send($item, 'item');
	            }
            }
		    
		    $ishomepage = ($this->getSite()->getTopPageId() == $page->getId());
		    $this->send($ishomepage, "ishomepage");
		    $this->send($this->getSite(), 'site');
		    $this->send($page, 'page');
		    
		}
	    
	}
	
	public function setPageValueFromAjaxForm(){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->findby('webid', $page_webid)){
	        
	        switch($this->getRequestParameter('name')){

                case "title":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setTitle($this->getRequestParameter('value'));
                    $page->save();
                    SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
                    echo $this->getRequestParameter('value');
                }else{
                    echo $page->getTitle();
                }
                exit;
                
                case "name":
                if(strlen($this->getRequestParameter('value')) && $this->getUser()->hasToken('edit_page_name')){
                    $v = SmartestStringHelper::toSlug($this->getRequestParameter('value'));
                    $page->setName($v);
                    $page->save();
                    SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
                    echo $v;
                }else{
                    echo $page->getName();
                }
                exit;
                
                case "parent":
                if(strlen($this->getRequestParameter('value')) && is_numeric($this->getRequestParameter('value'))){
                    $page->setParent($this->getRequestParameter('value'));
                    $page->save();
                    SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
                }
                exit;
                
                case "cache_frequency":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setCacheInterval($this->getRequestParameter('value'));
                    $page->save();
                }
                exit;
                
                case "force_static_title":
                if(strlen($this->getRequestParameter('value'))){
                    $page->setForceStaticTitle($this->getRequestParameter('value') ? 1 : 0);
                    $page->save();
                }
                exit;

    	    }
	        
	    }
	    
	}
	
	public function loadAssetGroupDropdownForNewPlaceholderForm(){
	    
	    $h = new SmartestAssetClassesHelper;
	    $type_code = $this->getRequestParameter('placeholder_type');
		
		if(in_array($type_code, $h->getTypeCodes())){
		    
		    $groups = $h->getAssetGroupsForPlaceholderType($type_code, $this->getSite()->getId());
		    $this->send($groups, 'groups');
		    $this->send($type_code, 'selected_type');
		    
		}
	    
	}
	
	public function clearPagesCache(){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if($this->getUser()->hasToken('clear_pages_cache')){
            
                $page_prefix = 'site'.$this->getSite()->getId().'_';
            
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
                
                }
            
            }
            
        }
	    
	}
    
    public function addPageDownload(){
        
        $page = new SmartestPage;
        
        if($page->smartFind($this->getRequestParameter('page_id'))){
            
            $asset = new SmartestAsset;
            
            if($asset->find($this->getRequestParameter('asset_id'))){
                
                $dl = $page->addDownloadById($asset->getId(), strip_tags($this->getRequestParameter('download_label')));
                header('Content-Type: application/json; charset=UTF8');
                echo $dl->__toJson();
                exit;
                
            }
            
        }
        
    }
    
    public function removePageDownload(){
        
        $page = new SmartestPage;
        
        if($page->smartFind($this->getRequestParameter('page_id'))){
            
            $asset = new SmartestAsset;
            
            if($asset->find($this->getRequestParameter('asset_id'))){
                
                $page->removeDownloadById($asset->getId());
                header('Content-Type: application/json; charset=UTF8');
                echo json_encode(array('success'=>true));
                exit;
                
            }else{
                
                header('Content-Type: application/json; charset=UTF8');
                echo json_encode(array('success'=>false, 'reason'=>'asset'));
                exit;
                
            }
            
        }else{
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode(array('success'=>false, 'reason'=>'page'));
            exit;
            
        }
        
    }
    
    public function restoreTrashedPage(){
        
        $page = new SmartestPage;
        
        if($page->smartFind($this->getRequestParameter('page_id'))){
            
            $page->setDeleted('FALSE');
            
            if(is_object($page->getParentPage()) && SmartestStringHelper::toRealBool($page->getParentPage()->getDeleted())){
                $page->setParent($this->getSite()->getTopPageId());
            }
            
            $page->save();
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode(array('success'=>true));
            exit;
            
        }else{
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode(array('success'=>false, 'reason'=>'page'));
            exit;
        }
        
    }
    
    public function checkPresetDefinitionForContainerId(){
        
        $preset_id = (int) $this->getRequestParameter('preset_id');
        $container_id = (int) $this->getRequestParameter('container_id');
        $preset = new SmartestPagePreset;
        $result = new stdClass;
        
        if($preset->find($preset_id)){
            $result->success = true;
            $result->preset_id = $preset_id;
            $result->container_id = $container_id;
            if($preset->hasDefinitionForAssetClassId($container_id)){
                $result->has_definition = true;
            }else{
                $result->has_definition = false;
            }
        }else{
            $result->success = false;
            $result->reason = 'Preset not found';
        }
        
        header('Content-Type: application/json; charset=UTF8');
        echo json_encode($result);
        exit;
        
    }
    
    public function checkPresetDefinitionForTextAssetId(){
        
        
        $preset_id = (int) $this->getRequestParameter('preset_id');
        $placeholder_id = (int) $this->getRequestParameter('text_placeholder_id');
        $preset = new SmartestPagePreset;
        $result = new stdClass;
        
        if($preset->find($preset_id)){
            $result->success = true;
            $result->preset_id = $preset_id;
            $result->placeholder_id = $placeholder_id;
            if($preset->hasDefinitionForAssetClassId($placeholder_id)){
                $result->has_definition = true;
            }else{
                $result->has_definition = false;
            }
        }else{
            $result->success = false;
            $result->reason = 'Preset not found';
        }
        
        header('Content-Type: application/json; charset=UTF8');
        echo json_encode($result);
        exit;
        
    }
    
    public function getListTemplatesByModel(){
        
        $th = new SmartestTemplatesLibraryHelper;
        $templates = $th->getSimpleListTemplatesByModel((int) $this->getRequestParameter('model_id'), $this->getSite()->getId());
        $this->send($templates, 'templates');
        $this->send(is_writable(SM_ROOT_DIR.'Presentation/Layouts/'), 'can_create_template');
        
    }
    
    public function bulkIndexPages(){
        
        $page_size = 18;
        $current_page_num = $this->getRequestParameter('page_num', 1);
        $data = array();
        $data['page_size'] = $page_size;
        $data['current_page_num'] = $current_page_num;
        
        if(!defined('SM_CMS_PAGE_SITE_ID')){
            define('SM_CMS_PAGE_SITE_ID', $this->getSite()->getId());
        }
        
        // if($model->find($model_id)){
            
            // $item_ids = $model->getItemIds($this->getSite()->getId(), SM_STATUS_LIVE);
            $cms_pages = $this->getSite()->getQuickPagesList();
            
            $num_cms_pages = count($cms_pages);
            $num_pages = ceil($num_cms_pages/$page_size);
            $data['num_cms_pages'] = $num_cms_pages;
            $data['num_pages'] = $num_pages;
            
            $this_operation_starting_index = ($current_page_num-1)*$page_size;
            $this_operation_starting_id = $cms_pages[$this_operation_starting_index]->getId();
            $data['this_operation_starting_index'] = $this_operation_starting_index;
            $data['this_operation_starting_id'] = $this_operation_starting_id;
            
            if($current_page_num < $data['num_pages']){
                $data['next_page_num'] = $current_page_num+1;
                $num_completed = $current_page_num*$page_size;
            }else{
                $num_completed = $num_cms_pages;
            }
            
            $pc_completed = number_format($num_completed/$num_cms_pages*100, 2);
            $data['percent_completed'] = $pc_completed;
            $data['num_completed'] = $num_completed;
            
            // $set = $model->getAllItemsAsSortableReferenceSet($this->getSite()->getId(), SM_STATUS_LIVE);
            // $items = $set->getPage($current_page_num, $page_size);
            
            try{
            
                for($i=$this_operation_starting_index;$i<$num_completed;$i++){
                    
                    $page = $cms_pages[$i];
                    $cms_page_data = $page->getElasticSearchAssociativeArray();
                    
                    $params['body'][]['index'] = array(
                        '_index' => $this->getSite()->getElasticSearchIndexName(),
                        '_id' => $page->getId(),
                        '_type' => 'smartest_page'
                    );
                          
                    $params['body'][] = $cms_page_data;
                    
                }
                
                $response = SmartestElasticSearchHelper::addBulkItemsToIndex($params);
                
                $data['response'] = $response;
                
                header("Content-Type: application/json");
                echo json_encode($data);
                exit;
            
            }catch(Elasticsearch\Common\Exceptions\ServerErrorResponseException $e){
                // print_r($e->getTrace());
            }
            
        // }
        
    }

}