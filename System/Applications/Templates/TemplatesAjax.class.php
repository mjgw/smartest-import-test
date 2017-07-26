<?php

class TemplatesAjax extends SmartestSystemApplication{
    
    public function relevantTemplates(){
        
		$h = new SmartestTemplatesLibraryHelper;
	    $type_code = $this->getRequestParameter('asset_type');
	    $template_id = $this->getRequestParameter('template');
        
	    if(is_numeric($template_id)){
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $this->send($template->scanStylesheetsForMentions($this->getSite()->getId()), 'stylesheets');
	        }else{
	            $this->send(array(), 'stylesheets');
	        }
        
        }else{
            
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);

         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
                    
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        $this->addUserMessage($message, SmartestUserMessage::ERROR);
         	        $show_form = false;
                    $this->send(array(), 'stylesheets');
                    
         	    }else{
         	        
                    $path = realpath(SM_ROOT_DIR.$location.$template_id);
                    $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$template_id);
         	        $this->send($template, 'template');
         	        $this->send($template->getContentForEditor(), 'template_content');
         	        $location = $template->getStorageLocation();
                    $this->send($template->scanStylesheetsForMentions($this->getSite()->getId()), 'stylesheets');
     	            
         	    }
     	        
 	        }else{
 	            // type not recognized
                $this->send(array(), 'stylesheets');
 	        }
            
        }
        
    }
    
    public function postBackTemplateEditorContentsFromModal(){
        
		$h = new SmartestTemplatesLibraryHelper;
		$edit_type = $this->getRequestParameter('edit_type');
		
		if($edit_type == 'imported'){
	        
	        $template_id = (int) $this->getRequestParameter('template_id');
	        
	        $template = new SmartestTemplateAsset;
	        
	        if($template->find($template_id)){
	            $type = $template->getTypeInfo();
	            
	            if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
	                $allow_update = true;
	                // $this->addUserMessageToNextRequest("The template has been successfully updated.", SmartestUserMessage::SUCCESS);
                }else{
                    $allow_update = false;
                    // $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING, true);
                }
	        }else{
	            // $this->addUserMessageToNextRequest("The template ID was not recognized", SmartestUserMessage::ERROR);
	            $allow_update = false;
	            $title = 'Edit '.$type['label'];
	        }
	        
	    }else{
	        
	        $filename = $this->getRequestParameter('filename');
            $type_code = $this->getRequestParameter('type');
	        
	        if(in_array($type_code, $h->getTypeCodes())){
                
                $types = $h->getTypes();
    		    $type = $types[$type_code];
    		    $this->send($type, 'type');
    		    $title = 'Edit '.$type['label'];
    		    $this->send($type, 'type_info');
	         
    	        $location = $h->getStorageLocationByTypeCode($type_code);
    	        
         	    if($location == SmartestAssetsLibraryHelper::MISSING_DATA){
         	        
         	        $message = "Template type ".$this->getRequestParameter('asset_type')." does not have any storage locations.";
         	        SmartestLog::getInstance('system')->log($message, SmartestLog::WARNING);
         	        // $this->addUserMessageToNextRequest($message, SmartestUserMessage::ERROR);
         	        $allow_update = false;
         	        
         	    }else{
         	        
         	        $template = new SmartestUnimportedTemplate(SM_ROOT_DIR.$location.$filename);
         	        
         	        if(is_writable($template->getStorageLocation(true)) && is_writable($template->getFullPathOnDisk())){
    	                $allow_update = true;
                    }else{
                        $allow_update = false;
                        // $this->addUserMessageToNextRequest("The file cannot be written. Please check permissions.", SmartestUserMessage::WARNING);
                    }
         	    }
     	        
 	        }else{
 	            // type not recognized
 	            $this->addUserMessageToNextRequest("The template type was not recognized", SmartestUserMessage::ERROR);
 	            $allow_update = false;
 	        }
	        
	    }
	    
	    if($allow_update){
	        
	        $content = $this->getRequestParameter('template_content');
            
            if(substr($content, 0, 9) == '<![CDATA['){
    		    $content = substr($content, 9);
    		}

    		if(substr($content, -3) == ']]>'){
    		    $content = substr($content, 0, -3);
    		}

    		$template_content = stripslashes($content);
    		SmartestFileSystemHelper::save($template->getFullPathOnDisk(), $template_content, true);
    		
    		SmartestLog::getInstance('site')->log("{$this->getUser()->getFullname()} made a change to template '{$template->getUrl()}' via the modal template editor.", SmartestLog::USER_ACTION);
    		
    		if($edit_type == 'imported'){
    		    $template->setModified(time());
    		    $template->save();
    		}
            
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode(array('success'=>true));
            exit;
	        
	    }else{
	        
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode(array('success'=>false));
            exit;
            
	    }
        
    }
    
}