<?php

class SmartestModelKitHelper{
    
    public static function execute($file_path, SmartestSite $site){
        
        if(self::isValid($file_path)){
            // create initial model, accroring to first half of the model kit
            // Add properties
                // if a property is a dropdown property, check if a dropdown with a corresponding identifier exists
                // if not, create the dropdown, otherwise use the one that exists
            // If assets are limited to file types or models, ensure this is saved to the property after it is created
            // return the new model
        }else{
            return false;
        }
        
    }
    
    public static function validate($file_path){
        // returns array of issues or empty array if valid
        $errors = array();
        if($data = SmartestYamlHelper::load($file_path)){
            if(isset($data['modelkit'])){
                if(!isset($data['modelkit']['name'])){
                    $errors[] = "The model kit does not specify a singular name.";
                }
                if(!isset($data['modelkit']['plural'])){
                    $errors[] = "The model kit does not specify a plural name.";
                }
            }else{
                $errors[] = "The file is not correctly formatted. (1)";
            }
        }else{
            $errors[] = "The file ".$file_path." could not be read";
        }
        
        return $errors;
        
    }
    
    public static function isValid($file_path){
        // return boolean - is it valid or not?
        return !(bool) count(self::validate($file_path));
    }
    
    public static function dropdownExists($dropdown_identifier){
        // return bool
	    $du = new SmartestDataUtility;
        $dropdowns = $du->getDropdowns($this->getSite()->getId());
        foreach($dropdowns as $d){
            if($d->getIdentifier() == $dropdown_identifier){
                return true;
            }
        }
        return false;
    }
    
    public static function createDropdown($data){
        // return $dropdown object
        
    }
    
}