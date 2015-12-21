<?php

class UsersAjax extends SmartestSystemApplication{

    public function tagUserWithString(){
        
        $u = new SmartestUser;
        $user_id = (int) $this->getRequestParameter('user_id');
        $tag_string = $this->getRequestParameter('tag_text');
        
        if($u->find($user_id)){
            $u->tag($tag_string);
        }
        
        exit;
        
    }
    
    public function untagUserWithString(){
        
        $u = new SmartestUser;
        $user_id = (int) $this->getRequestParameter('user_id');
        $tag_string = $this->getRequestParameter('tag_text');
        
        if($u->find($user_id)){
            $u->tag($tag_string);
        }
        
        exit;
        
    }
    
    public function untagUserWithTagId(){
        
        $u = new SmartestUser;
        $user_id = (int) $this->getRequestParameter('user_id');
        $tag_id = (int) $this->getRequestParameter('tag_id');
        
        if($u->find($user_id)){
            $u->removeTagWithId($tag_id);
        }
        
        exit;
        
    }

}