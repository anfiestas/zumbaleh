<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';

class UsaBulkSMSProvider implements ISmsProvider 
{
    private $provider_id=6;
    private $url = "http://usa.bulksms.com:5567/eapi/submission/send_sms/2/2.0";
  
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
		
		//to delete 00 $from=substr($from,2,strlen($from));
		$to=substr($to,2,strlen($to));
	
		$params="username=n2bolsa&password=perlinoTio69&message=".urlencode($text)."&msisdn=".urlencode($to)."&sender=n2manager";
		
		
		//try to send message	
		$request = new HttpRequest();
		
		$xmldata = $request->httpPostExecute($this->url,$params);
		
		if($xmldata == "ERROR"){
				$response=$xmldata;   
		}
		else{
		   $result = explode("|", $xmldata);
		   $resultCode = $result[0];
		   //$resultDescription = $result[1]; //status Description
		   $resultValue = $result[2];
		  
		    if($resultCode=="0")
		    {
		       // NO ERROR THEN
		       $externalId=$resultValue;
		       $response=$externalId;
				
		    }
		    else 
		    {
			 $response="ERROR";
			
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
			    case "11":
				//TODO - define DELIVERED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_DELIVERED,"11 - Message Delivered");
				break;

			    case "53":
				//TODO - define EXPIRED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_EXPIRED,"53 - Message Expired");
				break;
			    
			    case "64":
				//TODO - define EXPIRED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_SUBMITED,"64 - Message Queued");
				break;
			    
			    case "70":
				//TODO - define UNKNOW Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"70 - Unknown upstream status");
				break;
			    
			    case "22":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"22 - Internal Fatal Error");
				break;
			    
			    case "23":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"23 - Authentication failure");
				break;
			    
			    case "24":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"24 - Data validation failed");
				break;
			    
			    case "25":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"25 - You do not have sufficient credits");
				break;
			    
			    case "26":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"26 - Upstream credits not available");
				break;
			    
			    case "27":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"27 - You have exceeded your daily quota");
				break;
			    
			    case "28":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"28 - Upstream quota exceeded");
				break;
			    
			    case "29":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"29 - Message sending cancelled");
				break;
			    
			    case "32":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"32 - Blocked (probably because of a recipient's complaint against you)");
				break;
						    
			    case "33":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"33 - Failed: censored");
				break;
			    
			    case "50":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"50 - Delivery failed - generic failure");
				break;
			    
			    case "51":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"51 - Delivery to phone failed");
				break;
			    
			    case "52":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"52 - Delivery to network failed");
				break;
			    
			    case "54":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"54 - Failed on remote network");
				break;
			    
			    case "56":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"56 - Failed: remotely censored");
				break;
			    
			    case "57":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_DELIVERED,"57 - Delivered but failed due to fault on handset (SIM full)");
				break;
			    
			    case "31":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"31 - Unroutable");
				break;
	   }
	}
	
}
?>