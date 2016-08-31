<?php

class SmartestTag extends SmartestBaseTag implements SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_draft_mode = false;
    
    protected $_pages = array();
    protected $_page_ids = array();
    protected $_page_lookup_attempted = array();
    
    protected $_simple_items = array();
    protected $_items = array();
    protected $_item_ids = array();
    protected $_item_lookup_attempted = false;
    
    protected $_assets = array();
    protected $_asset_ids = array();
    protected $_asset_lookup_attempted = array();
    
    protected $_users = array();
    protected $_user_ids = array();
    protected $_user_lookup_attempted = array();
    
    protected $_is_attached = false; // Used when building the tags screen
    
    protected $_filters = array();
    
    protected $_icon_image_asset;
    protected $_description_text_asset;
    
    /* protected function __objectConstruct(){
        
        $this->_table_prefix = 'tag_';
		$this->_table_name = 'Tags';
        
    } */
    
    public function getPages($site_id='', $d='USE_DEFAULT'){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        if(count($this->_filters) && isset($this->_filters['model_id']) && is_numeric($this->_filters['model_id'])){
            return array();
        }
        
        if(!isset($this->_page_lookup_attempted[$site_id])){
        
            $sql = "SELECT * FROM TagsObjectsLookup, Pages WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=page_id AND taglookup_type='SM_PAGE_TAG_LINK'";
            
            if(is_numeric($site_id)){
                $sql .= " AND page_site_id='".$site_id."'";
            }
            
            if(!$draft){
                $sql .= " AND page_is_published='TRUE'";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getCurrentSiteId());
        
            $pages = array();
        
            foreach($result as $page_array){
                
                if($type_index[$page_array['page_id']] == 'ITEMCLASS'){
                    $page = new SmartestItemPage;
                }else{
                    $page = new SmartestPage;
                }
                
                $page->hydrate($page_array);
                $pages[] = $page;
                
                if($page->getId() && !in_array($page->getId(), $this->_page_ids)){
                    $this->_page_ids[] = $page->getId();
                }
            }
            
            $this->_page_lookup_attempted[$site_id] = true;
            $this->_pages[$site_id] = $pages;
        
        }
        
        return $this->_pages[$site_id];
        
    }
    
    public function getPageIds($site_id='', $d='USE_DEFAULT'){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        $this->getPages($site_id, $draft);
        return $this->_page_ids;
        
    }
    
    public function getSimpleItems($site_id=false, $draft_mode_arg='USE_DEFAULT', $model_id=false){
        
        $draft = ($draft_mode_arg === 'USE_DEFAULT') ? $this->_draft_mode : $draft_mode_arg;
        
        if(!$site_id || !is_numeric($site_id)){
            $site_id = 'all';
        }
        
        if(count($this->_filters)){
            
            if(isset($this->_filters['model_id']) && is_numeric($this->_filters['model_id'])){
                if(!is_numeric($model_id)){
                    $model_id = $this->_filters['model_id'];
                }
            }
            
        }
        
        if(!$this->_item_lookup_attempted[$site_id]){
        
            $sql = "SELECT * FROM TagsObjectsLookup, Items WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=item_id AND taglookup_type='SM_ITEM_TAG_LINK' AND item_deleted = '0'";
            
            if(is_numeric($site_id)){
                $sql .= " AND item_site_id='".$site_id."'";
            }
            
            if(!$draft){
                $sql .= " AND item_public='TRUE'";
            }
            
            if($model_id && is_numeric($model_id)){
                $sql .= " AND item_itemclass_id='".$model_id."'";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $items = array();
        
            foreach($result as $item_array){
                $item = new SmartestItem;
                $item->hydrate($item_array);
                $items[] = $item;
                
                if($item->getId() && !in_array($item->getId(), $this->_item_ids)){
                    $this->_item_ids[] = $item->getId();
                }
                
            }
            
            $this->_item_lookup_attempted[$site_id] = true;
            $this->_simple_items = $items;
        
        }
        
        return $this->_simple_items;
        
    }
    
    public function addFilter($filter_name, $filter_value){
        $this->_filters[$filter_name] = $filter_value;
    }
    
    public function removeFilter($filter_name){
        if(isset($this->_filters[$filter_name])){
            unset($this->_filters[$filter_name]);
        }
    }
    
    public function clearFilters(){
        $this->_filters = array();
    }
    
    public function getSimpleItemsAsArrays($site_id=false, $d='USE_DEFAULT', $model_id=false){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        $items = $this->getSimpleItems($site_id, $draft, $model_id);
        $arrays = array();
        
        foreach($items as $i){
            $arrays[] = $i->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getSimpleItemIds($site_id=false, $d='USE_DEFAULT', $model_id=false){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        $items = $this->getSimpleItems($site_id, $draft, $model_id);
        $ids = array();
        
        foreach($items as $i){
            $ids[] = $i->getId();
        }
        
        return $ids;
        
    }
    
    public function getItems($site_id=null, $model_id=null, $metapage_models_only=false){
        
        if(!$this->_item_lookup_attempted['site_'.$site_id]){
        
            $this->_items = $this->_getItems($site_id, $model_id, $metapage_models_only);;
        
        }
        
        return $this->_items;
        
    }
    
    protected function _getItems($site_id=null, $model_id=null, $metapage_models_only=false){
        
        if(count($this->_filters)){
            
            if(isset($this->_filters['model_id']) && is_numeric($this->_filters['model_id'])){
                if(!is_numeric($model_id)){
                    $model_id = $this->_filters['model_id'];
                }
            }
            
        }
        
        $sql = "SELECT DISTINCT Items.item_id FROM TagsObjectsLookup, Items WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=item_id AND taglookup_type='SM_ITEM_TAG_LINK' AND Items.item_deleted='0'";
        
        if($site_id && is_numeric($site_id)){
            $sql .= ' AND Items.item_site_id=\''.$site_id.'\'';
        }
        
        if($model_id && is_numeric($model_id)){
            $sql .= ' AND Items.item_itemclass_id=\''.$model_id.'\'';
        }else if($metapage_models_only && $site_id){
            $du = new SmartestDataUtility;
            $model_ids = $du->getModelIdsWIthMetapageOnSiteId($site_id);
            if(count($model_ids)){
                $sql .= ' AND Items.item_itemclass_id IN (\''.implode("','", $model_ids).'\')';
            }else{
                return array();
            }
        }
        
        if(!$this->getDraftMode()){
            $sql .= " AND item_public='TRUE'";
        }
        
        $result = $this->database->queryToArray($sql);
        
        $ids = array();
        
        foreach($result as $r){
            $ids[] = $r['item_id'];
        }
        
        $h = new SmartestCmsItemsHelper;
        
        if($model_id && is_numeric($model_id)){
            
            $model = new SmartestModel;
            
            if($model->find($model_id)){
                $s = new SmartestSortableItemReferenceSet($model, $this->getDraftMode());
                foreach($ids as $id){
                    $s->insertItemId($id);
                }
                $s->sort();
                $items = $s->getItems();
            }else{
                $items = $h->hydrateUniformListFromIdsArray($ids, $model_id, $this->getDraftMode());
            }
            
        }else{
            $items = $h->hydrateMixedListFromIdsArray($ids, $this->getDraftMode());
            $this->_item_lookup_attempted['site_'.$site_id] = true;
        }
        
        return $items;
        
    }
    
    public function getAssets($site_id=null){
        
        if(!$this->_asset_lookup_attempted['site_'.$site_id]){
        
            $sql = "SELECT Assets.* FROM TagsObjectsLookup, Assets WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=asset_id AND taglookup_type='SM_ASSET_TAG_LINK' AND asset_deleted=0";
            
            if(is_numeric($site_id)){
                $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1)";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $assets = array();
        
            foreach($result as $asset_array){
                
                $asset = new SmartestAsset;
                $asset->hydrate($asset_array);
                $assets[] = $asset;
                
                if($asset->getId() && !in_array($asset->getId(), $this->_asset_ids)){
                    $this->_asset_ids[] = $asset->getId();
                }
                
            }
            
            $this->_asset_lookup_attempted['site_'.$site_id] = true;
            $this->_assets = $assets;
            
        }
        
        return $this->_assets;
        
    }
    
    public function getImages($site_id=null){
        
        $sql = "SELECT Assets.* FROM TagsObjectsLookup, Assets WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=asset_id AND taglookup_type='SM_ASSET_TAG_LINK' AND asset_deleted=0 AND asset_type IN('SM_ASSETTYPE_JPEG_IMAGE','SM_ASSETTYPE_PNG_IMAGE','SM_ASSETTYPE_GIF_IMAGE')";
        
        if(is_numeric($site_id)){
            $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1)";
        }
        
        $result = $this->database->queryToArray($sql);
        
        $assets = array();
    
        foreach($result as $asset_array){
            
            $asset = new SmartestRenderableAsset;
            $asset->hydrate($asset_array);
            $assets[] = $asset;
            
            if($asset->getId() && !in_array($asset->getId(), $this->_asset_ids)){
                $this->_asset_ids[] = $asset->getId();
            }
            
        }
        
        return $assets;
        
    }
    
    public function getUsers(){
        
        if(!$this->_user_lookup_attempted){
        
            $sql = "SELECT Users.* FROM TagsObjectsLookup, Users WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=user_id AND taglookup_type='SM_USER_TAG_LINK'";
            
            /* if(is_numeric($site_id)){
                $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared=1)";
            } */
                
            // echo $sql;
            
            $result = $this->database->queryToArray($sql);
            
            $users = array();
        
            foreach($result as $user_array){
                
                $user = new SmartestUser;
                $user->hydrate($user_array);
                $users[] = $user;
                
                if($user->getId() && !in_array($user->getId(), $this->_user_ids)){
                    $this->_user_ids[] = $user->getId();
                }
                
            }
            
            $this->_user_lookup_attempted = true;
            $this->_users = $users;
            
        }
        
        return $this->_users;
        
    }
    
    public function getSystemUsers($site_id=null){
        
        if(!$this->_user_lookup_attempted['site_'.$site_id]){
        
            $sql = "SELECT Users.* FROM TagsObjectsLookup, Users WHERE taglookup_tag_id='".$this->getId()."' AND taglookup_object_id=user_id AND taglookup_type='SM_USER_TAG_LINK'";
            
            if(is_numeric($site_id)){
                $uh = new SmartestUsersHelper;
                $user_ids = $uh->getUserIdsOnSite($site_id);
                $sql .= " AND Users.user_id IN ('".implode("','", $user_ids)."')";
            }
            
            $result = $this->database->queryToArray($sql);
            
            $assets = array();
        
            foreach($result as $user_array){
                
                $user = new SmartestSystemUser;
                $user->hydrate($user_array);
                $users[] = $user;
                
                if($user->getId() && !in_array($user->getId(), $this->_user_ids)){
                    $this->_user_ids[] = $user->getId();
                }
                
            }
            
            $this->_user_lookup_attempted['site_'.$site_id] = true;
            $this->_users = $users;
            
        }
        
        return $this->_assets;
        
        
    }
    
    public function hasPage($page_id){
        
        // make sure pages have been retrieved
        $this->getPages();
        
        if(in_array($page_id, $this->_page_ids)){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function hasItem($item_id, $draft_mode=false){
        
        // make sure items have been retrieved
        $this->getSimpleItems(false, true, false);
        
        // print_r($this->_item_ids);
        
        if(in_array($item_id, $this->_item_ids)){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function hasAsset($asset_id){
        
        // make sure assets have been retrieved
        $this->getAssets(false, true, false);
        
        if(in_array($asset_id, $this->_asset_ids)){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function getObjectsOnSite($site_id, $d='USE_DEFAULT'){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        $master_array = array();
        
        $du = new SmartestDataUtility;
        $du->getModelsWithMetapageOnSiteId($site_id);
        
        $pages = $this->getPages($site_id, $draft);
        $items = $this->getItems($site_id, $draft, true);
        
        foreach($pages as $p){
            
            $key = $p->getDate();
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $p;
            
        }
        
        foreach($items as $i){
            
            $key = $i->getDate();
            if($key instanceof SmartestDateTime){
                $key = $key->getUnixFormat();
            }
            
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
            
        }
        
        krsort($master_array);
        
        return $master_array;
        
    }
    
    /* public function getObjectsOnSiteAsArrays($site_id, $d='USE_DEFAULT'){
        
        $draft = ($d == 'USE_DEFAULT') ? $this->_draft_mode : $d;
        
        $objects = $this->getObjectsOnSite($site_id, $draft_mode);
        $arrays = array();
        
        foreach($objects as $o){
            $arrays[] = $o->__toArray();
        }
        
        return $arrays;
        
    } */
    
    public function getDescriptionTextAsset(){
        
        $request = SmartestPersistentObject::get('request_data');
        
        if($this->getCurrentRequestData()->g('application')->g('name') == 'website'){
            $class = 'SmartestRenderableAsset';
        }else{
            $class = 'SmartestAsset';
        }
        
        if(is_object($this->_description_text_asset) && $this->_description_text_asset instanceof SmartestAsset){
            $this->_description_text_asset->setDraftMode($this->getDraftMode());
            return $this->_description_text_asset;
        }
        
        $id = $this->getField('description_text_asset_id');
        $asset = new $class;
        
        if(!is_numeric($id) || !$asset->find($id)){
            
            $this->_description_text_asset = new $class;
            
            $this->_description_text_asset->setWebid(SmartestStringHelper::random(32));
            $this->_description_text_asset->setLabel('Tag description text for tag '.$this->getLabel());
            $this->_description_text_asset->setCreated(time());
            $this->_description_text_asset->setModified(time());
            $this->_description_text_asset->setStringId(SmartestStringHelper::toVarName('Tag description text for tag '.$this->getLabel()));
            $this->_description_text_asset->setUrl(SmartestStringHelper::toVarName('Tag description text for tag '.$this->getLabel()).'.html');
            $this->_description_text_asset->setUserId($this->getId());
            $this->_description_text_asset->setSiteId(0);
            $this->_description_text_asset->setShared(1);
            $this->_description_text_asset->setType('SM_ASSETTYPE_RICH_TEXT');
            
            // if($this->getType() == 'SM_USERTYPE_SYSTEM_USER'){
                $this->_description_text_asset->setPublicStatusTrusted(1);
            /* }else{
                $this->_bio_text_asset->setPublicStatusTrusted(0);
            } */
            
            $this->_description_text_asset->setIsHidden(1);
            $this->_description_text_asset->setIsSystem(1);
            
            $this->_description_text_asset->save();
            
            // $this->_description_text_asset->getTextFragment()->setContent(SmartestStringHelper::sanitize(SmartestStringHelper::parseTextile(stripslashes($this->_properties['bio']))));
            $this->_description_text_asset->connectTextFragmentOnSave();
            $this->_description_text_asset->save();
            
            $this->setField('description_text_asset_id', $asset->getId());
            $sql = "UPDATE Tags SET Tags.tag_description_text_asset_id='".$this->_description_text_asset->getId()."' WHERE Tags.tag_id='".$this->getId()."' LIMIT 1";
            $this->database->rawQuery($sql);
            
        }else{
            $this->_description_text_asset = $asset;
        }
        
        $this->_description_text_asset->setDraftMode($this->getDraftMode());
        return $this->_description_text_asset;
        
    }
    
    public function getDescriptionTextAssetForEditor(){
        return $this->getDescriptionTextAsset()->getContentForEditor();
    }
    
    public function updateDescriptionTextAssetFromEditor($content){
        
	    $content = SmartestStringHelper::unProtectSmartestTags($content);
	    $content = SmartestTextFragmentCleaner::convertDoubleLineBreaks($content);
        $content = SmartestStringHelper::sanitize($content);
        
	    $this->getDescriptionTextAsset()->setContent($content);
        $this->getDescriptionTextAsset()->setModified(time());
        $this->getDescriptionTextAsset()->save();
        $this->getDescriptionTextAsset()->getTextFragment()->createPreviewFile();
        $this->getDescriptionTextAsset()->getTextFragment()->publish();
        
    }
    
    public function getIconImageAsset(){
        
        if(is_object($this->_icon_image_asset) && $this->_icon_image_asset instanceof SmartestAsset){
            return $this->_icon_image_asset;
        }else{
            $id = (int) $this->getField('icon_image_asset_id');
            $asset = new SmartestAsset;
            
            if($asset->find($id)){
                $this->_icon_image_asset = $asset;
                return $this->_icon_image_asset;
            }
            
        }
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "all":
            case "objects":
            // var_dump($this->_draft_mode);
            return $this->getObjectsOnSite($this->getCurrentSiteId(), $this->_draft_mode);
            
            case "url":
            return $this->_request->getDomain().'tags/'.$this->getName().'.html';
            
            case "feed_url":
            return $this->_request->getDomain().'tags/'.$this->getName().'/feed';
            
            case "attached":
            return $this->_is_attached;
            
            case "pages":
            return new SmartestArray($this->getPages($this->getCurrentSiteId()));
            
            case "images":
            return new SmartestArray($this->getImages($this->getCurrentSiteId()));
            
            case "description":
            case "description_text_asset":
            $this->getDescriptionTextAsset()->getTextFragment()->createPreviewFile();
            $this->getDescriptionTextAsset()->getTextFragment()->publish();
            return $this->getDescriptionTextAsset();
            
            case "slug":
            return $this->getName();
            
        }
        
        if(array_key_exists($offset, $this->_properties) || parent::offsetExists($offset)){
            return parent::offsetGet($offset);
        }
        
        $du = new SmartestDataUtility;
        $models = $du->getModelPluralNamesLowercase();
        
        if(isset($models[$offset])){
            // Model-specific tagged items retrieval by model name
            return new SmartestArray($this->_getItems($this->getCurrentSiteId(), $models[$offset], false));
        }
        
    }
    
    public function save(){
        
        if(strlen(trim($this->getLabel())) && strlen(trim($this->getName()))){
            return parent::save();
        }else{
            return false;
        }
        
    }
    
    public function delete(){
        
        // Delete Lookups
        $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_tag_id='".$this->getId()."'";
        $this->database->rawQuery($sql);
        parent::delete();
        
    }
    
    public function offsetSet($offset, $value){

        if($offset == 'attached'){
            $this->_is_attached = (bool) $value;
        }
        
        parent::offsetSet($offset, $value);
        
    }
    
    public function setDraftMode($mode){
        $this->_draft_mode = (bool) $mode;
    }
    
    public function getDraftMode(){
        if($this->getCurrentRequestData()->g('action') == "renderEditableDraftPage"){
            $this->setDraftMode(true);
        }
        return $this->_draft_mode;
    }
    
    public function getStorableFormat(){
        return $this->getId();
    }
    
    public function hydrateFromStorableFormat($raw){
        return $this->find($raw);
    }
    
    public function hydrateFromFormData($raw){
        return $this->find($raw);
    }
    
    public function renderInput($params){
        
    }
    
}