var $secretPass;
var $usernameEncoded;
var $userCredits;
var $userPhones;
var $keepMeLogged;
var $device_mac_address;


$('#login-register').live('pageinit',function(event){
		   
	$('.scrollTo').click(function(){
	    $('html, body').animate({
	        scrollTop: $( $(this).attr('href') ).offset().top
	    }, 500);
	    return false;
	});

    $("#counterSpoorers").flipCounter({imagePath:"images/flipCounter-medium.png"});
    $("#counterSpoorers").flipCounter(
        "startAnimation", // scroll counter from the current number to the specified number
        {
                number: 0, // the number we want to scroll from
                end_number: 22156, // the number we want the counter to scroll to
                easing: jQuery.easing.easeOutCubic, // this easing function to apply to the scroll.
                duration: 3000, // number of ms animation should take to complete
               
        }
);
      $("#counterDonations").flipCounter({imagePath:"images/flipCounter-medium.png",numFractionalDigits:2,numIntegralDigits:0});
      $("#counterDonations").flipCounter(
        "startAnimation", // scroll counter from the current number to the specified number
        {
                number: 0.0, // the number we want to scroll from
                end_number: 1771.82, // the number we want the counter to scroll to
                easing: jQuery.easing.easeOutCubic, // this easing function to apply to the scroll.
                duration: 3000, // number of ms animation should take to complete
               
        }
);
                
//	var q = queryString.parse(location.search);
// set the `row` property
//q.lang = "es";
// convert the object to a query string
// and overwrite the existing query string
//location = queryString.stringify(q);

	 if($localStorage.getItem("user_id")!=null && $localStorage.getItem("user_id")!='' && getLocalStorageItem("userpass")!=null && getLocalStorageItem("userpass")!=''){
	    $.mobile.changePage("#conversations", { transition: "none"});
	 }
});

$('#login-register #submitLogin').live('click',function(event){

var $validateFormData=true;

if ($validateFormData) {
	$.mobile.showPageLoadingMsg();
	var $strUrl=$domain+$rootFolder+"users/recover";
	
	//get form params
	var $username=$('#login-register').find('#login_name').val();
	var $password=$('#login-register').find('#login_pass').val();
	$keepMeLogged=$('#login-register').find('#keep_me_logged').prop("checked");
	
	 if($keepMeLogged==false)
		 $localStorage=sessionStorage;
	 
	 else
		 $localStorage=localStorage;
		
	var isFormDataValid = validateRegisterFormData($username,$password,$password);
	if (isFormDataValid) {
	$.base64.is_unicode = true;
	$usernameEncoded = $.base64.encode($username);
	
	$secretPass = Crypto.MD5($password, { asString: false });
	
	if(isNaN($username)){
		//mail
		if($username.indexOf("@") !== -1)
			param="mail=";
		else
			param="user_id=";
	}else{
		param="fullphone=";
	}
	$device_mac_address="web"+new Date().getTime();
	
	var params=param+$username+"&password="+$password+"&mac_address="+$device_mac_address;
	
    httpAuthRequest($usernameEncoded,$secretPass,$strUrl,params,'','POST','html',null,loginCallback,loginCallBackError);
	}
}

});

var loginCallback = function(result){
	   //save user data in localstorage for future use
	   resultValues=result.split(",");

		if(resultValues[0]=="OK" && resultValues[1] != null){
			$localStorage.setItem("user_id",resultValues[1]);
			 setLocalStorageItem("im_user_key",resultValues[2]);
			 setLocalStorageItem("userpass",$secretPass);
			 setLocalStorageItem("mac_address",$device_mac_address);
			 localStorage.setItem("keep_me_logged",$keepMeLogged);
			 
			 var $user = new User(resultValues[1],resultValues[3],resultValues[4],resultValues[5]);
			 setLocalStorageItem("user",JSON.stringify($user));

		}
		
	   $.mobile.changePage("#conversations", {transition: "none"});
	   $.mobile.hidePageLoadingMsg();
	   
	   /**Add userName on options logout*/
	    $.base64.is_unicode = true;
		//$usernameDecoded = $.base64.decode(localStorage.getItem("user_id"));
	    $usernameDecoded = $localStorage.getItem("user_id");
	   
	    if( resultValues[3]!="not specified" || resultValues[3]!=null) {
	    	$("#username").html(resultValues[3]);
		      $("#pin").html(""+$localStorage.getItem("user_id")+" | ");
		    
	    }else{
	    	$("#username").html(localStorage.getItem("user_id"));
	    }
		 
		$("#credits").html(resultValues[5]);
	
}
var loginCallBackError = function(data){
		notify('info',"Incorrect User or password");
		$.mobile.hidePageLoadingMsg();
	    throw new Error("Incorrect User or password");
 
}
