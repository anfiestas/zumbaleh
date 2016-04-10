
function GroupHelper() {
	this.strUrl=$domain+$rootFolder+"groups";
	this.toGroupId=null;
}

/******************** getGroupInfo *******************/
GroupHelper.prototype.getGroupInfo = function(userId,group){

var url=this.strUrl+"/info";
 if($localStorage.getItem("user_id")!=null && getLocalStorageItem("userpass")!=null){
  params="user_id="+userId+"&to_group_id="+group.userId+"&user_timestamp="+Math.round(+new Date()/1000);

  httpAuthRequest($localStorage.getItem("user_id"),getLocalStorageItem("im_user_key"),url,params,'','GET','',group,getInfoCallback,getInfoError);
 }

};

/****** CallBacks *****/
var getInfoCallback = function(result,group){

	resultValues=result.split(",");
	if(resultValues[0]=="OK" && resultValues[1] > 0){
		//Removes ending of result because we don't need
		resultValues.splice(resultValues.length-3,3);
		result=resultValues.toString();
		result=result.replace(resultValues[0]+","+resultValues[1]+",", "");
		
		groupMemberListResult = parseGroupMemberResponse(result);
		
		}
		
	if (groupMemberListResult!=null && groupMemberListResult!=""){
       
		group.setMembers(groupMemberListResult);
		setLocalStorageItem("group."+group.userId,JSON.stringify(group));
		
//		var contactsMap = contactsHelper.createContactsHashMap(contactListResult);
//		setLocalStorageItem("contactsMap",JSON.stringify(contactsMap));
		
		//Reload current page
		//$.mobile.changePage($.mobile.activePage, { allowSamePageTransition: "true",transition: "none"} );
		//conversationsHelper.refreshContactList(contactListResult);
		//notify('info','Your Contacts has been synchronized');
	}
	
};

var getInfoError = function(result){
//Show Error notification or threatment
	//TODO Why this error
	//alert("in sync caaaaalBack ERROR");
};

/************** Private functions *************/

/**
 * 
 * @param resultData
 * @returns {___contact}
 */
function parseGroupMemberResponse (resultData){
	try{
	var contactArray = new Array();
		if (resultData != null) {
		var contacts = resultData.split(",9&c3");
		for ( var i = 0; i < contacts.length; i++) {
			if(contacts[i]!=""){
			var contact = parseGroupMember(contacts[i]);
			contactArray.push(contact);
			
			}
		}
       
	}
	return contactArray;
	
	}catch(exception){
		if($debug==true)
			alert("Exception in chatHelper parsePengingIMAndRefresh': " + exception);
	}
};

/**
 * 
 * @param contactData
 * @returns {___contact}
 */
function parseGroupMember (contactData){
	   var timestamp=0;
	if(contactData!=null){
		var contactProperties=contactData.split(",");
	
		var contact = new GroupMember(contactProperties[0],contactProperties[1]);
	return contact;
}
		
};