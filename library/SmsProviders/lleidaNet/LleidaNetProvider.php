<?php
require_once 'SmsProviders/ISmsProvider.php';
require_once 'Http/HttpRequest.php';
require_once 'Helper/UserHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'Objects/Constants.php';

class LleidaNetProvider implements ISmsProvider 
{
    private $provider_id=4;//LleidaNet
    private $url = "https://sms.lleida.net/xmlapi/smsgw.cgi";
  
        public function getProviderId(){
	     return $this->provider_id;
	} 
       
        /*<result>
	    <action/>
	    <status/>
	    <msg/>
          </result>
        */
	public function sendSms($from,$to,$text)  
	{
	    $userBalance=0;
	    $messageId=0;
	    $broadcastID=0;
	    $errorCode="0-OK";
	    $externalId = date('ymdHis');
	        
		//to delete 00
		$from=substr($from,2,strlen($from));
		$from = "+".$from;
		$to_filter=explode(",",$to);
		$to=$to_filter[0];
		$to=substr($to,2,strlen($to));
		$to = "+".$to;
		
		$xmlRequest = "<sms><user>TEST317528</user><password>gg2LPX</password><src>$from</src><dst><num>$to
				    </num></dst><txt>$text</txt><data_coding>text</data_coding><mt_id>$externalId</mt_id></sms>";
				    
		$params="xml=".urlencode($xmlRequest);
		
		//try to send message	
		$request = new HttpRequest();
		$xmldata = $request->httpPostExecute($this->url,$params);

	
		    //IF HTTP STATUS 200
		    $xmlParse = new XmlParse($xmldata);

			// NO ERROR THEN

		       $status = $xmlParse->getValueByTagName("status");
		       
		       //status==100 ok
		       if($status==100){
			$response=$externalId.":".$to;
			
		       }
		       //else Error
		         

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