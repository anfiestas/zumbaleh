<?php
require_once 'Objects/Constants.php';

class PushNotificationsHelper {
    
        
     /**
     * send notification to Google Cloud Messaging
     *
     * @param string $tokens  notification tokens set by client
     * @param array  $messge struct message
     *                       - message => message text
     *
     * @return boolean false if faillure
     */
      public static function sendGCM($destId,$destPin,$tokens, $message) {

        $writer = new Zend_Log_Writer_Stream('../private/logs/notifications.log');
        $logger = new Zend_Log($writer);

        //Fix - Remove repeated push tokens!!And Duplicated,triplicated messages to same user
        $tokens = array_unique($tokens);

        $fields = array(
            "registration_ids" => $tokens,
            "data"             => array("message" => $message),
        );
        exec('curl --header "Authorization: key='.Constants::GCM_API_KEY.'" --header Content-Type:"application/json" https://android.googleapis.com/gcm/send  -d '.escapeshellarg(json_encode($fields)).' > /dev/null 2>/dev/null &');
        $result="";
        // Set POST variables
        /*$url = 'https://android.googleapis.com/gcm/send';

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
        */
        // curl_close($ch);
        
        $logger->info("UserInfo: ".$destId."-".$destPin." | message: ".$message." | resultPush: ".$result);

        return $result;

    }

    public static function checkTokenValidity($destId,$destPin,$tokens) {

        $writer = new Zend_Log_Writer_Stream('../private/logs/checkTokens.log');
        $logger = new Zend_Log($writer);

        //Fix - Remove repeated push tokens!!And Duplicated,triplicated messages to same user
        $tokens = array_unique($tokens);

        $fields = array(
            "registration_ids" => $tokens
        );
        //exec('curl --header "Authorization: key='.Constants::GCM_API_KEY.'" --header Content-Type:"application/json" https://android.googleapis.com/gcm/send  -d '.escapeshellarg(json_encode($fields)).' > /dev/null 2>/dev/null &');
        //$result="";
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
        
        // curl_close($ch);
        
        $logger->info("UserInfo: ".$destId."-".$destPin." | resultPush: ".$result);

        return $result;

    }

    
    
}