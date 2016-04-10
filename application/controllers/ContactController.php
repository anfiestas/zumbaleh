<?php
require_once 'Helper/CountryHelper.php';
require_once 'ServicesRest/ServicesRest.php';
require_once 'Zend/Rest/Server.php';

class ContactController extends Zend_Controller_Action
{
protected $server;
	
	
   public function init()
    {
       

    }
    public function indexAction()
    {
	 
	 
    }
    
    public function formAction()
    {
        $currentContact=($this->_getParam('contact')!=null)?$this->_getParam('contact'):null;
	$sendButton=$this->_getParam('send');
        $namePost=null;
	$mailPost=null;
	$countryPost=null;
	$messagePost=null;
        
      if($currentContact!=null){
	 if(array_key_exists('name', $currentContact)){$namePost = $currentContact['name'];}
	 if(array_key_exists('mail', $currentContact)){$mailPost = $currentContact['mail'];}
	 if(array_key_exists('country', $currentContact)){$countryPost =  $currentContact['country'];}
	 if(array_key_exists('message', $currentContact)){$messagePost =  $currentContact['message'];}
	
	}
        
        //Set view values
	$this->view->name=$namePost;
	$this->view->mail=$mailPost;
	$this->view->country=$countryPost;
	$this->view->message=$messagePost;
        
	//get country list
	$this->view->countryList = CountryHelper::getAll(); 
        
        //If next button was clicked we validate and go to next action
	if($sendButton!=null){
	    $validation=TRUE;
            
	    if(self::validateNotEmpty($namePost)==FALSE){
		$this->view->errorName="errorName";$validation=FALSE;
	    }

            if(self::validateNotEmpty($messagePost)==FALSE){
		$this->view->errorEmptyMessage="errorEmptyMessage";$validation=FALSE;
	    }
            
            if(self::validateMail($mailPost)==FALSE && $mailPost!=null){
		$this->view->errorMail="errorMail";$validation=FALSE;
	    }
            
	    if($validation){
		
		$this->_forward('send', null);

	    }
	   
	}
        
	 
    }
    
    public function sendAction()
    {
        //
        $subject="n2manager user Message";
       //Contenido del mensaje
	    $message="From: ".$this->view->mail."\n";
	    $message.="User Name: ".$this->view->name."\n";
            $message.="User Country: ".$this->view->country."\n";
	    $message.=$this->view->message;
	    //envio del mail 
			
	    //mailTo
			
	    $headers = "MIME-Version: 1.0\n";
	    $headers .= "Content-type: text/plain; charset=utf-8\n";
	    $headers .= "From: ".$this->view->name."<".$this->view->mail.">\n";
            
            if (mail("contact@n2manager.com", $subject, $message, $headers)) 
	    {    
		    $this->view->mailSend=TRUE;
            }else{
            
                 $this->view->mailSend=FALSE;
         
            }
     
       
       
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

