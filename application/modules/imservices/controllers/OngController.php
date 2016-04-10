<?php
require_once 'Helper/UserHelper.php';
require_once 'Helper/OngHelper.php';
require_once 'Objects/Constants.php';

class Imservices_OngController extends Zend_Controller_Action
{

   public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
		
    }
    
    

   
    //Get id list of ONG by country
    public function listAction()
    {
    	$userIdPost 	 = $this->_getParam('user_id');
		$countryIdPost   = $this->_getParam('country_id');
		$langPost 		 = $this->_getParam('lang');
		
		$ongList="";
				
      try{

		 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);

	  	  if ($user==null){
	  	  		$this->getResponse()->appendBody("Error,user does not exist");
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        	}

	     $ongList=OngHelper::getOngIdListByCountry($countryIdPost);

	     $response="OK,".$ongList;
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
    	$userIdPost 	 = $this->_getParam('user_id');
    	$ongIdPost       = $this->_getParam('ong_id');
		$langPost 		 = $this->_getParam('lang');
		
		$translate=Zend_Registry::get('Zend_Translate');

				
      try{

		 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);

	  	  if ($user==null){
	  	  		$this->getResponse()->appendBody("Error,user does not exist");
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        	}
	     //TODO FIX this: setLocale() is threated in HHVM
	     //We need to recode setLocale() in zend for threaded when supporting another languages
	     //$translate->setLocale($langPost);
	     $ongInfo.=$ongIdPost.",9&c3";
	     $ongInfo.=$translate->_("ong_".$ongIdPost."_name").",9&c3";
	     $ongInfo.=$translate->_("ong_".$ongIdPost."_description");
	

	     $response="OK,".$ongInfo;

	     Header( "Content-Type: text/html; charset=utf-8" );
	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

    public function executedonationAction()
    { 	require_once 'Helper/TransactionOngHelper.php';
    	require_once 'Helper/BannedDeviceHelper.php';

    	$userIdPost 	 	 = $this->_getParam('user_id');
    	$ongIdPost           = $this->_getParam('ong_id');
		$tokensPost 		 = $this->_getParam('tokens');
		$countryIdPost 		 = $this->_getParam('country_id');
		$eq_exchangePost 	 = $this->_getParam('eq_exchange');
		$currencyIdPost 	 = $this->_getParam('currency_id');
		$amountPost   = $this->_getParam('amount');
		$langPost   = $this->_getParam('lang');
		$versionPost = $this->_getParam('version');
		$bannerImpr = $this->_getParam('banner_impr');
		$deviceIdPost 		= $this->_getParam('device_id');
       	$serialIDPost 		= $this->_getParam('serial_id');

		$transactionId="2";
		$orderId=UserHelper::getNewRandomPin(12);
		
		$translate=Zend_Registry::get('Zend_Translate');
		$translate->setLocale($langPost);
		$ongName=$translate->_("ong_".$ongIdPost."_name");
		
      try{
      	//If banned device then error 403 forbidden
		 if(BannedDeviceHelper::isBanned($deviceIdPost,$serialIDPost)==TRUE)       		  
       		  throw new Exception("Error user Unauthorized",Constants::ERROR_USER_FORBIDDEN);
       		
         //Error control

		    //Verify user
  		   if($userIdPost==null){
		   		$this->getResponse()->appendBody("ERROR,11,user_id param is null");
	        	throw new Exception("Error user_id param is null",Constants::ERROR_BAD_REQUEST);
		   }
		   	if($versionPost < Constants::VERSION_VALID_VALUE){
           			$this->getResponse()->appendBody("Error,3,app version does not match with server value");
           			throw new Exception("amount paran is null",Constants::ERROR_RESOURCE_NOT_FOUND);
           	}

            //Verify user
           $user = UserHelper::getUserByPin($userIdPost);
           

		   if($user==null){
		   		$this->getResponse()->appendBody("ERROR,12,user_id param doesn't exist");
	        	throw new Exception("Error user_id param does not exist",Constants::ERROR_BAD_REQUEST);
		   }

		   //if banned by PIN error => 423 user locked
           if($user->getTokensProgram() >= Constants::TOKENS_USER_BANNED_ADBLOCK_PLUS)
           	    throw new Exception("Error user Unauthorized",Constants::ERROR_USER_PIN_LOCKED);

          if ($ongIdPost==null){
	  	  		$this->getResponse()->appendBody("Error,21,ong_id param is null");
	        	throw new Exception("ong_id param is null",Constants::ERROR_BAD_REQUEST);
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
	       if ($countryIdPost==null){
	  	  		$this->getResponse()->appendBody("Error,41,country_id paran is null");
	        	throw new Exception("country_id paran is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($eq_exchangePost==null){
	  	  		$this->getResponse()->appendBody("Error,51,eq_exchange paran is null");
	        	throw new Exception("eq_exchange paran is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($eq_exchangePost!=Constants::EQUATION_EXCHANGE_EUR){
	  	  		$this->getResponse()->appendBody("Error,52,eq_exchange paran does not match with server value,".Constants::EQUATION_EXCHANGE_EUR);
	        	throw new Exception("eq_exchange paran is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($currencyIdPost==null){
	  	  		$this->getResponse()->appendBody("Error,61,currency_id paran is null");
	        	throw new Exception("currency_id paran is null",Constants::ERROR_BAD_REQUEST);
	       }
	       if ($amountPost==null || $amountPost<=0){
	  	  		$this->getResponse()->appendBody("Error,71,amount paran is null or 0");
	        	throw new Exception("amount paran is null",Constants::ERROR_BAD_REQUEST);
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

         if ($amountPost < $DOWN_ALLOWED || $amountPost>$UP_ALLOWED){

         	     //error of 2 cents allowed
                $this->getResponse()->appendBody("Error,72, amount param does not match with server calculated value,".$amount);
	        	throw new Exception("amount param does not match with server calculated value",Constants::ERROR_BAD_REQUEST);
	       }

	     	    //si null we get database value
	    if($bannerImpr==null)
	    	 $bannerImpr= $user->getBannerImpr();

		$tid=TransactionOngHelper::createTransaction($orderId,$user->getId(),$ongIdPost,$ongName,$countryIdPost,
			$tokensPost,$amountPost/100, $currencyIdPost,$eq_exchangePost, $user->getTokens(),$user->getShortPhone(),
			Constants::TRANS_PENDING,"Transaction Waiting",$promoId,$promoCode, $bannerImpr);

	if($user->getId()!=1648){ 
        //TODO FIX: pass user by param not do request here
		  UserHelper::updateUserTokensTimeStamp($user, null,null,($user->getTokens()-$tokensPost),0,$bannerImpr);
		}

		 /********Send Congrats for donation message********/
			 	 require_once 'Helper/UserEventHelper.php';
			     require_once 'Helper/IMessageHelper.php';

		    	  $spooraUser = UserHelper::getUserById(-1);   

		    	  $textPost="Hoy, gracias a tu donación y a la de miles de spoorers ".$ongName." dará un fuerte impulso a sus proyectos. Mil gracias :)";
  				  $textPost2= "Si te gusta Spoora, ayúdanos a darla a conocer entre tus contactos. Ves al menú principal, pulsa el lápiz y luego dale a la opción Recomendar Spoora. La Revolución de la donación colaborativa y gratuita ha comenzado!";
		    	//Send message promo
		    	$mid = IMessageHelper::sendIMessage($spooraUser,utf8_decode($textPost),$user->getId(),null,$user->getPin(),2,null,null,Constants::SMS_SUBMITED);
		    	$mid = IMessageHelper::sendIMessage($spooraUser,utf8_decode($textPost2),$user->getId(),null,$user->getPin(),2,null,null,Constants::SMS_SUBMITED);
				//add Event to destinationUser
				UserEventHelper::addEventToUser($user->getId(),$user->getPin(),Constants::SMS_SUBMITED); 

	     $response="OK,".$orderId;

	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }   

    //Get id list of ONG by country
    public function getdonationsAction()
    { require_once 'Helper/TransactionOngHelper.php';
    	$userIdPost = $this->_getParam('user_id');
		
		$donationList="";
				
      try{

		 //Verify user
         $user = UserHelper::getUserByPin($userIdPost);

	  	  if ($user==null){
	  	  		$this->getResponse()->appendBody("Error,user does not exist");
	        	throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
	        	}

	     $donationList=TransactionOngHelper::getTransactions($user->getId());

	     $response="OK,".$donationList;
	     $this->getResponse()->appendBody($response);
            
        }catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error','default');
                 
        }
    }

}

