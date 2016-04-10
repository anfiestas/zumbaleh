<?php
interface ISmsProvider
{
     public function getProviderId();
          
     /*sendSms
      return: cod_error,credits,messageID
     */
    public function sendSms($from,$to,$text);
    
     /*updateSmsStatus
      return: cod_error,status,timeStamp,messageID
     */
    public function updateSmsStatus($messageBroadcast,$status);
    
}  

?>
