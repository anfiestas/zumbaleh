function ResizePageContentHeight(page)
{	
	 var $page = $(page);
      
     if($page[0].id!='faq' && $page[0].id!='donations'){
     var $content = $page.children(".ui-content");
     var hh = $page.children(".ui-header").outerHeight(); hh = hh ? hh : 0;
	 var fh = $page.children(".ui-footer").outerHeight(); fh = fh ? fh : 0;
     var fh = 0;
	 var pt = parseFloat($content.css("padding-top"));
	 var pb = parseFloat($content.css("padding-bottom"));
	 var wh = window.innerHeight;
	 var contentHeight=(wh - (hh + fh) - (pt + pb));
	 
	 //Centering banners horizontally 
	 var contentWidth = parseFloat($content.css("width"));	 
	 var whiteSpaceWidth=window.innerWidth- contentWidth;
	 
	 var $leftAd = $page.children("#leftAd");
	 var $rightAd = $page.children("#rightAd");
	 
	 var leftWhiteSpace=whiteSpaceWidth/2;
	 
	 

	 var sideAdSpace=leftWhiteSpace-parseFloat($leftAd.css("width"));
	 var AdPosition=sideAdSpace/2;
	 
	 //Centering banners vertically	 
	 var $leftAd = $page.children("#leftAd");
	 var $rightAd = $page.children("#rightAd");
	 
	 var whiteSpaceHeight=contentHeight-parseFloat($leftAd.css("height"));    
     
	 var hightWhiteSpace=whiteSpaceHeight/2;

	 var AdPositionH=hightWhiteSpace;
	 
	 //50 is the grey padding added on >=1600 resolutions
	 if(window.innerWidth>=1600){
		 AdPosition=AdPosition-50;

	 }
	 $leftAd.css("margin-top",AdPositionH);
	 $rightAd.css("margin-top",AdPositionH);
	 
	 
	 if(AdPosition>0){
		 $leftAd.css("margin-left",AdPosition);
		 $rightAd.css("right",AdPosition);
	 }
	 


	 $content.height(contentHeight);
	}
     //Set size of list on pages
     
	 //TODO: same thing for ui-block-b
	 //All list in ui-block-a have height limited and scroll
	 /* for Playbook *
     var $listContacts= $page.find(".content .ui-block-a ul,.content .ui-block-b");
     $listContacts.css("height",contentHeight-(pb+1));
     
     var $listReports = $page.find("#chatList");
     $listReports.css("height",contentHeight+25);
     */
	 /*Only for mobile
	 var $chatList = $page.find("#chatList ul");
	 $chatList.css("height",contentHeight+25);
	 */
	
     
}
/*******Encyrpting LocalStorage methods ********/
var getLocalStorageItem = function(itemName){

if($localStorage.getItem(itemName)!=null){
  try{	
	  if($cryptLocalStorage)
		  return Crypto.AES.decrypt($localStorage.getItem(itemName),$localStorage.getItem('user_id'));
	  else
		  return $localStorage.getItem(itemName);
  }catch(exception){
	  console.log("error getting contacts, user has changed...removing contacts and messages");
	  $localStorage.removeItem("contacts");
	  $localStorage.removeItem("messages");
	  return null;
  }
}
else{
	return null;
}

};

var setLocalStorageItem= function(itemName,itemValue){
	if ($cryptLocalStorage)
		$localStorage.setItem(itemName, Crypto.AES.encrypt(itemValue,$localStorage.getItem('user_id')));
	else
		$localStorage.setItem(itemName,itemValue);
};

var getDeviceLayout= function(){
	var isTouchDevice = function() {  return 'ontouchstart' in window || 'onmsgesturechange' in window; };
    var isDesktop = window.screenX != 0 && !isTouchDevice() ? true : false;
    
    if(!isDesktop){

		if($(window).height() > $(window).width() ){	
			return $MOBILE;//mobile
		}else{
			return $TABLET;//tablet
		}
    }else{

    	return $DESKTOP;//tablet
    }
	

}

var refreshLayout = function() {
	var current_path = window.location.pathname.split('/').pop();
	
	if($deviceLayout==$DESKTOP && current_path!="index.html"){
	
		//$.mobile.loadPage( "about/us.html" );
		window.location.replace('./index.html');
	}
	else if($deviceLayout==$TABLET && current_path!="tablet.html"){
		//$.mobile.loadPage( "about/us.html" );
		
		window.location.replace('./tablet.html');
	}
	else if($deviceLayout==$MOBILE && current_path!="mobile.html"){
		//$.mobile.loadPage( "about/us.html" );*
	
		window.location.replace('./mobile.html');
	}
}
/******* Notifications System ********/

function notify(notificationType,msg) {
	   // find any notification and display it as a popup
	   // will disperse them 100px apart
	   $(".notification").each(function(i) {
		  var $me = $(this);
		  if (notificationType=="success") aclass = "ui-body-suc";
		  if (notificationType=="error") aclass = "ui-body-err";
		  if (notificationType=="info") aclass = "ui-body-info";
		  if (notificationType=="warning") aclass = "ui-body-e";
		  $("<div class='ui-loader ui-overlay-shadow ui-body-e ui-corner-all'><h1>"+ msg +"</h1></div>").css({ 
			 "display": "block", 
			 "opacity": 0.96, 
			 "top": $(window).scrollTop() + 110 + (110 * i)
		  }).addClass(aclass).appendTo( $.mobile.pageContainer ).delay( 1800 + (400 * i) ).fadeOut( 1400 + (400*i), function(){
			 $(this).remove();
		  });
		  //$me.remove();  
	   });
	}
	
//Adds authParams to all url having loadExternal class   	
function addAuthParamsToURLs()
{   if(localStorage.getItem("user_id")!=null && localStorage.getItem("user_id")!=null){
		var $loadExternalHrefList=$('.loadExternal');
		for(i=0; i < $loadExternalHrefList.length; i++){
		   $loadExternalHref=$loadExternalHrefList[i];
		   
		   var externalUrl=$loadExternalHref.href.split("?",1)[0];
		   var authParams=getAuthenticationTokens(localStorage.getItem("user_id"),
												  localStorage.getItem("userpass"),
												  externalUrl,
												  "GET");
				
			$loadExternalHref.href=externalUrl+"?"+authParams[0][0]+"="+$.base64.encode(authParams[0][1])+"&"+authParams[1][0]+"="+$.base64.encode(authParams[1][1]);
		}
	} 

}
function loadURL(url){

  externalUrl=$domain+$rootFolder+$lang+"/"+url;
   var authParams=getAuthenticationTokens(localStorage.getItem("user_id"),
												  localStorage.getItem("userpass"),
												  externalUrl,
												  "GET");
followLink(externalUrl+"?"+authParams[0][0]+"="+$.base64.encode(authParams[0][1])+"&"+authParams[1][0]+"="+$.base64.encode(authParams[1][1]));
}

function followLink(address) {
	var encodedAddress = "";
	// URL Encode all instances of ':' in the address
	//encodedAddress = address.replace(/:/g, "%3A");
	// Leave the first instance of ':' in its normal form
	encodedAddress = address.replace(/%3A/, ":");
	// Escape all instances of '&' in the address
	encodedAddress = encodedAddress.replace(/&/g, "\&");
	
	if (typeof blackberry !== 'undefined') {
		try{
			// If I am a BlackBerry device, invoke native browser
			var args = new blackberry.invoke.BrowserArguments(encodedAddress);
			blackberry.invoke.invoke(blackberry.invoke.APP_BROWSER, args);
		} catch(e) {
 			alert("Sorry, there was a problem invoking the browser");
 		}
	} else {
		// If I am not a BlackBerry device, open link in current browser
		window.location = encodedAddress; 
	}
}

	