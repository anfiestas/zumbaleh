<?php
require_once 'Helper/UserHelper.php';
require_once 'Helper/UserLinkHelper.php';
require_once 'Helper/UserEventHelper.php';
require_once 'Objects/Constants.php';

class Imservices_UserlinksController extends Zend_Controller_Action
{
	
   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->userTimeStampPost = $this->_getParam('user_timestamp');
		$this->connectionType    = $this->_getParam('connection_type');
    }
    
    
    public function sendAction()
    {
     try{
	 
        $userIdPost 		= $this->_getParam('user_id');
		$toUserIdPost 		= $this->_getParam('to_user_id');
        $toPhonePost 		= $this->_getParam('to_phone');
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	       //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){

	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	            $result=UserLinkHelper::sendInvitationLink($user,$toUser);
           
                 if($result==1)
                  	$response="OK";
                     
                 else {
 
                	switch ($result) {
                		case 2:
                			$response="2-Request sent before";
                			$this->getResponse()->appendBody($response);
                			throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                			break;
                		
                		default:
                			# code...
                			break;
                	}
                }

	      }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function statusAction()
    {
     try{
	 
        $userIdPost 		= $this->_getParam('user_id');
		$toUserIdPost 		= $this->_getParam('to_user_id');
        $toPhonePost 		= $this->_getParam('to_phone');
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	       //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::getLinkStatus($user->getId(),$toUser->getId());
           
                if(!is_null($result))	
                  $response=$result;
                else
                	$response="ERROR";

	      }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }
    
    public function receivedAction()
    {
     try{
	  //$writer = new Zend_Log_Writer_Stream('../private/logs/userlinks.log');
	  //$logger = new Zend_Log($writer);

        $userIdPost = $this->_getParam('user_id');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	    
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $users=UserLinkHelper::getSendToMe($user->getId());
           		
                if(!is_null($users))	
                  	$response=$users;

                else
                	$response="OK,0";

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 	
	 	  //$logger->info("------------------------USERLINKS RECEIVED PIN:".$userIdPost."-------------------------------------");
		  //$logger->info($response);
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function getallacceptedAction()
    {
     try{
	 
        $userIdPost = $this->_getParam('user_id');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	      
	            //UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $users=UserLinkHelper::getAllAccepted($user->getId());
           
                if(!is_null($users))	
                  	$response=$users;

                else
                	$response="OK,0";

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function sentAction()
    {
     try{
	 
        $userIdPost = $this->_getParam('user_id');
        //$status  = $this->_getParam('status');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	      
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $messages=UserLinkHelper::getSendByMe($user->getId(),Constants::WAITING,"im");
           
                if(!is_null($messages))	
                  	$response=$messages;

                else
                	$response="OK,0";

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function acceptAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost  = $this->_getParam('to_phone');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){

	  	if($user->getIsGroup()){
	  		$response="3-Cannot use this service for groups";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
	  	 }

	         //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	      	    
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::acceptLink($user->getId(),$toUser->getId());
           
                if($result==1){
                  	$response="OK,".Constants::ACCEPTED;
                  	 //add Event to destinationUser
                  	 if(!$toUser->getIsGroup())
	       			 	UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_STATUS_CHANGED);
	       	
	       			 //if group then notify the other users of user accepts
	       			 if($toUser->getIsGroup()){
				          $userName=$user->getName();
				          if(is_null($userName) || $userName=="")
				               $userName=$user->getPin();

	       			 	  $params=$toUser->getPin()."9&c3".$user->getPin()."9&c3".$userName;
					 	  UserEventHelper::sendEventToAllUserLinksWithParams($toUser->getId(),$toUser->getPin(),$params,Constants::EVENT_GROUPS_USER_IN);
	       			 	}
                } else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	switch ($result) {
                		case 2:
                			$response="2-Request sent before";
                			$this->getResponse()->appendBody($response);
                			throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                			break;
                		
                		default:
                			# code...
                			break;
                	}
                	//$response="ERROR";
                }
            }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function blockAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost  = $this->_getParam('to_phone');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	         //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::blockLink($user->getId(),$toUser->getId());
           
                if($result==1){	
                  	$response="OK,".Constants::BLOCKED;
                  	 //add Event to destinationUser
	       			 //UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_STATUS_CHANGED);
                  }

                else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	$response="ERROR";
                }
            }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function unblockAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost  = $this->_getParam('to_phone');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	         //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::unblockLink($user->getId(),$toUser->getId());
           
                if($result==1){	
                  	$response="OK,".Constants::ACCEPTED;
                  	 //add Event to destinationUser
	       			 //UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_STATUS_CHANGED);
                  }

                else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	$response="ERROR";
                }
            }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }
   

	

}

