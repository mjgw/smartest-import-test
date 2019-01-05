<?php

class SmartestInternalLink extends SmartestCmsLink implements ArrayAccess, SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue, SmartestJsonCompatibleObject, JsonSerializable{
    
    /* protected $_page_id;
    protected $_item_id;
    protected $_file_id;
    protected $_link_object; */
    
    public function __construct($v=null){
        
        $this->database = SmartestPersistentObject::get('db:main');
        $this->_request = SmartestPersistentObject::get('controller')->getCurrentRequest();
        $this->_render_data = new SmartestParameterHolder('Internal Link Render Data');
        $this->_markup_attributes = new SmartestParameterHolder('Internal Link Markup Attributes');
        
        if(isset($v)){
            $this->setValue($v);
        }
            
    }
    
    public function setValue($v){
        
        if($v instanceof SmartestParameterHolder){
            $this->_destination_properties = $v;
        }elseif(strlen($v)){
            if(!$this->_destination_properties = SmartestLinkParser::parseSingle($v)){
                $this->_destination_properties = new SmartestParameterHolder('Blank link');
            }
        }else{
            // echo "No properties";
        }
        
        if($this->_destination_properties instanceof SmartestParameterHolder){
            
            $this->setTypeFromNameSpace($this->_destination_properties->getParameter('namespace'));
            $this->_loadDestination();
            $extra_markup_attributes = $this->getSeparatedAttributes($this->_destination_properties)->getParameter('html');
            
            if($this->_destination_properties->hasParameter('hash')){
                $this->_hash = $this->_destination_properties->getParameter('hash');
            }
            
            if($this->_render_data->hasParameter('hash')){
                $this->_hash = $this->_render_data->getParameter('hash');
            }
            
            if($this->_destination_properties->hasParameter('model')){
                $this->_model = $this->recognizeModel($this->_destination_properties->getParameter('model'));
            }elseif($this->_render_data->hasParameter('model')){
                $this->_model = $this->recognizeModel($this->_render_data->getParameter('model'));
            }
            
            return true;
        
        }else{
            return false;
        }
        
    }
    
    public function getValue(){
        return $this->getDestinationObject();
    }
    
    public function __toString(){
        return $this->render();
    }
    
    public function stdObjectOrScalar(){
        $dest = $this->getDestinationObject();
        if($dest instanceof SmartestJsonCompatibleObject){
            if($this->getType() == SM_LINK_TYPE_DOWNLOAD || $this->getType() == SM_LINK_TYPE_INTERNAL_ITEM || $this->getType() == SM_LINK_TYPE_METAPAGE){
                return $dest->__toSimpleObjectForParentObjectJson();
            }else{
                return $dest->stdObjectOrScalar();
            }
        }else{
            return null;
        }
    }
    
    public function jsonSerialize() {
        return $this->stdObjectOrScalar();
    }
    
    public function isPresent(){
        return is_object($this->getDestinationObject());
    }
    
    public function getDestinationObject(){
        
        if(is_object($this->getDestination())){
            switch($this->getType()){
            
                case SM_LINK_TYPE_PAGE:
                case SM_LINK_TYPE_DOWNLOAD:
                case SM_LINK_TYPE_TAG:
                case SM_LINK_TYPE_AUTHOR:
                return $this->getDestination();
            
                case SM_LINK_TYPE_METAPAGE:
                case SM_LINK_TYPE_INTERNAL_ITEM:
                return $this->_destination->getPrincipalItem();
            
            }
        }
        
    }
    
    public function getNamespace(){
        
        switch($this->getType()){
            
            case SM_LINK_TYPE_PAGE:
            return 'page';
            case SM_LINK_TYPE_DOWNLOAD:
            return 'download';
            case SM_LINK_TYPE_TAG:
            return 'tag';
            case SM_LINK_TYPE_AUTHOR:
            return 'user';
            case SM_LINK_TYPE_METAPAGE:
            case SM_LINK_TYPE_INTERNAL_ITEM:
            return 'item';
            
        }
        
    }
    
    public function getDestinationObjectId(){
        
        if(is_object($this->getDestinationObject())){
            return $this->getDestinationObject()->getId();
        }
        
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        
        $format = $this->getNamespace().':'.$this->getDestinationObjectId();
        return $format;
        
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->setValue($v);
    }
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        /* $properties = SmartestLinkParser::parseInternalLinkFromSubmittedValue($v);
        var_dump($properties);
        // exit; */
        // var_dump($v);
        return $this->setValue($v);
        // echo $this->getStorableFormat();
        // exit;
    }
    
    public function isInternal(){
        return true;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "dest":
            case "destination":
            case "target":
            return $this->getDestinationObject();
            
            case "url":
            case "href":
            case "web_path":
            return $this->getUrl();
            
            case "hash":
            return $this->_hash;
            
            case "type":
            return $this->getType();
            
            case "type_string":
            case "namespace":
            return $this->getNamespace();
            
            case "dest_id":
            case "target_id":
            return $this->getDestinationObjectId();
            
            case "storable_format":
            return $this->getStorableFormat();
            
            case "empty":
            case "_empty":
            return !$this->isPresent();
            
            case "has_value":
            return $this->isPresent();
            
        }
        
    }
    
    public function offsetSet($offset, $value){}
    public function offsetUnset($offset){}
    public function offsetExists($offset){}
    
}