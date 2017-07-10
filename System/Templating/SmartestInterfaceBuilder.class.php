<?php

class SmartestInterfaceBuilder extends SmartestEngine{
    
    public function __construct($pid){
	    
	    parent::__construct($pid);
	    $this->_context = SM_CONTEXT_SYSTEM_UI;
	    $this->plugins_dir[] = SM_ROOT_DIR."System/Templating/Plugins/InterfaceBuilder/";
        $this->assign('sm_user_agent', SmartestPersistentObject::get('userAgent'));
	    
	}
	
	public function getDraftMode(){
	    return false;
	}
	
	public function renderBasicInput(SmartestParameterHolder $p){ // $datatype, $name, $existing_value='', $values=''
	    $info = SmartestDataUtility::getDataType($p->getParameter('type'));
	    // echo $datatype;
	    // echo $info['input']['template'];
	    // $_render_data = new SmartestParameterHolder('Input Render Data: '.$name);
	    // $_render_data->setParameter('options', $values);
	    // $_render_data->setParameter('name', $name);
	    // print_r($p);
	    $this->run(SM_ROOT_DIR.$info['input']['template'], array('_input_data'=>$p));
	}
    
    public function renderUrlPreview(SmartestExternalUrl $url, SmartestParameterHolder $data){
        $render_template = SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/oembed_preview.tpl';
        ob_start();
        $id = substr(md5($url->getValue()),0,8).'-'.SmartestStringHelper::random(4);
        $this->run($render_template, array(
            'url_data'=>$data,
            'url'=>$url,
            'id'=> $id
        ));
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
}