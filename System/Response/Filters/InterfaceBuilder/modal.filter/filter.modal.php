<?php

function smartest_filter_modal($html, $filter){
    
    if(SmartestPersistentObject::get('request_data')->getParameter('namespace') == "modal"){
        
        // If Tidy is installed, use it
        /* if(function_exists('tidy_get_output')){
            // Specify configuration
            /* $config = array(
                'indent'         => true,
                'output-xhtml'   => true,
                'wrap'           => 200
            );

            // Tidy
            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();

            // Output
            $html = (string) $tidy; */
            
            //$html;
            
        // }
        
        // $html = str_replace('"work-area"', '"modal-work-area"', $html);
        
        // $doc = new DOMDocument();
        // $doc->loadHTML('<?xml encoding="UTF-8"><div id="root">'.SmartestStringHelper::encodeSmartestTags(SmartestStringHelper::onlyAmpersands($html)).'</div>');
        // $work_area = $doc->getElementById('work-area');
        // $work_area->setAttribute('id', 'modal-work-area');
        // echo $doc->saveHTML();
        
        // $element = simplexml_load_string('<div>'.SmartestStringHelper::onlyXMLEntities($html).'</div>')
        // if($element = simplexml_load_string('<div>'.html_entity_decode($html, ENT_QUOTES, 'UTF-8').'</div>')){
        if($element = simplexml_load_string('<div>'.SmartestStringHelper::removeHtmlEntitiesExceptXmlEssentials($html).'</div>')){
        // if($element = simplexml_load_string('<div>'.$html.'</div>')){
            // Valid HTML
            $work_area_element = $element->xpath("/div/div[1]");
            $work_area_element[0]['id'] = 'modal-work-area';
            // echo "SimpleXml Worked";
            return $work_area_element[0]->asXML();
        }else{
            // invalid HTML
            $html = str_replace('id="work-area"', 'id="modal-work-area"', $html);
            $html = str_replace('id="actions-area"', 'id="modal-actions-area"', $html);
            // echo "SimpleXml Failed";
            return $html;
        }
        
        // return $html;
        
    }else{
        return $html;
    }
    
}