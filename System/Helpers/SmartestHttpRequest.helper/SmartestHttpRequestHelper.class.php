<?php

SmartestHelper::register('HttpRequest');

class SmartestHttpRequestHelper extends SmartestHelper{
	
    public static $_retrieved_pages;
    
	public static function getContent($address, $correctResources=true, $type='GET', $variables=''){
		
		if(self::curlInstalled()){
		
    		if(substr($address, 0, 7) != 'http://' && substr($address, 0, 8) != 'https://'){
    			$address = 'http://'.$address;
    		}
		    
            $key = substr(md5($address), 0, 16);
            
            if(isset(self::$_retrieved_pages[$key])){
                $page = self::$_retrieved_pages[$key];
            }else{
                $page = self::rawCurlRequest($address, $type, $variables);
                self::$_retrieved_pages[$key] = $page;
            }
		
    		/* $ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $address);
    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_USERAGENT, 'Smartest PageGrab [HTTP Request Helper], (Version '.SM_SYSTEM_VERSION.')');
		
    		if($type == 'POST'){
    			curl_setopt($ch, CURLOPT_POST, 1);
    		}
		
    		$page = curl_exec($ch);
    		curl_close($ch); */
		
    		if($correctResources){
		
    			$res = self::getExternalResources($page);
    			$protocol = (self::isSecure($address)) ? 'https://' : 'http://';
    			$protocol_length = strlen($protocol);
    			$hostname = self::getHostName($address);
			
    			$urls = self::getLinkUrls($page);
			
    			$already_processed = array();
			
    			if(is_array($urls)){
    				foreach($urls as $resource_url){
    				    if(!in_array($resource_url, $already_processed)){
    					    if($resource_url{0} == '/'){
					        
    					        $regexp = SmartestStringHelper::toRegularExpression($resource_url);
        						$regexp = '/href=[\'"]?'.$regexp.'[\'"]/';
    						
        						$page = preg_replace($regexp, 'href="'.$protocol.$hostname.$resource_url."\\1".'"', $page);
        						$already_processed[] = $resource_url;
    						
        					}else{
        						if(substr($resource_url, 0, $protocol_length) != $protocol && strlen($resource_url) > 1){
        							$regexp = SmartestStringHelper::toRegularExpression($resource_url);
            						$page = preg_replace('/href=[\'"]?'.$regexp.'[\'"]/', 'href="'.$protocol.$hostname.$resource_url."\\1".'"', $page);
            						$already_processed[] = $resource_url;
        						}
        					}
    				    }
    				}
    			}
			
    			$already_processed = array();
		
    			if(is_array($res)){
    				foreach($res as $resource_url){
    				    if(!in_array($resource_url, $already_processed)){
    					    if($resource_url{0} == '/'){
        						$page = str_replace($resource_url, $protocol.$hostname.$resource_url, $page);
        						$already_processed[] = $resource_url;
        					}else{
        						if(substr($resource_url, 0, $protocol_length) != $protocol){
        							$page = str_replace($resource_url, $protocol.$hostname.'/'.$resource_url, $page);
        							$already_processed[] = $resource_url;
        						}
        					}
    				    }
    				}
    			}
		
    		}
		
    		return $page;
		
		}else{
	        
	        return false;
	        
	    }
		
	}
	
	public static function curlInstalled(){
	    
	    return function_exists('curl_init');
	    
	}
	
	public static function rawCurlRequest($url, $type='GET', $variables=''){
	    
	    if(self::curlInstalled()){
	    
    	    $ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		// curl_setopt($ch, CURLOPT_USERAGENT, 'Smartest PageGrab [HTTP Request Helper], (Version '.SM_SYSTEM_VERSION.')');
    		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/536.30.1 (KHTML, like Gecko) Version/6.0.5 Safari/536.30.1');
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
    		if($type == 'POST'){
                
    			curl_setopt($ch, CURLOPT_POST, 1);
                
                if(is_array($variables)){
                    curl_setopt($ch, CURLOPT_POST, count($variables));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $variables));
                }elseif(strlen($variables)){
                    curl_setopt($ch, CURLOPT_POST, count(explode('&', $variables)));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $variables);
                }
                
    		}
		
    		$page = curl_exec($ch);
    		curl_close($ch);
		
    		return $page;
		
		}else{
	        return false;
	    }
	    
	}
	
	public static function getHostName($address){
		
		preg_match('/^https?:\/\/([\w\.-]{7,})\//', $address, $matches);
		return $matches[1];
	}
	
	public static function getHostAddress($address){
		
		// $host = self::getHostName($address);
		// return `host $host`;
	}
	
	public static function getExternalResources($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match_all('/<(link|script|img)[^>]+(href|src)=[\'"](\.{0,2}\/?[^\'"]+)/', $html, $matches);
		preg_match_all('/<style[^>]*>[\s\n]*@import\s*((url\()?[\'"]?([\w\.\/-]+)[\'"]?\)?);/', $html, $matches_2);
		
		// print_r($matches_2);
		
		$res = $matches[3];
		$css_imports = $matches_2[3];
		
		foreach($css_imports as $css){
			if(!in_array($css, $res)){
				$res[] = $css;
			}
		}
		
		return $res;
	}
	
	public static function getLinkUrls($page){
	    
	    if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match_all('/<a[^>]*\shref=[\'"]?([^\'"]+)[\'"]([^>]*)>([^(<\/)]*)<\/a>/', $html, $matches);
		preg_match_all('/(window\.location|document\.location\.href)=[\'"](\.{0,2}\/?[^\'"]+)/', $html, $matches_2);
		
		$urls = $matches[1];
		$css_imports = $matches_2[2];
		
		foreach($css_imports as $css){
			if(!in_array($css, $urls)){
				$urls[] = $css;
			}
		}
		
		return $urls;
		
	}
	
	public static function getTitle($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
        preg_match('/<title>\n?\s?(.+)\n?\s?<\/title>/m', $html, $matches);
        
        return $matches[1];
		
	}
	
	public static function getMetas($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
        preg_match_all('/<meta (name|http-equiv|property)=[\'"]?([^"\']+)[\'"]? content=[\'"]?([^"\']*)[\'"]?/m', $html, $matches);
		
		$metas = array();
        
        foreach($matches[0] as $key=>$meta){
            
            $try_key = SmartestStringHelper::toVarName($matches[2][$key]);
            $limit = 0;
            
            while(isset($metas[$try_key]) && $limit < 10){
                
                // Yes - preg_match() in a while loop is not a great idea. But this is a very rare case, and will never run more than 10x
                if(preg_match('/.+_(\d+)$/', $try_key, $matches_k)){
                    $num = $matches_k[1];
                    $next_num = (int) $matches_k[1]+1;
                    $rep = '_'.$num;
                    $try_key = str_replace($rep, '_'.$next_num, $try_key);
                    $limit++;
                }else{
                    $try_key = $try_key.'_2';
                }
            }
            
            $new_key = $try_key;
            
			$metas[$new_key]['name'] = $matches[2][$key];
			$metas[$new_key]['value'] = $matches[3][$key];
			$metas[$new_key]['type'] = $matches[1][$key];
            
		}
        
        return $metas;
		
	}
	
	public static function getOpenGraphMetas($url){
	    
	    $all_metas = self::getMetas($url);
	    $open_graph_metas = array();
	    
	    foreach($all_metas as $m){
	        
	        if(substr($m['name'], 0, 3) == 'og:'){
	            $open_graph_metas[$m['name']] = $m['value'];
	        }
	        
	    }
	    
	    return $open_graph_metas;
	    
	}
	
	public static function getOpenGraphThumbnailUrl($url){
	    
	    $og_metas = self::getOpenGraphMetas($url);
	    
	    if(isset($og_metas['og:image'])){
	        $image_url = $og_metas['og:image'];
	        // Fix agnostic links, just for the purposes of grabbing image
	        if(substr($image_url, 0, 2) == '/'.'/'){
	            $image_url = 'http:'.$image_url;
	        }
	        
	        $url = new SmartestExternalUrl($image_url);
	        $local_filename = end(explode('/', $image_url));
	        
	        try{
                SmartestFileSystemHelper::saveRemoteBinaryFile($url->getValue(), $local_filename, $url);
            }catch(SmartestException $e){
                SmartestLog::getInstance('system')->log('Remote Open Graph image file could not be saved: '.$e->getMessage());
                return false;
            }
	        
	    }else{
	        return null;
	    }
	    
	}
	
	public static function isSecure($address){
		return (substr($address, 0, 8) == 'https://');
	}
	
}