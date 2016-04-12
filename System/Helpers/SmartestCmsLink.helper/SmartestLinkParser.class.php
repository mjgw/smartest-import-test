<?php

class SmartestLinkParser{
    
    const LINK_TARGET_TITLE = 'SM_LINK_GET_TARGET_TITLE';
    const LINK_TARGET_URL = 'SM_LINK_GET_TARGET_URL';
    
    public static function replaceAll($string){
        
        $oc = self::parseEasyLinks($string);
        
    }
    
    public static function parseEasyLinks($string){
        
        $pattern = '/\[(\[(([\w_-]+):)?([^\]\|]+)(\|([^\]]+))?\]|(\+)?(https?:\/\/[^\s\]]+)(\s+([^\]]+))?)\]|\[(@([\w_]+:[\w_]+))(\s+([^\]]+))?\]/i';
        preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
        
        $links = array();
        
        if(is_array($matches)){
            
            foreach($matches as $m){
                
                if(isset($m[8])){ // this means link started with 'http', so it is external
                    
                    $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[7]);
                    
                    $l->setParameter('original', $m[0]);
                    
                    $l->setParameter('scope', SM_LINK_SCOPE_EXTERNAL);
                    $l->setParameter('destination', $m[8]);
                    
                    if(strlen($m[7])){
                        $l->setParameter('newwin', true);
                    }
                
                    if($m[10]){
                        $l->setParameter('text', $m[10]);
                    }else{
                        $l->setParameter('text', $m[8]);
                    }
                    
                    $l->setParameter('format', SM_LINK_FORMAT_URL);
                    
                }else if(isset($m[11])){
                    
                    $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[11]);
                    
                    $l->setParameter('route', $m[11]);
                    
                    $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
                    $l->setParameter('format', SM_LINK_FORMAT_QUINCE_ROUTE);
                    $l->setParameter('destination', $m[11]);
                    $l->setParameter('original', $m[0]);
                    $l->setParameter('namespace', 'quince');
                    
                    if(isset($m[14])){
                        $l->setParameter('text', $m[14]);
                    }else{
                        $l->setParameter('text', self::LINK_TARGET_URL);
                    }
                
                }else{
                    
                    $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[2].SmartestStringHelper::toSlug($m[4]));
                    
                    $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
                    $l->setParameter('namespace', $m[3]);
                    
                    if(in_array($m[3], array('image', 'asset', 'download'))){
                        $l->setParameter('destination', $m[2].$m[4]);
                        $l->setParameter('filename', $m[4]);
                    }else if($m[3] == 'tag'){
                        $l->setParameter('destination', $m[4]);
                        $l->setParameter('tag_name', $m[4]);
                    }else if($m[3] == 'mailto'){
                        $l->setParameter('destination', $m[4]);
                    }else{
                        $l->setParameter('destination', $m[2].$m[4]);
                    }
                    
                    $l->setParameter('original', $m[0]);
                    
                    if(strpos($l->getParameter('destination'), '=') !== false){

                        // if(preg_match('/(name|id|webid)=([\w_-]+)(:(name|id|webid)=([\w_-]+))?/i', $l->getParameter('destination'), $dm)){
                        if(preg_match('/(meta)?page:((name|id|webid)=)?([\w_\$-]+)(:((name|id|webid)=)?([\w_\$-]+))?/i', $l->getParameter('destination'), $dm)){
                            
                            $l->setParameter('format', SM_LINK_FORMAT_AUTO);
                            
                            if(strlen($dm[2])){
                                $l->setParameter('page_ref_field_name', strtolower($dm[3]));
                            }else{
                                $l->setParameter('page_ref_field_name', 'name');
                            }

                            if($dm[3] == 'webid'){
                                $l->setParameter('page_ref_field_value', $dm[4]);
                            }else{
                                $l->setParameter('page_ref_field_value', trim(SmartestStringHelper::toSlug($dm[4])));
                            }

                            if(isset($dm[5]) && strlen($dm[5])){
                                if(strlen($dm[6])){
                                    if($dm[7] == 'name'){
                                        $l->setParameter('item_ref_field_name', 'slug');
                                    }else{
                                        $l->setParameter('item_ref_field_name', $dm[7]);
                                    }
                                }else{
                                    $l->setParameter('item_ref_field_name', 'slug');
                                }
                                $l->setParameter('item_ref_field_value', trim(SmartestStringHelper::toSlug($dm[8])));
                            }

                        }else{
                            
                        }

                    }else{
                        
                        if(strtolower($l->getParameter('namespace')) == 'page'){
                            $l->setParameter('page_ref_field_name', 'name');
                            $l->setParameter('page_ref_field_value', SmartestStringHelper::toSlug($m[4]));
                        }else{
                            // echo $l->getParameter('namespace');
                            if(!in_array($l->getParameter('namespace'), array('image', 'download', 'tag', 'asset', 'mailto'))){
                                $l->setParameter('destination', $m[2].SmartestStringHelper::toSlug($m[4]));
                                $l->setParameter('item_ref_field_name', 'slug');
                                $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[4]));
                                $l->setParameter('format', SM_LINK_FORMAT_USER);
                            }
                        }
                    }
                
                    if(isset($m[6])){
                        $l->setParameter('text', $m[6]);
                    }else{
                        $l->setParameter('text', self::LINK_TARGET_TITLE);
                    }
                }
                
                $links[] = $l;
            
            }
        }
        
        return $links;
    }
    
    public static function parseSingle($string){
        
        if($string == '#'){
            
            $l = new SmartestParameterHolder("Empty Link Parameters");
            $l->setParameter('scope', SM_LINK_SCOPE_NONE);
            $l->setParameter('destination', '#');
        
        }else if(preg_match('/^(https?:\/\/[^\s]+)(\s+([^\]]+))?$/i', trim($string), $m)){
            
            $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[0]);
            $l->setParameter('destination', $m[1]);
            $l->setParameter('scope', SM_LINK_SCOPE_EXTERNAL);
            if(isset($m[2])){
                $l->setParameter('text', $m[2]);
            }
            $l->setParameter('format', SM_LINK_FORMAT_URL);
        
        }else if(strlen($string) && $string{0} == '@'){
            
            // TODO: Finish integration with Quince's "URL For" functionality
            /* $controller = SmartestPersistentObject::get('controller');
            $url = $controller->getUrlFor($string);
            return strlen($url) ? $url : '#'; */
            
            // This is unfinished
            $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$string);
            
            $l->setParameter('route', $string);
            
            $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
            $l->setParameter('format', SM_LINK_FORMAT_QUINCE_ROUTE);
            $l->setParameter('destination', $string);
            $l->setParameter('namespace', 'quince');
            
        }else{
            
            $pattern = '/^(([\w_-]+):)([\w\.@_-]+)(#([\w\._-]+))?(\|([^\]]+))?$/i';
            
            if(preg_match($pattern, $string, $m)){
                
                $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[0]);
                $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
                $l->setParameter('destination', $m[1].$m[3]);
                $l->setParameter('namespace', $m[2]);
                $l->setParameter('format', SM_LINK_FORMAT_USER);
            
                if($l->getParameter('namespace') == 'mailto'){
                    $l->setParameter('destination', $m[3]);
                }
            
                if(in_array($m[2], array('image', 'asset', 'download', 'dl'))){
                    if(is_numeric($m[3])){
                        $l->setParameter('asset_id', $m[3]);
                    }else{
                        $l->setParameter('filename', $m[3]);
                    }
                }
            
                if(in_array($m[2], array('user', 'author'))){
                    if(is_numeric($m[3])){
                        $l->setParameter('user_id', $m[3]);
                    }else{
                        $l->setParameter('username', $m[3]);
                    }
                }
            
                if($m[2] == 'tag'){
                    if(is_numeric($m[3])){
                        $l->setParameter('tag_id', $m[3]);
                    }else{
                        $l->setParameter('tag_name', $m[3]);
                    }
                }
        
                if(isset($m[7])){
                    $l->setParameter('text', $m[7]);
                }else{
                    $l->setParameter('text', self::LINK_TARGET_TITLE);
                }
            
                if(isset($m[4]) && strlen($m[5])){
                    $l->setParameter('hash', $m[5]);
                }
                
                if(strtolower($l->getParameter('namespace')) == 'page'){
                
                    if(is_numeric($m[3])){
                        $l->setParameter('page_ref_field_name', 'id');
                        $l->setParameter('page_ref_field_value', $m[3]);
                    }else{
                        $l->setParameter('page_ref_field_name', 'name');
                        $l->setParameter('page_ref_field_value', SmartestStringHelper::toSlug($m[3]));
                    }
                
                }elseif(strtolower($l->getParameter('namespace')) == 'item'){
                
                    $l->setParameter('format', SM_LINK_FORMAT_FORM);
                    $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
                
                    if(is_numeric($m[3])){
                        $l->setParameter('item_ref_field_name', 'id');
                        $l->setParameter('item_ref_field_value', $m[3]);
                    }else{
                        $l->setParameter('item_ref_field_name', 'slug');
                        $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[3]));
                    }
                
                }else{
                
                    if(!in_array($l->getParameter('namespace'), array('image', 'download', 'tag', 'asset', 'mailto'))){
                        $l->setParameter('destination', $m[1].SmartestStringHelper::toSlug($m[3]));
                        $l->setParameter('item_ref_field_name', 'slug');
                        $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[3]));
                        $l->setParameter('format', SM_LINK_FORMAT_USER);
                    }
                }
            
            }else if(preg_match('/(meta)?page:((name|id|webid)=)?([\w_\$-]+)(:((name|id|webid)=)?([\w_\$-]+))?/i', $string, $m)){
                
                $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[0]);
                $l->setParameter('destination', $string);
            
                if(strlen($m[2])){
                    $l->setParameter('page_ref_field_name', $m[3]);
                }else{
                    $l->setParameter('page_ref_field_name', 'name');
                }
            
                if($m[3] == 'webid'){
                    $l->setParameter('page_ref_field_value', $m[4]);
                }else{
                    $l->setParameter('page_ref_field_value', trim(SmartestStringHelper::toSlug($m[4])));
                }
            
                $l->setParameter('format', SM_LINK_FORMAT_AUTO);
            
                if(strlen($m[1]) && isset($m[5]) && strlen($m[5])){
                    $l->setParameter('namespace', 'metapage');
                    if(strlen($m[6])){
                        if($m[7] == 'name'){
                            $l->setParameter('item_ref_field_name', 'slug');
                        }else{
                            $l->setParameter('item_ref_field_name', $m[7]);
                        }
                    }else{
                        $l->setParameter('item_ref_field_name', 'slug');
                    }
                    $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[8]));
                }else{
                    $l->setParameter('namespace', 'page');
                }
                
            }else{
                // no link
                echo "Link destination not parsable: ".$string;
            }
        }
        
        return $l;
        
    }
    
    public function processRegexMatch($match){
        
    }
    
    /* public static function parseInternalLinkFromSubmittedValue($submitted_value){
        
        $submitted_value = strtolower(trim($submitted_value));
        
        // var_dump($submitted_value);
        
        preg_match('/(page|item|asset|download|tag|user):(\d+)/', $submitted_value, $matches);
        $l = new SmartestParameterHolder("Parsed Internal Link Destination Properties: ".$matches[0]);
        $l->setParameter('namespace', $matches[1]);
        $l->setParameter('destination', $matches[0]);
        $l->setParameter('target_object_id', $matches[2]);
        
        /* if($matches[1] == 'page'){
            $l->setParameter('page_ref_field_name', 'id');
            $l->setParameter('page_ref_field_value', $matches[2]);
        }elseif($matches[1] == 'item'){
            $l->setParameter('item_ref_field_name', 'id');
            $l->setParameter('item_ref_field_value', $matches[2]);
        }elseif($matches[1] == 'user'){
            $l->setParameter('user_id', $matches[2]);
        }elseif($matches[1] == 'file'){
            $l->setParameter('asset_id', $matches[2]);
        }elseif($matches[1] == 'tag'){
            $l->setParameter('tag_id', $matches[2]);
        } 
        
        $l->setParameter('format', SM_LINK_FORMAT_USER);
        
        return $l;
        
    } */
    
}