/*Playbook MenuBar*/
function setHandlers()
{
	if ((typeof blackberry == "undefined") || (typeof blackberry.app == "undefined")) return false;
			
	//WebWorks API Reference guide:
	//	http://www.blackberry.com/developers/docs/webworks/api/playbook/
	//
	blackberry.app.event.onSwipeDown(showMenuBar);
}
		
function showMenuBar()
{ 	$page = $(":jqmData(role='page')");

   //Adds the authentication parameters to urls that needs to load user session on server
	if($("#menuBar").hasClass("showMenuBar")){
		hideMenuBar();
	}
	else{
		//$("#menuBar").show();
		document.getElementById("menuBar").className = "showMenuBar";
		//$("#menuBar").addClass("showMenuBar");
		$page.find('.content').live('click',function(event){
		hideMenuBar();
		});
	}
}
function hideMenuBar()
{   $page = $(":jqmData(role='page')");
	$page.find('.content').die('click');
	document.getElementById("menuBar").className = "hideMenuBar";
}	


function logoutAction()
{    $page = $(":jqmData(role='page')");
	conversationsHelper.logout();
	//Remove All localStorage
	localStorage.clear();
	sessionStorage.clear();
	
//	localStorage.removeItem("user_id");
//	localStorage.removeItem("userpass");
//	localStorage.removeItem("mac_address");
//	localStorage.removeItem("im_user_key");
//	localStorage.removeItem("contacts");
//
//	Removes only group data
//	Object.keys(localStorage)
//    .forEach(function(key){
//         //if (/^(group.)|(note-)/.test(key)) {
//    	if (/^(group.)/.test(key)) {
//             localStorage.removeItem(key);
//         }
//     });
	
	//$.mobile.changePage("#login-register", { transition: "none"});
    $(location).attr('href',$indexHtml);
		
}	