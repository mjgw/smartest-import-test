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
        // return boolean - is it valid or not?
    }
    
    public static function createDropdown($data){
        // return $dropdown object
    }
    
}