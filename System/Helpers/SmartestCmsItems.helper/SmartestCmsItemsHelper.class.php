<?php

class SmartestCmsItemsHelper{
    
    protected $database;
    protected $_models;
    
    public function __construct(){
        
        $this->database = SmartestDatabase::getInstance('SMARTEST');
        
    }
    
    public function getModelFromId($id){
        
        if(isset($this->_models[$id])){
            return $this->_models[$id];
        }else{
            $m = new SmartestModel;
            if($m->find($id)){
                $this->_models[$id] = $m;
                return $this->_models[$id];
            }else{
                return null;
            }
        }
        
    }
    
    /* public function statusToMode($status){
        
        switch($status){
            
            case SM_STATUS_ALL:
            
            case SM_STATUS_HIDDEN:
            
        }
        
    } */
    
    public function modeToStatus($mode){
        
    }
    
    public function hydrateMixedListFromIdsArray($ids, $draft_mode=false, $keep_ids=false){
        
        $results = $this->getSquareDbDataFromIdsArray($ids, null, $draft_mode);
        $items = array();
        
        foreach($results as $item_id => $result){
            
            $first = reset($result);
            $model_id = $first['item_itemclass_id'];
            
            if($model = $this->getModelFromId($model_id)){
                // echo "got model from ID";
                $class_name = $model->getClassName();
                $item = new $class_name();
                $item->hydrateFromRawDbRecord($result);
                $item->setDraftMode($draft_mode);
                if($keep_ids){
                    $items[$item->getId()] = $item;
                }else{
                    $items[] = $item;
                }
                
            }
            
        }
        
        return $items;
        
    }
    
    public function hydrateMixedListFromIdsArrayPreservingOrder($ids, $draft_mode=false){
        
        $items = array();
        $raw_items = $this->hydrateMixedListFromIdsArray($ids, $draft_mode, true);
        
        foreach($ids as $id){
            if(isset($raw_items[$id])){
                $items[] = $raw_items[$id];
            }
        }
        
        return $items;
        
    }
    
    protected function _hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode=false){

       $results = $this->getSquareDbDataFromIdsArray($ids, $model_id, $draft_mode);
       $items = array();
       
       if($model = $this->getModelFromId($model_id)){

           $class_name = $model->getClassName();

           foreach($results as $item_id => $result){

               $item = new $class_name();
               $item->hydrateFromRawDbRecord($result);
               $item->setDraftMode($draft_mode);
               $items[$item->getId()] = $item;

           }

       }

       return $items;
    
    }
    
    public function hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode=false){
        
        return array_values($this->_hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode));
        
    }
    
    public function hydrateUniformListFromIdsArrayPreservingOrder($ids, $model_id, $draft_mode=false){
        
        $items = array();
        $raw_items = $this->_hydrateUniformListFromIdsArray($ids, $model_id, $draft_mode);
        
        foreach($ids as $id){
            if(isset($raw_items[$id])){
                $items[] = $raw_items[$id];
            }
        }
        
        return $items;
        
    }
    
    public function getRawDbDataFromIdsArray($ids, $model_id='', $draft_mode=false){
        
        if(!count($ids)){
            // If no IDs are given, don't bother running a query
            return array();
        }
        
        if(is_numeric($model_id)){
            // a model is specified
            $pre_sql = "SELECT itemproperty_id FROM ItemProperties WHERE itemproperty_itemclass_id='".$model_id."'";
            $r = $this->database->queryToArray($pre_sql);
            if(count($r)){
                // The model has properties
                $sql = "SELECT * FROM Items, ItemPropertyValues WHERE Items.item_deleted !=1 AND Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND Items.item_id IN ('".implode("','", $ids)."') AND Items.item_itemclass_id='".$model_id."'";
                if($draft_mode == 0){
                    $sql .= " AND item_public='TRUE'";
                }
                return $this->database->queryToArray($sql);
            }else{
                // The model does not have properties
                $sql = "SELECT * FROM Items WHERE Items.item_deleted !=1 AND Items.item_id IN ('".implode("','", $ids)."') AND Items.item_itemclass_id='".$model_id."'";
                if($draft_mode == 0){
                    $sql .= " AND item_public='TRUE'";
                }
                return $this->database->queryToArray($sql);
            }
        }else{
            // a model is not specified
            $sql = "SELECT * FROM Items, ItemPropertyValues WHERE Items.item_deleted !=1 AND Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND Items.item_id IN ('".implode("','", $ids)."')";
            if($draft_mode == 0){
                $sql .= " AND item_public='TRUE'";
            }
            // echo $sql;
            return $this->database->queryToArray($sql);
        }
        
    }
    
    public function getSquareDbDataFromIdsArray($ids, $model_id='', $draft_mode=false){
        
        $included_item_ids = array();
        $items = array();
        
        // print_r($ids);
        
        foreach($this->getRawDbDataFromIdsArray($ids, $model_id, $draft_mode) as $result){
            
            $items[$result['item_id']][$result['itempropertyvalue_property_id']] = $result;
            
        }
        
        return $items;
        
    }
    
    public function getItemIdsWithChangedPropertyValues($model_id=null, $site_id=null, $last_modification_after=null){
        
        $sql = "SELECT DISTINCT Items.item_id, Items.item_name FROM Items, ItemClasses, ItemPropertyValues WHERE Items.item_id=ItemPropertyValues.itempropertyvalue_item_id AND Items.item_itemclass_id=ItemClasses.itemclass_id AND ItemClasses.itemclass_type='SM_ITEMCLASS_MODEL' AND ItemPropertyValues.itempropertyvalue_draft_content != ItemPropertyValues.itempropertyvalue_content AND ItemClasses.itemclass_uses_draft_properties AND Items.item_deleted !=1";
        
        if(is_numeric($model_id)){
            $sql .= " AND Items.item_itemclass_id='".$model_id."'";
        }
        
        if(is_numeric($site_id)){
            $sql .= " AND Items.item_itemclass_id='".$site_id."'";
        }
        
        if(is_numeric($last_modification_after)){
            $sql .= " AND Items.item_modified > '".$last_modification_after."'";
        }
        
        $sql .= " ORDER BY Items.item_modified DESC";
        
        // echo $sql;
        
        $result = $this->database->queryToArray($sql);
        
        $ids = array();
        
        foreach($result as $r){
            $ids[] = $r['item_id'];
        }
        
        return $ids;
        
    }
    
    public function getItemsWithChangedPropertyValues($model_id=null, $site_id=null, $last_modification_after=null){
        
        $ids = $this->getItemIdsWithChangedPropertyValues($model_id, $site_id, $last_modification_after);
        
        // print_r($ids);
        
        if(is_numeric($model_id)){
            return $this->hydrateUniformListFromIdsArrayPreservingOrder($ids, $model_id, true);
        }else{
            return $this->hydrateMixedListFromIdsArrayPreservingOrder($ids, true);
        }
        
    }

}