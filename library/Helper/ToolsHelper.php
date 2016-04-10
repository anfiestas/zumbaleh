<?php
class ToolsHelper {
    
    
public static function getRandomString($length) {
    
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";   
    $string="";
    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, (strlen($characters))-1)];
    }

    return $string;
}


public static function validateNotEmpty($value){
	
       $validator = new Zend_Validate_NotEmpty();

       $valid = $validator->isValid($value);
       
	return $valid;
    }

}
   /*public static function sendMail($user)
    {
        //
        $subject="Alerta Spammer Spoora";
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
     
       
       
    }*/
?>