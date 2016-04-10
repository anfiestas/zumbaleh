<?php
require_once 'Objects/Constants.php';
require_once 'Helper/UserHelper.php';
/**
 * Front Controller plug in to set up the action stack.
 *
 */
class Plugin_HttpAuthenticator extends Zend_Controller_Plugin_Abstract
{
	
    public function dispatchLoopStartup(Zend_Controller_Request_Http $request)
    {   
		$writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
		$logger = new Zend_Log($writer);
		
		 
	    try {
		    $controllerName = strtolower($request->getControllerName());
		    //$actionName = strtolower($request->getActionName());
		    $moduleName = $request->getModuleName();
            $METHOD= $request->getMethod();
            $ACTION= $request->getActionName();

		     $logger->info("------------------------Server Calculation MAC-------------------------------------");
		     $logger->info("HttpAuthenticator >> ".$request->getServer('REMOTE_ADDR')." URI:".$request->getRequestUri(). " METHOD:".$METHOD);
		     $logger->info("Authorizations: ".$request->getHeader("Authorizations"));
		     $logger->info("X-Authorizations: ".$request->getHeader("X-Authorizations"));
		     
		    //Make authentication only for n2manager REST and IM module
			/*
		    if ($moduleName=="default" && ($controllerName=="users" || $controllerName=="messages")){
		
			//getHeader Date
			$headerDate=$request->getHeader("Date");
			
			//getHeader Authorization: n2sms telephone base64Encoded : APPLY_SIGNATURE : USER_SIGNATURE
						//n2sms MDAzNDY1MTYyMDA1Mg==:c54813f3e70d6c20b95de6fe657377e3:n5REfa6QegCsYNOttfM0VepUd9U=
			$headerAuthorization=$request->getHeader("Authorizations");
			
			$headerArray=explode(":",$headerAuthorization);
			
			if($headerArray[0]==null || $headerArray[1]==null)
			   throw new Exception("Authentication error",Constants::ERROR_AUTHENTICATION_FAILED);
			$firstAuthArray=explode(" ",$headerArray[0]);
			
			$APPLY_NAME=$firstAuthArray[0];
			
			$TELEPHONE=base64_decode($firstAuthArray[1]);
			
			//get APPLY_KEY= md5("Polvorones La Estepa")
			$APPLY_SIGNATURE=$headerArray[1];
			
			$USER_SIGNATURE=$headerArray[2];
			
			$USER_SECRET_KEY = UserHelper::getUser($TELEPHONE)->getSecretKey();
			
			//get APPLY_KEY= md5("Polvorones La Estepa")
			$APPLY_SECRET_KEY= Constants::SECRET_APPLY_KEY;
			
			
			$URI= $request->getRequestUri();
			
			//Calculate APPLY_HMAC (Message Access Code ) using SHA1 algorithm
			$canonical_string1 = $headerDate .":" .$URI. ":" .$METHOD;
			$b64_mac_Apply = base64_encode(hash_hmac('sha1', $canonical_string1, $APPLY_SECRET_KEY,true));
			    
			//Calculate USER_HMAC (Message Access Code ) using SHA1 algorithm
			$canonical_string2 = $headerDate .":". $APPLY_NAME .":" . $TELEPHONE;
			$b64_mac_user = base64_encode(hash_hmac('sha1', $canonical_string2, $USER_SECRET_KEY,true));
			
			$authentication = "n2sms ".$firstAuthArray[1].":".$b64_mac_Apply.":" . $b64_mac_user;
			$logger->info($request->getServer('REMOTE_ADDR')." - HttpAuthentication: "."header:".$headerAuthorization." date:".$headerDate." URI:".$URI. " METHOD:".$METHOD);
			$logger->info("b64_mac_Apply:".$b64_mac_Apply." b64_mac_user:".$b64_mac_user);
			
			//compare HMAC with SIGNATURE
			if($b64_mac_Apply!=$APPLY_SIGNATURE || $b64_mac_user!=$USER_SIGNATURE){
			 throw new Exception("Authentication error",Constants::ERROR_AUTHENTICATION_FAILED);
			 
			}
			else{
			    
			}
			
		    }
		    else  */if ($moduleName=="imservices" && ($ACTION!="register") && ($ACTION!="recover") && ($ACTION!="put_my_thumb") && ($ACTION!="get_my_thumb") && ($controllerName !="media")){
		  
                  //getHeader Date
				$headerDate=$request->getHeader("Date");
				if($headerDate==null)
					$headerDate=$request->getHeader("X-Date");
				
				/*** HEADER-Definition: Authorization: zumbaleh! telephone base64Encoded : APPLY_SIGNATURE : USER_SIGNATURE
				**                      zumbaleh! MDAzNDY1MTYyMDA1Mg==:c54813f3e70d6c20b95de6fe657377e3:n5REfa6QegCsYNOttfM0VepUd9U=
				*/
				
			    $headerAuthorization=$request->getHeader("Authorizations");
				if($headerAuthorization==null)
					$headerAuthorization=$request->getHeader("X-Authorizations");

				$logger->info("headeeeerAuth:".$headerAuthorization);
				$headerArray=explode(":",$headerAuthorization);
				if($headerArray[0]==null || $headerArray[1]==null)
				   throw new Exception("Authentication error",Constants::ERROR_AUTHENTICATION_FAILED);
				$firstAuthArray=explode(" ",$headerArray[0]);
				
				//$APPLY_NAME="zumbaleh!";
				$APPLY_NAME=$firstAuthArray[0];
									
				$PIN=base64_decode($firstAuthArray[1]);

				//get APPLY_KEY= md5("Polvorones La Estepa")
				$APPLY_SIGNATURE=$headerArray[1];
				
				$USER_SIGNATURE=$headerArray[2];

				$USER_SECRET_KEY = UserHelper::getUserByPin($PIN)->getImSecretKey();
                 
				//get APPLY_KEY= md5("Polvorones La Estepa")
				$APPLY_SECRET_KEY= Constants::SECRET_APPLY_KEY;
				
				
				$URI= $request->getRequestUri();
				
				//Calculate APPLY_HMAC (Message Access Code ) using SHA1 algorithm
				$canonical_string1 = $headerDate .":" .$URI. ":" .$METHOD;
				$b64_mac_Apply = base64_encode(hash_hmac('sha1', $canonical_string1, $APPLY_SECRET_KEY,true)); 
				//Calculate USER_HMAC (Message Access Code ) using SHA1 algorithm
				$canonical_string2 = $headerDate .":". $APPLY_NAME .":" . $PIN;
				$b64_mac_user = base64_encode(hash_hmac('sha1', $canonical_string2, $USER_SECRET_KEY,true));
				
				$authentication = "zumbaleh! ".$firstAuthArray[1].":".$b64_mac_Apply.":" . $b64_mac_user;
				
				$logger->info($request->getServer('REMOTE_ADDR')." - HttpAuthentication: "."header:".$headerAuthorization." date:".$headerDate." URI:".$URI. " METHOD:".$METHOD);
				$logger->info("canonicalString1=".$canonical_string1);
				$logger->info("b64_mac_Apply:".$b64_mac_Apply." b64_mac_user:".$b64_mac_user);
				//compare HMAC with SIGNATURE
				if($b64_mac_Apply!=$APPLY_SIGNATURE || $b64_mac_user!=$USER_SIGNATURE){
					$logger->info("ERROR,Authentication error");
				 $this->getResponse()->appendBody("ERROR,Authentication error");
				 throw new Exception("Authentication error",Constants::ERROR_AUTHENTICATION_FAILED);
				 
				}
				else{
				    
				}

		    }
		    
		    $logger->info("-------------------------------------------------------------------------");
		} catch (Exception $e) {
		    
		    $request->setParam('error_code', $e->getCode());
		    $request->setParam('error_message', $e->getMessage().$request->getRequestUri());
		    $request->setParam('error_trace', $e->getTraceAsString());
		    
		    $request->setControllerName('error');
		    $request->setActionName('n2sms');
		    
		   
		} 
	
	
    }
}