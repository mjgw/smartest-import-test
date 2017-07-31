<?php

class SettingsAjax extends SmartestSystemApplication{

    public function tagsAutoComplete(){
        
        $db = SmartestDatabase::getInstance('SMARTEST');
        
        $string = SmartestStringHelper::sanitizeLookupValue($this->getRequestParameter('string'));
        
        $pre_sql = "SELECT * FROM Tags WHERE Tags.tag_name ='".SmartestStringHelper::toSlug($string, true)."'";
        $result = $db->queryToArray($pre_sql);
        
        if(count($result)){
            $allow_create = false;
        }else{
            $allow_create = true;
        }
        
        $this->send($allow_create, 'allow_create');
        
        $sql = "SELECT * FROM Tags WHERE (Tags.tag_label LIKE '%".$string."%' OR Tags.tag_name LIKE '%".$string."%')";
        
        if($this->requestParameterIsSet('avoid_ids')){
            if(is_array($this->getRequestParameter('avoid_ids'))){
                $avoid_ids = $this->getRequestParameter('avoid_ids');
            }else{
                $avoid_ids = explode(',', $this->getRequestParameter('avoid_ids'));
            }
            $sql .= " AND Tags.tag_id NOT IN ('".implode("','", $avoid_ids)."')";
        }
        
        $result = $db->queryToArray($sql);
        $tags = array();
        
        if(count($result)){
            foreach($result as $rt){
                $t = new SmartestTag;
                $t->hydrate($rt);
                $tags[] = $t;
            }
        }
        
        if($allow_create){
            $this->send(strip_tags($string), 'new_tag_label');
        }
        
        $this->send(count($tags), 'num_tags');
        $this->send($tags, 'tags');
        
    }
    
    public function deleteTagById(){
        
        if($this->getUser()->hasToken('delete_tags')){
        
            $t = new SmartestTag;
            $tag_id = (int) $this->getRequestParameter('tag_id');
            
            if($t->find($tag_id)){
                $t->delete();
            }
        
        }
        
        exit;
        
    }
    
    public function toggleTagFeatured(){
        
        $tag = new SmartestTag;
        
        if($tag->find($this->getRequestParameter('tag_id'))){
            
            $state = (int) ($this->requestParameterIsSet('featured') && SmartestStringHelper::toRealBool($this->getRequestParameter('featured')));
            $tag->setFeatured($state);
            $tag->save();
            
        }
        
    }
    
    public function createNewTag(){
        
        $tag_label = $this->getRequestParameter('new_tag_label');
        $tag_slug = SmartestStringHelper::toSlug($tag_label);
        
        $t = new SmartestTag;
        
        if(!$t->findBy('name', $tag_slug)){
            $t->setName($tag_slug);
            $t->setLabel($tag_label);
            $t->setType('SM_TAGTYPE_TAG');
            $t->setLanguage('eng');
            $t->save();
        }
        
        header('Content-Type: application/json; charset=UTF8');
        echo $t->__toJson();
        exit;
        
    }
    
    public function cleanDataCache(){
        
        if($this->getUser()->hasToken('clean_data_cache')){
            $result = SmartestCache::clean(true);
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($result);
        }
        
    }
    
    public function testServerSpeed(){
        
        if($this->getUser()->hasToken('test_server_speed')){
        
            $sql = "SELECT page_id FROM Pages WHERE page_deleted != 'TRUE' ORDER BY page_id DESC LIMIT 1";
            $db = SmartestPersistentObject::get('db:main');
            $r = $db->queryToArray($sql);
            $id = $r[0]['page_id'];
        
            $test_start_time = microtime(true);
            
            $p1 = new SmartestPage;
            $p1->find($id);
        
            for($i=0;$i<2000;$i++){
            
                // look it up and hydrate it by ID
                $p = new SmartestPage;
                $p->find($id);
            
                // access it via ArrayAccess
                $d = $p['title'];
                
                // convert type of one property (from string to integer)
                // $idsq = $p['id']*$p['id'];
            
            }
            
            $test_finish_time = microtime(true);
            $raw_speed_score = ($test_finish_time - $test_start_time)*1000;
            $test_time_taken = number_format($raw_speed_score, 2, ".", "");
        
            SmartestSystemSettingHelper::save('_server_speed_index', $test_time_taken);
            
            // $raw_speed_score = SmartestSystemSettingHelper::load('_server_speed_index');
            $this->send(is_numeric($test_time_taken), 'speed_score_available');
            $cats = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Info/serverspeed.yml');
            $speed_categories = $cats['levels'];
            $speed_categories[0] = null;
            $previous_category = array('description'=>'Unrated', 'image'=>'server-level-0.png', 'color'=>'333');
        
            $this->setTitle('About Smartest');
        
            ksort($speed_categories);
        
            $category = end($speed_categories);
            reset($speed_categories);
        
            foreach($speed_categories as $k => $sc){
          
                if($raw_speed_score < $k){
                    $category = $sc;
                    break;
                }else{
                    // $previous_category = $speed_categories[$k];
                    continue;
                }
            }
        
            $this->send($test_time_taken, 'speed_score');
            $this->send($category, 'speed_category_info');
        
        }else{
            
            // $this->addUserMessageToNextRequest("You do not have permission to test the server's speed.", SmartestUserMessage::ACCESS_DENIED);
            
        }
        
    }

}