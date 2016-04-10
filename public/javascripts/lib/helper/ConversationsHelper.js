function ConversationsHelper() {

}

// sync contacts with server
ConversationsHelper.prototype.getMessages = function() {

	if ($localStorage.getItem("user_id") != null
			&& getLocalStorageItem("userpass") != null) {
		var strUrl = $domain + $rootFolder + "rest/messages";
		httpAuthRequest($localStorage.getItem("user_id"), getLocalStorageItem("im_user_key"), strUrl, '', '', 'GET', 'json',
				null,showMessageListCallback, showLastStoredList);
	}

};

/** ****getMessages CallBacks**** */
var showMessageListCallback = function(data) {
	try {

		if (JSON.stringify(data) != getLocalStorageItem("messages")) {
			conversationsView.loadConversationsList(data);

			if (data != null && data.length > 0 && data[0].text != "undefined") {
				setLocalStorageItem("messages", JSON.stringify(data));
			}
		}
	} catch (exception) {
		if($debug==true)
			alert("Exception in showMessageListCallback': " + exception);
	}

};

var showLastStoredList = function(data) {
	try {
		// messageListJSON=getLocalStorageItem("messages");
		// if(messageListJSON!=null){
		// var messagesListArray = JSON.parse(messageListJSON);
		// loadMessagesList(messagesListArray);
		// }

	} catch (exception) {
		if($debug==true)
			alert("Exception in showLastStoredList': " + exception);
	}

};

//TODO: Move this method into Conversations View and Helpers
ConversationsHelper.prototype.refreshContactList = function(contactListArray){
     
	if(contactListArray!=null){
    //Sorts the array
	 contactListArray.sort(sort_by('lastActivity', false,null));
	// contactListArray.sort(sort_by('fullName', true, function(a){return a.toUpperCase()}));

	 var $page = $("#conversations");
	 listview = $page.find(".content ul");
	 if($deviceLayout==$TABLET){
		listHTML=refreshContactListOnSendSMSTablet(contactListArray);
	 }else if($deviceLayout==$DESKTOP){
		 listHTML=refreshContactListDesktop(contactListArray);
	 }else{
		 listHTML=refreshContactListOnSendSMSMobile(contactListArray);
	 }
	 if(listHTML!=""){
	
		 listview.html(listHTML);
		 listview.listview('refresh');
	 }
	}
};

//Login User in server
ConversationsHelper.prototype.login = function(){
  var strUrl=$domain+$rootFolder+"users";
  //session_type=2 means web browser
  params="user_id="+$localStorage.getItem("user_id")+"&mac_address="+getLocalStorageItem("mac_address")+"&session_type=2";
  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),strUrl+"/login",params,'','POST','',null,loginUserCallback,loginUserError);
	
};

/******Login User CallBacks*****/
var loginUserCallback = function(result){
	 resultValues=result.split(",");

		if(resultValues[0]=="OK"){
			 
			 $user = getLocalStorageItem("user");
			 if($user!=null){
				var $user =  JSON.parse($user);
				$user.tokens=resultValues[4];
			 }
			 else{
				 var $user = new User(localStorage.getItem("user_id"),localStorage.getItem("user_id"),"",resultValues[4]);
			 }

			    $("#credits").html($user.tokens);
			 setLocalStorageItem("user",JSON.stringify($user));
		}

	
};

var loginUserError = function(result){
//Show Error notification or threatment
	//TODO Why this error
	//alert("in sync caaaaalBack ERROR");
};

/*************************Logout User in server*****************************/
ConversationsHelper.prototype.logout = function(){
  var strUrl=$domain+$rootFolder+"users";
  params="user_id="+$localStorage.getItem("user_id")+"&mac_address="+getLocalStorageItem("mac_address");
  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),strUrl+"/logout",params,'','POST','',null,logoutUserCallback,logoutUserError);
	
};

/******Login User CallBacks*****/
var logoutUserCallback = function(result){

	
};

var logoutUserError = function(result){
//Show Error notification or threatment
	//TODO Why this error
	//alert("in sync caaaaalBack ERROR");
};