<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';

class Sms42TelecomProvider implements ISmsProvider 
{
    private $provider_id=5;
    private $url = "http://server2.msgtoolbox.com/api/current/send/message.php";
  
        public function getProviderId(){
	     return $this->provider_id;
	}
	
	public function sendSms($from,$to,$text)  
	{
	    $userBalance=0;
	    $messageId=0;
	    $broadcastID=0;
	    $errorCode="0-OK";
		
		$to_filter=explode(",",$to);
		$to=$to_filter[0];
		
		//to delete 00 international prefix
		if(substr($from,0,2)=="00")
		    $from=substr($from,2,strlen($from));
		if(substr($to,0,2)=="00")
		    $to=substr($to,2,strlen($to));
		
		/*****ATTENTION FIX 10133 as senderID *****/
		$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
		
		//get country Code
		$countryCode=substr($from,0,2);
		
		//If countryCode is french number then we replace the 33(countryCode) by ww (localCode)
    
		if ($countryCode == "33" && is_numeric($from))
		    $from="ww".substr($from,2,strlen($from));
		
		$logger->info("42IT From: ".$from);		
		/*****END FIX *********************/
		
		$params="username=n2bolsa&password=59960549&message=".urlencode($text)."&to=".urlencode($to)."&from=".urlencode($from)."&route=G1";
		
		//try to send message	
		$request = new HttpRequest();
		
		$xmldata = $request->httpPostExecute($this->url,$params);
		
		if($xmldata == "ERROR"){
				$response=$xmldata;   
		}
		else{
		   
		   $result = explode(",", $xmldata);
		   
		   $resultCode = $result[0];
		   
		   $resultValue = $result[1];
		    
		    if($resultCode=="0")
		    {
		        $response="ERROR";
			/*1: Bad logindetails
			2: Problem with the message
			3: Bad to number
			4: Not enough credits to send message
			*/
			$errorCode = $resultValue;
				
		    }
		    else if($resultCode=="1") 
		    {
			// NO ERROR THEN
		          $externalId=$resultValue;
			   $response=$externalId;
		    }
		}
	    
	     
        return $response;
	 
	}
	
	
	 /*
	  Adapts provider status with n2connect status
	  and update the current status
        */  
	public function updateSmsStatus($messageBroadcast,$status)  
	{
	   switch($status){
	                    /**** Generic provider status ***/
			   
			    case "10":
				//TODO - define SUBMITDs Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_SUBMITED,"10 - OK Message Sent to gateway");
				break;
			    case "11":
				//TODO - define QUEUED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"10 - Message Queued");
				break;
			    case "21":
				//TODO - define SUBMITDs Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_SUBMITED,"21 - OK Message Sent to network");
				break;
			    case "22":
				//TODO - define DELIVERED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_DELIVERED,"22 - Message Delivered");
				break;
			    case "30":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"30 - No credit");
				break;
			    case "41":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"41 - Malformed Message");
				break;
			    case "42":
				//TODO - define UNKNOW Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"42 - Internal Error");
				break;
			    case "44":
				//TODO - define EXPIRED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_EXPIRED,"44 - Message Expired");
				break;
			    case "50":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"50 - General delivery problem ");
				break;
			   
			    
	   }
	}
	
}
?>