
$('#reports').live('pageinit',function(event){
	
	var contactListJSON =getLocalStorageItem("messages"); 

	 if(contactListJSON!=null){
		var reportsList=$("#reports").find(".reportsList ul");
		reportsList.html("");
		var block_a=$("#reports").find(".ui-block-a ul");
		block_a.html("");
		//refresh new page content
		// reportsList.listview();
		 //reportsList.listview('refresh');
	  }
	
	
});

$('#reports').live('pageshow',function(event){
  
 //Update credits
 $user = JSON.parse(getLocalStorageItem("user")); 
 //$(".credits").html($user.balance);
 
 /***Refresh contacts List*****/
  var $page = $('#reports');   
	 //get contacts and create list
	 contactListJSON = getLocalStorageItem("contacts");
	 if(contactListJSON!=null){
		var contactListArray =  JSON.parse(contactListJSON);
		
	  }
	 else{
		//If first no contacts created before	
		var contactListArray =  new Array();
	 } 
	 if(contactListArray!=null && contactListArray!="")
		 refreshContactList2(contactListArray);
	
	 //show loading page dialog
	$.mobile.showPageLoadingMsg();	
	//show stored messages first
	messageListJSON=getLocalStorageItem("messages");
	if(messageListJSON!=null){
		var messagesListArray =  JSON.parse(messageListJSON);
		loadMessagesList(messagesListArray);
	  }

var $pageReports = $("#reports");
 
	// Asynchronously refresh the message list
	var strUrl=$domain+$rootFolder+"rest/messages";
	
	httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),strUrl,'','','GET','json',null,showMessageListCallback,showLastStoredList);
    
});

var showMessageListCallback = function(data) {
       	
	loadMessagesList(data);
		
		if(data!=null && data.length > 0 && data[0].text!="undefined"){
			setLocalStorageItem("messages",JSON.stringify(data));
		}
	  }

var showLastStoredList = function(data){
	messageListJSON=getLocalStorageItem("messages");
	 if(messageListJSON!=null){
			var messagesListArray =  JSON.parse(messageListJSON);
			loadMessagesList(messagesListArray);
		  }

	
	
}

var refreshContactList2 = function(contactListArray){

     //Sorts the array
	 //contactListArray=sortByFullName(contactListArray); 
	 contactListArray.sort(sort_by('firstName', true, function(a){return a.toUpperCase()}));
	 //listview = $(":jqmData(role='listview')");
	 var $pageReports = $("#reports");
	 var listview = $pageReports.find(".content .ui-block-a ul");
	 var listHTML=refreshContactListFromView("reports",contactListArray);
	 if(listHTML!=""){
		 listview.html(listHTML);
		 listview.listview('refresh');
	 }
	 //showContactInfoPanel($glbSelectedContact);

}