<?php

class SmartestTextFragmentAttachment extends SmartestManyToManyLookup{
    
    protected $_defined = false;
    protected $_asset;
    protected $_textFragment;
    protected $_div_width = null;
    protected $_edit_link = null;
    protected $_thumbnail_image;
    
    public function getTextFragmentId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setTextFragmentId($id){
        $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getAttachedAssetId(){
        return $this->getEntityForeignKeyValue(2);
    }
    
    public function setAttachedAssetId($id){
        $this->setEntityForeignKeyValue(2, (int) $id);
    }
    
    public function hydrate($id){
        
        parent::hydrate($id);
        
        if(is_array($id) && isset($id['asset_id'])){
            $a = new SmartestRenderableAsset;
            $a->hydrate($id);
            $this->_asset = $a;
        }
        
        if(is_array($id) && isset($id['textfragment_id'])){
            $tf = new SmartestTextFragment;
            $tf->hydrate($id);
            $this->_textFragment = $tf;
        }
        
    }
    
    public function __toArray(){
        
        $data = parent::__toArray();
        $data['status'] = $this->hasAsset() ? 'DEFINED' : 'UNDEFINED';
        
        if($this->hasAsset()){
            $data['caption'] = $this->getCaption();
            $data['alignment'] = $this->getAlignment();
            $data['caption_alignment'] = $this->getCaptionAlignment();
            $data['float'] = $this->getFloat();
            $data['border'] = $this->getBorder();
            // $data['asset'] = $this->_asset->__toArray();
            if(!$this->_asset->isBinaryImage()){
                $data['manual_width'] = $this->getManualWidth();
            }
            $data['asset'] = $this->_asset;
        }
        
        return $data;
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "name":
            return $this->getInstanceName();
            
            case "status":
            return $this->hasAsset() ? 'DEFINED' : 'UNDEFINED';
            
            case "caption":
            return $this->hasAsset() ? $this->getCaption() : '';
            
            case "allow_resize":
            return $this->hasAsset() ? $this->getResizeImageResizeFlag() : false;
            
            case "zoom":
            return $this->hasAsset() ? $this->getZoomFromThumbnail() : false;
            
            case "thumbnail_relative_size":
            return $this->getResizeImageResizeFlag() ? $this->getThumbnailRelativeSize() : null;
            
            case "alignment":
            return $this->hasAsset() ? $this->getAlignment() : null;
            
            case "caption_alignment":
            return $this->hasAsset() ? $this->getCaptionAlignment() : null;
            
            case "float":
            return $this->hasAsset() ? $this->getFloat() : null;
            
            case "border":
            return $this->hasAsset() ? $this->getBorder() : null;
            
            case "edit_link":
            return $this->_edit_link;
            
            case "div_width":
            return $this->_div_width;
            
            case "asset":
            return $this->hasAsset() ? $this->_asset : null;
            
            case "thumbnail":
            return $this->getThumbnailImage();
            break;
            
            case "asset_object":
            case "asset":
            return $this->hasAsset() ? $this->_asset : null;
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function offsetSet($offset, $value){
        
        switch($offset){
            
            case "edit_link":
            $this->_edit_link = $value;
            break;
            
            case "div_width":
            $this->_div_width = $value;
            break;
            
        }
        
    }
    
    public function setAttachmentName($name){
        $this->setInstanceName($name);
    }
    
    public function getAttachmentName(){
        return $this->getInstanceName();
    }
    
    public function getCaption(){
        return $this->getContextDataField('caption');
    }
    
    public function setCaption($caption){
        $this->setContextDataField('caption', mysql_real_escape_string($caption));
    }
    
    public function getAlignment(){
        return $this->getContextDataField('align');
    }
    
    public function setAlignment($align){
        
        $align = strtolower($align);
        
        if(!in_array($align, array('left', 'center', 'right'))){
            $align = 'left';
        }
        
        $this->setContextDataField('align', $align);
    }
    
    public function getCaptionAlignment(){
        return $this->getContextDataField('caption_align');
    }
    
    public function setCaptionAlignment($align){
        
        $align = strtolower($align);
        
        if(!in_array($align, array('left', 'center', 'right'))){
            $align = 'left';
        }
        
        $this->setContextDataField('caption_align', $align);
    }
    
    public function getFloat(){
        return $this->getContextDataField('float');
    }
    
    public function setFloat($float){
        $this->setContextDataField('float', SmartestStringHelper::toRealBool($float));
    }
    
    public function getBorder(){
        return $this->getContextDataField('border');
    }
    
    public function setBorder($border){
        $this->setContextDataField('border', SmartestStringHelper::toRealBool($border));
    }
    
    public function getManualWidth(){
        return $this->getContextDataField('manual_width');
    }
    
    public function setManualWidth($width){
        $this->setContextDataField('manual_width', $width);
    }
    
    public function getResizeImageResizeFlag(){
        return $this->getContextDataField('allow_resize');
    }
    
    public function setResizeImageResizeFlag($allow_resize){
        $this->setContextDataField('allow_resize', (bool) $allow_resize);
    }
    
    public function getZoomFromThumbnail(){
        return $this->getContextDataField('zoom');
    }
    
    public function setZoomFromThumbnail($zoom){
        $this->setContextDataField('zoom', (bool) $zoom);
    }
    
    public function getThumbnailRelativeSize(){
        return $this->getContextDataField('thumbnail_relative_size');
    }
    
    public function setThumbnailRelativeSize($size){
        $this->setContextDataField('thumbnail_relative_size', (int) $size);
    }
    
    public function getIsImage(){
        if($this->hasAsset()){
            return $this->_asset->isBinaryImage();
        }
    }
    
    public function hasAsset(){
        return (is_object($this->_asset) && $this->_asset->getUrl());
    }
    
    public function getAsset(){
        return $this->_asset;
    }
    
    public function setAsset(SmartestAsset $asset){
        return $this->_asset = $asset;
    }
    
    public function getThumbnailImage(){
        if($this->_asset->isImage()){
            $percentage = $this->getThumbnailRelativeSize() > 1 ? $this->getThumbnailRelativeSize() : 10;
            $this->_thumbnail_image = $this->_asset->getImage()->getResizedVersionFromPercentage($percentage);
            return $this->_thumbnail_image;
        }
    }
    
    public function hasTextFragment(){
        return (is_object($this->_textFragment) && $this->_textFragment->getId());
    }
    
    public function getTextFragment(){
        return $this->_textFragment;
    }
    
    public function setAttachedAssetAdditionalRenderDataParameter($name, $value){
        $this->_asset->setSingleAdditionalRenderDataParameter($name, $value);
    }
    
    public function save(){
        
        if(!$this->getType()){
            $this->setType('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        }
        
        parent::save();
        
    }
    
}