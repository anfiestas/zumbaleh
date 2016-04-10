<?php
require_once 'Http/HttpRequest.php';

    class RoutoTelecomSMS {
           var $user = "";
	   var $pass = "";
	   var $number = "";
	   var $ownnum = "";
	   var $message = "";
	   var $messageId = "";
	   var $type = "";
	   var $model = "";
	   var $op = "";
	   var $bulkId="";
	   var $schedule="";
	   
	   function SetUser($newuser) {
	           $this->user = $newuser;
		   return;
		   }
	   function SetPass($newpass) {
	           $this->pass = $newpass;
		   return;
		   }
	   function SetNumber($newnumber) {        
		   $this->number = $newnumber;
		   return;
		   }
	   function SetOwnNum($newownnum) {
	           $this->ownnum = $newownnum;
		   return;
		   }
	   function SetType($newtype) {
	   	   $this->type = $newtype;
		   return;
	   }
	   function SetModel($newmodel) {
	           $this->model = $newmodel;
		   return;
		   }
	   function SetMessage($newmessage) {
	           $this->message = $newmessage;
		   return;
		   }
	   function SetMessageId($newmessageid) {
	           $this->messageId = $newmessageid;
	       }
	   function SetOp($newop) {
	           $this->op = $newop;
		   return;
		   }
	   function SetBulkId($bulkid) {
	           $this->bulkId = $bulkid;
		   return;
		   }
	   function SetSchedule($schedule) {
	           $this->schedule = $schedule;
		   return;
		   }
	   function MIMEEncode($s) {
	            return base64_encode($s);
		    }
	    
	   function Send() {
	            $Body = "";
		    $Body .= "number=" . $this->number;
		    $Body .= "&user=" . urlencode($this->user);
		    $Body .= "&pass=" . urlencode($this->pass);
		    $Body .= "&message=" . urlencode($this->message);
		    if (strlen($this->messageId))
		    	$Body .= "&mess_id=" . urlencode($this->messageId) . "&delivery=1";
		    if ($this->ownnum != "") $Body .= "&ownnum=" . urlencode($this->ownnum);
		    if ($this->model != "") $Body .= "&model=" . $this->model;
		    if ($this->op != "") $Body .= "&op=" . $this->op;
		    if ($this->type != "") $Body .= "&type=" . $this->type;
		    if ($this->bulkId != "") $Body .= "&bulkid=" . urlencode($this->bulkId). "&delivery=1";
		    if ($this->schedule != "") $Body .= "&schedule=" . urlencode($this->schedule);
		    $Host = "https://smsc5.routotelecom.com/SMSsend";

		    //try to send message	
		    $request = new HttpRequest();
		    $xmldata = $request->httpPostExecute($Host,$Body);
		    return $xmldata;
		    }
	  
    }

