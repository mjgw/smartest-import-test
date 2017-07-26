<?php

class SmartestLicense extends SmartestObject implements SmartestBasicType, SmartestStorableValue, SmartestSubmittableValue, SmartestJsonCompatibleObject{
    
    protected $_shortname;
    protected $_allowsoftwareonlylicenses;
    protected $_data = null;
    
    public function __construct($value=null){
        $this->setValue($value);
    }
    
    public static function getLicenseData(){
        
        $raw = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/licenses.yml');
        $licenses = $raw['licenses'];
        
        foreach($licenses as $k=>$v){
            $licenses[$k]['shortname'] = $k;
            if(isset($licenses[$k]['copyleft']) && SmartestStringHelper::toRealBool($licenses[$k]['copyleft'])){
                $licenses[$k]['copyleft'] = true;
            }else{
                $licenses[$k]['copyleft'] = false;
            }
        }
        
        return $licenses;
        
    }
    
    public function getValue(){
        
        return $this->_shortname;
        
    }
    
    public function setValue($value){
        
        $all = self::getLicenseData();
        $this->_data = new SmartestParameterHolder('License info');
        
        if(isset($all[$value])){
            $this->_data->loadArray($all[$value]);
            $this->_shortname = $value;
            return true;
        }else{
            return false;
        }
        
    }
    
    public function hydrateFromStorableFormat($stored_format){
        return $this->setValue($stored_format);
    }
    
    public function getStorableFormat(){
        return $this->_shortname;
    }
    
    public function hydrateFromFormData($data){
        return $this->setValue($data);
    }
    
    public function stdObjectOrScalar(){
        $obj = new stdClass;
        $obj->label = $this->_data->getParameter('label');
        $obj->id = $this->_shortname;
        $obj->object_type = 'license';
        return $obj;
    }
    
    public function renderInput($params){
        
    }
    
    public function isPresent(){
        return is_object($this->_data);
    }
    
    public function __toString(){
        if($this->_data){
            return $this->_data->getParameter('label');
        }else{
            return 'None';
        }
    }
    
    public function offsetExists($offset){
        if($this->_data){
            return $this->_data->offsetExists($offset);
        }else{
            return false;
        }
    }
    
    public function offsetGet($offset){
        
        if($this->_data){
            return $this->_data->getParameter($offset);
        }else{
            return '';
        }
        
    }
    
}