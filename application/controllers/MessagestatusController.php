<?php 
require_once 'Objects/Constants.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Helper/MessageHelper.php';
require_once 'SmsProviders/intelliSMS/IntelliSMSProvider.php';
require_once 'SmsProviders/RoutoTelecom/RoutoProvider.php';
require_once 'SmsProviders/sms42IT/Sms42TelecomProvider.php';
require_once 'SmsProviders/usaBulkSMS/UsaBulkSMSProvider.php';
/**
 * Class MessagesController
 */
 
class MessagestatusController extends Zend_Rest_Controller {
    
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
    try{
      $externalId=null;
      $to=null;
      $status=null;
      $provider=null;
      $providerName=null;
      
       $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
       $logger = new Zend_Log($writer);
        //request sent from n2manager system to spoora
         if($this->_getParam('externalid')!=null){
            $externalId=$this->_getParam('externalid');
         }
	  //intelliSMS status report
	  elseif($this->_getParam('msgid')!=null){
	    $provider = new IntelliSMSProvider();
	    $providerName="IntelliSMS";
	    $externalId=$this->_getParam('msgid');
	    $to="00".$this->_getParam('toaddr');
	   }
	   //Routo report
	   elseif($this->_getParam('mess_id')!=null){
	    $provider = new RoutoProvider();
	    $providerName="RoutoSMS";
	    $externalId=$this->_getParam('mess_id');
	    $to="00".$this->_getParam('number');
	   }
	   //42Telecom
	   elseif($this->_getParam('smsid')!=null){
	    $provider = new Sms42TelecomProvider();
	    $providerName="42Telecom";
	    $externalId=$this->_getParam('smsid');
	    $to=null;
	    
	   }
	   //USA BulkSMS
	   elseif($this->_getParam('referring_batch_id')!=null){
	    $provider = new UsaBulkSMSProvider();
	    $providerName="UsaBulkSMS";
	    $externalId=$this->_getParam('referring_batch_id');
	    
	    if($this->_getParam('msisdn')!=null)
		$to="00".$this->_getParam('msisdn');
	    
	   }
	    $status=$this->_getParam('status');
	    
	    sleep(2);
		
	    if($to!=null){$messageBroadcast=MessageHelper::getMessageBroadcastByExternalId($externalId,$to);}
	     else{
		$messageBroadcast=MessageHelper::getMessageBroadcastByExternalIdOnly($externalId);
		$to=$messageBroadcast->getDestinationNumber();
		}
                //to update spoora SMS send messages status
          	$providerId=$messageBroadcast->getProviderId();
	    	switch($providerId){
			/*case 1: $provider= new Tm4bProvider();break;*/
			case 2: $provider= new RoutoProvider();$providerName="RoutoSMS";break;
			case 3: $provider= new IntelliSMSProvider();$providerName="IntelliSMS";break;
		}
	   
		if($messageBroadcast!=null){
		    $provider->updateSmsStatus($messageBroadcast,$status);  
		    $responseError="No error";
		}
		else{
		    $responseError="messageBroadcast NULL";
		}
	    
	    
	    $logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - MessagestatusController > getAction:\n\t".
				       "Provider: ".$providerName."\n\t".
				       "externalId: ".$externalId."\n\t".
				       "status: ".$status."\n\t".
				       "to: ".$to."\n\t".
				       "Error: ".$responseError."\n\t");
	    
	    $this->getResponse()->appendBody("OK");
	}catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }
    }
    
    
    public function getAction()
    {
	
    }
    
    //TODO create always first SMS in database an then update status
    public function postAction()
    {
      
    try{
     $externalId=null;
      $to=null;
      $status=null;
      $provider=null;
      $providerName=null;
      
       $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
       $logger = new Zend_Log($writer);
        //request sent from n2manager system to spoora
         if($this->_getParam('externalid')!=null){
            $externalId=$this->_getParam('externalid');
         }
	  //intelliSMS status report
	  elseif($this->_getParam('msgid')!=null){
	    $provider = new IntelliSMSProvider();
	    $providerName="IntelliSMS";
	    $externalId=$this->_getParam('msgid');
	    $to="00".$this->_getParam('toaddr');
	   }
	   //Routo report
	   elseif($this->_getParam('mess_id')!=null){
	    $provider = new RoutoProvider();
	    $providerName="RoutoSMS";
	    $externalId=$this->_getParam('mess_id');
	    $to="00".$this->_getParam('number');
	   }
	   //42Telecom
	   elseif($this->_getParam('smsid')!=null){
	    $provider = new Sms42TelecomProvider();
	    $providerName="42Telecom";
	    $externalId=$this->_getParam('smsid');
	    $to=null;
	    
	   }
	   //USA BulkSMS
	   elseif($this->_getParam('referring_batch_id')!=null){
	    $provider = new UsaBulkSMSProvider();
	    $providerName="UsaBulkSMS";
	    $externalId=$this->_getParam('referring_batch_id');
	    
	    if($this->_getParam('msisdn')!=null)
		$to="00".$this->_getParam('msisdn');
	    
	   }
	    $status=$this->_getParam('status');
	    
	    sleep(2);
		
	    if($to!=null){$messageBroadcast=MessageHelper::getMessageBroadcastByExternalId($externalId,$to);}
	     else{
		$messageBroadcast=MessageHelper::getMessageBroadcastByExternalIdOnly($externalId);
		$to=$messageBroadcast->getDestinationNumber();
		}
                //to update spoora SMS send messages status
          	$providerId=$messageBroadcast->getProviderId();
	    	switch($providerId){
			/*case 1: $provider= new Tm4bProvider();break;*/
			case 2: $provider= new RoutoProvider();$providerName="RoutoSMS";break;
			case 3: $provider= new IntelliSMSProvider();$providerName="IntelliSMS";break;
		}
	   
		if($messageBroadcast!=null){
		    $provider->updateSmsStatus($messageBroadcast,$status);  
		    $responseError="No error";
		}
		else{
		    $responseError="messageBroadcast NULL";
		}
	    
	    
	    $logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - MessagestatusController > getAction:\n\t".
				       "Provider: ".$providerName."\n\t".
				       "externalId: ".$externalId."\n\t".
				       "status: ".$status."\n\t".
				       "to: ".$to."\n\t".
				       "Error: ".$responseError."\n\t");
	    
	    $this->getResponse()->appendBody("OK");
	}catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }
	
    }
    
    public function putAction()
    {
     
      //  $this->getResponse()
      //    ->appendBody("From putAction() updating the requested message".$messageBroadcast->getExternalId());

    }
    
    public function deleteAction()
    {
        //$this->getResponse()
        //    ->appendBody("From deleteAction() deleting the requested message");

    }

}
