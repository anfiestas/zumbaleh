<?php
//Getting DB connexion
include("includes/connect.php");

$url = "https://www.tm4b.com/client/api/http.php";
  $SMS_DELIVERED      = 0;
  $SMS_SUBMITED       = 1;
  $SMS_FAILED         = 2;
  $SMS_UNKNOWN        = 3;
  $SMS_EXPIRED        = 4;
	
//loop messages from tm4b where status!= delivered
 $result2 = mysql_query("SELECT * FROM message_broadcast WHERE provider_id=1 and status_id=1"); 
    
   while ($row = mysql_fetch_assoc($result2)) {

      //update selected
	  $params="username=n2bolsa&password=n2bolsacrak1&type=check_status&version=2.1&smsid=".urlencode($row['external_id']."-1");
		
		$xmldata = httpPostExecute($url,$params);
		
		  //INTERNAL_SERVER_ERROR   
		 if($xmldata==3)
		 {
		    //internal error tm4b
		    $response="3-provider connection error,0,0";
		 }
		 else
		 {
		    //IF HTTP STATUS 200

		    if(substr($xmldata,0,5)=="error")
		    {
			$replaceThis = array("error(", ")");
			$error=str_replace($replaceThis, "", $xmldata);
			
			$error_provider=explode("|",$error);
			//TODO ERROR TREATMENT
				if($error_provider[0]=="0002")
				{
				    //do error trearment
				    
				}
				throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
				//$errorCode="3-error internal tm4b error";    
		    }
		    else
		    {   
			    $dom=new DomDocument();
				$dom->loadXML($xmldata);
				
				$report=getValueByTagName($dom,"report");
				$report = explode("|", $report);
				$status=$report[0];
				
				switch($status)
				{
				    case "DELIVRD":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_DELIVERED,"Message Delivered");
					break;
				    case "SUBMITD":
					//TODO - define SUBMITDs Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_SUBMITED,"Message Submitted");
					break;
				    case "FAILED":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_FAILED,"Message FAILED");
					break;
				    case "QUEUED":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_UNKNOWN,"Message Status QUEUED");
					break;
				    case "EXPIRED":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_EXPIRED,"Message EXPIRED");
					break;
				    case "UNKNOWN":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_UNKNOWN,"Message UNKNOWN");
					break;
				    case "NEGLCTD":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_FAILED,"Message NEGLCTD");
					break;
			    
				}
			
			
			//$timeStamp=$report[1];
			$errorCode=0;
		    }
		
		}
   
   }
	
   function HttpPostExecute($url,$params)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //gets output
	        
		$xmldata = curl_exec($ch);
		 
		if($xmldata==null){
		  //INTERNAL_PROVIDER_ERROR not delivered
		  $xmldata="3";
		}
		
		curl_close($ch);
                
                return $xmldata;
          
    }
	
   function updateMessageStatusById($id,$newStatus,$statusDetail){
      $query=sprintf("UPDATE message_broadcast SET status_id='%d',status_detail='%s',status_timestamp='%d' WHERE id='%d'", $newStatus,$statusDetail,time(),$id);
      $res6=mysql_query($query);
	 if($res6 == FALSE){echo "ERROR"; print $res6;}
   }
	
        
    function getValueByTagName($dom,$tagName)
    {
        $values = $dom->getElementsByTagName($tagName);
        $value  = $values->item(0)->nodeValue;
        return $value;
    }
?>