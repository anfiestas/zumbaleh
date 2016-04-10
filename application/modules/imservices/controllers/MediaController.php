<?php
require_once 'Helper/UserHelper.php';
require_once 'Helper/UserLinkHelper.php';
require_once 'Helper/IMessageHelper.php';
require_once 'Helper/MediaHelper.php';
require_once 'Helper/UserEventHelper.php';
require_once 'Objects/Constants.php';

class Imservices_MediaController extends Zend_Controller_Action
{

   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->userTimeStampPost = $this->_getParam('user_timestamp');
		$this->connectionType    = $this->_getParam('connection_type');
		
    }
    
    

    //Send a media from X to Y
    public function sendAction()
    {
      	$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
      try{

		$userIdPost = $this->_getParam('user_id');
	    $toPhonePost = $this->_getParam('to_phone');
	    $fileName = $this->_getParam('fileName');
	    $toUserIdPost = $this->_getParam('to_user_id');
	    $aliasPost = $this->_getParam('alias');
        $userMid="null";
        $logger->info("--------- SEND ACTION  -----");
	//Validate params
		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	    if ($this->connectionType==null)
	        throw new Exception("Error connection type is null",Constants::ERROR_RESOURCE_NOT_FOUND);
 
          //Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){
	    //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	    //Manage multiple destination PINS
	    //send invitations to users
		 if($toUserIdPost!=null){
		   	  $usersToSend=explode(',', $toUserIdPost);
		   	  	//Process upload one time
			 	//cannot set limit in safe_mode
				 set_time_limit (300);
				  if ($this->getRequest()->isPost()) {
					    	$uploadedFileURL=MediaHelper::doUpload($user->getPin(),$fileName);
				   }
				    else
				      $uploadedFileURL=NULL;

				//Create zumbNail only if video
			   $logger->info("--------- Creating thumbnails  -----".$uploadedFileURL);
			   if($uploadedFileURL!=NULL && ($this->connectionType==32 || $this->connectionType==1232)){
						exec("/usr/lib/php5/libexec/zumbnailCreator ".$uploadedFileURL);	
				 }
              foreach ($usersToSend as &$userPin) {
				     $toUser = UserHelper::getUserByPin(trim($userPin));
				     //send to user
				          //verifying $toUserId and $toPhone.
				    //$responseArray=UserHelper::getUserAndcheckErrors($userToSend,$toPhonePost);
				    //$toUser = $responseArray[1];

					    if($toUser->getIsGroup()){
					    	if(UserLinkHelper::getLinkStatus($user->getId(),$toUser->getId())==Constants::ACCEPTED){
				                	 $mid = IMessageHelper::sendIMessage($user,$uploadedFileURL,$toUser->getId(),$toUser->getFullPhone(),$toUser->getPin(),$this->connectionType,$user->getPin(), $aliasPost,Constants::SMS_SUBMITED);
				            		 $response="OK,".$mid.",".$uploadedFileURL;;
							}
							else{
								$response="3-you need to accept group invitation before start sending messages";
					            $this->getResponse()->appendBody($response);
					            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);

							}

					    }else{
						    	$mid = IMessageHelper::sendIMessage($user,$uploadedFileURL,$toUser->getId(),$toUser->getFullPhone(),$toUser->getPin(),$this->connectionType,null,null,Constants::SMS_SUBMITED);
						     
						      if($mid > 0){
						      	$response="OK,".$mid.",".$uploadedFileURL;
						      	//$this->getResponse()->appendBody($response);
							    $logger->info("--------- Thumbnail OK  -----");
						        //add Event to destinationUser
						        UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_MESSAGES_PENDINGS);
						        $logger->info("--------- Send Pending  -----");
						        }
						      else{
						      	$response="3-link is broken";
					            $this->getResponse()->appendBody($response);
					            throw new Exception($e,Constants::ERROR_AUTHENTICATION_FAILED);
						      }
					      }   

				}
		   	
		   }
	      $this->getResponse()->appendBody($response);

	    }
	    else{
	      throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	    }
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }
    
     public function getAction()
    {
      try{

		$userIdPost = $this->_getParam('user_id');
		$toUserIdPost = $this->_getParam('to_user_id');
	    $fileID = $this->_getParam('file_id');
      
	//Validate params
		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
 
          //Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){
	    //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);

 		//Process get file
	    $fullFilePath=APPLICATION_PATH."/../tmp/".$fileID;
	    
	    // magic_mime module installed?
			if (function_exists('mime_content_type')) {
				$mtype = mime_content_type($fullFilePath);
			}
			// fileinfo module installed?
			else if (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME); // return mime type
				$mtype = finfo_file($finfo, $fullFilePath);
				finfo_close($finfo); 
			}
	
		header('Content-Description: File Transfer');
	    //header('Content-Transfer-Encoding: binary');
	    //header('Expires: 0');
		header('Content-Type: '.$mtype);
	    header('Content-Disposition: attachment; filename="'.$fileID.'"');
	    header('Content-Length: ' . filesize($fullFilePath));
	    //Expires after one year in seconds
	  	header('Cache-Control: max-age=31536000');
	    readfile($fullFilePath);

	    }
	    else{
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

     public function getthumbnailAction()
    {
      try{

		$userIdPost = $this->_getParam('user_id');
		$toUserIdPost = $this->_getParam('to_user_id');
	    $fileID = $this->_getParam('file_id');
	    $type = $this->_getParam('type');
      
	//Validate params
		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
 
          //Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){
	    //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);

 		//Process get file
	    $fullFilePath=APPLICATION_PATH."/../tmp/".$fileID.".".$type;
	    
	    // magic_mime module installed?
			if (function_exists('mime_content_type')) {
				$mtype = mime_content_type($fullFilePath);
			}
			// fileinfo module installed?
			else if (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME); // return mime type
				$mtype = finfo_file($finfo, $fullFilePath);
				finfo_close($finfo); 
			}
	
		header('Content-Description: File Transfer');
	    //header('Content-Transfer-Encoding: binary');
	    //header('Expires: 0');
		header('Content-Type: '.$mtype);
	    header('Content-Disposition: attachment; filename="'.$fileID.'.'.$type.'"');
	    header('Content-Length: ' . filesize($fullFilePath));
	    readfile($fullFilePath);

	    }
	    else{
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

