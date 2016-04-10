<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Constants.php';

class IMessageHelper {
    
    
     public static function createIMessageBroadcast(&$user,$text,$sourceNumber,$destinationUserId,$to,$destinationPin,$realCost,$userCost,$countryId,$externalId,$providerId,$messageTypeId,$groupMemberPin,$alias,$messageStatus)
    {
     $statusName="SMS UNKNOWN";
        try {
            require_once 'Helper/UserLinkHelper.php';
            //Check if other user has not blocked sender user
            $linkStatus=UserLinkHelper::getLinkStatus($destinationUserId,$user->getId());
            
            if($linkStatus==1)
               $linkStatus2=UserLinkHelper::getLinkStatus($user->getId(),$destinationUserId);

            if($user->getId()==-1 || (!is_null($linkStatus) && $linkStatus==1 && $linkStatus2==1)){
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();

                if($messageStatus==Constants::SMS_DELIVERED)
                   $statusName="Message Delivered";
                else if($messageStatus==Constants::SMS_SUBMITED)
                   $statusName="Message SUBMITED";
                //TODO - status_id definir constantes
                //TODO - Enable transactions
                //create broadcast
                $data = array(
                'broadcast_id'           => 1,
                'uid'                    => $user->getId(),
                'source_pin'             => $user->getPin(),
                'external_id'            => trim($externalId),
                'destination_number'     => $to,
                'destination_pin'       => $destinationPin,
                'country_id'             => $countryId,
                'text'                   => $text,
                'user_cost'              => $userCost,
                'real_cost'              => $realCost,
                'provider_id'            => $providerId,
                'status_id'              => $messageStatus,
                'status_detail'          => $messageStatus." - ".$statusName,
                'status_timestamp'       => time(),
                'source_number'          => $sourceNumber,
                'destination_uid'        => $destinationUserId,
                'message_type_id'        => $messageTypeId,
                'group_member_pin'        => $groupMemberPin
                );
                
      
                    
                $db->insert('message_broadcast', $data);
                
                $mid = $db->lastInsertId();
               
                $db->commit();

                $data["id"]=$mid;


                //send to Group si groupMember
                if($groupMemberPin!=null){
                    //Add user profile name if alias is null
                    if($alias==null)
                         $data["name"]=$user->getName();
                

                    $data["source_pin"]=$destinationPin;

                    self::sendMessageToGroupDevices($data,$destinationUserId,$alias);
                }
                else{
                //send to User devices
                   self::sendMessageToUserDevices($data,$destinationUserId);
                }

                $db->closeConnection();
                
               return $mid;
           }
           else{
            //negative id means an error sending because userlink is broken
            return -1;
           } 
           
           
        }catch (Exception $e) {

          $db->rollBack();
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
    }

    public static function sendMessageToUserDevices($data,$uid)
    {
      require_once 'Helper/PushNotificationsHelper.php';
        try {
      
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $db->fetchAll('SELECT * FROM user_device WHERE uid = ?', $uid);
                
                $total_items=count($result);
              
                
                if ($total_items > 0)
                { 
                  foreach($result as $userDevice){
                         
                          // Storing message to user device on DB
                          $data["broadcast_id"]=$userDevice->id;
                          //$data["text"]="";
                          $data["mac_address"]=$userDevice->mac_address;
                          $db->insert('message_broadcast', $data);
                          $db->commit();
                    }

                    $db->closeConnection();

                  $registrationIDs = array();
                  $nonPushDevices = array();

                       // Preparing data of message
                            $fullFilePath=APPLICATION_PATH."/../tmp/".$data["text"];

                              $messagesArray= ",".$data["id"].",".(empty($data["source_number"])?"null":$data["source_number"]).",".
                              $data["destination_pin"].",".
                              $data["source_pin"].",".
                              (empty($data["destination_number"])?"null":$data["destination_number"]).",".
                              $data["status_timestamp"].",".
                              $data["text"].",".
                              ($data["message_type_id"] < 3 ?"0":filesize($fullFilePath)).",".
                              (empty($data["group_member_pin"])?"0":"1").",".
                              (empty($data["group_member_pin"])?"null":$data["group_member_pin"]).",".
                              $data["message_type_id"].",".
                              "0,".
                              "null,".
                              "9&c3";
                                        //check user devices 
                  foreach($result as $device){

                        if(!empty($device->push_id)){
                          //send push to device
                           array_push($registrationIDs,$device->push_id);
                        }else{
                          //create event in DB if not browser sesion
                          //TODO: BROWSER does not support events NOW
                          if($device->type==Constants::SESSION_TYPE_DEVICE){
                            array_push($nonPushDevices,$device->id);
                          }
                    }
         
                    }
                //send Notifications
               if(count($registrationIDs) > 0){

                   $message = "OK,".Constants::EVENT_PUSH_MESSAGES_PENDINGS.",1".$messagesArray;
                   $result=PushNotificationsHelper::sendGCM($uid,$data["destination_pin"],$registrationIDs,$message);
                }

                   
                }
                

           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
   public static function sendMessageToGroupDevices($data,$uid,$alias)
    {
      require_once 'Helper/PushNotificationsHelper.php';
      require_once 'Helper/OpenGroupHelper.php';
        try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $db->fetchAll('SELECT u.*,ud.id as device_id,ud.mac_address,ud.push_id FROM user_link_request ul,user u,user_device ud where ul.uid=? and u.id=ul.with_user_id and ud.uid=u.id and ul.status=1', $uid);
                
                $total_items=count($result);
              
                $registrationIDs = array();

                if ($total_items > 0)
                { 
                  foreach($result as $userDevice){
                          
                         
                          if(!empty($userDevice->push_id))
                                array_push($registrationIDs,$userDevice->push_id);
                    }

                    $db->closeConnection();
                    ///check if opengroup or normal group
                    //replace this by message_type for channels?
                   $isChannel = OpenGroupHelper::getInfo($data["destination_pin"]);
                          // Preparing data of message
                          //TODO json format- now only custom format
                      
                              $fullFilePath=APPLICATION_PATH."/../tmp/".$data["text"];

                              $messagesArray= ",".$data["id"].",".(empty($data["source_number"])?"null":$data["source_number"]).",".
                              $data["destination_pin"].",".
                              $data["source_pin"].",".
                              (empty($data["destination_number"])?"null":$data["destination_number"]).",".
                              $data["status_timestamp"].",".
                              $data["text"].",".
                              ($data["message_type_id"] < 3 ?"0":filesize($fullFilePath)).",".
                              (empty($data["group_member_pin"])?"0":"1").",".
                              (empty($data["group_member_pin"])?"null":$data["group_member_pin"]).",".
                              $data["message_type_id"].",".
                              ($isChannel==null?"0":"1").",".
                              ($isChannel==null?$data["name"]:$alias).",".
                              "9&c3";

                              $message = "OK,".Constants::EVENT_PUSH_MESSAGES_PENDINGS.",1".$messagesArray;
                              //Send push to all Group devices directly with only one request
                              $result=PushNotificationsHelper::sendGCM($uid,$data["destination_pin"],$registrationIDs,$message);
                              $message2 = "OK,1,".Constants::EVENT_MESSAGES_PENDINGS;
                              $result=PushNotificationsHelper::sendGCM($uid,$data["destination_pin"],$registrationIDs,$message2);
                }
                

           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

  public static function sendIMessage($user,$text,$destinationUserId,$destinationPhone,$destinationPin,$connectionTypeId,$groupMemberPin,$alias,$status)  
  {
     try {
            
     //TODO-encriptar text
      $text=str_replace(",",";",substr($text,0,Constants::MAX_IM_TEXT_SIZE));
      $mid=IMessageHelper::createIMessageBroadcast($user,$text,$user->getFullPhone(),$destinationUserId,$destinationPhone,$destinationPin,
              0,0,-1,"IM",0,$connectionTypeId,$groupMemberPin,$alias,$status);
      
      return $mid;
   
    }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
  }
    public static function updateIMessageBroadcast($message){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();
                    
                    if($message->getMessageId()==null)
                        throw new Exception("MessageId null in updateMessage",Constants::ERROR_RESOURCE_NOT_FOUND);
                    
                  //update new user balance
                    if($message->getBroadcastId()!=NULL)
                        $data["broadcast_id"] = $message->getBroadcastId();
                    if($message->getUserId()!=NULL)
                        $data["uid"] = $message->getUserId();
                    if($message->getExternalId()!=NULL)
                        $data["external_id"] = $message->getExternalId();
                    if($message->getDestinationNumber()!=NULL)
                        $data["destination_number"] = $message->getDestinationNumber();	
                    if($message->getCountryId()!=NULL)
                        $data["country_id"] = $message->getCountryId();
                    if($message->getText()!=NULL)
                        $data["text"] = $message->getText();
                    if($message->getUserCost()!=NULL)
                        $data["user_cost"] = $message->getUserCost();
                    if($message->getRealCost()!=NULL)
                        $data["real_cost"] = $message->getRealCost();
                    if($message->getProviderId()!=NULL)
                        $data["provider_id"] = $message->getProviderId();	
                    if($message->getStatus()!=NULL)
                        $data["status_id"] = $message->getStatus();
                    if($message->getStatusDetail()!=NULL)
                        $data["status_detail"] = $message->getStatusDetail();	
                    if($message->getStatusTimeStamp()!=NULL)
                        $data["status_timestamp"] = $message->getStatusTimeStamp();
			
            		    if($message->getSourceNumber()!=NULL)
                                    $data["source_number"] = $message->getSourceNumber();
            		    if($message->getDestinationUserId()!=NULL)
                                    $data["destination_uid"] = $message->getDestinationUserId();
            		    if($message->getMessageTypeId()!=NULL)
                                    $data["message_type_id"] = $message->getMessageTypeId();
                    if($message->getSourcePin()!=NULL)
                                    $data["source_pin"] = $message->getSourcePin();
                    if($message->getDestinationPin()!=NULL)
                        $data["destination_pin"] = $message->getDestinationPin();
		   
                    $where[] = "id = ".$message->getMessageId();
                    $db->update('message_broadcast', $data, $where);
        
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Zend_Db_Adapter_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                 // perhaps a failed login credential, or perhaps the RDBMS is not running
                } catch (Zend_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
                
                }catch (Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
              
                  
                }
    }    
    
    public static function getIMessageBroadcastById($smsId){
        require_once 'Objects/IMessageBroadcast.php';
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE broadcast_id=1 and id = ?', $smsId);
            
            if (count($result)== 1)
            {
                //$user= new User($result[0]->uid,$result[0]->telephone,$result[0]->balance,$result[0]->country_id);
               $messageBroadcast = new IMessageBroadcast();
               $messageBroadcast->setMessageId($result[0]->id);
               $messageBroadcast->setExternalId($result[0]->external_id);
               $messageBroadcast->setBroadcastId($result[0]->broadcast_id);
               $messageBroadcast->setDestinationNumber($result[0]->destination_number);
               $messageBroadcast->setCountryId($result[0]->country_id);
               $messageBroadcast->setUserCost($result[0]->user_cost);
               $messageBroadcast->setRealCost($result[0]->real_cost);
               $messageBroadcast->setProviderId($result[0]->provider_id);
               $messageBroadcast->setText($result[0]->text);
               $messageBroadcast->setStatusTimeStamp($result[0]->status_timestamp);
               $messageBroadcast->setStatus($result[0]->status_id);
               $messageBroadcast->setStatusDetail($result[0]->status_detail);
      	       $messageBroadcast->setDestinationUserId($result[0]->destination_uid);
               $messageBroadcast->setDestinationPin($result[0]->destination_pin);
      	       $messageBroadcast->setMessageTypeId($result[0]->message_type_id);
      	       $messageBroadcast->setSourceNumber($result[0]->source_number);
               $messageBroadcast->setSourcePin($result[0]->source_pin);
               $messageBroadcast->setGroupMemberPin($result[0]->group_member_pin);
               
            }
            else{
               $messageBroadcast=null;
            }

            $db->closeConnection();
            
           return $messageBroadcast;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
    
    
    public static function updateMessageStatusById($messageBroadCast,$newStatus,$statusDetail){

            try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
              //update new user balance
                $currentTimeStamp= time();
                $data = array(
                'status_id'      => $newStatus,
                'status_detail'      => $statusDetail,
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = "id =".$messageBroadCast->getMessageId();
                $where[] = "broadcast_id =1";
                $db->update('message_broadcast', $data, $where);
    
                $db->closeConnection();
                
               return $messageBroadCast;
               
               
            } catch (Zend_Db_Adapter_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            } catch (Zend_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
             
            }catch (Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
            }
      
    }

        public static function updateMessageStatus($messageId,$newStatus){

            try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                $result = true;

                if($newStatus < -1 || $newStatus > 4)
                  $result=false;
                if($newStatus==0)
                  $statusDetail="0 - Message DELIVERED";
                elseif ($newStatus==1) 
                   $statusDetail="1 - Message SUBMITED";
                

                $data = array(
                'status_id'      => $newStatus,
                'status_detail'      => $statusDetail,
                'status_timestamp'      => time()
                 );
                
                $where[] = "id =".$messageId;
                $where[] = "broadcast_id =1";
                $db->update('message_broadcast', $data, $where);
    
                $db->closeConnection();
                
               return $result;
               
               
            } catch (Zend_Db_Adapter_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            } catch (Zend_Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
             
            }catch (Exception $e) {
                   $db->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
            }
      
    }
    
        /*getSmsStatus
        Return the status of the message_broadcast with $smsId
        */  
	public static function getSmsStatus($smsId)  
	{
	    $newStatus=0;
	    $errorCode=0;
        try {
                
                //get message
                $messageBroadcast=IMessageHelper::getIMessageBroadcastById($smsId);
                
                if ($messageBroadcast==null)
                    throw new Exception("Error not valid or not existing messageID",Constants::ERROR_RESOURCE_NOT_FOUND);
                 
                $response=$errorCode.",".$messageBroadcast->getStatus().",".$messageBroadcast->getStatusTimeStamp().",".$messageBroadcast->getMessageId();
                
		$db->closeConnection();   
                return $response;
            
            }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
           
	}
    //Returns all IM(Pooling or c2dm) pending messages(status not DELIVERED) from a user
    
    public function setPendingMessagesToDelivered($user,$currentTimeStamp){
	 try {
	    $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
	    
		//set the messages to delivered
                $data = array(
                'status_id'      => Constants::SMS_DELIVERED,
                'status_detail'      => "0 - Message Delivered",
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = 'status_id!='.Constants::SMS_DELIVERED.' and destination_uid = '.$user->getId().' and message_type_id!=0 and broadcast_id=1';
                $db->update('message_broadcast', $data, $where);
		
		$db->closeConnection();
		return true;
	 }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
    }
    
    public function getPendingMessages($user,$format)
    {
      require_once 'Objects/representation/IMessageBroadcastRepresentation.php';
	       $format=strtolower($format);
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
	          $result = $db->fetchAll('SELECT * FROM message_broadcast mes WHERE mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 order by mes.id desc', array(Constants::SMS_DELIVERED,$user->getId()));
            $total_items=count($result);
	     
             if ($total_items > 0)
             {
          		//Set Messages to delivered
          		self::setPendingMessagesToDelivered($user,time());
          		
          		if($format=="array"){
            		$messagesArray= array();
            		array_push($messagesArray,"OK");
            		array_push($messagesArray,$total_items);
          	   }
          		 //Instant Message optimized format
          		 else if ($format=="im"){
          		    $messagesArray="OK,".$total_items;
      		    
      		      }
		 
                foreach($result as $messageBroadcast){
		     
                    $nextMessage = new IMessageBroadcastRepresentation();
            		     //$nextMessage->setMessageId($messageBroadcast->id); Necesario?
            		     $nextMessage->fromUserId=$messageBroadcast["source_pin"];
            		     $nextMessage->fromPhone=$messageBroadcast["source_number"];
            		     $nextMessage->toUserId=$messageBroadcast["destination_pin"];
            		     $nextMessage->toPhone=$messageBroadcast["destination_number"];
            		     $nextMessage->status=$messageBroadcast["status_id"];
            		     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast["text"],$user->getName())));
            		     $nextMessage->text=utf8_encode($messageBroadcast["text"]);
            		     $nextMessage->timestamp=$messageBroadcast["status_timestamp"];//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast["group_member_pin"];
                     //add Event to destinationUser
                     UserEventHelper::addEventToUser($messageBroadcast["uid"],$messageBroadcast["source_pin"],Constants::EVENT_MESSAGES_STATUS_CHANGED);

            		     if($format=="array"){
            			       array_push($messagesArray,$nextMessage);
            		      }
            		      else if ($format=="im"){
            		    
            		        $messagesArray.=",".(empty($nextMessage->fromPhone)?"null":$nextMessage->fromPhone).",".
                  			$nextMessage->toUserId.",".
                  			$nextMessage->fromUserId.",".
                        (empty($nextMessage->toPhone)?"null":$nextMessage->toPhone).",".
                  			$nextMessage->timestamp.",".
                        $nextMessage->text.",".
                        (empty($nextMessage->groupMemberPin)?"0":"1").",".
                        (empty($nextMessage->groupMemberPin)?"null":$nextMessage->groupMemberPin).",".
                  			"9&c3";
                        //                       
                        //
            			
            		       }
               }
		
            }
            else{
               $messagesArray=null;
            }  

            $db->closeConnection();
            
	    
	    
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }

    /*******/
	
    public function getPendingMessagesNew($user,$format)
    {
        require_once 'Objects/representation/IMessageBroadcastRepresentation.php';
         $format=strtolower($format);
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $result = $db->fetchAll('SELECT * FROM message_broadcast mes WHERE mes.broadcast_id=1 and mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 order by mes.id desc', array(Constants::SMS_DELIVERED,$user->getId()));
            $total_items=count($result);
       
             if ($total_items > 0)
             {
              //Set Messages to delivered
              self::setPendingMessagesToDelivered($user,time());
              
              if($format=="array"){
                $messagesArray= array();
                array_push($messagesArray,"OK");
                array_push($messagesArray,$total_items);
               }
               //Instant Message optimized format
               else if ($format=="im"){
                  $messagesArray="OK,".$total_items;
              
                }
     
                foreach($result as $messageBroadcast){
         
                    $nextMessage = new IMessageBroadcastRepresentation();
                     //$nextMessage->setMessageId($messageBroadcast->id); Necesario?
                     $nextMessage->fromUserId=$messageBroadcast["source_pin"];
                     $nextMessage->fromPhone=$messageBroadcast["source_number"];
                     $nextMessage->toUserId=$messageBroadcast["destination_pin"];
                     $nextMessage->toPhone=$messageBroadcast["destination_number"];
                     $nextMessage->status=$messageBroadcast["status_id"];
                     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast["text"],$user->getName())));
                     $nextMessage->text=utf8_encode($messageBroadcast["text"]);
                     $nextMessage->timestamp=$messageBroadcast["status_timestamp"];//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast["group_member_pin"];
                     $nextMessage->messageTypeId=$messageBroadcast["message_type_id"];
                     //add Event to destinationUser
                     UserEventHelper::addEventToUser($messageBroadcast["uid"],$messageBroadcast["source_pin"],Constants::EVENT_MESSAGES_STATUS_CHANGED);

                     if($format=="array"){
                         array_push($messagesArray,$nextMessage);
                      }
                      else if ($format=="im"){
                        $fullFilePath=APPLICATION_PATH."/../tmp/".$nextMessage->text;

                        $messagesArray.=",".(empty($nextMessage->fromPhone)?"null":$nextMessage->fromPhone).",".
                        $nextMessage->toUserId.",".
                        $nextMessage->fromUserId.",".
                        (empty($nextMessage->toPhone)?"null":$nextMessage->toPhone).",".
                        $nextMessage->timestamp.",".
                        $nextMessage->text.",".
                        ($nextMessage->messageTypeId < 3 ?"0":filesize($fullFilePath)).",".
                        (empty($nextMessage->groupMemberPin)?"0":"1").",".
                        (empty($nextMessage->groupMemberPin)?"null":$nextMessage->groupMemberPin).",".
                        $nextMessage->messageTypeId.",".
                        "9&c3";
                        //                       
                        //
                  
                       }
               }
    
            }
            else{
               $messagesArray=null;
            }  

            $db->closeConnection();
            
      
      
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }

     public function getPendingMessagesForDevice($user,$macAddress,$format)
    {require_once 'Objects/representation/IMessageBroadcastRepresentation.php';
        
         $format=strtolower($format);
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $result = $db->fetchAll('SELECT * FROM message_broadcast mes WHERE mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 and mes.mac_address=? order by mes.id desc', array(Constants::SMS_DELIVERED,$user->getId(),$macAddress));
            $total_items=count($result);
       
             if ($total_items > 0)
             {
              //Set Messages to delivered
              //self::setPendingMessagesToDelivered($user,time());
              
              if($format=="array"){
                $messagesArray= array();
                array_push($messagesArray,"OK");
                array_push($messagesArray,$total_items);
               }
               //Instant Message optimized format
               else if ($format=="im"){
                  $messagesArray="OK,".$total_items;
              
                }
     
                foreach($result as $messageBroadcast){
         
                    $nextMessage = new IMessageBroadcastRepresentation();
                     //$nextMessage->setMessageId($messageBroadcast->id); Necesario?
                     $nextMessage->fromUserId=$messageBroadcast["source_pin"];
                     $nextMessage->fromPhone=$messageBroadcast["source_number"];
                     $nextMessage->toUserId=$messageBroadcast["destination_pin"];
                     $nextMessage->toPhone=$messageBroadcast["destination_number"];
                     $nextMessage->status=$messageBroadcast["status_id"];
                     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast["text"],$user->getName())));
                     $nextMessage->text=utf8_encode($messageBroadcast["text"]);
                     $nextMessage->timestamp=$messageBroadcast["status_timestamp"];//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast["group_member_pin"];
                     $nextMessage->messageTypeId=$messageBroadcast["message_type_id"];
                     //add Event to destinationUser
                     UserEventHelper::addEventToUser($messageBroadcast["uid"],$messageBroadcast["source_pin"],Constants::EVENT_MESSAGES_STATUS_CHANGED);

                     if($format=="array"){
                         array_push($messagesArray,$nextMessage);
                      }
                      else if ($format=="im"){
                        $fullFilePath=APPLICATION_PATH."/../tmp/".$nextMessage->text;

                        $messagesArray.=",".(empty($nextMessage->fromPhone)?"null":$nextMessage->fromPhone).",".
                        $nextMessage->toUserId.",".
                        $nextMessage->fromUserId.",".
                        (empty($nextMessage->toPhone)?"null":$nextMessage->toPhone).",".
                        $nextMessage->timestamp.",".
                        $nextMessage->text.",".
                        ($nextMessage->messageTypeId < 3 ?"0":filesize($fullFilePath)).",".
                        (empty($nextMessage->groupMemberPin)?"0":"1").",".
                        (empty($nextMessage->groupMemberPin)?"null":$nextMessage->groupMemberPin).",".
                        $nextMessage->messageTypeId.",".
                        "9&c3";
                        //                       
                        //
                  
                       }
                       self::setPendingMessageToDelivered($messageBroadcast["id"],$messageBroadcast["broadcast_id"],time());
                       self::setPendingMessageToDelivered($messageBroadcast["id"],1,time());
               }
    
            }
            else{
               $messagesArray=null;
            }  

            $db->closeConnection();
            
      
      
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }


  public function setPendingMessageToDelivered($messageId,$deviceId,$currentTimeStamp){
   try {
      $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
      
    //set the messages to delivered
                $data = array(
                'status_id'      => Constants::SMS_DELIVERED,
                'status_detail'      => "0 - Message Delivered",
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = '(status_id!='.Constants::SMS_DELIVERED.' and id = '.$messageId.' and broadcast_id='.$deviceId.' and message_type_id!=0)';
                $db->update('message_broadcast', $data, $where);
    
    $db->closeConnection();
    return true;
   }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
    }

    public static function getMessagesStatus($messagesIdListArray)
    {

       try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

             $query="select status_id from message_broadcast where (broadcast_id=1 and id=";

              for ($i = 0; $i < count($messagesIdListArray); $i++) {
                   if($i==0)
                      $query.=$messagesIdListArray[$i].")";
                   else
                      $query.=" or  (broadcast_id=1 and id=".$messagesIdListArray[$i].")";

              }
           
             $query.=" LIMIT ".count($messagesIdListArray);

            $result = $dbConn->fetchAll($query);

            $total_items=count($result);
            if ($total_items > 0)
            { 
               $messagesArray="OK,".$total_items.",";
              foreach($result as $message){
                          $messagesArray.=(is_null($message->status_id)?"null":$message->status_id)."9&c3";

              }
            }
            else{
              $messagesArray="OK,".$total_items;
            }
            
            $dbConn->closeConnection();
            
           return $messagesArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

    public static function sendSpooraMessage($user,$text)
    {
      //Send IM
            require_once 'Helper/UserEventHelper.php';
            //Max tokens reached
            $spooraUser = UserHelper::getUserById(-1);   
            self::sendIMessage($spooraUser,$text,$user->getId(),null,$user->getPin(),2,null,Constants::SMS_SUBMITED);
            //add Event to destinationUser
            UserEventHelper::addEventToUser($user->getId(),$user->getPin(),Constants::SMS_SUBMITED);
    
    }


    public static function sendToAllUsers($text)
    {
      require_once 'Helper/UserEventHelper2.php';
      require_once 'Helper/UserHelper.php';
      require_once 'Helper/PushNotificationsHelper.php';

        try {
            $text=str_replace(",",";",substr($text,0,Constants::MAX_IM_TEXT_SIZE));
            $return="OK";

            $spooraUser = UserHelper::getUserById(-1);   

            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT us.uid,us.pin,ud.id as device_id,ud.mac_address,ud.push_id FROM user_stats us, user_device ud where us.uid=ud.uid');
            
               $total_items=count($result);
                $idTemp = 10; 
                $registrationIDs = array();

                if ($total_items > 0)
                {       // Storing message to user device on DB
                        foreach($result as $userDevice){
                              if(!empty($userDevice->push_id))
                                 array_push($registrationIDs,$userDevice->push_id);
                          }

                    $messagesArray= ",".$idTemp.",spoora,".
                    $result[0]->pin.",". //destination pin
                    $spooraUser->getPin().",".//source pin
                    "null,".
                    time().",".
                    $text.",".
                    "0,".//size if media
                    "0,".//group member
                    "null,".//group pin
                    "2,". //message type = 2 (text)
                    "0,". //is group
                     "null,".//alias
                     "9&c3";

                    //1000 registrationID's max per GCM request
                     $gcmRegIDsChunks = array_chunk($registrationIDs,500);
  
                      foreach($gcmRegIDsChunks as $gcmRegIdChunk){
       
                             $message = "OK,".Constants::EVENT_PUSH_MESSAGES_PENDINGS.",1".$messagesArray;
                            //Send push to all Group devices directly with only one request
                             $result2=PushNotificationsHelper::sendGCM($spooraUser->getId(),$spooraUser->getPin(),$gcmRegIdChunk,$message);
                         
                         
                             $message2 = "OK,1,".Constants::EVENT_MESSAGES_PENDINGS;
                             $result3=PushNotificationsHelper::sendGCM($spooraUser->getId(),$spooraUser->getPin(),$gcmRegIdChunk,$message2);
                     }
                }

            
           return $return;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getLastMessagesFromGroup($user,$groupId,$offset)
    {
        
        
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
           
            $result = $db->fetchAll('SELECT id,broadcast_id,message_broadcast.uid,source_pin,source_number,alias,destination_pin,destination_number,destination_uid,status_id,text,status_timestamp,group_member_pin,message_type_id FROM message_broadcast,opengroup_user WHERE (opengroup_user.group_id=? and opengroup_user.uid=message_broadcast.uid) and destination_uid = ? order by id desc limit ? offset ?', array($groupId,$groupId,Constants::GET_LAST_MESSAGES_PAGE_SIZE,$offset*Constants::GET_LAST_MESSAGES_PAGE_SIZE));
            $total_items=count($result);
    
             if ($total_items > 0)
             {

                $messagesArray="OK,".$total_items;
            
                
     
                foreach($result as $messageBroadcast){
         
                        $fullFilePath=APPLICATION_PATH."/../tmp/".$messageBroadcast["text"];

                        $messagesArray.=",".(empty($messageBroadcast["source_number"])?"null":$messageBroadcast["source_number"]).",".
                        $user->getPin().",".
                        $messageBroadcast["source_pin"].",".
                        //(empty($user->getFullPhone())?"null":$user->getFullPhone()).",".
                        $messageBroadcast["status_timestamp"].",".
                        utf8_encode($messageBroadcast["text"]).",".
                        ($messageBroadcast["message_type_id"] < 3 ?"0":filesize($fullFilePath)).",".
                        (empty($messageBroadcast["group_member_pin"])?"0":"1").",".
                        (empty($messageBroadcast["group_member_pin"])?"null":$messageBroadcast["group_member_pin"]).",".
                        $messageBroadcast["message_type_id"].",".
                        "1,".
                        $messageBroadcast["alias"].",".
                        "9&c3";
                   
               }
    
            }
            else{
               $messagesArray=null;
            }  

            $db->closeConnection();
            
      
      
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }

}