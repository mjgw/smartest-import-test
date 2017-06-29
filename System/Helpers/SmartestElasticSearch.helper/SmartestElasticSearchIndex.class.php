<?php

class SmartestElasticSearchIndex extends SmartestObject{
    
    protected $_name;
    protected $_usage;
    protected $_docs_info;
    protected $_shards;
    protected $_shards_info;
    
    public function __construct($name, $raw_data){
        
        $this->_name = $name;
        
        if(isset($raw_data['index'])){
            $usage = new SmartestParameterHolder('Usage info for index '.$this->_name);
            $usage->loadArray($raw_data['index']);
            $this->_usage = $usage;
        }
        
        if(isset($raw_data['docs'])){
            $docs = new SmartestParameterHolder('Documents info for index '.$this->_name);
            $docs->loadArray($raw_data['docs']);
            $this->_docs_info = $docs;
        }
        
        if(isset($raw_data['shards'])){
            
            $shards_info = new SmartestParameterHolder('Shards info for index '.$this->_name);
            $shards_info->setParameter('num_shards', count($raw_data['shards']));
            $this->_shards_info = $shards_info;
            $shards = array();
            
            foreach($raw_data['shards'] as $raw_shard){
                $shard = new SmartestElasticSearchShard($raw_shard[0]);
                $shards[] = $shard;
            }
            
            return $shards;
            
        }
        
    }
    
    public function getUsageInfo(){
        return $this->_usage;
    }
    
    public function getDocsInfo(){
        return $this->_docs_info;
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'name':
            return $this->_name;
            
            case 'docs_info':
            return $this->_docs_info;
            
            case 'usage_info':
            return $this->_usage;
            
            case 'size_formatted':
            return SmartestFileSystemHelper::formatRawFileSize($this->_usage->g('size_in_bytes'));
            
            case 'shards_info':
            return $this->_shards_info;
        }
        
    }
    
}