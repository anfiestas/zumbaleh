<?php 
require_once 'Objects/Constants.php';
class HttpRequest {
    
    
    public function httpGetExecute($url,$params)
    {
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_GET, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //gets output
	     
		$xmldata = curl_exec($ch);
	       
		curl_close($ch);
                
                return $xmldata;
      
    }
    
    public function HttpPostExecute($url,$params)
    {$writer = new Zend_Log_Writer_Stream('../private/logs/sms-http-request.log');
	    $logger = new Zend_Log($writer);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //gets output
	        
		$xmldata = curl_exec($ch);
		$info = curl_getinfo($ch);
		 
		$logger->info($url.$params." | result: HTTP:".$info['http_code']. " - ". $xmldata);
		
		if($xmldata==null || $info['http_code'] != Constants::HTTP_OK){
		  //INTERNAL_PROVIDER_ERROR not delivered
		  $xmldata="ERROR";
		}
		
		curl_close($ch);
                
        return $xmldata;
          
    }

    public function HttpBasicAuthPostExecute($user,$pass,$url,$params)
    {   
    	$writer = new Zend_Log_Writer_Stream('../private/logs/sms-http-request.log');
	    $logger = new Zend_Log($writer);

    	$auth = $user . ':' . $pass;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_POST, 3);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_USERPWD, $auth);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //gets output
	        
		$xmldata = curl_exec($ch);
		$info = curl_getinfo($ch);

		$logger->info($url.$params." | result: HTTP:".$info['http_code']. " - ". $xmldata);

		 
		if($xmldata==null || $info['http_code'] != Constants::HTTP_OK_CREATED){
		  //INTERNAL_PROVIDER_ERROR not delivered
		  $xmldata="ERROR";
		}
		
		curl_close($ch);
                
        return $xmldata;
          
    }

}
?>