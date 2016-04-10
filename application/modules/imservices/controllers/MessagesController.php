<?php


class Imservices_MessagesController extends Zend_Controller_Action
{
	
   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->userTimeStampPost = $this->_getParam('user_timestamp');
		$this->connectionType    = $this->_getParam('connection_type');
		
    }
    
    
    //Send a message from X to Y
    public function sendAction()
    {require_once 'Objects/Constants.php';
     require_once 'Helper/UserEventHelper.php';
     require_once 'Helper/UserHelper.php';
     require_once 'Helper/IMessageHelper.php';
     require_once 'Helper/UserLinkHelper.php';
      try{

		$userIdPost = $this->_getParam('user_id');
		$toUserIdPost = $this->_getParam('to_user_id');
	    $toPhonePost = $this->_getParam('to_phone');
		$textPost    = $this->_getParam('text');
		$userMACAddressPost = $this->_getParam('mac_address');
		$aliasPost = $this->_getParam('alias');
        $userMid="null";
        $tokensWon=0;
        $userDevice=-1;

	//Validate params
		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
        if ($textPost==null)//TODO-check if 1 or 2
	        throw new Exception("Error text is null",Constants::ERROR_BAD_REQUEST);
	    if ($this->connectionType==null)
	        throw new Exception("Error connection type is null",Constants::ERROR_RESOURCE_NOT_FOUND);
	    //TODO add mac_address in mobile
	    /*if($userMACAddressPost==null)
	    	 throw new Exception("Error mac_address is null",Constants::ERROR_RESOURCE_NOT_FOUND);*/
      //text decode  
     if(get_magic_quotes_gpc()){
	 	$textPost=stripslashes($textPost);
	    }
	    //$textPost = str_replace("ç","c",$textPost);	
           //$textPost = utf8_decode($textPost);

	//Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){
	    //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	    $tokensWon=$user->getTokens();
	     $usersToSend=explode(',', $toUserIdPost);
	    //verifying $toUserId and $toPhone.
	    //$responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	    //$toUser = $responseArray[1];
        foreach ($usersToSend as &$userPin) {
        	 //$toUser = UserHelper::getUserByPin(trim($userPin));
        	$responseArray=UserHelper::getUserAndcheckErrors($userPin,$toPhonePost);
	   		 $toUser = $responseArray[1];
					    if($toUser->getIsGroup()){
					    	if(UserLinkHelper::getLinkStatus($user->getId(),$toUser->getId())==Constants::ACCEPTED){
				             
					                 $mid = IMessageHelper::sendIMessage($user,$textPost,$toUser->getId(),$toUser->getFullPhone(),$toUser->getPin(),$this->connectionType,$user->getPin(),$aliasPost,Constants::SMS_SUBMITED);
					             	 $response="OK,".$mid;
							}
						else{
							$response="3-you need to accept group invitation before start sending messages";
				            $this->getResponse()->appendBody($response);
				            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);

						}

				  	  }else{
				    	$mid = IMessageHelper::sendIMessage($user,$textPost,$toUser->getId(),$toUser->getFullPhone(),$toUser->getPin(),$this->connectionType,null,null,Constants::SMS_SUBMITED);
				     
				      if($mid > 0){
				      	$response="OK,".$mid;
				        //add Event to destinationUser
				        UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_MESSAGES_PENDINGS);
				        }
				      else{
				      	$response="3-link is broken";
			            $this->getResponse()->appendBody($response);
			            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);
				      }
			      }

		    }
		}
	    else{
	      throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	    }
	      
	    //get UserDevice and check if type is 1(mac_address from phone)
		if($userMACAddressPost!=null){
			$userDevice=UserHelper::getUserDeviceFromMacAddresses($user->getId(),$userMACAddressPost);
		}else{$userMACAddressPost="";}
		   
	    //update tokens if mid!=-1(link is not broken) message send OK and in TOKENS PROGRAM && if messages comes from a mobile phone
	    if(($mid!=-1) && ($user->getTokensProgram() != Constants::TOKENS_PROG_DISABLED) && (substr($userMACAddressPost, 0, 3) != "web") && ($user->getId()!=$toUser->getId())){
	    
	    	$tokensWon=UserHelper::calculateTokens($user);
	    	UserHelper::updateUserTokensTimeStamp($user, $this->userTimeStampPost,$this->connectionType,$tokensWon,($tokensWon-$user->getTokens()),null);
		}

	    $response.=",".$tokensWon;
	    //FIX to maintain keep alive connection on android
	     Header( "Content-Type: text/plain; charset=utf-8" ); 
         $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }
    
     public function pendingAction()
     {
      
      try{
      	require_once 'Helper/UserHelper.php';
      	require_once 'Helper/IMessageHelper.php';

	 	 $userIdPost = $this->_getParam('user_id');
         $userPhonePost = $this->_getParam('user_phone');//No lo uso
	  
		
          //verifying user
	    $responseArray=UserHelper::getUserAndcheckErrors($userIdPost,$userPhonePost);
	    $user=$responseArray[1];
	  //Verify user
            
	   if($user!=null){
	      //Update timestamp
	      UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	      
	      //get Pending IMessages
	      $messages=IMessageHelper::getPendingMessages($user,"IM");
	 
	       if($messages!=null){
		  
		  //$response=Zend_Json::encode($messages);
		   $response=$messages;
	       }
	       else{
		   $response="OK,0";
	       }
	   }	  
	  
         $this->getResponse()->appendBody($response);
	 
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
     }

 public function pendingsAction()
     {

	  

      require_once 'Helper/UserHelper2.php';
      require_once 'Helper/IMessageHelper2.php';
      require_once 'Helper/DbHelper2.php';

      //$writer = new Zend_Log_Writer_Stream('../private/logs/pendings.log');
	  //$logger = new Zend_Log($writer);
      try{
      	//Open Connection
		$dbHelper = new DbHelper2();
        $dbHelper->getConnectionDb();

	 	 $userIdPost = $this->_getParam('user_id');
	  	 $userPhonePost = $this->_getParam('user_phone');//No lo uso
	  	 $macAddress = $this->_getParam('mac_address');
	  	 $soundControl = $this->_getParam('with_is_active');//Se añade el nuevo param active a la respuesta.

	  	$userHelper = new UserHelper2($dbHelper->getDb());
	    $iMessageHelper = new IMessageHelper2($dbHelper->getDb());
		

          //verifying user

	   if($userIdPost!=null && $userIdPost!=-1)
	   	 $user = $userHelper->getUserByPin($userIdPost);

	  //Verify user
            
	   if($user!=null){
	      //Update timestamp
	      //$userHelper->updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	      
	      //get Pending IMessages
	      if($macAddress!=null){
	      	            $macid=$userHelper->getMacIdFromMacAddresses($user->getId(),$macAddress);
	      		    

	      		      if($soundControl=="true"){
	      		     	 	$isActive=$userHelper->isSessionActive($user->getId(),$macAddress);
	      		     	 	$messages=$iMessageHelper->getPendingMessagesForDevice($user,$macid,$isActive,"IM",true);
	      		     	 	$messages="OK,".$messages;
	      		     	 }
	      		       else{
	      		       	 $messages=$iMessageHelper->getPendingMessagesForDevice($user,$macid,$isActive,"IM",false);
	      		       	 $messages="OK,".$messages;

	      		       }

	      	}
	      else{
	      	 $messages=$iMessageHelper->getPendingMessagesNew($user,"IM");
	      }
	 
	       if($messages!=null){
		  
		  //$response=Zend_Json::encode($messages);
		   $response=$messages;
	       }
	       else{
		    $response="OK,0";
	       }
	   }	  
	      //FIX to maintain keep alive connection on android
	     Header( "Content-Type: text/plain; charset=utf-8" ); 
         $this->getResponse()->appendBody($response);
  

	 	  if (connection_aborted())
			   $dbHelper->getDb()->rollBack();
   		  else
		 	   $dbHelper->getDb()->commit();
		  	 
	  	 
		 $dbHelper->getDb()->closeConnection();

      }catch (Exception $e) {
      
		$dbHelper->getDb()->rollBack();
   		$dbHelper->getDb()->closeConnection();
		
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
     }
     
    public function statusAction()
     {require_once 'Objects/Constants.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/IMessageHelper.php';

      try{
	 
        $userIdPost = $this->_getParam('user_id');
		$toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost = $this->_getParam('to_phone');
		$messageIdPost = $this->_getParam('message_id');
        
	//Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	      
	      //verifying $toUserId and $toPhone.
	     $responseArray=self::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	     $toUser=$responseArray[1];
	     // verify that message comes from userId
	     $messageBroadcast=IMessageHelper::getIMessageBroadcastById($messageIdPost);
	     
	     if($messageBroadcast!=null){
	       if($messageBroadcast->getDestinationUserId()==$toUser->getId())
		   $response=$messageBroadcast->getStatus();
	       else
	           throw new Exception("Error message id:".$messageIdPost." was not sent to this toUserId:".$toUserIdPost,Constants::ERROR_RESOURCE_NOT_FOUND);
	     }
	     else
	      {
		throw new Exception("Error message does not exist, message_id:".$messageIdPost,Constants::ERROR_RESOURCE_NOT_FOUND);
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

      public function statusupdateAction()
     {
      require_once 'Objects/Constants.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/IMessageHelper.php';
      require_once 'Helper/UserEventHelper.php';

      try{
	 
        $userIdPost = $this->_getParam('user_id');
        $fromUserIdPost = $this->_getParam('from_user_id');
		$messageIdPost = $this->_getParam('message_id');
		$messageStatusPost = $this->_getParam('status');
        $response="OK";
	
	  //Verify user
         $fromUser = UserHelper::getUserByPin($fromUserIdPost);
	 
	  if($userIdPost!=null){
	    
	     $result=IMessageHelper::updateMessageStatus($messageIdPost,$messageStatusPost);
	      
	      if (!$result)
	      	  throw new Exception("Error messages status does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      else
	      	  UserEventHelper:: addEventToUserWithParams($fromUser->getId(),$fromUser->getPin(),$messageIdPost.",".$userIdPost.",".$messageStatusPost,Constants::EVENT_PUSH_MESSAGES_STATUS_CHANGED);

	     }else{
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

     public function statuslistAction()
     {require_once 'Objects/Constants.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/IMessageHelper.php';
      try{
	 
        $userIdPost = $this->_getParam('user_id');
		$messagesCount = $this->_getParam('messages_count');
        $messagesIdList = $this->_getParam('messages_id_list');
        
      $messagesList = explode(",",$messagesIdList);

      if(count($messagesList)!=$messagesCount){
      		$this->getResponse()->appendBody("OK,-1,Number of messages received does not match");
      		throw new Exception("Number of messages received does not match",Constants::ERROR_BAD_REQUEST);
      		}

	//Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	  	  
	      
	      if($messagesList!=null){
	      	   $response=IMessageHelper::getMessagesStatus($messagesList);
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
    
    public function sendtoallAction()
     {
      require_once 'Objects/Constants.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/IMessageHelper.php';
      try{
	 
        $userPost = $this->_getParam('user');
        $passPost = $this->_getParam('pass');
		$textPost = $this->_getParam('text');

		if(($userPost!="admin") && ($passPost!="perlinoTio1!")){
			$this->getResponse()->appendBody("Non Authorized");
      		throw new Exception("Non Authorized",Constants::ERROR_AUTHENTICATION_FAILED);
		}
        else{
        	
          if(get_magic_quotes_gpc()){
	 			$textPost=stripslashes($textPost);
	    	}

        //Send message to All users
        	$result=IMessageHelper::sendToAllUsers($textPost);
        }
	      
	 $this->getResponse()->appendBody($result);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
     }


    public function getlastAction()
     {
      require_once 'Objects/Constants.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/IMessageHelper.php';
      try{
	 
       $userIdPost = $this->_getParam('user_id');
       $toUserIdPost = $this->_getParam('to_user_id');
       $offsetPost = $this->_getParam('offset');

       $offset=$offsetPost;
          //Verify user
        if($userIdPost==null){
                $this->getResponse()->appendBody("ERROR,11,user_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }

        if($toUserIdPost==null){
                $this->getResponse()->appendBody("ERROR,12,group_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }

         if($offsetPost==null){
                $offset=0;
           }

       //Verify user
        $user = UserHelper::getUserByPin($userIdPost);
        $toUser = UserHelper::getUserByPin($toUserIdPost);

       $messages=IMessageHelper::getLastMessagesFromGroup($user,$toUser->getId(),$offset);
        //Get last Message send to "to_user_id"
        	
        	      
	 $this->getResponse()->appendBody($messages);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
     }
    
    
    
    /*Help methods*/
     private function getUserAndcheckErrors($userIdPost,$userPhonePost)
     {
      require_once 'Helper/UserHelper.php';
      require_once 'Objects/Constants.php';

      $responseArray=array();
	    
         if(($userIdPost==NULL || $userIdPost=="-1") && ($userPhonePost==NULL || $userPhonePost=="-1"))
	        throw new Exception("Error you need to pass id or phone params",Constants::ERROR_BAD_REQUEST);
		
	 //replace - slashes and spaces
	 $notAllowedChars = array("-");
	 $userPhonePost = str_replace($notAllowedChars, "", $userPhonePost);
	 //Verify user
	    if($userIdPost!=null && $userIdPost!=-1)
	       $user1 = UserHelper::getUserByPin($userIdPost);
	     //remove + symbol if exist  
	    if($userPhonePost!=null && $userPhonePost!=-1)
	       $userPhonePost = PhoneNumberHelper::replacePlusSymbolByZeros($userPhonePost);
	    

	    if($user1!=null){
		   
		   //If userId and Phone
		   if($userPhonePost!=null && $userPhonePost!=-1){
		     
		     //validate phone with user phone stored in DB (last 8 digits) 
		     if(substr($user1->getFullPhone(),-8)==substr($userPhonePost,-8)){
			  //UserId and Phone params and same phone
			  $responseArray[0]=Constants::USER_AND_PHONE_PARAMS;
			  $responseArray[1]=$user1;
		     }
		     else{
		         //if userId and Phone not the same then search user by Phone
			 $user2 = UserHelper::getUserByFullPhoneOrShortPhone($userPhonePost);
			   if($user2!=null){

			      //UserId and Phone params but different phone
			       $responseArray[0]=Constants::USER_AND_PHONE_PARAMS;
			       $responseArray[1]=$user2;
		           }
			   else{
			     throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
			   }
		     }
		   }
		   //Only userId param
		    $responseArray[0]=Constants::ONLY_USER_PARAMS;
		    $responseArray[1]=$user1;
	     }
	     else
	      {//Only phone param
	       $user2 = UserHelper::getUserByFullPhoneOrShortPhone($userPhonePost);
	       
	        if($user2!=null){
		  //Only phone param
		  $responseArray[0]=Constants::ONLY_PHONE_PARAMS;
		  $responseArray[1]=$user2;
		}
		else
		  throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
		
	      }
	      return $responseArray;
	
    }
    
	

}


