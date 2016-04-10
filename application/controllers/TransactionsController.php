<?php 
require_once 'Xml/XmlParse.php';
require_once 'Router/ProviderRouter.php';
require_once 'Objects/Constants.php';
require_once 'Objects/Transaction.php';
require_once 'Objects/User.php';
require_once 'Helper/TransactionHelper.php';
require_once 'Helper/CurrencyHelper.php';
require_once 'Helper/UserHelper.php';
require_once 'Helper/PaypalHelper.php';
/**
 * Class MessagesController
 */
 
class TransactionsController extends Zend_Rest_Controller {
    
    public function init(){
        
        $this->_helper->viewRenderer->setNoRender(true);
       
    }
    /**
     * indexAction
     */

  public function indexAction()
    {
         $this->getResponse()
            ->appendBody("From indexAction() returning all messages");
    }
    
    
    public function getAction()
    {
	 $this->getResponse()
            ->appendBody("From getAction() returning all messages");
    }
    
    public function postAction()
    {
    

    }
    
    //LKXA Server calls this method via an http post to confirm the payment response 
    public function putAction(){
   
      $dsSignature = $this->_getParam('Ds_Signature');
      $dsResponse  = $this->_getParam('Ds_Response');
      $dsOrderId   = $this->_getParam('Ds_Order');
      $orderId   = $this->_getParam('order_id');
      $gateway   = $this->_getParam('gateway');
      
       try {
	    //update LaCaixa transaction
	    if($dsSignature!=null && $dsSignature!=null && $dsOrderId!=null){
		    $currentTransaction = TransactionHelper::getTransactionByOrderId($dsOrderId);
		    $currentTransaction->setGateWay("LaCaixa");
		    
		    $calcSignature = $this->getLaCaixaResponseSignature();
	
		    $responseArray=TransactionHelper::checkLaCaixaResponse($dsResponse,$dsSignature,$calcSignature);
	
		    TransactionHelper::doAfterPaymentGatewayResponse($currentTransaction,$responseArray);
		     
		    $writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		    $logger = new Zend_Log($writer);
		    $logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - TransactionsController > putAction:\n\t".
				       "TransactionID: ".$currentTransaction->getId()."\n\t".
				       "ResponseMessage: ".$responseArray[1]."\n\t". 
				       "Received Signature: ".$dsSignature."\n\t".
				       "Calculated Signature: ".$calcSignature);
	    }
	    //update transaction gateway
	    elseif($orderId!=null && $gateway!=null){
		TransactionHelper::setTransactionGateway($orderId,$gateway);
		 $this->getResponse()
            ->appendBody("Paypal put");
	    }
	}catch (Exception $e) {

	    $this->getRequest()->setParam('error_code', $e->getCode());
	    $this->getRequest()->setParam('error_message', $e->getMessage());
	    $this->getRequest()->setParam('error_trace', $e->getTraceAsString());
	    $this->_forward('n2sms', 'error');
                 
            } 
    }
    
    public function deleteAction()
    {
     
     //$this->getResponse()
      //     ->appendBody("From postAction() returning all messages");

    }
    
    public function okAction(){
	
      $dsSignature = $this->_getParam('Ds_Signature');
      $dsResponse  = $this->_getParam('Ds_Response');
      $dsOrderId    = $this->_getParam('Ds_Order');
	  
      $this->_helper->viewRenderer->setNoRender(false);
      
       try {
	    $currentTransaction = TransactionHelper::getTransactionByOrderId($dsOrderId);
	    $calcSignature = $this->getLaCaixaResponseSignature();

	    $responseArray=TransactionHelper::checkLaCaixaResponse($dsResponse,$dsSignature,$calcSignature);
	    
	    TransactionHelper::closeTransactionSession();
	    
		
		$userCurrency=CurrencyHelper::getCurrency($currentTransaction->getCurrencyId());
		$product = ProductHelper::getProductByCurrencyCode($currentTransaction->getProductId(),$userCurrency->getCode(),$currentTransaction->getGroupId());
		$country = CountryHelper::getCountry($currentTransaction->getCountryId());
		
		$totalPrice=number_format($currentTransaction->getAmount(),2, '.', '');
		$this->view->transaction = $currentTransaction;
		$this->view->totalPrice=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      $totalPrice,
									      $userCurrency->getHtmlSymbol());
		     
		$this->view->product = $product;
		$this->view->country =$country;
		
		$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
		$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - TransactionsController > okLaCaixaAction:\n\t".
				       "TransactionID: ".$currentTransaction->getId()."\n\t".
				       "ResponseMessage: ".$responseArray[1]."\n\t");
		
		}catch (Exception $e) {
		    $this->getRequest()->setParam('error_code', $e->getCode());
		    $this->getRequest()->setParam('error_message', $e->getMessage());
		    $this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		    $this->_forward('n2sms', 'error');
		     
		} 

	
    }
    
    public function errorAction(){
	
      $dsSignature = $this->_getParam('Ds_Signature');
      $dsResponse  = $this->_getParam('Ds_Response');
      $dsOrderId    = $this->_getParam('Ds_Order');
      $translate=Zend_Registry::get('Zend_Translate'); 	   
      $this->_helper->viewRenderer->setNoRender(false);
      
       try {	
		
	    $calcSignature = $this->getLaCaixaResponseSignature();
	    //get responseMessage
	    $responseArray=TransactionHelper::checkLaCaixaResponse($dsResponse,$dsSignature,$calcSignature);
		$this->view->paymentResponseMessage = $translate->_($responseArray[2]);
			
		$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
		$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - TransactionsController > errorLaCaixaAction:\n\t".
				       "OrderID: ".$dsOrderId ."\n\t".
				       "ResponseMessage: ".$responseArray[1]."\n\t");
		
	    }catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
            } 

	
    }
        
    public function paypalinitAction(){
	
      $orderId = $this->_getParam('order_id');
      $translate=Zend_Registry::get('Zend_Translate');
      $paypalLocale='en_US';
       
       if($translate->getLocale()=='es')
		    $paypalLocale='ES';
       try {
	 TransactionHelper::setTransactionGateway($orderId,"PayPal");
	 $currentTransaction = TransactionHelper::getTransactionByOrderId($orderId);
	 $transCurrency=CurrencyHelper::getCurrency($currentTransaction->getCurrencyId());
	
		// Set request-specific fields
		//format amount with decimal using coma , instead of point .
		$paymentAmount = urlencode(number_format($currentTransaction->getAmount(),2, '.',''));
		if($transCurrency->getCode()=="JPY"){
		    $paymentAmount = urlencode(number_format($currentTransaction->getAmount(),0, '.',''));
		}
		
		$currencyID =urlencode($transCurrency->getCode());	// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		$paymentType = urlencode('Sale');	//Authorization or 'Sale' or 'Order'
		
		$returnURL = 'http://'.Constants::HOST_URL.$translate->getLocale().'/transactions/paypal_process?order_id='.$orderId;
		$cancelURL = 'http://'.Constants::HOST_URL.$translate->getLocale().'/transactions/paypal_cancel?order_id='.$orderId;
		
		
		// Add request-specific fields to the request string.
		$nvpStr = "&Amt=$paymentAmount&ReturnUrl=$returnURL&CANCELURL=$cancelURL&PAYMENTACTION=$paymentType&CURRENCYCODE=$currencyID&NOSHIPPING=1&INVNUM=$orderId&DESC=n2manager SMS credits&LocaleCode=$paypalLocale";
		
		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = PaypalHelper::PPHttpPost('SetExpressCheckout', $nvpStr);
		
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			// Redirect to paypal.com.
			$token = urldecode($httpParsedResponseAr["TOKEN"]);
			$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=$token";
			if("sandbox" === PaypalHelper::$environment || "beta-sandbox" === PaypalHelper::$environment) {
				$payPalURL = "https://www.".PaypalHelper::$environment.".paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=$token";
			}
			//IMPORTANT: this redirects to payPalURL
			header("Location: $payPalURL");
			exit;
		} else  {
			exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
		}

	    }catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
            } 	
    }
    
    public function paypalprocessAction(){
	$this->_helper->viewRenderer->setNoRender(false);
	 
	$orderId = $this->_getParam('order_id');
	$token = $this->_getParam('token');
	$payerID = $this->_getParam('PayerID');
    
       try {
	    $currentTransaction = TransactionHelper::getTransactionByOrderId($orderId);    
		
		$transCurrency=CurrencyHelper::getCurrency($currentTransaction->getCurrencyId());
		$currentTransaction->setGateWay("PayPal");
	
		$paymentType = urlencode("Sale");					// or 'Sale' or 'Order'
		$paymentAmount = number_format($currentTransaction->getAmount(),2, '.', ',');
		$currencyID = urlencode($transCurrency->getCode());						// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// Add request-specific fields to the request string.
		$nvpStr = "&TOKEN=$token&PAYERID=$payerID&PAYMENTACTION=$paymentType&AMT=$paymentAmount&CURRENCYCODE=$currencyID&INVNUM=$orderId";
		$httpParsedResponseAr = PaypalHelper::PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
		
		$responseArray=PaypalHelper::checkPaypalResponse($httpParsedResponseAr);
		
		TransactionHelper::doAfterPaymentGatewayResponse($currentTransaction,$responseArray);
		
		
		$userCurrency=CurrencyHelper::getCurrency($currentTransaction->getCurrencyId());
		$product = ProductHelper::getProductByCurrencyCode($currentTransaction->getProductId(),$userCurrency->getCode(),$currentTransaction->getGroupId());
		$country = CountryHelper::getCountry($currentTransaction->getCountryId());
		
		$totalPrice=number_format($currentTransaction->getAmount(),2, '.', '');
		$this->view->transaction = $currentTransaction;
		$this->view->totalPrice=ProductHelper::getPriceInCurrencyFormat($product->getCurrencyId(),
									      $totalPrice,
									      $userCurrency->getHtmlSymbol());
		     
		$this->view->paymentResponseMessage = $responseArray[2];
		$this->view->product = $product;
		$this->view->country =$country;
		    
		if($responseArray[0]==Constants::TRANS_RECEIVED){TransactionHelper::closeTransactionSession(); }
		
	    $writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
		$logger->info($this->getRequest()->getServer('REMOTE_ADDR')." - TransactionsController > paypalprocessAction:\n\t".
				       "TransactionID: ".$currentTransaction->getId()."\n\t".
				       "ResponseMessage: ".$responseArray[1]."\n\t");
	   
	}catch (Exception $e) {
		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
            } 
    }
    
    public function paypalcancelAction(){
	
	$this->_helper->viewRenderer->setNoRender(false);
	$orderId = $this->_getParam('order_id');
	
	 try {
	$currentTransaction = TransactionHelper::getTransactionByOrderId($orderId);
	$currentTransaction->setGateWay("PayPal");
	$currentTransaction->setStatus(Constants::TRANS_FAILED);
	$currentTransaction->setStatusDetail("Cancel - Transaction cancelled by user");
	
	TransactionHelper::updateTransaction($currentTransaction);
	$this->view->paymentOrderId = $orderId;
	
	   
	}catch (Exception $e) {

		$this->getRequest()->setParam('error_code', $e->getCode());
		$this->getRequest()->setParam('error_message', $e->getMessage());
		$this->getRequest()->setParam('error_trace', $e->getTraceAsString());
		$this->_forward('n2sms', 'error');
                 
            } 
	
    }
    
  
    
    private function getLaCaixaResponseSignature(){
	
	$dsOrderId		= $this->_getParam('Ds_Order');
	$dsAmount		= $this->_getParam('Ds_Amount');
	$dsCurrency		= $this->_getParam('Ds_Currency');
	$dsResponse		= $this->_getParam('Ds_Response');
	$key			= Constants::LKXA_PRIVATE_KEY;
	$code			= Constants::LKXA_MERCHANT_PRIVATE_CODE;

	  $message = $dsAmount.$dsOrderId.$code.$dsCurrency.$dsResponse.$key;
	  $calcSignature = strtoupper(sha1($message,false));
	  return $calcSignature;
    }

}