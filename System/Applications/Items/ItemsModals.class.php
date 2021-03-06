<?php

class ItemsModals extends SmartestSystemApplication{
    
    public function chooseItems(){
        
        $item = new SmartestCmsItem;
        
        if($item->find($this->getRequestParameter('item_id'))){
            
            $item->setDraftMode(true);
            $this->send($item, 'item');
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                
                $this->send($property, 'property');
                
                if($property->getItemClassId() == $item->getItem()->getModelId()){
                    
                    $this->send($property->getPossibleValues(), 'options');
                    $item->getPropertyValueByNumericKey($property->getId());
                    $this->send($item->getPropertyValueByNumericKey($property->getId())->getIds(), 'selected_ids');
                    
                    // $ruri = '/smartest/item/edit/'.$item->getId();
        		    // 
        		    // if($this->getRequestParameter('from')){
        		    //     $ruri .= '&from='.$this->getRequestParameter('from');
        		    // }
        		    // 
        		    // if($this->getRequestParameter('item_id')){
        		    //     $ruri .= '&item_id='.$this->getRequestParameter('item_id');
        		    // }
        		    // 
        		    // if($this->getRequestParameter('page_id')){
        		    //     $ruri .= '&page_id='.$this->getRequestParameter('page_id');
        		    // }
        		    // 
        		    // if($this->getRequestParameter('author_id')){
        		    //     $ruri .= '&author_id='.$this->getRequestParameter('author_id');
        		    // }
        		    // 
        		    // if($this->getRequestParameter('search_query')){
        		    //     $ruri .= '&search_query='.$this->getRequestParameter('search_query');
        		    // }
        		    // 
        		    // if($this->getRequestParameter('tag')){
        		    //     $ruri .= '&tag='.$this->getRequestParameter('tag');
        		    // }
                    // 
        		    // $this->setTemporaryFormReturnUri($ruri);
                    
                }else{
                    // $this->addUserMessageToNextRequest("Item and property are from different models", SmartestUserMessage::ERROR);
                    // $this->formForward();
                }
                
            }else{
                
                // $this->addUserMessageToNextRequest("The property ID was not recognized", SmartestUserMessage::ERROR);
                // $this->formForward();
                
            }
            
        }else{
            
            // $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
            // $this->formForward();
            
        }
        
    }
    
    public function chooseFiles(){
        
        $item = new SmartestCmsItem;
        
        if($item->find($this->getRequestParameter('item_id'))){
            
            $item->setDraftMode(true);
            $this->send($item, 'item');
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                
                $this->send($property, 'property');
                
                if($property->getItemClassId() == $item->getItem()->getModelId()){
                    
                    $options = $property->getPossibleValues();
                    $this->send($options, 'options');
                    $item->getPropertyValueByNumericKey($property->getId());
                    $ids = $item->getPropertyValueByNumericKey($property->getId())->getIds();
                    $this->send($ids, 'selected_ids');
                    
                }else{
                    $this->addUserMessageToNextRequest("Item and property are from different models", SmartestUserMessage::ERROR);
                    $this->formForward();
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The property ID was not recognized", SmartestUserMessage::ERROR);
                $this->formForward();
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
        
    }
    
    public function addNewItemToItemSelection(){
        
        $this->send(date("Y"), 'default_year');
        $this->send(date("m"), 'default_month');
        $this->send(date("d"), 'default_day');
        
        if($this->getUser()->hasToken('add_items')){
            
            if($this->getRequestParameter('class_id')){
            
                $model_id = $this->getRequestParameter('class_id');
            
            }else if($this->getRequestParameter('for') == 'ipv' && $this->getRequestParameter('property_id')){
                
                $p = new SmartestItemProperty;
                
                if($p->find($this->getRequestParameter('property_id'))){
                    if($p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                        
                        $model_id = $p->getForeignKeyFilter();
                        $this->send($p, 'parent_property');
                        
                        if($parent_item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                            $this->send($parent_item, 'parent_item');
                        }
                        
                    }else{
                        
                    }
                }else{
                    
                }
                
            }else{
                
                // echo "did not find model";
                
        		$du = new SmartestDataUtility;
        		$models = $du->getModels(false, $this->getSite()->getId(), true);
                $this->send($models, 'models');
                $this->send(true, 'require_choose_model');
                
                return;
                
            }
            
            $this->send(false, 'require_choose_model');
            
            $model = new SmartestModel;
            // echo $this->getRequestParameter('use_plural_name');
            
            if($this->getRequestParameter('use_plural_name')){
                $found_model = $model->findBy('varname', $this->getRequestParameter('plural_name'));
            }else{
                $found_model = $model->find($model_id);
            }
            
            if($found_model){
                
                if($model->hasPrimaryProperty() && $model->getPrimaryProperty()->getDatatype() == 'SM_DATATYPE_ASSET'){
                    $this->redirect('/smartest/file/new?for=ipv&property_id='.$model->getPrimaryPropertyId());
                }
                
                if($model->getType() == 'SM_ITEMCLASS_MT1_SUB_MODEL'){
                    
                    $parent_model = $model->getParentModel();
                    
                    if($this->requestParameterIsSet('parent_item_id') && is_numeric($this->getRequestParameter('parent_item_id'))){
                        if($parent_item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('parent_item_id'))){
                            $this->send($parent_item, 'parent_item');
                        }else{
                            throw new SmartestException('Parent item \''.$this->getRequestParameter('parent_item_id').'\' could not be found');
                        }
                    }else{
                        // No parent item is set os the screen should generate a dropdown menu
                        $this->send($parent_model->getAllItems($this->getSite()->getId(), 0), 'possible_parent_items');
                    }
                    
                    $this->send($parent_model, 'parent_model');
                
                }elseif($model->getType() == 'SM_ITEMCLASS_MTM_SUB_MODEL'){
                    
                    $parent_model = $model->getParentModel();
                    $this->send($parent_model, 'parent_model');
                    
                }
                
                $start_name = 'Unnamed '.$model->getName();
                $this->send($start_name, 'start_name');
                $this->send($this->getUser()->hasToken('create_assets'), 'can_create_assets');
                $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
                $this->send($model->getProperties(), 'properties');
                $this->send($model, 'model');
                $this->send($this->getSite()->getLanguageCode(), 'site_language');
                $this->setTitle('Add '.$model->getName());
                $this->send(new SmartestArray(array_values($model->getAutomaticSetsForNewItem($this->getSite()->getId()))), 'automatic_sets');
            
            }else{
                
                $this->addUserMessageToNextRequest('The model id was not recognised.', SmartestUserMessage::ERROR);
                
            }
        
        }
        
    }
    
    public function updateItemsSelection(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                        
                            if(is_array($this->getRequestParameter('items'))){
                                $ids = array_keys($this->getRequestParameter('items'));
                            }else{
                                $ids = array();
                            }
                        
                            $item->setPropertyValueByNumericKey($property->getId(), $ids);
                            $item->save();
                            $this->addUserMessageToNextRequest("The attached items for this property were successfully updated.", SmartestUserMessage::SUCCESS);
                            $this->redirect('/smartest/item/edit/'.$item->getId());
                        
                        }else{
                            
                            $this->addUserMessageToNextRequest("The property ID was not recognized.", SmartestUserMessage::ERROR);
                            
                        }
                        
                    }else{
                        
                        $this->addUserMessageToNextRequest("The model '".$item->getModel()->getName()."' does not have a property with that ID.", SmartestUserMessage::ERROR);
                        
                    }
                
                }else{
                    
                    $this->addUserMessageToNextRequest("The property ID was in an invalid format.", SmartestUserMessage::ERROR);
                    
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was in an invalid format.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
        
    }
    
    /* public function chooseFiles(){
        
        $item = new SmartestCmsItem;
        
        if($item->find($this->getRequestParameter('item_id'))){
            
            $item->setDraftMode(true);
            $this->send($item, 'item');
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                
                $this->send($property, 'property');
                
                if($property->getItemClassId() == $item->getItem()->getModelId()){
                    $options = $property->getPossibleValues();
                    $this->send($options, 'options');
                    $item->getPropertyValueByNumericKey($property->getId());
                    $ids = $item->getPropertyValueByNumericKey($property->getId())->getIds();
                    $this->send($ids, 'selected_ids');
                }else{
                    $this->addUserMessageToNextRequest("Item and property are from different models", SmartestUserMessage::ERROR);
                    $this->formForward();
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The property ID was not recognized", SmartestUserMessage::ERROR);
                $this->formForward();
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
        
    } */
    
    public function updateFilesSelection(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                        
                            if(is_array($this->getRequestParameter('items'))){
                                $ids = array_keys($this->getRequestParameter('items'));
                            }else{
                                $ids = array();
                            }
                            
                            $item->setPropertyValueByNumericKey($property->getId(), $ids);
                            $item->save();
                            $this->addUserMessageToNextRequest("The attached files for this property were successfully updated.", SmartestUserMessage::SUCCESS);
                            $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                        
                        }else{
                            
                            $this->addUserMessageToNextRequest("The property ID was not recognized.", SmartestUserMessage::ERROR);
                            
                        }
                        
                    }else{
                        
                        $this->addUserMessageToNextRequest("The model '".$item->getModel()->getName()."' does not have a property with that ID.", SmartestUserMessage::ERROR);
                        
                    }
                
                }else{
                    
                    $this->addUserMessageToNextRequest("The property ID was in an invalid format.", SmartestUserMessage::ERROR);
                    
                }
                
                $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was in an invalid format.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
        
    }
    
    public function editAssetData($get){
	    
        // get item id and property id
	    // load item property, draft info
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $property_id = (int) $this->getRequestParameter('property_id');
	    
	    $item = new SmartestItem;
	    
	    if($item->find($item_id)){
	        
	        $property = new SmartestItemPropertyValueHolder;
	        
	        if($property->find($property_id)){
	            
	            $property->setContextualItemId($item_id);
	            $asset = $property->getData()->getDraftContent();
	            
	            $existing_render_data = $property->getData()->getInfo(true);
	            
	            // print_r($asset);
	            
	            if(is_object($asset)){
	                
	                $this->send($property, 'property');
	                $this->send($item, 'item');
	                $this->send($item->getModel(), 'model');
	                
	                $type = $asset->getTypeInfo();
	                $this->send($type, 'asset_type');
	                $this->send($asset->__toArray(), 'asset');
	                
	                if(isset($type['param'])){

            	        $raw_xml_params = $type['param'];
                        $params = array();
            	        foreach($raw_xml_params as $rxp){
            	            
            	            if(isset($rxp['default'])){
            	                $params[$rxp['name']]['xml_default'] = $rxp['default'];
            	                $params[$rxp['name']]['value'] = $rxp['default'];
                            }else{
                                $params[$rxp['name']]['xml_default'] = '';
                                $params[$rxp['name']]['value'] = '';
                            }
                            
                            $params[$rxp['name']]['type'] = $rxp['type'];
                            $params[$rxp['name']]['asset_default'] = '';
            	        }
            	        
            	        $this->send($type, 'asset_type');

            	    }else{
            	        $params = array();
            	    }
            	    
            	    $asset_params = $asset->getDefaultParameterValues();
            	    
            	    foreach($params as $key=>$p){
            	        // default values from xml are set above.
            	        
            	        // next, set values from asset
            	        if($asset_params->hasParameter($key) && strlen($asset_params->getParameter($key))){
            	            $params[$key]['value'] = $asset_params->getParameter($key);
            	            $params[$key]['asset_default'] = $asset_params->getParameter($key);
            	        }
            	        
            	        // then, override any values that already exist
            	        if(isset($existing_render_data[$key]) && strlen($existing_render_data[$key])){
            	            $params[$key]['value'] = $existing_render_data[$key];
            	        }
        	        }
        	        
        	        $this->send($params, 'params');
	                
	            }
	            
	        }else{
	            echo "Property could not be found";
	        }
	        
	    }else{
	        echo "Item could not be found";
	    }
	    
	    
	}
    
    public function showItemClassTemplateAccess(){
        
	    $model = new SmartestModel;
	    
	    if($model->find($this->getRequestParameter('class_id'))){
	        
            $this->send($model, 'model');
            $this->send($model->getProperties(), 'properties');
            
	    }
        
    }
	
	/* public function updateAssetData($get, $post){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $property_id = (int) $this->getRequestParameter('property_id');
	    $values = is_array($this->getRequestParameter('params')) ? $this->getRequestParameter('params') : array();
	    $new_values = array();
	    
	    $item = new SmartestItem;
	    
	    if($item->find($item_id)){
	        
	        $property = new SmartestItemPropertyValueHolder;
	        
	        if($property->find($property_id)){
	            
	            $property->setContextualItemId($item_id);
	            $value_object = $property->getData();
	            $asset = $value_object->getDraftContent();
	            
	            $existing_render_data = $value_object->getInfo(true);
	            
	            // $asset = new SmartestAsset;
	            
	            if(is_object($asset)){
	                
	                $type = $asset->getTypeInfo();
	                
	                if(isset($type['param'])){

            	        $raw_xml_params = $type['param'];
                        $params = array();
            	        
            	        foreach($raw_xml_params as $rxp){
            	            
            	            if(isset($rxp['default'])){
            	                $params[$rxp['name']]['xml_default'] = $rxp['default'];
            	                $params[$rxp['name']]['value'] = $rxp['default'];
                            }else{
                                $params[$rxp['name']]['xml_default'] = '';
                                $params[$rxp['name']]['value'] = '';
                            }
                            
                            $params[$rxp['name']]['type'] = $rxp['type'];
                            $params[$rxp['name']]['asset_default'] = '';
            	        }

            	    }else{
            	        $params = array();
            	    }
            	    
            	    $asset_params = $asset->getDefaultParameterValues();
            	    
            	    foreach($params as $key=>$p){
            	        // default values from xml are set above.
            	        
            	        // next, set values from asset
            	        if(isset($asset_params[$key]) && strlen($asset_params[$key])){
            	            $v = $asset_params[$key];
            	        }
            	        
            	        // then, override any values that already exist
            	        if(isset($existing_render_data[$key]) && strlen($existing_render_data[$key])){
            	            $v = $existing_render_data[$key];
            	        }
            	        
            	        if(isset($values[$key])){
            	            $v = $values[$key];
            	        }
            	        
            	        $value_object->setInfoField($key, $v);
            	        
        	        }
        	        
        	        $value_object->save();
        	        
	                $this->addUserMessageToNextRequest("The display parameters were updated", SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                $this->addUserMessageToNextRequest("No asset is currently selected for this property", SmartestUserMessage::ERROR);
	                
	            }
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest("The property ID wasn't recognized", SmartestUserMessage::ERROR);
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The item ID wasn't recognized", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	} */
	
	public function previewFeedItemPropertyValue(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                $item->setDraftMode(true);
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                            
                            $feed_ipv_object = $item->getPropertyValueByNumericKey($property->getId());
                            $feed_ipv_object->getItems();
                            
                            $this->send($feed_ipv_object, 'feed');
                            
                            /* if(is_array($this->getRequestParameter('items'))){
                                $ids = array_keys($this->getRequestParameter('items'));
                            }else{
                                $ids = array();
                            }
                            
                            $item->setPropertyValueByNumericKey($property->getId(), $ids);
                            $item->save();
                            $this->addUserMessageToNextRequest("The attached files for this property were successfully updated.", SmartestUserMessage::SUCCESS);
                            $this->redirect('/datamanager/editItem?item_id='.$item->getId()); */
                        
                        }else{
                            
                            $this->addUserMessageToNextRequest("The property ID was not recognized.", SmartestUserMessage::ERROR);
                            $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                        }
                        
                    }else{
                        
                        $this->addUserMessageToNextRequest("The model '".$item->getModel()->getName()."' does not have a property with that ID.", SmartestUserMessage::ERROR);
                        $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                    }
                
                }else{
                    
                    $this->addUserMessageToNextRequest("The property ID was in an invalid format.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/models');
                    
                }
                
                
                
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/models');
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was in an invalid format.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/models');
        }
        
    }
    
    public function previewTwitterAccountItemPropertyValue(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                $item->setDraftMode(true);
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                            
                            $twitter_ipv_object = $item->getPropertyValueByNumericKey($property->getId());
                            $this->send($twitter_ipv_object, 'acct');
                            // print_r($twitter_ipv_object->getTweetsJson());
                        
                        }else{
                            $this->addUserMessage('property ID not recognised');
                            
                        }
                    }else{
                        $this->addUserMessage('property ID is wrong for model');
                    }
                    
                }else{
                    $this->addUserMessage('property ID isn\'t numeric');
                }
                
            }else{
                $this->addUserMessage('Item with that ID not found');
            }
        }else{
            $this->addUserMessage('Item ID not set or not numeric');
        }
        
    }
    
	public function editRelatedContent($get){
	    
	    $item = new SmartestItem;
	    $item_id = $this->getRequestParameter('item_id');
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getRequestParameter('model_id')){
	            
	            $model_id = (int) $this->getRequestParameter('model_id');
	            $model = new SmartestModel;
	            
	            if($model->find($model_id)){
	                $mode = 'items';
	            }else{
	                $mode = 'pages';
	            }
            }else{
                $mode = 'pages';
            }
	        
	        $this->send($mode, 'mode');
	        
	        if($mode == 'items'){
	            
	            $this->setTitle($item->getName()." | Related ".$model->getPluralName());
	            $this->send($item, 'item');
	            $this->send($model, 'model');
	            
	            if($model->getId() == $item->getModelId()){
	                $related_ids = $item->getRelatedItemIds(true, $model->getId());
                }else{
                    $related_ids = $item->getRelatedForeignItemIds(true, $model->getId());
                }
                
	            $all_items  = $model->getSimpleItems($this->getSite()->getId());
	            
	            if($item->getModelId() == $this->getRequestParameter('model_id')){
	                foreach($all_items as $k=>$i){
	                    if($item_id == $i->getId()){
	                        unset($all_items[$k]);
	                    }
	                }
                }
	            
	            $this->send($all_items, 'items');
	            $this->send($related_ids, 'related_ids');
	            
            }else{
                
                $this->setTitle($item->getName()." | Related pages");
    	        $this->send($item, 'item');
    	        $related_ids = $item->getRelatedPageIds(true);
    	        $helper = new SmartestPageManagementHelper;
    	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        $this->send($pages, 'pages');
    	        $this->send($related_ids, 'related_ids');
    	        
            }
	        
	        // $related_pages = $page->getRelatedPagesAsArrays(true);
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/models');
	    }
	    
	}
	
	public function modelInfo($get){
	    
	    $model_id = (int) $this->getRequestParameter('class_id');
	    $model = new SmartestModel();
	    
	    if($model->find($model_id)){
	        
	        $this->send($model, 'model');
	        $this->send($model->getMetaPages(), 'metapages');
	        
	        $num_items_on_site = count($model->getSimpleItems($this->getSite()->getId()));
	        $num_items_all_sites = count($model->getSimpleItems());
	        
	        $file_path = substr($model->getClassFilePath(), strlen(SM_ROOT_DIR));
	        $this->send($file_path, 'class_file');
	        
	        $this->send(($num_items_on_site > 0) ? number_format($num_items_on_site) : 'none', 'num_items_on_site');
	        $this->send(number_format($num_items_all_sites), 'num_items_all_sites');
	        $this->send($this->getUser()->hasToken('edit_model_plural_name'), 'allow_plural_name_edit');
	        $this->send($this->getUser()->hasToken('edit_model'), 'allow_infn_edit');
	        
	        $sites_where_used = $model->getSitesWhereUsed();
	        $multiple_sites = (count($sites_where_used) > 1);
	        
	        $site_ids = array();
            
	        foreach($sites_where_used as $s){
	            $site_ids[] = $s->getId();
	        }
	        
	        $shared = ($model->isShared() || $multiple_sites);
	        $this->send($shared, 'shared');
	        
	        $this->send(SmartestFileSystemHelper::getFileSizeFormatted($model->getClassFilePath()), 'class_file_size');
	        
	        $is_movable = $model->isMovable();
	        
	        if($shared){
	            $ast = (!$multiple_sites && $model->getSiteId() == $this->getSite()->getId() && $is_movable);
            }else{
                $ast = ($model->hasSameNameAsModelOnOtherSite() || !$is_movable) ? false : true;
            }
            
            $this->send($ast, 'allow_sharing_toggle');
            $this->send($is_movable, 'is_movable');
            
            if(!$is_movable){
                $this->send($model->getFilesThatMustBeWrtableForSharingToggleButAreNot(), 'unwritable_files');
            }
            
	        $this->send($this->getSite()->getId(), 'current_site_id');
	        
	        if($model->getSiteId() == '0'){
	            $this->send(new SmartestString('Not set'), 'main_site_name');
            }else{
                $this->send(new SmartestString($model->getMainSite()->getName()), 'main_site_name');
            }
	        
	        $this->send($model->getAvailableDescriptionProperties(), 'description_properties');
	        $this->send($model->getAvailableSortProperties(), 'sort_properties');
	        $this->send($model->getAvailableThumbnailProperties(), 'thumbnail_properties');
	        
	        $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $model_id);
  	        $this->send($recent, 'recent_items');
  	        
  	        $allow_create_new = $this->getUser()->hasToken('add_items');
  	        $this->send($allow_create_new, 'allow_create_new');
  	        
  	        $this->send($model->getAvailablePrimaryProperties(), 'available_primary_properties');
	        
	    }else{
	        
	    }
	    
	}
    
	public function itemInfo($get){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    
	    $item = SmartestCmsItem::retrieveByPk($item_id);
	    
	    if(is_object($item)){
	        
	        $this->setFormReturnUri();
	        
	        $this->send($item->getModel()->getMetaPages(), 'metapages');
	        $this->send(($this->getUser()->hasToken('modify_items') && $this->getRequestParameter('enable_ajax')), 'user_can_modify_items');
	        $this->send(($this->getUser()->hasToken('edit_item_name') && $this->getRequestParameter('enable_ajax')), 'user_can_modify_item_slugs');
		    
		    $authors = array_values($item->getItem()->getAuthors());
		    
		    $num_authors = count($authors);
            $byline = '';

            if($num_authors){
                
                for($i=0;$i<$num_authors;$i++){

                    $byline .= $authors[$i]['full_name'];

                    if(isset($authors[$i+2])){
                        $byline .= ', ';
                    }else if(isset($authors[$i+1])){
                        $byline .= ' and ';
                    }

                }

                $this->send($byline, 'byline');
            }else{
                $this->send('No Authors', 'byline');
            }
		    
		    if($page = $item->getMetaPage()){
		        $this->send(true, 'has_page');
		        $this->send($page, 'page');
		    }
		    
		    $sets = $item->getItem()->getCurrentStaticSets();
		    $this->send($sets, 'sets');
		    
		    $possible_sets = $item->getItem()->getPossibleSets();
		    $this->send($possible_sets, 'possible_sets');
		    
		    $this->setTitle($item->getModel()->getName().' Information | '.$item->getName());
		    $this->send($item, 'item');
		    
		    $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $item->getItem()->getItemclassId());
		    $this->send($recent, 'recent_items');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
    
	public function editModelAutomaticSets(){
	    
	    $model = new SmartestModel;
	    
	    if($model->find($this->getRequestParameter('model_id'))){
	        
	        $this->send($model, 'model');
	        $this->send($model->getAutomaticSetIdsForNewItem(), 'selected_set_ids');
	        $this->send($model->getStaticSets($this->getSIte()->getId()), 'sets');
	        
	    }
	    
	}
    
    public function indexModel(){
        
	    $model = new SmartestModel;
	    
	    if($model->find($this->getRequestParameter('class_id'))){
	        
	        $this->send($model, 'model');
            $this->send(count($model->getItemIds($this->getSite()->getId(), SM_STATUS_LIVE)), 'num_public_items');
	        
	    }
        
    }
    
}