<?php

require_once 'Helper/UserHelper.php';
require_once 'Helper/UserLinkHelper.php';
require_once 'Objects/Constants.php';
require_once 'Helper/UserEventHelper.php';

class Imservices_GroupsController extends Zend_Controller_Action
{
	
   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->userTimeStampPost = $this->_getParam('user_timestamp');
		$this->connectionType    = $this->_getParam('connection_type');
    }
    
    
   public function createAction()
    {
       try{
	       $userIdPost      = $this->_getParam('user_id');
	       $groupNamePost 	= $this->_getParam('name');
	       $toUsersPost 	= $this->_getParam('to_users');
	       $groupDescriptionPost 	= $this->_getParam('description');
           $usersToInviteArray= array();
           $pinsError = null;
           $groupUser=null;
           $user = null;
           $pinsErrorCount=0;
  			
  			/*********Params error treatment******/
  			 //Verify user
  			if($userIdPost==null){
		   		$this->getResponse()->appendBody("ERROR,11,user_id param is null");
	        	throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
		   }

            //Verify user
           $user = UserHelper::getUserByPin($userIdPost);
           
		   if($user==null){
		   		$this->getResponse()->appendBody("ERROR,12,user_id param doesn't exist");
	        	throw new Exception("Error user_id param is null or don't exist",Constants::ERROR_RESOURCE_NOT_FOUND);
		   }

		    //Verify Group Name
		   if($groupNamePost==null){
		   		$this->getResponse()->appendBody("ERROR,21,name param is null");
	        	throw new Exception("Error name param is null",Constants::ERROR_BAD_REQUEST);
		   }

		   //Verify users to invite
		   if($toUsersPost==null){
		   		$this->getResponse()->appendBody("ERROR,31,to_users param is null");
	        	throw new Exception("Error to_users param is null",Constants::ERROR_BAD_REQUEST);
		   }
		   /*********End error treatment ************/
		   //Validate users to invite
		   	  $usersToInvitePins=explode(',', $toUsersPost);
              foreach ($usersToInvitePins as &$userPin) {
				     $userInvite = UserHelper::getUserByPin(trim($userPin));
            		 if($userInvite!=null)
            		 	array_push($usersToInviteArray,$userInvite);
            		 else{
            		 	$pinsError.=$userPin."9&c3";
            		 	$pinsErrorCount++;
						}
				     
				}
				//remove special char from the end
				 $pinsError=substr_replace($pinsError,"",-4);
				//If users to invite does not exist, then abort group creation and show message to client app
				if($pinsErrorCount > 0){
					$this->getResponse()->appendBody("ERROR,32,users to invite don't exist,".$pinsError);
	        		throw new Exception("Error user is empty",Constants::ERROR_RESOURCE_NOT_FOUND);

				}
				else{
					//Create GroupUser
			       $uid=UserHelper::createUser(null,null,0,-1,0,true, $user->getId(),$user->getPin(),$groupNamePost,$groupDescriptionPost,null,null,null);
		           //Get Group User info
				   $groupUser = UserHelper::getUserById($uid);
		           
		           //add myself to group
		            $result=UserLinkHelper::sendLinkRequest($groupUser->getId(),$user->getId(),Constants::ACCEPTED);
		          	$result=UserLinkHelper::sendLinkRequest($user->getId(),$groupUser->getId(),Constants::ACCEPTED);


					//send invitations to users
					foreach ($usersToInviteArray as &$userToInvite) {

						$result=UserLinkHelper::sendInvitationLink($groupUser,$userToInvite);

					}
				}
					
		   	

	      $response="OK,".$groupUser->getPin();
	    	    
	      $this->getResponse()->appendBody($response);
	      
	 
	  }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		//Unknown error,show message app
		//send mail admin with error


		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function inviteuserAction()
    {
     try{
	 
        $userIdPost 		= $this->_getParam('user_id');
        $groupIdPost 		= $this->_getParam('to_group_id');
		$toUserIdPost 		= $this->_getParam('to_user_id');
        $toPhonePost 		= $this->_getParam('to_phone');
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);
	  if($user!=null){

	  	   //Only owner can add users
	  	   if($user->getId()==$groupUser->getgroupOwnerId()){

		       //verifying $toUserId and $toPhone.
		      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
		      $toUser=$responseArray[1];
	      
	      if($toUser!=null){

	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	            $result=UserLinkHelper::sendInvitationLink($groupUser,$toUser);
              
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
                			$response="2-Request sent before";
                			$this->getResponse()->appendBody($response);
                			throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                			break;
                	}
                }
               }

	      }
	      else{
	      	$response="3-Only owner can add users to group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
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

    public function blockuserAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $groupIdPost  = $this->_getParam('to_group_id');
        $toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost  = $this->_getParam('to_phone');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);

	  if($user!=null){
	  	   //Only owner can add users
	  	   if($user->getId()==$groupUser->getgroupOwnerId()){
	         //verifying $toUserId and $toPhone.
		      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
		      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::blockLink($groupUser->getId(),$toUser->getId());
           
                if($result==1){	
                  	$response="OK,".Constants::BLOCKED;
                  	 //add Event to destinationUser
	       			 UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_STATUS_CHANGED);
                  }

                else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	$response="2-Request sent before";
                	$this->getResponse()->appendBody($response);
                	throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                	break;
                }
            }
        }
        else{
	      	$response="3-Only owner can block users to group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
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

    public function exitbyadminAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $groupIdPost  = $this->_getParam('to_group_id');
        $toUserIdPost = $this->_getParam('to_user_id');
        $toPhonePost  = $this->_getParam('to_phone');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);

	  if($user!=null){
	  	   //Only owner can remove user by admin
	  	   if($user->getId()==$groupUser->getgroupOwnerId()){
	         //verifying $toUserId and $toPhone.
		      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
		      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::removeLink($groupUser->getId(),$toUser->getId());
           
                if($result==1){	
                  	$response="OK";
                  	 //add Event to destinationUser
	       			 UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_STATUS_CHANGED);

	       			 //if group then notify the other users of user exit

				     $userName=$toUser->getName();
				     if(is_null($userName) || $userName=="")
				        $userName=$toUser->getPin();

						//Send events
					    $params=$groupUser->getPin()."9&c3".$toUser->getPin()."9&c3".$userName;
					    UserEventHelper::sendEventToAllUserLinksWithParams($groupUser->getId(),$groupUser->getPin(),$params,Constants::EVENT_GROUPS_USER_EXIT_BY_ADMIN);	 
                  }

                else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	$response="2-Request sent before";
                	$this->getResponse()->appendBody($response);
                	throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                	break;
                }
            }
        }
        else{
	      	$response="3-Only owner can block users to group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
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
     
    public function exitAction()
    {
     try{
	 
        $groupIdPost  = $this->_getParam('to_group_id');
        $userIdPost = $this->_getParam('user_id');
   

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);

	  	      if($user==null){
	        	$response="Error user does not exist";
                $this->getResponse()->appendBody($response);
                throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        }

             if($groupUser==null){
	        	$response="4-to_group_id is not a group";
                $this->getResponse()->appendBody($response);
                throw new Exception($e,Constants::ERROR_BAD_REQUEST);
	        }

	        if(!$groupUser->getIsGroup()){
	        	$response="3-to_group_id is not a group";
                $this->getResponse()->appendBody($response);
                throw new Exception($e,Constants::ERROR_BAD_REQUEST);
	        }
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
                
                $result=UserLinkHelper::removeLink($groupUser->getId(),$user->getId());
           
                if($result==1){	
                  	$response="OK";

				    $userName=$user->getName();
				      if(is_null($userName) || $userName=="")
				         $userName=$user->getPin();

				     $params=$groupUser->getPin()."9&c3".$user->getPin()."9&c3".$userName;
					 UserEventHelper::sendEventToAllUserLinksWithParams($groupUser->getId(),$groupUser->getPin(),$params,Constants::EVENT_GROUPS_USER_EXIT);
                  }

                else {
                	//TODO- ERROR,Only user who receives request can Accepts
                	//TODO ERROR,request yet accepted 
                	//TODO - or user blocked
                	$response="2-Request sent before";
                	$this->getResponse()->appendBody($response);
                	throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                	break;
                }
            

	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }


    public function infoAction()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $groupIdPost  = $this->_getParam('to_group_id');


	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);

	  if($user!=null){
        //User is on the group?
	  	if(UserLinkHelper::getLinkStatus($user->getId(),$groupUser->getId())!=Constants::ACCEPTED){
            $response="1- User does'nt belongs to the group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);
	  	}
        //Everything Ok make request
          $usersInGroup=UserLinkHelper::getSendByMe($groupUser->getId(),Constants::ACCEPTED,"im");
           
                if(!is_null($usersInGroup))	
                  	$response=$usersInGroup.",".$groupUser->getgroupOwnerPin().",".$groupUser->getName();

                else
                	$response="OK,0";

           $this->getResponse()->appendBody($response);
      }else{
		    $response="2 - Error user param is empty";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
	  	}
       


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
        $toUserIdPost = $this->_getParam('to_group_id');

	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  	if($user->getIsGroup()){
	  		$response="3-user_id must not be from a group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
	  	 }

	      //verifying $toUserId
	      $toUser=UserHelper::getUserByPin($toUserIdPost);
	 
	  if($user==null)
	      throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	  if($toUser==null)
	      throw new Exception("Error Group PIN does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      	    
                $result=UserLinkHelper::acceptLink($user->getId(),$toUser->getId());
           
                if($result==1){
	       			 	  $response=UserLinkHelper::getGroupMembersAndSendEvent($toUser,$user,Constants::ACCEPTED);
	       			 	
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
            
	
	 
	  	$this->getResponse()->appendBody($response);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }


    public function info1Action()
    {
     try{
	 
        $userIdPost   = $this->_getParam('user_id');
        $groupIdPost  = $this->_getParam('to_group_id');


	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 //Get Group User info
		 $groupUser = UserHelper::getUserByPin($groupIdPost);

        //User is on the group?
	  	if(UserLinkHelper::getLinkStatus($user->getId(),$groupUser->getId())!=Constants::ACCEPTED){
            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);
	  	}
        //Everything Ok make request
          $usersInGroup=UserLinkHelper::getSendByMe($groupUser->getId(),Constants::ACCEPTED,"im");
           
                if(!is_null($usersInGroup))	
                  	$response=$usersInGroup.",".$groupUser->getgroupOwnerPin().",".$groupUser->getName();

                else
                	$response="OK,0";

           $this->getResponse()->appendBody($response);
      
       


      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }

    }
	

}

