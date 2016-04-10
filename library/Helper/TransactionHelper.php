<?php
require_once 'Helper/DbHelper.php';
require_once 'Helper/UserHelper.php';
require_once 'Helper/ProductHelper.php';
require_once 'Helper/CountryHelper.php';
require_once 'Helper/CurrencyHelper.php';
require_once 'Helper/InvoiceHelper.php';
require_once 'Helper/MessageHelper.php';
require_once 'Helper/UserPromoHelper.php';
require_once 'Objects/Transaction.php';
require_once 'Objects/Constants.php';
require_once 'Router/ProviderRouter.php';
require_once 'SmsProviders/intelliSMS/IntelliSMSProvider.php';
require_once 'SmsProviders/RoutoTelecom/RoutoProvider.php';
require_once 'SmsProviders/twilio/TwilioProvider.php';
/*require_once 'SmsProviders/lleidaNet/LleidaNetProvider.php';
require_once 'SmsProviders/sms42IT/Sms42TelecomProvider.php';
require_once 'SmsProviders/usaBulkSMS/UsaBulkSMSProvider.php';
require_once 'SmsProviders/tm4b/Tm4bProvider.php';*/

class TransactionHelper {
    
    
    public static function createTransaction($shortPhone, $amount, $currencyId, $countryId, $productId, $orderId, $status, $status_detail,$gateway,$promoId,$promoCode,$groupId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->beginTransaction();
            //create message
            $data = array(
	    'order_id'		 => $orderId,
	    'short_phone'     	 => $shortPhone,
	    'promo_id'      	 => $promoId,
	    'promo_code'      	 => $promoCode,
            'amount'       	 => $amount,
            'currency_id'  	 => $currencyId,
            'product_id'  	 => $productId,
	    'group_id'      	 => $groupId,
	    'country_id'  	 => $countryId,
	    'start_time'   	 => time(),
            'status'       	 => $status,
	    'status_detail'      => $status_detail,
	    'gateway'      	 => $gateway
            );

            $db->insert('transaction', $data);
            $tid = $db->lastInsertId();
	    
            $db->commit();
            
           return $tid;
           
           
        
        }catch (Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
	   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
           $db->rollBack();
          //$n = $db->delete('messages', 'mid = '.$mid);
        }
      
    }
    
    public static function getTransactionByOrderId($orderId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM transaction WHERE order_id = ?', $orderId);
            
            if (count($result)== 1)
            {
                $transaction= new Transaction(
				$result[0]->id,
				$result[0]->order_id,
				$result[0]->user_id,
				$result[0]->short_phone,
				$result[0]->amount,
				$result[0]->currency_id,
				$result[0]->country_id,
				$result[0]->product_id,
				$result[0]->start_time,
				$result[0]->end_time,
				$result[0]->status,
				$result[0]->status_detail,
				$result[0]->gateway,
				$result[0]->promo_id,
				$result[0]->promo_code,
				$result[0]->group_id);
               
            }
            else{
               $transaction=null;
	       
            }
            
            $dbConn->closeConnection();
	    
           return $transaction;
  
        } catch (Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
     public static function getTransactionById($id)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM transaction WHERE id = ?', $id);
            
            if (count($result)== 1)
            {
                $transaction= new Transaction(
				$result[0]->id,
				$result[0]->order_id,
				$result[0]->user_id,
				$result[0]->short_phone,
				$result[0]->amount,
				$result[0]->currency_id,
				$result[0]->country_id,
				$result[0]->product_id,
				$result[0]->start_time,
				$result[0]->end_time,
				$result[0]->status,
				$result[0]->status_detail,
				$result[0]->gateway,
				$result[0]->promo_id,
				$result[0]->promo_code,
				$result[0]->group_id);
               
            }
            else{
               $transaction=null;
	      
            }
            
            $dbConn->closeConnection();
	    
           return $transaction;
  
        } catch (Exception $e) {
	    throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function getLastTransactionByUser($userId)
    {
 
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM transaction WHERE user_id = ? and status='.Constants::TRANS_RECEIVED.' order by id desc', $userId);
            
            if (count($result) > 0)
            {
                $transaction= new Transaction(
				$result[0]->id,
				$result[0]->order_id,
				$result[0]->user_id,
				$result[0]->short_phone,
				$result[0]->amount,
				$result[0]->currency_id,
				$result[0]->country_id,
				$result[0]->product_id,
				$result[0]->start_time,
				$result[0]->end_time,
				$result[0]->status,
				$result[0]->status_detail,
				$result[0]->gateway,
				$result[0]->promo_id,
				$result[0]->promo_code,
				$result[0]->group_id);
               
            }
            else{
               $transaction=null;
	      
            }
            
            $dbConn->closeConnection();
	    
           return $transaction;
  
        } catch (Exception $e) {
	    throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function updateTransaction($transaction){
        
            try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
              //update new user balance
                $data = array("order_id" => $transaction->getOrderId());
		
		if($transaction->getUserId()!=NULL)
		     $data["user_id"]=$transaction->getUserId();
		if($transaction->getShortPhone()!=NULL)
		     $data["short_phone"]=$transaction->getShortPhone();
		if($transaction->getAmount()!=NULL)
		    $data["amount"]=$transaction->getAmount();
		if($transaction->getCurrencyId()!=NULL)
		    $data["currency_id"]=$transaction->getCurrencyId();
		if($transaction->getCountryId()!=NULL)
		    $data["country_id"]=$transaction->getCountryId();
		if($transaction->getProductId()!=NULL)
		    $data["product_id"]=$transaction->getProductId();
		if($transaction->getEndTime()!=NULL)
		     $data["end_time"]=$transaction->getEndTime();
		if($transaction->getStatus()!=NULL)
		     $data["status"]=$transaction->getStatus();
		if($transaction->getStatusDetail()!=NULL)
		     $data["status_detail"]=$transaction->getStatusDetail();
		if($transaction->getGateway()!=NULL)
		     $data["gateway"]=$transaction->getGateway();
	        if($transaction->getPromoId()!=NULL)
		     $data["promo_id"]=$transaction->getPromoId();
		if($transaction->getPromoCode()!=NULL)
		     $data["promo_code"]=$transaction->getPromoCode();
		if($transaction->getGroupId()!=NULL)
		     $data["group_id"]=$transaction->getGroupId(); 
            
		
		$where[] = "id =".$transaction->getId();
                $db->update('transaction', $data, $where);
    
                $db->closeConnection();
                
               return true;
               
            }catch (Exception $e) {
              throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
          
              
            }
    }
    
    public static function updateTransactionTransDb($db,$transaction){
        
            try {
                
              //update new user balance
                $data = array("order_id" => $transaction->getOrderId());
		
		if($transaction->getUserId()!=NULL)
		     $data["user_id"]=$transaction->getUserId();
		if($transaction->getShortPhone()!=NULL)
		     $data["short_phone"]=$transaction->getShortPhone();
		if($transaction->getAmount()!=NULL)
		    $data["amount"]=$transaction->getAmount();
		if($transaction->getCurrencyId()!=NULL)
		    $data["currency_id"]=$transaction->getCurrencyId();
		if($transaction->getCountryId()!=NULL)
		    $data["country_id"]=$transaction->getCountryId();
		if($transaction->getProductId()!=NULL)
		    $data["product_id"]=$transaction->getProductId();
		if($transaction->getEndTime()!=NULL)
		     $data["end_time"]=$transaction->getEndTime();
		if($transaction->getStatus()!=NULL)
		     $data["status"]=$transaction->getStatus();
		if($transaction->getStatusDetail()!=NULL)
		     $data["status_detail"]=$transaction->getStatusDetail();
		if($transaction->getGateway()!=NULL)
		     $data["gateway"]=$transaction->getGateway();
	        if($transaction->getPromoId()!=NULL)
		     $data["promo_id"]=$transaction->getPromoId();
		if($transaction->getPromoCode()!=NULL)
		     $data["promo_code"]=$transaction->getPromoCode();
		if($transaction->getGroupId()!=NULL)
		     $data["group_id"]=$transaction->getGroupId();
                
		$where[] = "order_id =".$transaction->getOrderId();
                $db->update('transaction', $data, $where);
                
               return true;
               
            }catch (Exception $e) {
              throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
            }
    }
    
    public static function checkLaCaixaResponse($dsResponse,$dsSignature,$calcSignature)
    {
	$status= Constants::TRANS_FAILED;
	$responseArray [] = NULL;
	$responseMessage="";
	$errorMessageInLang="";
	
    if ($dsSignature == $calcSignature && $dsResponse == '0000') {
	    
	    $responseMessage= 'Pago recibido correctamente.';
	    $status= Constants::TRANS_RECEIVED;

	}
    else {  // there was an error on the answer, either a wrong response code or a wrong Ds_Signature
	
	if ($dsResponse < 100) { //if we've had a payment ok code (0-99) some one is trying to cheat us with a wrong signature
	  $responseMessage = 'LaCaixaError_0_99 - Se ha producido un error durante la transacción.Verifique los datos y vuelva a intentarlo de nuevo.';
	  $errorMessageInLang='LaCaixaError_0_99';
	}
	 else { // we had an payment error code. we need to reprocess the payment os the client can try to pay again.
	  
	  switch ($dsResponse) {
	    case 101:
	      $error = 'LaCaixaError_101 - Tarjeta caducada. Por favor, utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError_101';
	      break;
	    case 116:
	      $error = 'LaCaixaError_116 - No hay suificiente credito en su cuenta. Por favor, utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError_116';
	      break;
	    case 118:
	      $error = 'LaCaixaError_118 - Tarjeta no registrada. Por favor, utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError_118';
	      break;
	    case 129:
	      $error = 'LaCaixaError_129 - Codigo de seguridad incorrecto (CVV2/CVC2). Vuelva al proceso de compra de nuevo.';
	      $errorMessageInLang = 'LaCaixaError_129';
	      break;
	    case 180:
	      $error = 'LaCaixaError_180 - Tarjeta no valida para este servicio. Por favor, utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError_180';
	      break;
	    case 184:
	      $error = 'LaCaixaError_184 - Número de tarjeta incorrecto. Por favor vuelva al proceso de compra de nuevo.';
	      $errorMessageInLang = 'LaCaixaError_184';
	      break;
	    case 191:
	      $error = 'LaCaixaError_191 - Fecha de caducidad incorrecta. Por favor vuelva al proceso de compra de nuevo.';
	      $errorMessageInLang = 'LaCaixaError_191';
	      break;
	    case 921:
	    case 9912:
	      $error = 'LaCaixaError_921_9912 - El tipo de tarjeta no es valida. Por favor, utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError_921_9912';
	      break;
	    
	    default: 
	      $error = 'LaCaixaError - Ha habido un problema intentando procesar esta transaccion, rechazo de pago o otro error. Por favor, intentelo de nuevo o utilice una tarjeta diferente.';
	      $errorMessageInLang = 'LaCaixaError';
	  }
	
	  $responseMessage .= $error;
	}
	
      }
   
      $responseArray[0]=$status;
      $responseArray[1]=$responseMessage;
      $responseArray[2]=$errorMessageInLang;
      
     return $responseArray;
      
    }
    
    private function addUserToTransaction($transaction,$currentUser){
	
	try{
	       //Prevent multiple requests: If Transaction status is not received yet then proceed
		$transaction->setUserId($currentUser->getId());
		      
		//update transaction status to received or failed
		self::updateTransaction($transaction);

	}catch (Exception $e) {
	    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
	
    }
    
     private function updateTransactionStatus($transaction,$responseArray){
	
	try{
            
	      //Prevent multiple requests: If Transaction status is not received yet then proceed
	      if($transaction->getStatus()!=Constants::TRANS_RECEIVED){
	      //if($transaction->getEndTime()!=null){
  
			  $transaction->setStatus($responseArray[0]);
			  $transaction->setStatusDetail($responseArray[1]);
			  $transaction->setEndTime(time());
		          
			  //update transaction status to received or failed
			  self::updateTransaction($transaction);
		    
	      }
	}catch (Exception $e) {
	    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
	
    }
    
    public static function sendSmsSecretKey($currentUser){
	$response=null;
	  try{
	    $writer = new Zend_Log_Writer_Stream('../private/logs/sms.log');
	    $logger = new Zend_Log($writer);
	   
	    $from="spoora";
	    $text="This is your spoora ID. Don't delete this, the system will use it ".
			   "to verify you\n".
			   "NUM:".$currentUser->getFullPhone()."\n".
			   "ID:".$currentUser->getSecretKey();
			
		$destinationCountry = CountryHelper::getCountry($currentUser->getCountryId());
		$to=$currentUser->getFullPhone();
		//sender user is SYSTEM n2manager
		$systemUser = UserHelper::getUser($from);
		$messageId=MessageHelper::createMessageBroadcast($systemUser,""/*$text*/,$to,null,null,
								 $destinationCountry->getId(),"TEMPID-".time(),null);			 
		$providerRouter = new ProviderRouter($destinationCountry);
		$routes=$providerRouter->getRoutes();
		
		//Send SMS to Provider
		 foreach($routes as $route){
		 	
		     switch($route->getProviderId()){
				case 3: $provider= new IntelliSMSProvider();break;
				case 7: $provider= new TwilioProvider();break;
		     }

		    //response is externalId if OK

            if (Constants::DISABLE_SMS!="true")
		       $response=$provider->sendSms($from,$to,$text);

		    if($response!="ERROR"){//NOW SMS has been sent
			    $message = new MessageBroadcast();
			    $message->setMessageId($messageId);
			    $message->setProviderId($provider->getProviderId());
			    $message->setStatus(Constants::SMS_SUBMITED);
			    $message->setStatusDetail(Constants::SMS_SUBMITED."-Message SUBMITED");
			    $message->setExternalId($response);
			    $message->setRealCost($route->getPrice());
			    $message->setUserCost(1);	
			    MessageHelper::updateMessageBroadcast($message);
		       break;
		      }
		}
		$logger->info("TransactionHelper > sendSmsSecretKey:\n\t".
				       "externalId: ".$response."\n\t".
				       "provider: ".$route->getProviderId()."\n\t".
				       "messageId: ".$messageId."\n\t".
				       "user: ".$systemUser->getFullPhone()."\n\t".
				       "to: ".$to."\n\t".
				       "pin: ".$currentUser->getPin()."\n\t".
				       "toCountry: ".$destinationCountry->getName()."\n\t".
				       "text: ".$text."\n\t");
		 if($response=="ERROR")
		    throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);     
         }catch (Exception $e) {
		throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
                 
        }
	
    }
    
    private function createOrUpdateUserByTransaction($transaction){
	
	    $country = CountryHelper::getCountry($transaction->getCountryId());  
	    $fullPhone = Constants::IDD_PREFIX.$country->getCountryCode().$transaction->getShortPhone();
	    $currentUser = UserHelper::getUser($fullPhone);
	    $currency = CurrencyHelper::getCurrency($transaction->getCurrencyId());
	    $product = ProductHelper::getProductByCurrencyCode($transaction->getProductId() ,$currency->getCode(),$transaction->getGroupId());
	    $group = $product->getGroupId();
		
	    //if user not Exist we create it with 0 credit balance	
		if($currentUser==NULL){
		    if(UserHelper::createUser($fullPhone,$transaction->getShortPhone(),0,$country->getId(),$group,false,null,null,null,null,null,null))
			    $currentUser = UserHelper::getUser($fullPhone);
		}
		
		 //update User <balance> 
		 if($currentUser->getGroupId()==$transaction->getGroupId()){
		    $currentUser->setBalance($currentUser->getBalance() + $product->getCreditsNumber());
		 }
		 else{
		    //Change from Group 1 to Group 2
		    if($currentUser->getGroupId()< $transaction->getGroupId())
		        $currentUser->setBalance(($currentUser->getBalance()*2) + $product->getCreditsNumber()); 
		    //Change from Group 2 to Group 1
		    if($currentUser->getGroupId() > $transaction->getGroupId())
		        $currentUser->setBalance((ceil($currentUser->getBalance()/2)) + $product->getCreditsNumber());
		 }
		 //Add user in new Group
		 $currentUser->setGroupId($transaction->getGroupId());
		 
		//and <secret_key>: only if transaction was succeeded	
		$salt = uniqid(mt_rand(), true);
			  
		$messageKey=$salt.time().$transaction->getOrderId().$currentUser->getFullPhone();
		$currentUser->setSecretKey(md5($messageKey),false);
			  
		UserHelper::updateUser($currentUser);
		
		//Add promo Boca a Boca by default when creating user
		//if user does'nt have it yet
		 if(UserPromoHelper::getPromoCodeByUser($currentUser->getId(),Constants::PROMO_BOCA_A_BOCA)==null){
		    UserPromoHelper::addPromoToUser($currentUser->getId(),Constants::PROMO_BOCA_A_BOCA);
		    }
		    
	    return $currentUser;
    }

    
    public static function doAfterPaymentGatewayResponse($transaction,$responseArray)
    {
	$status = $responseArray[0];
	$response = $responseArray[1];
	$product = NULL;
	$currentUser = NULL;
	
	if($transaction!=NULL){
	    
	    self::updateTransactionStatus($transaction,$responseArray);
	    
	    //Update or creates user only if Transaction RECEIVED
	    if($status==Constants::TRANS_RECEIVED){
		
		$currentUser = self::createOrUpdateUserByTransaction($transaction);
    
		self::addUserToTransaction($transaction,$currentUser);
		
		//Sends SMS if transaction RECEIVED and user is well created
		self::sendSmsSecretKey($currentUser);
		
		InvoiceHelper::createInvoiceFromTransaction($transaction);
		
		self::notifyTransactionByMail($transaction,$currentUser);
		
		/*If promoId=PROMO_USER_EXISTING
		  Add credits to user owner of promotion*/
		
		if($transaction->getPromoId()==Constants::PROMO_BOCA_A_BOCA){
		    self::addCreditsToSponsorUser($transaction);
		}
		
	    }

	}
	else{
	     throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
	}

    }
    
    public function notifyTransactionByMail($transaction,$currentUser)
    {
      $destinationCountry = CountryHelper::getCountry($currentUser->getCountryId());
        $subject="n2manager Transaction successfull";
       //Contenido del mensaje
	    $message="User phone: ".$currentUser->getFullPhone()."\n";
            $message.="User Country: ".$destinationCountry->getName()."\n";
	    $message.= "Transaction processed successfully: \n";
	    $message.= " - ProductId:".$transaction->getProductId()."\n";
	    $message.= " - Amount:".$transaction->getAmount();
	    //envio del mail 
			
	    //mailTo
			
	    $headers = "MIME-Version: 1.0\n";
	    $headers .= "Content-type: text/plain; charset=utf-8\n";
	    $headers .= "From: n2manager<contact@n2manager.com>\n";
            
            mail("contact@n2manager.com", $subject, $message, $headers); 
	    
	
    }
       /**
       Gets the last finished transaction of the sponsorUser
       and adds credits(product_credits/10) to it's account
       */
       private function addCreditsToSponsorUser($transaction){
	
	    $promoId = $transaction->getPromoId();
	    $promoCode = $transaction->getPromoCode();

             $userPromo = UserPromoHelper::getUserPromoByPromoCode($promoId,$promoCode);
             $userSponsor= UserHelper::getUserById($userPromo->getUserId());
	     
	     $transactionSponsor=self::getLastTransactionByUser($userSponsor->getId());
	     
	     //Only add credits to Sponsor user if he bought at least one time
	     if($transactionSponsor!=null){
		 $transCurrency=CurrencyHelper::getCurrency($transactionSponsor->getCurrencyId());
		 $product = ProductHelper::getProductByCurrencyCode($transactionSponsor->getProductId() ,$transCurrency->getCode(),$transaction->getGroupId());
		 //update User <balance>: productCredits /10 lo
		 if($product->getFreeCredits()==0)
		     $userSponsor->setBalance($userSponsor->getBalance() + $product->getCreditsNumber()/10);
		 else
		     $userSponsor->setBalance($userSponsor->getBalance() + $product->getFreeCredits());
			  
		UserHelper::updateUser($userSponsor);
	     }
	    
	
    }
    
    public static function closeTransactionSession(){
	
	   //Close session after complete payment
	    //if guest session exists, unset it
	    if(Zend_Session::sessionExists('payment')){
		Zend_Session::start();
		Zend_Session:: namespaceUnset('payment');
		Zend_Session::destroy(true);
		//Zend_Session::expireSessionCookie();
		}
    }
    
      public static function setTransactionGateway($orderId,$gateway){

	try {
	    
	    $currentTransaction = TransactionHelper::getTransactionByOrderId($orderId);
	    $transCurrency=CurrencyHelper::getCurrency($currentTransaction->getCurrencyId());
	    $product = ProductHelper::getProductByCurrencyCode($currentTransaction->getProductId() ,$transCurrency->getCode(),$currentTransaction->getGroupId());
	     
	    $currentTransaction->setGateWay($gateway);
	    $currentTransaction->setStatus(Constants::TRANS_PENDING);
	    $currentTransaction->setStatusDetail("Pending in Gateway");
	    
	    //Updates amount with gateway charges
	    if($gateway==Constants::GATEWAY_PAYPAL){
		$paypalCharge=self::getPaypalCharge($transCurrency,$product->getPrice());
		$totalAmount=number_format($product->getPrice()+$paypalCharge,2, '.', '');
	    }
	    else{
         $laCaixaCharge=$product->getPrice() * Constants::LKXA_EXTRA_CHARGE;
		 $totalAmount=number_format($product->getPrice()+ $laCaixaCharge,2, '.', '');
	    }
	    "totalAmount:".$totalAmount;
	    $currentTransaction->setAmount($totalAmount);
	    
	    TransactionHelper::updateTransaction($currentTransaction);
	
	}catch (Exception $e) {
		throw new Exception("Exception setting gateway");
                 
            } 
	
    }
    
    public static function getPaypalCharge($currency,$price){
	if($currency->getCode()=="JPY"){
	    $paypalCharge=number_format(($price * self::getPaypalChargePercent($currency))+self::getPaypalChargeFix($currency),0, '.', '');
	    }
	else{
	$paypalCharge=($price * self::getPaypalChargePercent($currency))+self::getPaypalChargeFix($currency);
	 }
	 
	
    
       return $paypalCharge;
    }
    
    public static function getPaypalChargePercent($currency){
        switch($currency->getCode()){
	case 'EUR': return Constants::PAYPAL_CHARGE_EUR_PER;break;
	case 'USD': return Constants::PAYPAL_CHARGE_USD_PER;break;
	case 'GBP': return Constants::PAYPAL_CHARGE_GBP_PER;break;
	case 'AUD': return Constants::PAYPAL_CHARGE_AUD_PER;break;
	case 'CAD': return Constants::PAYPAL_CHARGE_CAD_PER;break;
	case 'CHF': return Constants::PAYPAL_CHARGE_CHF_PER;break;
	case 'JPY': return Constants::PAYPAL_CHARGE_JPY_PER;break;
	}
     }
    public static function getPaypalChargeFix($currency){
        switch($currency->getCode()){
	case 'EUR': return Constants::PAYPAL_CHARGE_EUR_FIX;break;
	case 'USD': return Constants::PAYPAL_CHARGE_USD_FIX;break;
	case 'GBP': return Constants::PAYPAL_CHARGE_GBP_FIX;break;
	case 'AUD': return Constants::PAYPAL_CHARGE_AUD_FIX;break;
	case 'CAD': return Constants::PAYPAL_CHARGE_CAD_FIX;break;
	case 'CHF': return Constants::PAYPAL_CHARGE_CHF_FIX;break;
	case 'JPY': return Constants::PAYPAL_CHARGE_JPY_FIX;break;
	}
     }
    
   
    
}
