<?php

function smarty_function_site_favicon($params, &$smartest_engine){
    
    /* if(isset($params['id']) && is_numeric($params['id'])){
        $asset_id = $params['id'];
    }else if(isset($params['name']) && strlen($params['name'])){
        $asset_id = $params['name'];
    }
    
    if(isset($params['path'])){
        $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
    }else{
        $path = 'none';
    } */
        
    // echo SM_CMS_PAGE_SITE_ID;
    
    if($site = $smartest_engine->getPage()->getSite()){
        
        if($site->getFaviconId() && is_object($site->getFavicon()) && is_file($site->getFavicon()->getFullPathOnDisk())){
            // $hour_var = floor(time() / 3600) * 3600;
            // $params['hour_var'] = $hour_var;
            // if(){
                $hash = substr(md5_file($site->getFavicon()->getFullPathOnDisk()),0,8);
                $params['hash'] = $hash;
                // var_dump($hash);
                return $smartest_engine->renderAssetById($site->getFaviconId(), $params);
            // }
        }
    }
    
    // return $smartest_engine->renderAssetById($asset_id, $params, $path);
    
}