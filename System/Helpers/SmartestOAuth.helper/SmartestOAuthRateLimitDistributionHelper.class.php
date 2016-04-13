<?php

class SmartestOAuthRateLimitDistributionHelper{

    public static $accounts;
    private static $initialized = false;
    
    public static function init(){
    
        $services = SmartestOAuthHelper::getServices();
        $h = new SmartestOAuthHelper;
        $accounts = array();
        
        foreach($services as $s){
            $rough_accounts[$s->getParameter('shortname')] = $h->getAccounts($s->getParameter('id'), true);
            foreach($rough_accounts[$s->getParameter('shortname')] as $acct){
                $timekey = microtime(true)*10000;
                while(isset($accounts[$s->getParameter('shortname')][$timekey])){
                    $timekey++;
                }
                $accounts[$s->getParameter('shortname')][$timekey] = $acct;
            }
            ksort($accounts);
        }
        
        self::$accounts = $accounts;
        
        self::$initialized = true;
    
    }
    
    public static function getAccountForService($service_shortname){
        
        if(!self::$initialized){
            self::init();
        }
        
        if(isset(self::$accounts[$service_shortname])){
            if(count(self::$accounts[$service_shortname])){
                // print_r(array_keys(self::$accounts[$service_shortname]));
                $num_service_accounts = count(self::$accounts[$service_shortname]);
                if($num_service_accounts == 1){
                    $acct = array_shift(array_values(self::$accounts[$service_shortname]));
                    return $acct;
                }else{
                    $acct = array_shift(self::$accounts[$service_shortname]);
                    $timekey = microtime(true)*10000;
                    while(isset(self::$accounts[$service_shortname][$timekey])){
                        $timekey++;
                    }
                    // add account back on at end of array and ensure it is last
                    self::$accounts[$service_shortname][$timekey] = $acct;
                    ksort(self::$accounts[$service_shortname]);
                    return $acct;
                }
            }else{
                // no accounts for this service
                echo "No accounts exist for service '".$service_shortname."'";
            }
        }else{
            // unrecognised service
            echo "Service name '".$service_shortname."' is not reconised.";
        }
        
    }

}