<?php
require_once 'Objects/Constants.php';

class MediaHelper {
    
    
    /*Help methods*/
     /*
     php save_mode = On
     safe_mode_exec_dir = /usr/lib/php5/libexec

     Uses  /usr/lib/php5/libexec/zumbnailCreator process to create preview of videos
     */

       public static function doUpload($userPin,$fileName){
        $writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
        $logger = new Zend_Log($writer);
        try{
        
               $upload = new Zend_File_Transfer_Adapter_Http();
              
               //$upload->setDestination("/Applications/XAMPP/xamppfiles/htdocs/zumbaleh/uploads/");

               $upload->setDestination( '../tmp');
               //$upload->setOptions(array('useByteString' => false));
               //$size = $upload->getFileSize();
               //$fileURLName=$userPin."_".time()."_".$size."_".$fileName;
               $fileURLName=$userPin."_".time()."_".$fileName;
               $upload->addFilter('Rename',array('target' => '../tmp/' . $fileURLName));
               if ($upload->receive()) { 
                 
                  $logger->info("--------- UPLOAD RECEIVED  -----".$fileURLName);

               }
               else{
                 $messages = $adapter->getMessages();
                 // TODO log error
                  $response=implode("\n", $messages);
                  $logger->info("--------- UPLOAD ERROR  -----".$response);
                  throw new Exception("Error in file upload ".$response,Constants::ERROR_BAD_REQUEST);

                 $fileURLName=NULL;
                 
               }
             
          

              $logger->info("--------- UPLOAD RECEIVED FINAL  -----".$fileURLName);
            return $fileURLName;

        }catch (Exception $e) {
                
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
                 
        }

    }

     /*Help methods*/
     /*
     php save_mode = On
     safe_mode_exec_dir = /usr/lib/php5/libexec

     Uses  /usr/lib/php5/libexec/zumbnailCreator process to create preview of videos
     */

       public static function doUploadToProfile($userPin,$fileName){
        $writer = new Zend_Log_Writer_Stream('../private/logs/n2sms.log');
        $logger = new Zend_Log($writer);
        try{
        
               $upload = new Zend_File_Transfer_Adapter_Http();
              
               //$upload->setDestination("/Applications/XAMPP/xamppfiles/htdocs/zumbaleh/uploads/");

               $upload->setDestination( '../profiles');
               //$upload->setOptions(array('useByteString' => false));
               //$size = $upload->getFileSize();
               //$fileURLName=$userPin."_".time()."_".$size."_".$fileName;
               $upload->addFilter('Rename',array('target' => '../profiles/'. $fileName,'overwrite' => true));
               if ($upload->receive()) { 
                 
                  $logger->info("--------- UPLOAD RECEIVED  -----".$fileURLName);

               }
               else{
                 $messages = $adapter->getMessages();
                 // TODO log error
                  $response=implode("\n", $messages);
                  $logger->info("--------- UPLOAD ERROR  -----".$response);
                  throw new Exception("Error in file upload ".$response,Constants::ERROR_BAD_REQUEST);

                 $fileName=NULL;
                 
               }

              $logger->info("--------- UPLOAD RECEIVED FINAL  -----".$fileURLName);
            return $fileName;

        }catch (Exception $e) {
                
            throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
                 
        }

    }


    function exec_enabled() {
          $disabled = explode(', ', ini_get('disable_functions'));
          return !in_array('exec', $disabled);
        }
    
    
}