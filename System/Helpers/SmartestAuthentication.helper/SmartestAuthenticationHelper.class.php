<?php

SmartestHelper::register('Authentication');

class SmartestAuthenticationHelper extends SmartestHelper{

	private $database;
	protected $userId;
	protected $user;
	// protected $userLoggedIn;
	
	public function __construct(){
		
		$this->database = SmartestPersistentObject::get('db:main');
		
        /* if(session_is_registered()){
            
        }
        
		if(SmartestSession::get('user:isAuthenticated')){
			// $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
		}else{
			// $this->userLoggedIn = false;
			SmartestSession::set('user:isAuthenticated', false);
		} */	
	}

	public function newLogin($username, $password, $service='smartest', $use_email=false){
		if($user = $this->checkLoginDetails($username, $password, $service, $use_email)){
			return $user;
		}else{
			return false;
		}
	}
	
	public function checkLoginDetails($username, $password, $service, $use_email=false){
		
		// What kind of user object should be instantiated
		if(strtolower($service) == 'smartest'){
		    $userObj = new SmartestSystemUser;
		    $require_smartest = true;
		}else{
		    $userObj = new SmartestUser;
		    $require_smartest = false;
	    }
	    
	    if($use_email && SmartestStringHelper::isEmailAddress($username)){
	        $findby_field = 'email';
	    }else{
	        $findby_field = 'username';
	    }
	    
	    if(strpos($username, ' ') !== false){
	        // there is a space in the username, which Smartest never allows, so this can't be a username. return false for added security
            SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed for username \''.$username.'\' because it contained a space.');
	        return false;
	    }
	    
	    // Does that username exist?
		if($userObj->findBy($findby_field, $username)){
			
			if($require_smartest && $userObj->getType() != 'SM_USERTYPE_SYSTEM_USER'){
                SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed for username \''.$username.'\' because it is not a system user account and does not have permission to access the Smartest backend.');
			    return false;
			}
			
			if($userObj->getActivated()){
		        
		        if(strlen($userObj->getPasswordSalt())){
		            
		            if($userObj->getPassword() === md5($password.$userObj->getPasswordSalt())){
		                
                        if(get_class($userObj) == 'SmartestSystemUser'){
                            $userObj->getTokens();
                        }
    			        
                        SmartestSession::start();
    			        SmartestSession::set('user:isAuthenticated', true);
    			        
    			        if($userObj->getType() == 'SM_USERTYPE_SYSTEM_USER'){
    			            SmartestSession::set('user:isAuthenticatedToCms', true);
    			        }
    			        
    			        // $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');
    			        
    			        // Give the user a new password salt every time they log in
    			        $userObj->setPasswordWithSalt($password, SmartestStringHelper::random(40), true);
    			        $userObj->setLastVisit(time());
    			        $userObj->save();
                        SmartestLog::getInstance('auth')->log('User \''.$username.'\' authenticated successfully from IP address '.$_SERVER['REMOTE_ADDR'].'.');
		
        			    return $userObj;
    			    
			        }else{
			            
                        SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed for username \''.$username.'\' because the password supplied was incorrect.');
			            return false;
			            
			        }
    			
			    }else{
			        
			        if($userObj->getPassword() === md5($password)){
			            
			            $userObj->getTokens();
			            $userObj->setPasswordWithSalt($password, SmartestStringHelper::random(40), true);
			            $userObj->setLastVisit(time());
			            $userObj->save();
                        
                        SmartestLog::getInstance('auth')->log('User \''.$username.'\' authenticated successfully from IP address '.$_SERVER['REMOTE_ADDR'].'. Their password was not salted, but a salt has now been added.');
			            
                        SmartestSession::start();
                        
			            if($userObj->getType() == 'SM_USERTYPE_SYSTEM_USER'){
    			            SmartestSession::set('user:isAuthenticatedToCms', true);
    			        }
			            
    			        SmartestSession::set('user:isAuthenticated', true);
    			        // $this->userLoggedIn =& SmartestSession::get('user:isAuthenticated');

        			    return $userObj;
			            
			        }else{
			            
			            return false;
			            
			        }
			        
			    }
		
		    }else{
	            
	            // The user is not activated
                SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed for username \''.$username.'\' because that user account is not activated.');
		        return false;
	        
		    }
			
		}else{
            if($findby_field == 'email'){
                SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed using email address \''.$username.'\' because no account with that email address could be found.');
            }else{
                SmartestLog::getInstance('auth')->log('Attempted login from IP address '.$_SERVER['REMOTE_ADDR'].' failed for username \''.$username.'\' because no account with that username could be found.');
            }
			return false;
		}
	}
	
	public function startSessionAsUser(SmartestUser $u){
	    
	    if(SmartestSession::get('user:isAuthenticated')){
			// User is already logged in
			return false;
		}else{
			
            SmartestSession::start();
            
			$u->setLastVisit(time());
	        $u->save();
			
			if($u->getType() == 'SM_USERTYPE_SYSTEM_USER'){
	            SmartestSession::set('user:isAuthenticatedToCms', true);
	        }
            
	        SmartestSession::set('user:isAuthenticated', true);
	        
	        SmartestSession::set('user', $u);
	        
	        return true;
			
		}
	    
	}
	
	public function getUserIsLoggedIn(){
		
        if(SmartestSession::get('user:isAuthenticated')){
			return true;
		}else{
			return false;
		}
	}
	
	public function getSystemUserIsLoggedIn(){
		
		if(SmartestSession::get('user:isAuthenticatedToCms')){
			return true;
		}else{
			return false;
		}
	}
	
	public function logout(){
        
        $u = SmartestSession::get('user');
        
        if($u instanceof SmartestUser){
            SmartestLog::getInstance('auth')->log('User \''.$u->getUsername().'\' logged out manually.');
        }
        
		SmartestSession::set('user:isAuthenticated', false);
		SmartestSession::clearAll();
		$this->user = array();
        
	}

}