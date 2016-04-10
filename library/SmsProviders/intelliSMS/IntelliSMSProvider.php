<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';

class IntelliSMSProvider implements ISmsProvider 
{
    private $provider_id=3;
    private $url = "https://www.intellisoftware.co.uk/smsgateway/sendmsg.aspx";
  
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
		    
		$to=substr($to,2,strlen($to));
		
		$params="username=n2bolsa&password=perlinoTio1&text=".urlencode($text)."&to=".urlencode($to)."&from=".urlencode($from);//."&sim=yes";
		
		
		//try to send message	
		$request = new HttpRequest();
		
		$xmldata = $request->httpPostExecute($this->url,$params);
		
		if($xmldata == "ERROR"){
				$response=$xmldata;   
		}
		else{
		   $result = explode(":", $xmldata);
		   $resultCode = $result[0];
		   $resultValue = $result[1];
		  
		    if($resultCode=="ERR")
		    {
		        $response="ERROR".date('ymdHis');
				
		    }
		    else if($resultCode=="ID") 
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
			    case "4":
				//TODO - define DELIVERED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_DELIVERED,"4 - Message Delivered");
				break;
			    case "1":
			    case "3":
				//TODO - define SUBMITDs Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_SUBMITED,"3 - OK Message Sent");
				break;
			   
			    case "2":
				//TODO - define QUEUED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"2 - Message Queued");
				break;
			    case "8":
				//TODO - define EXPIRED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_EXPIRED,"8 - Message Expired");
				break;
			    case "0":
				//TODO - define UNKNOW Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"0 - Unknown");
				break;
			    case "6":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"6 - Unable To Deliver");
				break;
			    case "5":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"5 - Error With Request");
				break;
			    case "7":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"7 - Routing Error");
				break;
	   }
	}
	
}
?>