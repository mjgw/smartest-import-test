<?php

class SmartestVCFContactCard extends SmartestFile{
    
    public function __construct($file_path=null){
        parent::__construct();
        
        if($file_path){
            $this->loadFile($file_path);
        }
    }
    
    public function getRawFields(){
        $c = $this->getContent(true);
        $fields = explode("\n", $c);
    }
    
    
    
}