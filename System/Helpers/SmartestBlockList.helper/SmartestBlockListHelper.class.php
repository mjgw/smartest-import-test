<?php

class SmartestBlockListHelper{
    
    protected $database;
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getBlockLists($site_id=null){
        
    }
    
    public function getBlockListStyles($site_id=null){
        
        $sql = "SELECT * FROM Tags WHERE Tags.tag_type='SM_TAGTYPE_BLOCKLISTSTYLE'";
        
        if(is_numeric($site_id)){
            $sql .= " AND Tags.tag_site_id='".$site_id."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $styles = array();
        
        foreach($result as $r){
            $s = new SmartestBlockListStyle;
            $s->hydrate($r);
            $styles[$s->getName()] = $s;
        }
        
        return $styles;
        
    }
    
    public function getOrCreateBlocklistForPage($page_id, $style_name='default'){
        
    }
    
}