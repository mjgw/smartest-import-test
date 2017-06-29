<?php

class SmartestElasticSearchShard extends SmartestObject{
    
    protected $_state, $_node, $_index, $_size;
    
    public function __construct($raw_data){
        
        $this->_state = $raw_data['state'];
        $this->_size = $raw_data['index']['size_in_bytes'];
        $this->_index = $raw_data['routing']['index'];
        $this->_node = $raw_data['routing']['node'];
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'state':
            return $this->_state;
            
            case 'size':
            return $this->_size;
            
            case 'size_formatted':
            return $this->getSizeFormatted();
            
            case 'index_name':
            return $this->_index;
            
            case 'node':
            return $this->_node;
            
        }
        
    }
    
    public function getState(){
        return $this->_state;
    }
    
    public function getSize(){
        return $this->_size;
    }
    
    public function getSizeFormatted(){
        return SmartestFileSystemHelper::formatRawFileSize($this->_size);
    }
    
    public function getIndexName(){
        return $this->_index;
    }
    
    public function getNodeName(){
        return $this->_node;
    }
    
}