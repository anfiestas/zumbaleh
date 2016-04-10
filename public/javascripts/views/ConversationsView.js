function ConversationsView() {
	this.currentPage = $('#conversations');
	this.contactsList=this.currentPage.find('.ui-block-b');
}

ConversationsView.prototype.loadConversationsList = function(data){
	try{    
			
		    var $pageReports = $("#conversations");
			// Append the list to the DOM and make it a list view	
		    //var reportsList=$pageReports.find(".reportsList");
		    var reportsList=$pageReports.find(".reportsList ul");
		
			// Create a new list
			//reportsList.html("<ul></ul>");
			//$list = reportsList.find("ul");
			if(data!=null){
			reportsList.html("");
			for (var i = 0; i < data.length; i++) {
				var $strHtml='<li><a href="#">';
				$strHtml+='<img src="images/mobile/icon_unknown_mini.png"/>';
				var today = new Date();
				today=today.getDate()+"/"+(today.getMonth()+1)+"/"+today.getFullYear();
				
				var smsDate = new Date(data[i].timestamp*1000);
				formatedDate=smsDate.getDate()+"/"+(smsDate.getMonth()+1)+"/"+smsDate.getFullYear();
				if(today==formatedDate){
					minutes=((smsDate.getMinutes()+ "").length==1)?"0"+smsDate.getMinutes():smsDate.getMinutes();
					formatedDate=smsDate.getHours()+":"+minutes;
				}
				$strHtml+='<div class="floatRight">'+formatedDate+'</div>';
				var contactName="";
				
				$strHtml+='<h3 class="ui-li-heading">'+data[i].destinationNumber+'</h3>';
				
				if(data[i].status==0)
				   var $statusIcon='icondelivered24.png';
				else if(data[i].status==1)
				   var $statusIcon='iconsended24.png';
				else
				   var $statusIcon='iconfailed24.png';
				  
				$strHtml+='<img width="16" height="16" src="images/'+$statusIcon+'"/>';
				$strHtml+='<p>'+data[i].text+'</p>';
				$strHtml+='</a>';
				$strHtml+='<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>';
				$strHtml+='</li>';
				
				reportsList.append($strHtml);
				//reportsList.html($strHtml);
		
		}
		//call listview widget
		//reportsList.listview();
		reportsList.listview('refresh');
		}
	   
		// Hide the loading dialog
		//$.mobile.hidePageLoadingMsg();
		
	}catch(exception){
		if($debug==true)
			alert("Exception in ConversationsView.loadConversationsList': " + exception);
	}
};

ConversationsView.prototype.alertMessageReceived = function(messageIM){
	try{  
		 var $pageReports = $("#conversations");

		 var listview = $pageReports.find(".content ul");
	
		 $contactDiv=$('#'+messageIM.fromUserId);
		
		if($contactDiv.html()==null){
			$newUser='<li data-icon="false" id="'+messageIM.fromUserId+'" data-theme="c" class="ui-btn ui-btn-icon-right ui-li ui-li-has-thumb ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a id="#toUserId='+messageIM.fromUserId+'" class="ui-link-inherit"><img src="images/mobile/contactNoPicture.png" class="ui-li-thumb"><div class="floatRight">19/02/2012</div> <h3 class="ui-li-heading">'+messageIM.fromUserId+'</h3><p class="ui-li-desc">'+messageIM.text+'</p></a> </div></div></li>';
			listview.prepend($newUser);
			 doAlert(messageIM);
		}
		if($contactDiv.html()!=null){		
		//Set alert message notification style
			listview.prepend($contactDiv[0].outerHTML);

			$contactDiv.remove();
	   
	        doAlert(messageIM);

			//conversationsHelper.refreshContactList(contactListArray);
		

		 /*var $page = $("#conversations");
		 listview = $page.find(".content ul");
//		
		 listview.listview('refresh');*/
		 
		 
		}
	
}catch(exception){
	if($debug==true)
		alert("Exception in ConversationsView.alertMessageReceived': " + exception);
}
};



function alertMessage(messageIM) {

	var isOldTitle = true;
var oldTitle = "Spoora";
var newTitle = messageIM.text;
var interval = null;
function changeTitle() {
    document.title = isOldTitle ? oldTitle : newTitle;
    isOldTitle = !isOldTitle;
}
interval = setInterval(changeTitle, 700);

$(window).focus(function () {
    clearInterval(interval);
    $("title").text(newTitle);
    
});

}

function doAlert(messageIM){
	 $contactDivnew=$('#'+messageIM.fromUserId);
	   $contactDivnew.find('p').html(messageIM.text);
	    

		 //Show text of message blinking on Tab
		 alertMessage(messageIM);
		 
		 if( (window_focused==true && $contactDiv.hasClass("ui-btn-active")==false) || window_focused==false){
			 
			//Make blink
			 if($contactDiv.hasClass("ui-btn-active")==false ||  window_focused==false){
				 
				 for(i=0;i<3;i++) {
					 $contactDivnew.fadeTo('slow', 0.5).fadeTo('slow', 1.0);
					  }
					$contactDivnew.addClass("alertNewMessage");
			 }
			 
			 if($isActive==1){
				 var audio = $("#messageInSound")[0];
				 audio.play();
			 }
		 }
		 
		
		 //Put lastMessage on Top
		 var contactListJSON = getLocalStorageItem("contacts");

		if(contactListJSON!=null){
				
			var contactListArray =  JSON.parse(contactListJSON);

			for (var i=0;i<contactListArray.length;i++){

				if(contactListArray[i].userId==messageIM.fromUserId){
				 contactListArray[i].lastActivity=messageIM.timestamp;
				 setLocalStorageItem("contacts",JSON.stringify(contactListArray));
				 break;
				}

			}
		}

}

function refreshContactListOnSendSMSMobile(contactListArray){
	markup="";
	
	 if(contactListArray!="" && contactListArray.length > 0){
			
			var markup="";
		     var currentLetter="";
			 var phoneIndex=0;
			 for (var i = 0; i < contactListArray.length; i++) {
					var contact = contactListArray[i];
				  if(contact.syncStatus =='undefined' || contact.syncStatus!="removed"){
					 
			          //for (var p=0; p < contact.phones.length; p++){
			          if(contact.fullName!="null"){var tag=contact.fullName;}
			          	else {var tag=contact.userId;}
					  
			          //Show las conversation message
			          var conversationIMStored=getLocalStorageItem("conversations."+contact.userId);
			          if(conversationIMStored!=null)
			             { 	 //Load current chat
			        	    conversationIMStored = JSON.parse(conversationIMStored);
			        	    var text=conversationIMStored[0].text;
			             }
			          else{conversationIMStored="";var text="";}
			          	//Load curre 
			          
					 markup+=' <li><a id="#toUserId='+contact.userId+'">'+'<img src="images/mobile/icon_unknown_mini.png" />'+
					 '<div class="floatRight">19/02/2012</div> <h3 class="ui-li-heading">'+tag+'</h3>'+
					 ' <img width="16" height="16" src="images/icondelivered24.png" /> '+
					 '<p>'+text+'</p></a> '+
					 '<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span> </li>';
				  }
			 }
	
	 }
	 
	 return markup;
}
function refreshContactListOnSendSMSTablet(contactListArray){
	markup="";
	
	 if(contactListArray!="" && contactListArray.length > 0){
			
			var markup="";
		     var currentLetter="";
			 var phoneIndex=0;
			 for (var i = 0; i < contactListArray.length; i++) {
					var contact = contactListArray[i];
				  if(contact.syncStatus =='undefined' || contact.syncStatus!="removed"){
				      var nextLetter=contact.fullName.charAt(0);
					  if(currentLetter!=nextLetter){
					      var currentLetter = nextLetter;
					      markup+='<li data-role="list-divider">'+currentLetter+'</li>';
					  }
					  
			          //for (var p=0; p < contact.phones.length; p++){
			          if(contact.fullName!="null"){var tag=contact.fullName;}
			          else
			        	  var tag=contact.userId;
			          //else if(contact.phone!="null"){var tag=contact.fullName;}
					  markup+='<li data-icon="false"><a id="#toUserId='+contact.userId+'">'+tag+'</a></li>';
				      //phoneIndex++;
					  //}
				  }
			 }
	
	 }
	 
	 return markup;
}

function refreshContactListDesktop(contactListArray){
	markup="";
	 if(contactListArray!="" && contactListArray.length > 0){
			
			var markup="";
		     var currentLetter="";
			 var phoneIndex=0;
			 for (var i = 0; i < contactListArray.length; i++) {
					var contact = contactListArray[i];
				  if(contact.syncStatus =='undefined' || contact.syncStatus!="removed"){
					 
			          //for (var p=0; p < contact.phones.length; p++){
			          if(contact.fullName!="null"){var tag=contact.fullName;}
			          	else {var tag=contact.userId;}
					  
			          //Show las conversation message
			          var conversationIMStored=getLocalStorageItem("conversations."+contact.userId);
			          if(conversationIMStored!=null)
			             { 	 //Load current chat
			        	    conversationIMStored = JSON.parse(conversationIMStored);
			        	    var text=conversationIMStored[conversationIMStored.length-1].text;
			             }
			          else{conversationIMStored="";var text="";}
			          	//Load curre 
			          if(contact.isGroup==1)
			          	var icon="icon_group_ok.png";
			          else
			          	var icon="icon_unknown_mini.png";

					 markup+=' <li data-icon="false" id="'+contact.userId+'"><a id="#toUserId='+contact.userId+'">'+'<img src="images/mobile/'+icon+'" />'+
					 '<div class="floatRight">19/02/2012</div> <h3 class="ui-li-heading">'+tag+'</h3>'+
					 '<p>'+text+'</p></a> '+'</li>';
				  }
			 }
	
	 }
	 
	 return markup;
}
