<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'RoutoTelecomSMS.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';
require_once 'Helper/CountryHelper.php';
require_once 'Helper/PhoneNumberHelper.php';

class RoutoProvider implements ISmsProvider 
{
    private $provider_id=2;//Routo
   
        public function getProviderId(){
	     return $this->provider_id;
	}
	
	public function sendSms($from,$to,$text)  
	{
	    $userBalance=0;
	    $messageId=0;
	    $broadcastID=0;
	    $errorCode="0-OK";
		
		if(substr($from,0,2)=="00")
		    $from=substr($from,2,strlen($from));
	    
		$toInRoutoFormat=substr($to,2,strlen($to));
		//try to send message
		// creating object
		$sms = new RoutoTelecomSMS();
		// setting SMS parameters
		$sms->SetUser("n2bolsa");
		$sms->SetPass("nr2jhzpm");
		$externalId = date('ymdHis');
		$sms->SetMessageId($externalId);
		$sms->SetNumber($toInRoutoFormat);
		$sms->SetOwnNum($from); // optional
		$sms->SetType("SMS"); // optional
		$sms->SetMessage($text);
		
		// send SMS and print result
		$xmldata = trim($sms->Send());
		$response=$externalId;
		    
		if($xmldata == "ERROR")
			 $response=$xmldata;   
		       
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
			    case "0":
				//TODO - define DELIVERED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_DELIVERED,"0-Message Delivered");
				break;
			    case "247":
			    case "248":
			    case "250":
				//TODO - define SUBMITDs Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_SUBMITED,"250-Message Submitted");
				break;
			    case "9":
			    case "251":
				//TODO - define FAILED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"251-Message Undeliverable");
				break;
			    case "19":
				//TODO - define QUEUED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"19-Message waiting, list full");
				break;
			    case "253":
				//TODO - define EXPIRED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_EXPIRED,"253-Message EXPIRED");
				break;
			    case "-1":
				//TODO - define UNKNOW Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"-1-Message UNKNOWN");
				break;
			    case "249":
				//TODO - define REJECTED Constant
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"249-Message Rejected");
				break;
			    
			    /**** Specific provider status ***/
			    case "1":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"1-Message Rejected: Message length is invalid");
				break;
			    case "-2":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"-2-System error");
				break;
			    case "2":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"2-Subscriber absent");
				break;
			    case "3":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"3-Device memory capacity exceeded");
				break;
			    case "4":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"4-Equipment protocol error");
				break;
			    case "5":
				break;
			    case "6":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"6-Equipment not SM equipped");
				break;
			    case "7":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"7-Unknown service centre");
				break;
			    case "8":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"8-Service centre congestion");
				break;
			    case "10":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"10-Rejected: Invalid source address");
				break;
			    case "11":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"11-Invalid destination address");
				break;
			    case "12":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"12-Illegal subscriber");
				break;
			    case "13":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"13-Teleservice not provisioned");
				break;
			    case "14":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"14-Illegal equipment");
				break;
			    case "15":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"15-Call barred");
				break;
			    case "16":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"16-Facility not supported");
				break;
			    case "17":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"17-Subscriber busy for SM");
				break;
			    case "18":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"18-System failure");
				break;
			    case "20":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"20-Data missing");
				break;
			    case "21":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"21-Unexpected data value");
				break;
			    case "22":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"22-Resource limitation");
				break;
			    case "23":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"23-Initiating release");
				break;
			    case "24":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"24-Unknown alphabet");
				break;
			    case "25":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_UNKNOWN,"25-USSD busy");
				break;
			    case "26":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"26-Duplicated invoke ID");
				break;
			    case "27":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"27-No supported service");
				break;
			    case "28":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"28-Mistyped parameter");
				break;
			    case "29":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"29-Unexpected response from peer");
				break;
			    case "30":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"30-Service completion failure");
				break;
			    case "31":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"31-No response from peer");
				break;
			    case "32":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"32-Invalid response received");
				break;
			    case "34":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"34-Invalid destination");
				break;
			    case "49":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"49-Message type not supported");
				break;
			    case "50":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"50-Destination blocked for sending");
				break;
			    case "51":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"51-Not enough money");
				break;
			    case "52":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"52-No price");
				break;
			    case "67":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"67-Invalid esm_class field data");
				break;
			    case "69":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"69-Rejected by SMSC");
				break;
			    case "72":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"72-Rejected: Invalid source address TON");
				break;
			    case "73":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"73-Rejected: Invalid source address NPI");
				break;
			    case "80":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"80-Rejected: Invalid destination address TON");
				break;
			    case "81":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"81-Rejected: Invalid destination address NPI");
				break;
			    case "88":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"88-Throttling error");
				break;
			    case "97":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"97-Rejected: Invalid scheduled delivery time");
				break;
			    case "100":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"100-Error sending message");
				break;
			    case "252":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"252-Deleted");
				break;
			    case "254":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"254-Roaming level not supported");
				break;
			    case "255":
				$messageBroadcast=MessageHelper::updateMessageStatusById($messageBroadcast,Constants::SMS_FAILED,"255-Unknown error");
				break;
			    
			}
	}
	
}
?>