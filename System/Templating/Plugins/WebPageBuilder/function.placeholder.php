<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_placeholder($params, &$smartest_engine){
	if(@$params['name']){
		
        if(isset($params['instance']) && strlen($params['instance'])){
            $instance_name = SmartestStringHelper::toVarName($params['instance']);
        }else{
            if(strlen($smartest_engine->getTemplateVariable('__parent_container_instance'))){
                $parent_instance = SmartestStringHelper::toVarName($smartest_engine->getTemplateVariable('__parent_container_instance'));
                $instance_name = '__child_of_'.$parent_instance;
            }else{
                $instance_name = 'default';
            }
        }
        
        $params['instance'] = $instance_name;
        
        // print_r($params);
        
        $name = SmartestStringHelper::toVarName($params['name']);
        
		return $smartest_engine->renderPlaceholder($name, $params, $smartest_engine->getPage());
        
	}else{
		return null;
	}
}
