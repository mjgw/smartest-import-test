<?php

class SmartestCmsItemList extends SmartestBaseCmsItemList{
    
    protected $_list_items = array();
    protected $_data_set;
    protected $_fetch_attempted = false;
    protected $_site_id = null;
    
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'list_';
		$this->_table_name = 'Lists';
		
	}
	
	public function load($list_name, $page, $draft=false){
	    
	    if(is_object($page)){
	        
	        $sql = "SELECT * FROM Lists WHERE list_name='".$list_name."' AND list_page_id='".$page->getId()."'";
	        $result = $this->database->queryToArray($sql);
	        
            $this->_site_id = $page->getSiteId();
            
	        if(count($result)){
	            $this->hydrate($result[0]);
	            return true;
	        }else{
	            return false;
	        }
	        
	    
        }
	    
	}
	
	public function exists($list_name, $page_id){
	    
	    $sql = "SELECT * FROM Lists WHERE list_name='".SmartestStringHelper::toVarName($list_name)."' AND list_page_id='".$page_id."'";
        $result = $this->database->queryToArray($sql);
        
        if(count($result)){
            $this->hydrate($result[0]);
            return true;
        }else{
            return false;
        }
        
        
	}
	
	public function getInfoForPageTree($level=1){
	    
	    $info = array();
	    $info['exists'] = 'true';
	    $info['defined'] = $this->hasChanged() ? 'DRAFT' : 'PUBLISHED';
	    $info['assetclass_name'] = $this->_properties['name'];
		$info['type'] = "list";
		$info['level'] = $level;
		return $info;
	    
	}
	
	public function hasChanged(){
	    return ($this->_properties['draft_set_id'] == $this->_properties['live_set_id'] && $this->_properties['draft_template_file'] == $this->_properties['live_template_file'] && $this->_properties['draft_header_template'] == $this->_properties['live_header_template'] && $this->_properties['draft_footer_template'] == $this->_properties['live_footer_template']) ? false : true;
	}
	
	public function hasHeaderTemplate($draft=false){
	    
	    if($draft){
	        $header_template_file_name = $this->getDraftHeaderTemplate();
	    }else{
	        $header_template_file_name = $this->getLiveHeaderTemplate();
	    }
	    
	    return ((strlen($header_template_file_name) > 4) && is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$header_template_file_name)) ? true : false;
	}
	
	public function hasRepeatingTemplate($draft=false){
	    
	    if($draft){
	        $repeating_template_file_name = $this->getDraftTemplateFile();
	    }else{
	        $repeating_template_file_name = $this->getLiveTemplateFile();
	    }
	    
	    return ((strlen($repeating_template_file_name) > 4) && (is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$repeating_template_file_name) || is_file(SM_ROOT_DIR.'Presentation/Layouts/'.$repeating_template_file_name))) ? true : false;
	}
	
	public function hasFooterTemplate($draft=false){
	    
	    if($draft){
	        $footer_template_file_name = $this->getDraftFooterTemplate();
	    }else{
	        $footer_template_file_name = $this->getLiveFooterTemplate();
	    }
	    
	    return ((strlen($footer_template_file_name) > 4) && is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$footer_template_file_name)) ? true : false;
	    
	}
	
	public function getRepeatingTemplate($draft=false){
	    
	    return SM_ROOT_DIR.$this->getRepeatingTemplateInSmartest($draft);
        
	}
    
    public function getRepeatingTemplateInSmartest($draft=false){
        
	    if($draft){
	        $repeating_template_file_name = $this->getDraftTemplateFile();
	    }else{
	        $repeating_template_file_name = $this->getLiveTemplateFile();
	    }
	    
	    if($this->getType() == 'SM_LIST_SIMPLE'){
            if(is_file(SM_ROOT_DIR.'Presentation/Layouts/'.$repeating_template_file_name)){
                return 'Presentation/Layouts/'.$repeating_template_file_name;
            }else if(is_file(SM_ROOT_DIR.'Presentation/ListItems/'.$repeating_template_file_name)){
                return 'Presentation/ListItems/'.$repeating_template_file_name;
            }
        }elseif($this->getType() == 'SM_LIST_TAG'){
            return 'Presentation/Layouts/'.$repeating_template_file_name;
        }
        
    }
	
	public function getHeaderTemplate($draft=false){
	    
	    if($draft){
	        $header_template_file_name = $this->getDraftHeaderTemplate();
	    }else{
	        $header_template_file_name = $this->getLiveHeaderTemplate();
	    }
	    
	    return SM_ROOT_DIR.'Presentation/ListItems/'.$header_template_file_name;
	    
	}
	
	public function getFooterTemplate($draft=false){
	    
	    if($draft){
	        $footer_template_file_name = $this->getDraftFooterTemplate();
	    }else{
	        $footer_template_file_name = $this->getLiveFooterTemplate();
	    }
	    
	    return SM_ROOT_DIR.'Presentation/ListItems/'.$footer_template_file_name;
	    
	}
	
	public function getItems($draft=false, $limit=null, $site_id=null){
	    
	    if(!$this->_fetch_attempted){
	        
            if($this->getType() == 'SM_LIST_SIMPLE'){
            
    	        $this->_data_set = new SmartestCmsItemSet;
	    
        	    if($draft){
        	        if(!$this->_data_set->find($this->getDraftSetId())){
        	            throw new SmartestException('The set chosen as the draft definition for this page (ID='.$this->getDraftSetId().') does not exist.');
        	        }
        	    }else{
        	        if(!$this->_data_set->find($this->getLiveSetId())){
        	            throw new SmartestException('The set chosen as the live definition for this page (ID='.$this->getLiveSetId().') does not exist.');
        	        }
        	    }
	        
    	        $mode = $draft ? SM_QUERY_ALL_DRAFT_CURRENT : SM_QUERY_PUBLIC_LIVE_CURRENT;
                $this->_list_items = $this->_data_set->getMembersPaged($mode, $limit);
	        
            }elseif ($this->getType() == 'SM_LIST_TAG'){
                
                if($draft){
                    $tag_id = $this->getDraftSetId();
                    $model_id = $this->getDraftSetFilter();
                }else{
                    $tag_id = $this->getLiveSetId();
                    $model_id = $this->getLiveSetFilter();
                }
                
                $tag = new SmartestTag;
                
                if($tag->find($tag_id)){
                    
                    if($this->getMaximumLength() && is_numeric($this->getMaximumLength())){
                        $data = $tag->getItems($this->_site_id, $model_id);
                        $this->_list_items = array_slice($data, 0, $this->getMaximumLength());
                    }else{
                        $this->_list_items = $tag->getItems($this->_site_id, $model_id);
                    }
                    
                }
                
            }
            
	        $this->_fetch_attempted = true;
	    
	    }
	    
	    return $this->_list_items;
	    
	}
	
	public function getItemsAsArrays($draft){ // This function is deprecated and should be removed soon
	    
	    if(!$this->_fetch_attempted){
	    
	        /* $this->_data_set = new SmartestCmsItemSet;
	    
    	    if($draft){
    	        $this->_data_set->hydrate($this->getDraftSetId());
    	    }else{
    	        $this->_data_set->hydrate($this->getLiveSetId());
    	    }
	    
    	    $this->_list_items = $this->_data_set->getMembers();
    	    $this->_fetch_attempted = true; */
    	    
    	    // force list to generate list members
    	    $items = $this->getItems($draft);
	    
	    }
	    
	    return $this->_data_set->getMembersAsArrays($draft);
	}
    
    public function getSet($draft=false){
        
        if(!$this->_fetch_attempted || is_object($this->_data_set)){
            return $this->_data_set;
        }else{
            
	        $this->_data_set = new SmartestCmsItemSet;
	    
    	    if($draft){
                if($this->_data_set->find($this->getDraftSetId())){
    	            $this->_data_set->setRetrieveMode(SM_QUERY_ALL_DRAFT_CURRENT);
                }else{
                    throw new SmartestException('The set chosen as the draft definition for this page (ID='.$this->getDraftSetId().') does not exist.');
                }
    	    }else{
                if($this->_data_set->find($this->getLiveSetId())){
    	            $this->_data_set->setRetrieveMode(SM_QUERY_PUBLIC_LIVE_CURRENT);
                }else{
                    throw new SmartestException('The set chosen as the live definition for this page (ID='.$this->getLiveSetId().') does not exist.');
                }
    	    }
            
            return $this->_data_set;
            
        }
        
    }

}