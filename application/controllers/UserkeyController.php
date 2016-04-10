<?php 
require_once 'Xml/XmlParse.php';
require_once 'Router/ProviderRouter.php';
require_once 'Helper/UserHelper.php';
require_once 'Helper/TransactionHelper.php';

/**
 * Class MessagesController
 */
 
class UserKeyController extends Zend_Rest_Controller {
    
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
		 if(MessageHelper::isSmsSecretKeySentToday($user->getFullPhone())){
		     throw new Exception("One secret key has been sent yet today ",Constants::ERROR_BAD_REQUEST);
		 }
		 else{
		     //send SecretKey by sms
		    TransactionHelper::sendSmsSecretKey($user);
		    
		    //Discounting credits
		    $newBalance=$user->getBalance()-1;
		    $user->setBalance($newBalance);
		    
		    UserHelper::updateUser($user);
		    
		    
		}
		
	     }
             else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
              
	    $response=$errorCode.",".$newBalance;
            
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