<?php

class SmartestAutomationHelper{
    
    protected $_site;
    
    public function __construct(SmartestSite $site){
        $this->_site = $site;
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function internalMaintenanceTrigger(){
        
        // Internal trigger should not be run for every request, and should only trigger essential maintenance such as cache file removal
        // Determine when internal trigger was last run, and if it was long enough ago, run it again. Should run according to interval set in automation.yml
        $maintenance_last_triggered = (int) SmartestSystemSettingHelper::load('maintenance_last_internally_triggered');
        
        $settings = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'Configuration/automation.yml');
        $maintenance_interval = $settings['internal_trigger_maintenance_interval'];
        
        if($maintenance_last_triggered < (time() - $maintenance_interval)){
            
            // clean pages cache
            $this->cleanPagesCache();
        
            // clean data cache
            $this->cleanDataCache();
            
            SmartestSystemSettingHelper::save('maintenance_last_internally_triggered', time());
            
        }
        
    }
    
    public function internalPublicationTrigger(){
        
        // Internal trigger should not be run for every request, and should only trigger essential maintenance such as cache file removal
        // Determine when internal trigger was last run, and if it was long enough ago, run it again. Should run according to interval set in automation.yml
        
        $publication_last_triggered = (int) SmartestSystemSettingHelper::load('publication_last_internally_triggered');
        
        $settings = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'Configuration/automation.yml');
        $publication_interval = $settings['internal_trigger_publication_interval'];
        
        if($publication_last_triggered < (time() - $publication_interval)){
            
            // identify any pages that should be published, but aren't (not published but publication date set in past)
            $this->publishNewItems();
        
            // identify any items that should be published, but aren't (not published but publication date set in past)
            $this->publishNewPages();
            
            SmartestSystemSettingHelper::save('publication_last_internally_triggered', time());
            
        }
        
    }
    
    public function externalTrigger(){
        
        // External trigger occurs outside of ordinary pageloads and is only ever initiated by visiting /smartest/automate from an IP address authorised in Configuration/automation.yml
        
        // Also triggers the same maintenance operations as the internal trigger, so run those:
        
        // clean pages cache
        $this->cleanPagesCache();
        // clean data cache
        $this->cleanDataCache();
        // identify any pages that should be published, but aren't (not published but publication date set in past)
        $this->publishNewItems();
        // identify any items that should be published, but aren't (not published but publication date set in past)
        $this->publishNewPages();
        
        // Loop through all applications to determine if each has an Automation class, and if it does, instantiate it
        
        // trigger any site-level custom cron behaviours defined in Sites/SITENAME/Library/SiteAutomated.class.php
        
        // trigger any custom global cron behaviours defined in Library/Automated/GlobalAutomated.class.php
        
    }
    
    private function cleanPagesCache(){
        // identify all cached pages that will no longer be used and delete
        
    }
    
    private function cleanDataCache(){
        // identify all files older than X days and delete
        
    }
    
    private function publishNewItems(){
        // Loop through models (all sites)
        
        $du = new SmartestDataUtility;
        $models = $du->getModels();
        $ih = new SmartestCmsItemsHelper;
        
        foreach($models as $model){
            
            if($model->getDefaultDatePropertyId()){
                
                $date_property_id = $model->getDefaultDatePropertyId();
                
                $sql = "SELECT Items.item_id, CONVERT(ItemPropertyValues.itempropertyvalue_draft_content, DECIMAL(15,5)) AS item_publish_time FROM Items, ItemPropertyValues WHERE ItemPropertyValues.itempropertyvalue_item_id=Items.item_id AND item_itemclass_id='".$model->getId()."' AND item_public='SCHED' AND itempropertyvalue_property_id='".$date_property_id."' AND itempropertyvalue_draft_content < ".time()."";
                
                $result = $this->database->queryToArray($sql);
                $item_ids = array();
                
                foreach($result as $r){
                    $item_ids[] = $r['item_id'];
                }
                
                $items = $ih->hydrateUniformListFromIdsArray($item_ids, $model->getId(), true);
                
                foreach($items as $item){
                    $item->publish();
                }
                
            }
            
        }
        
    }
    
    private function publishNewPages(){
        // Locate any pages that are not published, set to publish in the future, and publish date has passed
        
        // Publish each one
        
    }
    
}