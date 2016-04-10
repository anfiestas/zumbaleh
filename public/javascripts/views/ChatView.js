function ChatView(parentPage) {
	this.currentPage = $('#'+parentPage);
	this.chatList=this.currentPage.find('#chatList');
	this.sendBarDiv=this.currentPage.find('#sendBarDiv');
	this.messageTextInput=this.currentPage.find('#new_message');
	
	this.contactName=this.currentPage.find('#contactName');
}


ChatView.prototype.loadPendingMessagesIM = function(data){
	try{    
		var bubble="";
	    var oldDay= new Date();
		var today = new Date();

	    
		today=today.getDate()+"/"+(today.getMonth()+1)+"/"+today.getFullYear();
		oldDay=oldDay.getDate()+"/"+(oldDay.getMonth()+1)+"/"+oldDay.getFullYear();
		
		var init=0;
		//Add new elements on the list
		if(data.length > maxMessagesConversation)
			 init = data.length-maxMessagesConversation;
		

		
		for (var i = init; i < data.length; i++) {
			var $strHtml="";
			
			//Calculate date
			var imDate = new Date(data[i].timestamp*1000);
			var imFormatedDate=imDate.getDate()+"/"+(imDate.getMonth()+1)+"/"+imDate.getFullYear();
			
			minutes=((imDate.getMinutes()+ "").length==1)?"0"+imDate.getMinutes():imDate.getMinutes();
			formatedDate=imDate.getHours()+":"+minutes;
			
			if(today==imFormatedDate){

			}else{
				if(oldDay!=imFormatedDate){
					oldDay=imFormatedDate;
					$newDay="<p class='newDay'> <span class='newDayValue'>"+imFormatedDate+"</span></p>";
					this.chatList.append($newDay);
				}
			}
			
			
			if(data[i].fromUserId==user_id){
				 bubble="'triangle-border right'";
				 var align="right";
			}
			else{
				 bubble="'triangle-border left'";
				 var align="left";
				 }
				//TODO Si grupo pintar aqui el nombre del user y color para cada mensaje recibido
			    if(data[i].isFromGroup==1){
			    	var groupId = data[i].fromUserId;
			    	var group = new Group();
			    	group = group.getGroupFromStorageById(groupId);
			    	//get member from group members List
			    	var result = $.grep(group.members, function(member){ return member.userId == data[i].fromGroupMember; });
					
			    	if(result[0].fullName!="null")
			    		$strHtml += "<span style='margin-left:15px;float:left;clear:both; color:"+result[0].color+";'>"+result[0].fullName+"</span>";
			    	else
			    		$strHtml += "<span style='margin-left:15px;float:left;clear:both; color:"+result[0].color+";'>"+result[0].userId+"</span>";
			    	this.chatList.append($strHtml);
			    }
			    

				if(data[i].messageType=="2"){
					$strHtml = "<p class="+bubble+">"+data[i].text+"</p> <span class='imDate' style='float:left;'>"+formatedDate+"</span>";
					this.chatList.append($strHtml);
				}
				else if(data[i].text!=null && (data[i].messageType=="31" || data[i].messageType=="1231")){

					//chatHelper.getMediaIM(getLocalStorageItem("user_id"), data[i].text, Math.round(+new Date()/1000), null);
					//get images
					 var url=$domain+$rootFolder+"media/get";
					 var params="user_id="+$localStorage.getItem("user_id")+"&file_id="+data[i].text;
					 var image = new Image();
					  
					// if(data[i].fromUserId!=user_id){
						 //if messages received
					  image.accessKey=data[i].text.split(".")[0]; 
					  image.src = url+"?"+params;
					/* }else{
						 //If messages send
						image.accessKey=data[i].timestamp; 
						image.src =data[i].text;
					 }*/
					  //$strHtml = "<p class="+bubble+" id='"+image.accessKey+"'></p>";
					   $strHtml = "<p class="+bubble+"><a id='"+image.accessKey+"' href='"+image.src+"' download="+image.src+"> </a></p> <span class='imDate' style='float:"+align+";'>"+formatedDate+"</span>";
						this.chatList.append($strHtml);
						//console.log("aqui2");
					  image.onload = function () {

						  //IMPORTANT USE THIS
						    $('#'+this.accessKey).append(this);
						    scaleImage($(this),$(this).width(),$(this).height());
						    //scroll down
						   chatView.chatList.animate({ scrollTop: chatView.chatList[0].scrollHeight}, 0);
					    };
					  

				}else{
					$strHtml = "<p class="+bubble+">"+data[i].text+"</p> <span class='imDate' style='float:right;'>"+formatedDate+"</span>";
					this.chatList.append($strHtml);
				}

			
		}

		//Scroll to bottom page
		//64 px per message
		var newScrollSize=(data.length)*64 + 737;
		newScrollSize=newScrollSize+chatView.chatList[0].scrollTop;
		var scrollLimit=(chatView.chatList[0].scrollHeight-200);
		if(newScrollSize > scrollLimit){
		
			chatView.chatList.animate({ scrollTop: chatView.chatList[0].scrollHeight}, 0);	
		}


		
	}catch(exception){
		if($debug==true)
			alert("Exception in ChatView.loadPendingMessagesIM': " + exception);
	}
};


var scaleImage = function(image,width,height){
	
	  var maxWidth = 400; // Max width for the image
	  var maxHeight = 400;    // Max height for the image
	  var ratio = 0;  // Used for aspect ratio
	    
	 // Check if the current width is larger than the max
  if(width > maxWidth){
      ratio = maxWidth / width;   // get ratio for scaling image
      if (typeof image.css === "undefined") {
    	  image.style.width = maxWidth+"px"; 
          image.style.height = height * ratio+"px"; 
      }else{
    	  image.css("width", maxWidth); // Set new width
    	  image.css("height", height * ratio);  // Scale height based on ratio
      }
     
    
      height = height * ratio;    // Reset height to match scaled image
      width = width * ratio;    // Reset width to match scaled image
  }
  // Check if current height is larger than max
  if(height > maxHeight){
      ratio = maxHeight / height; // get ratio for scaling image
      if (typeof image.css === "undefined") {
    	  image.style.height = maxHeight+"px"; 
    	  image.style.width = width * ratio+"px"; 
      }else{
	      image.css("height", maxHeight);   // Set new height	
	      image.css("width", width * ratio);    // Scale width based on ratio
      }
      
      width = width * ratio;    // Reset width to match scaled image
      height = height * ratio;    // Reset height to match scaled image
  }
	
};
