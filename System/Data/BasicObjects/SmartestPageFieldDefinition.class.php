<?php

class SmartestPageFieldDefinition extends SmartestBasePageFieldDefinition{
	
	protected $_page;
    protected $_field;
	
	// protected function __objectConstruct(){
	// 	
	// 	$this->_table_prefix = 'pagepropertyvalue_';
	// 	$this->_table_name = 'PagePropertyValues';
	// 	
	// }
	
	public function loadForUpdate($field, $page, $draft=false){
	    
	    if(is_object($field) && is_object($page)){
            
            $this->_page = $page;
            $this->_field = $field;
            
            if($field->getIsSitewide()){
                
                $sql = "SELECT * FROM PagePropertyValues WHERE pagepropertyvalue_site_id='".$this->_page->getSiteId()."' AND pagepropertyvalue_pageproperty_id='".$field->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    $this->hydrate($result[0]);
                    return true;
                }
                
            }else{
            
                $sql = "SELECT * FROM PagePropertyValues WHERE pagepropertyvalue_page_id='".$this->_page->getId()."' AND pagepropertyvalue_pageproperty_id='".$field->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    $this->hydrate($result[0]);
                    return true;
                }
            }
        }
        
        return false;
	}
	
	public function getPage(){
	    return $this->_page;
	}
    
    public function save(){
        if(is_object($this->_field) && $this->_field->getIsSitewide()){
            if($this->_properties['live_value'] != $this->_properties['draft_value']){
                // Global field has been updated - this could be used as a trigger that, if published, means that the pages cache needs clearing
            }
        }
        parent::save();
    }
	
}
