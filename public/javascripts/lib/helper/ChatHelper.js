function ChatHelper() {
this.strUrl=$domain+$rootFolder;
this.text="";
}


/******************** sendIM *******************/
ChatHelper.prototype.sendIM = function(userId,toUserId,toPhone,messageText,connectionType){
var url=this.strUrl+"messages/send";
this.text=messageText;
 if($localStorage.getItem("user_id")!=null && getLocalStorageItem("userpass")!=null){
    params="user_id="+user_id+"&mac_address="+getLocalStorageItem("mac_address")+"&to_user_id="+toUserId+"&text="+messageText+"&user_timestamp="+Math.round(+new Date()/1000)+"&connection_type="+connectionType;


  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),url,params,'html','POST','',messageText,sendIMCallback,sendIMError);

 }

};


function sendIMCallback(result,messageText){
  
	//save text into Contact conversationList(localStorage)
	var newMessageArray = new Array();
	newMessageArray.push(new MessageIM(user_id,user_phone,toUserId,"",messageText,Math.round(+new Date()/1000)));
	 chatView.loadPendingMessagesIM(newMessageArray);
	chatHelper.updateStoredMessagesIM(newMessageArray,toUserId);
	
	//Refresh tokens
	 resultValues=result.split(",");

		if(resultValues[0]=="OK"){
			 
			 $user = getLocalStorageItem("user");
				var $user =  JSON.parse($user);
				$user.tokens=resultValues[2];
			    $("#credits").html($user.tokens);
			 setLocalStorageItem("user",JSON.stringify($user));
		}

};

var sendIMError = function(result){
//Show Error notification or threatment
	//alert("sentError");
};

/****************** getPendingIM *******************/
//GetPendingIM contacts with server
ChatHelper.prototype.getPendingIM = function(userId,userPhone,connectionType,nextEvent){
//	alert("getPending");
var url=this.strUrl+"messages/pendings";

 if($localStorage.getItem("user_id")!=null && getLocalStorageItem("userpass")!=null){
  params="user_id="+userId+"&mac_address="+getLocalStorageItem("mac_address")+"&user_timestamp="+Math.round(+new Date()/1000)+"&connection_type="+connectionType+"&with_is_active=true";

  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),url,params,'','GET','',nextEvent,getPendingIMCallback,getPendingIMError);
 }

};
var getPendingIMCallback = function(result,event){
//	alert("OK");
	resultValues=result.split(",");
	$isActive=resultValues[2];

    //Add new element on the list
	if(resultValues[0]=="OK" && resultValues[1] > 0){


		result=result.replace(resultValues[0]+","+resultValues[1]+","+resultValues[2]+",", "");
		//Fires event
		//$("#chat").trigger(event,{ result: result });
		chatView.currentPage.trigger(event,{ result: result });
	}
	 //Execute next pending timer after the end of first pendings
	setTimeout("chatHelper.getPendingIM(user_id, user_phone, 2,'pendingIMGettedEvent')",execInterval); 	
};

var getPendingIMError = function(result){
	 //Execute next pending timer after the end of first pendings
	setTimeout("chatHelper.getPendingIM(user_id, user_phone, 2,'pendingIMGettedEvent')",execInterval); 	
//Show Error notification or threatment
	//alert("Pendings Error");
};

ChatHelper.prototype.parsePengingIMAndRefresh = function(result){
	try{
		
	var messagesIMArray = new Array();
	var conversationArray = new Array();
		if (result != null) {
		var messagesIM = result.split("9&c3,");
		for ( var i = messagesIM.length-1; i >= 0; i--) {
			
			var messageIM = this.parseMessageIM(messagesIM[i]);
			
			if(conversationArray.length==0 || conversationArray[conversationArray.length-1].fromUserId==messageIM.fromUserId){
				
				conversationArray.push(messageIM);
				//messagesIMArray.push(conversationArray);
				if(i==0){
					chatHelper.updateStoreAndPaint(conversationArray);
				}
			}
				
			else
				{
					//End of conversation messages, store it
					messagesIMArray.push(conversationArray);
					//new conversation
					conversationArray = new Array();
					conversationArray.push(messageIM);

				 	//stores last conversation
					var lastConvers=messagesIMArray[messagesIMArray.length-1];
					chatHelper.updateStoreAndPaint(lastConvers);
				
						if(i==0)
							chatHelper.updateStoreAndPaint(conversationArray);	
				}
			//Update conversationsView
			conversationsView.alertMessageReceived(messageIM);
		}
       
	}
	
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
ChatHelper.prototype.parseMessageIM = function(messageIM){
	   
	if(messageIM!=null){
		var messageValue=messageIM.split(",");
		var messageIM = 
			new MessageIM(messageValue[2],messageValue[0],messageValue[1],messageValue[3],messageValue[5],
					      messageValue[4],messageValue[6],messageValue[7],messageValue[8],messageValue[9]);
			return messageIM;
		}
		
};

ChatHelper.prototype.updateStoredMessagesIM = function(messagesIMArray,toUserId){
// store on localStorage
var messagesIMStored = getLocalStorageItem("conversations."+toUserId);

if (messagesIMStored == null) {
	messagesIMStored = messagesIMArray;
} else {
	messagesIMStored=JSON.parse(messagesIMStored);
	$.merge(messagesIMStored, messagesIMArray);
}
setLocalStorageItem("conversations."+toUserId, JSON.stringify(messagesIMStored));
return false;
};

ChatHelper.prototype.setPendingIMGettedtListener = function(page){
	
	$('#'+page).bind('pendingIMGettedEvent', function(event,data) {
		try{
			   var currentPageId=$.mobile.activePage.get(0)["id"];
               chatHelper.parsePengingIMAndRefresh(data.result);
              //TODO: refresh change color of list users received
				 if (currentPageId == page) {
					 //refresh current chat				     
				     var conversationIMStored=getLocalStorageItem("conversations."+toUserId);
				     if(conversationIMStored!=null)
				        { 	 
				 		//Load current chat
				 //	    var conversationIMStored = JSON.parse(conversationIMStored);
//				 	    chatView.chatList.html("");
//				    	var newMessageArray = new Array();
//			    		newMessageArray.push(new MessageIM(user_id,user_phone,toUserId,"",sendText,Math.round(+new Date()/1000)));
//				 	    chatView.loadPendingMessagesIM(conversationIMStored);
				        }
				}

			}catch(exception){
				if($debug==true)
					alert("Exception in conversations bind.pendingIMGettedEvent': " + exception);
			}
			
			});
};

ChatHelper.prototype.sendIMScrolling= function(){
	$page=$.mobile.activePage;
	 var listview = $("#conversations").find(".content ul");

	if(chatView.messageTextInput.val()!=""){
		//move up conversation
		 var $contactDivnew=$('#'+toUserId);
		 listview.prepend($contactDivnew[0].outerHTML);
	     $contactDivnew.remove();

	   var $contactDivnew=$('#'+toUserId);
	   $contactDivnew.find('p').html(chatView.messageTextInput.val());

		chatHelper.sendIM(user_id, toUserId, null,  chatView.messageTextInput.val(), 2);
		chatView.messageTextInput.val("");
		 //Scroll to bottom page
		 chatView.chatList.animate({ scrollTop: chatView.chatList[0].scrollHeight}, 1);

	}
};

ChatHelper.prototype.loadAndRefreshCurrentChat= function(toUserId){
var conversationIMStored=getLocalStorageItem("conversations."+toUserId);
if(conversationIMStored!=null)
   { 	 
	//Load current chat
    var conversationIMStored = JSON.parse(conversationIMStored);
    chatView.chatList.html("");
    chatView.loadPendingMessagesIM(conversationIMStored);
   }
	else{
		 chatView.chatList.html("");
	}
};

/******* Functions *********/

ChatHelper.prototype.updateStoreAndPaint= function(conversationArray){
	var contact=$('#'+conversationArray[conversationArray.length-1].fromUserId);
	chatHelper.updateStoredMessagesIM(conversationArray,conversationArray[conversationArray.length-1].fromUserId);
	//Paints only new messages in current conversation
	if(contact.hasClass("ui-btn-active")==true)
		chatView.loadPendingMessagesIM(conversationArray);

}
/****************** getMedia *******************/
//GetPendingIM contacts with server
//ChatHelper.prototype.getMediaIM = function(userId,fileId,connectionType,nextEvent){
//
//var url=this.strUrl+"media/get";
//
//if(getLocalStorageItem("user_id")!=null && getLocalStorageItem("userpass")!=null){
//params="user_id="+userId+"&file_id="+fileId+"&user_timestamp="+Math.round(+new Date()/1000);
//
//httpAuthRequest(getLocalStorageItem("user_id"),getLocalStorageItem("im_user_key"),url,params,'','GET','',nextEvent,getMediaCallback,getMediaError);
//}
//
//};
//var getMediaCallback = function(result,event){
//	
//
//};
//
//var getMediaError = function(result){
////Show Error notification or threatment
//	alert("sentError");
//};

