<?php

function smarty_function_reorder_set($params, &$smartest_engine){
	if((isset($params['id']) && !empty($params['id'])) || (isset($params['name']) && !empty($params['name']))){
		return $smartest_engine->renderReorderSetButton(isset($params['id']) ? $params['id'] : $params['name']);
	}else{
		return "Set order button error: 'id' or 'name' not properly specified.";
	}
		
}