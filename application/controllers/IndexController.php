<?php
require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Sendmail.php';

class IndexController extends Zend_Controller_Action
{
protected $server;
	
	
   public function init()
    {
       

    }
    public function indexAction()
    {  
         /*$currentContact=($this->_getParam('contact')!=null)?$this->_getParam('contact'):null;
         $mailPost=null;
         $this->view->mailSend="nothing";
         $validation=TRUE;

       if($currentContact!=null){

        if(array_key_exists('mail', $currentContact)){$mailPost = $currentContact['mail'];}

        if(self::validateNotEmpty($mailPost)==FALSE){

            $this->view->error="errorEmptyMail";$validation=FALSE;
        }
            
       if(self::validateMail($mailPost)==FALSE && $mailPost!=null){

             $this->view->error="errorMail";$validation=FALSE;
        }
        if($validation){
      //send mail to us

          $to="n2bolsa@gmail.com";
          $subject="I want to subscribe to spoora mailling list to get latesd news";
          $message="I want to subscribe to spoora mailling list, send me the news to: ".$mailPost;
            //Send mail with ZendMail
         $mail = new Zend_Mail("utf-8");
         $mail->setFrom($mailPost, $mailPost);
         $mail->addTo($to);
         $mail->setSubject($subject);
         $mail->setBodyText($message);
         

         if($mail->send()){
                $this->view->mailSend="TRUE";
            }
        else{
                $this->view->mailSend="FALSE";
            }
            
            
        }
    }
*/
   }



    private  function validateNotEmpty($value){
    
       $validator = new Zend_Validate_NotEmpty();

       $valid = $validator->isValid($value);
       
    return $valid;
    }
    
    private  function validateMail($value){
    
       $validator = new Zend_Validate_EmailAddress();

       $valid = $validator->isValid($value);
       
    return $valid;
    }
 


	

}

