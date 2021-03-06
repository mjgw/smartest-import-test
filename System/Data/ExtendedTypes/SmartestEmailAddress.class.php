<?php

class SmartestEmailAddress extends SmartestString{
    
    protected $_name;
    
    public function formatIsValid(){
        return SmartestStringHelper::isEmailAddress($this->_string);
    }
    
    public function getDisplayName(){
        return $this->_name;
    }
    
    public function setDisplayName($n){
        $this->_name = $n;
    }
    
    public function getExtendedVersion(){
        if(strlen($this->_name)){
            return $this->_name.' <'.$this->_string.'>';
        }else{
            return $this->_string;
        }
    }
    
    public function getDomainPart(){
        $parts = explode('@', $this->_string);
        return $parts[1];
    }
    
    public function getDomain(){
        return $this->getDomainPart();
    }
    
    public function getLocalPart(){
        $parts = explode('@', $this->_string);
        return $parts[0];
    }
    
    public function getMailToLink(){
        return SmartestCmsLinkHelper::createLink('mailto:'.$this->_string, array('with'=>$this->_string));
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case 'is_valid':
            case 'valid':
            return $this->formatIsValid();
            
            case 'domain':
            case 'domain_part':
            return $this->getDomainPart();
            
            case 'local_part':
            return $this->getLocalPart();
            
            case 'extended':
            case 'extended_version':
            return $this->getExtendedVersion();
            
            case 'mailto':
            $l = $this->getMailToLink();
            return $l->render();
            
            case 'mailto_link_contents':
            case 'mailto_string':
            case 'link_contents':
            return 'mailto:'.$this->_string;
            
            case 'escaped_at':
            return str_replace('@', '(at)', $this->_string);
            
        }
        
        return parent::offsetGet($offset);
        
    }
  
}