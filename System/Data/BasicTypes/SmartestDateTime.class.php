<?php

class SmartestDateTime implements SmartestBasicType, ArrayAccess, SmartestStorableValue, SmartestSubmittableValue, SmartestSearchableValue, SmartestJsonCompatibleObject{
    
    protected $_value;
    protected $_all_day = false;
    protected $_time_format = "g:ia";
    protected $_day_format = "l jS F, Y";
    protected $_sync_value_to_now = false;
    protected $_is_never = false;
    protected $_resolution = SM_DATETIME_RESOLUTION_MINUTES;
    
    const NEVER = '%NEVER%';
    const NOW = '%NOW%';
    
    const RESOLUTION_SECONDS = 1;
    const RESOLUTION_MINUTES = 2;
    const RESOLUTION_HOURS   = 4;
    const RESOLUTION_DAYS    = 8;
    const RESOLUTION_MONTHS  = 16;
    const RESOLUTION_YEARS   = 32;
    
    public function __construct($date=''){
        if($date == self::NEVER){
            $this->_is_never = true;
        }else{
            $this->setValue($date);
        }
    }
    
    public function setValue($v){
        
        if($v == self::NOW){
            $this->_value = time();
            $this->_sync_value_to_now = true;
            $this->_is_never = false;
            return true;
        }else if($v == self::NEVER || $v == '0' || !$v){
            $this->_is_never = true;
        }else if(is_array($v)){
            $this->setValueFromUserInputArray($v);
            $this->_is_never = false;
            return true;
        }else if(is_numeric($v) && $v != '0'){
            $this->_value = (int) $v;
            $this->_is_never = false;
            return true;
        }else if(strlen($v) == 19){ // this is the fastest way to check for the format YYYY-MM-DD hh:ii:ss
            $this->setValueFromUserInputArray(array(
                'h' => substr($v, 11, 2),
                'i' => substr($v, 14, 2),
                's' => substr($v, 17, 2),
                'Y' => substr($v, 0, 4),
                'M' => substr($v, 5, 2),
                'D' => substr($v, 8, 2)
            ));
            $this->_is_never = false;
            return true;
        }else{
            return $this->_value = strtotime($v);
        }
    }
    
    public function isPresent(){
        return (bool) $this->_value && !$this->_is_never;
    }
    
    public function setValueFromUserInputArray($v){
        $this->hydrateFromFormData($v);
    }
    
    public function getValue($format="l jS F, Y"){
        if(strlen($format)){
            return date($format, $this->_value);
        }else{
            return $this->_value;
        }
    }
    
    public function getResolution(){
        return $this->_resolution;
    }
    
    public function setResolution($r){
        $this->_resolution = $r;
    }
    
    public function getUnixFormat(){
        
        if($this->_is_never){
            return 0;
        }
        
        if($this->_sync_value_to_now){
            return time();
        }else{
            return $this->_value;
        }
    }
    
    public function getNowDeltaRaw(){
        
        return time()-$this->_value;
        
    }
    
    public function getNowDeltaFormatted(){
        
        if($this->_is_never){
            return 'Never';
        }
        
        // get delta between $time and $currentTime
        $delta = abs(time() - $this->_value);
        
        if($delta > 0){

            // if delta is more than 7 days print the date
            if ($delta > 60 * 60 * 24 *7 ) {
                // return $timeToCompare;
                $weeks = floor($delta / (60*60*24*7));
                return $weeks . " weeks";
            }   

            // if delta is more than 24 hours print in days
            else if ($delta > 60 * 60 *24) {
                $days = ceil($delta / (60*60*24));
                return $days . " days";
            }

            // if delta is more than 60 minutes, print in hours
            else if ($delta > 60 * 60){
                $hours = ceil($delta / (60*60));
                return $hours . " hours";
            }

            // if delta is more than 60 seconds print in minutes
            else if ($delta > 60) {
                $minutes = ceil($delta / 60);
                return $minutes . " minutes";
            }
            
            else if ($delta > 40) {
                return "one minute";
            }

            // actually for now: if it is less or equal to 60 seconds, just say it is a minute
            return $delta . " seconds";
        
        }
        
    }
    
    public function getSearchQueryMatchableValue(){
        return $this->_value;
    }
    
    /*
    
    $hours = floor($total_time/3600);
	$rounded_hours = ceil($total_time/3600);
	
	$remaining_time = $total_time-($hours*3600);
	$minutes = floor($remaining_time/60);
	
	$remaining_time -= $minutes*60;
	$seconds = ceil($remaining_time);
	
	return array("H"=>$hours, "M"=>$minutes, "S"=>$seconds, "R"=>$rounded_hours);
    
    */
    
    public function __toString(){
        
        if($this->_is_never){
            return (string) $this->_value;
        }
        
        if($this->_all_day){
            if($this->_sync_value_to_now){
                return date($this->_day_format, time());
            }else{
                return date($this->_day_format, $this->_value);
            }
        }else{
            if($this->_sync_value_to_now){
                return date($this->_time_format.' \o\n '.$this->_day_format, time());
            }else{
                return date($this->_time_format.' \o\n '.$this->_day_format, $this->_value);
            }
        }
    }
    
    public function stdObjectOrScalar(){
        return date('c', $this->_value);
    }
    
    // The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        
        if($this->_is_never){
            return '0';
        }
        
        if($this->_sync_value_to_now){
            return self::NOW;
        }else{
            return $this->_value;
        }
    }
    
    public function hydrateFromStorableFormat($v){
        return $this->setValue($v);
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        
        if(isset($v['is_never']) && (bool) $v['is_never']){
            $this->_is_never = true;
            $this->_value = 0;
            return true;
        }else{
            $this->_is_never = false;
        }
        
        if(!is_array($v)){
            return $this->setValue($v);
        }
        
        if(isset($v['h'])){
            $hour = $v['h'];
        }else{
            $hour = 1;
        }
        
        if(isset($v['i'])){
            $minute = $v['i'];
        }else{
            $minute = 1;
        }
        
        if(isset($v['s'])){
            $second = $v['s'];
        }else{
            $second = 1;
        }
        
        if(isset($v['Y'])){
            $year = $v['Y'];
        }else{
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        if(isset($v['M'])){
            $month = $v['M'];
        }else{
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        if(isset($v['D'])){
            $day = $v['D'];
        }else{
            throw new SmartestException("Arrays passed to SmartestDateTime::hydrateFromFormData() must have Y, M, and D keys");
        }
        
        $this->_value = mktime($hour, $minute, $second, $month, $day, $year);
        
        return true;
        
    }
    
    public function getWithCustomFormat($format){
        
        return date($format, $this->_value);
        
    }
    
    public function offsetExists($offset){
	    
	    return in_array($offset, array('g', 'i', 'a', 'm', 's', 'h', 'Y', 'year', 'M', 'D', 'H', 'unix', 'raw', 'month_name', 'D', 'mysql_day', 'empty', 'in_past', 'has_passed', 'in_future', 'now_delta_raw', 'now_delta_formatted'));
	    
	}
	
	public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case 'i':
	        return date('i', $this->_value);
	        
	        case 'h':
	        return date('h', $this->_value);
	        
	        case 'H':
	        return date('H', $this->_value);
	        
	        case 's':
	        return date('s', $this->_value);
	        
	        case 'Y':
	        case 'year':
	        return date('Y', $this->_value);
	        
	        case 'M':
	        return date('m', $this->_value);
	        
	        case 'month_name':
	        return date('F', $this->_value);
	        
	        case 'D':
	        return date('d', $this->_value);
	        
	        case 'unix':
	        case 'raw':
            case 'value':
	        return (int) $this->getUnixFormat();
	        
	        case 'mysql_day':
	        return date('Y-m-d', $this->_value);
	        
	        case 'empty':
	        return !$this->isPresent();
	        
	        case 'day_only':
	        return date($this->_day_format, $this->_value);
	        
	        case 'time_only':
            if($this->_is_never){
                return 'Never'; 
            }else{
                return date($this->_time_format, $this->_value);
            }
	        
	        case 'month_only':
	        return date('F Y', $this->_value);
	        
	        case 'in_past':
	        case 'has_passed':
	        return new SmartestBoolean(!$this->_is_never && time() > $this->_value);
	        
	        case 'in_future':
	        return new SmartestBoolean(!$this->_is_never && time() < $this->_value);
	        
	        case 'now_delta_raw':
	        return $this->getNowDeltaRaw();
            
            case 'is_never':
            return $this->_is_never;
	        
	        case 'now_delta_formatted':
	        return $this->getNowDeltaFormatted();
	        
	        default:
	        return date($offset, $this->_value);
	        
	    }
	    
	}
	
	public function offsetSet($offset, $value){
	    // read only
	}
	
	public function offsetUnset($offset){
	    // read only
	}
    
}