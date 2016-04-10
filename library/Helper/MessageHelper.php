<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/MessageBroadcast.php';
require_once 'Objects/Constants.php';
require_once 'Router/ProviderRouter.php';
require_once 'SmsProviders/intelliSMS/IntelliSMSProvider.php';
require_once 'SmsProviders/RoutoTelecom/RoutoProvider.php';
class MessageHelper {
    
    
    public static function createMessageBroadcast(&$user,$text,$to,$realCost,$userCost,$countryId,$externalId,$providerId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->beginTransaction();
            
            //TODO - status_id definir constantes
            //TODO - Enable transactions
            //create broadcast
            $data = array(
            'broadcast_id'           => 1,
            'uid'                    => $user->getId(),
            'external_id'            => trim($externalId),
            'destination_number'     => $to,
            'country_id'             => $countryId,
            'text'                   => $text,
            'user_cost'              => $userCost,
            'real_cost'              => $realCost,
            'provider_id'            => $providerId,
            'status_id'              => Constants::SMS_AT_HOME,
            'status_detail'          => "-1 - Message at Home",
            'status_timestamp'       => time()
            );

            $db->insert('message_broadcast', $data);
            
            $mid = $db->lastInsertId();
           
            $db->commit();
            
            $db->closeConnection();
            
           return $mid;
           
           
        }catch (Exception $e) {

          $db->rollBack();
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
    }
    
    public static function updateMessageBroadcast($message){
            
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
    
    public static function getMessageBroadcastById($smsId){
        
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE id = ?', $smsId);
            
            if (count($result)== 1)
            {

               $messageBroadcast = new MessageBroadcast();
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
    
 
      public static function getMessageBroadcastByExternalId($externalId,$to){
        
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE external_id like ? and destination_number = ?', array($externalId."%",$to));
            
            if (count($result)== 1)
            { 
               $messageBroadcast = new MessageBroadcast();
               $messageBroadcast->setMessageId($result[0]->id);
               $messageBroadcast->setExternalId($result[0]->external_id);
               $messageBroadcast->setBroadcastId($result[0]->broadcast_id);
               $messageBroadcast->setStatusTimeStamp($result[0]->status_timestamp);
               $messageBroadcast->setStatus($result[0]->status_id);
               $messageBroadcast->setStatusDetail($result[0]->status_detail); 
               $messageBroadcast->setProviderId($result[0]->provider_id);                
            }
            else{
               $messageBroadcast=null;
            }
             
            $db->closeConnection();
            
	     //if we can not find sms on the n2manager DB, we read the n2websms DB
	    
	    if ($messageBroadcast==null)
	          $messageBroadcast = self::getMessageBroadcastByExternalId_n2websmsDB($externalId,$to);
	    
           return $messageBroadcast;
           
           

         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
    
    private function getMessageBroadcastByExternalId_n2websmsDB($externalId,$to){
	   try {
	    
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnection_n2websmsDB();
            
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE external_id like ? and destination_number = ?', array($externalId."%",$to));
            
            if (count($result)== 1)
            { 
             
               $messageBroadcast = new MessageBroadcast();
               $messageBroadcast->setMessageId($result[0]->id);
               $messageBroadcast->setExternalId($result[0]->external_id);
               $messageBroadcast->setBroadcastId($result[0]->broadcast_id);
               $messageBroadcast->setStatusTimeStamp($result[0]->status_timestamp);
               $messageBroadcast->setStatus($result[0]->status_id);
               $messageBroadcast->setStatusDetail($result[0]->status_detail); 
               $messageBroadcast->setProviderId($result[0]->provider_id); 
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
    
    public static function getMessageBroadcastByExternalIdOnly($externalId){
        
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE external_id like ?', array($externalId."%"));
            
            if (count($result)== 1)
            { 

               $messageBroadcast = new MessageBroadcast();
               $messageBroadcast->setMessageId($result[0]->id);
               $messageBroadcast->setExternalId($result[0]->external_id);
               $messageBroadcast->setBroadcastId($result[0]->broadcast_id);
               $messageBroadcast->setStatusTimeStamp($result[0]->status_timestamp);
               $messageBroadcast->setStatus($result[0]->status_id);
               $messageBroadcast->setStatusDetail($result[0]->status_detail);
	       $messageBroadcast->setDestinationNumber($result[0]->destination_number); 
               $messageBroadcast->setProviderId($result[0]->provider_id); 
            }
            else{
               $messageBroadcast=null;
            }

            $db->closeConnection();
            
	    //if we can not find sms on the n2manager DB, we read the n2websms DB
	    if($messageBroadcast==null)
	         $messageBroadcast = self::getMessageBroadcastByExternalIdOnly_n2websmsDB($externalId);
	    
           return $messageBroadcast;
           
           

         
        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
    
        public static function getMessageBroadcastByExternalIdOnly_n2websmsDB($externalId){
        
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnection_n2websmsDB();
            
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE external_id like ?', array($externalId."%"));
            
            if (count($result)== 1)
            { 
               $messageBroadcast = new MessageBroadcast();
               $messageBroadcast->setMessageId($result[0]->id);
               $messageBroadcast->setExternalId($result[0]->external_id);
               $messageBroadcast->setBroadcastId($result[0]->broadcast_id);
               $messageBroadcast->setStatusTimeStamp($result[0]->status_timestamp);
               $messageBroadcast->setStatus($result[0]->status_id);
               $messageBroadcast->setStatusDetail($result[0]->status_detail);
	       $messageBroadcast->setDestinationNumber($result[0]->destination_number); 
               $messageBroadcast->setProviderId($result[0]->provider_id); 
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
                
                $messageBroadCast->setStatus($newStatus);
                $messageBroadCast->setStatusTimeStamp($currentTimeStamp);
                
		//$messageBroadCast = self::updateMessageStatusById_n2websmsDB($messageBroadCast,$newStatus,$statusDetail);
		
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
    
     public static function updateMessageStatusById_n2websmsDB($messageBroadCast,$newStatus,$statusDetail){

            try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnection_n2websmsDB();
                
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
                
                $messageBroadCast->setStatus($newStatus);
                $messageBroadCast->setStatusTimeStamp($currentTimeStamp);
                
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
    
        /*getSmsStatus
        Return the status of the message_broadcast with $smsId
        */  
	public static function getSmsStatus($smsId)  
	{
	    $newStatus=0;
	    $errorCode=0;
        try {
                
                //get message
                $messageBroadcast=MessageHelper::getMessageBroadcastById($smsId);
                
                if ($messageBroadcast==null)
                    throw new Exception("Error not valid or not existing messageID",Constants::ERROR_RESOURCE_NOT_FOUND);
                 
                $response=$errorCode.",".$messageBroadcast->getStatus().",".$messageBroadcast->getStatusTimeStamp().",".$messageBroadcast->getMessageId();
                   
                return $response;
            
            }catch (Exception $e) {
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }
           
	}
        
    public static function updateMessageAfterSent($user,$route,$messageId,$destCountry,$externalId){
	$errorCode="0-OK";
	try{	    
	    //If group1 or sent to country in group 2 ==> balance-1
	    if($user->getGroupId()==Constants::GROUP_1 || $destCountry->getGroupId()==Constants::GROUP_2)
		$userCost=1;
	    else //If from group2 sent to country in group 1 ==> balance-2
	        $userCost=2;
		   
	    $user->setBalance($user->getBalance()-$userCost);
					
	    UserHelper::updateUser($user);
	    
	    $message = new MessageBroadcast();
	    $message->setMessageId($messageId);
	    $message->setProviderId($route->getProviderId());
		     //NOW SMS has been sent
	    $message->setStatus(Constants::SMS_SUBMITED);
	    $message->setStatusDetail(Constants::SMS_SUBMITED."-Message SUBMITED");
	    $message->setExternalId($externalId);
			
	    $message->setRealCost($route->getPrice());
	    $message->setUserCost($userCost);
			
	    MessageHelper::updateMessageBroadcast($message);
	    			
	    $response=$errorCode.",".$user->getBalance().",".$messageId;
	    
	 return $response;
        }catch(Exception $e){
            $db->rollBack();
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
            
        }
    }
    public function sendSMS($user,$from,$fromCountry,$to,$destinationCountry,$text,$utf8)
    {
      $writer = new Zend_Log_Writer_Stream('../private/logs/n2manager.messages.log');
      $logger = new Zend_Log($writer);
      $translate=Zend_Registry::get('Zend_Translate');
      
      try{
       // print_r($destinationCountry);
       $cryptText = MessageHelper::encrypt($text, $user->getId());
       $messageId=MessageHelper::createMessageBroadcast($user,$cryptText,$to,null,null,
								 $destinationCountry->getId(),"TEMPID-".microtime(),null);
	
	$userBalance= $user->getBalance();
	$providerRouter = new ProviderRouter($destinationCountry);
	$routes=$providerRouter->getRoutes();
		
		//Send SMS to Provider
		 foreach($routes as $route){
		     switch($route->getProviderId()){
			/*case 1: $provider= new Tm4bProvider();break;*/
			case 2: $provider= new RoutoProvider();break;
			case 3: $provider= new IntelliSMSProvider();break;
			/*case 4: $provider= new LleidaNetProvider();break;*/
			/*case 5: $provider= new Sms42TelecomProvider();break;*/
			case 6: $provider= new UsaBulkSMSProvider();break;
		     }
		    //response is externalId if OK
		    
		    if($utf8)
		        $text = self::utf8toucs2hex($text);
		    else
		        $text = utf8_decode($text);

                   $response=$provider->sendSms($from,$to,$text,$utf8);

		    if($response!="ERROR"){
		      //Update user balance, message info,externalId
		       //$response=MessageHelper::updateMessageAfterSent($user,$route,$messageId,$text,$response);
                        
		       break;
		      }
		    
		 }
	 	 
	 $logger->info(" - MessagesController > postAction:\n\t".
				       "externalId: ".$response."\n\t".
				       "provider: ".$route->getProviderId()."\n\t".
				       "messageId: ".$messageId."\n\t".
				       "user: ".$user->getId()."\n\t".
				       "from: ".$from."\n\t".
				       "to: ".$to."\n\t".
				       "toCountry: ".$destinationCountry->getName()."\n\t");
	return $response;

         }catch (Exception $e) {
		throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
                 
        }
		      
           
       
    }
    
       public static function isSmsSecretKeySentToday($to){
        
           try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $return=false;
	    
	    $today=mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
	    $tomorrow=mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
            
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchAll('SELECT * FROM message_broadcast WHERE destination_number = ? and uid=-1 and status_timestamp > ? and status_timestamp < ? ', array($to,$today,$tomorrow));
            
            if (count($result) >= 1)
            { 
               $return= true;
               
            }

            $db->closeConnection();
            
	    
           return $return;
           

        }catch (Exception $e) {
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
        }
    }
    
     //Crypt and Decrypt messages
    
    function encrypt($string, $key) {
	$result = '';
	for($i=0; $i<strlen($string); $i++) {
	  $char = substr($string, $i, 1);
	  $keychar = substr($key, ($i % strlen($key))-1, 1);
	  $char = chr(ord($char)+ord($keychar));
	  $result.=$char;
	}
      
	return base64_encode($result);
      }

    function decrypt($string, $key) {
      $result = '';
      $string = base64_decode($string);
    
      for($i=0; $i<strlen($string); $i++) {
	$char = substr($string, $i, 1);
	$keychar = substr($key, ($i % strlen($key))-1, 1);
	$char = chr(ord($char)-ord($keychar));
	$result.=$char;
      }
    
      return $result;
    }
    
    //Fuctions to help that is not latin text
      function uniord($u) {
	// i just copied this function fron the php.net comments, but it should work fine!
	$k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
	$k1 = ord(substr($k, 0, 1));
	$k2 = ord(substr($k, 1, 1));
	return $k2 * 256 + $k1;
      }
      
      function is_not_latin($str) {
	if(mb_detect_encoding($str) !== 'UTF-8') {
	    $str = mb_convert_encoding($str,mb_detect_encoding($str),'UTF-8');
	}
    
	/*
	$str = str_split($str); <- this function is not mb safe, it splits by bytes, not characters. we cannot use it
	$str = preg_split('//u',$str); <- this function woulrd probably work fine but there was a bug reported in some php version so it pslits by bytes and not chars as well
	*/
	preg_match_all('/.|\n/u', $str, $matches);
	$chars = $matches[0];
	$arabic_count = 0;
	$latin_count = 0;
	$total_count = 0;
	foreach($chars as $char) {
	    //$pos = ord($char); we cant use that, its not binary safe 
	    $pos = self::uniord($char);
	    //echo $char ." --> ".$pos.PHP_EOL;
    
	    if($pos >= 256/*1536 && $pos <= 1791*/) {
		$arabic_count++;
	    } else if($pos > 123 && $pos < 123) {
		$latin_count++;
	    }
	    $total_count++;
	}
	if($total_count > 0){
	    if(($arabic_count/$total_count) > 0.6) {
		// 60% arabic chars, its probably arabic
		return true;
	    }
	}
	return false;
    }
    
    //Unicode helper functions

    function utf8toucs2hex($utf8)
    {
	    $utf8_hex = bin2hex( $utf8 );
	    return self::utf8hextoucs2hex($utf8_hex);
    }
    
    function utf8hextoucs2hex($str)
    {
	   $ucs2 = "";
    
	   for ($i=0;$i<strlen($str);$i+=2)
	   {
		    $char1hex = $str[$i].$str[$i+1]; 
		   
		    $char1dec = hexdec($char1hex);
		    if ( $char1dec < 128)
		    {
			    $results = $char1hex;
		    }
		    else if ( $char1dec < 224 )
		    {
			    $char2hex = $str[$i+2].$str[$i+3]; 
			    $results = dechex( ((hexdec($char1hex)-192)*64) + (hexdec($char2hex)-128) );
			    $i+=2;
		    }
		    else if ( $char1dec < 240 )
		    {
			    $char2hex = $str[$i+2].$str[$i+3]; 
			    $char3hex = $str[$i+4].$str[$i+5]; 
			    $results = dechex( ((hexdec($char1hex)-224)*4096) + ((hexdec($char2hex)-128)*64) + (hexdec($char3hex)-128) );
			    $i+=4;
		    }
		    else
		    {
			    //Not supported: UCS-2 only
			    $i+=6;
		    }
    
		    while ( strlen($results) < 4 )
		    {
			    $results = '0' . $results;
		    }
    
		    $ucs2 .= $results;
	    }
    
	    return strtoupper($ucs2);
    }
    
    
    
}
