<?php

class SmartestString extends SmartestObject implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue, SmartestSearchableValue, SmartestJsonCompatibleObject{
	
	protected $_string = '';
	
    public function __construct($string=''){
        $this->setValue($string);
    }
    
    public function __toString(){
        if(defined('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && constant('SM_CMS_PAGE_CONSTRUCTION_IN_PROGRESS') && defined('SM_CMS_PAGE_ID') && !SmartestStringHelper::containsEscapedEntities($this->_string)){
            if(SmartestStringHelper::containsEscapedXmlEntities($this->_string)){
                return (string) str_replace('///', '<br />', $this->_string);
            }else{
                return (string) str_replace('///', '<br />', SmartestStringHelper::toXmlEntities($this->_string));
            }
        }else{
            return (string) $this->_string;
        }
    }
    
    public function __toJsonCompatible(){
        return $this->_string;
    }
    
    public function stdObjectOrScalar(){
        return $this->_string;
    }
    
    public function setValue($v){
        if(strlen($v)){
            $this->_string = (string) $v;
        }else{
            $this->_string = '';
        }
    }
    
    public function getValue(){
        return $this->_string;
    }
    
    public function getWordCount(){
        return SmartestStringHelper::getWordCount($this->_string);
    }
    
    public function isPresent(){
        return (bool) strlen($this->_string);
    }
    
    public function getSearchQueryMatchableValue(){
        return $this->_string;
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_string;
    }
    
    public function hydrateFromStorableFormat($v){
        $this->setValue($v);
        return true;
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        $this->setValue($v);
        return true;
    }
    
    public function toSlug(){
    	return SmartestStringHelper::toSlug($this->_string);
    }
    
    public function toVarName(){
    	return SmartestStringHelper::toVarName($this->_string);
    }
    
    public function toConstantName(){
    	return SmartestStringHelper::toConstantName($this->_string);
    }
    
    public function toCamelCase(){
    	return SmartestStringHelper::toCamelCase($this->_string);
    }
    
    public function toHexUrlEncoded(){
        return SmartestStringHelper::toHexUrlEncoded($this->_string);
    }
    
    public function toHtmlEncoded(){
        return SmartestStringHelper::toHtmlEntitiesSmart($this->_string);
    }
    
    public function toXmlEncoded(){
        return SmartestStringHelper::toXmlEntitiesSmart($this->_string);
    }
    
    public function isMd5Hash(){
    	return SmartestStringHelper::isMd5Hash($this->_string);
    }
    
    public function toParagraphsArray(){
        return SmartestStringHelper::splitByDoubleLineBreaks($this->_string);
    }
    
    public function offsetExists($offset){
        return in_array(strtolower($offset), array('slug', 'varname', 'constantname', 'camelcase', 'is_md5', 'length', 'paragraphs', 'encoded', 'urlencoded', 'wordcount', 'xmlentities', 'html_escape'));
    }
    
    public function offsetGet($offset){
        
        switch(strtolower($offset)){
            case "slug":
            return $this->toSlug();
            case 'varname':
            return $this->toVarName();
            case "constantname":
            return $this->toConstantName();
            case 'camelcase':
            return $this->toCamelCase();
            case "is_md5":
            return $this->isMd5Hash();
            case "length":
            return strlen($this->_string);
            case "paragraphs":
            return SmartestStringHelper::toParagraphs($this->_string);
            case "first_paragraph":
            case "first_para":
            return SmartestStringHelper::getFirstParagraph($this->_string);
            case "convert_line_breaks":
            return SmartestStringHelper::splitBySingleLineBreaks($this->_string);
            case "encoded":
            return $this->toHexEncoded();
            case "urlencoded":
            return urlencode($this->_string);
            case "wordcount":
            case "num_words":
            return $this->getWordCount();
            case "charcount":
            case "charactercount":
            return strlen($this->_string);
            case "contentlength":
            return strlen(trim($this->_string));
            case "paracount":
            return count(SmartestStringHelper::splitBySingleLineBreaks($this->_string));
            case "textile":
            return SmartestStringHelper::parseTextile($this->_string);
            case "xmlentities":
            return (string) SmartestStringHelper::toXmlEntitiesSmart($this->_string);
            case "html_escape":
            return htmlspecialchars($this->_string, ENT_QUOTES, 'UTF-8', false);
            case "empty":
            return (strlen($this->_string) == 0);
            case "lower":
            case "lowercase":
            return strtolower($this->_string);
            case "upper":
            case "uppercase":
            return strtoupper($this->_string);
            case "title":
            case "titlecase":
            return SmartestStringHelper::toTitleCase($this->_string);
            case "titlecase_strict":
            return SmartestStringHelper::toTitleCase($this->_string, true);
            case "_php_class":
            return get_class($this);
            case "raw":
            return $this->_string;
            default:
            if(preg_match('/^truncate_(\d+)$/', $offset, $matches)){
                return SmartestStringHelper::truncate($this->_string, $matches[1]);
            }
            return $this->_string;
        }
    }
 
}