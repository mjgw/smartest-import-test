<?php

class SmartestModelKitHelper{
    
    public static function execute($file_path, SmartestSite $site){
        
        if(self::validate($file_path)){
            $dropdowns = array();
            // return the new model
        }else{
            return false;
        }
        
    }
    
    public static function validate($file_path){
        // returns array of issues or empty array if valid
        
    }
    
    public static function isValid($file_path){
        // return boolean - is it valid or not?
        return !(bool) count(self::validate($file_path));
    }
    
    public static function dropdownExists($dropdown_id){
        // return bool
    }
    
    public static function createDropdown($data){
        // return $dropdown object
    }
    
}