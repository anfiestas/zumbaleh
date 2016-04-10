<?php 
require_once 'Helper/UserHelper.php';
require_once 'Objects/Constants.php';

/**
 * Class MessagesController
 */
 
class UsersController extends Zend_Rest_Controller {
    
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
        try{
            $errorCode="0-OK";
            $userId=$this->_getParam('id');
        
            //Verify user
	    $user = UserHelper::getUserByFullPhoneOrShortPhone($userId);
             
             if ($user!=null)
	     {
		//User exist
		//treatment of $to and see if different numbers
		$userBalance=$user->getBalance();
		
	     }
             else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
              
	    $response=$errorCode.",".$userBalance;
            
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
        try{
            $errorCode="0-OK";
            $userId=$this->_getParam('id');
        
            //Verify user
	    $user = UserHelper::getUserByFullPhoneOrShortPhone($userId);
             
             if ($user!=null)
	     {
		//User exist
		//treatment of $to and see if different numbers
		$userBalance=$user->getBalance();
		
	     }
             else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
              
	    $response=$errorCode.",".$userBalance;
            
            $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
        }

    }
    
    
    
    
    public function putAction()
    {
        self::postAction();

    }
    
    public function deleteAction()
    {
        //$this->getResponse()
        //    ->appendBody("From deleteAction() deleting the user");

    }

}