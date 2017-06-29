<?php

class SmartestRenderableAsset extends SmartestAsset implements SmartestDualModedObject, SmartestSearchableValue{
    
    protected $_render_data;
    protected $_draft_mode = false;
    protected $_usage; // can be item property, attachment, placeholder, etc.
    
    protected function __objectConstruct(){
		
		$this->_render_data = new SmartestParameterHolder('Asset render data');
		
	}
	
	public function hydrate($id, $site_id='', $dup=false){
	    
	    $result = parent::hydrate($id, $site_id, $dup);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	        return is_object($result) ? $result : true;
	    }else{
	        return false;
	    }
	    
	}
	
	public function hydrateBy($field, $value, $site_id=''){
	    
	    SmartestLog::getInstance('system')->log("Deprecated function used: SmartestRenderableAsset->hydrateBy()");
	    return $this->findBy($field, $value, $site_id);
	    
	}
	
	public function findBy($field, $value, $site_id=''){
	    
	    $result = parent::findBy($field, $value, $site_id);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	        return true;
	    }else{
	        return false;
	    }
	    
	}
	
	/* public function hydrate($id, $site_id=''){
		
		if(is_array($id)){
		        
	        $offset = strlen($this->_table_prefix);
	        
	        foreach($this->_original_fields as $fn){
	            // if the new array has a value with a key that exists in this object's table 
	            if(isset($id[$fn])){
	                // if the field is exempted from prefix (rare)
	                if(isset($this->_no_prefix[$fn])){
	                    $this->_properties[$fn] = $id[$fn];
	                }else{
	                    $this->_properties[substr($fn, $offset)] = $id[$fn];
	                }
	            }
	        }
	        
	        $this->setAdditionalRenderData($this->getDefaultParams());
				
			$this->_came_from_database = true;
			
			return true;
		
		}else if(is_object($id) && (!method_exists($id, '__toString') || !is_numeric($id->__toString()))){
		    
		    throw new SmartestException("Tried to hydrate a SmartestRenderableAsset object with another object (of type ".get_class($id).")");
		
		}else{
		    
		    return $this->find($id, $site_id);
	        
		}
		
	} */
	
	public function find($id, $site_id=''){
	    
	    $result = parent::find($id, $site_id);
	    
	    if($result){
	        $this->setAdditionalRenderData($this->getDefaultParams());
	    }
	    
	    return $result;
	    
	}
	
	public function __toString(){
	    
        // This function has to return a string:
        try{
    	    $output = $this->render();
            
            if($this->getType() == 'SM_ASSETTYPE_FLASH_VIDEO'){
                // var_dump($output);
            }
            
    	    if(strlen($output)){
    	        return $output;
    	    }else{
    	        return '';
    	    }
            
        }catch(SmartestException $e){
            return 'Error: '.$e->getMessage();
        }
	}
	
	public function extractId(){
        if(isset($this->_type_info['url_translation'])){
            $regex = "/".$this->_type_info['url_translation']['format']."/i";
    	    preg_match($regex, $this->getUrl(), $matches);
    	    $position = isset($this->_type_info['url_translation']['id_position']) ? $this->_type_info['url_translation']['id_position'] : 1;
    	    return $matches[$position];
        }
	}
	
	public function render($draft_mode='unset', $edit_button_in_draft=true){
	    
        if($draft_mode === 'unset'){
	        $draft_mode = $this->_draft_mode;
	    }
        
        if($this->_type_info['storage']['type'] == 'external_translated'){
	        $this->_render_data->setParameter('remote_id', $this->extractId());
	    }
	    
	    if(!($this->_render_data->hasParameter('html_id') && strlen($this->_render_data->getParameter('html_id')))){
	        $this->_render_data->setParameter('html_id', SmartestStringHelper::toSlug($this->_type_info['label']).'-'.substr($this->getWebId(), 0, 8));
	    }
        
        if(!$edit_button_in_draft){
            $this->_render_data->setParameter('_hide_edit_button', true);
        }
        
        if($this->getType() == 'SM_ASSETTYPE_OEMBED_URL'){
            $this->_render_data->setParameter('markup', $this->getContent());
        }
	    
        if($this->getId()){
	        
	        $sm = new SmartyManager('BasicRenderer');
            $r = $sm->initialize($this->getStringId());
            $r->assignAsset($this);
            $ua = SmartestPersistentObject::get('userAgent');
            $r->assign('sm_user_agent', $ua);
            $r->setDraftMode($draft_mode);
    	    $content = $r->renderAsset($this->_render_data);
    	    
    	    return $content;
	    
	    }else{
            if($draft_mode){
                return '<span class="smartest-preview-hint">[No file selected for this value]</span>';
            }
        }
	    
	}
	
	public function renderPreview(){
	    
        if(isset($this->_type_info['render']['preview_template']) && is_file(SM_ROOT_DIR.$this->_type_info['render']['preview_template'])){
	        
	        if($this->_type_info['storage']['type'] == 'external_translated'){
    	        $this->_render_data->setParameter('remote_id', $this->extractId());
    	    }

    	    if(!($this->_render_data->hasParameter('html_id') && strlen($this->_render_data->getParameter('html_id')))){
    	        $this->_render_data->setParameter('html_id', SmartestStringHelper::toSlug($this->_type_info['label']).'-'.substr($this->getWebId(), 0, 8));
    	    }
            
            $ua = SmartestPersistentObject::get('userAgent');
            
	        $sm = new SmartyManager('BasicRenderer');
            $r = $sm->initialize($this->getStringId());
            $r->assign('preview_mode', true);
            $r->assign('sm_user_agent', $ua);
            $r->assignAsset($this);
            $r->setDraftMode(true);
    	    $content = $r->renderAsset($this->_render_data, null, true);
    	    
    	    return $content;
	        
        }else{
            return $this->render(true);
        }
	    
	}
	
	public function setDraftMode($m){
	    $this->_draft_mode = (bool) $m;
	}
	
	public function getDraftMode(){
	    return $this->_draft_mode;
	}
	
	public function setAdditionalRenderData($info, $not_empty_only=false){
	    
	    if($info instanceof SmartestParameterHolder){
	        $info = $info->getParameters();
	    }
	    
	    if(is_array($info)){
	        foreach($info as $key=>$value){
	            if(!$not_empty_only || ($not_empty_only && strlen($value))){
	                $this->setSingleAdditionalRenderDataParameter($key, $value);
                }
	        }
	    }
	    
	}
	
	public function setSingleAdditionalRenderDataParameter($name, $value){
	    $this->_render_data->setParameter($name, $value);
	}
	
	public function getRenderData(){
	    return $this->_render_data;
	}
    
    public function getSearchQueryMatchableValue(){
        if($this->usesTextFragment()){
            return html_entity_decode(strip_tags($this->render()), ENT_COMPAT, 'UTF-8');
        }else{
            return $this->getUrl();
        }
    }
    
    public function offsetGet($offset){
	    
	    switch($offset){
        
            case "html":
            return $this->render();
        
            case "render_data":
            return $this->_render_data;
            
            case "render_data_debug":
            return print_r($this->_render_data, true);
            
            case "link_contents":
            if($this->isImage()){
                return 'image:'.$this->getUrl();
            }
            break;
            
            case "text":
            case "text_content":
            if($this->isParsable()){
                return new SmartestString($this->render());
            }else{
                return $this->getContent();
            }
            
            case "is_defined":
            return (bool) $this->getId();
            
            case "first_paragraph":
            if($this->isParsable()){
                return SmartestStringHelper::getFirstParagraph($this->render());
            }else{
                return SmartestStringHelper::getFirstParagraph($this->getContent());
            }
            
            case "draft_mode":
            return new SmartestBoolean($this->getDraftMode());
        
        }
        
        if(strlen($this->_render_data->getParameter($offset))){
	        return $this->_render_data->getParameter($offset);
	    }
        
        return parent::offsetGet($offset);
        
    }
    
}