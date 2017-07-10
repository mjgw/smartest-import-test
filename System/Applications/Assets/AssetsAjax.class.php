<?php

class AssetsAjax extends SmartestSystemApplication{
    
    public function fullTextSearchResults(){
        
        $db = SmartestDatabase::getInstance('SMARTEST');
        $q = $this->getRequestParameter('query');
        $sql = "SELECT Assets.* FROM Assets, TextFragments WHERE TextFragments.textfragment_asset_id=Assets.asset_id AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND Assets.asset_deleted=0 AND (Assets.asset_stringid LIKE '%".$q."%' OR Assets.asset_label LIKE '%".$q."%' OR (TextFragments.textfragment_content LIKE '%".$q."%')) ORDER BY Assets.asset_label LIMIT 150";
        $assets = array();
        
        if(strlen($q) > 2){
            
            $result = $db->queryToArray($sql);
            
            foreach($result as $r){
                $a = new SmartestAsset;
                $a->hydrate($r);
                $assets[] = $a;
            }
            
            $this->send($assets, 'assets');
            
        }
        
        exit;
        
    }
    
    public function assetSearch(){
	    
	    $db = SmartestDatabase::getInstance('SMARTEST');
	    $q = $this->getRequestParameter('query');
	    // $sql = "SELECT Assets.* FROM Assets, TextFragments, Tags, TagsObjectsLookup WHERE (asset_site_id='".$this->getSite()->getId()."' OR asset_shared=1) AND asset_deleted='0' AND (Assets.asset_stringid LIKE '%".$q."%' OR Assets.asset_label LIKE '%".$q."%' OR (TextFragments.textfragment_content LIKE '%".$q."%' AND (TextFragments.textfragment_asset_id=Assets.asset_id OR Assets.asset_fragment_id=TextFragments.textfragment_id) OR (Tags.tag_label LIKE '%".$q."%' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id=Assets.asset_id AND TagsObjectsLookup.taglookup_type='SM_ASSET_TAG_LINK'))) ORDER BY Assets.asset_label";
	    // $result = $db->queryToArray($sql);
        
        if($this->requestParameterIsSet('limit')){
            
            $limit_file_types = array();
            $separated = explode(',', $this->getRequestParameter('limit'));
            $alh = new SmartestAssetsLibraryHelper;
            
            if(count($separated)){
                foreach($separated as $pft){
                    if(substr($pft, 0, 12) == 'SM_ASSETTYPE'){
                        $limit_file_types[] = $pft;
                    }elseif(substr($pft, 0, 12) == 'SM_ASSETCLAS'){
                        $ach = new SmartestAssetClassesHelper;
                        $unpacked_asset_type_codes = $ach->getAssetTypeCodesFromAssetClassType($pft);
                        if(is_array($unpacked_asset_type_codes)){
                            foreach($unpacked_asset_type_codes as $c){
                                $limit_file_types[] = $c;
                            }
                        }
                    }elseif($alh->isValidCategoryShortName($pft)){
                        $unpacked_asset_type_codes = $alh->getAssetTypeCodesInCategory($pft);
                        if(is_array($unpacked_asset_type_codes)){
                            foreach($unpacked_asset_type_codes as $c){
                                $limit_file_types[] = $c;
                            }
                        }
                    }
                }
            }
            
            $limit_file_types = array_unique($limit_file_types);
            
        }else{
            $limit_file_types = array();
        }
        
        if(count($limit_file_types)){
            if(count($limit_file_types) > 1){
                if($this->getRequestParameter('limitType') == 'exclude'){
                    $sql_filetype_insert = " AND asset_type NOT IN ('".implode("','", $limit_file_types)."')";
                }else{
                    $sql_filetype_insert = " AND asset_type IN ('".implode("','", $limit_file_types)."')";
                }
            }else{
                if($this->getRequestParameter('limitType') == 'exclude'){
                    $sql_filetype_insert = " AND asset_type != '".$limit_file_types[0]."'";
                }else{
                    $sql_filetype_insert = " AND asset_type = '".$limit_file_types[0]."'";
                }
            }
        }else{
            $sql_filetype_insert = '';
        }
        
	    $sql1 = "SELECT Assets.asset_id FROM Assets, TextFragments WHERE TextFragments.textfragment_asset_id=Assets.asset_id AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND Assets.asset_deleted=0 AND (TextFragments.textfragment_content LIKE '%".$q."%')".$sql_filetype_insert." ORDER BY Assets.asset_label LIMIT 150";
	    $result1 = $db->queryToArray($sql1);
	    
	    $sql2 = "SELECT Assets.asset_id FROM Assets, Tags, TagsObjectsLookup WHERE Assets.asset_deleted=0 AND (Assets.asset_site_id='".$this->getSite()->getId()."' OR Assets.asset_shared='1') AND (Tags.tag_label LIKE '%".$q."%' AND TagsObjectsLookup.taglookup_tag_id=Tags.tag_id AND TagsObjectsLookup.taglookup_object_id=Assets.asset_id AND TagsObjectsLookup.taglookup_type='SM_ASSET_TAG_LINK')".$sql_filetype_insert." LIMIT 150";
	    $result2 = $db->queryToArray($sql2);
	    
	    $sql3 = "SELECT Assets.asset_id FROM Assets WHERE Assets.asset_deleted=0 AND (Assets.asset_site_id='{$this->getSite()->getId()}' OR Assets.asset_shared='1') AND (Assets.asset_stringid LIKE '%{$q}%' OR Assets.asset_label LIKE '%{$q}%')".$sql_filetype_insert;
	    $result3 = $db->queryToArray($sql3);
	    
	    $asset_ids = array();
	    
	    foreach($result1 as $r){
	        $asset_ids[] = $r['asset_id'];
	    }
	    
	    foreach($result2 as $r){
	        $asset_ids[] = $r['asset_id'];
	    }
	    
	    foreach($result3 as $r){
	        $asset_ids[] = $r['asset_id'];
	    }
	    
	    $asset_ids = array_unique($asset_ids);
	    
	    $final_sql = "SELECT Assets.* FROM Assets WHERE Assets.asset_id IN ('".implode("','", $asset_ids)."')";
        $final_sql .= " ORDER BY asset_label ASC";
	    $result = $db->queryToArray($final_sql);
	    $assets = array();
	    
	    foreach($result as $r){
	        $asset = new SmartestAsset;
	        $asset->hydrate($r);
	        $assets[] = $asset;
	    }
	    
	    $this->send($assets, 'assets');
	    
	}
    
    public function setAssetLabelFromInPlaceEditField(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setLabel($this->getRequestParameter('new_label'));
	        $asset->save();
	        echo $this->getRequestParameter('new_label');
	        exit;
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        exit;
	        
	    }
        
    }
    
    public function setAssetOwnerById(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setUserId($this->getRequestParameter('owner_id'));
	        SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' set the owner id of file '.$asset->getLabel().' to'.$this->getRequestParameter('owner_id').'.');
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function setAssetThumbnailId(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setThumbnailId($this->getRequestParameter('thumbnail_id'));
	        SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' set the thumbnail asset id of file '.$asset->getLabel().' to'.$this->getRequestParameter('thumbnail_id').'.');
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function setAssetLanguage(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setLanguage($this->getRequestParameter('asset_language'));
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function setAssetShared(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        header('HTTP/1.1 200 OK');
	        $asset->setShared($this->getRequestParameter('is_shared'));
	        
	        if($this->getRequestParameter('is_shared')){
	            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' shared file '.$asset->getLabel().' with other sites.');
	        }else{
	            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' made file '.$asset->getLabel().' no longer with other sites.');
	        }
	        
	        $asset->save();
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function assetComments(){
        
        $asset = new SmartestAsset;
        $asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
		    
		    $this->send($asset, 'asset');
		    $comments = $asset->getComments();
		    $this->send($comments, 'comments');
		    header('HTTP/1.1 200 OK');
		
		}
        
    }
    
    public function submitAssetComment(){
        
        $asset = new SmartestAsset;
		$asset_id = $this->getRequestParameter('asset_id');

		if($asset->find($asset_id)){
            
            $asset->addComment($this->getRequestParameter('comment_content'), $this->getUser()->getId());
            SmartestLog::getInstance('site')->log($this->getUser()->getFullName().' left a comment on file '.$asset->getLabel().'.');
            header('HTTP/1.1 200 OK');

		}
		
		exit;
		
    }
    
    public function removeAssetComment(){
        
        $comment = new SmartestAssetComment;
        
        if($comment->find($this->getRequestParameter('comment_id'))){
            $comment->delete();
        }
        
    }
    
    public function assetInfoJson(){
        
        $asset = new SmartestAsset;
        
        if($asset->find($this->getRequestParameter('asset_id'))){
            
            /* $obj = new stdClass;
            
            if($asset->isBinaryImage()){
                $obj->width = $asset->getWidth();
                $obj->height = $asset->getHeight();
                $obj->is_image = true;
            }else{
                $obj->width = $asset->getWidth();
                $obj->is_image = false;
            }
            
            $obj->type = $asset->getType(); */
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($asset->__toArray());
            exit;
            
        }
        
    }
    
    public function updateGalleryOrder(){
        
        $group = new SmartestAssetGroup;
        
        if($group->find($this->getRequestParameter('group_id'))){
            if($group->getIsGallery()){
                if($this->getRequestParameter('new_order')){
                    $group->setNewOrderFromString($this->getRequestParameter('new_order'));
                    // echo "proceed";
                    header('HTTP/1.1 200 OK');
                }
            }
        }
        
        exit;
        
    }
    
    public function tagAsset(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        if($asset->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function unTagAsset(){
        
        $asset = new SmartestAsset;
	    
	    if($asset->find($this->getRequestParameter('asset_id'))){
	        
	        if($asset->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	    exit;
        
    }
    
    public function updateAssetGroupMembershipCaption(){
        
        $membership = new SmartestAssetGalleryMembership;
        
        if($membership->find($this->getRequestParameter('membership_id'))){
            
            $membership->setCaption(strip_tags($this->getRequestParameter('new_caption')));
            $membership->save();
            echo strip_tags($this->getRequestParameter('new_caption'));
            exit;
            
        }else{
            
            echo "Gallery membership not found";
            exit;
            
        }
        
    }
    
    public function deleteAssetGroupMembership(){
        
        $membership = new SmartestAssetGalleryMembership;
        
        if($membership->find($this->getRequestParameter('membership_id'))){
            
            $membership->setCaption(strip_tags($this->getRequestParameter('membership_id')));
            $membership->delete();
            exit;
            
        }
        
    }
    
    public function getAssetInfoJsonForAttachmentForm(){
        
        $asset = new SmartestAsset;
        
        if($asset->find($this->getRequestParameter('attached_file_id'))){
            
            $obj = new stdClass;
            
            if($asset->isBinaryImage()){
                $obj->width = $asset->getWidth();
                $obj->height = $asset->getHeight();
                $obj->is_image = true;
            }else{
                $obj->width = $asset->getWidth();
                $obj->is_image = false;
            }
            
            $obj->label = $asset->getLabel();
            $obj->type = $asset->getType();
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($obj);
            exit;
            
        }
        
    }
    
    public function getReplacementThumbnailForMiniImageBrowser(){
        
        $asset = new SmartestAsset;
        
        if($this->requestParameterIsSet('asset_id')){
            
            if($asset->find($this->getRequestParameter('asset_id'))){
                
                $this->send(true, 'found_asset');
                $this->send($asset, 'asset');
                
                if($this->requestParameterIsSet('for')){
                    $this->send(SmartestStringHelper::toVarName($this->getRequestParameter('for')), 'for');
                }
                
            }else{
                $this->send(false, 'found_asset');
            }
            
        }else{
            $this->send(false, 'found_asset');
        }
        
        if($this->requestParameterIsSet('input_id')){
            $this->send(strip_tags($this->getRequestParameter('input_id')), 'input_id');
        }else{
            $this->send(SmartestStringHelper::randomFromFormat('LlLlLlLl'), 'input_id');
        }
        
    }
    
    public function uploadNewImageFromMiniImageBrowser(){
        
        if(SmartestUploadHelper::uploadExists('asset_file')){
            
            if($this->getUser()->hasToken('create_assets')){
                
                $suffix = SmartestUploadHelper::getUnsavedUploadDotSuffix('asset_file');
                $alh = new SmartestAssetsLibraryHelper();
                
                if($asset_type = $alh->getTypeCodeBySuffix($suffix)){
                    
                    $ach = new SmartestAssetCreationHelper($asset_type);
                
    	            $upload = new SmartestUploadHelper('asset_file');
                    $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
                    // creates a new unsaved asset from the file upload
                    $ach->createNewAssetFromFileUpload($upload, $this->getRequestParameter('asset_label'));
                    $asset = $ach->finish();
                    
                    if(strlen($this->getRequestParameter('asset_credit'))){
                        // throw new SmartestException($this->getRequestParameter('asset_credit'));
                        $asset->setCredit($this->getRequestParameter('asset_credit'));
                    }
                    
                    $assetSimpleObj = new stdClass;
                    $assetSimpleObj->asset_id = $asset->getId();
                    $assetSimpleObj->asset_webid = $asset->getWebId();
                    $assetSimpleObj->asset_label = $asset->getLabel();
                    $assetSimpleObj->asset_url = $asset->getUrl();
                    
                    $asset->setSiteId($this->getSite()->getId());
                    $asset->save();
                    
                }
                
                if($this->requestParameterIsSet('for')){
                    
                    $assetSimpleObj->for = $this->getRequestParameter('for');
                    
                    switch($this->getRequestParameter('for')){
                        
                        case 'ipv':
                        
                        if($property_id = $this->getRequestParameter('property_id')){
                            
                            $assetSimpleObj->property_id = $this->getRequestParameter('property_id');
                            
                            $property = new SmartestItemProperty;
                            if($property->find($property_id)){
                                // Property exists, so that's good
                                if($property->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
                                    $group = new SmartestAssetGroup;
                                    if($group->find($property->getOptionSetId())){
                                        $group->addAssetById($asset->getId(), false);
                                    }
                                }
                            }
                        }
                        
                        break;
                        
                        case 'placeholder':
                        
                        if($placeholder_id = $this->getRequestParameter('placeholder_id')){
                            
                            $assetSimpleObj->placeholder_id = $this->getRequestParameter('placeholder_id');
                            
                            $placeholder = new SmartestPlaceholder;
                            if($placeholder->find($placeholder_id)){
                                // Placeholder exists, so that's good
                                if($placeholder->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_ASSETGROUP'){
                                    $group = new SmartestAssetGroup;
                                    if($group->find($placeholder->getFilterValue())){
                                        $group->addAssetById($asset->getId(), false);
                                    }
                                }
                            }
                        }
                        
                        case 'user_profile_pic':
                        
                        if($user_id = $this->getRequestParameter('user_id')){
                            
                            $assetSimpleObj->user_id = $this->getRequestParameter('user_id');
                            
                            $user = new SmartestSystemUser;
                            
                            if($user->find($user_id)){
                                
                                // $user->setProfilePicAssetId($asset->getId());
                                // $user->save();
                                
                                $asset->setSiteId($this->getSite()->getId());
                                $asset->setShared(1);
                                $asset->setIsSystem(1);
                                $asset->setIsHidden(1);
                                $asset->setUserId($user->getId());
                                $asset->save();
                                
                                $uh = new SmartestUsersHelper;

                    		    if($g = $uh->getUserProfilePicsGroup()){
                                    $g->addAssetById($asset->getId(), true);
                                }
                            }
                        }
                        
                        break;
                        
                    }
                    
                }
                
                header('Content-type: application/javascript');
                echo json_encode($assetSimpleObj);
                exit;
                
            }
            
        }
        
    }
    
    public function uploadNewFileViaBrowserAjaxRequest(){
        
        if(SmartestUploadHelper::uploadExists('new_file')){
            
            if($this->getUser()->hasToken('create_assets')){
                
                $asset_type = $this->getRequestParameter('asset_type');
                
                if(in_array($asset_type, SmartestDataUtility::getAssetTypeCodes())){
                    $ach = new SmartestAssetCreationHelper($asset_type);
                    $upload = new SmartestUploadHelper('new_file');
                    $upload->setUploadDirectory(SM_ROOT_DIR.'System/Temporary/');
                    $ach->createNewAssetFromFileUpload($upload, $this->getRequestParameter('asset_label'));
                    $asset = $ach->finish();
                    
                    $assetSimpleObj = new stdClass;
                    $assetSimpleObj->asset_id = $asset->getId();
                    $assetSimpleObj->asset_webid = $asset->getWebId();
                    $assetSimpleObj->asset_label = $asset->getLabel();
                    $assetSimpleObj->asset_url = $asset->getUrl();
                    
                    $asset->setSiteId($this->getSite()->getId());
                    $asset->save();
                    
                    if($this->requestParameterIsSet('for')){
                    
                        $assetSimpleObj->for = $this->getRequestParameter('for');
                    
                        switch($this->getRequestParameter('for')){
                        
                            case 'ipv':
                        
                            if($property_id = $this->getRequestParameter('property_id')){
                                // Property exists, so that's good
                                
                                $assetSimpleObj->property_id = $this->getRequestParameter('property_id');
                            
                                $property = new SmartestItemProperty;
                                if($property->find($property_id)){
                                    
                                    // If an item is specified, set the new asset as the value for this property on the specified item
                                    if($this->requestParameterIsSet('item_id')){
                                        if($item = SmartestCmsItem::retrieveByPk((int) $this->getRequestParameter('item_id'))){
                                            $item->setPropertyValueByNumericKey($property_id, $asset->getId());
                                            $item->save();
                                        }
                                    }
                                        
                                    // If the property is limited to a grou, add the file to that group
                                    if($property->getOptionSetType() == 'SM_PROPERTY_FILTERTYPE_ASSETGROUP'){
                                        $group = new SmartestAssetGroup;
                                        if($group->find($property->getOptionSetId())){
                                            $group->addAssetById($asset->getId(), false);
                                        }
                                    }
                                }
                            }
                        
                            break;
                        
                            case 'placeholder':
                        
                            if($placeholder_id = (int) $this->getRequestParameter('placeholder_id')){
                            
                                $assetSimpleObj->placeholder_id = $this->getRequestParameter('placeholder_id');
                            
                                $placeholder = new SmartestPlaceholder;
                                if($placeholder->find($placeholder_id)){
                                    
                                    // TODO: If a page is specified, set this asset as the value for the given placeholder
                                    
                                    // Placeholder exists, so that's good
                                    if($placeholder->getFilterType() == 'SM_ASSETCLASS_FILTERTYPE_ASSETGROUP'){
                                        $group = new SmartestAssetGroup;
                                        if($group->find($placeholder->getFilterValue())){
                                            $group->addAssetById($asset->getId(), false);
                                        }
                                    }
                                }
                            }
                        
                            break;
                            
                            case 'group':
                            
                            if($group_id = (int) $this->getRequestParameter('group_id')){
                                
                                $group = new SmartestAssetGroup;
                                if($group->find($group_id)){
                                    $group->addAssetById($asset->getId());
                                }
                                
                            }
                        
                        }
                    
                    }
                    
                }else{
                    // The requested asset type does not exist
                }
                
            }else{
                // User does not have permission to create new files
            }
            
        }else{
            // New file upload does not exist
        }
        
    }
    
    public function validateExternalResourceUrl(){
        
        $url = $this->getRequestParameter('url');
        $urlobj = new SmartestExternalUrl($url);
        $result = $urlobj->getExternalMediaInfo();
        header('Content-Type: application/json; charset=UTF8');
        echo json_encode($result->stdObjectOrScalar());
        exit;
        
    }
    
    public function urlPreview(){
        $h = new SmartestAPIServicesHelper;
        $url = $this->getRequestParameter('url');
        $urlobj = new SmartestExternalUrl($url);
        // $this->send($urlobj->getHtmlMetaData(), 'metadata');
        echo $urlobj->getPreviewMarkup();
        exit;
    }
    
    public function postBackTextEditorContentsFromModal(){
        
        if($this->getUser()->hasToken('modify_assets')){
            
            $asset = new SmartestAsset;
            
            if($asset->find($this->getRequestParameter('asset_id'))){
                
                if($asset->getType() == 'SM_ASSETTYPE_RICH_TEXT'){
                    
                    $content = $this->getRequestParameter('asset_content');
        		    $content = SmartestStringHelper::unProtectSmartestTags($content);
        		    $content = SmartestTextFragmentCleaner::convertDoubleLineBreaks($content);
                    $success = (bool) $asset->setContentFromEditor($content);
                    if($success){
            	        $asset->setModified(time());
                        $asset->save();
                        header('Content-Type: application/json; charset=UTF8');
                        echo json_encode(array('success'=>true));
                        exit;
                    }else{
                        header('Content-Type: application/json; charset=UTF8');
                        echo json_encode(array('success'=>false));
                        exit;
                    }
                    
                }
                
            }
        }
        
    }
    
    public function createEmbeddableAssetFromModal(){
        
        $ach = new SmartestAssetCreationHelper($this->getRequestParameter('asset_type'));
        
        try{
            
            $ach->createNewAssetFromUrl(new SmartestExternalUrl($this->getRequestParameter('asset_url')), $this->getRequestParameter('asset_label'), true);
            $asset = $ach->finish();
            $asset->setSiteId($this->getSite()->getId());
            
            if($this->getRequestParameter('asset_type') == 'SM_ASSETTYPE_OEMBED_URL'){
                $asset->setOEmbedServiceId($this->getRequestParameter('service_id'));
            }
            
            $asset->save();
            $obj = $asset->__toSimpleObject();
            
            if($this->getRequestParameter('asset_type') == 'SM_ASSETTYPE_OEMBED_URL'){
                $obj->oembed_service_id = $asset->getOEmbedServiceId();
            }
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($obj);
            exit;
            
        }catch(SmartestException $e){
            // Deal with exception
        }
        
    }
    
    public function createEmbeddableAssetFromModalTextArea(){
        
        $ach = new SmartestAssetCreationHelper($this->getRequestParameter('asset_type'));
        
        try{
            
            $ach->createNewAssetFromTextArea($this->getRequestParameter('asset_contents'), $this->getRequestParameter('asset_label'));
            $asset = $ach->finish();
            $asset->setSiteId($this->getSite()->getId());
            $asset->save();
            
            $obj = $asset->__toSimpleObject();
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($obj);
            exit;
            
        }catch(SmartestException $e){
            // Deal with exception
        }
        
    }
    
    public function insertGalleryForIpv(){
        
        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
            
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                if($property->getItemClassId() == $item->getModelId()){
                    
            	    $set = new SmartestAssetGroup;
            	    $set->setLabel($this->getRequestParameter('asset_gallery_label'));
            	    $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('asset_gallery_label')));
                    $set->setIsGallery(true);
        
                    $type_var = $this->getRequestParameter('asset_gallery_type');
        
            	    if($type_var == 'ALL'){
            	        $set->setFilterType('SM_SET_FILTERTYPE_NONE');
            	    }else{
            	        switch(substr($type_var, 0, 1)){
            	            case 'A':
            	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETTYPE');
            	            break;
            	            case 'P':
            	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
            	            break;
            	            case 'G':
            	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETGROUP');
            	            break;
            	        }
            	    }
        
            	    $set->setFilterValue(($type_var == 'ALL') ? null : substr($type_var, 2));
            	    $set->setSiteId($this->getSite()->getId());
            	    $set->setShared(0);
            	    $set->save();
                    
                    $item->setPropertyValueByNumericKey($property->getId(), $set->getId());
                    $item->save();
                    
                    $obj = $set->__toSimpleObject();
            
                    header('Content-Type: application/json; charset=UTF8');
                    echo json_encode($obj);
                    exit;
                    
                }else{
                    // property and item are from diff models
                }
            }else{
                // property ID doesn't exist
            }
            
        }else{
            // Item doesn't exist
        }
        
    }
    
}