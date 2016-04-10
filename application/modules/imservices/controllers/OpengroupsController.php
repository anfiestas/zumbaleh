<?php

require_once 'Helper/UserHelper.php';
require_once 'Helper/UserLinkHelper.php';
require_once 'Objects/Constants.php';
require_once 'Helper/UserEventHelper.php';
require_once 'Helper/OpenGroupHelper.php';

class Imservices_OpengroupsController extends Zend_Controller_Action
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
       	   //this is admin user
	       $userIdPost       = $this->_getParam('user_id');
	       $groupNamePost 	 = $this->_getParam('name');
	       $aliasPost 		 = $this->_getParam('alias');
	       $groupDescriptionPost	 = $this->_getParam('description');
	       $groupCategoryPost	 = $this->_getParam('category_id');
	       $groupPasswordPost = $this->_getParam('password');
	       $groupModePost		 = $this->_getParam('mode');
           $uid=null;
          
           $groupUser=null;
           $user = null;


  			 //Verify user
  			if($userIdPost==null){
		   		$this->getResponse()->appendBody("ERROR,11,user_id param is null");
	        	throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
		   }

		           $user = UserHelper::getUserByPin($userIdPost);

                      //Create group in search table
                    $uid=OpenGroupHelper::create($groupNamePost,$groupDescriptionPost,$groupModePost,$groupCategoryPost,$user->getId(),$user->getPin(),$groupPasswordPost);
                    

                    if($uid!=null){
                       //Get Group User info
    				          $groupUser = UserHelper::getUserById($uid);

    		           //add myself to group                   
                        $result=UserLinkHelper::acceptLink($user->getId(),$groupUser->getId(),Constants::ACCEPTED);
                        
                        //Add alias to group
        		            if($aliasPost!=null)
                            $userAddedOK = OpenGroupHelper::addUserToGroup($groupUser->getId(),$user->getId(),$aliasPost);
                        
                        $response="OK,".$groupUser->getPin();
                        $this->getResponse()->appendBody($response);
                    }
                    else{
                        $this->getResponse()->appendBody("Error,2, Unknow error creating channel");
                        throw new Exception("Error,2,  Unknow error creating channel",Constants::ERROR_INTERNAL_SERVER);
                        }
	    	    

	  }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }
    public function getcategoriesAction()
    {
      $userIdPost  = $this->_getParam('user_id');
    	//$response=json_encode(Constants::$categories);
        $categories="";
        //           
        if ($userIdPost=="MXWH9250" || $userIdPost=="YQAB3856" || $userIdPost=="WXTP4924" || $userIdPost=="PGMU3624" || $userIdPost=="IVZG8101" ||
        $userIdPost=="LHZH0213" || $userIdPost=="DGNX0151" || $userIdPost=="NXSQ6101" || $userIdPost=="NKCE8354" || $userIdPost=="HATP8589" ||
        $userIdPost=="HATP8589" || $userIdPost=="WQZS1397" || $userIdPost=="NEMC8294" ||  $userIdPost=="EBOK1948")

           Constants::$categories[0] = "Secret";

        foreach(Constants::$categories as $id => $category){
              $categories.=$id.",".$category.",9&c3";
        }
      


    	$this->getResponse()->appendBody("OK,".count(Constants::$categories).",".$categories);
 
    }

    public function getgroupsbycategoryAction()
    {   $category_id   = $this->_getParam('category_id');
    	

    	$groups = OpenGroupHelper::getGroupsByCategory($category_id);
        $this->getResponse()->appendBody($groups);
 
    }

    public function subscribeAction()
    {
      try{

        $userIdPost   = $this->_getParam('user_id');
        $groupIdPost   = $this->_getParam('to_group_id');
        $aliasPost   = $this->_getParam('alias');
        $passwordPost   = $this->_getParam('password');

        //Verify user
        if($userIdPost==null){
                $this->getResponse()->appendBody("ERROR,11,user_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }
        $user = UserHelper::getUserByPin($userIdPost);
        
        if($groupIdPost==null){
                $this->getResponse()->appendBody("ERROR,12,group_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
        }
        $userGroup = OpenGroupHelper::getInfo($groupIdPost);

        //verify pass
        $userGroupPass= OpenGroupHelper::getFieldValue($userGroup["id"],'password');

         //check password if needed
         if($userGroupPass!=null){
                if($userGroupPass!= md5($passwordPost)){
                    $this->getResponse()->appendBody("ERROR,13,group password is invalid");
                    throw new Exception("ERROR,13,group password is invalid",Constants::ERROR_AUTHENTICATION_FAILED);
                }
         }

        //Update Alias
         if($aliasPost==null){
                $this->getResponse()->appendBody("ERROR,13,alias param is null");
                throw new Exception("Error alias param is null",Constants::ERROR_BAD_REQUEST);

         }
        $user->setAlias($aliasPost);

        // Check if user blocked by ADMIN
        if(UserLinkHelper::getLinkStatus($userGroup["id"],$user->getId())==Constants::BLOCKED){     
            $this->getResponse()->appendBody("ERROR,13,user blocked");
            throw new Exception("ERROR,13,user blocked",Constants::ERROR_AUTHENTICATION_FAILED);
        }

        //Add alias to group
        $userAddedOK = OpenGroupHelper::addUserToGroup($userGroup["id"],$user->getId(),$aliasPost);
        

         //Subscribe user               
         $result=UserLinkHelper::acceptLink($user->getId(),$userGroup["id"],Constants::ACCEPTED);

         //Send notif to members;
           
                if($result==1){
                    //update total num users count
                    OpenGroupHelper::updateFieldValue($userGroup["id"],"users_count",($userGroup["users_count"]+1));

                    $params=$userGroup['pin']."9&c3".$user->getPin()."9&c3".$user->getAlias();
                    UserEventHelper::sendEventToAllUserLinksWithParams($userGroup['id'],$userGroup['pin'],$params,Constants::EVENT_GROUPS_USER_IN);

                    //Return group info
                    $response="OK,".$userGroup["pin"].",".$userGroup["name"].",".$userGroup["description"].",".$userGroup["mode"].",".($userGroup["users_count"]+1).",".$userGroup["group_owner_pin"];
                        
                } 


    $this->getResponse()->appendBody($response);

    }catch (Exception $e) {

    $this->getRequest()->setParam('error_code', $e->getCode());
    $this->getRequest()->setParam('error_message', $e->getMessage());
    $this->getRequest()->setParam('error_trace', $e->getTraceAsString());
    $this->_forward('n2sms', 'error','default');
                 
        }

    }

    public function unsubscribeAction()
    {
      $groupIdPost  = $this->_getParam('to_group_id');
      $userIdPost = $this->_getParam('user_id');
      $aliasPost   = $this->_getParam('alias');
   
          //Verify user
        if($userIdPost==null){
                $this->getResponse()->appendBody("ERROR,11,user_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }
        $user = UserHelper::getUserByPin($userIdPost);

        if($groupIdPost==null){
                $this->getResponse()->appendBody("ERROR,12,group_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }

       //Verify user
        $user = UserHelper::getUserByPin($userIdPost);

        if($aliasPost!=null){
                $user->setAlias($aliasPost);
        }

       //Get Group User info
           $userGroup = OpenGroupHelper::getInfo($groupIdPost);

      // Check if user blocked by ADMIN
        if(UserLinkHelper::getLinkStatus($userGroup["id"],$user->getId())==Constants::BLOCKED){     
            $this->getResponse()->appendBody("OK");
            //throw new Exception("ERROR,13,user blocked",Constants::HTTP_OK);
        }
        else{
    	     $result=UserLinkHelper::removeLink($userGroup["id"],$user->getId());

            if($result==1){
                  //Removes user from opengroup users list(alias unicity control)
                   OpenGroupHelper::removeUserFromGroup($userGroup["id"],$user->getId());
                  //update total num users count
                  OpenGroupHelper::updateFieldValue($userGroup["id"],"users_count",($userGroup["users_count"]-1));
                  //Send events
                  $params=$userGroup['pin']."9&c3".$user->getPin()."9&c3".$user->getAlias();
                 UserEventHelper::sendEventToAllUserLinksWithParams($userGroup['id'],$userGroup['pin'],$params,Constants::EVENT_GROUPS_USER_EXIT);

                  //Return group info
                  $response="OK";
                        
                } else{
                  $response="ERROR,2,Request sent before";
                }
 
          $this->getResponse()->appendBody($response);
      }
    }

    public function searchAction()
    {
    	$groupName   = $this->_getParam('value');
    	$groupsList=OpenGroupHelper::searchGroupsByName($groupName);

    	$this->getResponse()->appendBody($groupsList);
 
    }

    public function infoAction()
    { $groupIdPost   = $this->_getParam('to_group_id');
    	
        if($groupIdPost==null){
                $this->getResponse()->appendBody("ERROR,12,group_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }

        $userGroup = OpenGroupHelper::getInfo($groupIdPost);

        //Return group info
        if($userGroup!=null){
          $response="OK,".$userGroup["pin"].",".$userGroup["name"].",".$userGroup["description"].",".$userGroup["mode"].",".$userGroup["users_count"].",".$userGroup["group_owner_pin"];
       
        }else{
           $response="ERROR,13, This group_id is not from opengroup";
        }
        
        $this->getResponse()->appendBody($response);
    }

    public function gettotalelemsAction()
    {$groupIdPost   = $this->_getParam('to_group_id');
      
        if($groupIdPost==null){
                $this->getResponse()->appendBody("ERROR,12,group_id param is null");
                throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
           }
       $userGroup = OpenGroupHelper::getInfo($groupIdPost);

    	 $numElems = OpenGroupHelper::getNumElems($userGroup["id"]);
       $response="OK,".$numElems;
       $this->getResponse()->appendBody($response);
 
    }

    public function isuseranonymousAction()
    {
    	  $userIdPost   = $this->_getParam('user_id');
        $groupIdPost  = $this->_getParam('to_group_id');
        $toUserIdPost = $this->_getParam('to_user_id');

        $toUser = UserHelper::getUserByPin($toUserIdPost);
        $isAnonymous = UserHelper::getFieldValue($toUser->getId(),"is_anonymous");

       $response="OK,".$isAnonymous;
       $this->getResponse()->appendBody($response);
 
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
        $aliasPost  = $this->_getParam('alias_name');

          $user = UserHelper::getUserByPin($userIdPost);
          //Get Group User info
           $groupUser = UserHelper::getUserByPin($groupIdPost);
           //to user is ok
           $toUser = UserHelper::getUserByPin($toUserIdPost);

            //Verify POST user data
            self::validateAdminParams($user,$groupUser,$toUser);
           
                
            $response="OK";
            self::kickOutUserFromOpenGroupAndNotify($groupUser,$toUser,$aliasPost);

                
             
            
        
      $this->getResponse()->appendBody($response);
   
      }catch (Exception $e) {

        $this->getRequest()->setParam('error_code', $e->getCode());
        $this->getRequest()->setParam('error_message', $e->getMessage());
        $this->getRequest()->setParam('error_trace', $e->getTraceAsString());
        $this->_forward('n2sms', 'error','default');
                     
        }
   
    }

    public function reportAction()
    { require_once 'Helper/BannedDeviceHelper.php';

      $adminIdPost  = $this->_getParam('user_id');
      $levelPost  = $this->_getParam('level');
      $reportUserIdPost  = $this->_getParam('report_user_id');
      $groupIdPost  = $this->_getParam('report_group_id');
      $aliasPost  = $this->_getParam('alias_name');
      //log only

      $typePost  = $this->_getParam('type_message');
      $textPost  = $this->_getParam('text_name');
      
       $writer = new Zend_Log_Writer_Stream('../private/logs/moderation.log');
        $logger = new Zend_Log($writer);

        $user = UserHelper::getUserByPin($adminIdPost);
      //Get Group User info
       $groupUser = UserHelper::getUserByPin($groupIdPost);
       //to user is ok
       $toUser = UserHelper::getUserByPin($reportUserIdPost);
      //Verify is admin
      self::validateAdminParams($user,$groupUser,$toUser);

       switch ($levelPost) {
           case 1:
                /********Send WARNING message********/

                require_once 'Helper/IMessageHelper.php';

                $spooraUser = UserHelper::getUserById(-1);   

                $textWarning="El administrador del canal '".$groupUser->getName()."' ha reportado que has enviado mensajes que violan las normas de uso de la aplicación. En caso de reincidencia el administrador te expulsará del canal";

              //Send message promo
              $mid = IMessageHelper::sendIMessage($spooraUser,$textWarning,$toUser->getId(),null,$toUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
        
              //add Event to destinationUser
             // UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::SMS_SUBMITED); 

       /***********/  
          
               break;
           case 2:

                 require_once 'Helper/IMessageHelper.php';

                $spooraUser = UserHelper::getUserById(-1);   

                $textWarning="El administrador del canal '".$groupUser->getName()."' te ha expulsado debido a la violacion reiterada de las normas de uso de la aplicación.";

              //Send message promo
              $mid = IMessageHelper::sendIMessage($spooraUser,$textWarning,$toUser->getId(),null,$toUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
               
              //same as exit_by_admin method
              self::kickOutUserFromOpenGroupAndNotify($groupUser,$toUser,$aliasPost);
               break;
           case 3:
              //bann user by PIN
              //TODO - change field on DB: tokens_program by user_status
               UserHelper::updateFieldValue($toUser->getId(),"tokens_program",Constants::TOKENS_USER_BANNED_GENERIC);

               //NOW SEND NOTIFICATIONS AND REMOVE LINKS AND USER FROM OPENGROUP
               //TODO FACTORIZAR
               self::kickOutUserFromAllOpenGroupsAndNotify($groupUser,$toUser,$aliasPost);
               break;
           case 4:
              //bann user by device
               BannedDeviceHelper::addBanToUserDevices($toUser->getPin(),$toUser->getId());

               //NOW SEND NOTIFICATIONS AND REMOVE LINKS AND USER FROM OPENGROUP
               //TODO FACTORIZAR
               self::kickOutUserFromAllOpenGroupsAndNotify($groupUser,$toUser,$aliasPost);
               break;
       }

        //TODO Log file
        $logger->info("UserInfo: ".$toUser->getId()."-".$toUser->getPin().",".$aliasPost." | Group: ".$groupIdPost."-".$groupUser->getName()." | Alert origin: ".$textPost.", LEVEL: ".$levelPost);

       $this->getResponse()->appendBody("OK");
 
    }

  //******* Private Controller helper methods *************/

    private function kickOutUserFromOpenGroupAndNotify($groupUser,$toUser,$alias)
     {
                      //TODO: Definir Constantes codigos nuevos de evento para Baneo por device, por pin y expulsado
                        $params=$groupUser->getPin()."9&c3".$toUser->getPin()."9&c3".$alias;
                        // in this case userId is a groupId so it will notify all users from opengroup
                       UserEventHelper::sendEventToAllUserLinksWithParams($groupUser->getId(),$groupUser->getPin(),$params,Constants::EVENT_GROUPS_USER_EXIT_BY_ADMIN);

                      //$result=UserLinkHelper::removeLink($group["id"],$toUser->getId());
                      $result=UserLinkHelper::blockLink($groupUser->getId(),$toUser->getId());
                  //Removes user from opengroup users list(alias unicity control)
                   OpenGroupHelper::removeUserFromGroup($groupUser->getId(),$toUser->getId());
                  //update total num users count
                  //OpenGroupHelper::updateFieldValue($group["id"],"users_count",($userGroup["users_count"]-1));
                  if($result!=1){ 

                    $response="2-Request sent before";
                    $this->getResponse()->appendBody($response);
                    throw new Exception($e,Constants::ERROR_BAD_REQUEST);
                  }

      

     }
  private function kickOutUserFromAllOpenGroupsAndNotify($groupUser,$toUser,$alias)
  {

      //update total num users count
      //OpenGroupHelper::updateFieldValue($groupUser->getId(),"users_count",($userGroup["users_count"]+1));
      $openGroupsFromUser=OpenGroupHelper::getOpengroupsByUser($toUser->getId());

       foreach($openGroupsFromUser as $group){

                  $toUser->setAlias($group['alias']);
                 //TODO: Definir Constantes codigos nuevos de evento para Baneo por device, por pin y expulsado
                  $params=$group['pin']."9&c3".$toUser->getPin()."9&c3".$group['alias'];
                  // in this case userId is a groupId so it will notify all users from opengroup
                 UserEventHelper::sendEventToAllUserLinksWithParams($group['id'],$group['pin'],$params,Constants::EVENT_GROUPS_USER_EXIT_BY_ADMIN);

                  $result=UserLinkHelper::blockLink($group["id"],$toUser->getId());
                  
                  //update total num users count
                  //OpenGroupHelper::updateFieldValue($group["id"],"users_count",($userGroup["users_count"]-1));

                 
             }

     }

     private function validateAdminParams($user,$groupUser,$toUser){


      // check if user is admin of the group and notnull
      if($user==null || $groupUser==null || ($user->getId()!=$groupUser->getgroupOwnerId())){
           $response="3-Only admin can block users to group";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
      }

       if($toUser==null){
            $response="4-target user is null or not correct";
            $this->getResponse()->appendBody($response);
            throw new Exception($e,Constants::ERROR_BAD_REQUEST);
       }


     }

  

}

