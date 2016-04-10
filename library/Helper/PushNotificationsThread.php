<?php
require_once 'Objects/Constants.php';

class PushNotificationsHelper extends Thread{
    
        
     /**
     * send notification to Google Cloud Messaging
     *
     * @param string $tokens  notification tokens set by client
     * @param array  $messge struct message
     *                       - message => message text
     *
     * @return boolean false if faillure
     */
      public function __construct($destId,$destPin,$tokens, $message) {
          $this->destId=$destId;
          $this->destPin=$destPin;
          $this->tokens=$tokens;
          $this->message=$message;
      }
        public function run(){
        $writer = new Zend_Log_Writer_Stream('../private/logs/notifications.log');
        $logger = new Zend_Log($writer);

        $fields = array(
            'registration_ids' => $tokens,
            'data'             => array("message" => $message),
        );

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $headers = array( 
                        'Authorization: key=' . Constants::GCM_API_KEY,
                        'Content-Type: application/json'
                        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt( $ch, CURLOPT_TIMEOUT,SP_TIMEOUT);

        // Execute post
        $result = curl_exec($ch);

        curl_close($ch);
        
        $logger->info("UserInfo: ".$destId."-".$destPin." | message: ".$message." | resultPush: ".$result);

        return $result;

    }

    
    
}