var $glbSelectedContactId;
var $glbSelectedContact;

$('#contacts').live('pageinit',function(event){
	
	var contactListJSON = getLocalStorageItem("contacts"); 

	 if(contactListJSON!=null){
		var blockb=$("#contacts").find(".ui-content .ui-block-b");
		var blocka=$("#contacts").find(".ui-content .ui-block-a ul");
		//refresh new page content
		 blockb.html(" ").trigger("create");
		 blocka.html(" ").trigger("create");
	  }
	
	
});
$('#contacts').live('pageshow',function(event){

 //Update credits
 $user = JSON.parse(getLocalStorageItem("user")); 
 //$(".credits").html($user.balance);
 
 var $page = $('#contacts');   
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
		 refreshContactList(contactListArray);
     
});
/**
 * After do contact modifications, we try to submit changes to server
 * If fails, contacts will be in localStorage and they will be put
 * to server on next contactEditing or next appInit
 */
$('#contacts').live('pagebeforehide',function(event){
	
	//syncContacts();
});

//Show Edit Contact Form
$('#contacts #showEditContact').live('click',function(event){
$(this).addClass('ui-btn-active');
// Get the object that represents the contact we
// are interested in
var contactListJSON = getLocalStorageItem("contacts");

var contactListArray =  JSON.parse(contactListJSON);

if(contactListJSON!=null && $glbSelectedContactId!=null){
	
var contact = contactListArray[$glbSelectedContactId];
var blockb=$("#contacts").find(".ui-content .ui-block-b");

markup=getEditContatPanel(contact);

//refresh new page content
blockb.html(markup).trigger("create");
}
//exit(0);

});

$("#contacts #showAddPhoneField").live('click',function(event){


var elem=$("#contacts :jqmData(role='fieldcontain')");
var markup = getAddNewPhoneNumber();
elem.append(markup);
elem.trigger("create");

});
/******Show AddContact Form ******/
$("#contacts #showAddContact").live('click', function(event) {

	unselectAllListView();
	$glbSelectedContact = null;
	var blockb = $("#contacts").find(".content .ui-block-b");

	var markup = getAddContactForm();

	// refresh new page content
	blockb.html(markup).trigger("create");

});

/*Removes selected contact by Id*/

$('#contacts #submitRemoveContact').live('click',function(event){

  $(this).simpledialog({
    'mode' : 'bool',
    'prompt' : 'Remove this contact?',
    'useModal': true,
	'theme':'pickPageTheme',
    'buttons' : {
      'OK': {
        click: function () {
         removeSelectedContact();
        }
      },
      'Cancel': {
        click: function () {
		  //$(this).simpledialog.('close');
        },
        icon: "delete",
        theme: "c"
      }
    }
  });


});

$('#contacts #submitAddContact').live('click',function(event){

var contact = getCurrentFormContact();
contact.syncStatus="added";

var isFormDataValid = validateContactFormData(contact);
var contactListArray;
if (isFormDataValid) {
	//TODO - encode in base64 and crypt contact information
		//var jsonContact = JSON.stringify(contactsList);
		 contactListJSON = getLocalStorageItem("contacts");
		 if(typeof(contactListJSON)!="undefined" && JSON.parse(contactListJSON)!=null){
			contactListArray =  JSON.parse(contactListJSON);
		  }
		 else{
			//If first no contacts created before	
			contactListArray =  new Array();
		 }
		 //Add contact to list
		 contactListArray[contactListArray.length]=contact;
		 
		 $glbSelectedContact=contact;
		 
		 //Refresh contactList in left side
		 refreshContactList(contactListArray);
		 
		 //Store ordered contactList: contacts list is allways sorted in localStorage
		 setLocalStorageItem("contacts",JSON.stringify(contactListArray));
		 unselectAllContactsActionBar();
		 notify('info','Contact has been added');
	 
}


});

$('#contacts #submitSaveContact').live('click',function(event){

	var contactListJSON = getLocalStorageItem("contacts");
	var currentFormContact =getCurrentFormContact();
	var isFormDataValid = validateContactFormData(currentFormContact);
	
	if(contactListJSON!=null && isFormDataValid){
		$glbSelectedContact = currentFormContact;
		
		var contactListArray =  JSON.parse(contactListJSON);
	   //set sync_status=update
		$glbSelectedContact.syncStatus="updated";
		
		contactListArray[$glbSelectedContactId]=$glbSelectedContact;
		//Refresh contactList in left side
		refreshContactList(contactListArray);
	 
		//Store ordered contactList: contacts list is allways sorted in localStorage
		setLocalStorageItem("contacts",JSON.stringify(contactListArray));
		
		unselectAllContactsActionBar();
		notify('info','Contact has been saved');	
	}

});

/**Cancel*/
$('#contacts #submitCancelContact').live('click',function(event){
	
	var blockb=$("#contacts").find(".ui-content .ui-block-b");
	 markup=" ";
	//refresh new page content
	 blockb.html(markup).trigger("create");
	 
	 unselectAllContactsActionBar();
	 unselectAllListView();
});

/*
Click in a contact from listview
*/
$("#contacts :jqmData(role='listview') li").live('click',function(event){

unselectAllListView();
$(this).addClass("ui-btn-active");
var action=$(this).find('a').attr("id");
//replace all since except id by ""
if(action!=null){
	var contactId = action.replace(/.*contact=/, "" );
	$glbSelectedContactId=contactId;
	// Get the object that represents the contact we
	// are interested in
	var contactListJSON = getLocalStorageItem("contacts");
	if(contactListJSON!=null){
	var contactListArray =  JSON.parse(contactListJSON);
	$glbSelectedContact = contactListArray[contactId];
	
	var markup=showContactInfoPanel($glbSelectedContact);
	//refresh new page content
	var blockb=$("#contacts").find(".content .ui-block-b");
	blockb.html(markup).trigger( "create" );
}
}
});

/******************************************************************************Helper Functions******************************************************************************/

//contactListArray.sort(sort_by('firstName', true, function(a){return a.toUpperCase()}));
var sort_by = function(field, reverse, primer){

   var key = function (x) {return primer ? primer(x[field]) : x[field]};

   return function (a,b) {
       var A = key(a), B = key(b);
       return ((A < B) ? -1 :
               (A > B) ? +1 : 0) * [-1,1][+!!reverse];                  
   }
}

var sortByFullName = function(contactArray){

var sortedArray =  new Array();
 for (var i = 0; i < contactArray.length; i++) {
     sortedArray[i]=contactArray[i].firstName + ' ' + contactArray[i].lastName;
 }
 
 return sortedArray.sort();

}

var refreshContactList = function(contactListArray){

     //Sorts the array
	 //contactListArray=sortByFullName(contactListArray); 
	 contactListArray.sort(sort_by('firstName', true, function(a){return a.toUpperCase()}));
	 //listview = $("#contacts:jqmData(role='listview')");
	  var $page = $("#contacts");
	  var listview = $page.find(".content .ui-block-a ul");
	  var listHTML=refreshContactListFromView("contacts",contactListArray);
	 if(listHTML!=""){
		 listview.html(listHTML);
		 listview.listview('refresh');
     }

	 showContactInfoPanel($glbSelectedContact);

}

var unselectAllListView=function(){

listview = $(":jqmData(role='listview')");
listview.find("li").each(function(){
        $(this).removeClass("ui-btn-active");
		
      });
	  
}
var unselectAllContactsActionBar=function(){

	listview = $("#contactsActionBar");
	
	listview.find("a").each(function(){
	        $(this).removeClass("ui-btn-active");
			
	      });
		  
	}


var getCurrentFormContact=function(){
    var $page = $('#contacts');
    var contactFirstName=$page.find('#contact_firstname').val();
	var contactLastName=$page.find('#contact_lastname').val();
	var contactPhonesArray =  new Array();
	
	var contact = new Object();
	
	//if(typeof($glbSelectedContact.id)!=null)
    if(typeof($glbSelectedContact)!='undefined' && $glbSelectedContact!=null)
		contact.id=$glbSelectedContact.id;
	
	contact.firstName = contactFirstName;
	contact.lastName = contactLastName;
	
	
		//get all phones
	$('.contact_phone_number').each(function(index,elem){
		 var contactPhone = new Object();
		 if(elem.value!=""){
			contactPhone.number=elem.value;
			contactPhonesArray[index]=contactPhone;
		 }
	});
	
	$('.contact_phone_type').each(function(index,elem){
	    if(index < contactPhonesArray.length){
          contactPhonesArray[index].description=elem.value;
		  }
	});
	
	
	//contact.phone = contactPhone;
	contact.phones = contactPhonesArray;
	
	return contact;
}

var removeSelectedContact = function(){
var contactListJSON = getLocalStorageItem("contacts");
var contactListArray =  JSON.parse(contactListJSON);
	if(contactListJSON!=null && $glbSelectedContact!=null){
		
		$glbSelectedContact.syncStatus="removed";
		contactListArray[$glbSelectedContactId]=$glbSelectedContact;
		
		//Refresh contactList in left side
		refreshContactList(contactListArray);
	 
		//Store ordered contactList: contacts list is allways sorted in localStorage
		setLocalStorageItem("contacts",JSON.stringify(contactListArray));
		
		notify('info','Contact has been removed');
		
		//TODO goto initial contacts page
		var blockb=$("#contacts").find(".content .ui-block-b");
		markup= '';
		//refresh new page content
		blockb.html(markup).trigger( "create" );

	}
}

var validateContactFormData = function(contact){
	if(contact.firstName=="" && contact.lastName==""){
				notify('error','Name info cannot be empty');
	return false;
	}
	if(contact.phones.length==0 || contact.phones[0].number==''){
				notify('error','Phone cannot be empty');
	return false;
	}
  return true;

}



/*** TODO 
 * 
 * 1.Contact phones cannot be empty for the moment. Solution1: add always an empty phone per contact
 * 2.Delete phones on EditContact form.
 * 3.Accents when comparing json Objects on serverSide.
 */