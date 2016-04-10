var $secretPass;
var $usernameEncoded;

$('#login-register #submitRegister,#register #submitRegister').live('click',function(event){
	$.mobile.showPageLoadingMsg();
	var strUrl=$domain+$rootFolder+"rest/users";
	$page=$.mobile.activePage;
	//get form params
	var $username=$page.find('#register_name').val();
	var $password=$page.find('#register_pass').val();
	var $password2=$page.find('#register_pass_2').val();
	
	var isFormDataValid = validateRegisterFormData($username,$password,$password2);
	
	if (isFormDataValid) {
		$.base64.is_unicode = true;
		$usernameEncoded = $.base64.encode($username);
		
		$secretPass = Crypto.MD5($password, { asString: false });
		
		$passEncoded = $.base64.encode($secretPass);
		var params="mail="+$usernameEncoded+"&password="+$passEncoded;
		
	    httpAuthRequest($usernameEncoded,$secretPass,strUrl,params,'','POST','json',null,registerCallback,registerCallBackError);

	}

});

var validateRegisterFormData = function($username,$password,$password2){
	if($username=="" || $username==null){
		notify('error','PIN cannot be empty');
		 $.mobile.hidePageLoadingMsg();
	  return false;
	}
	if($password=="" || $password==null){
		notify('error','User Password cannot be empty');
		 $.mobile.hidePageLoadingMsg();
	return false;
	}
	if($password!=$password2){
		notify('error','Introduced passwords are different');
		 $.mobile.hidePageLoadingMsg();
	return false;
	}

  return true;

}


var registerCallback = function(data){
	   //save data in localstorage for future use
	   $localStorage.getItem("user_id",$usernameEncoded);
	   setLocalStorageItem("userpass",$secretPass);

	   $.mobile.changePage("#sms-send", { transition: "none"});

	   $.mobile.hidePageLoadingMsg();
	
}
var registerCallBackError = function(result){
	 $.mobile.hidePageLoadingMsg();
	 
	switch(result.status){
		case 0: notify('error','Error - It seems that you are OFFLINE');break;
		default: notify('error','Error sending request');
	}
	
}