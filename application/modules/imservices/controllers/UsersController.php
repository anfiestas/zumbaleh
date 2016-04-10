<?php
require_once 'Helper/UserHelper.php';
require_once 'Objects/Constants.php';
require_once 'Helper/UserStatsHelper.php';

class Imservices_UsersController extends Zend_Controller_Action
{
	
   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->userTimeStampPost = $this->_getParam('user_timestamp');
		$this->connectionType    = $this->_getParam('connection_type');

    }
    
    //TODO
    public function registerAction()
    {     	require_once 'Helper/BannedDeviceHelper.php';
       try{
       	   $currentUser=null;
	 	   $userMACAddressPost = $this->_getParam('mac_address');
	 	   $sessionType = $this->_getParam('session_type');
	 	   $passwordPost 		= $this->_getParam('password');
       	   $mailPost 			= $this->_getParam('mail');

       	   $deviceIdPost 		= $this->_getParam('device_id');
       	   $serialIDPost 		= $this->_getParam('serial_id');

	 	   $uid=null;
	 	   $response="";

	 	   if($deviceIdPost==null && $serialIDPost==null && $userMACAddressPost==null){
	 	   	 	$this->getResponse()->appendBody("ERROR,UDID is null");
	        	throw new Exception("ERROR not unique device_id",Constants::ERROR_BAD_REQUEST);
	 	   }


       	if($deviceIdPost==null || $deviceIdPost=="null" || $deviceIdPost=="000000000000000"
                    ||$deviceIdPost=="004999010640000"
                    || $deviceIdPost=="358817001013700"
                    || $deviceIdPost=="Unavailable"
                    || $deviceIdPost==""
                    || mb_strtolower($deviceIdPost)=="unknown"){
       		$deviceIdPost=null;
       }
          if($serialIDPost==null ||$serialIDPost=="null"  || $serialIDPost=="000000000000000"
                    ||$serialIDPost=="004999010640000"
                    || $serialIDPost=="358817001013700"
                    ||$serialIDPost=="Unknown"){
       		$serialIDPost=null;
       }

       //If both serial and device are null then we use mac:
       if($deviceIdPost==null && $serialIDPost==null){
               $UDID = $userMACAddressPost;
         }
        else{
           //Build UDID unique device id:
      		 $UDID = (is_null($deviceIdPost)?"$":$deviceIdPost)."_".(is_null($serialIDPost)?"$":$serialIDPost);
        }
      
        //Si device baneado error 403
       	if(BannedDeviceHelper::isBanned($deviceIdPost,$serialIDPost)==TRUE)       		  
       		  throw new Exception("Error user Unauthorized",Constants::ERROR_USER_FORBIDDEN);

	 	   if($sessionType==null)
	 	   	 	$sessionType=Constants::SESSION_TYPE_DEVICE;

	 	   	 	if($mailPost!=null){

	 	  		 try {
		       			 $userExist=UserHelper::getUserByMail($mailPost);
		       		 } catch (Exception $e) {
		       		 			if($e->getMessage()!="Not existing mail"){
			       		 	 		$this->getResponse()->appendBody("ERROR,61,".$e->getMessage());
			       		 	 		 throw $e;
		       		 	 		 }
		       		 }

		       		 if ($userExist!=null){
		       		 	  $this->getResponse()->appendBody("ERROR,61,user mail already exist");
			       		  throw new Exception("Error duplicate mail",Constants::ERROR_RESOURCE_NOT_FOUND);
		       		 }

				}

	      	 	$uid=UserHelper::createUser(null,null,0,-1,0,false,null,null,null,null,null,null,null);
	      	 	 $currentUser = UserHelper::getUserById($uid);
	      	 	
	      	 	 UserStatsHelper::createStats($uid,$currentUser->getPin(),null);

	      	 	 //Add user device with serial and device_id
	      	 	 UserHelper::registerDeviceToUser($uid,$currentUser->getPin(),$UDID,$serialIDPostVal,$deviceIdPostVal,$sessionType);
				    


	       if($currentUser!=null){
	       	
		       	if($mailPost!=null)
		     		$currentUser->setMail($mailPost);
		     		 
		   	   if($passwordPost!=null)
		    	    UserHelper::setUserPassword($currentUser,$passwordPost);

			 UserHelper::updateUserTimeStamp($currentUser, $this->userTimeStampPost,$this->connectionType);

			 /********Send Welcome message********/
			 	 require_once 'Helper/UserEventHelper.php';
			     require_once 'Helper/IMessageHelper.php';

		    	  $spooraUser = UserHelper::getUserById(-1);   

		    	  $textPost="¡Bienvenido a spoora! Bienvenido a la mensajería instantánea solidaria. Recuerda que al validar tu número de teléfono recibirás tus primeros 100 spooris para realizar tu primera donación.";
  				  $textPost2= "-Nuevo programa Embajadores SPOORA! Si al validar su número de teléfono te señalan como embajador introduciendo tu código PIN, tanto tú como él ganareis 500 spooris al momento (a partir de la décima referencia la cantidad será de 300 spooris) - https://www.myspoora.com/blog/programa-embajadores-spoora";

		    	//Send message promo
		    	$mid = IMessageHelper::sendIMessage($spooraUser,$textPost,$currentUser->getId(),null,$currentUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
				$mid = IMessageHelper::sendIMessage($spooraUser,$textPost2,$currentUser->getId(),null,$currentUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
				//add Event to destinationUser
				UserEventHelper::addEventToUser($currentUser->getId(),$currentUser->getPin(),Constants::SMS_SUBMITED); 

			 /***********/	
	         $response="OK,".$currentUser->getPin().",".$currentUser->getImSecretKey();
	       }
	    
	    
	      $this->getResponse()->appendBody($response);
	
	
	 
	  }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());


		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    /*
    This method send´s authentication SMS to entered phone
    */
    public function phoneauthAction()
    {	require_once 'Helper/TransactionHelper.php';

       $writer = new Zend_Log_Writer_Stream('../private/logs/phone_auth.log');
	   $logger = new Zend_Log($writer);



       try{

       	   $userIdPost      = $this->_getParam('user_id');
       	   $countryIdPost   = $this->_getParam('country_id');
	 	   $fullPhonePost   = $this->_getParam('fullphone');
	 	   $ambassador_pin  = $this->_getParam('pin_emb');
	 	   $response="OK";

	 	   $logger->info("https://www.myspoora.com/imservices/users/phone_auth?user_id=".$userIdPost."&country_id=".$countryIdPost."&fullphone=".$fullPhonePost ."pin_emb=". $ambassador_pin);

	 	  //Verify user
         $user = UserHelper::getUserByPin($userIdPost);

	  if($user!=null){
	  	$salt="dsdsd35s";
	  	//TODO filter by mac address
		 	 /*if(MessageHelper::isSmsSecretKeySentToday($fullPhonePost)){
			     throw new Exception("One secret key has been sent yet today ",Constants::ERROR_BAD_REQUEST);
			 }
			 else{*/
			    //FIX for French number, in case user inserts 0 after 
			    //prefix in international format (ERROR 0619 43 33 22)
			 	if($countryIdPost==73 && $fullPhonePost[4]==0){
			 		$fullPhonePost="0033".substr($fullPhonePost, 5);
			 	}
			 	
			    //Venezuela
			 	if($countryIdPost==230 && $fullPhonePost[4]==0){
			 		$fullPhonePost="0058".substr($fullPhonePost, 5);
			 	}
			 	//Chile
			 	if($countryIdPost==43 && $fullPhonePost[4]!=9){
			 		$fullPhonePost="00569".substr($fullPhonePost, 4);
			 	}
			 	
			 	 //send SecretKey by sms
			 	
			 	$user->setCountryId($countryIdPost);

			    $messageKey=$salt.time().$user->getFullPhone();
				$user->setSecretKey(md5($messageKey),false);

				if($user->getFullPhone()=="0034608072034")
					throw new Exception("Error user spam",Constants::ERROR_RESOURCE_NOT_FOUND);

				$user->setTempPhone($fullPhonePost);
				UserHelper::updateUser($user);

				//dont´t save phone and country until validation by sms
				$user->setFullPhone($fullPhonePost);

			  	TransactionHelper::sendSmsSecretKey($user);

			 	//$user->setCountryId(null);
				
			    //UserHelper::updateUser($user);

				//If embassador program
			if($ambassador_pin!=null && $ambassador_pin!=$userIdPost){
					
		
					$ambassadorUser = UserHelper::getUserByPin($ambassador_pin);
					if($ambassadorUser!=null){
							require_once 'Helper/UserAmbassadorHelper.php';
							UserAmbassadorHelper::addAmbassador($ambassadorUser->getId(),$user->getId());
					}

			    
			    }
			//}
   	  }
	    
	    
	      $this->getResponse()->appendBody($response);
	
	
	 
	  }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());


		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

    public function phoneconfAction()
    {require_once 'Helper/CountryHelper.php';
       try{
       	   $userIdPost        = $this->_getParam('user_id');
       	   $zumbalehIdPost    = $this->_getParam('zumbaleh_id');
       	   $fullPhonePost     = $this->_getParam('fullphone');
       	   $response="ERROR";
	 	 

	 	   if ($userIdPost==null){
	 	   		 $this->getResponse()->appendBody("ERROR,user is empty");
	        	throw new Exception("Error user is empty",Constants::ERROR_RESOURCE_NOT_FOUND);
	        }

	 	  //Verify user
         $newUser = UserHelper::getUserByPin($userIdPost);
	 
		  if($newUser!=null){

		  	if($newUser->getSecretKey()!=$zumbalehIdPost){
		  		 $this->getResponse()->appendBody("ERROR,zumbale id is not valid");
	        	throw new Exception("Error zumbaleh id is not valid",Constants::ERROR_AUTHENTICATION_FAILED);
	        }

	        $country = CountryHelper::getCountry($newUser->getCountryId());
			
		
			//Constants::IDD_PREFIX.
			$noPrefixShortPhone=substr($fullPhonePost,strlen(Constants::IDD_PREFIX.$country->getCountryCode()),strlen($fullPhonePost));
			
			//$noPrefixShortPhone=PhoneNumberHelper::removeInternationalPrefix($fullPhonePost,$country);


		    //If phone was used before by another PIN
		     $oldUser=UserHelper::getUserByFullPhoneOrShortPhone2($fullPhonePost);

		    //update user with old user id and info
		     if($oldUser!=null && $oldUser->pin!=$newUser->getPin()){
		     	$currentUserId=$newUser->getId();
		     	//backup pin
		     	UserHelper::addPinToHistory($oldUser->id,$oldUser->pin);
		     	//Delete old user from users table
		     	UserHelper::deleteUser($oldUser->id);

			    //UserHelper::updateUser($newUser);
			    $newUser->setId($oldUser->id);
			    //$newUser->setMail($oldUser->mail);
			    //$newUser->setBalance($oldUser->balance);
			    //$newUser->setName($oldUser->name);
			    //$newUser->setTokens($oldUser->tokens);
			    //$newUser->setTokensProgram($oldUser->tokens_program);
			    //Update user info and ID

			    UserHelper::updateUserAndUserId($currentUserId,$newUser);
			    //Update and deletes old user devices
			    UserHelper::updateAndCleanUserDevices($currentUserId,$newUser);

			    //TODO: update links of new user on dataBase
			    //Send PIN CHANGED event
		    }

	   	  
	   	  }
	   	  else
	      { $this->getResponse()->appendBody("ERROR,user does not exist");
			throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);

	      }

		  require_once 'Helper/UserAmbassadorHelper.php';
		   $result=UserAmbassadorHelper::validateAmbassadorAndSharePoints($newUser->getId(),$newUser->getTokens(),$userIdPost);
	  	  //envío mensajes si OK
	  	  if($result > 0){
	  	  		$spooraUser = UserHelper::getUserById(-1);   
	  	  		  $country = CountryHelper::getCountry($newUser->getCountryId());

		    	  $textPost1="Congratulations! You've got ".$result." extra spooris thanks to your ambassador.";
		    	  
		    	  if($country->getId()==196)
		    	  	$textPost1="Enhorabuena! Has obtenido ".$result." spooris por participar en el programa Embajadores SPOORA. Te recordamos que puedes seguir ganando spooris
si otros usuarios nuevos te referencian como Embajador SPOORA durante el proceso de validación del número de teléfono.";
		    	  
		    	  else if($country->getId()==73)
		    	  	$textPost1="Bravo, vous venez de ganer ".$result." spooris extra grace à votre participation au programme ambassadeur SPOORA.";
		    	  
		    	//Send message promo
		    	$mid = IMessageHelper::sendIMessage($spooraUser,$textPost1,$newUser->getId(),$fullPhonePost,$newUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
				
				//add Event to destinationUser
				UserEventHelper::addEventToUser($newUser->getId(),$newUser->getPin(),Constants::SMS_SUBMITED);

	  	  }else{

	  	     /********* WELCOME PROMOOO *************/
 		 
 			//if user without phone and phone was not validated before
 			if (!$newUser->getFullPhone() &&  $oldUser==null){
 		
 				 require_once 'Helper/UserEventHelper.php';
			     require_once 'Helper/IMessageHelper.php';
		    	//PROMO 100 spooris for validate phone
		    	//If first time user validates phone then add credits
		    	$newUser->setTokens($newUser->getTokens()+ Constants::WELCOME_TOKENS);

		    	  $spooraUser = UserHelper::getUserById(-1);   

		    	  $textPost="Congratulations! You've got your first 100 spooris. Now press your spooris counter on top of the screen to give back them to the entity of your choice";
		    	  
		    	  if($country->getId()==196)
		    	  	$textPost="Ya tienes tus primeros 100 spooris :) | Pulsa en el contador situado en la parte superior derecha para realizar tu primera donación";
		    	  
		    	  else if($country->getId()==73)
		    	  	$textPost="Bravo! Utilise bien tes premiers 100 spooris. Maintenant presse sur le compteur dans la partie supérieure de l'écran pour faire ton premier don :)";
		    	  
		    	//Send message promo
		    	$mid = IMessageHelper::sendIMessage($spooraUser,$textPost,$newUser->getId(),$fullPhonePost,$newUser->getPin(),2,null,null,Constants::SMS_SUBMITED);
				
				//add Event to destinationUser
				UserEventHelper::addEventToUser($newUser->getId(),$newUser->getPin(),Constants::SMS_SUBMITED);

		    }

		    /***************************************/
	  	  }
	     		  
	  	   $newUser->setFullPhone($fullPhonePost);
		   $newUser->setShortPhone($noPrefixShortPhone);
		   
		   UserHelper::updateUser($newUser);

		   $response="OK";

	      $this->getResponse()->appendBody($response);
	
	
	 
	  }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());


		$this->_forward('n2sms', 'error','default');
                 
        }
	 
    }

   public function loginAction()
    {
    	require_once 'Helper/VersionHelper.php';
    	require_once 'Helper/BannedDeviceHelper.php';

      try{
        
		$userIdPost        	= $this->_getParam('user_id');//Using user_id to allow users without SIM to chat
	    $userPhonePost     	= $this->_getParam('user_phone');
	    $userMACAddressPost	= $this->_getParam('mac_address');
	    $versionPost     	= $this->_getParam('version');
	   // $tokensPost     	= $this->_getParam('tokens');
	    $sessionType 		= $this->_getParam('session_type');
	    $deviceIdPost 		= $this->_getParam('device_id');
       	$serialIDPost 		= $this->_getParam('serial_id');
       	$rootPost 			= $this->_getParam('root');

  	if($deviceIdPost==null || $deviceIdPost=="null" || $deviceIdPost=="000000000000000"
                    ||$deviceIdPost=="004999010640000"
                    || $deviceIdPost=="358817001013700"
                    || $deviceIdPost=="Unavailable"
                    || $deviceIdPost==""
                    || mb_strtolower($deviceIdPost)=="unknown"){
       		$deviceIdPost=null;
       }
          if($serialIDPost==null ||$serialIDPost=="null"  || $serialIDPost=="000000000000000"
                    ||$serialIDPost=="004999010640000"
                    || $serialIDPost=="358817001013700"
                    ||$serialIDPost=="Unknown"){
       		$serialIDPost=null;
       }

       //If both serial and device are null then we use mac:
       if($deviceIdPost==null && $serialIDPost==null){
               $UDID = $userMACAddressPost;
         }
        else{
           //Build UDID unique device id:
      		 $UDID = (is_null($deviceIdPost)?"$":$deviceIdPost)."_".(is_null($serialIDPost)?"$":$serialIDPost);
        }
      

       //Si device baneado error 403
       	if(BannedDeviceHelper::isBanned($deviceIdPost,$serialIDPost)==TRUE)       		  
       		  throw new Exception("Error user Forbidden",Constants::ERROR_USER_FORBIDDEN);

	     if($sessionType==null)
	 	   	 	$sessionType=Constants::SESSION_TYPE_DEVICE;

	 	$macid=null;
	 	$versionValue=0;
	 	$tokens=0;
	 	$tokensResponse=0;

		  $user = UserHelper::getUserTokensByPin($userIdPost);
	  	  if ($user==null)
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);		

	       //if banned by PIN 423 user locked
           if($user->tokens_program >= Constants::TOKENS_USER_BANNED_ADBLOCK_PLUS)
           	    throw new Exception("Error user Unauthorized",Constants::ERROR_USER_FORBIDDEN);
           		//TODO client side -gestionar error 423 ERROR_USER_PIN_LOCKED

           //search user id by mac address if exist(Session Open)
		   if($UDID!=null){

			 $device=UserHelper::getUserDevice($user->id,$UDID);

		   }
           //if no session is opened REGISTER new device and Open new session
           if ($device->id==null && $UDID!=null){
           		UserHelper::registerDeviceToUser($user->id,$user->pin,$UDID,$serialIDPost,$deviceIdPost,$sessionType);}
           else	{
       		   UserHelper::updateSessionLastConnection($user->id,$device->id);}
 		 
 		  if (($device->device_id==null && $deviceIdPost!=null) || ($device->serial_id==null && $serialIDPost!=null)){
       		 	//TODO: Guardando device_id y serial solo sino existen ya
 		  		//FIX: TODO: eliminar is_active (no se usa)
       		   UserHelper::updateDeviceAndSerial($device->id,$deviceIdPost,$serialIDPost);
       		}

           	if ($versionPost!=null){

           		if($versionPost >= Constants::VERSION_VALID_VALUE)
           			$versionValue=Constants::VERSION_VALID;
           		elseif($versionPost <= Constants::VERSION_INVALID_VALUE)
           			$versionValue=Constants::VERSION_INVALID;
           		elseif($versionPost <= Constants::VERSION_INVALID_BUT_CONTINUE_VALUE)
           			$versionValue=Constants::VERSION_INVALID_BUT_CONTINUE;


           	}

           //	if ($tokensPost!=null){
           		//Update tokens
           		if($user->tokens_program != Constants::TOKENS_PROG_DISABLED){
    
           			$tokensResponse=Constants::TOKENS_OK;

           		//TODO tokens not OK =2
           	 	//TODO compare tokens
           		}else{
           			$tokensResponse=Constants::TOKENS_PROG_DISABLED;

           		}
           		//$tokens=$tokensPost;
           		
           	//}
          //TODO borrar esta linea al cabo de un mes
          //Abril = 1427846400 para todos los users existentes si user nuevo, ya tendra el registro creado y no hara nada.		
         UserStatsHelper::createStats($user->id,$user->pin,"1427846400");
           //Update Alias
        if($rootPost!=null){
                UserHelper::updateFieldValue($user->id,"is_root",$rootPost);
        }
         
	     $response="OK,".$versionValue.",".$tokensResponse.",".$user->tokens_program.",".$user->tokens.",".Constants::EQUATION_EXCHANGE_EUR.",".Constants::MIN_BALANCE_DONATIONS.",".Constants::MIN_BALANCE_SELF_PAYMENT.",".Constants::MONTH_ONG_ID.",".Constants::MONTH_ONG_NAME.",".Constants::TOKENS_MAX_BEFORE_AD;


        $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

     public function logoutAction()
     { 
      try{
	 
        $userIdPost 		= $this->_getParam('user_id');
        $userMACAddressPost = $this->_getParam('mac_address');
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
         $macid=UserHelper::getMacIdFromMacAddresses($user->getId(),$userMACAddressPost);
	 
	  if($user!=null && $macid!=null){

	      
	       $response=UserHelper::removeMacAddress($macid);
	      

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

    public function checkpromoAction()
    {

      try{
        
		$userIdPost        	= $this->_getParam('user_id');//Using user_id to allow users without SIM to chat
	    $tokensPost     	= $this->_getParam('tokens');

	 	$uid=null;
	 	$tokens=0;
	 	$tokensResponse=0;

		  $user = UserHelper::getUserTokensByPin($userIdPost);

           	if ($tokensPost!=null){
           		//Update tokens
           		if($user->tokens_program != Constants::TOKENS_PROG_DISABLED){

           			$tokensResponse=Constants::TOKENS_OK;

           		//TODO tokens not OK =2
           	 	//TODO compare tokens
           		}else{
           			$tokensResponse=Constants::TOKENS_PROG_DISABLED;

           		}
           		$tokens=$tokensPost;
           		//TODO check tokens with entered by params
           		
           	}

	     $response="OK,".$tokensResponse.",".$user->tokens_program.",".Constants::EQUATION_EXCHANGE_EUR;


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
	      //Update timestamp

	      
	       //verifying $toUserId and $toPhone.
	      $responseArray=UserHelper::getUserAndcheckErrors($toUserIdPost,$toPhonePost);
	      $toUser=$responseArray[1];
	      
	      if($toUser!=null){
	            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	           $response=(is_null($toUser->getPin())?"null":$toUser->getPin()).",".$toUser->getFullPhone().",".$toUser->getLastConnetion().",".TRUE;
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

     public function checkphonesAction()
     { 
      try{
	 
        $userIdPost 		= $this->_getParam('user_id');
        $phoneListPost 		= $this->_getParam('user_phone_list');
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	      //Update timestamp

	      $phoneList = explode(",",$phoneListPost);
	      
	      if($phoneList!=null){
	            $response=UserHelper::getValidUsersByPhone($user,$phoneList);
	      }

	  }
	  else
	      {
		throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	      }
	 
	      //TODO: Make a batch file to update conversations stats, don't do that from API services
	      //FIX:
	      if(Constants::MEMCACHE==TRUE){
             $memcache = new Memcache();
             $memcache->addServer('localhost', 11211);
             $memcache->connect('localhost', 11211);

              $updated = $memcache->get("stats_conversation_updated".$user->getId());

               
            if($updated==null){
            
	         	require_once 'Helper/UserLinkHelper.php';
	         	$totalConversations=UserLinkHelper::getConversationsCount($user->getId());
	         	UserStatsHelper::updateFieldValue($user->getId(),"conversations_count", $totalConversations);

	         	$memcache->set("stats_conversation_updated".$user->getId(), 1, false, (86400/3));
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

     public function searchAction()
     { 
      try{
	 
		$toUserIdPost 		= $this->_getParam('to_user_id');
        $toPhonePost 		= $this->_getParam('to_phone');
        $mailPost 			= $this->_getParam('mail');
        $fullNamePost 		= $this->_getParam('fullname');
        $searchByPost 		= $this->_getParam('search_by');
         
        if($searchByPost=="to_user_id"){
            if($toUserIdPost!=null)
        	 	$user = UserHelper::getUserByPin($toUserIdPost);

        }
        elseif($searchByPost=="mail"){
            if($mailPost!=null)
        	 	$user = UserHelper::getUserByMail($mailPost);

        }
        elseif($searchByPost=="to_phone"){
            if($toPhonePost!=null)
        	 	$user = UserHelper::getUserByLastDigits($toPhonePost);

        }
        elseif($searchByPost=="fullname"){
            if($fullNamePost!=null)
        	 	$user = UserHelper::getUserByPin($fullNamePost);

        }
        
	 
	  if($user!=null){
            UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	        $response=$user->getPin().",".$user->getName().",".$user->getMail().",".TRUE;
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
     
     public function updateAction()
     { 
      try{
	 
        $userIdPost 		= $this->_getParam('user_id');
		$passwordPost 		= $this->_getParam('password');
        $fullNamePost 		= $this->_getParam('fullname');
        $mailPost 			= $this->_getParam('mail');
        $fullPhone 			= $this->_getParam('fullphone');
        $anonymousModePost 	= $this->_getParam('anonymous');
        $groupDescriptionPost	 = $this->_getParam('description');
   
         
        //Validate params
	if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
       //Check Params
	 if ($passwordPost==null)//TODO-check if correct stamp
	        throw new Exception("Error pass cannot be null",Constants::ERROR_BAD_REQUEST);
		
	 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);
	 
	  if($user!=null){
	      //Update timestamp
	      if($this->userTimeStampPost!=null)
		       $user->setLastConnection($this->userTimeStampPost); 
		   if($this->connectionType!=null)
			   $user->setConnectionTypeId($this->connectionType);
  
          if($mailPost!=null)
	     	 $user->setMail($mailPost);

	      if($fullNamePost!=null)
	    	  $user->setName($fullNamePost);
	      if($passwordPost!=null)
	    	    UserHelper::setUserPassword($user,$passwordPost);

	      if($fullPhone!=null){
	    	  $user->setFullPhone($fullPhone);
	    	}

	      UserHelper::updateUser($user);


	     //TODO lanzar error si mail o numero existen

        if($anonymousModePost!=null){
                UserHelper::updateFieldValue($user->getId(),"is_anonymous",$anonymousModePost);
        }
          if($groupDescriptionPost!=null){
                UserHelper::updateFieldValue($user->getId(),"description",$groupDescriptionPost);
        }

	      $response="OK";
	      
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
    
    public function eventsAction()
    { require_once 'Helper/UserEventHelper.php';

     $writer = new Zend_Log_Writer_Stream('../private/logs/events.log');
	 $logger = new Zend_Log($writer);
      try{
        
		 $userPinPost   = $this->_getParam('user_id');
		 $withParamsPost   = $this->_getParam('with_params');
	     
	     //UserHelper::updateLastConnection($userPinPost,$this->userTimeStampPost);
	     
	      $user = UserHelper::getUserByPin($userPinPost);
	      if ($user==null){
	 	   		 $this->getResponse()->appendBody("ERROR,user does not exist is null o -1");
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        }

	     if($withParamsPost){

			 $response=UserEventHelper::getEventsWithParamsByUserId($user->getId());
	     }else{
	     	$response=UserEventHelper::getEvents($userPinPost);
	     }


	     $this->getResponse()->appendBody($response);
	   
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

    public function recoverAction()
     { require_once 'Helper/BannedDeviceHelper.php';
      try{
	  
		$pinPost 		= $this->_getParam('user_id');
        $fullPhonePost 	= $this->_getParam('fullphone');
        $mailPost 		= $this->_getParam('mail');
        $passwordPost 	= $this->_getParam('password');
        $userMACAddressPost	= $this->_getParam('mac_address');
        $macid=null;
        $deviceIdPost 		= $this->_getParam('device_id');
       	$serialIDPost 		= $this->_getParam('serial_id');

        if($deviceIdPost=="null"  || $deviceIdPost=="000000000000000"
                    ||$deviceIdPost=="004999010640000"
                    || $deviceIdPost=="358817001013700"
                    ||$deviceIdPost=="Unknown"){
       		$deviceIdPost=null;
       }
       if($serialIDPost=="null"  || $serialIDPost=="000000000000000"
                    ||$serialIDPost=="004999010640000"
                    || $serialIDPost=="358817001013700"
                    ||$serialIDPost=="Unknown"){
       		$serialIDPost=null;
       }
        //Si device baneado error 403
       	if(BannedDeviceHelper::isBanned($deviceIdPost,$serialIDPost)==TRUE)       		  
       		  throw new Exception("Error user Unauthorized",Constants::ERROR_USER_FORBIDDEN);


        $user=UserHelper::recoverUser($pinPost,$mailPost,$fullPhonePost,$passwordPost);
  		
	 
	  if($user!=null){

	        $response="OK,".$user->getPin().",".$user->getImSecretKey().",".$user->getName().",".$user->getMail().",".$user->getTokens().",".$user->getBannerImpr();

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


       //Send a media from X to Y
    public function putmythumbAction()
    {   require_once 'Helper/MediaHelper.php';
      	$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
      try{

		$userIdPost = $this->_getParam('user_id');

		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);

	        //Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){

	 	  //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	    
	    //Process upload
 		//cannot set limit in safe_mode
 		set_time_limit (60);
        if ($this->getRequest()->isPost()) {
	    	$uploadedFileURL=MediaHelper::doUploadToProfile($user->getPin(),$user->getPin().".mythumb");
	    		$response="OK,".$uploadedFileURL;
		  }
         else
             throw new Exception("Error in file upload method ",Constants::ERROR_BAD_REQUEST);


         $this->getResponse()->appendBody($response);

	 }


		 }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

    public function getmythumbAction()
    {
      try{

		$userIdPost = $this->_getParam('user_id');
      
	//Validate params
		if ($userIdPost==null)
	        throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
 
          //Verify user
	 if($userIdPost!=null && $userIdPost!=-1)
	    $user = UserHelper::getUserByPin($userIdPost);
	 	
	 if($user!=null){
	    //Update timestamp
	     UserHelper::updateUserTimeStamp($user, $this->userTimeStampPost,$this->connectionType);
	     $fileID=$user->getPin().".mythumb";
 		//Process get file
	    $fullFilePath=APPLICATION_PATH."/../profiles/".$fileID;
	    
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

    //Send a media from X to Y
    public function gettokensAction()
    {

      try{

		$userIdPost = $this->_getParam('user_id');

		  $user = UserHelper::getUserTokensByPin($userIdPost);

	  	  if ($user==null){
	  	  		$this->getResponse()->appendBody("Error user does not exist");
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        	}


	     $response="OK,".$user->tokens.",".Constants::EQUATION_EXCHANGE_EUR.",".Constants::MIN_BALANCE_DONATIONS.",".Constants::MIN_BALANCE_SELF_PAYMENT;
	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

    //Method for add pushID for GCM and APPLE Notifications
    //MUST BE CALLED ONLY FOR phone/tablet clients not web browsers.
    public function addpushidAction()
    {
  

      try{
        
		$userIdPost        	= $this->_getParam('user_id');//Using user_id to allow users without SIM to chat
	    $userMACAddressPost	= $this->_getParam('mac_address');
	    $pushID     	= $this->_getParam('push_id');
	    $deviceIdPost 		= $this->_getParam('device_id');
       	$serialIDPost 		= $this->_getParam('serial_id');
		
	 	$macid=null;
		$sessionType=Constants::SESSION_TYPE_DEVICE;

		if($deviceIdPost==null || $deviceIdPost=="null" || $deviceIdPost=="000000000000000"
	                    ||$deviceIdPost=="004999010640000"
	                    || $deviceIdPost=="358817001013700"
	                    || $deviceIdPost=="Unavailable"
	                    || $deviceIdPost==""
	                    || mb_strtolower($deviceIdPost)=="unknown"){
	       		$deviceIdPost=null;
	       }
	          if($serialIDPost==null ||$serialIDPost=="null"  || $serialIDPost=="000000000000000"
	                    ||$serialIDPost=="004999010640000"
	                    || $serialIDPost=="358817001013700"
	                    ||$serialIDPost=="Unknown"){
	       		$serialIDPost=null;
	       }

	       //If both serial and device are null then we use mac:
	       if($deviceIdPost==null && $serialIDPost==null){
	               $UDID = $userMACAddressPost;
	         }
	        else{
	           //Build UDID unique device id:
	      		 $UDID = (is_null($deviceIdPost)?"$":$deviceIdPost)."_".(is_null($serialIDPost)?"$":$serialIDPost);
	        }

	 	if($userIdPost!=null && $userIdPost!=-1)
	 		  $user = UserHelper::getUserTokensByPin($userIdPost);

	  	  if ($user==null)
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);		

           //get user device id if exist(Session Open)
		   if($UDID!=null){
				$device=UserHelper::getUserDevice($user->id,$UDID);
		   }
           		
            if ($device->id==null && $UDID!=null){
            	 //if deviceID has not being registered bedore THEN create new device with pushID
           		UserHelper::registerDeviceToUser($user->id,$user->pin,$UDID,$serialIDPost,$deviceIdPost,$sessionType);    
           		$device=UserHelper::getUserDevice($user->id,$UDID);
           	}
           	
           	//REMOVE OLD push_id's in old devices if some still duplicated
           	UserHelper::cleanDevicesByPushId($pushID,$device->id);

           	//UPDATE device with pushID
           	UserHelper::addPushIDToDevice($device->id,$pushID);
           

           		

	     $response="OK";


        $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }


    public function executeselfpaymentAction()
    { 	require_once 'Helper/TransactionSelfPaymentHelper.php';
   		require_once 'Helper/TransactionOngHelper.php';
   		require_once 'Helper/BannedDeviceHelper.php';


    	$userIdPost 	 	 = $this->_getParam('user_id');
		$tokensPost 		 = $this->_getParam('tokens');
		$countryIdPost 		 = $this->_getParam('country_id');
		$eq_exchangePost 	 = $this->_getParam('eq_exchange');
		$currencyIdPost 	 = $this->_getParam('currency_id');
		$entityNamePost      = $this->_getParam('entity_name');
    	$accountNumberPost   = $this->_getParam('account_number');
		$amountPost   = $this->_getParam('amount');
		$versionPost = $this->_getParam('version');
		$paypalMail = $this->_getParam('paypal_mail');
		$paypalName = $this->_getParam('paypal_name');
		$bannerImpr = $this->_getParam('banner_impr');
		//TODO check if banned
		$deviceIdPost 		= $this->_getParam('device_id');
       	$serialIDPost 		= $this->_getParam('serial_id');
       	$is_root 			= $this->_getParam('root');
               
		$transactionId="2";
		$orderId=UserHelper::getNewRandomPin(12);
		
		


      try{  
      	//If banned device then error 403 forbidden
		 if(BannedDeviceHelper::isBanned($deviceIdPost,$serialIDPost)==TRUE)       		  
       		  throw new Exception("Error user Unauthorized",Constants::ERROR_USER_FORBIDDEN);

            //Verify user
  		   if($userIdPost==null){
		   		$this->getResponse()->appendBody("ERROR,11,user_id param is null");
	        	throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
		   }
		   if($versionPost < Constants::VERSION_VALID_VALUE){
           			$this->getResponse()->appendBody("Error,3,app version does not match with server value");
           			throw new Exception("Error,3,app version does not match with server value",Constants::ERROR_BAD_REQUEST);
           	}

            //Verify user
           $user = UserHelper::getUserByPin($userIdPost);
           
           //if banned by PIN 423 user locked
           if($user->getTokensProgram() >= Constants::TOKENS_USER_BANNED_ADBLOCK_PLUS)
           	    throw new Exception("Error user Unauthorized",Constants::ERROR_USER_PIN_LOCKED);

		   if($user==null){
		   		$this->getResponse()->appendBody("ERROR,12,user_id param doesn't exist");
	        	throw new Exception("Error user_id param does not exist",Constants::ERROR_BAD_REQUEST);
		   }

		  //Mobile phone must be validated to make self payment
		   if($user->getFullPhone()==null){
  					
                $this->getResponse()->appendBody("Error,101, need to validate a phone number");
	        	throw new Exception("need to validate a phone number",Constants::ERROR_BAD_REQUEST);

	       }

	       if ($countryIdPost==null){
	  	  		$this->getResponse()->appendBody("Error,41,country_id paran is null");
	        	throw new Exception("country_id paran is null",Constants::ERROR_BAD_REQUEST);
	       }

	       //control tokens
	       if ($tokensPost==null){
	  	  		$this->getResponse()->appendBody("Error,31,tokens param is null");
	        	throw new Exception("tokens param is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if($tokensPost != $user->getTokens()){
	       		$this->getResponse()->appendBody("Error,32, tokens param does not match with server value,".$user->getTokens());
	        	throw new Exception("tokens param does not match with server value",Constants::ERROR_BAD_REQUEST);
	       }

	       if ($eq_exchangePost==null){
	  	  		$this->getResponse()->appendBody("Error,51,eq_exchange paran is null");
	        	throw new Exception("eq_exchange paran is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($eq_exchangePost!=Constants::EQUATION_EXCHANGE_EUR){
	  	  		$this->getResponse()->appendBody("Error,52,eq_exchange paran does not match with server value,".Constants::EQUATION_EXCHANGE_EUR);
	        	throw new Exception("eq_exchange param is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($currencyIdPost==null){
	  	  		$this->getResponse()->appendBody("Error,61,currency_id paran is null");
	        	throw new Exception("currency_id paran is null",Constants::ERROR_BAD_REQUEST);
	       }

	       if ($amountPost==null || $amountPost<=0){
	  	  		$this->getResponse()->appendBody("Error,71,amount paran is null or 0");
	        	throw new Exception("amount param is null",Constants::ERROR_BAD_REQUEST);
	       }

	       //if Banc Account payment
	       if($paypalMail==null){
		       if ($entityNamePost==null){
		  	  		$this->getResponse()->appendBody("Error,81,entityNamePost paran is null");
		        	throw new Exception("amount param is null",Constants::ERROR_BAD_REQUEST);
		       }
		       if ($accountNumberPost==null){
		  	  		$this->getResponse()->appendBody("Error,91,accountNumberPost paran is null");
		        	throw new Exception("amount param is null",Constants::ERROR_BAD_REQUEST);
		       }else{
		       		 if ($countryIdPost!=196 || $user->getCountryId()!=196){
	  	  		$this->getResponse()->appendBody("Error,62, money transfer only works for Spanish users");
	        	throw new Exception("money transfer only works for Spain",Constants::ERROR_BAD_REQUEST);
	       			}
		       }
	       }
	       /*if ($langPost==null){
	  	  		$this->getResponse()->appendBody("Error,81,lang paran is null");
	        	throw new Exception("lang paran is null",Constants::ERROR_BAD_REQUEST);
	       }*/


           	

	    $promoId=null;
	    $promoCode=null;
	
        //TODO - Calcular eq_exchange por pais en función de la divisa (currency). If dolar then user $ eq_exchange
        $amount= (Constants::EQUATION_EXCHANGE_EUR/100) * $user->getTokens();
         $DOWN_ALLOWED= $amount-2;
         $UP_ALLOWED= $amount+2;

         if($amount < Constants::MIN_BALANCE_SELF_PAYMENT*100){
         		//error Balance not enough
                $this->getResponse()->appendBody("Error,73, amount is not enough to make self payment,".$amount);
	        	throw new Exception("Error,73, amount is not enough to make self payment",Constants::ERROR_BAD_REQUEST);
         }

         if ($amountPost < $DOWN_ALLOWED || $amountPost>$UP_ALLOWED){

         	     //error of 2 cents allowed
                $this->getResponse()->appendBody("Error,72, amount param does not match with server calculated value,".$amount);
	        	throw new Exception("amount param does not match with server calculated value",Constants::ERROR_BAD_REQUEST);
	       }

	    //si null we get database value
	    if($bannerImpr==null)
	    	 $bannerImpr= $user->getBannerImpr();

	    $promoId=null;
	    $promoCode=null;

	    //Self_Payment is always 50% for user retribution and 50% for ONG donation
	    /* Uncomment here for 50% - 50% share between user and ONG
	    $half_tokens=$tokensPost/2;
	    $half_amount=$amountPost/2;*/

	    //New Self_payment fixed 1 euro for ONG, the rest for the user
	    $ong_tokens=(Constants::FIXED_SELF_PAYMENT_VALUE_ONG * 100) / (Constants::EQUATION_EXCHANGE_EUR/100);
	    $ong_amount= Constants::FIXED_SELF_PAYMENT_VALUE_ONG * 100;

	   	$user_tokens=$tokensPost -  $ong_tokens;
	    $user_amount=$amountPost - $ong_amount;

	    //if not admins pin
	    if($user->getId()!=1648){ 
        //TODO FIX: pass user by param not do request here
	     // set tokens to Zero after payment	
		  UserHelper::updateUserTokensTimeStamp($user, null,null,0,0,$bannerImpr);
		}

		$oldTokens=$user->getTokens();
		$user->setTokens($user->getTokens()-$user_tokens);

	    //50% User
	    //Send user self_paymetn
		$tid=TransactionSelfPaymentHelper::createTransaction($orderId,$user->getId(),$entityNamePost,$accountNumberPost,$countryIdPost,
			$user_tokens,$user_amount/100, $currencyIdPost,$eq_exchangePost, $oldTokens,$user->getShortPhone(),
			Constants::TRANS_PENDING,"Transaction Waiting",$promoId,$promoCode,$paypalMail,$paypalName,$bannerImpr,$is_root);

		
		//50% Donation for Monthly ONG
		//Send donation
	
		$tid2=TransactionOngHelper::createTransaction($orderId,$user->getId(),Constants::MONTH_ONG_ID,Constants::MONTH_ONG_NAME,$countryIdPost,
			$ong_tokens,$ong_amount/100, $currencyIdPost,$eq_exchangePost, $oldTokens,$user->getShortPhone(),
			Constants::TRANS_PENDING,"Transaction Waiting",$promoId,$promoCode,$bannerImpr);


		// TODO set to zero banner_impr
	     $response="OK,".$orderId;

	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

        //Method for add pushID for GCM and APPLE Notifications
    //MUST BE CALLED ONLY FOR phone/tablet clients not web browsers.
   public function sendinfoAction()
   {

      try{
        
		$userIdPost        	= $this->_getParam('user_id');//Using user_id to allow users without SIM to chat
	    $codePost			= $this->_getParam('code');
	    $messagePost     	= $this->_getParam('message');
	
		$sessionType=Constants::SESSION_TYPE_DEVICE;

	 	if($userIdPost!=null && $userIdPost!=-1)
	    	$user = UserHelper::getUserByPin($userIdPost);

	  	  if ($user==null)
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);		

	      //USO de ADBLOCKPLUS  
          if($codePost==101){
          	 
          	 	UserStatsHelper::updateFieldIncrement($user->getId(),"adblockplus_count");
				$count= UserStatsHelper::getFieldValue($user->getId(),"adblockplus_count");

	          	if($count==1 || $count==20 || $count==40|| $count==60 || $count==80){
	          		//require_once 'Helper/IMessageHelper.php';
	          		//IMessageHelper::sendSpooraMessage($user,"El sistema ha detectado un uso fraudulento de la aplicación. Le rogamos que permita la visibilidad de los banners publicitarios, de lo contrario se le bloqueará la función de sumar spooris y no podrá solicitar cobro alguno. En caso de duda o consulta  puede escribirnos a contact@myspoora.com. Puede consultar nuestras normas de conviviencia en https://www.myspoora.com/#faq");
	          	}
	          	if($count==100){
	          		//require_once 'Helper/IMessageHelper.php';
				//IMessageHelper::sendSpooraMessage($user,"El sistema ha deshabilitado la función de incremento de spooris debido al uso fraudulento de la aplicación y la violación flagrante de nuestras normas de convivencia. A su vez, la opción de solicitud de cobros ha sido bloqueada. En caso de duda o consulta puede escribirnos a contact@myspoora.com Puede consultar nuestras normas de conviviencia en https://www.myspoora.com/#faq");
	          		//Baneo user
	          		//$user->setTokensProgram(Constants::TOKENS_USER_BANNED_ADBLOCK_PLUS);
				//	UserHelper::updateUser($user);

	          	}
          		

          	}

          //IMPIDE VISUALIZAR PUBLI PERO NO SABEMOS CON QUE SISTEMA	
          elseif($codePost==100 || ($codePost > 101 && $codePost < 106)){

          	 UserStatsHelper::updateFieldIncrement($user->getId(),"adblock_count");
			 $count= UserStatsHelper::getFieldValue($user->getId(),"adblock_count");
          	//aviso a partir de 20 pilladas
          	if($count==20 || $count==40|| $count==60 || $count==80){
	          	//require_once 'Helper/IMessageHelper.php';
	          	//IMessageHelper::sendSpooraMessage($user,"El sistema ha detectado un uso fraudulento de la aplicación. Le rogamos que permita la visibilidad de los banners publicitarios, de lo contrario se le bloqueará la función de sumar spooris y no podrá solicitar cobro alguno. En caso de duda o consulta  puede escribirnos a contact@myspoora.com. Puede consultar nuestras normas de conviviencia en https://www.myspoora.com/#faq");
          	}
          	
          	if($count==100){
          		//require_once 'Helper/IMessageHelper.php';
			//	IMessageHelper::sendSpooraMessage($user,"El sistema ha deshabilitado la función de incremento de spooris debido al uso fraudulento de la aplicación y la violación flagrante de nuestras normas de convivencia. A su vez, la opción de solicitud de cobros ha sido bloqueada. En caso de duda o consulta puede escribirnos a contact@myspoora.com Puede consultar nuestras normas de conviviencia en https://www.myspoora.com/#faq");
	          	//Baneo user
	          	//$user->setTokensProgram(Constants::TOKENS_USER_BANNED_GENERIC);
			//	UserHelper::updateUser($user);
          	}

          }

           //Banner impresions
           elseif($codePost==2001){
			 //Guardar impresiones de writer
           		$user->setBannerImpr($messagePost);
				UserHelper::updateUser($user);
           }
           //Banner impresions
           elseif($codePost==99){
			 //Guardar impresiones de writer
           	if(UserStatsHelper::getFieldValue($user->getId(),"adblock_count") > 0 )
            	 UserStatsHelper::updateFieldDecrement($user->getId(),"adblock_count");
           }

          $writer = new Zend_Log_Writer_Stream('../private/logs/info.log');
	 	  $logger = new Zend_Log($writer);
	 	  $logger->info($userIdPost.",".$codePost.",".$messagePost);
           		

	     $response="OK";


        $this->getResponse()->appendBody($response);
		
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
  }
  
   public function addspoorisAction()
     { 
      try{
	 
     $userIdPost 		= $this->_getParam('user_id');
     $addValuePost = $this->_getParam('add');
     $isFirstReward=null;
		
	 //Verify user
     $user = UserHelper::getUserByPin($userIdPost);
	 $response="Error";

               
          
			  if($user!=null &&  $addValuePost > 0){
		 			$new_tokens=  $user->getTokens();

					     if(Constants::MEMCACHE==TRUE){
					             $memcache = new Memcache();
					             $memcache->addServer('localhost', 11211);
					             $memcache->connect('localhost', 11211);
						  		//Permite añadir recompensa solo una vez por dia (24h)
					  	 		 $isFirstReward = $memcache->get("daily_reward".$user->getId());
					  	 		}

			       if($user->getTokensDay() < Constants::TOKENS_MAX_DAY && $isFirstReward==null){

					      UserHelper::updateFieldValue($user->getId(),"tokens",$user->getTokens() + $addValuePost);
					      UserHelper::updateFieldValue($user->getId(),"tokens_day",$user->getTokensDay() + $addValuePost);
					      $new_tokens=($user->getTokens() + $addValuePost);

					      if(Constants::MEMCACHE==TRUE)
					      		$memcache->set("daily_reward".$user->getId(), 1, false, (86400/3));
					      
					      $response="OK";
			  		}
			  		else{
			  			 $response="OK";
			  		}
			      
			  }
			  else
			      {
				    throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
			      }
	  
	  $this->getResponse()->appendBody($response.",".$new_tokens);
	 
      }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
     }	

         //Get id list of ONG by country
    public function getcurrentmilisAction()
    {
    	
		
      try{

	     $response="OK,".explode('.',microtime(true) * 1000)[0].",100";
	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }


    public function checktokenAction()
    { 
    	require_once 'Helper/PushNotificationsHelper.php';
    	$userIdPost = $this->_getParam('user_id');
		$result="";
		

    	try{
    		
 			$user = UserHelper::getUserByPin($userIdPost);
		    $devicesList=UserHelper::getDevicesFromUser($user->getId());

		    foreach($devicesList as $device){

                        if(!empty($device->push_id)){
                        	$registrationIDs = array();
                          //send push to device
                           array_push($registrationIDs,$device->push_id);
                           $result.=PushNotificationsHelper::checkTokenValidity($user->getId(),$user->getPin(),$registrationIDs). " | ";
                        }

         
                    }

	     	$this->getResponse()->appendBody($result);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
  	
  	}


}




                      

