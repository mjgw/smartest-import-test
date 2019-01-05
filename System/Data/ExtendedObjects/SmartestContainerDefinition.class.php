<?php

class SmartestContainerDefinition extends SmartestAssetIdentifier{

	protected $_container;
	protected $_depth = null;
	protected $_template = null;
	
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('ContainerId', 'assetclass_id');
		$this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
		
	}
	
    public function load($name, $page, $draft=false, $item_id=null, $instance_name='default'){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            $instance_name = SmartestStringHelper::toVarName($instance_name);
            $container = new SmartestContainer;
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_type = 'SM_ASSETCLASS_CONTAINER' AND assetclass_name='".$name."' AND (assetclass_site_id='".$page->getSiteId()."' OR assetclass_shared=1)";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
            // if($container->hydrateBy('name', $name)){
                
                $container->hydrate($result[0]);
                
                $this->_asset_class = $container;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                
                if(is_numeric($item_id)){
                    $sql .= " AND assetidentifier_item_id='".$item_id."'";
                }else{
                    $sql .= " AND assetidentifier_item_id IS NULL";
                }
                
                if(strlen($instance_name)){
                    $sql .= " AND assetidentifier_instance_name='".$instance_name."'";
                }
                
                $result = $this->database->queryToArray($sql, true);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    if($container->getType() != "SM_ASSETCLASS_CONTAINER"){
                        
                        // raise a warning?
                        
                    }
                    
                    $template = new SmartestTemplateAsset;
                    
                    if($draft){
                        $template_id = $this->getDraftAssetId();
                    }else{
                        $template_id = $this->getLiveAssetId();
                    }
                    
                    if($template->hydrate($template_id)){
                        
                        $this->_template = $template;
                        $this->_template->setIsDraft($draft);
                        // print_r($this->_template);
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        // Template doesn't exist
                        $this->_loaded = false;
                        return false;
                    }
                }else{
                    // Container not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Container by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function loadForUpdate($name, $page, $draft=false, $item_id=null, $instance_name='default'){
        
        if(strlen($name) && is_object($page)){
            
            $container = new SmartestContainer;
            $instance_name = SmartestStringHelper::toVarName($instance_name);
            
            if($container->findBy('name', $name)){
                return $this->loadWithObjects($container, $page, $draft, $item_id, $instance_name);
            }else{
                // Container by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function loadWithObjects(SmartestContainer $container, SmartestPage $page, $draft=false, $item_id=null, $instance_name='default'){
        
        $this->_asset_class = $container;
        $this->_page = $page;
        $instance_name = SmartestStringHelper::toVarName($instance_name);
        
        $sql = "SELECT * FROM AssetIdentifiers, AssetClasses WHERE assetclass_type = 'SM_ASSETCLASS_CONTAINER' AND assetidentifier_assetclass_id=assetclass_id AND assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
        
        if(is_numeric($item_id)){
            $sql .= " AND assetidentifier_item_id='".$item_id."'";
        }else{
            $sql .= " AND assetidentifier_item_id IS NULL";
        }
        
        if(strlen($instance_name)){
            $sql .= " AND assetidentifier_instance_name='".$instance_name."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $container->getType();
        
        if(count($result)){
            
            // A definition exists
            $this->hydrate($result[0]);
            
            if($container->getType() == "SM_ASSETCLASS_CONTAINER"){
                
                $template = new SmartestTemplateAsset;
                $this->_template = $template;
                $this->_template->setIsDraft($draft);
                $this->_loaded = true;
                return true;
                
            }else{
                
                // asset class being filled is not a container
                $this->_loaded = false;
                return false;
                
            }
            
        }else{
            
            // Container not defined (yet)
            $this->_loaded = false;
            return false;
        }
        
    }
    
    public function getTemplateFilePath(){
        
        if($this->_template->getFile()->exists()){
            // var_dump($this->_template->getFile()->getPath());
            return $this->_template->getFile()->getPath();
        }else{
            return null;
        }
    }
    
    public function getTemplateFilePathInSmartest(){
        
        if(!$this->getDraftAssetId()){
            var_dump($this->getId());
        }
        
        if($this->_template->getFile()->exists()){
            // var_dump($this->_template->getFile()->getPath());
            return $this->_template->getFile()->getSmartestPath();
        }else{
            return null;
        }
    }
    
    public function getTemplate(){
        /* if($this->_template->getFile()->exists()){
            // var_dump($this->_template->getFile()->getPath());
            return $this->_template->getFile()->getPath();
        }else{
            return null;
        } */
        
        return $this->_template;
    }
    
    public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        $container = new SmartestContainer;
        $container->hydrate($array);
        $this->_asset_class = $container;
        
        $template = new SmartestTemplateAsset;
        $template->hydrate($array);
        $this->_template = $template;
        
    }
    
    public function getContainer(){
	    
	    if(!is_object($this->_asset_class)){
	    
	        $c = new SmartestContainer;
	        $c->hydrate($this->getAssetClassId());
	        $this->_asset_class = $c;
	    
        }
	    
	    return $this->_asset_class;
	}

}