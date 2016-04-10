<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Constants.php';
class PaypalHelper {
        

/** SetExpressCheckout NVP example; last modified 08MAY23.
*
*  Initiate an Express Checkout transaction. 
*/

public static $environment = Constants::PAYPAL_API_ENVIRONMENT;	// sandbox or 'beta-sandbox' or 'live'

/**
 * Send HTTP POST Request
 *
 * @param	string	The API method name
 * @param	string	The POST Message fields in &name=value pair format
 * @return	array	Parsed HTTP Response body
 */
public static function PPHttpPost($methodName_, $nvpStr_) {
	//global $environment;

	/* REAL PARAMETERS Set up your API credentials, PayPal end point, and API version.*/
	$API_UserName = urlencode(Constants::PAYPAL_API_USER);
	$API_Password = urlencode(Constants::PAYPAL_API_PASS);
	$API_Signature = urlencode(Constants::PAYPAL_API_SIGNATURE);
	
	/* TEST PARAMETERS
	$API_UserName  = urlencode(Constants::PAYPAL_API_USER);
	$API_Password  = urlencode(Constants::PAYPAL_API_PASS);
	$API_Signature = urlencode(Constants::PAYPAL_API_SIGNATURE);
	*/
	
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if("sandbox" === self::$environment || "beta-sandbox" === self::$environment) {
		$API_Endpoint = "https://api-3t.".self::$environment.".paypal.com/nvp";
	}
	$version = urlencode('56.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// Get response from the server.
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
}

 public static function checkPaypalResponse($httpParsedResponseAr)
    {
	$status= Constants::TRANS_FAILED;
	$responseArray [] = NULL;
    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	  
	    
	    if("COMPLETED" == strtoupper($httpParsedResponseAr["PAYMENTSTATUS"])){
		$status= Constants::TRANS_RECEIVED;
		$responseMessage= 'Pago recibido correctamente.';
		$errorMessageInLang = 'PayPalPaymentReceived';
	    }
	    else{
	      //Pending why?
	      if("NONE" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - No hay razon';
		$errorMessageInLang = 'PayPalUnknownkError';
	      }
	       if("ADDRESS" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - El usuario no indico direccion de entrega';
		$errorMessageInLang = 'PayPalPaymentErrorAddress';
	      }
	       if("ECHECK" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"]." - El pago está pendiente, ya se hizo a través de una transferencia que aún no ha sido completada.";
		$errorMessageInLang = 'PayPalPaymentErrorTransfer';
	      }
	       if("INTL" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - El pago está pendiente, porque usted tiene una cuenta fuera de los Estados Unidos y no dispone de un sistema de transferencia.
		Manualmente, debe aceptar o negar el pago desde su Resumen de Cuenta.';
		$errorMessageInLang = 'PayPalPaymentErrorInternational';
	      }
	       if("MULTI-CURRENCY" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - No hay razon';
		$errorMessageInLang = 'PayPalUnknownkError';
	      }
	       if("VERIFY" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - No hay razon';
		$errorMessageInLang = 'PayPalUnknownkError';
	      }
	       if("OTHER" == strtoupper($httpParsedResponseAr["PENDINGREASON"])){
		$status= Constants::TRANS_FAILED;
		$responseMessage= $httpParsedResponseAr["REASONCODE"].' - El pago no se ha realizado correctamente';
		$errorMessageInLang = 'PayPalUnknownkError';
	      }
	      
	    }
    
	} else  {
	      $status= Constants::TRANS_FAILED;
	      $responseMessage= 'Error with ACK verification';
	      $errorMessageInLang = 'PayPalErrorACK';
	}
	
    
      $responseArray[0]=$status;
      $responseArray[1]=$responseMessage;
      $responseArray[2]=$errorMessageInLang;
      
     return $responseArray;
      
    }

    
   
    
}