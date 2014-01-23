<?php

class SmartestAuthenticationException extends SmartestException{
    
    protected $_controller;
    
    public function __construct(){
        $this->_controller = SmartestPersistentObject::get('controller');
    }
    
    public function lockOut($hash='session'){
        
        $this->setReturnCookie();
        $this->setPostVarsCookie();
        $e = new SmartestRedirectException();
        
        $c = SmartestPersistentObject::get('controller');
        
        if($c->getCurrentRequest()->getNamespace() == 'ajax' || $c->getCurrentRequest()->getNamespace() == 'modal'){
            
            header('HTTP/1.1 401 Unauthorized');
            // This is so that if a modal is summoned when the system has timed out, the modal will redirect the user to the login screen
            if($c->getCurrentRequest()->getNamespace() == 'modal'){
                echo '<script type="text/javascript">window.location="'.$this->_controller->getCurrentRequest()->getDomain().'smartest/login#'.$hash.'";</script>';
            }
            exit;
            
        }else{
            
            $helper = new SmartestAuthenticationHelper;
    	    // return $helper->getUserIsLoggedIn();
            
            if($this->_controller->getCurrentRequest()->getRequestString() == 'smartest'){
                if($helper->getUserIsLoggedIn()){
    		        $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login#unauthorized');
		        }else{
		            $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login');
		        }
    	    }else{
    	        $e->setRedirectUrl($this->_controller->getCurrentRequest()->getDomain().'smartest/login#'.$hash);
    	    }
	    
    		$e->redirect(array(401, 303), true);
		
	    }
		
    }
    
    public function setPostVarsCookie(){
        // TODO - any post vars that were submitted should be stored, so that they can be submitted when the user logs in again.
    }
    
    public function setReturnCookie(){
        
        if($this->_controller->getCurrentRequest()->getRequestString() != 'smartest'){
            if($this->_controller->getCurrentRequest()->getNamespace() != 'modal' && $this->_controller->getCurrentRequest()->getNamespace() != 'ajax' && !$this->_controller->getCurrentRequest()->isAjax()){
                $domain = $this->_controller->getCurrentRequest()->getDomain();
                SmartestCookiesHelper::setCookie('SMARTEST_RET', $this->_controller->getCurrentRequest()->getRequestStringWithVars(), 1); // The user has a day to log back in before this information is lost
            }
        }
        
    }

}