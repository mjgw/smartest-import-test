<?php
  
// include_once SM_ROOT_DIR."System/Applications/Assets/AssetsManager.class.php";

class Templates extends SmartestSystemApplication{

	// private $AssetsManager;
	
	public function startPage(){
	    
	    if($this->getApplicationPreference('startpage_view') == 'groups'){
	        $this->forward('templates', 'templateGroups');
	    }else if($this->getApplicationPreference('startpage_view') == 'models'){
	        $this->forward('templates', 'templatesByModel');
	    }else{
	        $this->forward('templates', 'templateTypes');
	    }
	    
	}
	
	public function templateTypes(){          	
		
		$this->setTitle("Your Templates");
		$this->setFormReturnUri();
		$this->setFormReturnDescription('template types');
		
		$this->setApplicationPreference('startpage_view', 'types');
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		$locations = $h->getUnWritableStorageLocations();
		$this->send($types, 'types');
		$this->send($locations, 'locations');
		
		$this->setFormReturnDescription('template types');
		
		$this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId()), 'recently_edited');
		
	}
	
	public function templateGroups(){
	    
	    $this->requireOpenProject();
	    
	    $this->setTitle("Template groups");
	    $tlh = new SmartestTemplatesLibraryHelper;
	    // $h = new SmartestAssetsLibraryHelper;
	    $locations = $tlh->getUnWritableStorageLocations();
		$this->send($locations, 'locations');
		$this->setFormReturnUri();
		$this->setFormReturnDescription('template groups');
		
	    
	    
	    if($this->getRequestParameter('template_type') && in_array($this->getRequestParameter('template_type'), $tlh->getTypeCodes())){
	        $type = $this->getRequestParameter('template_type');
	    }else{
	        $this->setApplicationPreference('startpage_view', 'groups');
	        $type = 'ALL';
	    }
	    
	    $groups = $tlh->getTemplateGroups($type, $this->getSite()->getId());
	    $this->send($groups, 'groups');
	    
	}
	
	public function templatesByModel(){
	    
	    $this->requireOpenProject();
	    
	    $du = new SmartestDataUtility;
	    $models = $du->getModelsWithPrimaryKeyIndices($this->getSite()->getId());
        $this->send($models, 'models');
        $model_ids = $du->getModelIds($this->getSite()->getId());
        $tlh = new SmartestTemplatesLibraryHelper;
        $this->setApplicationPreference('startpage_view', 'models');
        $locations = $tlh->getUnWritableStorageLocations();
		$this->send($locations, 'locations');
        
        $this->setFormReturnUri();
        
        if($this->getRequestParameter('model_id') !== null && is_numeric($this->getRequestParameter('model_id'))){
            
            $model_id = $this->getRequestParameter('model_id');
            
            if($model_id > 0){
                $model = $models[$this->getRequestParameter('model_id')];
                $this->setApplicationPreference('templates_model_view_model_id', $model_id);
            }else{
                $model = new SmartestModel;
                $this->setApplicationPreference('templates_model_view_model_id', 0);
            }
            
        }else if($pref_id = $this->getApplicationPreference('templates_model_view_model_id')){
            $model_id = $pref_id;
            $model = $models[$pref_id];
        }else{
            $model_id = 0;
            $model = new SmartestModel;
        }
        
        if($model_id > 0){
            $this->setFormReturnDescription(strtolower($model->getName()).' templates');
        }else{
            $this->setFormReturnDescription('unassigned templates');
        }
        
        $this->send($this->getApplicationPreference('list_by_type_view', 'grid'), 'list_style');
        $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId()), 'recently_edited');
        $this->send($model, 'model');
        $this->send(new SmartestArray($tlh->getTemplatesByModelId($model_id, $this->getSIte()->getId())), 'templates');
	    
	}
	
	public function addTemplateGroup(){
	    
	    $tlh = new SmartestTemplatesLibraryHelper;
	    $template_types = $tlh->getTypes();
	    
	    if($this->getRequestParameter('filter_type')){
	        $this->send($this->getRequestParameter('filter_type'), 'filter_type');
	    }
	    
	    $this->send($template_types, 'template_types');
	    
	}
	
	public function insertTemplateGroup(){
	    
	    $this->requireOpenProject();
	    
	    $set = new SmartestTemplateGroup;
	    $set->setLabel($this->getRequestParameter('template_group_label'));
	    $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('template_group_label')));
	    
	    $tlh = new SmartestTemplatesLibraryHelper;
	    
	    if($this->getRequestParameter('template_group_type')){
	    
    	    if(in_array($this->getRequestParameter('template_group_type'), $tlh->getTypeCodes())){
	        
    	        $set->setFilterType('SM_SET_FILTERTYPE_TEMPLATETYPE');
    	        $set->setFilterValue($this->getRequestParameter('template_group_type'));
    	        $set->setSiteId($this->getSite()->getId());
    	        $shared = $this->getRequestParameter('template_group_shared') ? 1 : 0;
    	        $set->setShared($shared);
        	    $set->save();
    	        $this->addUserMessageToNextRequest("Your new template group was successfully created.", SmartestUserMessage::SUCCESS);
	        
        	    header("HTTP/1.1 201 Created");
        	    $this->redirect('/templates/editTemplateGroupContents?group_id='.$set->getId());
    	    
    	    }else{
	        
    	        $this->addUserMessageToNextRequest("The template type was not recognised. Please try again.", SmartestUserMessage::ERROR);
    	        $this->redirect('/templates/addTemplateGroup');
	        
	        }
	    
        }else{
            
            $this->addUserMessage('You must select what type of templates will be used in this group', SmartestUserMessage::WARNING);
            $this->forward('templates', 'addTemplateGroup');
            
        }
	    
	}
	
	public function browseTemplateGroup($get){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('template group');
	    $this->send($this->getApplicationPreference('list_by_type_view', 'grid'), 'list_style');
	    
	    $group = new SmartestTemplateGroup;
	    
	    if($group->find($group_id)){
	        
	        $this->send(new SmartestArray($group->getMembers($this->getSite()->getId(), false)), 'templates');
	        $this->send($group, 'group');
	        
	    }
	    
	}
	
	public function editTemplateGroup(){
	    
	    $group = new SmartestTemplateGroup;
	    
	    if($group->find($this->getRequestParameter('group_id'))){
	        
	        $this->send($group, 'group');
	        $this->send($this->getUser()->hasToken('edit_template_group_names'), 'allow_name_edit');
	        
	        $is_empty = count($group->getMemberships()) == 0;
	        
	        $this->send($is_empty, 'allow_type_change');
	        
	        if($is_empty){
	        
    	        $h = new SmartestTemplatesLibraryHelper;
    	        $this->send($h->getGroupableTypes(), 'template_types');
    	    
	        }
	        
	        $this->send(true, 'allow_shared_toggle');
	        
	        $du = new SmartestDataUtility;
	        $this->send($du->getModels(false, $this->getSite()->getId()), 'models');
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function updateTemplateGroup(){
	    
	    $group = new SmartestTemplateGroup;
	    $group_id = (int) $this->getRequestParameter('group_id');
	    
	    if($group->find($group_id)){
	        
	        $group->setLabel($this->getRequestParameter('group_label'));
	        
	        if($this->getUser()->hasToken('edit_template_group_names')){
	            $group->setName(SmartestStringHelper::toVarName($this->getRequestParameter('group_name')));
	        }
	        
	        /* if($group->isUsedForPlaceholders()){
                $group->setShared(1);
            }else{ */
                $shared = ($this->getRequestParameter('group_shared') && $this->getRequestParameter('group_shared')) ? 1 : 0;
                $group->setShared($shared);
            // }
            
            $group->setItemclassId($this->getRequestParameter('template_group_model_id'));
            
            if(count($group->getMemberships()) == 0){
                
                $group->setFilterType('SM_SET_FILTERTYPE_TEMPLATETYPE');
        	    $group->setFilterValue($this->getRequestParameter('template_group_type'));
                
            }
            
            $group->save();
            
            $this->addUserMessageToNextRequest("The template group was updated", SmartestUserMessage::SUCCESS);
            $this->redirect('/templates/editTemplateGroup?group_id='.$group_id);
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The template group ID was not recognized", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/templates/groups');
	        
	    }
	    
	}
	
	public function editTemplateGroupContents(){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $this->setFormReturnUri();
	    
	    $group = new SmartestTemplateGroup;
	    
	    if($group->find($group_id)){
	        
	        $this->send($group->getOptions($this->getSite()->getId()), 'non_members');
	        $this->send($group->getMembers($this->getSite()->getId(), false), 'members');
	        $this->send($group, 'group');
	        
	    }
	    
	}
	
	public function transferSingleTemplate($get, $post){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestTemplateGroup;
	    
	    if($group->find($group_id)){
	        
	        $template_id = (int) $this->getRequestParameter('template_id');
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            if($this->getRequestParameter('transferAction') == 'add'){
	                $group->addTemplateById($template_id);
                }else{
                    $group->removeTemplateById($template_id);
                }
	        }
	        
	        if($this->getRequestParameter('from') == 'edit'){
                $this->redirect('/templates/editTemplate?template_id='.$template->getId());
    	    }else{
    	        $this->formForward();
    	    }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	}
	
	public function transferTemplates($get, $post){
	    
	    $group_id = $this->getRequestParameter('group_id');
	    
	    $group = new SmartestTemplateGroup;
	    
	    if($group->find($group_id)){
	        
	        if($this->getRequestParameter('transferAction') == 'add'){
	            
	            $template_ids = ($this->getRequestParameter('available_templates') && is_array($this->getRequestParameter('available_templates'))) ? $this->getRequestParameter('available_templates') : array();
	            
	            foreach($template_ids as $tid){
	            
	                $group->addTemplateById($tid);
	            
	            }
	            
	        }else{
	            
	            $template_ids = ($this->getRequestParameter('used_templates') && is_array($this->getRequestParameter('used_templates'))) ? $this->getRequestParameter('used_templates') : array();
	            
	            foreach($template_ids as $tid){
	            
	                $group->removeTemplateById($tid);
	            
	            }
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The group ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function deleteTemplateGroupConfirm(){
	    
	}
	
	public function deleteTemplateGroup(){
	    
	}
	
	public function listByType($get){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('templates');
	    $h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('type');
	    $types = $h->getTypes();
	    
	    if(in_array($type_code, $h->getTypeCodes())){
	        
	        $type = $types[$type_code];
	        
	        $this->send(is_writable(SM_ROOT_DIR.$type['storage']['location']), 'dir_is_writable');
	        $this->send($type, 'type');
	        $this->send(true, 'show_list');
	        
	        switch($type_code){
	            
	            case "SM_ASSETTYPE_MASTER_TEMPLATE":
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_MASTER_TEMPLATE'), 'recently_edited');
	            $templates = $h->getMasterTemplates($this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_CONTAINER_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_CONTAINER_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_ITEMSPACE_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_ITEMSPACE_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_ITEMSPACE_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_ART_LIST_TEMPLATE'), 'recently_edited');
	            $templates = $h->getArticulatedListTemplates($this->getSite()->getId());
	            break;
	            
	            case "SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE":
	            $alh = new SmartestAssetsLibraryHelper;
	            $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), 'SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE'), 'recently_edited');
	            $templates = $alh->getAssetsByTypeCode("SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE", $this->getSite()->getId());
	            break;
	            
	        }
	        
	        $this->send($this->getApplicationPreference('list_by_type_view', 'grid'), 'list_style');
	        $this->send($templates, 'templates');
	        $this->send(count($templates), 'count');
	    
        }
	    
	}
	
	public function import(){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $non_template_categories = $h->getCategoryShortNames();
	    $templates_key = array_search('templates', $non_template_categories);
	    unset($non_template_categories[$templates_key]);
	    $types = $h->getTypes();
	    
	    $location_types = $h->getTypeCodesByStorageLocation($non_template_categories);
	    $locations = array_keys($location_types);
	    $location_types_info = $location_types;
	    
	    foreach($location_types_info as $path => &$l){
	        foreach($l as &$type){
	            $type = $types[$type];
	        }
	    }
	    
	    $this->send($location_types_info, 'types_info');
	    
	    // now, get a list of the file names for each location that can be found in the database
	    foreach($location_types as $location => $types){
            
            $sql = "SELECT asset_url FROM Assets WHERE asset_type IN ('".implode("', '", $types)."')";
            $result = SmartestDatabase::getInstance('SMARTEST')->queryToArray($sql);
            $db_files[$location] = array();
            
            foreach($result as $f){
                if(strlen($f['asset_url'])){
                    $db_files[$location][] = $f['asset_url'];
                }
            }
        }
        
        // now, get a list of the file names for each location, whether or not they can be found in the database.
        foreach($locations as $location){
            
            $disk_files[$location] = array();
            $disk_files[$location] = SmartestFileSystemHelper::getDirectoryContents($location, false, SM_DIR_SCAN_FILES);
            
        }
        
        // now, compare the list of what is found in each location with what exists in the database for those types.
        foreach($locations as $location){
            
            $new_files[$location] = array();
            foreach($disk_files[$location] as $file_on_disk){
                // if the file is not in the database,
                if(!in_array($file_on_disk, $db_files[$location])){
                    // it is a new file.
                    $new_files[$location][] = $file_on_disk;
                }
            }
        }
        
        $this->send($new_files, 'new_files');
	    
	}
	
	public function addTemplateData($get, $post){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $location_types = $h->getTypeCodesByStorageLocation();
	    
	    if($this->requestParameterIsSet('new_files') && is_array($this->getRequestParameter('new_files'))){
	        $new_files = $this->getRequestParameter('new_files');
	    }else{
	        $new_files = array();
	    }
	    
	    $files_array = array();
	    $i = 0;
	    
	    foreach($new_files as $f){
	        
	        $files_array[$i] = array();
	        $types = $h->getPossibleTypesBySuffix(SmartestStringHelper::getDotSuffix($f));
	        $files_array[$i]['filename'] = basename($f);
	        // $files_array[$i]['suggested_name'] = SmartestStringHelper::removeDotSuffix($files_array[$i]['filename']);
	        $files_array[$i]['suggested_name'] = SmartestStringHelper::toTitleCaseFromFileName(SmartestStringHelper::removeDotSuffix($files_array[$i]['filename']));
	        $files_array[$i]['current_directory'] = dirname($f).'/';
	        
	        if(count($types)){
	            $files_array[$i]['possible_types'] = $types;
	            $files_array[$i]['suffix_recognized'] = true;
            }else{
                $files_array[$i]['possible_types'] = $h->getAcceptableNameOptionsForUnknownSuffix($files_array[$i]['filename'], $files_array[$i]['current_directory']);
                $files_array[$i]['suffix_recognized'] = false;
                $files_array[$i]['actual_suffix'] = SmartestStringHelper::getDotSuffix($f);
            }
            
	        $files_array[$i]['type_code'] = $type['id'];
	        $files_array[$i]['type_label'] = $type['label'];
	        
	        $alh = new SmartestAssetsLibraryHelper;
	        
	        $files_array[$i]['size'] = SmartestFileSystemHelper::getFileSizeFormatted(SM_ROOT_DIR.$f);
	        $i++;
	    }
	    
	    $this->send($files_array, 'files');
	    
	}
	
	public function createTemplateAssetsFromFiles($get, $post){
	    
	    if($this->requestParameterIsSet('new_files') && is_array($this->getRequestParameter('new_files'))){
	        $new_files = $this->getRequestParameter('new_files');
	    }else{
	        $new_files = array();
	    }
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $asset_types = SmartestDataUtility::getAssetTypes();
	    
	    foreach($new_files as $nf){
	        
	        $type = $asset_types[$nf['type']];
	        $required_suffixes = array();
	        
	        foreach($type['suffix'] as $s){
	            $required_suffixes[] = $s['_content'];
	        }
	        
	        $existing_location = dirname($nf['filename']).'/';
	        $existing_suffix = SmartestStringHelper::getDotSuffix($nf['filename']);
	        
	        $required_location = $type['storage']['location'];
	        
	        if($existing_location != $required_location){
	            // The file type has been recognized by its file suffix, but needs to be moved to the right place for the file type chosen by the user (user has been warned about this)
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($nf['filename']));
	            $success = SmartestFileSystemHelper::move(SM_ROOT_DIR.$nf['filename'], $move_to);
	            $filename = SmartestFileSystemHelper::baseName($move_to);
	        }else if(!in_array($existing_suffix, $required_suffixes)){
	            // The file is in the right place, but had an unrecognized file suffix (so needs to be renamed - user has been warned about this)
	            $no_suffix = SmartestStringHelper::removeDotSuffix($nf['filename']);
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($no_suffix).'.'.$required_suffixes[0]);
	            $success = SmartestFileSystemHelper::move(SM_ROOT_DIR.$nf['filename'], $move_to);
	            $filename = SmartestFileSystemHelper::baseName($move_to);
	        }else{
	            $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$nf['filename']);
	            $filename = SmartestFileSystemHelper::baseName($nf['filename']);
	            $success = true;
	        }
	        
	        if($success){
	            
    	        $a = new SmartestTemplateAsset;
    	        
    	        $a->setType($nf['type']);
    	        $a->setSiteId($this->getSite()->getId());
    	        $a->setShared(isset($nf['shared']) ? 1 : 0);
    	        $a->setWebid(SmartestStringHelper::random(32));
    	        $a->setLabel($nf['name']);
    	        $a->setStringid(SmartestStringHelper::toVarName($nf['name']));
    	        $a->setUrl($filename);
    	        $a->setUserId($this->getUser()->getId());
    	        $a->setCreated(time());
    	        $a->save();
    	        
            }
	        
	    }
	    
	    $this->addUserMessageToNextRequest(count($new_files)." file".((count($new_files) == 1) ? " was " : "s were ")."successfully added to the repository.", SmartestUserMessage::SUCCESS);
	    $this->formForward();
	    
	}
    
    public function startSingleTemplateImport(){
        
        $tlh = new SmartestTemplatesLibraryHelper;
        $types = $tlh->getTypes();
        $type_codes = $tlh->getTypeCodes();
        
        if($this->requestParameterIsSet('type')){
            
            $type = $this->getRequestParameter('type');
            $this->send($types[$type], 'template_type');
            
            if(in_array($type, $type_codes)){
                
                $this->send(true, 'type_specified');
                $ui = $tlh->getUnimportedTemplatesByType($type);
                $this->send($ui, 'potential_templates');
                
                if($this->requestParameterIsSet('group_id')){
                    $group_id = $this->getRequestParameter('group_id');
                    $group = new SmartestTemplateGroup;
                    if($group->find($group_id)){
                        $this->send($group, 'add_to_group');
                    }
                }
                
                if($this->requestParameterIsSet('style_id')){
                    $style_id = $this->getRequestParameter('style_id');
                    $style = new SmartestBlockListStyle;
                    if($style->find($style_id)){
                        $this->send($style, 'blocklist_style');
                    }
                }
                
            }else{
                $this->send(false, 'type_specified');
                $this->send($types, 'types');
            }
            
        }else{
            $this->send(false, 'type_specified');
            $this->send($types, 'types');
        }
        
    }
    
    public function finishSingleTemplateImport(){
        
        $template = new SmartestTemplateAsset;
        $template->setUrl($this->getRequestParameter('chosen_file'));
        $template->setType($this->getRequestParameter('type'));
        $template->setSiteId($this->getSite()->getId());
        $template->setUserId($this->getUser()->getId());
        $template->setLabel($this->getRequestParameter('new_template_label'));
        $template->setStringId(SmartestStringHelper::toVarName($this->getRequestParameter('new_template_label')));
        $template->setWebId(SmartestStringHelper::random(32));
        $template->save();
        
        if($group_id = $this->getRequestParameter('add_to_group_id')){
            $group = new SmartestTemplateGroup;
            if($group->find($group_id)){
                $template->addToGroupById($group_id);
                $this->redirect('/templates/browseTemplateGroup?group_id='.$group->getId());
                exit;
            }
        }
        
        if($this->getRequestParameter('type') == 'SM_ASSETTYPE_BLOCKLIST_TEMPLATE' && $style_id = $this->getRequestParameter('blocklist_style_id')){
            // Associate template with blocklist style:
            $style_id = $this->getRequestParameter('blocklist_style_id');
            $style = new SmartestBlockListStyle;
            if($style->find($style_id)){
                $style->addBlockTemplate($template);
                $this->redirect('@blocklists:edit_blocklist_style?style_id='.$style->getId());
            }
        }
        
        $this->formForward();
        
    }
	
	public function importSingleTemplate($get){
	    
	    $h = new SmartestAssetsLibraryHelper;
	    $non_template_categories = $h->getCategoryShortNames();
	    $templates_key = array_search('templates', $non_template_categories);
	    unset($non_template_categories[$templates_key]);
	    $show_form = true;
        
		$tlh = new SmartestTemplatesLibraryHelper;
		$template_types = $tlh->getTypes();
        $this->send($template_types, 'template_types');
	    
	    // $cat = $h->getTypesByCategory($non_template_categories);
	    // $types = $cat['templates']['types'];
	    // $this->send($types, 'template_types');
	    
	    // $location = $h->getStorageLocationByTypeCode($this->getRequestParameter('asset_type'));
	    // 
	    // if($location == SmartestAssetsLibraryHelper::ASSET_TYPE_UNKNOWN){
	    //     $message = "Template type ".$this->getRequestParameter('asset_type')." was not recognized.";
	    //     SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
	    //     $this->addUserMessage($message, SmartestUserMessage::WARNING);
	    //     $show_form = false;
	    // }else if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
	    //     $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
	    //     SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
	    //     // $this->send($message, 'error_message');
	    //     $this->addUserMessage($message, SmartestUserMessage::WARNING);
	    //     $show_form = false;
	    // }else{
	    //     $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$this->getRequestParameter('template'));
	    //     $force_shared = ($template->isInUseOnMultipleSites($this->getRequestParameter('asset_type')) || (count($template->getSitesWhereUsed()) > 0 && !in_array($this->getSite()->getId(), $template->getSiteIdsWhereUsed())));
	    //     $this->send($force_shared, 'force_shared');
	    //     $this->send($template, 'template');
	    // }
	    // 
	    // $this->send($show_form, 'show_form');
	    
	}
	
	public function importNewTemplateForContainerDefinition(){
	    
	    if($this->getRequestParameter('container_id') && $this->getRequestParameter('page_id')){
            
            $page = new SmartestPage;
            
            if($page->find($this->getRequestParameter('page_id'))){
            
                $container = new SmartestContainer;
                $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
                $this->send($instance_name, 'instance');
                
                if($container->find($this->getRequestParameter('container_id'))){
                        
                    $alh = new SmartestAssetsLibraryHelper;
                    $potential_templates = $alh->getUnimportedFilesByType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
                    
                    $this->send($potential_templates, 'potential_templates');
                    $this->send((bool) count($potential_templates), 'potential_templates_exist');
                    
                    $this->send($container, 'container');
                    $this->send($page, 'page');
                    
                    if(is_numeric($this->getRequestParameter('item_id'))){
                        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                            $this->send($item, 'item');
                        }
                        $this->send((int) $this->getRequestParameter('item_id'), 'item_id');
                    }
                    
    	            $page_definition = new SmartestContainerDefinition;
	            
    	            if($page_definition->loadWithObjects($container, $page, true, null, $instance_name)){
	                
    	                if($type_index[$page_webid] == 'ITEMCLASS'){
	                    
    	                    $item_definition = new SmartestContainerDefinition;
	                    
    	                    if($item_definition->loadWithObjects($container, $page, true, $item_id, $instance_name)){
	                        
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
                    
                    // selected_template_id
                        
                        // print_r($potential_templates);
                        
                    /* if(!$this->getRequestParameter('asset_type')){
                        
                        $types = $placeholder->getPossibleFileTypes();
                        $this->send($types, 'types');
                        
                        if(count($types) == 1){
                            $url = '/smartest/file/new?for=placeholder&asset_type='.$types[0]['id'].'&placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
                        }else{
                            $url = '/smartest/file/new?for=placeholder&placeholder_id='.$placeholder->getId().'&page_id='.$page->getId();
                        }
                        
                        if($this->getRequestParameter('item_id')) $url .= '&item_id='.$this->getRequestParameter('item_id');
                        
                        $this->redirect($url);
                        
                    } */
                    
                }else{
                    $this->addUserMessageToNextRequest("The placeholder ID was not recognised.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/files');
                }
            
            }else{
                
                $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/pages');
                
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("Both page and placeholder IDs must be provided.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/pages');
            
        }
	    
	}
	
    public function finishNewTemplateImportToContainerDefinition(){
	    
	    if($this->getRequestParameter('container_id') && $this->getRequestParameter('page_id')){
            
  	        $helper = new SmartestPageManagementHelper;
        	$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
            
            $page_id = $this->getRequestParameter('page_id');
		
        	if(isset($type_index[$page_id])){
        	    if($type_index[$page_id] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
        		    $page = new SmartestItemPage;
        		}else{
        		    $page = new SmartestPage;
        		}
        	}else{
        	    $page = new SmartestPage;
        	}
            
            if($page->find($this->getRequestParameter('page_id'))){
            
                $container = new SmartestContainer;
            
                if($container->find($this->getRequestParameter('container_id'))){
                    
                    // first create container template
                    
                    $template = new SmartestTemplateAsset;
                    $template->setLabel($this->getRequestParameter('new_template_label'));
                    $template->setStringId(SmartestStringHelper::toVarName($this->getRequestParameter('new_template_label')));
                    $template->setUserId($this->getUser()->getId());
                    $template->setUrl($this->getRequestParameter('chosen_file'));
                    $template->setType('SM_ASSETTYPE_CONTAINER_TEMPLATE');
                    $template->setSiteId($this->getSite()->getId());
                    $template->setCreated(time());
                    $template->setWebId(SmartestStringHelper::random(32));
                    $template->save();
                    
                    $instance_name = ($this->requestParameterIsSet('instance') && strlen($this->getRequestParameter('instance'))) ? $this->getRequestParameter('instance') : 'default';
                    
                    // Next create Container definition
                    
                    $definition = new SmartestContainerDefinition;
                    
                    if($type_index[$page_id] == 'NORMAL' || ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') != 'THIS')){
	                
    	                if($definition->loadForUpdate($container->getName(), $page, true, null, $instance_name)){
    	                    
                            $definition->setDraftAssetId($template->getId());
                            $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on page '".$page->getTitle(true)."' with newly imported template ".$template->getUrl().".";
                            
                        }else{
	                    
    	                    // wasn't already defined
    	                    $definition->setDraftAssetId($template->getId());
    	                    $definition->setAssetclassId($container->getId());
    	                    $definition->setInstanceName($instance_name);
    	                    $definition->setPageId($page->getId());
    	                    $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on page '".$page->getTitle(true)."' with newly imported template ".$template->getUrl().".";
	                
    	                }
	            
    	                if($this->getRequestParameter('definition_scope') == 'ALL'){
	                    
    	                    // DELETE ALL PER-ITEM DEFINITIONS
    	                    $pmh = new SmartestPageManagementHelper;
    	                    $pmh->removePerItemDefinitions($page->getId(), $container->getId());
	                    
    	                }
	                
    	                $definition->save();
	            
                    }else if($type_index[$page_id] == 'ITEMCLASS' && ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS')){
                        
                        // If this is an item meta-page the user has opted to use this definition only for the current item
                        if($definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'), $instance_name)){
	                    
    	                    // all-items definition doesn't exist but per-item for this item does
    	                    $definition->setDraftAssetId($template->getId());
    
    	                    if(is_array($this->getRequestParameter('params'))){
        	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
        	                }
    
        	                $definition->save();
        	                $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$template->getId()." when displaying item ID ".$this->getRequestParameter('item_id').".";
    
    	                }else{
    
    	                    // wasn't already defined for any items at all. Define for this item
    	                    $definition->setDraftAssetId($template->getId());
    	                    $definition->setAssetclassId($container->getId());
    	                    $definition->setItemId($this->getRequestParameter('item_id'));
    	                    $definition->setInstanceName($instance_name);
    	                    $definition->setPageId($page->getId());
    
    	                    if(is_array($this->getRequestParameter('params'))){
        	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
        	                }
    
        	                $definition->save();
        	                $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$template->getId()." when displaying item ID ".$this->getRequestParameter('item_id').".";
    
    	                }
                        
                    }
                    
                    /* $def->setPageId($page->getId());
                    $def->setAssetClassId($container->getId());
                    $def->setDraftAssetId($template->getId());
                        
                    if(is_numeric($this->getRequestParameter('item_id'))){
                        // $this->send($this->getRequestParameter('item_id'), 'item_id');
                        $def->setItemId($this->getRequestParameter('item_id'));
                    }
                    
                    $def->save(); */
                    
                    // TODO: Add the template to a group if the container is limited to one.
                    
                    if($container->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_TEMPLATEGROUP'){
                        $template_group_id = $container->getFilterValue();
                        $group = new SmartestTemplateGroup;
                        if($group->find($template_group_id)){
                            $group->addTemplateById($template->getId(), false);
                        }
                    }
                    
                    $this->formForward();
                    
                }else{
                    $this->addUserMessageToNextRequest("The placeholder ID was not recognised.", SmartestUserMessage::ERROR);
                    $this->redirect('/smartest/files');
                }
            
            }else{
                
                $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
                $this->redirect('/smartest/pages');
                
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("Both page and placeholder IDs must be provided.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/pages');
            
        }
	    
	}
	
	public function addSingleTemplateToDatabase($get, $post){
	    
	    $tlh = new SmartestTemplatesLibraryHelper;
	    $types = $tlh->getTypes();
	    
	    $type = $types[$this->getRequestParameter('template_type')];
	    
	    $existing_location = $this->getRequestParameter('template_current_storage');
        
        $required_location = $type['storage']['location'];
        $current_path = realpath(SM_ROOT_DIR.$existing_location.$this->getRequestParameter('template_filename'));
        
        if(is_file($current_path) && SmartestFileSystemHelper::isSafeFileName($current_path, SM_ROOT_DIR.'Presentation/')){
        
            if($existing_location != $required_location){
                // The file type has been recognized by its file suffix, but needs to be moved to the right place (so needs to be moved - user has been warned about this)
                $move_to = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.$required_location.SmartestFileSystemHelper::baseName($current_path));
                $success = SmartestFileSystemHelper::move($current_path, $move_to);
                $filename = SmartestFileSystemHelper::baseName($move_to);
            }else{
                $move_to = $current_path;
                $filename = SmartestFileSystemHelper::baseName($move_to);
                $success = true;
            }
        
            if($success){
            
    	        $a = new SmartestAsset;
	        
    	        $a->setType($this->getRequestParameter('template_type'));
    	        $a->setSiteId($this->getSite()->getId());
    	        $a->setShared($this->requestParameterIsSet('template_type') ? 1 : 0);
    	        $a->setWebid(SmartestStringHelper::random(32));
    	        $a->setStringid(SmartestStringHelper::toVarName($this->getRequestParameter('template_name')));
    	        $a->setUrl($filename);
    	        $a->setUserId($this->getUser()->getId());
    	        $a->setCreated(time());
    	        $a->save();
                
                $this->addUserMessageToNextRequest("The template has been successfully imported to the repository.", SmartestUserMessage::SUCCESS);

            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The file you tried to import was not found or was outside the templates directory.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function convertTemplateType($get){
	    
	    $t = new SmartestTemplateAsset;
	    $id = (int) $this->getRequestParameter('template_id');
	    
	    if($t->find($id)){
	        $this->send($t, 'template');
	        $is_convertable = $t->isConvertable();
	        if($is_convertable){
	            $alh = new SmartestAssetsLibraryHelper;
	            $possible_types = $alh->getSelectedTypes($t->getTypeConvertOptions());
	            $this->send(array_values($possible_types), 'possible_types');
	            $this->send($t->getTypeInfo(), 'current_type');
	        }else{
	            
	        }
	        $this->send($is_convertable, 'is_convertable');
	    }else{
	        $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function updateTemplateType($get, $post){
	    
	    $template_id = (int) $this->getRequestParameter('template_id');
	    $t = new SmartestTemplateAsset;
	    
	    if($t->find($template_id)){
	        if($t->isConvertable()){
	            
	            $h = new SmartestTemplatesLibraryHelper;
	            $types = $h->getTypes();
	            
	            if(array_key_exists($this->getRequestParameter('new_type'), $types)){
	                $t->setType($this->getRequestParameter('new_type'));
	                $t->save();
	                $this->addUserMessageToNextRequest("The template was successfully converted to type: \"".$types[$this->getRequestParameter('new_type')]['label']."\"", SmartestUserMessage::SUCCESS);
	                SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} changed template '{$t->getLabel()}' to type '".$types[$this->getRequestParameter('new_type')]['label']."'.", SmartestLog::USER_ACTION);
                }else{
                    $this->addUserMessageToNextRequest("The new type was not recognized", SmartestUserMessage::ERROR);
                }
	            
	        }else{
	            $this->addUserMessageToNextRequest("This template cannot be converted at the moment.", SmartestUserMessage::WARNING);
	        }
	    }else{
	        $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function addTemplate($get){
		
		// $type = (in_array($this->getRequestParameter('type'), array('SM_PAGE_MASTER_TEMPLATE', 'SM_LIST_ITEM_TEMPLATE', 'SM_CONTAINER_TEMPLATE'))) ? $this->getRequestParameter('type') : 'SM_PAGE_MASTER_TEMPLATE';
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		
		if($this->requestParameterIsSet('type')){
		
		    $type_id = $this->getRequestParameter('type');
            
            if(isset($types[$type_id])){
                
                $type = $types[$type_id];
		
        		$title = "Add a new ".strtolower($type['label']);
        		$path = SM_ROOT_DIR.$type['storage']['location'];
                
    		    $allow_save = is_writable($path);
    		    $this->send($path, 'path');
    		    $default_name = SmartestStringHelper::toVarName('untitled '.$type['label']);
                $this->send($type, 'template_type');
                $this->send(true, 'type_specified');
                
            }else{
                $this->addUserMessageToNextRequest("The specified template type does not exist.", SmartestUserMessage::ERROR);
                $this->formForward();
            }
    		
		    
		/* switch($type){
		    case "SM_PAGE_MASTER_TEMPLATE":
		    case "SM_ASSETTYPE_MASTER_TEMPLATE":
		    $title = "Add a Page Master Template";
		    $path = SM_ROOT_DIR."Presentation/Masters/";
		    break;
		    
		    case "SM_LIST_ITEM_TEMPLATE":
		    case "SM_ASSETTYPE_ART_LIST_TEMPLATE":
		    $title = "Add a List Item Template";
		    $path = SM_ROOT_DIR."Presentation/ListItems/";
		    break;
		    
		    case "SM_CONTAINER_TEMPLATE":
		    case "SM_ASSETTYPE_CONTAINER_TEMPLATE":
		    $title = "Add a Container Template";
		    $path = SM_ROOT_DIR."Presentation/Layouts/";
		    break;
		} */
		
	    }else{
	        
	        $title = "Add a new template";
	        $default_name = 'untitled_template';
            $numeric_types = array_values($types);
            $type = $numeric_types[0];
	        
	        $allow_save = false;
	        
	        foreach($types as $t){
	            if($t['storage']['writable']){
	                $allow_save = true;
	                break;
	            }
	        }
	        
	    }
        
        $this->send($types, 'types');
        
        if($this->requestParameterIsSet('style_id')){
            $style_id = $this->getRequestParameter('style_id');
            $style = new SmartestBlockListStyle;
            if($style->find($style_id)){
                $this->send($style, 'blocklist_style');
            }
        }
	    
	    if($this->getRequestParameter('add_to_group_id')){
	        
	        $group = new SmartestTemplateGroup;
	        
	        if($group->find($this->getRequestParameter('add_to_group_id'))){
	            $type_id = $group->getFilterValue();
	            $type = $types[$type_id];
	            $default_name = SmartestStringHelper::toVarName('untitled '.$type['label']);
	            $title = "Add a new ".strtolower($type['label']);
	            $this->send($group, 'add_to_group');
	        }
	        
	    }
        
        $this->send($default_name, 'default_name');
	    $this->send($type, 'template_type');
	    $this->send($type ? true : false, 'type_specified');
		
		$this->setTitle($title);
		
		$formTemplateInclude = "addTemplate.tpl";
		$this->send($allow_save, 'allow_save');
		$this->send($title, 'interface_title');
		
	}
	
 	public function saveNewTemplate($get, $post){
		
		$h = new SmartestTemplatesLibraryHelper;
		$types = $h->getTypes();
		
		// $template_type = $this->getRequestParameter('template_type');
		$type_id = $this->getRequestParameter('template_type');
		
		if(isset($types[$type_id])){
		    
		    $type = $types[$type_id];
		
    		$path = SM_ROOT_DIR.$type['storage']['location'];
		
    		if($this->getRequestParameter('add_type') == "DIRECT"){
		    
    			$content  = $this->getRequestParameter('template_content');
			
    			if(substr($content, 0, 9) == '<![CDATA['){
    			    $content = substr($content, 9);
    			}
			
    			if(substr($content, -3) == ']]>'){
    			    $content = substr($content, 0, -3);
    			}
			    
			    $label    = $this->getRequestParameter('template_name');
    			$stringid = SmartestStringHelper::toVarName($this->getRequestParameter('template_name'));
			
    			// if(SmartestStringHelper::getDotSuffix($this->getRequestParameter('template_name')) != 'tpl'){
    			    $file = SmartestFileSystemHelper::removeDotSuffix($stringid).'.tpl';
    			/* }else{
    			    
    			} */
			    
			    $full_filename = SmartestFileSystemHelper::getUniqueFileName($path.$file);
    			$final_filename = basename($full_filename);
			
    		}elseif($this->getRequestParameter('add_type') == "UPLOAD"){
		    
    		    $uploader = new SmartestUploadHelper('template_upload');
    			$uploader->setUploadDirectory($path);
			    
    			if(!$uploader->hasDotSuffix('tpl')){
        			$uploader->setFileName(SmartestStringHelper::toVarName(SmartestFileSystemHelper::removeDotSuffix($uploader->getFileName())).".tpl");
        		}
    		
        		$final_filename = $uploader->getFileName();
        		$full_filename = $path.$final_filename;
			
    		}
		
    		$new_template = new SmartestTemplateAsset;
		
    		$new_template->setType($type_id);
    		$new_template->setCreated(time());
    		$new_template->setLabel($stringid);
    		$new_template->setStringid($stringid);
    		$new_template->setWebid(SmartestStringHelper::random(32));
    		$new_template->setUrl($final_filename);
    		$new_template->setSiteId($this->getSite()->getId());
    		$shared = $this->getRequestParameter('template_shared') ? 1 : 0;
    		$new_template->setShared($shared);
		
    		if($this->getRequestParameter('add_type') == "DIRECT"){
			
    			if(SmartestFileSystemHelper::save($full_filename, stripslashes($this->getRequestParameter('template_content')), true)){
    			    SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} created a new template '{$new_template->getLabel()}' (".$full_filename.").", SmartestLog::USER_ACTION);
    				$this->addUserMessageToNextRequest('The file was saved successfully.', SmartestUserMessage::SUCCESS);
    				$file_success = true;
    			}else{
    				$this->addUserMessageToNextRequest('There was a problem creating the file.', SmartestUserMessage::WARNING);
    				$file_success = false;
    			}
			
    		}else if($this->getRequestParameter('add_type') == "UPLOAD"){
		
    		    if($uploader->save()) { // Move the file over
    			    $this->addUserMessageToNextRequest('The file was saved successfully.', SmartestUserMessage::SUCCESS);
    			    $file_success = true;
    		    }else{ // Couldn't save the file
    		        $this->addUserMessageToNextRequest('There was a problem creating the file.', SmartestUserMessage::WARNING);
    		        $file_success = false;
    		    }
		
    		}
    		
    	    if($file_success){
    	        // Add the template asset to the database
    	        $new_template->save();
    	        @chmod($full_filename, 0666);
    	        
    	        if($this->requestParameterIsSet('add_to_group_id')){
    	            
    	            $group = new SmartestTemplateGroup;
    	            
    	            if($group->find($this->getRequestParameter('add_to_group_id'))){
    	                if($group->getFilterValue() == $new_template->getType()){
    	                    $group->addTemplateById($new_template->getId());
    	                }else{
    	                    $this->addUserMessageToNextRequest('The new template could not be added to group '.$group->getLabel().' because it is the wrong type of template.', SmartestUserMessage::WARNING);
    	                }
    	            }
    	            
    	        }
                
                if($type_id == 'SM_ASSETTYPE_BLOCKLIST_TEMPLATE' && $style_id = $this->getRequestParameter('blocklist_style_id')){
                    // Associate template with blocklist style:
                    $style_id = $this->getRequestParameter('blocklist_style_id');
                    $style = new SmartestBlockListStyle;
                    if($style->find($style_id)){
                        $style->addBlockTemplate($new_template);
                        $this->redirect('@blocklists:edit_blocklist_style?style_id='.$style->getId());
                    }
                }
    	        
    	    }
		
	    }else{
	        
	        $this->addUserMessageToNextRequest('The given template type code was not recognized', SmartestUserMessage::ERROR);
	        
	    }
		
		$this->formForward();
	}
			
	public function editTemplate($get){
		
		$template_type = $this->getRequestParameter('type');
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('asset_type');
	    $template_id = $this->getRequestParameter('template');
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
                $this->send(strpos($template->getContent(), '<!--{{SMARTEST_LAYOUT_BUILDER}}-->') !== false, 'layout_builder_cta');
	            $this->send($template, 'template');
	            $this->send($template->getContentForEditor(), "template_content");
	            $show_form = true;
	            $this->send($type, 'type');
	            $this->send('asset', 'edit_type');
	            $location = $template->getStorageLocation();
	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        		$title = 'Edit '.$type['label'];
        		$this->send($type, 'type_info');
        		$this->send($template->IsConvertable(), 'is_convertable');
        		$this->send($template->getImportedStylesheets(), 'stylesheets');
        		$template->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
        		$this->getUser()->addRecentlyEditedTemplateById($template_id, $this->getSIte()->getId());
        		$this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), $template->getType()), 'recently_edited');
                $this->send($template->getMentionedCSSTokens(), 'tokens');
                
	        }else{
	            $this->addUserMessage("The template ID was not recognized");
	            $show_form = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	        if($template->getModelId() == 0 && isset($type['model_specific']) && $type['model_specific'] != 'never'){
	            
	            $show_suggested_models = false;
	            
    	        // look for repeats - these are conclusive
    	        if(preg_match_all('/<\?sm:repeat from="([\w_]+)"/i', $template->getContent(), $repeat_matches)){
	            
    	            $models = array();
    	            $model_ids = array();
	            
    	            foreach($repeat_matches[1] as $set_name){
    	                $s = new SmartestCmsItemSet;
    	                if($s->findBy('name', $set_name)){
    	                    if(!in_array($s->getModelId(), $model_ids)){
    	                        $models[] = $s->getModel();
    	                        $model_ids[] = $s->getModelId();
                            }
    	                }
    	            }
    	            
    	            if(count($models)){
	                    $show_suggested_models = true;
	                    $this->send(new SmartestArray($models), 'suggested_models');
                    }
	            
    	        }else if(preg_match_all('/(\$(this\.principal_item|repeated_item|item)\.|property name=")([\w_]+)/', $template->getContent(), $var_matches)){ // look for variables - these are suggestive
    	            
    	            $found_property_names = array_values(array_unique($var_matches[3]));
    	            $database = SmartestDatabase::getInstance('SMARTEST');
    	            $sql = "SELECT DISTINCT ItemClasses.* FROM ItemClasses, ItemProperties WHERE ItemProperties.itemproperty_itemclass_id=ItemClasses.itemclass_id AND ItemProperties.itemproperty_varname IN ('".implode("','", $found_property_names)."') AND (ItemClasses.itemclass_site_id='".$this->getSite()->getId()."' OR ItemClasses.itemclass_shared='1')";
    	            $result = $database->queryToArray($sql);
    	            $models = array();
    	            
    	            foreach($result as $r){
    	                $m = new SmartestModel;
    	                $m->hydrate($r);
    	                $models[] = $m;
    	            }
    	            
    	            if(count($result)){
	                    $show_suggested_models = true;
	                    $this->send(new SmartestArray($models), 'suggested_models');
                    }
    	            
    	        }
    	        
    	        $this->send($show_suggested_models, 'show_suggested_models');
	        
            }else{
                
                $model = new SmartestModel;
                
                if($model->find($template->getModelId())){
                    $this->send($model, 'model');
                }
                
                $this->send(false, 'show_suggested_models');
            }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessage($message, SmartestUserMessage::ERROR);
         	        $show_form = false;
         	    }else{
         	        $path = realpath(SM_ROOT_DIR.$location.$template_id);
         	        if(SmartestFileSystemHelper::isSafeFileName($path, SM_ROOT_DIR.$location)){
         	            $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
             	        $this->send($template, 'template');
             	        $this->send($template->getContentForEditor(), 'template_content');
             	        $location = $template->getStorageLocation();
        	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
        	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        	            $this->send('file', 'edit_type');
             	        $show_form = true;
             	        $this->send(false, 'is_convertable');
                        $this->send($template->getMentionedCSSTokens(), 'tokens');
     	            }else{
     	                $show_form = false;
     	                $this->send(false, 'is_convertable');
     	                $this->addUserMessage("You are not allowed to edit that file. This incident has been logged.", SmartestUserMessage::ACCESS_DENIED);
     	            }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	        }
	        
	    }
	    
	    $is_editable = $show_form && $dir_is_writable && $file_is_writable;
        
	    $this->send($dir_is_writable, 'dir_is_writable');
		$this->send($file_is_writable, 'file_is_writable');
	    $this->send($title, "interface_title");
	    $this->send($show_form, "show_form");
	    $this->send($is_editable, "is_editable");

	}
    
	public function updateTemplate($get, $post){
		
		$h = new SmartestTemplatesLibraryHelper;
		$edit_type = $this->getRequestParameter('edit_type');
		
		if($edit_type == 'imported'){
	        
	        $template_id = (int) $this->getRequestParameter('template_id');
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            
	            if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
	                $allow_update = true;
	                $this->addUserMessageToNextRequest("The template has been successfully updated.", SmartestUserMessage::SUCCESS);
                }else{
                    $allow_update = false;
                    $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING, true);
                }
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	            $allow_update = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        $filename = $this->getRequestParameter('filename');
            $type_code = $this->getRequestParameter('type');
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);
    	        
         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_update = false;
         	        
         	    }else{
         	        
         	        // echo SM_ROOT_DIR.$location.$filename.' ';
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$filename);
         	        
         	        // echo $template->getStorageLocation(true).' ';
         	        
         	        if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
    	                $allow_update = true;
                    }else{
                        $allow_update = false;
                        $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING);
                    }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_update = false;
 	        }
	        
	    }
	    
	    if($allow_update){
	        
	        $content = $this->getRequestParameter('template_content');

    		if(substr($content, 0, 9) == '<![CDATA['){
    		    $content = substr($content, 9);
    		}

    		if(substr($content, -3) == ']]>'){
    		    $content = substr($content, 0, -3);
    		}

    		$template_content = stripslashes($content);
    		
    		SmartestFileSystemHelper::save($template->getFullPathOnDisk(), $template_content, true);
    		
    		SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} made a change to template '{$template->getUrl()}'.", SmartestLog::USER_ACTION);
    		
    		if($edit_type == 'imported'){
    		    $template->setModified(time());
    		    $template->save();
    		}
	        
	    }
	    
	    if($this->getRequestParameter('_submit_action') == "continue"){
	        if($edit_type == 'imported'){
	            $url_id = $template->getId();
	        }else{
	            $url_id = $this->getRequestParameter('filename');
	        }
	        $this->redirect("/templates/editTemplate?asset_type=".$type_code."&template=".$url_id);
	    }else{
	        // $this->addUserMessageToNextRequest("The template was successfully saved.", SmartestUserMessage::SUCCESS);
	        $this->formForward();
	    }
		
		/* $data = $post;
		unset($data['template_content']);
		print_r($data); */
		
		/* $template_type = $this->getRequestParameter('type');
		
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $this->getRequestParameter('filename');
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $this->getRequestParameter('filename');
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$template = new SmartestAsset;
			
			if($template->hydrate($this->getRequestParameter('template_id'))){
    		    $template_name = $template->getUrl();
    		}
			
		}
		
		$content = $this->getRequestParameter('template_content');
		
		if(substr($content, 0, 9) == '<![CDATA['){
		    $content = substr($content, 9);
		}
		
		if(substr($content, -3) == ']]>'){
		    $content = substr($content, 0, -3);
		}
		
		$template_content = stripslashes($content);
		
		$file = $path.$template_name;
		
		if(SmartestFileSystemHelper::save($file, $template_content, true)){
			$this->setFormReturnVar('success', 'true');
			$this->addUserMessageToNextRequest('Your changes were saved successfully.');
		}else{
			$this->setFormReturnVar('success', 'failed');
			$this->addUserMessageToNextRequest('Couldn\'t save changes. Check file permissions.');
		}
  		
  		$this->formForward(); */
  		
	}
    
	public function editTemplateModal(){
		
		$template_type = $this->getRequestParameter('type');
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('template_type');
	    $template_id = $this->getRequestParameter('template');
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
                
	            $type = $template->getTypeInfo();
                $type_code = $template->getType();
                // $this->send(strpos($template->getContent(), '<!--{{SMARTEST_LAYOUT_BUILDER}}-->') !== false, 'layout_builder_cta');
	            $this->send($template, 'template');
                $contents = $template->getContentForEditor();
                $contents = strlen($contents) ? $contents : '&lt;!-- Blank template --&gt;';
	            $this->send($contents, "editor_contents");
                $show_form = true;
	            $this->send($type, 'type');
	            $this->send('asset', 'edit_type');
	            $location = $template->getStorageLocation();
	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        		$title = 'Edit '.$type['label'];
        		$this->send($type, 'type_info');
                $this->send(true, 'show_editor');
        		// $this->send($template->IsConvertable(), 'is_convertable');
        		// $this->send($template->getImportedStylesheets(), 'stylesheets');
        		// $template->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
        		$this->getUser()->addRecentlyEditedTemplateById($template_id, $this->getSIte()->getId());
        		// $this->send($this->getUser()->getRecentlyEditedTemplates($this->getSite()->getId(), $template->getType()), 'recently_edited');
                
	        }else{
	            // $this->addUserMessage("The template ID was not recognized");
	            $show_form = false;
	            $title = 'Edit '.$type['label'];
                $this->send(false, 'show_editor');
                $this->send("The template ID was not recognized.", 'message');
	        }
            
            $this->send(false, 'show_suggested_models');
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessage($message, SmartestUserMessage::ERROR);
         	        $show_form = false;
                    $this->send(false, 'show_editor');
         	    }else{
         	        $path = realpath(SM_ROOT_DIR.$location.$template_id);
         	        if(SmartestFileSystemHelper::isSafeFileName($path, SM_ROOT_DIR.$location)){
         	            $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
             	        $this->send($template, 'template');
                        $contents = $template->getContentForEditor();
                        $contents = strlen($contents) ? $contents : '&lt;!-- Blank template --&gt;';
	                    $this->send($contents, "editor_contents");
             	        $location = $template->getStorageLocation();
        	            $dir_is_writable = is_writable(SM_ROOT_DIR.$location);
        	            $file_is_writable = is_writable(SM_ROOT_DIR.$location.$template->getUrl());
        	            $this->send('file', 'edit_type');
             	        $show_form = true;
             	        $this->send(false, 'is_convertable');
                        $this->send(true, 'show_editor');
     	            }else{
     	                $show_form = false;
                        $this->send(false, 'show_editor');
     	                $this->send("You are not allowed to edit that file. This incident has been logged.", 'message');
     	                // $this->addUserMessage("You are not allowed to edit that file. This incident has been logged.", SmartestUserMessage::ACCESS_DENIED);
     	            }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	        }
	        
	    }
        
        if($this->requestParameterIsSet('from')){
            $this->send($this->getRequestParameter('from'), 'from');
        }
        
        if($this->requestParameterIsSet('page_id')){
            $page = new SmartestPage;
            if($page->smartFind($this->getRequestParameter('page_id'))){
                $this->send($page, 'page');
            }
        }
        
        if($this->requestParameterIsSet('item_id')){
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                $this->send($item, 'item');
            }
        }
	    
	    $is_editable = $show_form && $dir_is_writable && $file_is_writable;
        
	    $this->send($dir_is_writable, 'dir_is_writable');
		$this->send($file_is_writable, 'file_is_writable');
	    $this->send($show_form, "show_form");
	    $this->send($is_editable, "is_editable");
        $this->send(SmartestStringHelper::randomFromFormat('RRRR'), 'random_nonce');

	}
    
    public function buildLayout(){
        
    }
	
	public function updateTemplateInfo(){
	    
	    $template = new SmartestTemplateAsset;
	    
	    if($template->find($this->getRequestParameter('template_id'))){
	        
	        $template->setLabel($this->getRequestParameter('template_label'));
	        $template->setUserId($this->getRequestParameter('template_user_id'));
	        $template->setShared($this->getRequestParameter('template_shared') ? 1 : 0);
	        $template->setModelId($this->getRequestParameter('template_model_id', 0));
	        $template->setLanguage($this->getRequestParameter('template_language'));
	        $template->save();
	        
	        $this->addUserMessageToNextRequest("The template has been updated.", SmartestUserMessage::SUCCESS);
	        
	        if($this->getRequestParameter('_submit_action') == "continue"){
    	        $this->redirect("/templates/templateInfo?template=".$template->getId());
    	    }else{
    	        $this->formForward();
    	    }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function pairTemplateWithModelOneClick(){
	    
	    $template = new SmartestTemplateAsset;
	    
	    if($template->find($this->getRequestParameter('template_id'))){
	        $du = new SmartestDataUtility;
	        if(in_array($this->getRequestParameter('model_id'), $du->getModelIds($this->getSite()->getId()))){
	            $model = new SmartestModel;
	            if($model->find($this->getRequestParameter('model_id'))){
	                $template->setModelId($this->getRequestParameter('model_id'));
	                $template->save();
	                $this->addUserMessageToNextRequest('The template has now been paired with the '.strtolower($model->getName()).' model.', SmartestUserMessage::SUCCESS);
                }else{
                    $this->addUserMessageToNextRequest('The specified model ID was not found.', SmartestUserMessage::ERROR);
                }
	        }else{
	            $this->addUserMessageToNextRequest('The specified model ID is not in use.', SmartestUserMessage::WARNING);
	        }
	        
	        $this->redirect('/templates/editTemplate?template='.$this->getRequestParameter('template_id'));
	        
	    }else{
	        $this->addUserMessageToNextRequest('The specified template ID was not found.', SmartestUserMessage::ERROR);
	    }
	    
	    $this->redirect('/smartest/templates');
	    
	}
	
	public function hideTemplateModelPairingMessage(){
	    
	    $template = new SmartestTemplateAsset;
	    
	    if($template->find($this->getRequestParameter('template_id'))){
	        $template->setModelId('-1');
            $template->save();
            $this->redirect('/templates/editTemplate?template='.$this->getRequestParameter('template_id'));
	    }else{
	        $this->addUserMessageToNextRequest('The specified template ID was not found.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/templates');
	    }
	    
	}
	
	public function templateInfo($get){
	    
	    $template_id = $this->getRequestParameter('template');
	    
	    $template = new SmartestTemplateAsset;

		if($template->find($template_id)){
		    
		    $this->send($template->getGroups(), 'groups');
		    $this->send($template->getPossibleGroups(), 'possible_groups');
		    
		    $this->send($template->getPossibleOwners(), 'potential_owners');
		    $this->send($template->getTypeInfo(), 'template_type');
		    $this->send($template, 'template');
		    
		    $du = new SmartestDataUtility;
	        $this->send($du->getModels(false, $this->getSite()->getId()), 'models');
		    
		}
	    
	}

	public function deleteTemplate($get){
	    
	    $template_type = $this->getRequestParameter('type');
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('asset_type');
	    $template_id = $this->getRequestParameter('template');
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_delete = true;
	            $location = $template->getStorageLocation();
	            $delete_type = 'imported';
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_delete = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_delete = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        // $location = $template->getStorageLocation();
    	            $allow_delete = true;
    	            $delete_type = 'file';
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_delete = false;
 	        }
	        
	    }
		
		if($allow_delete){
		
		    $new_file = SmartestFileSystemHelper::getUniqueFileName($template->getFullPathOnDisk());
		    
		    SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} deleted template '{$template->getLabel()}'.", SmartestLog::USER_ACTION);
		    
		    if($template->delete()){
                $this->addUserMessageToNextRequest('The template was deleted successfully.', SmartestUserMessage::SUCCESS);
    		}else{
    			$this->addUserMessageToNextRequest('Couldn\'t move template to trash. Please check file permissions.', SmartestUserMessage::WARNING);
    		}
		
	    }
		
		$this->formForward();
			
		/* $template_type = $this->getRequestParameter('type');
			
		if($template_type == 'SM_PAGE_MASTER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Masters/";
			$template_name = $this->getRequestParameter('template_name');
			
		}else if($template_type=='SM_LIST_ITEM_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/ListItems/";
			$template_name = $this->getRequestParameter('template_name');
			
		}else if($template_type=='SM_CONTAINER_TEMPLATE'){
			
			$path = SM_ROOT_DIR."Presentation/Layouts/";
			$asset = new SmartestAsset;
			$asset->hydrate($this->getRequestParameter('template_id'));
			$template_name = $asset->getUrl();
			
		}
		
		$old_filename = $path.$template_name;
		$new_filename = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Documents/Deleted/'.$template_name);
		
		// make sure the file, which has been passed via the url, isn't outside of $path.
		if(SmartestFileSystemHelper::isSafeFileName($old_filename, $path)){
		    
		    //// add bit in here that does the database work for container templates
		    if($template_type == 'SM_CONTAINER_TEMPLATE'){

    			if($asset->delete()){
    			    
    			    $this->addUserMessageToNextRequest('The template was successfully deleted.');
        			$this->setFormReturnVar('deletedTemplate', 'true');
    			}else{
    			    
    			    $this->addUserMessageToNextRequest('The template could not be deleted. Please check your file permissions.');
    			    $this->setFormReturnVar('deletedTemplate', 'failed');
    			}

    		}else{
    		    
    		    if(rename($old_filename, $new_filename)){

    		        $this->addUserMessageToNextRequest('The template was successfully deleted.');
        			$this->setFormReturnVar('deletedTemplate', 'true');

    		    }else{
    		        
    		        $this->addUserMessageToNextRequest('The template could not be deleted. Please check your file permissions.');
    			    $this->setFormReturnVar('deletedTemplate', 'failed');
        			
    		    }
    		    
    		}
		    
		    
			
		}else{
		    
		    $this->addUserMessageToNextRequest('The file you are trying to delete is outside the current editing scope.');
		    $this->setFormReturnVar('deletedTemplate', 'failed');
		    
		}
			
		$this->formForward(); */
	}
	
	
	
	
	function duplicateTemplate($get){
		
		$template_type = $this->getRequestParameter('type');
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('asset_type');
	    $template_id = $this->getRequestParameter('template');
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_copy = true;
	            $location = $template->getStorageLocation();
	            $copy_type = 'imported';
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_copy = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_copy = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        // $location = $template->getStorageLocation();
    	            $allow_copy = true;
    	            $copy_type = 'file';
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_copy = false;
 	        }
	        
	    }
		
		if($allow_copy){
		
		    $new_file = SmartestFileSystemHelper::getUniqueFileName($template->getFullPathOnDisk());
		    
		    if(copy($template->getFullPathOnDisk(), $new_file)){
                
                if($copy_type == 'imported'){
                    
                    $new_asset = new SmartestTemplateAsset;
                    $new_asset->setType($template->getType());
        		    $new_asset->setStringId(SmartestStringHelper::toVarName(basename($new_file)));
    			    $new_asset->setUrl(basename($new_file));
    			    $new_asset->setWebid(SmartestStringHelper::random(32));
    			    $new_asset->setCreated(time());
    			    $new_asset->setUserId($this->getUser()->getId());
    			    $new_asset->setSiteId($this->getSite()->getId());
    			    $new_asset->save();
    			    
			    }

    			$this->addUserMessageToNextRequest('Your new copy was created successfully as '.basename($new_file).'.', SmartestUserMessage::SUCCESS);
    			SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} duplicated template '{$template->getLabel()}' to new file ".basename($new_file).".", SmartestLog::USER_ACTION);

    		}else{
    			$this->addUserMessageToNextRequest('Couldn\'t create new copy. Please check file permissions.', SmartestUserMessage::WARNING);
    		}
		
	    }
		
		$this->formForward();
		
	}
	
	function downloadTemplate($get){
		
		$template_type = $this->getRequestParameter('type');
		
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('asset_type');
	    $template_id = $this->getRequestParameter('template');
	    
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            $allow_download = true;
	            $location = $template->getStorageLocation();
	        }else{
	            $this->addUserMessageToNextRequest("The template ID was not recognized");
	            $allow_download = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_download = false;
         	    }else{
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        $location = $template->getStorageLocation();
    	            $allow_download = true;
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_download = false;
 	        }
	        
	    }
		
		if($allow_download){
		
		    $ua = $this->getUserAgent()->getAppName();
		
    		if($ua == 'Explorer' || $ua == 'Opera'){
    		    $mime_type = 'application/octetstream';
    		}else{
    		    $mime_type = 'application/octet-stream';
    		}
		
    		$download = new SmartestDownloadHelper($template->getFullPathOnDisk());
    		$download->setMimeType($mime_type);
    		$download->send();
		
	    }else{
	        
	        $this->formForward();
	        
	    }
		
	}
}