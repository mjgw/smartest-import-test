<?php

function smarty_function_edit_gallery($params, &$smartest_engine){
    
	if((isset($params['id']) && !empty($params['id'])) || (isset($params['name']) && !empty($params['name']))){
		return $smartest_engine->renderEditGalleryButton(isset($params['id']) ? $params['id'] : $params['name']);
	}else{
		return $smartest_engine->raiseError("Set order button error: 'id' or 'name' not properly specified.");
	}
    
}