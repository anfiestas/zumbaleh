<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';

class Tm4bProvider implements ISmsProvider 
{
    private $provider_id=1;//TM4B
    private $url = "https://www.tm4b.com/client/api/http.php";
  
        public function getProviderId(){
	     return $this->provider_id;
	} 
         /*tm4b response:
		   <broadcastID>MT0017295001</broadcastID>
		   <recipients>1</recipients>
		   <balanceType>GBP</balanceType>
		   <credits>7</credits>
		   <balance>2041.6</balance>
		   <neglected>-</neglected>
		   
		   or
		   
		   error(0001|Invalid Username or Password)
	*/
	public function sendSms($from,$to,$text)  
	{
	    $userBalance=0;
	    $messageId=0;
	    $broadcastID=0;
	    $errorCode="0-OK";
		
		$to_filter=explode(",",$to);
		$to=$to_filter[0];
		$params="username=n2bolsa&password=n2bolsacrak1&type=broadcast&version=2.1&msg=".urlencode($text)."&to=".urlencode($to)."&from=".urlencode($from);//."&sim=yes";
		
		
		//try to send message	
		$request = new HttpRequest();
		$xmldata = $request->httpPostExecute($this->url,$params);
		 
		 //INTERNAL_SERVER_ERROR   
		 if($xmldata=="ERROR")
		 {
		    //internal error tm4b
		    //throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
		    $response=$xmldata;
		 }
		 else
		 {
		    //IF HTTP STATUS 200
		    $xmlParse = new XmlParse($xmldata);
		    
		    if(substr($xmldata,0,5)=="error")
		    {
			$replaceThis = array("error(", ")");
			$error=str_replace($replaceThis, "", $xmldata);
			
			$error_provider=explode("|",$error);
			//TODO ERROR TREATMENT
			if($error_provider[0]=="0002")
			{
			    //do error trearment
			    
			}
			throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
			//$errorCode="3-error internal tm4b error";    
		    }
		    else
		    {
			// NO ERROR THEN
		       $externalId=$xmlParse->getValueByTagName("broadcastID");
		       //$balance_n2=$xmlParse->getValueByTagName("balance");
		       //$realCost=$xmlParse->getValueByTagName("credits");
		       
		       $response=$externalId;
		    }
		 }
	    
	      
             return $response;
	 
	}
	
	 /*tm4b response:
              <report>DELIVRD|1001131902</report>
        */  
	public function updateSmsStatus($messageBroadcast,$status)  
	{
	    // There is no
	}
	
}
?>