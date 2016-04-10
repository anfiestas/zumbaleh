
function ContactsHelper() {
	this.strUrl=$domain+$rootFolder+"userlinks";
}

ContactsHelper.prototype.createContactsHashMap = function(contactsArray) {
	 var contactsMap = new Array();
	 
	 if(contactsArray!="" && contactsArray.length > 0){
			
			 var phoneIndex=0;
			 for (var i = 0; i < contactsArray.length; i++) {
					var contact = contactsArray[i];
					  
			          for (var p=0; p < contact.phones.length; p++){
							contactsMap[contact.phones[p].number]=contact.firstName+' '+contact.lastName;
				      phoneIndex++;
					  }
				  
			 }
	
	 }
	 
    return contactsMap;
};

//sync contacts with server
ContactsHelper.prototype.syncContacts = function(){
  params="user_id="+$localStorage.getItem("user_id");
  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),this.strUrl+"/get_all_accepted",params,'','POST','',null,syncContactsCallback,syncContactsError);
 
	
};

/******synContacts CallBacks*****/
var syncContactsCallback = function(result){

	//contacts are sync on the background without affect user usability
	 //save user data in localstorage for future use
	resultValues=result.split(",");
	if(resultValues[0]=="OK" && resultValues[1] > 0){
		result=result.replace(resultValues[0]+","+resultValues[1]+",", "");
		contactListResult = parseContacResponse(result);
		
		}
		
	if (contactListResult!=null && contactListResult!=""){
       
		setLocalStorageItem("contacts",JSON.stringify(contactListResult));
		
//		var contactsMap = contactsHelper.createContactsHashMap(contactListResult);
//		setLocalStorageItem("contactsMap",JSON.stringify(contactsMap));
		
		//Reload current page
		//$.mobile.changePage($.mobile.activePage, { allowSamePageTransition: "true",transition: "none"} );
		conversationsHelper.refreshContactList(contactListResult);
		//notify('info','Your Contacts has been synchronized');
	}
	
};

var syncContactsError = function(result){
//Show Error notification or threatment
	//TODO Why this error
	//alert("in sync caaaaalBack ERROR");
};

/******************** user Status *******************/
ContactsHelper.prototype.getStatus = function(userId,toUserId,toPhone){
  
var url=this.strUrl+"/user_status";
 if($localStorage.getItem("user_id")!=null && getLocalStorageItem("userpass")!=null){
  params="user_id="+userId+"&to_user_id="+toUserId+"&to_phone="+toPhone+"&user_timestamp="+Math.round(+new Date()/1000);

  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),url,params,'','GET','',null,userStatusCallback,userStatusError);
 }

};


var userStatusCallback = function(result){
 	
	//save text into Contact conversationList(localStorage)
//	var newMessageArray = new Array();
//	newMessageArray.push(new MessageIM(user_id,user_phone,49,to_number,sendText,Math.round(+new Date()/1000)));
	 //chatView.loadPendingMessagesIM(newMessageArray);
//	chatHelper.updateStoredMessagesIM(newMessageArray);
	alert(JSON.stringify(result));

};

var userStatusError = function(result){
//Show Error notification or threatment
	alert("sentError");
};
/***********************************/
var parseContacResponse = function(resultData){
	try{
	var contactArray = new Array();
		if (resultData != null) {
		var contacts = resultData.split(",9&c3");
		for ( var i = 0; i < contacts.length; i++) {
			if(contacts[i]!=""){
			var contact = parseContact(contacts[i]);
			contactArray.push(contact);
			
			//Save info if group
			if(contact.isGroup==1){
				var group = new Group(contact.userId,contact.fullName,contact.groupOwnerId);
				groupHelper.getGroupInfo($localStorage.getItem("user_id"),group);
			}
				
			
			}
		}
       
	}
	return contactArray.reverse();
	
	}catch(exception){
		if($debug==true)
			alert("Exception in chatHelper parsePengingIMAndRefresh': " + exception);
	}
};

/**
 * 
 * @param messageIM
 * @returns {___messageIM0}
 */
var parseContact = function(contactData){
	   var timestamp=0;
	if(contactData!=null){
		var contactProperties=contactData.split(",");

		 var conversationIMStored=getLocalStorageItem("conversations."+contactProperties[0]);
			          if(conversationIMStored!=null)
			             { 	 //Load current chat
			        	    conversationIMStored = JSON.parse(conversationIMStored);
			        	     timestamp=conversationIMStored[conversationIMStored.length-1].timestamp;
			        	}
	
		var contact = new Contact(contactProperties[0],contactProperties[1],contactProperties[2],contactProperties[3],contactProperties[4],timestamp);
	return contact;
}
		
};


