//Global constants
$MOBILE="mobile";
$TABLET="tablet";
$DESKTOP="desktop";
//Global vars
var conversationsHelper = new ConversationsHelper();
var $deviceLayout;
var window_focused=true;


if(localStorage.getItem("keep_me_logged")=="true" || localStorage.getItem("keep_me_logged")==null){
	var $localStorage=localStorage;
}else{
	var $localStorage=sessionStorage;
}
	



//Set deviceLayout at init
//if($deviceLayout==null)
	  //$deviceLayout= getDeviceLayout();
$deviceLayout=$DESKTOP;
//refreshLayout();
	
//var $autoLayout=true;
/**
 * TEST PARAMS
  var $domain="http://localhost:8080/spoora/public";
  var $rootFolder = "/imservices/";
 */

/**
 * PRODUCTION PARAMS */
  //var $domain="https://www.myspoora.com";
  var $domain=location.href.replace(location.hash,"");
  var $rootFolder = "imservices/";

var $indexHtml=""
var $lang="en";
var $debug=false;  
var $cryptLocalStorage = true;

$( document ).bind( "mobileinit", function() {
    // Make your jQuery Mobile framework configuration changes here!
    $.mobile.allowCrossDomainPages = true;
    $.mobile.defaultPageTransition = 'none';
    //$.mobile.page.prototype.options.domCache = true;
    //ONLY MOBILE
    //$.mobile.touchOverflowEnabled = true;
    //$.mobile.fixedToolbars.setTouchToggleEnabled(true);
    

	 //Resize each page on show
	 $(":jqmData(role='page')").live("pageshow", function(event) {
	   //enable this on tablets
	   ResizePageContentHeight(event.target);
	   //Language page


		  ga('send', 'pageview', {
		  'page': $.mobile.activePage.attr("data-url"),
		  'title': $.mobile.activePage.attr("data-url")
		});

	 });

    
	 
	$(document).ajaxError(function(e, xhr, settings) {
		
	       if(xhr.status==401){
				notify('error','Authentication failed');
				$.mobile.changePage("#login-register");
			  }
		
	       /* if(xhr.status==500) 
	        * notify('error','Internal server error');*/
		   if(xhr.status==404)
			  notify('info','Resource does not exist');
			  
			  
			$.mobile.hidePageLoadingMsg();
		  
		});
	
	
});


//Controls windows resize
//TODO - control this
$(window).resize(function() {
	 // alert("resizing");
	//refreshLayout();
	$page=$.mobile.activePage;
	ResizePageContentHeight($page);
	
	});
//On windows close
$(window).bind('beforeunload', function(){
	 //clean localStorage if using sessionStorage and close server session
	 if(localStorage.getItem("keep_me_logged")=="false"){
		 localStorage.clear();
		 //Close server session
		 conversationsHelper.logout();
	}
	 

});

/*$(window).hover(function(event) {
	
    if (!event.fromElement) {
        console.log("active");
    }//else inactive
});*/

/*
 * When our app loses focus*/
  function onBlur() {
	 //console.log("inactive");
	  window_focused=false;
	   execInterval=12000;
};

function onFocus(){
	 //console.log("active");
	if(localStorage.getItem("keep_me_logged")!=null){
		 execInterval=3000;
		conversationsHelper.login();
	}

	 
	window_focused=true;

};

if (/*@cc_on!@*/false) { // check for Internet Explorer
	document.onfocusin = onFocus;
	document.onfocusout = onBlur;
} else {
	window.onfocus = onFocus;
	window.onblur = onBlur;
}

$(document).ready(function() { 
		changeLang('');
	});

function changeLang(lang) {
  jQuery.i18n.properties({
	  name: 'index', 
	  mode:'both', 
	  language:lang,
	  path:'lang/',  
	  callback: function(){ 
	  	$('#textHeaderPrincipal1').html(spoora_title1);
	  	$('#login_header').html(login_header); 
	  	$('#login_name').attr("placeholder", login_user); 
	  	$('#login_pass').attr("placeholder", login_pass);
	  	$('#keep_me_logged_label').html(login_keep_loged); 
	  	$('#submitLogin').prev('span').find('span.ui-btn-text').text(login_now); 
	  	$('#passRemember').html(login_remember_pass); 
	  	$('#dontHave').html(login_dont_have);
	  	$('#login_get_one').html(login_get_one);
	  	$('.what_is').html(what_is);
	  	$('#what_is_desc').html(what_is_desc); 
	  	$('#how_it_works').html(how_it_works); 
	  	$('#how_it_works_desc').html(how_it_works_desc); 
	  	$('#step_1').html(step_1); 
	  	$('#step_2').html(step_2); 
	  	$('#step_3').html(step_3); 
	  	$('#joinUs').html(joinUs);
	  	$('#attending').html(attending);
	  	$('#madeIn').html(madeIn); 
	  	$('#totalSpoorers').html(totalSpoorers);
	  	$('#totalDonations').html(totalDonations);
	  	$('.donations_history').html(donations_history); 
	  	$('#donations_history_desc').html(donations_history_desc); 
	  	$('#donations_explain').html(donations_explain);
	  	$('#current_month').html(current_month);
	  	$('#total_donated_month').html(total_donated_month); 
	  	$('#headerEntity').html(headerEntity);
	  	$('#headerAmount').html(headerAmount); 
	  	$('#headerCode').html(headerCode); 
	  	$('#headerDate').html(headerDate); 
	  	$('.redCross').html(redCross);
	  	$('.contact').html(contact);
	  	$('#total_donated_march').html(total_donated_march);
	  	$('#donations_march').html(donations_march);
	  	$('#donations_from_start').html(donations_from_start); 
	  	$('#donations_april').html(donations_april);
	  	$('#total_donated').html(total_donated);
	  	$('#donations_may').html(donations_may);
		$('#donations_june').html(donations_june);
		$('#donations_july').html(donations_july);
		$('#donations_august').html(donations_august);
		$('#donations_september').html(donations_september);
		$('#spoora_works_with').html(spoora_works_with);
		$('#work_with_us_contact').html(work_with_us_contact);
		$('#donations_october').html(donations_october);
		$('#donations_november').html(donations_november);
		$('#donations_december').html(donations_december);
		$('#donations_janvier15').html(donations_janvier15);
		
		
		}
	});
}

/*This method is executed once when booting the App*/
//$(document).ready(function() {
	 


     /*$(document).ajaxSuccess(function(e, xhr, settings) {
		 
		  if (settings.url == "http://localhost:8080/n2websms/public/rest/users") {
			loginOK();
		  }
		  if (settings.url == "http://localhost:8080/n2websms/public/rest/messages") {
			showMessageList(xhr.responseText);
		  }
		  
	});*/

//	$(document).ajaxError(function(e, xhr, settings) {
//	
//       if(xhr.status==401){
//			notify('error','Authentication failed');
//			$.mobile.changePage("#login-register");
//		  }
//	   if(xhr.status==500)
//		  notify('error','Internal server error');
//	   if(xhr.status==404)
//		  notify('info','Resource does not exist');
//		  
//		  
//		$.mobile.hidePageLoadingMsg();
//	  
//	});
	
	
//});

