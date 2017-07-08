<?php

class SmartestSite extends SmartestBaseSite{
    
    protected $_home_page = null;
    protected $_containers = array();
    protected $_placeholders = array();
    protected $_field_names = array();
    protected $_sets = array();
    protected $_models = array();
    protected $displayPages = array();
    protected $displayPagesIndex = 0;
    protected $_last_search_time_taken = 0;
    
    public static $special_page_ids = array();
    
	public function getHomePage($draft_mode=false){
	    
	    if($this->_home_page === null){
	        $page = new SmartestPage;
    	    $page->find($this->getTopPageId());
    	    $page->setDraftMode($draft_mode);
    	    $this->_home_page = $page;
        }
	    
	    return $this->_home_page;
	    
	}
	
	public function getPagesTree($draft_mode=true, $normal_pages_only=false){
	    
	    /* if(SmartestCache::hasData('site_pages_tree_'.$this->getId(), true)){
			
			$tree = SmartestCache::load('site_pages_tree_'.$this->getId(), true);
			
		}else{ */
            
            $home_page = $this->getHomePage();
		    
		    $home_page->setDraftMode($draft_mode);
		    
		    $tree = array();
			$tree[0]["info"] = $home_page;
            // $tree[0]["info"] = $home_page;
            $tree[0]["treeLevel"] = 0;
			$tree[0]["children"] = $home_page->getPagesSubTree(1);
            
            // var_dump($home_page->getDraftMode());
			
            // echo count($tree[0]["children"]);
            
			// SmartestCache::save('site_pages_tree_'.$this->getId(), $tree, -1, true);
		
        // }
		
		return $tree;
	    
	}
    
    public function getPagesTreeWithSpecialPages($draft_mode=true){
        
        $home_page = $this->getHomePage($draft_mode);
	    $home_page->setDraftMode($draft_mode);
	    
	    $tree = array();
		$tree[0]["info"] = $home_page;
		$tree[0]["treeLevel"] = 0;
        $tree[0]["children"] = $home_page->getPagesSubTreeWithSpecialPages(1);
        
        return $tree;
        
    }
	
	public function getPagesList($draft_mode=false, $normal_pages_only=false){
	    
	    $this->displayPages = array();
	    $this->displayPagesIndex = 0;
	    $list = $this->getSerializedPageTree($this->getPagesTree($draft_mode, $normal_pages_only));
	    return $list;
	    
	}
    
    public function getPagesListWithSpecialPages($draft_mode=false){
        
	    $this->displayPages = array();
	    $this->displayPagesIndex = 0;
	    $list = $this->getSerializedPageTree($this->getPagesTreeWithSpecialPages($draft_mode, true));
	    return $list;
        
    }
	
	public function getSerializedPageTree($tree){
		
        foreach($tree as $key => $page){
			
			$this->displayPages[$this->displayPagesIndex]['info'] = $page['info'];
			$this->displayPages[$this->displayPagesIndex]['treeLevel'] = $page['treeLevel'];
			$children = $page['children'];
			
			if(isset($page['child_items'])){
			    $this->displayPages[$this->displayPagesIndex]['child_items'] = $page['child_items'];
		    }
			
			$this->displayPagesIndex++;
			
			if(count($children) > 0){
				$this->getSerializedPageTree($children);
			}
	
		}
        
        return $this->displayPages;
		
	}
	
	public function getSpecialPageIds(){
	    
	    if(count(self::$special_page_ids)){
	        return self::$special_page_ids;
	    }else{
            $ids = new SmartestParameterHolder('Special page IDs for site \''.$this->getName().'\'');
            $ids->setParameter('tag_page_id', $this->getTagPageId());
            $ids->setParameter('user_page_id', $this->getUserPageId());
            $ids->setParameter('error_page_id', $this->getErrorPageId());
            $ids->setParameter('search_page_id', $this->getSearchPageId());
            $ids->setParameter('holding_page_id', $this->getHoldingPageId());
            self::$special_page_ids = $ids;
            return $ids;
        }
        
    }
    
    public function pageIdIsSpecial($page_id){
        return strlen((string) $page_id) && in_array($page_id, $this->getSpecialPageIds()->getParameters());
    }
	
	public function getNormalPagesList($draft_mode=false, $return_plain_objects=false){
	    
	    if($return_plain_objects){
	        
	        
	        
	    }else{
	        
	        $list = $this->getPagesList($draft_mode, true);
	        
	        foreach($list as $k=>$page){
                if($page['info']['type'] != 'NORMAL' || $this->getSpecialPageIds()->hasValue($page['info']['id'])){
    	            unset($list[$k]);
    	        }
    	    }
    	    
    	    return array_values($list);
	    
        }
        
	}
    
    public function getQuickPagesList($draft_mode=false){
        
        $sql = "SELECT * FROM Pages WHERE Pages.page_type='NORMAL' AND Pages.page_deleted != 'TRUE' AND Pages.page_site_id='{$this->getId()}'";
        
        if(!$draft_mode){
            $sql .= " AND Pages.page_is_published='TRUE' ORDER BY Pages.page_title";
        }
        
        $result = $this->database->queryToArray($sql);
        $pages = array();
        
        foreach($result as $r){
            $p = new SmartestPage;
            $p->hydrate($r);
            $pages[] = $p;
        }
        
        return $pages;
    }
    
    public function getDefaultBlockListStyle(){
        
        $id = $this->getDefaultBlockListStyleId();
        $style = new SmartestBlockListStyle;
        
        if($id && $style->find($id)){
            return $style;
        }else{
            
            $style->setLabel('Default BlockList style for '.$this->getName());
            $style->setName(SmartestStringHelper::toVarName($style->getLabel()));
            $style->setSiteId($this->getId());
            $style->save();
            
            $this->setDefaultBlockListStyleId($style->getId());
            $this->database->rawQuery("UPDATE Sites SET Sites.site_default_blocklist_style_id='".$style->getId()."' WHERE Sites.site_id='".$this->getId()."' LIMIT 1");
            
            return $style;
        }
        
    }
    
    public function getBlockListStyles(){
        
        $h = new SmartestBlockListHelper;
        return $h->getBlockListStyles($this->getId());
        
    }
	
    public function getSearchResults($query, $search_type='ALL'){
        if(SmartestElasticSearchHelper::elasticSearchIsOperational()){
            return $this->getElasticSearchResults($query, $search_type);
        }else{
            return $this->getNativeSearchResults($query, $search_type);
        }
    }
    
    public function getElasticSearchResults($query, $search_type='ALL'){
        
        $results = array();
        $fields = array();
        $params = array();
        $ids = array();
        $page_ids = array();
        $du = new SmartestDataUtility;
        $search_start_time = microtime(true);
        
        $model_names = array_values($du->getModelPluralNamesLowercase($this->getId(), true));
        $items_type = ($search_type == 'ALL' || !in_array($search_type, $model_names)) ? implode(',',$model_names) : $search_type;
        
        foreach($this->getModelsWithMetapage() as $model){
            $fields = array_merge($fields, $model->getElasticSearchIndexTypedQueryPropertyNames());
        }
        
        $params['index'] = $this->getElasticSearchIndexName();
        $params['type'] = $items_type;
        $params['body'] = array(
            'min_score'=>0.1,
            'query'=>array(
                'multi_match'=>array(
                    'query'=>$query,
                    'type'=>'best_fields',
                    'fields'=>$fields,
                    'tie_breaker'=>0.3
                )
            )
        );
        
        $raw_results = SmartestElasticSearchHelper::getItemsMatchingQuery($params);
        // print_r($raw_results);
        
        if(isset($raw_results['hits']) && isset($raw_results['hits']['hits'])){
            
            foreach($raw_results['hits']['hits'] as $raw_item){
                $ids[] = $raw_item['_id'];
            }
            
            $h = new SmartestCmsItemsHelper;
            
            $items = $h->hydrateMixedListFromIdsArray($ids);
            
        }
        
        $page_fields = $this->getElasticSearchPageSearchFields();
        $params['index'] = $this->getElasticSearchIndexName();
        $params['type'] = 'smartest_page';
        $params['body'] = array(
            'min_score'=>0.1,
            'query'=>array(
                'multi_match'=>array(
                    'query'=>$query,
                    'type'=>'best_fields',
                    'fields'=>$page_fields,
                    'tie_breaker'=>0.3
                )
            )
        );
        
        $raw_page_results = SmartestElasticSearchHelper::getItemsMatchingQuery($params);
        // echo count($raw_page_results['hits']['hits']);
        $page_ids = array();
        $homepage_id = $this->getTopPageId();
        
        if(isset($raw_page_results['hits']) && isset($raw_page_results['hits']['hits'])){
            
            foreach($raw_page_results['hits']['hits'] as $raw_item){
                if($raw_item['_id'] != $homepage_id){
                    $page_ids[] = $raw_item['_id'];
                }
            }
            
            $sql = "SELECT * FROM Pages WHERE Pages.page_id IN ('".implode("','",$page_ids)."')";
            $result = $this->database->queryToArray($sql);
            $pages = array();
            
            foreach($result as $r){
                $p = new SmartestPage;
                $p->hydrate($r);
                $pages[] = $p;
            }
            
        }
        
        /// Start building array
        $master_array = array();
        
        foreach($pages as $p){
            
            $key = $p->getDate()*1000;
        
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
            
            $key = $key*1000;
        
            if(in_array($key, array_keys($master_array))){
                while(in_array($key, array_keys($master_array))){
                    $key++;
                }
            }
            
            $master_array[$key] = $i;
        
        }

        krsort($master_array);
        
        $search_end_time = microtime(true);
        $this->_last_search_time_taken = ($search_end_time - $search_start_time)*1000;
        
        return $master_array;
        
    }
    
    public function getElasticSearchModelTypeNames(){
        $index_names = array();
        foreach($this->getModelsWithMetapage() as $m){
            $index_names[] = $m->getElasticSearchIndexNameForSiteId($this->getId());
        }
        return $index_names;
    }
    
    public function getElasticSearchTypeNames(){
        $index_names = $this->getElasticSearchModelTypeNames();
        $index_names[] = 'pages';
        return $index_names;
    }
    
    public function getElasticSearchIndexName(){
        return SmartestSystemHelper::getInstallIdNoColons().'_site_'.$this->getId();
    }
    
    public function getElasticSearchPageSearchFields(){
        $fields = array('name^3','description','meta_description','search_terms^2');
        $default_placeholder_id = $this->getPrimaryTextPlaceholderId();
        foreach($this->getPlaceholders() as $p){
            if($p->isForText()){
                if($p->getId() == $default_placeholder_id){
                    $fields[] = 'placeholder__'.$p->getName().'^2';
                }else{
                    $fields[] = 'placeholder__'.$p->getName();
                }
            }
        }
        return $fields;
    }
    
    public function createElasticSearchIndexParams(){
        
        $data = array();
        
        $index_name = $this->getElasticSearchIndexName();
        
        $data['index'] = $index_name;
        $data['body'] = array();
        
        $data['body']['settings'] = [
            'number_of_shards' => 5,
            'number_of_replicas' => 1
        ];
        
        $data['body']['mappings'] = array();
        
        foreach($this->getModelsWithMetapage() as $m){
            $data['body']['mappings'][$m->getVarName()] = $m->getElasticSearchIndexCreationData();
        }
        
        return $data;
        
    }
    
    public function createElasticSearchIndex(){
        
        if(SmartestElasticSearchHelper::elasticSearchIsOperational()){
            
            $data = $this->createElasticSearchIndexParams();
            $name = $this->getElasticSearchIndexName();
            
            try{
            
                $client = SmartestElasticSearchHelper::getClient();
                // $data = $this->createElasticSearchIndexParamsForSiteId($site->getId());
                $response = $client->indices()->create($data);
                return $response;
            
            }catch(Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
                // could not check for index or build one because ES isn't running
            }
        }
    }
    
	public function getNativeSearchResults($query, $search_type='ALL'){
	    
        $query = strip_tags($query);
	    $search_query_words = preg_split('/[^\w]+/', $query);
	    $h = new SmartestCmsItemsHelper;
	    $search_start_time = microtime(true);
        // $model_ids = $du->getModelIdsWIthMetapageOnSiteId($this->getId());
	    
	    $pages = array();
	    $pages_sql = "SELECT Pages.* FROM Pages WHERE page_site_id='".$this->getId()."' AND page_deleted != 'TRUE' AND page_id !='".$this->getSearchPageId()."' AND page_id !='".$this->getTagPageId()."' AND page_type='NORMAL' AND page_is_published='TRUE'";
	    $items_sql = "SELECT Items.item_id FROM Items WHERE item_site_id='".$this->getId()."' AND item_deleted=0 AND item_public='TRUE'";
	    
	    if(count($search_query_words) > 0){
	        $pages_sql .= ' AND (';
	        $items_sql .= ' AND (';
	    }else{
	        return array();
	    }
	    
	    foreach($search_query_words as $key=>$word){
	        
	        if($key > 0){
	            $pages_sql .= "OR ";
	            $items_sql .= "OR ";
	        }
	        
	        $pages_sql .= "(page_search_field LIKE '%".$word."%' OR page_title LIKE '%".$word."%') ";
	        $items_sql .= "(item_search_field LIKE '%".$word."%' OR item_name LIKE '%".$word."%') ";
	        
        }
        
        $pages_sql .= ')';
        $items_sql .= ')';
        
        $du = new SmartestDataUtility;
        $model_ids = $du->getModelIdsWithMetapageOnSiteId($this->getId());
        
        if(count($model_ids)){
            $items_sql .= ' AND Items.item_itemclass_id IN (\''.implode("','", $model_ids).'\')';
        }
        
        // echo $items_sql;
        
        if(count($search_query_words)){
            
            $pages_result = $this->database->queryToArray($pages_sql);
            
            foreach($pages_result as $array){
                $page = new SmartestPage;
                $page->hydrate($array);
                $pages[] = $page;
            }
            
            $items_result = $this->database->queryToArray($items_sql);
            $ids = array();

            foreach($items_result as $array){
                $ids[] = $array['item_id'];
            }
            
            $items = $h->hydrateMixedListFromIdsArray($ids);
            
            $master_array = array();
            
            foreach($pages as $p){

                $key = $p->getDate()*1000;

                if(in_array($key, array_keys($master_array))){
                    while(in_array($key, array_keys($master_array))){
                        $key++;
                    }
                }

                $master_array[$key] = $p;

            }

            foreach($items as $i){
                
                $key = $i->getDate()*1000;
                
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
            
            $search_end_time = microtime(true);
            $this->_last_search_time_taken = ($search_end_time - $search_start_time)*1000;
            
            return $master_array;
            
        }else{
            // no search terms were entered so no search results come back
            return array();
        }
	    
	}
    
    public function getSingleModelSearchResults($query, $model_id){
        
        $query = strip_tags($query);
	    $search_query_words = preg_split('/[^\w]+/', $query);
        
        if(count($search_query_words)){
            
            $du = new SmartestDataUtility;
            $model_ids = $du->getModelIdsWithMetapageOnSiteId($this->getId());
        
            if(in_array($model_id, $model_ids)){
                $items_sql = "SELECT Items.item_id FROM Items WHERE item_site_id='".$this->getId()."' AND item_deleted=0 AND item_public='TRUE' AND item_itemclass_id='".$model_id."' AND (";
                
        	    foreach($search_query_words as $key=>$word){
	        
        	        if($key > 0){
        	            $items_sql .= " OR ";
        	        }
	        
        	        $items_sql .= "(item_search_field LIKE '%".$word."%' OR item_name LIKE '%".$word."%') ";
	        
                }
        
                $items_sql .= ')';
                
            }else{
                // Model does not have default metapage
                return array();
            }
            
        }else{
            // No words entered
            return array();
        }
        
    }
	
	public function getLastSearchTimeTaken(){
	    
	    return $this->_last_search_time_taken;
	    
	}
	
	public function getPublicComments(){
	    
	    
	    
	}
	
	public function getTitleFormatSeparator(){
	    
	    $found = preg_match_all('/[\/\|\>›\xBB-]+/', $this->getTitleFormat(), $matches);
	    
	    if(count($matches)){
	        $symbols = $matches[0];
	        return $symbols[0];
        }
	}
	
	public function getModels($top_level=true){
	    
	    /* $sql = "SELECT * FROM ItemClasses WHERE ItemClasses.itemclass_type='SM_ITEMCLASS_MODEL' AND (ItemClasses.itemclass_shared='1' OR ItemClasses.itemclass_site_id = '".$this->getId()."') ORDER BY itemclass_name";
	    $result = $this->database->queryToArray($sql);
	    $models = array();
	    
	    if(count($result)){
	        
	        foreach($result as $m_array){
	            $m = new SmartestModel;
	            $m->hydrate($m_array);
	            $models[] = $m;
	        }
	        
	        return $models;
	        
	    }else{
	        return array();
	    } */
        
        $du = new SmartestDataUtility;
        return $du->getModels(false, $this->getId(), true, $top_level);
	    
	}
    
    public function getModelsWithMetapage(){
        $du = new SmartestDataUtility;
        return $du->getMetaPageModels($this->getId());
    }
	
	public function getDataSets(){
	    
	    $sql = "SELECT * FROM Sets WHERE (Sets.set_type='DYNAMIC' || Sets.set_type='STATIC') AND (Sets.set_shared='1' OR Sets.set_site_id = '".$this->getId()."') ORDER BY set_name";
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
	    if(count($result)){
	        
	        foreach($result as $s_array){
	            $s = new SmartestCmsItemSet;
	            $s->hydrate($s_array);
	            $sets[] = $s;
	        }
	        
	        return $sets;
	    }else{
	        return array();
	    }
	}
	
	/* public function getDataSetsAsArrays(){
	    
	    $sets = $this->getDataSets();
	    $arrays = array();
	    
	    foreach($sets as $s){
	        $arrays[] = $s->__toArray(false);
	    }
	    
	    return $arrays;
	    
	} */
	
	public function getContainers(){
	    
	    $sql = "SELECT * FROM AssetClasses WHERE (assetclass_site_id='".$this->getId()."' OR assetclass_shared='1') AND assetclass_type='SM_ASSETCLASS_CONTAINER'";
	    $result = $this->database->queryToArray($sql);
	    
	    $containers = array();
	    
	    foreach($result as $r){
	        $c = new SmartestContainer;
	        $c->hydrate($r);
	        $containers[] = $c;
	    }
	    
	    return $containers;
	    
	}
	
	public function getPlaceholders(){
	    $du = new SmartestDataUtility;
        return $du->getPlaceholders($this->getId());
	}
	
	public function getFieldNames(){
	    
	    if(!count($this->_field_names)){
	    
    	    $sql = "SELECT pageproperty_name FROM PageProperties WHERE pageproperty_site_id='".$this->getId()."'";
    	    $result = $this->database->queryToArray($sql);
    	    $names = array();
	    
    	    foreach($result as $r){
    	        $names[] = $r['pageproperty_name'];
    	    }
	    
    	    $this->_field_names = $names;
	    
        }
	    
	    return $this->_field_names;
	    
	}
	
	public function fieldExists($field_name){
        
        return in_array(SmartestStringHelper::toVarName($field_name), $this->getFieldNames());
        
    }
    
    public function findByPageId($page_id){
        
        $result = array();
        
        if(is_numeric($page_id)){
			// numeric_id
			$sql = "SELECT page_site_id FROM Pages WHERE page_id='".$page_id."'";
            $result = $this->database->queryToArray($sql);
		}else if(preg_match('/[a-zA-Z0-9\$-]{32}/', $page_id)){
			// 'webid'
            $sql = "SELECT page_site_id FROM Pages WHERE page_webid='".$page_id."'";
            $result = $this->database->queryToArray($sql);
		}else{
            // echo "did not match";
            $result = array();
        }
        
        if(count($result)){
            // print_r($result);
            $site_id = $result[0]['page_site_id'];
            return $this->find($site_id);
        }else{
            return null;
        }
        
    }
	
	public function getFullDirectoryPath(){
	    return SM_ROOT_DIR.'Sites/'.$this->getDirectoryName().'/';
	}
    
    public function getTopLevelUrl(){
        $request = SmartestPersistentObject::get('request_data');
	    return 'http://'.$this->getDomain().$request->g('domain');
    }
    
	public function getHomepageFullUrl(){
        return $this->getTopLevelUrl().$this->getHomePage()->getDefaultUrl();
	}
	
	public function getUniqueId(){
	    if(!strlen($this->_properties['unique_id'])){
	        $new_id = $this->calculateUniqueId();
	        $s = $this->copy();
	        $s->setUniqueId($new_id);
	        $s->save();
	        $this->_properties['unique_id'] = $new_id;
	        return $new_id;
	    }else{
	        return $this->_properties['unique_id'];
	    }
	}
	
	public function calculateUniqueId(){
	    $site_id = implode(':', str_split(substr(md5($this->getName().SmartestStringHelper::random(40)), 0, 4), 2));
	    $install_id = SmartestSystemSettingHelper::getInstallId();
	    $id = $install_id.':'.$site_id;
	    return $id;
	}
	
	public function testDirectoryStructure(){
	    // $directory = 
        /* $d = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/system.yml');
        $structure = */
    }
    
    public function getDirectory(){
        
        return SM_ROOT_DIR.'Sites/'.$this->getDirectoryName().'/';
        
    }
    
    public function getUsersThatHaveAccess(){
        
        $sql = "SELECT DISTINCT Users.* FROM Users, UsersTokensLookup WHERE UsersTokensLookup.utlookup_token_id='21' AND UsersTokensLookup.utlookup_user_id=Users.user_id AND (UsersTokensLookup.utlookup_site_id='".$this->getId()."' OR UsersTokensLookup.utlookup_is_global=1) ORDER BY Users.user_lastname";
        $result = $this->database->queryToArray($sql);
        $users = array();
        
        foreach($result as $r){
            $u = new SmartestSystemUser;
            $u->hydrate($r);
            $users[] = $u;
        }
        
        return $users;
        
    }
    
    public function urlExists($url, $ignore_deleted=false){
        
        $sql = "SELECT PageUrls.pageurl_id FROM PageUrls, Pages WHERE pageurl_url='".$url."' AND PageUrls.pageurl_page_id=Pages.page_id AND Pages.page_site_id='".$this->getId()."'";
        
        if($ignore_deleted){
            $sql .= " AND page_deleted='FALSE'";
        }
        
        return (bool) count($this->database->queryToArray($sql));
        
    }
    
    public function getContentByUrl($url){
        
        if($url == '/'){
            return $this->getHomePage();
        }
        
        if(strlen($url) > 1 && $url{0} == '/'){
            $url = substr($url, 1);
        }
        
        $h = new SmartestRequestUrlHelper;
        
        if($page = $h->getNormalPageByUrl($url, $this->getId())){

	        // we are viewing a static page
	        return $page;

	    }else if($page = $h->getItemClassPageByUrl($url, $this->getId(), 'return')){
            
            // we are viewing an item page
	        return $page;

	    }else{
            
            // page not found
		    return false;

	    }
        
    }
    
    public function getLanguageCode(){
        
        $ph = new SmartestPreferencesHelper;
        return $ph->getGlobalPreference('site_language', 0, $this->getId());
        
    }
    
    public function setLanguageCode($code){
        
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_language', $code, 0, $this->getId());
        
    }
    
    public function getLogoAsset(){
        
        $a = new SmartestSiteLogoAsset;
        $a->find($this->getLogoImageAssetId());
        return $a;
        
    }
    
    public function getImages($limit=null){
        
        $alh = new SmartestAssetsLibraryHelper;
        $images = $alh->getAssetsByTypeCode(array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE'), $this->getId());
        
        if(is_numeric($limit) && $limit > 0){
            return array_slice($images, 0, $limit);
        }else{
            return $images;
        }
        
    }
    
    public function getOrganizationName(){
        $ph = new SmartestPreferencesHelper;
        $on = $ph->getGlobalPreference('site_organisation_name', null, $this->getId());
        return $on;
    }
    
    public function getOrganizationNameOrSiteName(){
        $on = $this->getOrganizationName();
        if(strlen($on)){
            return $on;
        }else{
            return $this->getName();
        }
    }
    
    public function setOrganizationName($name){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_organisation_name', $name, null, $this->getId());
    }
    
    public function getOrganisationName(){
        return $this->getOrganizationName();
    }
    
    public function setOrganisationName($name){
        $this->setOrganizationName($name);
    }
    
    public function getOEmbedEnabled(){
        $ph = new SmartestPreferencesHelper;
        return (bool) $ph->getGlobalPreference('site_oembed_enabled', null, $this->getId());
    }
    
    public function getOEmbedWidth(){
        $ph = new SmartestPreferencesHelper;
        $val = (int) $ph->getGlobalPreference('site_oembed_width', null, $this->getId());
        $val = $val ? $val : 420;
        return $val;
    }
    
    public function getOEmbedHeight(){
        $ph = new SmartestPreferencesHelper;
        $val = (int) $ph->getGlobalPreference('site_oembed_width', null, $this->getId());
        $val = $val ? $val : 140;
        return $val;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "unique_id":
            return $this->getUniqueId();
            
            case "user_page_id":
            return $this->getUserPageId();
            
            case "tag_page_id":
            return $this->getTagPageId();
            
            case "error_page_id":
            return $this->getErrorPageId();
            
            case "search_page_id":
            return $this->getSearchPageId();
            
            case "holding_page_id":
            return $this->getHoldingPageId();
            
            case "logo":
            return $this->getLogoAsset();
            
            case "language_code":
            return $this->getLanguageCode();
            
            case "home_page":
            case "homepage":
            return $this->getHomePage();
            
            case "organization":
            case "organisation":
            case "organization_name":
            case "organisation_name":
            return new SmartestString($this->getOrganisationName());
            
            case "organization_name_safe":
            case "organisation_name_safe":
            return new SmartestString($this->getOrganizationNameOrSiteName());
            
            case "pages_list":
            return $this->getPagesList();
            
            case "_admin_normal_pages_list":
            return $this->getPagesList(true, true);
            
            case "favicon_id":
            return $this->getFaviconId();
            
            case "favicon":
            return $this->getFavicon();
            
            case 'elasticsearch_index_name':
            return $this->getElasticSearchIndexName();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getFaviconId(){
        $ph = new SmartestPreferencesHelper;
        return $ph->getGlobalPreference('site_favicon_id', null, $this->getId());
    }
    
    public function setFaviconId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_favicon_id', $id, null, $this->getId());
    }
    
    public function getFavicon(){
        if($id = $this->getFaviconId()){
            $asset = new SmartestRenderableAsset;
            if($asset->find($id)){
                return $asset;
            }
        }
    }
    
    /** User Page **/
    
    public function getUserPageId(){
        $ph = new SmartestPreferencesHelper;
        return $ph->getGlobalPreference('site_user_page_id', null, $this->getId());
    }
    
    public function setUserPageId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_user_page_id', $id, null, $this->getId());
    }
    
    public function getUserPage(){
        $upid = $this->getUserPageId();
        $p = new SmartestPage;
        $p->find($upid);
        return $p;
    }
    
    /** Tag Page **/
    
    public function getTagPageId(){
        $ph = new SmartestPreferencesHelper;
        if($ph->getGlobalPreference('site_tag_page_id', null, $this->getId(), true)){
            return $ph->getGlobalPreference('site_tag_page_id', null, $this->getId());
        }else{
            // Migrate to new storage system
            $p = $this->_properties['tag_page_id'];
            $ph->setGlobalPreference('site_tag_page_id', $p, null, $this->getId());
            return $p;
        }
    }
    
    public function setTagPageId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_tag_page_id', $id, null, $this->getId());
    }
    
    public function getTagPage(){
        $tpid = $this->getTagPageId();
        $p = new SmartestPage;
        $p->find($tpid);
        return $p;
    }
    
    /** Search Page **/
    
    public function getSearchPageId(){
        $ph = new SmartestPreferencesHelper;
        if($ph->getGlobalPreference('site_search_page_id', null, $this->getId(), true)){
            return $ph->getGlobalPreference('site_search_page_id', null, $this->getId());
        }else{
            // Migrate to new storage system
            $p = $this->_properties['search_page_id'];
            $ph->setGlobalPreference('site_search_page_id', $p, null, $this->getId());
            return $p;
        }
    }
    
    public function setSearchPageId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_search_page_id', $id, null, $this->getId());
    }
    
    public function getSearchPage(){
        $pid = $this->getSearchPageId();
        $p = new SmartestPage;
        $p->find($pid);
        return $p;
    }
    
    /** Error Page **/
    
    public function getErrorPageId(){
        $ph = new SmartestPreferencesHelper;
        if($ph->getGlobalPreference('site_error_page_id', null, $this->getId(), true)){
            return $ph->getGlobalPreference('site_error_page_id', null, $this->getId());
        }else{
            // Migrate to new storage system
            $p = $this->_properties['error_page_id'];
            $ph->setGlobalPreference('site_error_page_id', $p, null, $this->getId());
            return $p;
        }
    }
    
    public function setErrorPageId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_error_page_id', $id, null, $this->getId());
    }
    
    public function getErrorPage(){
        $pid = $this->getErrorPageId();
        $p = new SmartestPage;
        $p->find($pid);
        return $p;
    }
    
    /** Holding Page **/
    
    public function getHoldingPageId(){
        $ph = new SmartestPreferencesHelper;
        if($ph->getGlobalPreference('site_holding_page_id', null, $this->getId(), true)){
            return $ph->getGlobalPreference('site_holding_page_id', null, $this->getId());
        }else{
            return null;
        }
    }
    
    public function setHoldingPageId($id){
        $ph = new SmartestPreferencesHelper;
        return $ph->setGlobalPreference('site_holding_page_id', $id, null, $this->getId());
    }
    
    public function getHoldingPage(){
        $pid = $this->getHoldingPageId();
        $p = new SmartestPage;
        $p->find($pid);
        return $p;
    }
	
}