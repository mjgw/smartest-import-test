<?php

class SmartestTextFragment extends SmartestBaseTextFragment{
    
    protected $_asset;
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'textfragment_';
		$this->_table_name = 'TextFragments';
        
    }
    
    public function setAsset(SmartestAsset $a){
        $this->_asset = $a;
    }
    
    public function getAsset(){
        
        if(!$this->_asset){
            $a = new SmartestAsset;
            if($a->find($this->getAssetId())){
                $this->_asset = $a;
            }
        }
        
        return $this->_asset;
    }
    
    public function getAttachments(){
        
        $attachment_names = $this->parseAttachmentNames();
        $attachments = array();
        
        foreach($attachment_names as $a){
            $attachments[$a] = '';
        }
        
        // look up any defined attachments
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        
        foreach($attachment_names as $a){
            $q->addAllowedInstanceName($a);
        }
        
        $q->addForeignTableConstraint('Assets.asset_deleted', 1, SmartestQuery::NOT_EQUAL);
        $results = $q->retrieve();
        
        foreach($attachment_names as $a){
          
            if(array_key_exists($a, $results)){
                $attachments[$a] = $results[$a];
            }else{
                $attachments[$a] = new SmartestTextFragmentAttachment;
                $attachments[$a]->setInstanceName($a);
            }
        }
        
        return $attachments;
        
    }
    
    public function getAttachmentsAsArrays($include_objects=false){
        
        $attachments = $this->getAttachments();
        $arrays = array();
        
        foreach($attachments as $name => $object){
            
            $arrays[$name] = $object->__toArray();
            $arrays[$name]['_name'] = $name;
            
        }
        
        return $arrays;
        
    }
    
    public function getAttachmentsForElementsTree($level, $version){
        if($version == 'draft'){
            
            $attachments = $this->getAttachments();
            $children = array();
            $parent_asset = $this->getAsset();
            
            foreach($attachments as $key=>$a){
                
                $asset = $a->getAsset();
                
                if(is_object($asset)){
                    
                    $child = array();
                    $child['state'] = 'closed';
                    $child['info']['asset_id'] = $asset->getId();
                    $child['info']['asset_webid'] = $asset->getWebId();
                    $child['info']['asset_type'] = $asset->getType();
                    $child['info']['assetclass_name'] = $key;
                    $child['info']['assetclass_id'] = 'asset_'.$parent_asset->getId().'_attachment'.$key;
                    $child['info']['defined'] = 'PUBLISHED';
                    $child['info']['exists'] = 'true';
                    $child['info']['filename'] = '';
                    $child['info']['type'] = 'attachment';
                
                    $child['asset_object'] = $asset;
                    $children[] = $child;
                }
                
            }
            
            return $children;
            
        }else{
            return array();
        }
    }
    
    public function parseAttachmentNames(){
        
        if($this->getAsset()->getType() == 'SM_ASSETTYPE_TEXTILE_TEXT'){
            $regexp = preg_match_all('/\{attach:([\w_-]+)\}/', $this->_properties['content'], $matches);
        }else{
            $regexp = preg_match_all('/<\?sm:attachment.+?name="([\w_-]+)"/', $this->_properties['content'], $matches);
        }
        
        $attachment_names = array();
        
        foreach($matches[1] as $an){
            $n = SmartestStringHelper::toVarName($an);
            if(!in_array($n, $attachment_names)){
                $attachment_names[] = $n;
            }
        }
        
        return $attachment_names;
    }
    
    public function containsAttachmentTags(){
        
        if($this->getAsset()->getType() == 'SM_ASSETTYPE_TEXTILE_TEXT'){
            $c = !(strpos($this->_properties['content'], '{attach:') === FALSE);
        }else{
            $c = !(strpos($this->_properties['content'], '<?sm:att') === FALSE);
        }
        
        return $c;
    }
    
    public function getAttachmentCurrentDefinition($attachment_name){
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addAllowedInstanceName($attachment_name);
        $q->addForeignTableConstraint('Assets.asset_deleted', 1, SmartestQuery::NOT_EQUAL);
        
        $results = array_values($q->retrieve());
        
        if(count($results)){
            $def = $results[0];
            if($def instanceof SmartestTextFragmentAttachment){
                return $def;
            }else{
                return new SmartestTextFragmentAttachment;
            }
        }else{
            return new SmartestTextFragmentAttachment;
        }
    }
    
    public function attachmentIsDefined($attachment_name){
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addAllowedInstanceName($attachment_name);
    }
    
    public function getDisplayParameters(){
	    
	    $info = $this->getTypeInfo();
	    
	}
	
	public function getParsableFilePath($draft_mode=false){
	    
	    if($draft_mode){
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Previews/tfpreview_'.SmartestStringHelper::toHash($this->getId(), 8, 'SHA1').'.tmp.tpl';
	    }else{
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Live/tflive_'.SmartestStringHelper::toHash($this->getId(), 8, 'SHA1').'.tpl';
	    }
	    
	    return $file_path;
	}
	
	public function publish(){
	    
        // Any attached textfragments such as embed codes should also be published
        foreach($this->getAttachments() as $attachment){
            if($attachment->hasAsset()){
                if($attachment->getAsset()->usesTextFragment()){
                    if($attachment->getAsset()->getTextFragmentId() != $this->getId()){
                        $attachment->getAsset()->getTextFragment()->publish();
                    }
                }
            }
        }
        
	    $content = $this->getContent();
	    
	    $parser = new SmartestDataBaseStoredTextAssetToolkit();
	    $method = $this->getAsset()->getConvertMethodName();
	    
	    if(method_exists($parser, $method)){
            $content = $parser->$method($content, $this->_asset);
        }
	    
	    return SmartestFileSystemHelper::save($this->getParsableFilePath(), $content, true);
	    
	}
	
	public function duplicate(){
	    
	    $dup = $this->copy();
	    $dup->save();
	    SmartestFileSystemHelper::copy($this->getParsableFilePath(), $dup->getParsableFilePath());
	    SmartestFileSystemHelper::copy($this->getParsableFilePath(true), $dup->getParsableFilePath(true));
	    return $dup;
	    
	}
	
	public function isPublished(){
	    return file_exists($this->getParsableFilePath());
	}
	
	public function createPreviewFile(){
	    // $parser = new SmartestDataBaseStoredTextAssetToolkit($this);
	    $content = stripslashes($this->getContent());
	    
	    $parser = new SmartestDataBaseStoredTextAssetToolkit();
	    $method = $this->getAsset()->getConvertMethodName();
	    
	    if(method_exists($parser, $method)){
            $content = $parser->$method($content, $this->_asset);
        }
	    
	    $result = SmartestFileSystemHelper::save($this->getParsableFilePath(true), $content, true);
	    return $result;
	}
	
	public function ensurePreviewFileExists(){
	    if(!file_exists($this->getParsableFilePath(true))){
	        return $this->createPreviewFile();
	    }else{
	        return true;
	    }
	}
	
	public function getPreviewFilePath(){
	    return $this->getParsableFilePath(true);
	}
	
	public function getContent(){
	    return $this->_getContent();
	}
    
	public function getContentForEditor(){
	    
        $string = $this->_getContent();
        $regexp = preg_match_all('/<\?sm:attachment.+?name="([\w_-]+)".*:\?>/', $string, $matches);
        
        $num_tags = count($matches[0]);
        
        if($num_tags){
            
            $att_objects = $this->getAttachments();
            
            $i = 1;
            foreach($matches[0] as $key => $attachment){
                
                $att_name = str_replace('-', '_', $matches[1][$key]);
                
                if(isset($att_objects[$att_name]) && is_object($att_objects[$att_name])){
                    $float = (bool) $att_objects[$att_name]->getFloat();
                    $alignment = $att_objects[$att_name]->getAlignment();
                    if($alignment == 'left' || $alignment == 'right'){
                        if($float){
                            $additional_style .= 'float:'.$alignment.';';
                        }
                        if($alignment == 'left'){
                            $additional_style .= 'margin:0 10px 10px 0;';
                        }else{
                            $additional_style .= 'margin:0 0 10px 10px;';
                        }
                    }else{
                        $additional_style .= 'margin:0 auto 10px auto;';
                    }
                }
                
                if($num_tags == $i){
                    
                    // We are on the last one - check if it is at the bottom of the file
                    $text = trim($string);
                    $start = strlen($attachment) * -1;
                    $last_chars = substr($text, $start);
                    $attachment_div_text = '<figure id="sm-attachment-'.$matches[1][$key].'" class="sm-attachment-proxy mceNonEditable '.$alignment.'" data-attachmentname="'.$matches[1][$key].'" style="'.$additional_style.'">Media attachment: <strong>'.$matches[1][$key].'</strong></figure>';
                    
                    if($last_chars == $attachment){
                        $string = str_replace($attachment, $attachment_div_text.'<p class="sm-attachment-buffer"></p>', $string);
                    }else{
                        $string = str_replace($attachment, $attachment_div_text, $string);
                    }
                    
                }else{
                    $attachment_div_text = '<figure id="sm-attachment-'.$matches[1][$key].'" class="sm-attachment-proxy mceNonEditable" data-attachmentname="'.$matches[1][$key].'" style="'.$additional_style.'">Media attachment: <strong>'.$matches[1][$key].'</strong></figure>';
                    $string = str_replace($attachment, $attachment_div_text, $string);
                }
                $i++;
            }
        }
        
        $regexp = preg_match_all('/<a[^>]+?href="(https?:\/\/[^"]+)"[^>]*>([^<]*)?<\/a>/', $string, $matches_a);
        
        if(count($matches_a[0])){
            foreach($matches_a[0] as $key => $link){
                if(strpos($link, 'class="twitter-')){
                    
                }else{
                    if(strpos($link, 'target="_blank"')){
                        $string = str_replace($link, '[+'.$matches_a[1][$key].' '.$matches_a[2][$key].']',$string);
                    }else{
                        $string = str_replace($link, '['.$matches_a[1][$key].' '.$matches_a[2][$key].']',$string);
                    }
                }
                
            }
        }
        
        $regexp = preg_match_all('/<a href="mailto:([^"]+)+"[^>]*>([^<]*)?<\/a>/', $string, $matches_m);
        
        if(count($matches_m[0])){
            foreach($matches_m[0] as $key => $link){
                if(trim($matches_m[1][$key]) == trim($matches_m[2][$key])){
                    $string = str_replace($link, '[[mailto:'.trim($matches_m[1][$key]).']]',$string);
                }else{
                    $string = str_replace($link, '[[mailto:'.trim($matches_m[1][$key]).' '.trim($matches_m[2][$key]).']]',$string);
                }
            }
        }
        
        $regexp = preg_match_all('/<a[^>]+?href="((\.{0,2}\/)*([^"]+)?)"[^>]*>([^<]*)?<\/a>/', $string, $matches_ai);
        
        // print_r($matches_ai[2]);
        
        if(is_object($this->getCurrentSite())){
            $site = $this->getCurrentSite();
            $error_page_id = $site->getErrorPageId();
            // echo $this->getCurrentSite()->getInternalLabel();
            foreach($matches_ai[3] as $key=>$url){
                // var_dump($url);
                $linked_page = $this->getCurrentSite()->getContentByUrl($url);
                if(is_object($linked_page) && $linked_page->getId() != $error_page_id){
                    // echo $linked_page->getTitle();
                    $replacement = $linked_page->getLinkCodeWithTextField();
                    $replacement = str_replace('%%TEXT%%', $matches_ai[4][$key], $replacement);
                    // echo $replacement;
                    $string = str_replace($matches_ai[0][$key], $replacement, $string);
                }
            }
        }
        
        $regexp = preg_match_all('/<\?sm:link[^>]+?to="([\w-]+:[\w-]+)"[^(with)>]*(with="([^">]*)")?[^(style)>]*(style="([^">]*)")*[^>]*:\?>/', $string, $matches_lt);
        
        // echo $string;
        
        /* $regexp = preg_match_all('/<\?sm:link.+?to="([\w_-]+:[\w_-]+(\|[^"]+))".*:\?>/', $string, $matches_l); */
        
        // print_r($matches_lt);
        
        if(count($matches_lt[0])){
            foreach($matches_lt[0] as $key => $template_style_link){
                // $string = str_replace($attachment, '<div id="sm-attachment-'.$matches[1][$key].'" class="sm-attachment-proxy" data-attachmentname="'.$matches[1][$key].'" style="border:1px dotted #f00;padding:5px">Smartest Attachment: <strong>'.$matches[1][$key].'</strong></div>', $string);
                
                if(strlen($matches_lt[4][$key])){
                    $opening_span = '<span '.$matches_lt[4][$key].'>';
                    $closing_span = '</span>';
                }else{
                    $opening_span = $closing_span = '';
                }
                
                $new_link = $opening_span.'[['.$matches_lt[1][$key];
                
                if(strlen($matches_lt[2][$key])){
                    $new_link .= '|'.$matches_lt[3][$key];
                }
                
                $new_link .= ']]'.$closing_span;
                
                $string = str_replace($matches_lt[0][$key], $new_link, $string);
                
            }
        }
        
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        
	}
	
	public function getContentAsObject(){
	    return new SmartestString($this->_properties['content']);
	}
	
	public function setContent($content){
	    return $this->_setContent($content);
	}
    
    public function setContentFromEditor($content){
        
        if($this->getAsset()->getType() == 'SM_ASSETTYPE_RICH_TEXT'){
            
            if(class_exists('tidy')){
            
                $tidy = new tidy;
                $tidy->ParseString($content);
                $new = $tidy->repairString($content, array( 
                    'output-xml' => true, 
                    'input-xml' => true,
                    'bare' => true,
                    'wrap' => 0 
                ));
            
                if(strlen(trim($new))){
                    $content = $new;
                }
            
                $content = preg_replace( "/\r|\n/", "", $content);
            
            }
        
            $content = str_replace('<p>&nbsp;</p>', '', $content);
            $content = str_replace('&nbsp;', ' ', $content);
        
            if($element = simplexml_load_string(str_replace('&', '&amp;', html_entity_decode('<div>'.$content.'</div>', ENT_QUOTES, 'UTF-8')))){
            
                $content = preg_replace( "/\r|\n/", "", $content);
                $divs = (array) $element->xpath('/div/figure');
            
                foreach($divs as $divelement){
                    $attributes = (array) $divelement->attributes();
                    if(strpos($attributes['@attributes']['class'], 'sm-attachment-proxy') !== false){
                        $attachment_name = $attributes['@attributes']['data-attachmentname'];
                        $content = str_replace($divelement->asXML(), '<?sm:attachment name="'.$attachment_name.'":?>', $content);
                    }
                }
            
                $divs = (array) $element->xpath('/div/ul/li/figure');
            
                foreach($divs as $divelement){
                    $attributes = (array) $divelement->attributes();
                    if(strpos($attributes['@attributes']['class'], 'sm-attachment-proxy') !== false){
                        $attachment_name = $attributes['@attributes']['data-attachmentname'];
                        $content = str_replace($divelement->asXML(), '<?sm:attachment name="'.$attachment_name.'":?>', $content);
                    }
                }
            
                $paras = (array) $element->xpath('/div/p');
            
                // Code to remove buffer paragraphs
                foreach($paras as $p_element){
                    $attributes = (array) $p_element->attributes();
                    // If the element has a class
                    if(isset($attributes['@attributes']['class'])){
                        // if the sm-attachment-buffer class has been set
                        if(strpos($attributes['@attributes']['class'], 'sm-attachment-buffer') !== false){
                            // If the contents of the paragraph are more than just space
                            if(strlen($p_element->__toString()) && strlen(trim($p_element->__toString()))){
                                $content = str_replace($p_element->asXML(), '<p>'.$p_element->__toString().'</p>', $content);
                            }else{
                                // Otherwise, remove the paragraph
                                $content = str_replace($p_element->asXML(), '', $content);
                            }
                        }
                    }
                }
            
                $content = str_replace(':?>', ":?>\n", $content);
                $content = str_replace('</p>', "</p>\n", $content);
                $content = str_replace('</li>', "</li>\n", $content);
                $content = str_replace('</h1>', "</h1>\n", $content);
                $content = str_replace('</h2>', "</h2>\n", $content);
                $content = str_replace('</h3>', "</h3>\n", $content);
                $content = str_replace('</h4>', "</h4>\n", $content);
            
                return $this->_setContent($content);
        
            }else{
                // SimpleXML failed
                if(strlen($content) && strpos($content, 'sm-attachment-proxy') === false){
                    // If there are no attachment proxy divs, just skip filtering
                    return $this->_setContent($content);
                }else{
                    // TODO: deal with the attachments differently
                    // throw new SmartestException("Textfragment could not be updated.");
                    SmartestLog::getInstance('system')->log("TextFragment with ID ".$this->getId().' (asset ID '.$this->getAssetId().') could not be updated because it cannot be parsed by SimpleXml, and contains proxy elements that need to be removed before saving.');
                    return false;
                }
            }
            
        }else{
            
            return $this->_setContent($content);
            
        }
        
    }
	
	protected function _getContent(){
	    return $this->_properties['content'];
	}
	
	protected function _setContent($content){
	    return $this->setField('content', $content);
	}
	
	public function save(){
	    
	    $this->setModified(time());
	    $this->createPreviewFile();
	    
	    parent::save();
	    
	}
	
	public function getWordCount(){
        return SmartestStringHelper::getWordCount($this->_properties['content']);
    }
    
    public function getLength(){
        return strlen($this->_properties['content']);
    }
    
    public function getLengthWithoutHTML(){
        return strlen(strip_tags($this->_properties['content']));
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "word_count":
            case "wordcount":
            return $this->getWordCount();
            
            case "length":
            case "text_length":
            return $this->getLength();
            
            case "object":
            return $this->getContentAsObject();
            
            case "draft_parsable_file_path":
            return $this->getParsableFilePath(true);
            
            case "live_parsable_file_path":
            return $this->getParsableFilePath();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    /* public function getCurrentSite(){
        
        
        
    } */
    
}