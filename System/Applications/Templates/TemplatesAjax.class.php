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
    
}