<?php

include_once(SM_ROOT_DIR.'System/Library/Mobile-Detect-2.8.3/Mobile_Detect.php');
include_once(SM_ROOT_DIR.'System/Library/PhpUserAgent-0.3.0/UserAgentParser.php');

SmartestHelper::register('UserAgent');

class SmartestUserAgentHelper extends SmartestHelper implements ArrayAccess{
	
	protected $_browser = array();
	protected $simpleObject;
	protected $_userAgent;
    protected $_preferences_helper;
	
	public function __construct(){
    	
    	$this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->_detector = new Mobile_Detect;
        $this->_preferences_helper = new SmartestPreferencesHelper;
    	
    	//language
		if($languages = getenv('HTTP_ACCEPT_LANGUAGE')){
			$languages = preg_replace('/(;q=[0-9]+.[0-9]+)/i','',$languages);
    	}else{
			$languages = 'en-us';
    	}
    	
        $this->_browser['language'] = $languages;
        
        $uadata = parse_user_agent();
        
        if(isset($uadata['platform'])){
            $this->_browser['platform'] = $uadata['platform'];
        }
        
        if(isset($uadata['browser'])){
            $this->_browser['appName'] = $uadata['browser'];
        }
        
        if(isset($uadata['browser'])){
            $this->_browser['appVersion'] = $uadata['version'];
            preg_match('/^\d+/', $uadata['version'], $matches);
            $this->_browser['appVersionInteger'] = (int) $matches[0];
        }
        
        $this_browser_key = SmartestStringHelper::toVarName($this->_browser['appName']);
        $old_browsers = SmartestYamlHelper::fastLoad(SM_ROOT_DIR.'System/Core/Types/old_browsers.yml');
        
        if(isset($old_browsers['first_reliable_versions'][$this_browser_key])){
            $is_supported_browser = (version_compare($this->_browser['appVersion'], $old_browsers['first_reliable_versions'][$this_browser_key]) >= 0);
        }else{
            $is_supported_browser = false;
        }
        
        $this->_browser['supported'] = $is_supported_browser;
        
        if(!defined('SM_USERAGENT_TYPE')){
            if($this->_detector->isMobile()){
                if($this->_detector->isTablet()){
                    define('SM_USERAGENT_TYPE', SM_USERAGENT_LARGE_MOBILE);
                }else{
                    define('SM_USERAGENT_TYPE', SM_USERAGENT_SMALL_MOBILE);
                }
            }else{
                if($this->_browser['supported']){
                    define('SM_USERAGENT_TYPE', SM_USERAGENT_NORMAL);
                }else{
                    define('SM_USERAGENT_TYPE', SM_USERAGENT_UNSUPPORTED_BROWSER);
                }
            }
        }
        
	}
	
	public function getPlatform(){
	    
	    if(!isset($this->_browser['platform'])){
	    
	        // detect platform	    
    	    /* if(preg_match('/Win(dows|98|95|32|16|NT|XP)/i', $this->_userAgent)) {
    	  	    $this->_browser['platform'] = 'Windows';
   		    }else if(stripos($this->_userAgent, 'Mac')) {
    	  	    $this->_browser['platform'] = 'Macintosh';
    	    }else if(stripos($this->_userAgent, 'linux')) {
    	  	    $this->_browser['platform'] = 'GNU/Linux';
            }else if(stripos($this->_userAgent, 'unix')) {
                $this->_browser['platform'] = 'Unix';
    	    }else{
    	 	    $this->_browser['platform'] = 'Unknown';
    	    } */
                
            $uadata = parse_user_agent();
    
            if(isset($uadata['platform'])){
                $this->_browser['platform'] = $uadata['platform'];
            }    
            
    	}
    	
    	return $this->_browser['platform'];
    	
	}
    
    public function getAppName(){
	    
	    if(!isset($this->_browser['appName'])){
	    
	        // detect browser program
		    /* if (stripos($this->_userAgent, 'MSIE')){
                $this->_browser['appName'] = 'Explorer';
    	    }else if (stripos($this->_userAgent, 'Safari')) {
                $this->_browser['appName'] = 'Safari';
    	    }else if (stripos($this->_userAgent, 'Chrome')) {
                $this->_browser['appName'] = 'Chrome';
    	    }else if (stripos($this->_userAgent, 'Firefox')) {
                $this->_browser['appName'] = 'Firefox';
    	    }else if (stripos($this->_userAgent, 'Opera')) {
                $this->_browser['appName'] = 'Opera';
            }else if (stripos($this->_userAgent, 'OmniWeb')) {
                $this->_browser['appName'] = 'OmniWeb';
            }else if (stripos($this->_userAgent, 'Netscape')) {
                $this->_browser['appName'] = 'Netscape';
            }else{
    		    $this->_browser['appName'] = 'Unknown';
    	    } */
            
            if(isset($uadata['browser'])){
                $this->_browser['appName'] = $uadata['browser'];
            }
            
    	}
    	
	    return $this->_browser['appName'];
	}
	
	public function getAppVersion(){
	    
	    if(!isset($this->_browser['appVersion'])){
	        
            if(isset($uadata['browser'])){
                $this->_browser['appVersion'] = $uadata['version'];
                preg_match('/^\d+/', $uadata['version'], $matches);
                $this->_browser['appVersionInteger'] = (int) $matches[0];
            }
            
    	    /* if($this->isExplorer()){
    	        // look for number after 'MSIE'
    	        preg_match('/MSIE\s?(\d+\.\d+)/i', $this->_userAgent, $matches);
    	        $this->_browser['appVersion'] = $matches[1];
    	        $this->_browser['appVersionInteger'] = (int) floor($matches[1]);
    	    }else if($this->isSafari()){
    	        preg_match('/Safari\/((\d+)(\.[\d]+)+)/i', $this->_userAgent, $matches);
    	        $build = $matches[2];
    	        // echo $build;
    	        switch($build){
    	            case 85:
    	            $this->_browser['appVersion'] = '1.0';
    	            $this->_browser['appVersionInteger'] = 1;
    	            break;
    	            case 100:
    	            $this->_browser['appVersion'] = '1.1';
    	            $this->_browser['appVersionInteger'] = 1;
    	            break;
    	            case 125:
    	            $this->_browser['appVersion'] = '1.2';
    	            $this->_browser['appVersionInteger'] = 1;
    	            break;
    	            case 312:
    	            $this->_browser['appVersion'] = '1.3';
    	            $this->_browser['appVersionInteger'] = 1;
    	            break;
    	            case 412:
    	            $this->_browser['appVersion'] = '2.0';
    	            $this->_browser['appVersionInteger'] = 2;
    	            break;
    	            case 416:
    	            $this->_browser['appVersion'] = '2.0.2';
    	            $this->_browser['appVersionInteger'] = 2;
    	            break;
    	            case 417:
    	            $this->_browser['appVersion'] = '2.0.3';
    	            $this->_browser['appVersionInteger'] = 2;
    	            break;
    	            case 419:
    	            $this->_browser['appVersion'] = '2.0.4';
    	            $this->_browser['appVersionInteger'] = 2;
    	            break;
    	            case 522:
    	            $this->_browser['appVersion'] = '3.0';
    	            $this->_browser['appVersionInteger'] = 3;
    	            break;
    	            case 525:
    	            $this->_browser['appVersion'] = '3.1.1';
    	            $this->_browser['appVersionInteger'] = 3;
    	            break;
    	            case 531:
    	            $this->_browser['appVersion'] = '4.0.3';
    	            $this->_browser['appVersionInteger'] = 4;
    	            break;
    	        }
    	    }else if($this->isFirefox()){
    	        preg_match('/Firefox\/(\d[\d\.]+\d+)/i', $this->_userAgent, $matches);
    	        $this->_browser['appVersion'] = $matches[1];
    	        $this->_browser['appVersionInteger'] = (int) $matches[1]{0};
    	    }else if($this->isCamino()){
        	    preg_match('/Camino\/(\d[\d\.]+\d+)/i', $this->_userAgent, $matches);
        	    $this->_browser['appVersion'] = $matches[1];
        	    $this->_browser['appVersionInteger'] = (int) $matches[1]{0};
        	    // echo $this->_userAgent;
        	} */
    	}
	    
	    return isset($this->_browser['appVersion']) ? $this->_browser['appVersion'] : null;
	}
	
	public function getAppVersionInteger(){
	    $this->getAppVersion();
	    return isset($this->_browser['appVersionInteger']) ? $this->_browser['appVersionInteger'] : null;
	}
	
	public function getRenderingEngineName(){
	    
	    if(!isset($this->_browser['engine'])){
	        
            switch($this->getAppName()){
                
                case "Safari":
                return "Webkit";
                
                case "Chrome":
                if($this->getAppVersionInteger() > 27){
                    return "Blink";
                }else{
                    return "Webkit";
                }
                
                case "Camino":
                case "Firefox":
                case "Iceweasel":
                case "icecat":
                return "Gecko";
                
                case "Konqueror":
                return "KHTML";
                
                case "MSIE":
                if($this->getAppVersionInteger() == 5 && $this->getPlatform() == 'Macintosh'){
                    return "Tasman";
                }else{
                    return "Trident";
                }
                
                case "Silk":
                case "Kindle":
                return "Blink";
                
                case "Lynx":
                case "Wget":
                case "Curl":
                return "None";
                
                case "Opera":
                if($this->getAppVersionInteger() > 14){
                    return "Blink";
                }else if($this->getAppVersionInteger() < 7){
                    return "Unknown";
                }else{
                    return "Presto";
                }
                
            }
            
	        // set engine
            /* if (stripos($this->_userAgent, 'Gecko')) { // rendering engines
                
                if (stripos($this->_userAgent, 'KHTML') === FALSE) {
                    $this->_browser['engine'] = 'Gecko';
                }else{
                    if(stripos($this->_userAgent, 'AppleWebKit') === FALSE){
                        $this->_browser['engine'] = 'KHTML';
                    }else{
                        $this->_browser['engine'] = 'AppleWebKit';
                    }
                }
                
            }else{
                if($this->isExplorer()){
                    if($this->isMacintosh()){
                        $this->_browser['engine'] = 'MSIE for MAC';
                    }else{
                        $this->_browser['engine'] = 'MSIE';
                    }
                }
            } */
        }
        
        return $this->_browser['engine'];
        
	}
    
	// platforms
	public function isMacintosh(){
		return $this->getPlatform() == "Macintosh" ? true : false;
	}
	
	public function isWindows(){
		return $this->getPlatform() == "Windows" ? true : false;
	}
	
	public function isLinux(){
		return $this->getPlatform() == "Linux" ? true : false;
	}
	
	public function isUnix(){
		return ($this->getPlatform() == "Unix" /* || ($this->isMacintosh() && $this->) */) ? true : false;
	}
    
    // Devices
    
    
	
	// rendering engines
	function isGecko(){
		return $this->getRenderingEngineName() == "Gecko" ? true : false;
	}
	
	// browsers 
	public function isSafari(){
		return $this->getAppName() == "Safari" ? true : false;
	}
	
	public function isExplorer(){
		return $this->getAppName() == "Explorer" ? true : false;
	}
	
	public function isFirefox(){
		return $this->getAppName() == "Firefox" ? true : false;
	}
	
	public function getLanguage(){
		return $this->_browser['language'];
	}
	
	public function getSimpleClientSideObject(){
	    if(is_object($this->simpleObject)){
	        return $this->simpleObject;
	    }else{
	        $this->simpleObject = new SmartestClientSideUserAgentObject;
	        $this->simpleObject->appName = $this->getAppName();
	        $this->simpleObject->appVersion = $this->getAppVersion();
	        $this->simpleObject->appVersionInteger = $this->getAppVersionInteger();
	        $this->simpleObject->platform = $this->getPlatform();
	        $this->simpleObject->engine = $this->getRenderingEngineName();
	        $this->simpleObject->supported = $this->_browser['supported'];
            $this->simpleObject->language = $this->_browser['language'];
	        return $this->simpleObject;
	    }
	}
	
	public function __toArray(){
	    $array = array();
	    $array['appName'] = $this->getAppName();
	    $array['appVersion'] = $this->getAppVersion();
	    $array['appVersionInteger'] = $this->getAppVersionInteger();
	    $array['platform'] = $this->getPlatform();
	    $array['engine'] = $this->getRenderingEngineName();
	    $array['language'] = $this->_browser['language'];
	    return $array;
	}
	
	public function getSimpleClientSideObjectAsJson(){
	    return json_encode($this->getSimpleClientSideObject());
	}
    
    public function offsetSet($o, $v){}
    public function offsetUnset($o){}
    public function offsetExists($o){}
        
    public function offsetGet($offset){
        
        $offset = SmartestStringHelper::toVarName($offset);
        
        switch($offset){
            
            case "name":
            case "appname":
            return $this->getAppName();
            
            case "version":
            return $this->getAppVersion();
            
            case "version_integer":
            case "vi":
            return $this->getAppVersionInteger();
            
            case "engine":
            return $this->getRenderingEngineName();
            
            case "platform":
            return $this->getPlatform();
            
            case "is_mobile":
            return $this->_detector->isMobile();
            
            case "is_phone":
            case "is_small_mobile":
            return (SM_USERAGENT_TYPE == SM_USERAGENT_SMALL_MOBILE);
            
            case "is_tablet":
            case "is_large_mobile":
            return (SM_USERAGENT_TYPE == SM_USERAGENT_LARGE_MOBILE);
            
            case "is_desktop":
            case "is_pc":
            return (SM_USERAGENT_TYPE == SM_USERAGENT_NORMAL || SM_USERAGENT_TYPE == SM_USERAGENT_UNSUPPORTED_BROWSER);
            
            case "is_supported_browser":
            return (SM_USERAGENT_TYPE == SM_USERAGENT_NORMAL);
            
            case "is_unsupported_browser":
            return (SM_USERAGENT_TYPE == SM_USERAGENT_UNSUPPORTED_BROWSER);
            
        }
            
    }

}

// Safari: Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3
// full list of user agents at: http://www.pgts.com.au/pgtsj/pgtsj0208c.html