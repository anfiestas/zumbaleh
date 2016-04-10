<?php
require_once 'ServicesRest/ServicesRest.php';
require_once 'Zend/Rest/Server.php';

class DownloadController extends Zend_Controller_Action
{
protected $server;
	
	
   public function init()
    {
       

    }
    public function indexAction()
    {
	    $writer = new Zend_Log_Writer_Stream('../private/logs/downloads.log');
        $logger = new Zend_Log($writer);

      try{

        $userIdPost = $this->_getParam('pin');
      
        $logger->info("--------- ShareApp invitacionSpoora -----" . $userIdPost);
        $this->_redirect('https://play.google.com/store/apps/details?id=com.spoora&referrer=utm_source%3Dmyspoora%26utm_medium%3DApp%26utm_campaign%3DinvitacionSpoora%26pin%3D'.$userIdPost);
        $response="OK";
    
        $this->getResponse()->appendBody($response);

        
            
        }catch (Exception $e) {

        $this->getRequest()->setParam('error_code', $e->getCode());
        $this->getRequest()->setParam('error_message', $e->getMessage());
        $this->getRequest()->setParam('error_trace', $e->getTraceAsString());
        $this->_forward('n2sms', 'error','default');
                 
        }
    }
 
    
	

}

