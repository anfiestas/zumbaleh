<?php
require_once 'Objects/Constants.php';

class ErrorController extends Zend_Controller_Action
{
 
        public function init(){
        
       // $this->_helper->viewRenderer->setNoRender(true);
		//IMPORTANT: to disable the layout html content to be printed in REST responses
		$this->_helper->layout->disableLayout();
       
    }
	
    public function errorAction()
    {
	 $writer=new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
	 $logger=new Zend_Log($writer);

        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_RESOURCE_NOT_FOUND);
                $this->view->message = 'Page not found...';
                break;
             case Constants::ERROR_AUTHENTICATION_FAILED:
        
                // 401 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_AUTHENTICATION_FAILED);
                $this->view->message = 'Authentication failed';
                break;
            //case N2SMS_USER_VALIDATION_ERROR
            default:
                // 500 application error 
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_INTERNAL_SERVER);
                $this->view->message = 'Internal Server error..';
                break;
        }
	//$logger->err($this->getRequest()->getServer('REMOTE_ADDR')."- EXCEPTION - ".$error_code." ".$errors->exception);
		
	if($errors!=null){
			$this->view->exception = $errors->exception;
			$this->view->request   = $errors->request;
		}
        
    }
    
    public function n2smsAction()
    {
	$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
	$logger = new Zend_Log($writer);
	
	$errors = $this->_getParam('error_handler');
	
        $error_code = $this->_getParam('error_code');
	$error_message = $this->_getParam('error_message');
        $error_trace = $this->_getParam('error_trace');
	
        switch ($error_code) {
            
            case Constants::ERROR_AUTHENTICATION_FAILED:
        
                // 401 error -- Authentication error
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_AUTHENTICATION_FAILED);
                $this->view->message = 'Authentication error';
                break;
            case Constants::ERROR_USER_FORBIDDEN:
        
                // 401 error -- Authentication error
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_USER_FORBIDDEN);
                $this->view->message = 'Forbidden user error';
                break;

            case Constants::ERROR_USER_PIN_LOCKED:
        
                // 401 error -- ERROR_USER_PIN_LOCKED
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_USER_PIN_LOCKED);
                $this->view->message = 'Pin Locked user error';
                break;
            
            case Constants::ERROR_RESOURCE_NOT_FOUND:
        
                // 404 error -- Resource not found
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_RESOURCE_NOT_FOUND);
                $this->view->message = 'Resource not fond error';
                break;
	    
	    case Constants::ERROR_NOT_ENOUGH_CREDITS:
        
                // 506 error -- Provider error
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_NOT_ENOUGH_CREDITS);
                $this->view->message = 'Not enough credits error';
                break;
	    
            case Constants::ERROR_BAD_REQUEST:
        
                // 400 error -- Bad Request
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_BAD_REQUEST);
                $this->view->message = 'Bad request error';
                break;
	    
	    case Constants::ERROR_PROVIDER:
        
                // 506 error -- Provider error
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_PROVIDER);
                $this->view->message = 'Provider server error';
                break;
	    
            default:
                // 500 error -- internal server error
                $this->getResponse()->setHttpResponseCode(Constants::ERROR_INTERNAL_SERVER);
                $this->view->message = 'Internal Server error';
                break;
        }
	
	$logger->err($this->getRequest()->getServer('REMOTE_ADDR')." - EXCEPTION - ".$error_code.": ".$error_message."\n".$error_trace);
	
		if($errors!=null){
			$this->view->exception = $errors->exception;
			$this->view->request   = $errors->request;
		}
    }


}

