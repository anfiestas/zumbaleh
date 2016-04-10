<?php
require_once 'Helper/CurrencyHelper.php';
require_once 'Helper/CountryHelper.php';
require_once 'Helper/ProductHelper.php';
require_once 'Helper/PhoneNumberHelper.php';
require_once 'Helper/UserHelper.php';
require_once 'Helper/UserPromoHelper.php';
require_once 'Helper/TransactionHelper.php';
require_once 'Objects/Country.php';
require_once 'Objects/Transaction.php';
require_once 'Objects/User.php';
require_once 'Objects/Product.php';
require_once 'Objects/Constants.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Session.php';

class CreditsController extends Zend_Controller_Action
{

    public function init()
    {
        //$this->_helper->viewRenderer->setNoRender(false);
    }
	
    public function selectionAction()
    {   $translate=Zend_Registry::get('Zend_Translate');
      
	//get payment values from POST form
	$currentPayment=($this->_getParam('payment')!=null)?$this->_getParam('payment'):null;
	$nextButton=$this->_getParam('next');
	$validatePromoButton=$this->_getParam('validate_promo');
	$currencyIdPost=null;
	$productIdPost=null;
	$phoneNumberPost=null;
	$userCurrency=null;
	$countryIdPost=null;
	$groupIdPost = null;
	$confirmPhonePost=FALSE;
	$readedConditonsPost=FALSE;
	$validPromoCode=FALSE;
	$promoCodePost = $this->_getParam('promo');
	$promo=null;
	$promoId=Constants::PROMO_NOTHING;
	$userPromoCode=null;
	//Form values
	 if($currentPayment!=null){
	 if(array_key_exists('country_id', $currentPayment)){$countryIdPost = $currentPayment['country_id'];}
	//delete spaces from phone
	 if(array_key_exists('phone_number', $currentPayment)){$phoneNumberPost =  str_replace(" ", "", $currentPayment['phone_number']);}
	 if(array_key_exists('currency', $currentPayment)){$currencyIdPost =  $currentPayment['currency'];}
	 if(array_key_exists('group', $currentPayment)){$groupIdPost =  $currentPayment['group'];}
	 if(array_key_exists('product', $currentPayment)){$productIdPost =  $currentPayment['product'];}
	 if(array_key_exists('confirm_phone', $currentPayment)){$confirmPhonePost =  $currentPayment['confirm_phone'];}
	 if(array_key_exists('readed_condicions', $currentPayment)){$readedConditonsPost =  $currentPayment['readed_condicions'];}
	 if(array_key_exists('promo_code', $currentPayment)){if($promoCodePost==null){$promoCodePost =  $currentPayment['promo_code'];}}
	
	}
	
	//set view Parameters
	
	if($currencyIdPost==null)
	     $currencyIdPost='EUR';
        if($groupIdPost==null)
	     $groupIdPost= Constants::GROUP_1;//TODO if IP from europe ->group1 else group2
	   
	//get country list
	$this->view->countryList = CountryHelper::getAll();
	
	//get Currency list
	$this->view->currencyList = CurrencyHelper::getAll();
	
	
        $userCurrency=CurrencyHelper::getCurrencyByCode($currencyIdPost);
	
	//Set view values
	$this->view->countryId=$countryIdPost;
	$this->view->product=$productIdPost;
	$this->view->phoneNumber=$phoneNumberPost;
	$this->view->userCurrency=$userCurrency;
	$this->view->promoCode=$promoCodePost;
	$this->view->groupSelected=$groupIdPost;
	$this->view->groupList= array(Constants::GROUP_1 => "Group 1 - Europe",Constants::GROUP_2 => "Group2 - Rest of the world");
	//Find user by fullPhone on system
	$country = CountryHelper::getCountry($countryIdPost);
	if($country!=null){
	    //TODO if user ads national code normally
	    //$phoneNumberPost = PhoneNumberHelper::removeNationalPrefix($phoneNumberPost,$country);
	    //$this->view->phoneNumber=$phoneNumberPost;
	    
	    $fullPhone=Constants::IDD_PREFIX.$country->getCountryCode().$phoneNumberPost;

	    $user = UserHelper::getUser($fullPhone);
	    
	    //Select promo "existingUserPromo" because user already exist on system
	    if ($user!=null && $user->getFullPhone()==$fullPhone){
		$promoId=Constants::PROMO_EXISTING_USER;
		
		$userPromo=UserPromoHelper::getPromoCodeByUser($user->getId(),Constants::PROMO_BOCA_A_BOCA);
		$userPromoCode = $userPromo->getPromoCode();
	    }
	    
	}
	
	//Check if Code of boca a boca promo it's OK
	if($promoCodePost!=null && $promoCodePost!=$translate->_("your_code_here") && $promoId!=Constants::PROMO_EXISTING_USER){
	    
	    if(UserPromoHelper::isPromoCodeValid(Constants::PROMO_BOCA_A_BOCA,$promoCodePost)==true){
		$promoId=Constants::PROMO_BOCA_A_BOCA;
		$validPromoCode=TRUE;
	    }
	}
	
	//get product list
	$this->view->productList = ProductHelper::getProductsByCurrencyCodeAndPromo($currencyIdPost,$promoId,$groupIdPost);
	
	$this->view->promoId=$promoId;
	$this->view->userPromoCode=$userPromoCode;
	$this->view->promoURL=$this->getFrontController()->getBaseUrl().'/promosms/'.$userPromoCode;
	$this->view->host=$this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
	//If next button was clicked we validate and go to next action
	if($nextButton!=null){
	    $validation=TRUE;
	    if(self::validatePhoneNumber($phoneNumberPost)==FALSE){
		$this->view->errorPhoneMessage="errorPhoneMessage";$validation=FALSE;
	    }
	    if($confirmPhonePost==FALSE){
	        $this->view->errorConfirmPhoneMessage="errorConfirmPhoneMessage";$validation=FALSE;
	    }
	    if($readedConditonsPost==FALSE){
	        $this->view->errorReadedConditionsMessage="errorReadedConditionsMessage";$validation=FALSE;
	    }
	     
	    
	    if($validation){
		//$this->getRequest()->setParam('payment[phone_number]', $phoneNumberPost);
		$this->_forward('confirmation', null);

	    }
	   
	}
	else if($validatePromoButton!=null){
	
	if($validPromoCode==FALSE){
	        $this->view->errorCodeMessage="errorCodeMessage";$validation=FALSE;
	    }
	}
	Header( "Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT" );
	Header( "Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT" );
	Header( "Cache-Control: no-store, no-cache, must-revalidate" ); // HTTP/1.1
	Header( "Cache-Control: post-check=0, pre-check=0", FALSE );
	Header( "Pragma: no-cache" ); // HTTP/1.0 
	
    }
	
    public function confirmationAction()
    {
        $translate=Zend_Registry::get('Zend_Translate');
	//get payment values from POST form
	$currentPayment=$this->_getParam('payment');
	$countryIdPost   =  null;
	$phoneNumberPost =  null;
	$currencyIdPost  =  null;
	$productIdPost   =  null;
	$groupIdPost = null;
	$promoCodePost   =  null;
	$promoId         =  null;
	
	 if(array_key_exists('country_id', $currentPayment)){$countryIdPost = $currentPayment['country_id'];}
	 if(array_key_exists('phone_number', $currentPayment)){$phoneNumberPost =  str_replace(" ", "", $currentPayment['phone_number']);}
	 if(array_key_exists('currency', $currentPayment)){$currencyIdPost =  $currentPayment['currency'];}
	 if(array_key_exists('product', $currentPayment)){$productIdPost =  $currentPayment['product'];}
	 if(array_key_exists('promo_code', $currentPayment)){$promoCodePost =  $currentPayment['promo_code'];}
	 if(array_key_exists('group', $currentPayment)){$groupIdPost =  $currentPayment['group'];}
	 
	 $promoId=$this->view->promoId;
	
	//stores payment info in Session
	Zend_Session::start();
	$paymentSession = new Zend_Session_Namespace('payment');

	    if (!isset($defaultNamespace->initialized)) {
	       Zend_Session::regenerateId();
	       $defaultNamespace->initialized = true;
	    }
		
	    //30 minutes Session expirantion
	    $paymentSession->setExpirationSeconds(60*30);
	
	//getCurrency
	$userCurrency=CurrencyHelper::getCurrencyByCode($currencyIdPost);
	
	//get product and price for currency
	$product = ProductHelper::getProductByCurrencyCode($productIdPost ,$currencyIdPost,$groupIdPost);
	//get Country Code
	$country = CountryHelper::getCountry($countryIdPost);
	
	$noPrefixShortPhone=PhoneNumberHelper::removeNationalPrefix($phoneNumberPost,$country);
	//transform to international phone number
	$fullPhoneNumber=$country->getCountryCode().$noPrefixShortPhone;
	
	$locale = new Zend_Locale();
	//Prepare LaCaixa form
	//Constant commerce values
	$url_tpv=Constants::LKXA_URL;
	$key=Constants::LKXA_PRIVATE_KEY;
	$name='n2bolsa';
	$merchantName='n2manager SMS';
	$code=Constants::LKXA_MERCHANT_PRIVATE_CODE;
	$terminal='001';
	$orderId= date('ymdHis');//sprintf("%012d", 2);//str_replace(".","",str_replace(" ","",microtime()));
	$laCaixaCharge=$product->getPrice() * Constants::LKXA_EXTRA_CHARGE;
	$amountLaCaixaFormat=number_format($product->getPrice()+$laCaixaCharge,2, '.', '')*100;
	$currency='978';
	$transactionType='0';
	$merchantResponseURL='http://'.Constants::HOST_URL.'transactions/'.$orderId;
	$userOkResponseURL='http://'.Constants::HOST_URL.$translate->getLocale().'/transactions/ok';
	$userErrorResponseURL='http://'.Constants::HOST_URL.$translate->getLocale().'/transactions/error';
	$producto='n2manager credits';
	
	// Compute hash to sign form data
	$message = $amountLaCaixaFormat.$orderId.$code.$currency.$transactionType.$merchantResponseURL.$key;
	$signature = sha1($message,false);

	if($paymentSession->transaction_id==null){
		//Transaction does not exist.Create  new transaction
		$tid=TransactionHelper::createTransaction($noPrefixShortPhone,$product->getPrice(),$product->getCurrencyId(),
							    $country->getId(),$product->getId(),$orderId,Constants::TRANS_STARTED,
							    "Transaction started",null,$promoId,$promoCodePost,$groupIdPost);
		//sets OrderId in Session
		$paymentSession->transaction_id = $tid;
	 }
	 else{
	    //Transaction exist yet. We update transaction only
	    
	    $transaction=TransactionHelper::getTransactionById($paymentSession->transaction_id);
	    
	    if($transaction!=null && $transaction->getStatus()==Constants::TRANS_STARTED){
		
		    $transaction->setOrderId($orderId);
		    $transaction->setShortPhone($noPrefixShortPhone);
		    $transaction->setAmount($product->getPrice());
		    $transaction->setCurrencyId($product->getCurrencyId());
		    $transaction->setCountryId($country->getId());
		    $transaction->setProductId($product->getId());
		    $transaction->setPromoId($promoId);
		    $transaction->setPromoCode($promoCodePost);
		    
		if($transaction!=null)
		     TransactionHelper::updateTransaction($transaction);
	    }
	    else{
		//last transaction finished yet, start a new one
		$tid=TransactionHelper::createTransaction($noPrefixShortPhone,$product->getPrice(),$product->getCurrencyId(),
							    $country->getId(),$product->getId(),$orderId,Constants::TRANS_STARTED,
							    "Transaction started",null,$promoId,$promoCodePost,$groupIdPost);
		//sets OrderId in Session
		$paymentSession->transaction_id = $tid;
	    }
	 }
	 
	 
	//Insert values into View
	$this->view->url_tpv=$url_tpv;
	$this->view->amountLaCaixaFormat=$amountLaCaixaFormat;
	$this->view->currency=$currency;
	$this->view->order=$orderId;
	$this->view->code=$code;
	$this->view->terminal=$terminal;
	$this->view->transactionType=$transactionType;
	$this->view->merchantResponseURL=$merchantResponseURL;
	$this->view->merchantName=$merchantName;
	$this->view->userOkResponseURL=$userOkResponseURL;
	$this->view->userErrorResponseURL=$userErrorResponseURL;
	$this->view->signature=$signature;
	$this->view->languageCode="002";
	$this->view->phoneNumber=$phoneNumberPost;
	
	$this->view->product=$product;
	$this->view->country=$country;
	$paypalCharge=TransactionHelper::getPaypalCharge($userCurrency,$product->getPrice());
	
	$paypalTotal=number_format($product->getPrice()+$paypalCharge,2, '.', '');
	$laCaixaTotal=number_format($product->getPrice()+$laCaixaCharge,2, '.', '');
	
	$this->view->totalPriceByCard=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      $laCaixaTotal,
									      $userCurrency->getHtmlSymbol());
	$this->view->totalPriceByPaypal=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      $paypalTotal,
									      $userCurrency->getHtmlSymbol());
	
	$this->view->paypalCharge=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      number_format($paypalCharge,2, '.', ''),
									      $userCurrency->getHtmlSymbol());
	$this->view->laCaixaCharge=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      number_format($laCaixaCharge,2, '.', ''),
									      $userCurrency->getHtmlSymbol());
	$this->view->userCurrency=$userCurrency;
    }
    public function termsAction()
    {
	$this->_helper->layout->disableLayout();
    }
    
    public function coverageAction()
    {
	$this->_helper->layout->disableLayout();
	
	//get country list
	$this->view->countryList = CountryHelper::getAll();
	
    }
    
    public function paybycardAction(){
	
    //updates status of transaction to payment by card
   
    }
    
    public function errorAction(){
	
    //updates status of transaction to payment by card
   
    }
    
    public function receivedAction(){
	
    //updates status of transaction to payment by card
   
    }
    
    private  function validatePhoneNumber($phoneNumberPost){
	
	$validator = new Zend_Validate_Regex('/^[0-9]+$/');
	//^[0-9]+$
	$valid=$validator->isValid($phoneNumberPost);
	return $valid;
    }
    
   
    

}

