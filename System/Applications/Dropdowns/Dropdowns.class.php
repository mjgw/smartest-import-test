<?php

class Dropdowns extends SmartestSystemApplication{
  
    public function startPage($get){
        
	    $this->setFormReturnUri();
	    
	    $du = new SmartestDataUtility;
        $dropdowns = $du->getDropdowns($this->getSite()->getId());
	    
	    $this->send($dropdowns, 'dropdowns');
	    $this->send(count($dropdowns), 'count');
	    	
	}

	public function addDropDown($get){ 
		
        $types = SmartestDataUtility::getDataTypes('dropdown', true, false);
        $this->send($types, 'types');
        
	}

	public function insertDropDown($get, $post){ 
	    
	    $label = $post['dropdown_label'];
	    
	    if(strlen($label)){
	    
    	    $name = SmartestStringHelper::toVarName($label);
	    
    	    $dropdown = new SmartestDropdown;
	    
    	    if(!$dropdown->hydrateBy('name', $name)){
    	        
    	        $dropdown->setName($name);
    	        $dropdown->setLabel($label);
                $dropdown->setDatatype($this->getRequestParameter('dropdown_datatype'));
    	        $dropdown->save();
    	        $this->addUserMessageToNextRequest('Your new dropdown menu was saved successfully.', SmartestUserMessage::SUCCESS);
    	        
    	        if($this->getRequestParameter('continue_to_values')){
    	            $this->redirect('/dropdowns/addDropDownValue?dropdown_id='.$dropdown->getId());
	            }else{
	                $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
	            }
	            
    	    }else{
    	        $this->addUserMessage('A dropdown menu with that name already exists.', SmartestUserMessage::INFO);
    	        $this->forward('dropdowns', 'addDropDown');
    	    }
	    
        }else{
            
            $this->addUserMessage('You must enter a valid label for the dropdown.', SmartestUserMessage::ERROR);
            $this->forward('dropdowns','addDropDown');
            
        }
	    
	}

	public function dropdownInfo($get){
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	        $this->send($dropdown->getFieldsWhereUsed($this->getSite()->getId()), 'fields');
	        $this->send($dropdown->getItemPropertiesWhereUsed($this->getSite()->getId()), 'item_properties');
            $this->send($this->getUser()->hasToken('edit_dropdown_datatype'), 'allow_datatype_change');
            $types = SmartestDataUtility::getDataTypes('dropdown', true, false);
            $this->send($types, 'types');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	         
	}
	
	public function editValues($get){
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	        $dropdown->getOptions();
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	         
	}

	public function updateDropDown($get, $post){ 
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $dropdown->setLabel($post['dropdown_label']);
            $dropdown->setName(SmartestStringHelper::toVarName($post['dropdown_name']));
            if($this->getUser()->hasToken('edit_dropdown_datatype')){
                $dropdown->setDatatype($this->getRequestParameter('dropdown_datatype'));
            }
	        $dropdown->setLanguage($post['dropdown_language']);
	        $dropdown->save();
	    }
	    
	    $this->formForward();
	    
	}

	public function deleteDropDown($get){ 
	    
	    $dropdown_id = (int) $get['dropdown_id'];
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $dropdown->delete();
	        $this->addUserMessageToNextRequest('The dropdown was successfully deleted.', SmartestUserMessage::SUCCESS);
	        $this->redirect('/smartest/dropdowns');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}

	public function dropdownValues($get){ 
	    
	    $this->setFormReturnUri();
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $options = $dropdown->getOptions();
	        
	        $this->send($dropdown, 'dropdown');
	        $this->send($options, 'options');
	        
	    }else{
	        $this->addUserMessage("The supplied dropdown ID was not recognized.");
	    }
	    
	}

	public function addDropDownValue($get){ 
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        $this->send($dropdown, 'dropdown');
	    }else{
	        $this->addUserMessageToNextRequest('The dropdown ID was not recognized.', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }  
	}	

	public function insertDropDownValue($get, $post){
	    
	    $label = $post['dropdownvalue_label'];
	    
	    if(strlen($post['dropdownvalue_value'])){
	        $value = SmartestStringHelper::toVarName($post['dropdownvalue_value']);
        }else{
            $value = $post['dropdownvalue_value'];
        }
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $option = new SmartestDropdownOption;
	    
	        $option->setDropdownId($dropdown->getId());
	        $option->setLabel($label);
	        $option->setValue($value);
	        $option->setOrder($dropdown->getNextOptionOrderIndex());
	        
	        $option->save();
	        
	        if($this->getRequestParameter('continue_to_values')){
	            $this->addUserMessageToNextRequest('Your new dropdown value was saved successfully.', SmartestUserMessage::SUCCESS);
	            $this->redirect('/dropdowns/addDropDownValue?dropdown_id='.$dropdown->getId());
            }else{
                $this->addUserMessageToNextRequest('Your new dropdown value was saved successfully.', SmartestUserMessage::SUCCESS);
    	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
            }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The dropdown ID menu was not recognized.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}

	public function editDropDownValue($get){ 
	    
	    // $dropdown_value_id=$get['dropdown_value_id'];
	    $dropdown_value_id = (int) $this->getRequestParameter('dropdown_value_id');
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($dropdown_value_id)){
	    
	        $this->send($dropdown_id, 'dropdown_id');
	        $this->send($option, 'option');
	        $this->send($option->getDropdown(), 'dropdown');
	    
        }else{
            
            $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
            $this->redirect('/smartest/dropdowns');
            
        }
	        
	}

	public function updateDropDownValue($get, $post){ 
	    
	    $id = (int) $this->getRequestParameter('dropdown_value_id');
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($id)){
	        $drop_down_label = SmartestStringHelper::sanitize($post['dropdown_label']);
	        $option->setLabel($drop_down_label);
	        $option->save();
	        $this->addUserMessageToNextRequest("The option was updated.", SmartestUserMessage::SUCCESS);
	    }else{
	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();  
	}
	
	public function deleteDropDownValue($get){ 
	    
	    $id = (int) $this->getRequestParameter('dropdown_value_id');
	    
	    $option = new SmartestDropdownOption;
	    
	    if($option->find($id)){
	        $option->delete();
	        $this->addUserMessageToNextRequest("The option was deleted.", SmartestUserMessage::SUCCESS);
	        $this->redirect('/dropdowns/editValues?dropdown_id='.$option->getDropdownId());
	    }else{
	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
    
	public function editDropdownOrder($get){ 
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $options = $dropdown->getOptions();
	        
	        $this->send($dropdown, 'dropdown');
	        $this->send($options, 'options');
	        
	    }else{
	        $this->addUserMessage("The supplied dropdown ID was not recognized.");
	    }
	    
	}
	
	public function moveDropDownValueUp($get, $post){
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $dropdown->fixOrderIndices();
	        
	        // $option_id = $get['dropdown_value_id'];
	        $option_id = (int) $this->getRequestParameter('dropdown_value_id');
    	    $option = new SmartestDropdownOption;

    	    if($option->find($option_id)){
                $option->moveUp();
                $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }else{
    	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
    	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }
    	    
	    }else{
	        $this->addUserMessageToNextRequest("The dropdown ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}
	
	public function moveDropDownValueDown($get, $post){
	    
	    $dropdown_id = (int) $this->getRequestParameter('dropdown_id');
	    $dropdown = new SmartestDropdown;
	    
	    if($dropdown->find($dropdown_id)){
	        
	        $dropdown->fixOrderIndices();
	        
	        // $option_id = $get['dropdown_value_id'];
	        $option_id = (int) $this->getRequestParameter('dropdown_value_id');
    	    $option = new SmartestDropdownOption;

    	    if($option->find($option_id)){
                $option->moveDown();
                $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }else{
    	        $this->addUserMessageToNextRequest("The option ID was not recognized.", SmartestUserMessage::ERROR);
    	        $this->redirect('/dropdowns/editValues?dropdown_id='.$dropdown->getId());
    	    }
    	    
	    }else{
	        $this->addUserMessageToNextRequest("The dropdown ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/dropdowns');
	    }
	    
	}
	
}