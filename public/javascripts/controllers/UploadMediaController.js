var iMaxFilesize = 10485760; // 10MB

function showFileSelectDialog() {
   var evt = document.createEvent("MouseEvents");
   evt.initEvent("click", false, false);
   var node = document.getElementById('fileUpload');
   node.dispatchEvent(evt);
}
function fileSelected() {
	// get selected file element
    var oFile = document.getElementById('fileUpload').files[0];
	  acceptedTypes = {
		      'image/png': true,
		      'image/jpeg': true,
		      'image/gif': true
		    };
 // little test for filesize
    if (oFile.size > iMaxFilesize) {
       // document.getElementById('warnsize').style.display = 'block';
    	alert("file size limit is 10Mb");
        return;
    }
    if(acceptedTypes[oFile.type] === true){

	    // prepare HTML5 FileReader
	    var oReader = new FileReader();
	
	    // read selected file as DataURL
	    //oReader.readAsDataURL(oFile);
	    
	    startUploading(oFile.name);
    }
    else{
    	alert("You can only upload images");
        return;
    }
}

function startUploading() {
	
	  var oFile = document.getElementById('fileUpload').files[0];
    // cleanup all temp states
    iPreviousBytesLoaded = 0;
    bubble="'triangle-border right'";
//    document.getElementById('upload_response').style.display = 'none';
//    document.getElementById('error').style.display = 'none';
//    document.getElementById('error2').style.display = 'none';
//    document.getElementById('abort').style.display = 'none';
//    document.getElementById('warnsize').style.display = 'none';
//    document.getElementById('progress_percent').innerHTML = '';
//    var oProgress = document.getElementById('progress');
//    oProgress.style.display = 'block';
//    oProgress.style.width = '0px';

    // get form data for POSTing
    //var formdata = document.getElementById('upload_form'); // for FF3
    //var formdata = new FormData(document.getElementById('upload_form'));
    var formdata = new FormData();
    var name = oFile.name;
    name = name.replace("_","-");
    formdata.append(name, oFile);
    formdata.append("user_id", $localStorage.getItem("user_id")); 
    formdata.append("to_user_id", toUserId);
    formdata.append("fileName", name);
    formdata.append("connection_type", "31");

    // create XMLHttpRequest object, adding few event listeners, and POSTing our data
    var oXHR = new XMLHttpRequest();        
//    oXHR.upload.addEventListener('progress', uploadProgress, false);
    oXHR.addEventListener('load', uploadFinish, true);
    //oXHR.addEventListener('error', uploadError, false);
    //oXHR.addEventListener('abort', uploadAbort, false);
    var url=$domain+$rootFolder+"media/send";
    //params="user_id="+localStorage.getItem("user_id")+"&to_user_id="+toUserId+"&fileName="+oFile.name+"&connection_type=31";
    oXHR.open('POST', url);
    oXHR.send(formdata);

    // set inner timer
    //oTimer = setInterval(doInnerUpdates, 300);
	 var image = new Image();
	 //image.src = url+"?"+params;
	var imgId= Math.round(+new Date()/1000);
	  $strHtml = "<p class="+bubble+" id='"+imgId+"'></p>";
	  chatView.chatList.append($strHtml);
	  /*image.onload = function () {
		  //IMPORTANT USE THIS
		    $('#'+this.accessKey).append(this);
		    //scroll down
		    chatView.chatList.animate({ scrollTop: chatView.chatList[0].scrollHeight}, 1);
	    };*/
	    
	    ///
	 // get preview element
	    // prepare HTML5 FileReader
	    var oReader = new FileReader();
	        oReader.onload = function(e){
		        // e.target.result contains the DataURL which we will use as a source of the image
	        	image.src = e.target.result;
	        	$("#"+imgId).append(image);
	        	 scaleImage(image,image.width,image.height);
		        // read selected file as DataURL

	        };
	       oReader.readAsDataURL(oFile);
       	  
	       
}
	        
function doInnerUpdates() { // we will use this function to display upload speed
    var iCB = iBytesUploaded;
    var iDiff = iCB - iPreviousBytesLoaded;

    // if nothing new loaded - exit
    if (iDiff == 0)
        return;

    iPreviousBytesLoaded = iCB;
    iDiff = iDiff * 2;
    var iBytesRem = iBytesTotal - iPreviousBytesLoaded;
    var secondsRemaining = iBytesRem / iDiff;

    // update speed info
    var iSpeed = iDiff.toString() + 'B/s';
    if (iDiff > 1024 * 1024) {
        iSpeed = (Math.round(iDiff * 100/(1024*1024))/100).toString() + 'MB/s';
    } else if (iDiff > 1024) {
        iSpeed =  (Math.round(iDiff * 100/1024)/100).toString() + 'KB/s';
    }

    document.getElementById('speed').innerHTML = iSpeed;
    document.getElementById('remaining').innerHTML = '| ' + secondsToTime(secondsRemaining);        
}
function uploadProgress(e) { // upload process in progress
    if (e.lengthComputable) {
        iBytesUploaded = e.loaded;
        iBytesTotal = e.total;
        var iPercentComplete = Math.round(e.loaded * 100 / e.total);
        var iBytesTransfered = bytesToSize(iBytesUploaded);

        document.getElementById('progress_percent').innerHTML = iPercentComplete.toString() + '%';
        document.getElementById('progress').style.width = (iPercentComplete * 4).toString() + 'px';
        document.getElementById('b_transfered').innerHTML = iBytesTransfered;
        if (iPercentComplete == 100) {
            var oUploadResponse = document.getElementById('upload_response');
            oUploadResponse.innerHTML = '<h1>Please wait...processing</h1>';
            oUploadResponse.style.display = 'block';
        }
    } else {
        document.getElementById('progress').innerHTML = 'unable to compute';
    }
}

function uploadFinish(e) { // upload successfully finished
   // var oUploadResponse = document.getElementById('upload_response');
   // oUploadResponse.innerHTML = e.target.responseText;
    //oUploadResponse.style.display = 'block';
	var responseValue=e.target.responseText.split(",");
	
   
	//save Image into Contact conversationList(localStorage)
	var newMessageArray = new Array();
	newMessageArray.push(new MessageIM(user_id,user_phone,toUserId,"",responseValue[2],Math.round(+new Date()/1000),e.total,0,0,"31"));
	
	chatHelper.updateStoredMessagesIM(newMessageArray,toUserId);
	
   // document.getElementById('progress_percent').innerHTML = '100%';
    //document.getElementById('progress').style.width = '400px';
    //document.getElementById('filesize').innerHTML = sResultFileSize;
    //document.getElementById('remaining').innerHTML = '| 00:00:00';

    //clearInterval(oTimer);
}

function uploadError(e) { // upload error
    //document.getElementById('error2').style.display = 'block';
	alert("upload error");
    clearInterval(oTimer);
}  

function uploadAbort(e) { // upload abort
    document.getElementById('abort').style.display = 'block';
    clearInterval(oTimer);
}