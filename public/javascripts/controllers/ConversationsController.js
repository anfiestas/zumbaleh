//Controller views
var conversationsView = new ConversationsView();
//Static Objects and Helpers
var contactsHelper = new ContactsHelper();
var conversationsHelper = new ConversationsHelper();
var groupHelper = new GroupHelper();
var chatHelper = new ChatHelper();
var glbContactList=null;
/*spanish number*/
var user_id;
var user_phone="";
//var user_id=49 var user_phone="0033619519272";

var toUserId;
var execInterval=3000;
var maxMessagesConversation=200;
var $isActive=1;


 /*if (window.webkitNotifications.checkPermission() == 0) { // 0 is PERMISSION_ALLOWED
    // function defined in step 2
    window.webkitNotifications.createNotification(
        'icon.png', 'Notification Title', 'Notification content...');

  } else {
    window.webkitNotifications.requestPermission();

  }*/

/**
 * This method is executed one time, before page 
 * is being added to the DOM.
 * Conversations is the first page being loaded, so we use it
 */

$('#conversations').live('pagebeforecreate',function(event){
	try{
		try{
			var contactsStored = getLocalStorageItem("contacts");
			if(contactsStored!=null){
				var glbContactList = JSON.parse(contactsStored);
				conversationsHelper.refreshContactList(glbContactList);
			}
				
		}catch(exception){
			//TODO - error on init list
			 //alert("Exception in conversations refreshContactList': " + exception);
			 //localStorage.removeItem("contacts");
		}
		
		//SyncContacts
		 if($localStorage.getItem("user_id")!=null){
			 contactsHelper.syncContacts();
			 user_id=$localStorage.getItem("user_id");
		 }
		 
		 //conversationsHelper.getMessages();
	    /**Add userName on options logout*/
	    $.base64.is_unicode = true;
		//$usernameDecoded = $.base64.decode(localStorage.getItem("user_id"));
	    $user = getLocalStorageItem("user");
	    if($user!=null){
		var $user =  JSON.parse($user);
		    $("#username").html($user.fullName);
		    if($user.fullName!=$user.userId)
		      $("#pin").html(""+$user.userId+" | ");
		    $("#credits").html($user.tokens);
	    }
		/**
		 * Pooling pendingIM in 5 secs execution interval
		 * and onResponse execute pendingIMGettedEvent
		 */
		//TODO:when chat view opened:timeout=3000
		//when other view or app in undreground 30000
		chatHelper.getPendingIM(user_id, user_phone, 2,"pendingIMGettedEvent"); 
		
		/**
		 * Add pendingIMGettedEvent Listener
		 */ 
		if($deviceLayout==$MOBILE)
			chatHelper.setPendingIMGettedtListener("chat");
		else
			chatHelper.setPendingIMGettedtListener("conversations");

		
	}catch(exception){
		if($debug==true)
			alert("Exception in conversations pagebeforecreate': " + exception);
	}

});

/**
 * This method is executed one time when first time
 * page is being added in the DOM
 */
$('#conversations').live('pageinit',function(event){
	
	try{
		if($deviceLayout==$MOBILE)
			chatView = new ChatView("chat");
		else
			chatView = new ChatView("conversations");
	
	}catch(exception){
		if($debug==true)
			alert("Exception in reports ShowPage': " + exception);
	}
	
	
});

/**
 * Use this method to update dynamic information
 * of the page. This method is executed each time
 * page is showed
 */
$('#conversations').live('pageshow',function(event){
	try{
		//TODO read conversations
	    //else show you dont't have conversations
		//Login
		//conversationsHelper.login();
	}catch(exception){
		if($debug==true)
			alert("Exception in conversations PageShow': " + exception);
	}

});

/*
Click in a contact from listview
*/

if($deviceLayout==$TABLET || $deviceLayout==$DESKTOP){
	$("#conversations :jqmData(role='listview') li").live('click',function(event){
	
	unselectAllListView();
	$(this).addClass("ui-btn-active");
	var action=$(this).find('a').attr("id");
	toUserId = action.replace(/.*toUserId=/, "" );

	 //var $pageReports = $("#conversations");
		//$contactDiv=$pageReports.find('#'+messageIM.fromUserId);
		$(this).removeClass( "alertNewMessage");
	try{
	
	   // $.mobile.silentScroll(10000);
	    chatHelper.loadAndRefreshCurrentChat(toUserId);
	    chatView.chatList.animate({ scrollTop: chatView.chatList[0].scrollHeight}, 1);	
	
	    
	    
	}catch(exception){
		if($debug==true)
			alert("Exception in chat PageShow': " + exception);
	}
	});
	
}else{
	
	$("#conversations :jqmData(role='listview') li").live('click',function(event){

		unselectAllListView();
		$(this).addClass("ui-btn-active");
		var action=$(this).find('a').attr("id");
		toUserId = action.replace(/.*toUserId=/, "" );
		 //var $pageReports = $("#conversations");
			//$contactDiv=$pageReports.find('#'+messageIM.fromUserId);
			$(this).removeClass( "alertNewMessage");
			
		try{

		   //send param toUserId, and change to chat page
		    $.mobile.changePage("#chat", {transition: "slide"});
			$.mobile.hidePageLoadingMsg();
		}catch(exception){
			if($debug==true)
				alert("Exception in chat PageShow': " + exception);
		}
		});
}
if($deviceLayout==$TABLET || $deviceLayout==$DESKTOP){
	//Click to send
	$('#conversations #submitSendMessage').live('click',function(event){
		chatHelper.sendIMScrolling();
		
	});

	$(document).keypress(function(e) {
    if(e.which == 13) {
        chatHelper.sendIMScrolling();
    }
	});
}

/** HELP METHODS**/
