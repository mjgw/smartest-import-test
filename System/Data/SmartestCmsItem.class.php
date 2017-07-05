<?php

/**
* @package Smartest
* @subpackage CMS Object Model
* @author Marcus Gilroy-Ware <marcus@mjgw.com>
* SmartestCmsItem is the underlying class that is extended to create the objects that are created and edited in the CMS
* Please store any additional information on this class at: http://wiki.smartestproject.org/SmartestCmsItem
*/

class SmartestCmsItem extends SmartestObject implements SmartestGenericListedObject, SmartestStorableValue, SmartestSubmittableValue, SmartestDualModedObject, SmartestSearchableValue, SmartestJsonCompatibleObject{
	
	/** 
	* Description
	* @access protected
	* @var mixed
	*/
	protected $_item;
	
	/** 
	* Description
	* @access protected
	* @var SmartestModel
	*/
	protected $_model = null;
    protected $_model_varname = null;
	
	/** 
	* A list of the actual properties of the loaded object. The numeric keys are the primary keys of the properties in the Properties table.
	* @access protected
	* @var array
	*/
	protected $_properties = array();
	
	/** 
	* A list of all those properties that have been modified which is generated and updated automatically so that when the object is saved, only the properties in this list will be updated.
	* @access protected
	* @var array
	*/
	protected $_modified_properties = array();
	
	/** 
	* A list of any properties that are referred to by the user's code, but aren't linked to actual properties in the structure of the model.
	* @access protected
	* @var array
	*/
	protected $_overloaded_properties = array();
	
	/** 
	* A mapping of the items' property names to the ids of the properties.
	* @access protected
	* @var array
	*/
	protected $_properties_lookup = array();
	
	/** 
	* A mapping of the varnames of the properties to the ids of the properties, for speed.
	* @access protected
	* @var array
	*/
	protected $_varnames_lookup = array();
	
	/** 
	* Description
	* @access protected
	* @var array
	*/
	protected $_property_values_lookup = array();
	
	/** 
	* Description
	* @access protected
	* @var boolean
	*/
	
	protected $_came_from_database = false;
	protected $_model_built = false;
	protected $_lookups_built = false;
	protected $_save_errors = array();
	protected $_draft_mode = false;
	protected $_request;
	protected $_disabled_template_properties = array();
	protected $_item_chain_data;
	
	/** 
	* Description
	* @access protected
	* @var SmartestMysql
	*/
	protected $database;
	
	const NAME = '_SMARTEST_ITEM_NAME';
	const ID = '_SMARTEST_ITEM_ID';
	const WEB_ID = '_SMARTEST_ITEM_WEBID';
	const WEBID = '_SMARTEST_ITEM_WEBID';
	const LONG_ID = '_SMARTEST_ITEM_WEBID';
	const NUM_COMMENTS = '_SMARTEST_ITEM_NUM_COMMENTS';
	const NUM_HITS = '_SMARTEST_ITEM_NUM_HITS';
	const AVERAGE_RATING = '_SMARTEST_ITEM_AVG_RATING';
	
	const NOT_CHANGED = 100;
	const AWAITING_APPROVAL = 101;
	const CHANGES_APPROVED = 102;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		$this->_item = new SmartestItem;
		
		$this->generateModel();
		// $this->generatePropertiesLookup();
		$this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
		
		/* if(get_class($this) == 'SmartestCmsItem'){
		    throw new SmartestException('here');
		} */
		
	}
	
	private function generateModel(){
		
		if(isset($this->_model_id) && !$this->_model_built){
		
		    if(SmartestCache::hasData('model_properties_'.$this->_model_id, true)){
		        $result = SmartestCache::load('model_properties_'.$this->_model_id, true);
		    }else{
			    // gotta get that from the database too
			    $sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_model_id."'";
			    $result = $this->database->queryToArray($sql);
			    SmartestCache::save('model_properties_'.$this->_model_id, $result, -1, true);
		    } 
		
		    $properties = array();
            if(is_array($result)){
    		    foreach($result as $key => $raw_property){
		        
    		        $property = new SmartestItemPropertyValueHolder;
    		        $property->hydrate($raw_property);
		        
    		        if($property->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
    		            // print_r($property->getOriginalDbRecord());
    		        }
		        
    		        $this->_properties[$raw_property['itemproperty_id']] = $property;
		        
    		    }
            }else{
                SmartestLog::getInstance('system')->log('Result from properties query should be array. '.gettype($result).' given instead. (Model ID: '.$this->_model_id.')', SmartestLog::WARNING);
            }
		    
		    $this->_model_built = true;
		
	    }
		
	}
	
	public function __call($name, $args){
		  throw new SmartestException("Call to undefined function: ".get_class($this).'->'.$name.'()');
	}
	
	public function getPropertyVarNames(){
	    return array_keys($this->_varnames_lookup);
	}
	
	public function setDraftMode($mode){
	    $this->_draft_mode = (bool) $mode;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	// The next three methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_item->getId();
    }
    
    public function hydrateFromStorableFormat($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }
    
    public function hydrateFromFormData($v){
        $r = $this->find((int) $v);
        return $r;
    }
    
    // Convenience function to provide controller instance internally
    protected function getController(){
        return SmartestPersistentObject::get('controller');
    }
    
    public function disableTemplateProperty($property_id){
        $this->_disabled_template_properties[$property_id] = true;
    }
    
    public function getModelVarname(){
        if(isset($this->_model_varname)){
            return $this->_model_varname;
        }else{
            if($this->_model instanceof SmartestModel){
                $this->_model_varname = SmartestStringHelper::toVarName($this->_model->getName());
                return $this->_model_varname;
            }else{
                $class_name = get_class($this);
                if($class_name == 'SmartestCmsItem'){
                    $this->_model_varname = SmartestStringHelper::toVarName($this->getModel()->getName());
                    return $this->_model_varname;
                }else{
                    $class_name = preg_replace('/([a-z])([A-Z])/', "$1_$2", $class_name);
                    $class_name =  SmartestStringHelper::toVarName($class_name);
                    $this->_model_varname = $class_name;
                    return $this->_model_varname;
                }
            }
        }
    }
	
	public function offsetExists($offset){
	    return ($this->_item->offsetExists($offset) || isset($this->_varnames_lookup[$offset]) || in_array($offset, array('_workflow_status', '_model', '_properties')));
	}
	
	public function offsetGet($offset){
	    
	    if(defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && constant('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && defined('SM_CMS_PAGE_ID')){
		    $dah = new SmartestDataAppearanceHelper;
            $dah->setItemAppearsOnPage($this->getId(), constant('SM_CMS_PAGE_ID'));
		}
	    
	    if($offset == 'name'){
	        return new SmartestString($this->getName());
	    }
        
	    if($offset == '_php_class'){
	        return get_class($this);
	    }
        
        if($offset == '_type_label'){
            return $this->_item->getModel()->getName();
        }
        
        if($offset == 'has_tag'){
	        return new SmartestTagPresenceChecker($this->getTags());
	    }
        
        if($offset == '_is_linkable'){
            return $this->isLinkable();
        }
        
        if(isset($this->_parent_item_slug) && $offset == $this->_parent_item_slug && $this->getModel()->getType() == 'SM_ITEMCLASS_MT1_SUB_MODEL'){
            return $this->getParentItem();
        }
        
        if(is_array($this->_many_to_one_sub_models) && array_key_exists($offset, $this->_many_to_one_sub_models)){
            return new SmartestArray($this->getSubModelItems($this->_many_to_one_sub_models[$offset]));
        }
	    
        if(isset($this->_varnames_lookup[$offset])){
	        
            if(isset($this->_disabled_template_properties[$this->_varnames_lookup[$offset]])){
	            return "Recursion disallowed";
	        }else{
                $v = $this->getPropertyValueByNumericKey($this->_varnames_lookup[$offset], $this->getDraftMode(), true);
                
                if($v instanceof SmartestAsset){
                    $v->setDraftMode($this->getDraftMode());
                }
                
	            if(is_null($v)){
                    // Null value returned. Should be logged.
	                return new SmartestString('');
	            }else{
	                return $v;
                }
            }
    
        }else if($this->_item->offsetExists($offset)){
	        
	        return $this->_item->offsetGet($offset);
	        
	    }else if(isset($this->_many_to_one_sub_models) && count($this->_many_to_one_sub_models) && isset($this->_many_to_one_sub_models[$offset])){
	        
            return $this->getSubModelItems($this->_many_to_one_sub_models[$offset]);
            
	    }else{
	        
	        switch($offset){
	            
	            case "_workflow_status":
	            
	            switch($this->getWorkflowStatus()){
            	    
            	    case self::NOT_CHANGED:
            	    return 'Not changed';
            	    break;
            	    
            	    case self::CHANGES_APPROVED:
            	    return 'Approved and ready for publishing';
            	    break;
            	    
            	    default:
            	    return 'Awaiting approval';
            	    break;
            	}
            	
	            break;
	            
	            case 'label':
	            return $this->getItem()->getName();
                
                case '_editor_name':
                case 'editor_name':
                return $this->getEditorName();
	            
	            case 'related':
	            // Sergiy - Moved your if statement to here: (Marcus)
	            return $this->getItem()->getRelatedContentForRender($this->getDraftMode());
	            
                case 'public_comments':
	            case 'comments':
	            return $this->getItem()->getPublicComments();
	            
                case 'num_public_comments':
	            case 'num_comments':
	            return $this->getItem()->getNumApprovedPublicComments();
	            
                case 'action_url':
                return $this->getItem()->getActionUrl();
                
	            case '_php_class':
	            return get_class($this);
	            
	            case 'url':
	            case 'permalink':
	            return $this->getUrl();
	            
	            case 'tags_cs_string':
	            return $this->_item->getTagsAsCommaSeparatedString();
	            
	            case 'absolute_uri':
	            case 'absolute_url':
                // echo "test";
	            return $this->getAbsoluteUri();
	            
	            // case 'description':
	            case '_description':
	            return $this->getDescriptionFieldContents();
	            
	            case '_auto_date':
                case '_date':
                $d = $this->getDate();
                if($d instanceof SmartestDateTime){
                    return $d;
                }else{
                    return new SmartestDateTime($d);
                }
                
	            case '_auto_date_raw':
                case '_date_raw':
	            return (int) $this->getDate();
	            
	            case '_is_published':
	            return new SmartestBoolean($this->isPublished());
	            
	            case '_byline':
	            return SmartestStringHelper::toCommaSeparatedList($this->getItem()->getAuthors());
	            
	            case '_model':
	            return $this->getModel();
	            
	            case '_properties':
	            return $this->getProperties();
	            
	            case '_editable_properties':
	            return $this->getProperties();
	            
	            case '_draft_mode':
	            return new SmartestBoolean($this->_draft_mode);
	            
	            case '_meta_page':
	            if($p = $this->getMetaPage()){
	                return $p;
                }else{
                    break;
                }
                
                case '_json':
                return $this->__toJson();
                
                case '_json_pretty':
                return $this->__toJsonPretty();
                
                case '_auto_thumbnail':
                case '_thumbnail':
                return $this->getDefaultThumbnailImage();
                
                case '_site':
                return $this->getSite();
                
                case "_type_varname":
                case "_type":
                return SmartestStringHelper::toVarName($this->_item->getModel()->getName());
                
                case 'empty':
                return !is_numeric($this->_item->getId());
	            
	        }
	        
	        $infn = SmartestStringHelper::toVarName($this->getModel()->getItemNameFieldName());
	        if($offset == $infn){
	            return new SmartestString($this->getName());
	        }
	        
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
	
	public function getCacheFiles(){
	    return $this->getItem()->getCacheFiles();
	}
	
	private function getField($field_name, $draft=false){
	    
	    $t = $this->_properties[$this->_properties_lookup[$field_name]]->getTypeInfo();
        
        if(array_key_exists($field_name, $this->_properties_lookup)){
		    if($this->_properties[$this->_properties_lookup[$field_name]] instanceof SmartestItemPropertyValueHolder){
			    
			    // return $this->_properties[$this->_properties_lookup[$field_name]];
			    if($this->_properties[$this->_properties_lookup[$field_name]]->getData() instanceof SmartestItemPropertyValue){
			        
			        if($draft){
		                return $this->_properties[$this->_properties_lookup[$field_name]]->getData()->getDraftContent();
		            }else{
		                return $this->_properties[$this->_properties_lookup[$field_name]]->getData()->getContent();
		            }
		        }else{
		            
		            if($t['valuetype'] != 'auto'){
		            
    		            // no value found, so create one
    		            $ipv = new SmartestItemPropertyValue;
        	            $ipv->setPropertyId($this->_properties[$this->_properties_lookup[$field_name]]->getId());
        	            $ipv->setItemId($this->getItem()->getId());
        	            $ipv->setDraftContentId($this->_properties[$this->_properties_lookup[$field_name]]->getDefaultValue());
        	            // var_dump($this->_properties[$this->_properties_lookup[$field_name]]->getDefaultValue());
        	            $ipv->save();
    	            
        	            if($draft){
        	                return $ipv->getDraftContent();
        	            }else{
        	                return null;
        	            }
    	            
	                }
		        }
		    }
		}else if(array_key_exists($field_name, $this->_overloaded_properties)){
			return $this->_overloaded_properties[$field_name];
		}else{
			return null;
		}
	}
	
	private function setField($field_name, $value){
		if(array_key_exists($field_name, $this->_properties_lookup)){
			// field being set is part of the model and corresponds to a column in the db table
			
			// $this->_properties[$this->_properties_lookup[$field_name]]->setDraftContent($value);
			$this->setPropertyValueByNumericKey($this->_properties_lookup[$field_name], $value);
			
			// $this->_modified_properties[$this->_properties_lookup[$field_name]] = $value;
		}else{
			// field being set is an overloaded property, which is stored, but not retrieved from or stored in the db
			$this->_overloaded_properties[$field_name] = $value;
			
		}
		
		return true;
	}
	
	public function setModelId($id){
	    
	    $id = (int) $id;
	    
	    if($this instanceof SmartestCmsItem && !$this->_model_built){
	        
	        $this->_model_id = $id;
	        $this->_model = new SmartestModel;
	        
	        if(!$this->_model->find($this->_model_id)){
	            throw new SmartestException('The model ID '.$this->_model_id.' doesn\'t exist.');
	        }
	        
	        if(!$this->_model_built){
    	        $this->generateModel();
    	    }
	        
	    }
	    
	}
	
	public function getRequest(){
	    return $this->_request;
	}
	
	public function setSiteId($id){
	    if(is_object($this->_item)){
	        $this->_item->setSiteId($id);
        }
	}
	
	public function getSiteId(){
        if ($id = $this->_item->getSiteId()) return (int) $id;
	}
    
	public function getSite(){
	    return $this->_item->getHomeSite();
	}
    
    public function getMetapageId(){
        if($id = $this->_item->getMetapageId()) return (int) $id;
	}
	
	public function getMetaPage(){
	    return $this->_item->getMetapage();
	}
    
    public function isLinkable(){
        $p = $this->getMetapage();
        return (is_object($p) && $p->getId());
    }
	
	public function getDefaultThumbnailImage(){
	    if($propertyid = $this->getModel()->getDefaultThumbnailPropertyId()){
	        return $this->getPropertyValueByNumericKey($propertyid, true);
	    }
	}
	
	public function getItemSpaceDefinitions($draft=false){
	    return $this->_item->getItemSpaceDefinitions($draft);
	}
	
	public function hydrateNewFromRequest($request_data, $site_id=''){
	    
	    if(is_array($request_data)){
	            
		    $this->_item->setName(SmartestStringHelper::sanitize($request_data['_name']));
		    $this->_item->setLanguage(SmartestStringHelper::sanitize($request_data['_language']));
            
            $this->_item->setPublic('FALSE');
            
            $this->_item->setItemclassId($this->_model_id);
            $this->_item->setSlug(SmartestStringHelper::toSlug($this->_item->getName(), true), $site_id);
            $this->_item->setCreated(time());
            $this->_item->setModified(time()+2); // this is to make it show up on the approval todo list
            
            if(SmartestPersistentObject::get('user') instanceof SmartestUser){
                $this->_item->setCreatedbyUserid(SmartestPersistentObject::get('user')->getId());
            }
	        
	        /* foreach($request_data as $key => $value){
	        
	            if(isset($this->_properties[$key]) && !in_array($key, array('_name', '_is_public')) && is_object($this->_properties[$key])){
	                
	                $this->setPropertyValueByNumericKey($key, $value);
                    
	            }else{
	                // echo "property value object not found<br />";
	                // property object doesn't exist
	                // $this->_save_errors[$key] = $value;
	                // TODO: decide what to do here and implement it here
	            }
	        } */
	        
	        switch($this->getModel()->getLongIdFormat()){
	            
	            case '_STD':
	            case '':
	            $webid = SmartestStringHelper::random(32);
	            break;
	            
	            case '_UUID':
	            $webid = SmartestStringHelper::generateUUID();
	            break;
	            
	            default:
	            $webid = SmartestStringHelper::randomFromFormat($this->getModel()->getLongIdFormat());
	            
	        }
	        
	        $this->_item->setWebid($webid);
	        
	        foreach($this->getModel()->getProperties() as $p){
                if(isset($request_data[$p->getId()])){
                    $this->setPropertyValueByNumericKey($p->getId(), $request_data[$p->getId()]);
                }
            }
	        
	        if(!count($this->_save_errors)){
	            return true;
	        }else{
	            return false;
	        }
	        
	    }else{
	        
	        // error - expecting data in associative array
	        
	    }
	}
	
	public function find($id, $draft=false){
		
		if($this->_item->find($id)){
		    $this->_runPostSimpleItemFind($id, $draft);
		    return true;
	    }else{
	        return false;
	    }
		
	}
	
	public function findBy($field, $id, $draft=false){
		
		if($this->_item->findBy($field, $id)){
		    $this->_runPostSimpleItemFind($this->_item->getId(), $draft);
		    return true;
	    }else{
	        return false;
	    }
		
	}
	
	private function _runPostSimpleItemFind($id, $draft){
	    
	    $this->_came_from_database = true;
	    
	    if(!$this->_model_built){
	        $this->_model_id = $this->_item->getItemclassId();
	        $this->generateModel();
	    }
	    
	    if(SmartestCache::hasData('model_properties_'.$this->_model_id, true)){
		    $properties_result = SmartestCache::load('model_properties_'.$this->_model_id, true);
	    }else{
		    // gotta get that from the database too
		    $properties_sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_model_id."' AND itemproperty_varname !='hydrate'";
		    $properties_result = $this->database->queryToArray($properties_sql);
		    SmartestCache::save('model_properties_'.$this->_model_id, $result, -1, true);
	    }
	    
	    // loop through properties first time, just setting up empty holder items
	    foreach($properties_result as $property){
	        
	        if(!isset($this->_properties[$property['itemproperty_id']]) || !is_object($this->_properties[$property['itemproperty_id']])){
	            SmartestCache::clear('model_properties_'.$this->_model_id, true);
	            $this->_properties[$property['itemproperty_id']] = new SmartestItemPropertyValueHolder;
	            
	        }
	        
		    $this->_properties[$property['itemproperty_id']]->hydrate($property);
		    $this->_properties[$property['itemproperty_id']]->setItem($this);// Sergiy: &$this=>$this for PHP 5.4
		    // $this->_properties[$property['itemproperty_id']]->setContextualItemId($this->_item->getId());
	    }
	    
	    $values_sql = "SELECT * FROM ItemPropertyValues WHERE itempropertyvalue_item_id='$id'";
	    $result = $this->database->queryToArray($values_sql, true);
	    
	    // then loop through properties again, making sure all are given either a ipv from the last db query, or given a new one if none was found.
	    // these ifs and buts shouldn't run very often if everything is working as it should
		
		foreach($result as $propertyvalue){
		    
		    $ipv = new SmartestItemPropertyValue;
		    $ipv->hydrate($propertyvalue);
		    $ipv->setItem($this);
		    
            // if the property object does not exist, create and hydrate it
            
            if(!isset($this->_properties[$ipv->getPropertyId()]) || !is_object($this->_properties[$ipv->getPropertyId()])){
                $this->_properties[$ipv->getPropertyId()] = new SmartestItemPropertyValueHolder;
		    }
		    
		    if(!$this->_properties[$ipv->getPropertyId()]->hasData()){
		        $this->_properties[$ipv->getPropertyId()]->hydrateValueFromIpvObject($ipv);
            }
		    
		    // give the property the current item id, so that it knows which ItemPropertyValue record to retrieve in any future operations (though it isn't needed in this one)
		    // $this->_properties[$ipv->getPropertyId()]->setContextualItemId($this->_item->getId());
		    
		    // $this->_properties[$ipv->getPropertyId()]->hydrateValueFromIpvArray($propertyvalue);
		    
	    } 
	    
	    // all properties should now be represented.
	    // last jobs are:
	    //// 1. to make sure all property objects have value objects
	    //// 2. to give the value objects info about their properties, without doing more queries.
	    foreach($this->_properties as $pid=>$p){
	        // this function will automatically crate a value and save it
	        $p->getData()->hydratePropertyFromExteriorArray($p->getOriginalDbRecord());
	    }
	    
	}
	
	public function hydrate($id, $draft=false){
	    return $this->find($id, $draft);
	}
	
	public function isHydrated(){
	    // var_dump($this->getItem()->isHydrated());
	    // return $this->getItem()->isHydrated();
	    return $this->_came_from_database;
	}
	
	// Raw data from an SQL query that retrieves both Items and ItemPropertyValues can be passed to the item via this function
	public function hydrateFromRawDbRecord($record){
	    if($this->isHydrated()){
	        throw new SmartestException("Tried to hydrate an already-hydrated SmartestCmsItem object.");
	    }else{
	        
	        $item = new SmartestItem;
	        $item->hydrate(reset($record));
	        $this->_item = $item;
	        
	        if($this->_model_built){
	            foreach($this->_properties as &$p){
	                // $p is an itempropertyvalueholder object
                    if(isset($record[$p->getId()])){
	                    $p->hydrateValueFromIpvArray($record[$p->getId()], $this); // Sergiy: &$this=>$this for PHP 5.4
                    }
	            }
	        }
	    }
	}
	
	public function getId(){
		return (int) $this->getItem()->getId();
	}
	
	public function getName(){
		$n = $this->getItem()->getName();
		if($this->_draft_mode && $this->getItem()->getPublic() != 'TRUE' && $this->getRequest()->getAction() == "renderEditableDraftPage"){
		    $n = '*'.$n;
		}
		return $n;
	}
    
    final public function getEditorName(){
        return $this->getItem()->getName();
    }
	
	public function setName($name){
		return $this->getItem()->setName($name);
	}
	
	public function setSlug($slug, $site_id=''){
	    return $this->getItem()->setSlug(SmartestStringHelper::toSlug($name), $site_id);
	}
	
	public function setIsPublic($p){
        $p = SmartestStringHelper::toRealBool($p);
	    return $this->getItem()->setIsPublic($p);
	}
	
	public function setLanguage($lang_code){
		return $this->getItem()->setLanguage($lang_code);
	}
    
    public function setSubModelItemOrderIndex($order_index){
        return $this->getItem()->setOrderIndex($order_index);
    }
	
	// needed for compliance with SmartestGenericListedObject
	public function getTitle(){
	    return $this->getName();
	}
    
	public function getFormattedTitle(){
	    return $this->getName();
	}
	
	public function getDate(){
        
        // get model's default date property ID. If there is a property set, retrieve the value of thatproperty for this item.
        // echo ;
	    
        if(is_numeric($this->getModel()->getDefaultDatePropertyId()) && $value = $this->getPropertyValueByNumericKey($this->getModel()->getDefaultDatePropertyId(), true)){
        // if($propertyid = $this->getModel()->getDefaultDatePropertyId() && $value = $this->getPropertyValueByNumericKey($this->getModel()->getDefaultDatePropertyId(), true)){
	        $propertyid = $this->getModel()->getDefaultDatePropertyId();
            // echo $propertyid.' ';
            $value = $this->getPropertyValueByNumericKey($propertyid, true);
            // print_r($value);
            return $value;
            
	    }elseif(isset($this->_varnames_lookup['date_published'])){
            
            $v = $this->getPropertyValueByNumericKey($this->_varnames_lookup['date_published'], $this->getDraftMode(), true);
            
            if($v instanceof SmartestDateTime){
                return $v->getUnixFormat();
            }else{
        	    if($this->getDraftMode()){
                    return $this->getItem()->getCreated();
                }else{
                    return $this->getItem()->getLastPublished();
                }
            }
            
        }else{
    	    if($this->getDraftMode()){
                return $this->getItem()->getCreated();
            }else{
                return $this->getItem()->getLastPublished();
            }
        }
        
	}
	
	public function getDescription(){
	    return $this->getDescriptionFieldContents();
	}
	
	public function getSlug(){
		return $this->getItem()->getSlug();
	}
	
	public function getWebid(){
		return $this->getItem()->getWebid();
	}
    
    public function getRelatedItems(){
        return $this->getItem()->getRelatedItems($this->getDraftMode());
    }
	
	public function getIsPublic(){
		return ($this->getItem()->getPublic() == 'TRUE') ? 'TRUE' : 'FALSE';
	}
	
	public function isPublished(){
	  return ($this->getItem()->getPublic() == 'TRUE') ? true : false;
	}
	
	public function getItem(){
		return $this->_item;
	}
	
	public function getLinkContents(){
	    
	    if($this->getMetapageId()){
	        $page_id = $this->getMetapageId();
	    }else if($this->getModel()->getDefaultMetapageId()){
	        $page_id = $this->getModel()->getDefaultMetapageId();
	    }else{
	        return null;
	    }
	    
	    return 'metapage:id='.$page_id.':id='.$this->getId();
	    
	}
	
	public function getLinkObject(){
	    
	    // $link = SmartestCmsLinkHelper::createLink($this->getLinkContents(), array());
	    $link = SmartestCmsLinkHelper::createLinkFromCmsItem($this, array());
	    return $link;
	    
	}
	
	public function getUrl(){
	    
	    // $link = SmartestCmsLinkHelper::createLink('metapage:id='.$page_id.':id='.$this->getId(), 'Raw Link Params: '.'metapage:id='.$page_id.':id='.$this->getId());
	    $link = $this->getLinkObject();
	    
	    if($link->hasError()){
	        // echo $link->getError();
	        // return '#';
	    }else{
	        return $link->getUrl(false, true);
        }
	    
	}
	
	public function getAbsoluteUri(){
	    return $this->getLinkObject()->getAbsoluteUrlObject();
	}
    
    public function getSearchQueryMatchableValue(){
        return $this->getEditorName();
    }
	
	public function getModel(){
	    
        if(!$this->_model && is_object($this->_item) && $this->_item->getItemclassId()){
	        $modelclass = isset($this->_model_class) ? $this->_model_class : 'SmartestModel';
            $model = new $modelclass;
	        $model->find($this->_item->getItemclassId());
	        $this->_model = $model;
	    }else if(!$this->_model && $this->_model_id){
	        $model = new SmartestModel;
	        if($model->find($this->_model_id)){
	            $this->_model = $model;
	        }
	    }
	    
	    return $this->_model;
	    
	}
	
	public function getModelId(){
	    return $this->_item->getItemclassId();
	}
    
    public function getParentItemId(){
        return $this->_item->getParentId();
    }
    
    public function setParentItemId($id){
        $this->_item->setParentId($id);
    }
    
    public function getParentItem(){
        if(isset($this->_parent_item) && is_object($this->_parent_item)){
            return $this->_parent_item;
        }else{
            if($this->getModel()->getType() == 'SM_ITEMCLASS_MT1_SUB_MODEL'){
                if($parent_item_id = $this->getParentItemId()){
                    if($parent_item = SmartestCmsItem::retrieveByPk($parent_item_id)){
                        $this->_parent_item = $parent_item;
                        $this->_parent_item->setDraftMode($this->getDraftMode());
                        return $this->_parent_item;
                    }else{
                        // an item with this ID couldn't be found!
                        throw new SmartestException('No item with the ID \''.$parent_item_id.'\' could be found.');
                    }
                }else{
                    // There is no parent item ID!
                    throw new SmartestException('No parent item ID for item ID \''.$this->getId().'\' could be found.');
                    return null;
                }
            }else{
                return null;
            }
        }
        
    }
	
	public function getDescriptionField(){
	    
	    // default_description_property_id
	    if($this->getModel()->getDefaultDescriptionPropertyId()){
	        $property_id = $this->getModel()->getDefaultDescriptionPropertyId();
	        $property = $this->getPropertyByNumericKey($property_id);
	        return $property;
	    }else{
	        return null;
	    }
	    
	}
	
	public function getDescriptionFieldContents(){
	    
	    $property = $this->getDescriptionField();
	    
	    if(is_object($property)){
	        
	        $type_info = $property->getTypeInfo();
	        
	        if($property->getDatatype() == 'SM_DATATYPE_ASSET'){
	            $asset = new SmartestRenderableAsset;
	            
	            if($asset = $this->getPropertyValueByNumericKey($property->getId())){
	                // get asset content
	                return $asset;
	            }else{
	                // throw new SmartestException(sprintf("Asset with ID %s was not found.", $this->getPropertyValueByNumericKey($property_id)));
	                return null;
	            }
	            
	        }else{
	            return $this->getPropertyValueByNumericKey($property->getId());
	        }
	        
	    }else{
	        if($this->getModel()->getDefaultDescriptionPropertyId()){
	            throw new SmartestException(sprintf("Specified model description property with ID '%s' is not an object.", $property_id));
            }else{
                SmartestLog::getInstance('system')->log("Model '".$this->getModel()->getName().'\' does not have a description property, so no description can be given in content mixture.');
            }
	    }
	    
	}
    
    public function getPublicComments(){
        return $this->getItem()->getPublicComments();
    }
    
    public function getNumPublicComments(){
        return $this->getItem()->getNumApprovedPublicComments();
    }
    
    public function getPrivateComments(){
        return $this->getItem()->getPrivateComments();
    }
    
    public function getNumPrivateComments(){
        return $this->getItem()->getNumPrivateComments();
    }
	
	public function compile($draft=false, $numeric_keys=false){
	    return $this->__toArray($draft, $numeric_keys);
	}
	
	public function __toArray($draft=false, $numeric_keys=false, $get_all_fk_property_options=false){
		// return associative array of property names and values
		$result = array();
		
		$result = $this->_item->__toArray(true);
		
		foreach($this->_varnames_lookup as $vn => $id){
		    
		    if($numeric_keys){
		        $key = $id;
		    }else{
		        $key = $vn;
		    }
		    
		    if($draft){
		        if(isset($this->_properties[$id]) && is_object($this->_properties[$id]->getData())){
		            $result[$key] = $this->_properties[$id]->getData()->getDraftContent();
	            }
	        }else{
	            if(isset($this->_properties[$id]) && is_object($this->_properties[$id]->getData())){
	                $result[$key] = $this->_properties[$id]->getData()->getContent();
                }
	        }
		}
		
		switch($this->getWorkflowStatus()){
		    case self::NOT_CHANGED:
		    $result['_workflow_status'] = 'Not changed';
		    break;
		    case self::CHANGES_APPROVED:
		    $result['_workflow_status'] = 'Approved and ready for publishing';
		    break;
		    default:
		    $result['_workflow_status'] = 'Awaiting approval';
		    break;
		}
		
		if(is_object($this->getModel())){
		    $result['_model'] = $this->getModel()->__toArray();
	    }
	    
		$result['_properties'] = $this->getPropertiesAsArrays($numeric_keys, $get_all_fk_property_options);
		
		ksort($result);
		
		return $result;
	}
	
	public function __toSimpleObject($basic_info_only=false){
	    
	    $obj = new stdClass;
	    $obj->name = $this->getName();
	    $obj->id = (int) $this->getId();
        $obj->webid = $this->getWebId();
	    $obj->slug = $this->getSlug();
        $obj->public = $this->isPublished();
        $obj->object_type = 'item';
        $obj->model = $this->_item->getModel()->getName();
        
        if($this->getMetapageId()){
            $obj->uri = $this->getAbsoluteUri()->__toString();
        }
	    
	    if(!$basic_info_only){
	        foreach($this->getProperties() as $p){
                $vn = $p->getVarname();
                $val = $p->getData()->getContent();
                if($p->getDatatype() == 'SM_DATATYPE_ASSET' || $p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                    if($val->getId()){
                        $obj->$vn = $p->getData()->getContent()->__toSimpleObjectForParentObjectJson();
                    }else{
                        $obj->$vn = null;
                    }
                }else{
                    if($val instanceof SmartestJsonCompatibleObject){
                        $obj->$vn = $p->getData()->getContent()->stdObjectOrScalar();
                    }else{
                        $obj->$vn = null;
                    }
                }
	        }
	    }
	    
	    return $obj;
	    
	}
    
    public function __toSimpleObjectForParentObjectJson(){
        
	    $obj = new stdClass;
	    $obj->name = $this->getName();
	    $obj->id = (int) $this->getId();
        $obj->webid = $this->getWebId();
	    $obj->slug = $this->getSlug();
        $obj->public = $this->isPublished();
        $obj->object_type = 'item';
        $obj->model = $this->_item->getModel()->getName();
        
        if($this->getMetapageId()){
            $obj->uri = $this->getAbsoluteUri()->__toString();
        }
        
        return $obj;
        
    }
    
    public function stdObjectOrScalar(){
        return $this->__toSimpleObjectForParentObjectJson();
    }
	
	public function __toJson($basic_info_only=false){
	    return json_encode($this->__toSimpleObject($basic_info_only));
	}
    
	public function __toJsonPretty($basic_info_only=false){
	    
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
	        return json_encode($this->__toSimpleObject($basic_info_only), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        }else{
            return json_encode($this->__toSimpleObject($basic_info_only), JSON_UNESCAPED_SLASHES);
        }
	    
	}
	
	public function getProperties($numeric_keys=false){
	    
	    $result = array();
	    
        foreach($this->_varnames_lookup as $vn => $id){
	    
	        if($numeric_keys){
	            $key = $id;
	        }else{
	            $key = $vn;
	        }
	    
	        $result[$key] = $this->_properties[$id];
	        
		}
		
	    return $result;
	    
	}
	
	public function getPropertyValueHolders(){
	    return $this->getProperties();
	}
	
	public function getPropertiesThatRequireDuplicationDecision(){
	    
	    $properties = array();
	    
	    foreach($this->getProperties() as $p){
	        $info = $p->getTypeInfo();
	        if($info['id'] == 'SM_DATATYPE_ASSET' || $info['id'] == 'SM_DATATYPE_TEMPLATE'){
	            $properties[] = $p;
	        }
	    }
	    
	    return $properties;
	    
	}
	
	public function getStringProperties(){
	    
	    $type_codes = SmartestDataUtility::getDataTypeCodesByValueType('string');
	    $sql = "SELECT itemproperty_id, itemproperty_varname FROM ItemProperties WHERE itemproperty_datatype IN ('".implode("','", $type_codes)."') AND itemproperty_itemclass_id='".$this->_item->getItemclassId()."' ORDER BY itemproperty_order_index ASC;";
	    // $result = $this->database->queryToArray($sql);
	    
	    $signifiers = $this->database->queryFieldsToArrays(array('itemproperty_varname', 'itemproperty_id'), $sql);
	    // print_r($signifiers['itemproperty_id']);
	    
	    // return $signifiers['itemproperty_varname'];
	    
	    /* $varnames = array();
	    $ids = array();*/
	    $properties = array();
	    
	    /* foreach($result as $r){
	        $varnames[] = $r['itemproperty_varname'];
	        $ids[] = $r['itemproperty_id'];
	    } */
	    
	    foreach($this->_properties as $key => $propertyvalueholder){
	        // echo $key.' ';
	        if(in_array($key, $signifiers['itemproperty_id'])){
	            $properties[] = $propertyvalueholder;
	        }
	    }
	    
	    return $properties;
	    
	}
	
	public function getDropdownMenuProperties(){
	    
	    $sql = "SELECT itemproperty_id, itemproperty_varname FROM ItemProperties WHERE itemproperty_datatype = 'SM_DATATYPE_DROPDOWN_MENU' AND itemproperty_itemclass_id='".$this->_item->getItemclassId()."' ORDER BY itemproperty_order_index ASC;";
	    $signifiers = $this->database->queryFieldsToArrays(array('itemproperty_varname', 'itemproperty_id'), $sql);
	    $properties = array();
	    
	    foreach($this->_properties as $key => $propertyvalueholder){
	        if(in_array($key, $signifiers['itemproperty_id'])){
	            $properties[] = $propertyvalueholder;
	        }
	    }
	    
	    return $properties;
	    
	}
	
	public function getPropertiesAsArrays($numeric_keys=false, $get_all_fk_property_options=false){
	    
	    $result = array();
	    
	    foreach($this->_varnames_lookup as $fn => $id){
	    
	        if($numeric_keys){
	            $key = $id;
	        }else{
	            $key = $vn;
	        }
	    
	        $result[$key] = $this->_properties[$id]->__toArray();
	        $result[$key]['_type_info'] = $this->_properties[$id]->getTypeInfo();
            
            if($this->_properties[$id]->isForeignKey() && $get_all_fk_property_options){
                $result[$key]['_options'] = $this->_properties[$id]->getPossibleValuesAsArrays();
            }
	        
		}
		
		return $result;
		
	}
    
    public function getSubModelItems($sub_model_id){
        
        $m = new SmartestSubModel;
        
        if($m->find($sub_model_id)){
            
            $ids = array();
            
            if($m->getType() == 'SM_ITEMCLASS_MT1_SUB_MODEL'){
            
                $sql = "SELECT Items.item_id FROM Items WHERE item_itemclass_id='".$m->getId()."' AND item_parent_id='".$this->getId()."'";
            
                if($this->getDraftMode()){
                    $set_item_draft_mode = true;
                }else{
                    $set_item_draft_mode = false;
                    $sql .= " AND item_public='TRUE' AND item_is_archived !='1'";
                }
                
                $th = new SmartestDatabaseTableHelper;
                
                if($th->tableHasColumn('Items', 'item_order_index')){
                    $sql .= " ORDER BY item_order_index ASC";
                }else{
                    $th->flushCache();
                }
                
                $result = $this->database->queryToArray($sql);
            
                foreach($result as $r){
                    $ids[] = $r['item_id'];
                }
            
            }elseif($m->getType() == 'SM_ITEMCLASS_MTM_SUB_MODEL'){
                
                
                
            }
            
            // Create new set from the item IDs
            $set = new SmartestSortableItemReferenceSet($m, $set_item_draft_mode);
            $set->setDraftMode($this->getDraftMode() ? -1 : 0);
            $set->loadItemIds($ids);
        
            // Sort the items by their default sort property, or by name if this is not defined
            if($default_property_id = $m->getDefaultSortPropertyId()){
                // $set->sort($default_property_id, $m->getDefaultSortPropertyDirection());
            }
            
            return $set->getItems();
            
        }
        
    }
    
    public function getNextSubModelItemOrderIndex($sub_model_id){
        
        $m = new SmartestSubModel;
        
        if($m->find($sub_model_id)){
            
            $sql = "SELECT Items.item_id, Items.item_order_index FROM Items WHERE item_itemclass_id='".$m->getId()."' AND item_parent_id='".$this->getId()."' ORDER BY item_order_index DESC LIMIT 1";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
                return $result[0]['item_order_index'];
            }else{
                return 0;
            }
            
        }
        
    }
	
	public function getTags(){
	    return $this->_item->getTags();
	}
	
	public function getTagsAsArrays(){
	    return $this->_item->getTagsAsArrays();
	}
    
	public function getFeaturedTags(){
	    return $this->_item->getFeaturedTags();
	}
	
	public function updateTagsFromStringsArray($strings_array){
	    $this->_item->updateTagsFromStringsArray($strings_array);
	}
	
	public function getTagsAsCommaSeparatedString(){
	    return $this->_item->getTagsAsCommaSeparatedString();
	}
	
	public function getAuthors(){
	    return $this->getItem()->getAuthors();
	}
	
	public function addAuthorById($user_id){
	    return $this->getItem()->addAuthorById($user_id);
	}
	
	public function getPropertyByNumericKey($key){
	    if(array_key_exists($key, $this->_properties)){
	        return $this->_properties[$key];
	    }else{
	        return null;
	    }
	}
	
	public function getPropertyValueByNumericKey($key, $draft='DEFAULT'){
	    
        if($draft == 'DEFAULT'){
            $draft = $this->getDraftMode();
        }
        
        if(is_string($key) || is_int($key)){
            
            if(array_key_exists($key, $this->_properties)){
	        
    	        // echo "test";
    	        try{
	            
    	            if(!$this->_properties[$key]->getData()->hasItem()){
    	                $this->_properties[$key]->getData()->setItem($this);
    	            }
    	        
        	        if($this->_properties[$key]->getDatatype() == 'SM_DATATYPE_TEMPLATE'){
        	            // var_dump($this->getDraftMode());
        	        }
    	        
        	        if($this->getDraftMode()){
        	            $raw_value = $this->_properties[$key]->getData()->getDraftContent();
        	            // $raw_value->setDraftMode(true);
                    }else{
                        $raw_value = $this->_properties[$key]->getData()->getContent();
                    }
                
                    // echo get_class($raw_value);
            
                }catch(SmartestException $e){
                    echo $e->getMessage();
                }
            
                $t = $this->_properties[$key]->getTypeInfo();
            
                if($t['valuetype'] == 'auto'){
                
                    if($t['id'] == 'SM_DATATYPE_AUTO_ITEM_FK'){
                
                        $class = $t['class'];
                    
                        $ids = array();
                
                        $field = $draft ? 'itempropertyvalue_draft_content' : 'itempropertyvalue_content';
                
                        $sql = "SELECT item_id FROM Items, ItemProperties, ItemPropertyValues WHERE item_deleted !=1 AND item_itemclass_id=itemproperty_itemclass_id AND itempropertyvalue_item_id=item_id AND itempropertyvalue_property_id = itemproperty_id AND ".$field."='".$this->getId()."' AND itemproperty_id='".$this->_properties[$key]->getForeignKeyFilter()."'";
                        $result = $this->database->queryToArray($sql);
                    
                        // The following code attempts to sort the items by their default sort order
                    
                        $foreign_property_id = $this->_properties[$key]->getForeignKeyFilter();
                        $foreign_property = new SmartestItemProperty;
                    
                        if($foreign_property->find($foreign_property_id)){
                        
                            $foreign_model_id = $foreign_property->getItemClassId();
                        
                            $model = new SmartestModel;
                        
                            if($draft){
                                $rdm = -1; // -1 is the draft-agnostic (both draft and live) mode for SmartestSortableItemReferenceSet (1 returns only draft objects)
                            }else{
                                $rdm = 0;
                            }
                        
                            if($model->find($foreign_model_id)){
                            
                                $s = new SmartestSortableItemReferenceSet($model, $draft);
                                $s->setDraftMode($rdm);
                            
                                foreach($result as $r){
                                    $s->insertItemId($r['item_id']);
                                }
                            
                                $s->sort();
                                $ids = $s->getItemIds();
                            
                            }else{
                                foreach($result as $r){
                                    $ids[] = $r['item_id'];
                                }
                            }
                        
                        }else{
                            foreach($result as $r){
                                $ids[] = $r['item_id'];
                            }
                        }
                    
                        $obj = new $class;
                        $obj->hydrateFromStoredIdsArray($ids, $draft);
                        return $obj;
                
                    }
                
                }

                if(is_object($raw_value)){
                    $r = $raw_value;
                }else if($value_ob = SmartestDataUtility::objectize($raw_value, $this->_properties[$key]->getDatatype(), $this->_properties[$key]->getForeignKeyFilter())){
                    $r = $value_obj;
                }else if(is_null($raw_value) && $c = SmartestDataUtility::getClassForDataType($this->_properties[$key]->getDatatype(), $this->_properties[$key]->getForeignKeyFilter())){
                    $r = new $c;
                }

                if($r instanceof SmartestDualModedObject){
                    $r->setDraftMode($this->getDraftMode());
                }
            
                return $r;
            
    	    }else{
    	        return null;
    	    }
        
        }else{
            // var_dump($key);
            // $e = new SmartestException('');
            // print_r(array_slice($e->getTrace(), 0, 5));
        }
        
	}
	
	public function getPropertyRawValueByNumericKey($key){
	    
	    if(array_key_exists($key, $this->_properties)){
	        
	        if($this->getDraftMode()){
	            $raw_value = $this->_properties[$key]->getData()->getRawValue(true);
            }else{
                $raw_value = $this->_properties[$key]->getData()->getRawValue(false);
            }
            
            return $raw_value;
            
	    }else{
	        return null;
	    }
	}
	
	public function getPropertyValueByVarName($varname){
	    
	    if(array_key_exists($varname, $this->_varnames_lookup)){
	        /* if($this->getDraftMode()){
	            return $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getDraftContent();
            }else{
                return $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getContent();
            } */
                
            $property_id = $this->_varnames_lookup[$varname];
            // echo $property_id;
            
            if($this->getDraftMode()){
	            $raw_value = $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getDraftContent();
            }else{
                $raw_value = $this->_properties[$this->_varnames_lookup[$varname]]->getData()->getContent();
            }
            
            // print_r($raw_value);
            if(is_object($raw_value)){
                return $raw_value;
            }else if($value_ob = SmartestDataUtility::objectize($raw_value, $this->_properties[$this->_varnames_lookup[$varname]]->getDatatype())){
                // echo $value_obj;
                return $value_obj;
            }
            
	    }else{
	        return null;
	    }
	}
	
	public function setPropertyValueByNumericKey($key, $raw_value, $from_form=false){
	    
	    if(array_key_exists($key, $this->_properties)){
	        
	        if(!$this->_properties[$key]->getData()->getPropertyId()){
	            $this->_properties[$key]->getData()->setPropertyId($key);
	        }
	        
	        if(!$this->_properties[$key]->getData()->getItemId()){
	            $this->_properties[$key]->getData()->setItemId($this->getId());
	        }
	        
	        // var_dump($raw_value);
	        
	        // var_dump(get_class($this->_properties[$key]->getData()));
	        // echo $key;
            
            $this->_properties[$key]->getData()->setContent($raw_value, false, false, $from_form);
	        // print_r($this->_properties[$key]->getData()->getDraftContent());
	        
	        // return $this->_properties[$key]->getData()->setContent($raw_value);
	        
	        // echo $this->_properties[$key]->getDatatype();
	        
	        /* if($value_obj = SmartestDataUtility::objectizeFromRawFormData($value, $this->_properties[$key]->getDatatype())){
	            // echo $value_obj->getStorableFormat().' ';
	            // echo $value_obj;
	            return $this->_properties[$key]->getData()->setContent($value_obj->getStorableFormat());
            }else{
                // echo "failed";
            } */
	        
	    }else{
	        return null;
	    }
	}
	
	public function __toString(){
		// return item's built-in name
		return $this->getItem()->getName();
	}
	
	public function getWorkflowStatus(){
	    if($this->getItem()->getModified() > $this->getItem()->getLastPublished()){
	        
	        // page has changed since it was last published
	        if($this->getItem()->getChangesApproved()){
	            return self::CHANGES_APPROVED;
	        }else{
	            return self::AWAITING_APPROVAL;
	        }
	        
	    }else{
	        // page hasn't been modified
	        return self::NOT_CHANGED;
	    }
	}
    
    public function getElasticSearchAssociativeArray(){
        
        $p_array = array();
        $p_array['name'] = '';
        foreach($this->getProperties() as $property_value_holder){
            if($property_value_holder->isSearchable() && $property_value_holder->getData()->getContent() instanceof SmartestSearchableValue){
                $p_array[$property_value_holder->getVarName()] = $property_value_holder->getData()->getContent()->getSearchQueryMatchableValue();
            }
        }
        $p_array['name'] = $this->getEditorName();
        return $p_array;
        
    }
	
	public function save(){
		
        $this->preSaveAction();
        
		$this->_save_errors = array();
		
		if($this->_came_from_database){
            $this->unCache();
        }else{
		    
		    if(!$this->_item->getWebId()){
		    
		        // create web id for SmartestItem object first
		        // $webid = SmartestStringHelper::random(32);
		        
		        switch($this->getModel()->getLongIdFormat()){

    	            case '_STD':
    	            case '':
    	            $webid = SmartestStringHelper::random(32);
    	            break;

    	            case '_UUID':
    	            $webid = SmartestStringHelper::generateUUID();
    	            break;

    	            default:
    	            $webid = SmartestStringHelper::randomFromFormat($this->getModel()->getLongIdFormat());

    	        }
		        
		        $this->_item->setWebId($webid);
		    
	        }
	        
	        // If the item is new, a site_id is also automatically assigned by SmartestItem::save()
	        
	    }
	    
	    if($this->_item->getName()){
	        
	        if(!$this->_item->getItemclassId()){
	            $this->_item->setItemclassId($this->_model_id);
	        }
	        
	        if(!strlen($this->_item->getSlug())){
	            $this->_item->setSlug(SmartestStringHelper::toSlug($this->_item->getName()));
	        }
	        
            $this->_item->setModified(time());
	        $this->_item->save();
            
            foreach($this->getModel()->getProperties() as $prop){
                
                $key = $prop->getId();
                $t = $prop->getTypeInfo();
                
                $this->_properties[$key]->setContextualItemId($this->_item->getId());
                $this->_properties[$key]->getData()->setItemId($this->_item->getId());
                
                if($this->_properties[$key]->getRequired() == 'TRUE' && !$this->_properties[$key]->getData()->getDraftContent()){
                    
                    // raise error
                    $this->_save_errors[] = $key; // SmartestItemPropertyValue::OMISSION_ERROR;
                    
                }
                
                if($t['valuetype'] != 'auto'){
                
                    // save a value object regardless if it is
                    $this->_properties[$key]->getData()->save();
                
                }
                
            }
            
        }else{
            // raise error - the item had no name
            $this->_save_errors[] = '_name';
            throw new SmartestException("Item saved without a name", SM_ERROR_USER);
        }
        
        $this->postSaveAction();
        
        if(count($this->_save_errors)){
            return false;
        }else{
            return true;
        }
        
	}
	
	public function saveAndPublish(){
	    
	    $this->save();
	    $this->publish();
	    
	}
    
    public function touch(){
        $this->_item->setLastModified(time());
        $this->_item->save();
    }
	
	public function getSaveErrors(){
	    return $this->_save_errors;
	}
	
    public function preSaveAction(){
        // This function will be overridden. This definition is simply here to make sure it can always be called safely.
    }
    
    public function postSaveAction(){
        // This function will be overridden. This definition is simply here to make sure it can always be called safely.
    }
    
	public function delete(){
		// mark as deleted
		if($this->_item instanceof SmartestItem && $this->_item->isHydrated()){
		    
		    $sql = "SELECT AssetIdentifiers.assetidentifier_live_asset_id, AssetIdentifiers.assetidentifier_assetclass_id, AssetClasses.assetclass_id, AssetClasses.assetclass_name, Pages.page_title, Pages.page_id FROM AssetIdentifiers, AssetClasses, Pages WHERE AssetIdentifiers.assetidentifier_live_asset_id='".$this->getId()."' AND AssetClasses.assetclass_type='SM_ASSETCLASS_ITEM_SPACE' AND AssetIdentifiers.assetidentifier_assetclass_id=AssetClasses.assetclass_id AND AssetIdentifiers.assetidentifier_page_id=Pages.page_id";
		    $result = $this->database->queryToArray($sql);
		    
		    if(count($result)){
		        SmartestLog::getInstance('system')->log("Item '{$this->getName()}' could not be deleted because it is currently the live, published item for the itemspace '{$result[0]['assetclass_name']}' on page '{$result[0]['page_title']}'");
		        return false;
		    }
		    
		    $this->_item->setDeleted(1);
		    $this->_item->save();
		    
		    return true;
		}
	}
	
	public function hardDelete(){
	    
	    if($this->_item instanceof SmartestItem && $this->_item->isHydrated()){
	        $this->_item->delete(true);
	    }
	    
	}
	
	public function duplicateFactory($name){
	    
	    $class = get_class($this);
	    $dupe = new $class;
	    $dupe->setItemForDuplicate($this->getItem()->duplicateWithoutSaving());
	    $dupe->setName($name);
	    
	    switch($this->getModel()->getLongIdFormat()){
            
            case '_STD':
            case '':
            $webid = SmartestStringHelper::random(32);
            break;
            
            case '_UUID':
            $webid = SmartestStringHelper::generateUUID();
            break;
            
            default:
            $webid = SmartestStringHelper::randomFromFormat($this->getModel()->getLongIdFormat());
            
        }
        
        $this->_item->setWebid($webid);
	    $dupe->getItem()->setWebId($webid);
	    $dupe->getItem()->setSlug($this->getItem()->getSlug());
	    $dupe->getItem()->save();
	    
	    $new_values = array();
	    
	    // print_r(array_merge(, SmartestDataUtility::getDataTypeCodesByValueType('foreignkey')));
	    
	    foreach($this->getStringProperties() as $p){
	        $new_values[$p->getId()] = $p->getData()->duplicateWithoutSaving(); // new SmartestItemPropertyValue object the same as the old one
	        $new_values[$p->getId()]->setItemId($dupe->getItem()->getId());
	    }
	    
	    foreach($this->getDropdownMenuProperties() as $p){
	        $new_values[$p->getId()] = $p->getData()->duplicateWithoutSaving(); // new SmartestItemPropertyValue object the same as the old one
	        $new_values[$p->getId()]->setItemId($dupe->getItem()->getId());
	    }
	    
	    $dupe->loadPropertiesForDuplication($new_values);
	    $dupe->save();
	    
	    foreach($this->getAuthors() as $a){
	        $dupe->addAuthorById($a->getId());
	    }
	    
	    return $dupe;
	    
	}
	
	public function loadPropertiesForDuplication($properties_from_other_item){
	    
	    if(is_array($properties_from_other_item)){
	        foreach($properties_from_other_item as $k => $p){ // These are ItemPropertyValueHolder objects
	            $this->_properties[$k]->replaceItemPropertyValueWith($properties_from_other_item[$k]);
	        }
	    }
	}
	
	public function setItemForDuplicate(SmartestItem $i){
	    $this->_item = $i;
	}
	
	public function publish($automated=false){
	    
        if($this->_item->getPublic() == 'FALSE' && !$automated){
            // If the item is not already published, look for a property called "date_published", and if it exists, set it to today's date.
        }
        
	    // NOTE: the SmartestItemPropertyValue::publish() function checks the user's permission, so this one doesn't need to
	    foreach($this->_properties as $pid => $p){
	        
	        if($p instanceof SmartestItemPropertyValueHolder){
	            $p->getData()->publish();
	        }
	        
	    }
        
        if($this->getModel()->getType() == 'SM_ITEMCLASS_MT1_SUB_MODEL'){
            $this->getParentItem()->unCache();
        }
        
        if(isset($this->_many_to_one_sub_models)){
            
        }
	    
	    $sql = "UPDATE TodoItems SET todoitem_is_complete='1' WHERE todoitem_type='SM_TODOITEMTYPE_PUBLISH_ITEM' AND todoitem_foreign_object_id='".$this->_item->getId()."'";
	    $this->database->rawQuery($sql);
        
        if(SmartestElasticSearchHelper::elasticSearchIsOperational()){
            
            $site = $this->getSite();
            $site_index_name = $site->getElasticSearchIndexName();
            
            if(SmartestElasticSearchHelper::itemIsIndexed(array('index'=>$site_index_name,'type' => $this->getModel()->getPluralName(),'id' => $this->getId()))){
                $params = [
                    'index' => $site_index_name,
                    'type' => $this->getModel()->getVarName(),
                    'id' => $this->getId(),
                    'body' => array('doc'=>$this->getElasticSearchAssociativeArray())
                ];
                // print_r($params);
                // echo "exists";
                $index_result = SmartestElasticSearchHelper::updateItem($params);
                
            }else{
                $params = [
                    'index' => $site_index_name,
                    'type' => $this->getModel()->getVarName(),
                    'id' => $this->getId(),
                    'body' => $this->getElasticSearchAssociativeArray()
                ];
                // echo "does not exist";
                $index_result = SmartestElasticSearchHelper::addItemToIndex($params);
            }
            // print_r($index_result);
            // exit;
        }
	    
	    $this->_item->setChangesApproved(1);
	    $this->_item->setLastPublished(time());
	    $this->_item->setIsHeld(0);
	    $this->_item->setPublic('TRUE');
	    $this->_item->save();
	    
	    $this->unCache();
	    
	}
	
	public function unPublish(){
	    
        $this->_item->setPublic('FALSE');
	    $this->_item->save();
        
        if(SmartestElasticSearchHelper::elasticSearchIsOperational()){
            $site_index_name = $site->getElasticSearchIndexName();
            $params = array(
                'index'=>$site_index_name,
                'type' => $this->getModel()->getVarName(),
                'id' => $this->getId()
            );
            SmartestElasticSearchHelper::deleteItem($params);
        }
        
	}
    
    public function unCache(){
	    foreach($this->getCacheFiles() as $file){
	        unlink($file);
	    }
    }
    
    public function rePublishAllSubModelItems(){
        if(isset($this->_many_to_one_sub_models)){
            foreach($this->_many_to_one_sub_models as $sub_model_id){
                $this->publishSubModelItems($sub_model_id);
            }
        }
    }
    
    public function publishSubModelItems($sub_model_id, $all=true){
        
        $items = $this->getSubModelItems($sub_model_id);
        
        foreach($items as $sub_model_item){
            if(($sub_model_item->isPublished() && $sub_model_item->getItem()->getIsModifiedSinceLastPublish()) || $all){
                $sub_model_item->publish();
            }
        }
    }
    
    public function rePublishSubModelItems($sub_model_id){
        return $this->publishSubModelItems($sub_model_id, false);
    }
	
	public function isApproved(){
	    return ($this->_item->getChangesApproved() == 1) ? true : false;
	}
	
	public function getRelatedPagesAsArrays($draft_mode=false){
	    return $this->_item->getRelatedPagesAsArrays($draft_mode);
	}
    
    public static function getModelClassName($item_id){
	    
	    // $item = new SmartestItem;
	    // $item->find($item_id);
	    // $model_id = $item->getItemclassId();
        
        $database = SmartestPersistentObject::get('db:main');
        $field = is_numeric($item_id) ? 'item_id' : 'item_webid';
        $sql = "SELECT item_itemclass_id FROM Items WHERE ".$field."='".$item_id."'";
        $result = $database->queryToArray($sql);
        
        if(count($result)){
	        
            $model_id = $result[0]['item_itemclass_id'];
            
    	    $model = new SmartestModel;
    	    $model->find($model_id);
            // $model->init();
    	    return $model->getClassName();
        
        }
	    
    }
    
    // builds a fully populated object of the correct type from just the primary key or webid
    public static function retrieveByPk($item_id, $dont_bother_with_class=false){
        
        if(__CLASS__ == 'SmartestCmsItem'){
        
            if(!$dont_bother_with_class){
                $className = self::getModelClassName($item_id);
            }
        
            if($dont_bother_with_class || !class_exists($className)){
                $object = new SmartestCmsItem;
            }else{
                $object = new $className;
            }
        
        }else{
            
            $className = __CLASS__;
            $object = new $className;
            
        }
        
        if(is_numeric($item_id) && $object->find($item_id)){
            return $object;
        }elseif($object->findBy('webid', $item_id)){
            return $object;
        }else{
            return null;
        }
    }
    
    public static function all($mode=9, $site_id=null){
        
        if(__CLASS__ == 'SmartestCmsItem'){
        
            // Error - all() must be called on a specific model
        
        }else{
            
            $className = __CLASS__;
            $object = new $className;
            $model = $object->getModel();
            return $model->getAllItems($site_id, $mode);
            
        }
        
    }
    
    // builds a fully populated object of the correct type from just the primary key or webid
    /* public static function retrieveByName($item_id, $dont_bother_with_class=false){
        
        if(__CLASS__ == 'SmartestCmsItem'){
        
            if(!$dont_bother_with_class){
                $className = self::getModelClassName($item_id);
            }
        
            if(!$dont_bother_with_class && class_exists($className)){
                $object = new $className;
            }else{
                $object = new SmartestCmsItem;
            }
        
        }else{
            
            $className = __CLASS__;
            $object = new $className;
            
        }
        
        if($object->find($item_id)){
            return $object;
        }else{
            return null;
        }
    } */
    
    public static function createNewByModelId($id){
        
        $m = new SmartestModel;
        
        if($m->find($id)){
            $class_name = $m->getClassName();
            if(class_exists($class_name)){
                return new $class_name;
            }else{
                // error - model's class name does not exist
            }
        }else{
            // error - model not found
        }
        
    }
    
    protected function getDataStore(){
        return SmartestPersistentObject::get('centralDataHolder');
    }
    
    public function initializeItemChainDataStorage(){
        if(!$this->_item_chain_data){
            $this->_item_chain_data = new SmartestParameterHolder('Item Chain information for item '.$this->_item->getName());
        }
    }
    
    public function setPositionInItemChain($set_id, $position){
        $this->initializeItemChainDataStorage();
        $this->_item_chain_data->setParameter('pos_'.$set_id, (int) $position);
    }
    
    public function setPreviousPrimaryKeyInItemChain($set_id, $id){
        $this->initializeItemChainDataStorage();
        $this->_item_chain_data->setParameter('prev_'.$set_id, (int) $id);
    }
    
    public function setNextPrimaryKeyInItemChain($set_id, $id){
        $this->initializeItemChainDataStorage();
        $this->_item_chain_data->setParameter('next_'.$set_id, (int) $id);
    }
    
    public function getPositionInItemChain($set_id){
        $this->_item_chain_data->getParameter('pos_'.$set_id);
    }
    
    public function getPreviousPrimaryKeyInItemChain($set_id){
        $this->_item_chain_data->getParameter('prev_'.$set_id);
    }
    
    public function getNextPrimaryKeyInItemChain($set_id){
        $this->_item_chain_data->getParameter('next_'.$set_id);
    }
	
}
