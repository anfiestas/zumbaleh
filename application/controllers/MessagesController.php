<?php 
require_once 'Xml/XmlParse.php';
require_once 'Router/ProviderRouter.php';
require_once 'Objects/Constants.php';
require_once 'Objects/User.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Objects/Constants.php';
require_once 'Helper/MessageHelper.php';
require_once 'Helper/UserHelper.php';
require_once 'Helper/PhoneNumberHelper.php';

require_once 'SmsProviders/tm4b/Tm4bProvider.php';
require_once 'SmsProviders/intelliSMS/IntelliSMSProvider.php';
require_once 'SmsProviders/RoutoTelecom/RoutoProvider.php';
require_once 'SmsProviders/lleidaNet/LleidaNetProvider.php';
require_once 'SmsProviders/sms42IT/Sms42TelecomProvider.php';
require_once 'SmsProviders/usaBulkSMS/UsaBulkSMSProvider.php';
/**
 * Class MessagesController
 */
 
class MessagesController extends Zend_Rest_Controller {
    
    public function init(){
        
        $this->_helper->viewRenderer->setNoRender(true);
		//IMPORTANT: to disable the layout html content to be printed in REST responses
		$this->_helper->layout->disableLayout();
       
    }
    /**
     * indexAction
     */

  public function indexAction()
    {
         //$this->getResponse()
         //   ->appendBody("From indexAction() returning all messages");
    }
    
    
    public function getAction()
    {
	 try{
	    
        $smsId=$this->_getParam('id');
        
		if($smsId!="send"){
			$response=MessageHelper::getSmsStatus($smsId);
			$this->getResponse()->appendBody($response);
	     }
		 else{
				 $providerRouter=null;
			$provider=null;
				$user=null;
			$response="";
				$from=$this->_getParam('from');
			
				$to=urldecode($this->_getParam('to'));
			
			//replace - slashes and spaces
			$notAllowedChars = array("-");
			$to = str_replace($notAllowedChars, "", $to);
			
				$text=$this->_getParam('text');
			if(get_magic_quotes_gpc()){
			$text=stripslashes($text);
			}
			else
				nothing;
			
			$text = str_replace("ç","c",$text);	
				$text = utf8_decode($text);
			
			$user = UserHelper::getUser($from);
			
			 $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
			 $logger = new Zend_Log($writer);
			
			$this->checkParamErrors($from,$to,$user);
			
			$countryUser = CountryHelper::getCountry($user->getCountryId());
			$to=PhoneNumberHelper::toInternationalFormat($to,$countryUser);
			
			$destinationCountry = PhoneNumberHelper::getPhoneCountry($to,$countryUser);
			$messageId=MessageHelper::createMessageBroadcast($user,""/*$text*/,$to,null,null,
									 $destinationCountry->getId(),"TEMPID-".time(),null);
			$userBalance= $user->getBalance();
			$providerRouter = new ProviderRouter($destinationCountry);
			$routes=$providerRouter->getRoutes();
			
			//Send SMS to Provider
			 foreach($routes as $route){
				 switch($route->getProviderId()){
				case 1: $provider= new Tm4bProvider();break;
				case 2: $provider= new RoutoProvider();break;
				case 3: $provider= new IntelliSMSProvider();break;
				case 4: $provider= new LleidaNetProvider();break;
				case 5: $provider= new Sms42TelecomProvider();break;
				case 6: $provider= new UsaBulkSMSProvider();break;
				
				 }
				//response is externalId if OK
					   $response=$provider->sendSms($from,$to,$text);

				if($response!="ERROR"){
				  //Update user balance, message info,externalId
				   $response=MessageHelper::updateMessageAfterSent($user,$route,$messageId,$destinationCountry,$response);
				   break;
				  }
			}
			
			$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - MessagesController > postAction:\n\t".
						   "externalId: ".$response."\n\t".
						   "provider: ".$route->getProviderId()."\n\t".
						   "messageId: ".$messageId."\n\t".
						   "user: ".$user->getId()."\n\t".
						   "fromCountry: ".$countryUser->getName()."\n\t".
						   "to: ".$to."\n\t".
						   "toCountry: ".$destinationCountry->getName()."\n\t");
			 
			 if($response=="ERROR")
				throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
			 
			//parse serviceResponse
			$this->getResponse()->appendBody($response);
		  }
         }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }
    }
    
    //TODO create always first SMS in database an then update status
    public function postAction()
    {
        try{
	    $providerRouter=null;
	    $provider=null;
            $user=null;
	    $response="";
            $from=$this->_getParam('from');
	    
            $to=urldecode($this->_getParam('to'));
	    
	    //replace - slashes and spaces
	    $notAllowedChars = array("-");
	    $to = str_replace($notAllowedChars, "", $to);
	    
            $text=$this->_getParam('text');
	    if(get_magic_quotes_gpc()){
	 	$text=stripslashes($text);
	    }
	    else
         	nothing;
	    
	    $text = str_replace("ç","c",$text);	
            $text = utf8_decode($text);
	    
	    $user = UserHelper::getUser($from);
	    
	     $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
	     $logger = new Zend_Log($writer);
	    
		$this->checkParamErrors($from,$to,$user);
		
		$countryUser = CountryHelper::getCountry($user->getCountryId());
		$to=PhoneNumberHelper::toInternationalFormat($to,$countryUser);
		
		$destinationCountry = PhoneNumberHelper::getPhoneCountry($to,$countryUser);
		$messageId=MessageHelper::createMessageBroadcast($user,""/*$text*/,$to,null,null,
								 $destinationCountry->getId(),"TEMPID-".time(),null);
		$userBalance= $user->getBalance();
		$providerRouter = new ProviderRouter($destinationCountry);
		$routes=$providerRouter->getRoutes();
		
		//Send SMS to Provider
		 foreach($routes as $route){
		     switch($route->getProviderId()){
			case 1: $provider= new Tm4bProvider();break;
			case 2: $provider= new RoutoProvider();break;
			case 3: $provider= new IntelliSMSProvider();break;
			case 4: $provider= new LleidaNetProvider();break;
			case 5: $provider= new Sms42TelecomProvider();break;
			case 6: $provider= new UsaBulkSMSProvider();break;
			
		     }
		    //response is externalId if OK
                   $response=$provider->sendSms($from,$to,$text);

		    if($response!="ERROR"){
		      //Update user balance, message info,externalId
		       $response=MessageHelper::updateMessageAfterSent($user,$route,$messageId,$destinationCountry,$response);
		       break;
		      }
		}
		
		$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - MessagesController > postAction:\n\t".
				       "externalId: ".$response."\n\t".
				       "provider: ".$route->getProviderId()."\n\t".
				       "messageId: ".$messageId."\n\t".
				       "user: ".$user->getId()."\n\t".
				       "fromCountry: ".$countryUser->getName()."\n\t".
				       "to: ".$to."\n\t".
				       "toCountry: ".$destinationCountry->getName()."\n\t");
		 
		 if($response=="ERROR")
		    throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
		 
		//parse serviceResponse
		$this->getResponse()->appendBody($response);
            
         }catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }
   

    }
    
    
    //if get sms but android 4 sends PUT
    public function putAction()
    {

        $smsId=$this->_getParam('id');
        
		if($smsId!="send"){
			$response=MessageHelper::getSmsStatus($smsId);
			$this->getResponse()->appendBody($response);
	     }
		 else{
				 $providerRouter=null;
			$provider=null;
				$user=null;
			$response="";
				$from=$this->_getParam('from');
			
				$to=urldecode($this->_getParam('to'));
			
			//replace - slashes and spaces
			$notAllowedChars = array("-");
			$to = str_replace($notAllowedChars, "", $to);
			
				$text=$this->_getParam('text');
			if(get_magic_quotes_gpc()){
			$text=stripslashes($text);
			}
			else
				nothing;
			
			$text = str_replace("ç","c",$text);	
				$text = utf8_decode($text);
			
			$user = UserHelper::getUser($from);
			
			 $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
			 $logger = new Zend_Log($writer);
			
			$this->checkParamErrors($from,$to,$user);
			
			$countryUser = CountryHelper::getCountry($user->getCountryId());
			$to=PhoneNumberHelper::toInternationalFormat($to,$countryUser);
			
			$destinationCountry = PhoneNumberHelper::getPhoneCountry($to,$countryUser);
			$messageId=MessageHelper::createMessageBroadcast($user,""/*$text*/,$to,null,null,
									 $destinationCountry->getId(),"TEMPID-".time(),null);
			$userBalance= $user->getBalance();
			$providerRouter = new ProviderRouter($destinationCountry);
			$routes=$providerRouter->getRoutes();
			
			//Send SMS to Provider
			 foreach($routes as $route){
				 switch($route->getProviderId()){
				case 1: $provider= new Tm4bProvider();break;
				case 2: $provider= new RoutoProvider();break;
				case 3: $provider= new IntelliSMSProvider();break;
				case 4: $provider= new LleidaNetProvider();break;
				case 5: $provider= new Sms42TelecomProvider();break;
				case 6: $provider= new UsaBulkSMSProvider();break;
				
				 }
				//response is externalId if OK
					   $response=$provider->sendSms($from,$to,$text);

				if($response!="ERROR"){
				  //Update user balance, message info,externalId
				   $response=MessageHelper::updateMessageAfterSent($user,$route,$messageId,$destinationCountry,$response);
				   break;
				  }
			}
			
			$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - MessagesController > postAction:\n\t".
						   "externalId: ".$response."\n\t".
						   "provider: ".$route->getProviderId()."\n\t".
						   "messageId: ".$messageId."\n\t".
						   "user: ".$user->getId()."\n\t".
						   "fromCountry: ".$countryUser->getName()."\n\t".
						   "to: ".$to."\n\t".
						   "toCountry: ".$destinationCountry->getName()."\n\t");
			 
			 if($response=="ERROR")
				throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
			 
			//parse serviceResponse
			$this->getResponse()->appendBody($response);
		  }

    }
    
    public function deleteAction()
    {
        //$this->getResponse()
        //    ->appendBody("From deleteAction() deleting the requested message");

    }
    
    private function checkParamErrors($from,$to,$user){
	
	    if($to==null)
		throw new Exception("Error destination number is null",Constants::ERROR_BAD_REQUEST);
	    if($from==null)
		throw new Exception("Error origin number is null",Constants::ERROR_BAD_REQUEST);
	    //Verify user
	    if ($user==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	     
		//Todo: Verificar que no le llegue para enviar 1 SMS es decir el precio de 1 credito
	    if ($user->getBalance() < 1)
		throw new Exception("Not enough credits for user:". $user->getFullPhone(),Constants::ERROR_NOT_ENOUGH_CREDITS);
		
    }
    
 

}