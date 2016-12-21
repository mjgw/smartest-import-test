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

}