<?php

class SettingsAjax extends SmartestSystemApplication{

    public function tagsAutoComplete(){
        
        $string = SmartestStringHelper::sanitizeLookupValue($this->getRequestParameter('string'));
        
        $sql = "SELECT * FROM Tags WHERE (Tags.tag_label LIKE '%".$string."%' OR Tags.tag_name LIKE '%".$string."%')";
        
        if($this->requestParameterIsSet('avoid_ids')){
            if(is_array($this->getRequestParameter('avoid_ids'))){
                $avoid_ids = $this->getRequestParameter('avoid_ids');
            }else{
                $avoid_ids = explode(',', $this->getRequestParameter('avoid_ids'));
            }
            $sql .= " AND Tags.tag_id NOT IN ('".implode("','", $avoid_ids)."')";
        }
        
        $db = SmartestDatabase::getInstance('SMARTEST');
        $result = $db->queryToArray($sql);
        $tags = array();
        
        if(count($result)){
            foreach($result as $rt){
                $t = new SmartestTag;
                $t->hydrate($rt);
                $tags[] = $t;
            }
        }
        
        if(count($tags) == 0){
            $this->send(strip_tags($string), 'new_tag_label');
        }
        
        $this->send(count($tags), 'num_tags');
        $this->send($tags, 'tags');
        
    }
    
    public function deleteTagById(){
        
        $t = new SmartestTag;
        $tag_id = (int) $this->getRequestParameter('tag_id');
        
        if($t->find($tag_id)){
            $t->delete();
        }
        
        exit;
        
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

}