
/**
 * This method is executed one time, before page 
 * is being added to the DOM.
 * Conversations is the first page being loaded, so we use it
 */

$('#chat').live('pagebeforecreate',function(event){
	try{
	
		
	}catch(exception){
		if($debug==true)
			alert("Exception in chat pagebeforecreate': " + exception);
	}

});

/**
 * This method is executed one time when first time
 * page is being added in the DOM
 */
$('#chat').live('pageinit',function(event){
	
	try{
	 	//var messages = JSON.parse(getLocalStorageItem("messages"));
		//conversationsView.loadConversationsList(messages);
		$page=$.mobile.activePage;
		
	}catch(exception){
		if($debug==true)
			alert("Exception in chat PageInit': " + exception);
	}
	
	
});

/**
 * Use this method to update dynamic information
 * of the page. This method is executed each time
 * page is showed
 */
$('#chat').live('pageshow',function(event){
	try{

		//init controller View
		//chatView = new ChatView();
		
		//load contactName
		var contactsStored = getLocalStorageItem("contacts");
		if(contactsStored!=null){
			var glbContactList = JSON.parse(contactsStored);
			var result = $.grep(glbContactList, function(e){ return e.userId == toUserId; });
			
			if (result.length == 0) {
				chatView.contactName.html("");
				} else if (result.length == 1) {
				 if(result[0].fullName!=="null")
					chatView.contactName.html(result[0].fullName);
				 else
					 chatView.contactName.html(result[0].userId);
				} else {
					chatView.contactName.html(result[0].fullName);
				}
			}
		
		chatHelper.loadAndRefreshCurrentChat(toUserId);

		
	    
	}catch(exception){
		if($debug==true)
			alert("Exception in chat PageShow': " + exception);
	}

});


$('#chat #submitSendMessage').live('click',function(event){
	chatHelper.sendIMScrolling();
	
});

/*
Click in a contact from listview
*/



