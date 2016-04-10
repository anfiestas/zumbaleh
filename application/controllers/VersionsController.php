<?php 
require_once 'Xml/XmlParse.php';
require_once 'Router/ProviderRouter.php';
require_once 'Helper/VersionHelper.php';
require_once 'Objects/Version.php';

/**
 * Class MessagesController
 */
 
class VersionsController extends Zend_Rest_Controller {
    
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
          //  ->appendBody("From indexAction() returning all users");
    }
    
    /*getUserBalance
      return: cod_error,balance
     */
    public function getAction()
    { 
		$writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.versions.log');
	    $logger = new Zend_Log($writer);
		
        try{
    
            $versionNumber=$this->_getParam('id');
        
            //Verify user
	    $version = VersionHelper::getVersion($versionNumber);
        
		$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - VersionsController > getAction:\n\t".
				       "version: ".$versionNumber."\n\t".
				       "params: ".print_r($this->getRequest()->getParams(),true));
		
             if ($version!=null)
	     {
	        $response=$version->getValidity();
		
	     }
             else
	      {
		throw new Exception("Error invalid version number",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
              
	
            $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }
    }
    
    
    public function postAction()
    {
      // $this->getResponse()
      //     ->appendBody("From postAction()users");

    }
    
    
    
    
    public function putAction()
    {
        //$this->getResponse()
        //   ->appendBody("From putAction() updating the user");

    }
    
    public function deleteAction()
    {
        //$this->getResponse()
        //    ->appendBody("From deleteAction() deleting the user");

    }

}