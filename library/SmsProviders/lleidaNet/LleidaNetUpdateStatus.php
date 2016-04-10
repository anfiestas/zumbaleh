<?php
//Getting DB connexion
include("includes/connect.php");

$url = "http://sms.lleida.net/xmlapi/smsgw.cgi";
  $SMS_DELIVERED      = 0;
  $SMS_SUBMITED       = 1;
  $SMS_FAILED         = 2;
  $SMS_UNKNOWN        = 3;
  $SMS_EXPIRED        = 4;
	
//loop messages from tm4b where status!= delivered
 $result2 = mysql_query("SELECT * FROM message_broadcast WHERE provider_id=4 and status_id=1"); 
    
   while ($row = mysql_fetch_assoc($result2)) {

      //update selected
                echo "externalId: ".$row['external_id'];
		$xmlRequest = "<query_mt_status><user>TEST317528</user><password>gg2LPX</password><mt_id>".$row['external_id']."</mt_id></query_mt_status>";
				    
		$params="xml=".urlencode($xmlRequest);
		
		$xmldata = httpPostExecute($url,$params);
		
		  //INTERNAL_SERVER_ERROR   
		 if($xmldata==3)
		 {
		    //internal error tm4b
		    $response="3-provider connection error,0,0";
		 }
		 else
		 {
		    
		     //if error
			//throw new Exception("Internal error in Provider",Constants::ERROR_PROVIDER);
		                echo "xmldata:".$xmldata;
			        $dom=new DomDocument();
				$dom->loadXML($xmldata);
				
				$status=getValueByTagName($dom,"status_code");
				echo "status:".$status;
				switch($status)
				{
				    case "1":   
				    case "2":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_UNKNOWN,"Message UNKNOWN");
					break;
				    case "3":
					//TODO - define SUBMITDs Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_SUBMITED,"Message Submitted");
					break;
				    case "4":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_DELIVERED,"Message Delivered");
					break;
				    case "5":
				    case "9":   
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_UNKNOWN,"Message Status QUEUED");
					break;
				    case "6":
				    case "7":
				    case "8":
					//TODO - define DELIVERED Constant
					$messageBroadcast=updateMessageStatusById($row['id'],$SMS_FAILED,"Message FAILED");
					break;
				    case "-13":
				        $messageBroadcast=updateMessageStatusById($row['id'],$SMS_FAILED,"Message Id not valid");
					break;
				}
			
			$errorCode=0;
	
		
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