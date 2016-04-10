<?php

require_once 'Objects/Constants.php';

class IMessageHelper2 {
    
    public function __construct($dbConn){
        $this->dbConn=$dbConn;

    }


     public  function createIMessageBroadcast(&$user,$text,$sourceNumber,$destinationUserId,$to,$destinationPin,$realCost,$userCost,$countryId,$externalId,$providerId,$messageTypeId,$groupMemberPin,$messageStatus)
    {//require_once 'Helper/UserLinkHelper.php';
     $statusName="SMS UNKNOWN";
        try {
            require_once 'Helper/UserLinkHelper.php';
           //Check if other user has not blocked sender user
            $linkStatus=UserLinkHelper::getLinkStatus($destinationUserId,$user->getId());
            
            if($linkStatus==1)
               $linkStatus2=UserLinkHelper::getLinkStatus($user->getId(),$destinationUserId);

            if($user->getId()==-1 || (!is_null($linkStatus) && $linkStatus==1 && $linkStatus2==1)){
               
                
                $this->dbConn->beginTransaction();

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
                
      
                    
                $this->dbConn->insert('message_broadcast', $data);
                
                $mid = $this->dbConn->lastInsertId();
               
                $this->dbConn->commit();

                  //send to User devices
                $data["id"]=$mid;
                self::sendMessageToUserDevices($data,$destinationUserId);
                
                
               return $mid;
           }
           else{
            //negative id means an error sending because userlink is broken
            return -1;
           } 
           
           
        }catch (Exception $e) {

          $this->dbConn->rollBack();
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
    }

    public  function sendMessageToUserDevices($data,$uid)
    {
     
        try {
      
                
                $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $this->dbConn->fetchAll('SELECT * FROM user_device WHERE uid = ?', $uid);
                
                $total_items=count($result);

                if ($total_items > 0)
                { 
                  foreach($result as $userDevice){
                          
                          $data["broadcast_id"]=$userDevice->id;
                          //$data["text"]="";
                          $data["mac_address"]=$userDevice->mac_address;
                          $this->dbConn->insert('message_broadcast', $data);
                          $this->dbConn->commit();
                          
                      }
                   
                }

            

           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

  public  function sendIMessage($user,$text,$destinationUserId,$destinationPhone,$destinationPin,$connectionTypeId,$groupMemberPin,$status)  
  {
     try {
            
    //TODO-encriptar text
      $text=substr($text,0,Constants::MAX_IM_TEXT_SIZE);

     $mid=self::createIMessageBroadcast($user,$text,$user->getFullPhone(),$destinationUserId,$destinationPhone,$destinationPin,
              0,0,-1,"IM",0,$connectionTypeId,$groupMemberPin,$status);
      return $mid;
   
    }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
  }


    public  function updateIMessageBroadcast($message){
            
                try {
                    
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
                    $this->dbConn->update('message_broadcast', $data, $where);

                   return true;
                   
                   
                } catch (Zend_Db_Adapter_Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                 // perhaps a failed login credential, or perhaps the RDBMS is not running
                } catch (Zend_Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
                
                }catch (Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
              
                  
                }
    }    
    
    public  function getIMessageBroadcastById($smsId){
        require_once 'Objects/IMessageBroadcast.php';
           try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM message_broadcast WHERE broadcast_id=1 and id = ?', $smsId);
            
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

            
           return $messageBroadcast;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
    
    
    public  function updateMessageStatusById($messageBroadCast,$newStatus,$statusDetail){

            try {

                
              //update new user balance
                $currentTimeStamp= time();
                $data = array(
                'status_id'      => $newStatus,
                'status_detail'      => $statusDetail,
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = "id =".$messageBroadCast->getMessageId();
                $where[] = "broadcast_id =1";
                $this->dbConn->update('message_broadcast', $data, $where);
    
                
                
               return $messageBroadCast;
               
               
            } catch (Zend_Db_Adapter_Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            } catch (Zend_Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
             
            }catch (Exception $e) {
                   $this->dbConn->rollBack();
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
            }
      
    }
    
        /*getSmsStatus
        Return the status of the message_broadcast with $smsId
        */  
	public  function getSmsStatus($smsId)  
	{
	    $newStatus=0;
	    $errorCode=0;
        try {
                
                //get message
                $messageBroadcast=self::getIMessageBroadcastById($smsId);
                
                if ($messageBroadcast==null)
                    throw new Exception("Error not valid or not existing messageID",Constants::ERROR_RESOURCE_NOT_FOUND);
                 
                $response=$errorCode.",".$messageBroadcast->getStatus().",".$messageBroadcast->getStatusTimeStamp().",".$messageBroadcast->getMessageId();
                
		   
                return $response;
            
            }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
           
	}
    //Returns all IM(Pooling or c2dm) pending messages(status not DELIVERED) from a user
    
    public function setPendingMessagesToDelivered($user,$currentTimeStamp){
	 try {
	    
		//set the messages to delivered
                $data = array(
                'status_id'      => Constants::SMS_DELIVERED,
                'status_detail'      => "0 - Message Delivered",
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = 'status_id!='.Constants::SMS_DELIVERED.' and destination_uid = '.$user->getId().' and message_type_id!=0 and broadcast_id=1';
                $this->dbConn->update('message_broadcast', $data, $where);
		
		
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
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
	          $result = $this->dbConn->fetchAll('SELECT * FROM message_broadcast mes WHERE mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 order by mes.id desc', array(Constants::SMS_DELIVERED,$user->getId()));
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
            		     $nextMessage->fromUserId=$messageBroadcast->source_pin;
            		     $nextMessage->fromPhone=$messageBroadcast->source_number;
            		     $nextMessage->toUserId=$messageBroadcast->destination_pin;
            		     $nextMessage->toPhone=$messageBroadcast->destination_number;
            		     $nextMessage->status=$messageBroadcast->status_id;
            		     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast["text"],$user->getName())));
            		     $nextMessage->text=utf8_encode($messageBroadcast->text);
            		     $nextMessage->timestamp=$messageBroadcast->status_timestamp;//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast->group_member_pin;
                     //add Event to destinationUser
                     UserEventHelper::addEventToUser($messageBroadcast->uid,$messageBroadcast->source_pin,Constants::EVENT_MESSAGES_STATUS_CHANGED);

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

	    
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }

    /*******/
	
    public function getPendingMessagesNew($user,$format)
    {require_once 'Objects/representation/IMessageBroadcastRepresentation.php';
    require_once 'Helper/UserEventHelper2.php';
        $userEventHelper = new UserEventHelper2($this->dbConn);
         $format=strtolower($format);
       try {

            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM message_broadcast mes WHERE mes.broadcast_id=1 and mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 order by mes.id desc', array(Constants::SMS_DELIVERED,$user->getId()));
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
                     $nextMessage->fromUserId=$messageBroadcast->source_pin;
                     $nextMessage->fromPhone=$messageBroadcast->source_number;
                     $nextMessage->toUserId=$messageBroadcast->destination_pin;
                     $nextMessage->toPhone=$messageBroadcast->destination_number;
                     $nextMessage->status=$messageBroadcast->status_id;
                     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast->text"],$user->getName())));
                     $nextMessage->text=utf8_encode($messageBroadcast->text);
                     $nextMessage->timestamp=$messageBroadcast->status_timestamp;//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast->group_member_pin;
                     $nextMessage->messageTypeId=$messageBroadcast->message_type_id;
                     //add Event to destinationUser
                     $userEventHelper->addEventToUser($messageBroadcast->uid,$messageBroadcast->source_pin,Constants::EVENT_MESSAGES_STATUS_CHANGED);

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

      
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
     //TODO: NOT USED ANY MORE REMOVE THIS METHOD OLD VERSION
     public function getPendingMessagesForDeviceOld($user,$macAddress,$format)
    {
     require_once 'Objects/representation/IMessageBroadcastRepresentation.php';
     require_once 'Helper/UserEventHelper2.php';
     
        $userEventHelper = new UserEventHelper2($this->dbConn);
        $format=strtolower($format);
        $messagesBufferAllowed="";
        $count=0;
       try {

            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT id,broadcast_id,uid,source_pin,source_number,destination_pin,destination_number,destination_uid,status_id,text,status_timestamp,group_member_pin,message_type_id FROM message_broadcast mes WHERE mes.status_id!=? and mes.destination_uid = ? and mes.message_type_id!=0 and mes.mac_address=? order by mes.id asc LIMIT 40', array(Constants::SMS_DELIVERED,$user->getId(),$macAddress));
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

                 foreach($result as $messageBroadcast){
                    
                    $nextMessage = new IMessageBroadcastRepresentation();
                     //$nextMessage->setMessageId($messageBroadcast->id); Necesario?
                     $nextMessage->fromUserId=$messageBroadcast->source_pin;
                     $nextMessage->fromPhone=$messageBroadcast->source_number;
                     $nextMessage->toUserId=$messageBroadcast->destination_pin;
                     $nextMessage->toPhone=$messageBroadcast->destination_number;
                     $nextMessage->status=$messageBroadcast->status_id;
                     //$nextMessage->setText(utf8_encode(self::decrypt($messageBroadcast["text"],$user->getName())));
                     $text=substr($text,0,2008);
                     $nextMessage->text=str_replace(",",";",substr($messageBroadcast->text,0,Constants::MAX_IM_TEXT_SIZE));
                     $nextMessage->timestamp=$messageBroadcast->status_timestamp;//Timestamp cambia cada vez que el sms cambia de estado o solo cuando se envia el sms?
                     $nextMessage->groupMemberPin=$messageBroadcast->group_member_pin;
                     $nextMessage->messageTypeId=$messageBroadcast->message_type_id;
                     //add Event to destinationUser
                     $userEventHelper->addEventToUser($messageBroadcast->uid,$messageBroadcast->source_pin,Constants::EVENT_MESSAGES_STATUS_CHANGED);

                     if($format=="array"){
                         array_push($messagesArray,$nextMessage);
                      }
                      else if ($format=="im"){

                            $fullFilePath=APPLICATION_PATH."/../tmp/".$nextMessage->text;
                            $messagesBufferAllowed=",".(empty($nextMessage->fromPhone)?"null":$nextMessage->fromPhone).",".
                            $nextMessage->toUserId.",".
                            $nextMessage->fromUserId.",".
                            (empty($nextMessage->toPhone)?"null":$nextMessage->toPhone).",".
                            $nextMessage->timestamp.",".
                            $nextMessage->text.",".
                            ($nextMessage->messageTypeId < 3 ?"0":filesize($fullFilePath)).",".
                            (empty($nextMessage->groupMemberPin)?"0":"1").",".
                            (empty($nextMessage->groupMemberPin)?"null":$nextMessage->groupMemberPin).",".
                            $nextMessage->messageTypeId.",".
                            "9&c3".$messagesBufferAllowed;
                             
                        //response_max_size -5 because (OK,message_number)
                       if(strlen($messagesBufferAllowed)< (Constants::RESPONSE_MAX_SIZE-5)){
                        $messagesArray=$messagesBufferAllowed;
                        $count++;
                        self::setPendingMessageToDelivered($messageBroadcast->id,$messageBroadcast->broadcast_id,time());
                        self::setPendingMessageToDelivered($messageBroadcast->id,1,time());
                       
                        }
                        else{
                          //still having messages to read but reached MAX BUFFER SIZE
                          //add pending messages Event to destinationUser
                         $userEventHelper::addEventToUser($messageBroadcast->destination_uid,$messageBroadcast->destination_pin,Constants::EVENT_MESSAGES_PENDINGS);
                          break;
                        }
                          
                  
                       }

               }
                //Instant Message optimized format
               if ($format=="im"){
                  $messagesArray=$count.$messagesArray;
              
                }
    
            }
            else{
               $messagesArray=$count=0;
            }  
      
           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }

    public function getPendingMessagesForDevice($user,$macid,$isActive,$format,$soundControl)
    {
        require_once 'Helper/UserEventHelper2.php';
        $userEventHelper = new UserEventHelper2($this->dbConn);
        $format=strtolower($format);
        $messagesBufferAllowed="";
        $count=0;
       try {


            $this->dbConn->beginTransaction();

            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT id,broadcast_id,uid,source_pin,source_number,destination_pin,destination_number,destination_uid,status_id,text,status_timestamp,group_member_pin,message_type_id FROM message_broadcast mes WHERE mes.status_id=? and mes.destination_uid = ? and mes.message_type_id!=0 and mes.broadcast_id=? LIMIT 40', array(Constants::SMS_SUBMITED,$user->getId(),$macid));
            $total_items=count($result);
       
             if ($total_items > 0)
             {

                 foreach($result as $messageBroadcast){
                    //add Event to destinationUser
                     $userEventHelper->addEventToUser($messageBroadcast->uid,$messageBroadcast->source_pin,Constants::EVENT_MESSAGES_STATUS_CHANGED);


                            $text=str_replace(",",";",substr($messageBroadcast->text,0,Constants::MAX_IM_TEXT_SIZE));

                            $fullFilePath=APPLICATION_PATH."/../tmp/".$text;
                            $messagesBufferAllowed=",".(empty($messageBroadcast->source_number)?"null":$messageBroadcast->source_number).",".
                            $messageBroadcast->destination_pin.",".
                            $messageBroadcast->source_pin.",".
                            (empty($messageBroadcast->destination_number)?"null":$messageBroadcast->destination_number).",".
                            $messageBroadcast->status_timestamp.",".
                            $text.",".
                            ($messageBroadcast->message_type_id < 3 ?"0":filesize($fullFilePath)).",".
                            (empty($messageBroadcast->group_member_pin)?"0":"1").",".
                            (empty($messageBroadcast->group_member_pin)?"null":$messageBroadcast->group_member_pin).",".
                            $messageBroadcast->message_type_id.",".
                            "9&c3".$messagesBufferAllowed;

                             
                        //response_max_size -5 because (OK,message_number)
                       if(strlen($messagesBufferAllowed)< (Constants::RESPONSE_MAX_SIZE-5)){
                        $messagesArray=$messagesBufferAllowed;
                        $count++;
                        self::setPendingMessageToDelivered($messageBroadcast->id,$macid,time());
                        self::setPendingMessageToDelivered($messageBroadcast->id,1,time());
                       
                        }
                        else{
                         
                          //still having messages to read but reached MAX BUFFER SIZE
                          //add pending messages Event to destinationUser
                         $userEventHelper->addEventToUser($messageBroadcast->destination_uid,$messageBroadcast->destination_pin,Constants::EVENT_MESSAGES_PENDINGS);
                          break;
                        }
                          
                  
                    }

             }  

             if (!$soundControl)
                  $messagesArray=$count.$messagesArray; 
              else
                  $messagesArray=$count.",".$isActive.$messagesArray;  


           return $messagesArray;
           
         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }


  public function setPendingMessageToDelivered($messageId,$deviceId,$currentTimeStamp){
   try {
         //$this->dbConn->beginTransaction();
    //set the messages to delivered
                $data = array(
                'status_id'      => Constants::SMS_DELIVERED,
                'status_detail'      => "0 - Message Delivered",
                'status_timestamp'      => $currentTimeStamp
                 );
                
                $where[] = '(status_id!='.Constants::SMS_DELIVERED.' and id = '.$messageId.' and broadcast_id='.$deviceId.' and message_type_id!=0)';
                $this->dbConn->update('message_broadcast', $data, $where);
               // $this->dbConn->commit();
    
    
    return true;
   }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
    }

    public  function getMessagesStatus($messagesIdListArray)
    {

       try {
        
            
           $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

             $query="select status_id from message_broadcast where (broadcast_id=1 and id=";

              for ($i = 0; $i < count($messagesIdListArray); $i++) {
                   if($i==0)
                      $query.=$messagesIdListArray[$i].")";
                   else
                      $query.=" or  (broadcast_id=1 and id=".$messagesIdListArray[$i].")";

              }
           
             $query.=" LIMIT ".count($messagesIdListArray);

            $result = $this->dbConn->fetchAll($query);

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
            
            
           return $messagesArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }
    
    
}
