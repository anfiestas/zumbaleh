function getEditContatPanel(contact){

	markup = '<div class="content-bar ui-bar ui-bar-c">';
	markup+=	'<div>';
	markup+=		'<img id="addContact" width="96" height="96" src="images/contactNoPicture.png" alt="Add picture">';
	markup+=	'</div>';
	markup+=	'</br>';
	markup+=	'<fieldset data-role="fieldcontain">';
	markup+=		'<label for="contact_firstname">First Name</label>';
	markup+=		'<input type="text" name="contact_firstname" id="contact_firstname" value="'+contact.firstName+'" />';								 
	markup+=		'<label for="contact_lastname">Last Name</label>'; 
	markup+=		'<input type="text" name="contact_lastname" id="contact_lastname" value="'+contact.lastName+'"/>';
									 
	markup+=		'<label for="contact_phone_number_0">Phone <a href="#" id="showAddPhoneField" data-role="button" data-icon="plus" data-iconpos="notext"></a></label>';
	               for (var i=0; i< contact.phones.length; i++){
	markup+=		(i!=0)?'<label for="contact_phone_number_'+i+'"></label>':"";

	markup+=		'<input style="width:36%" type="text" name="contact_phone_number" id="contact_phone_number_'+i+'" class="contact_phone_number" value="'+contact.phones[i].number+'"/>';
	markup+=        '<label style="width:0%" for="contact_phone_type" class="ui-hide-label"></label>';
	markup+=		'<select name="contact_phone_type" id="contact_phone_type" class="contact_phone_type">';
	                 if(contact.phones[i].description!="Mobile" && contact.phones[i].description!="Work" 
	                	    && contact.phones[i].description!="Home" && contact.phones[i].description!="Other"){
	markup+=                	 '<option value="'.concat(contact.phones[i].description,'" selected >',contact.phones[i].description,'</option>');
	                    }
	markup+=			'<option value="Mobile"'.concat(("Mobile"==contact.phones[i].description)? ' selected' : "",'>Mobile</option>');
	markup+=			'<option value="Work"'.concat(("Work"==contact.phones[i].description)? ' selected' : "",'>Work</option>');
	markup+=			'<option value="Home"'.concat(("Home"==contact.phones[i].description)? ' selected' : "",'>Home</option>');
	markup+=			'<option value="Other"'.concat(("Other"==contact.phones[i].description)? ' selected' : "",'>Other</option>');
	markup+=		'</select>';				
				   }

	markup+=	'</fieldset>';
	markup+= '</div>';

	markup+= '<div class="floatRight">';
	markup+= 	'<button type="button" name="submit" id="submitSaveContact" data-inline="true">Save Changes</button>';
	markup+= 	'<button type="button" name="submit" id="submitCancelContact" data-inline="true">Cancel</button>';
	markup+= '</div>';
	
return markup;
}

function refreshContactListFromView(action,contactListArray){
	markup="";
	
	 if(contactListArray!="" && contactListArray.length > 0){
			
		 var markup="";
	     var currentLetter="";
		 
		 for (var i = 0; i < contactListArray.length; i++) {
			 if(contactListArray[i].syncStatus =='undefined' || contactListArray[i].syncStatus!="removed"){
		      var nextLetter=contactListArray[i].firstName.charAt(0);
			  if(currentLetter!=nextLetter){
			      var currentLetter = nextLetter;
			      markup+='<li data-role="list-divider">'+currentLetter+'</li>';
			  }
			  markup+='<li ';
			  if($glbSelectedContact!=null){
			  if(typeof($glbSelectedContact)!='undefined' && contactListArray[i].firstName==$glbSelectedContact.firstName && contactListArray[i].lastName==$glbSelectedContact.lastName)
					markup+='class="ui-btn-active"';
			  }
			  markup+='><a id="#'+action+'?contact='+i+'">'+contactListArray[i].firstName+' '+contactListArray[i].lastName+'</a></li>';
			  
			  }
		 }
	
	 }
	 
	 return markup;
}

function getAddNewPhoneNumber(){
	
	var markup=		'<label for="contact_phone_number2"></label>';
	markup+=		'<input style="width:36%" type="text" name="contact_phone_number" id="contact_phone_number2" class="contact_phone_number"/>';
	markup+=        '<label style="width:0%" for="contact_phone_type2" class="ui-hide-label"></label>';
	markup+=		'<select name="contact_phone_type" id="contact_phone_type2" class="contact_phone_type">'; 
	markup+=			'<option value="Mobile">Mobile</option>';
	markup+=			'<option value="Work">Work</option>';
	markup+=			'<option value="Home">Home</option>';
	markup+=			'<option value="Other">Other</option>';
	markup+=			'</select>';
	
	return markup;
	
}

function getAddContactForm(){
	
	markup = '<div class="content-bar ui-bar ui-bar-c">';
	markup+=	'<div>';
	markup+=		'<img id="addContact" width="96" height="96" src="images/contactNoPicture.png" alt="Add picture">';
	markup+=	'</div>';
	markup+=	'</br>';
	markup+=	'<fieldset data-role="fieldcontain">';
	markup+=		'<label for="contact_firstname">First Name</label>';
	markup+=		'<input type="text" name="contact_firstname" id="contact_firstname" />';								 
	markup+=		'<label for="contact_lastname">Last Name</label>'; 
	markup+=		'<input type="text" name="contact_lastname" id="contact_lastname" />';
									 
	markup+=		'<label for="contact_phone_number_0">Phone <a href="#" id="showAddPhoneField" data-role="button" data-icon="plus" data-iconpos="notext"></a></label>';
	markup+=		'<input style="width:36%" type="text" name="contact_phone_number" id="contact_phone_number_0" class="contact_phone_number"/>';
	markup+=        '<label style="width:0%" for="contact_phone_type" class="ui-hide-label"></label>';
	markup+=		'<select name="contact_phone_type" id="contact_phone_type" class="contact_phone_type">'; 
	markup+=			'<option value="Mobile">Mobile</option>';
	markup+=			'<option value="Work">Work</option>';
	markup+=			'<option value="Home">Home</option>';
	markup+=			'<option value="Other">Other</option>';
	markup+=			'</select>';
	markup+=	'</fieldset>';
	markup+= '</div>'

	markup+= '<div class="floatRight">';
	markup+= 	'<button type="button" name="submit" id="submitAddContact" data-inline="true">Save</button>';
	markup+= 	'<button type="button" name="submit" id="submitCancelContact" data-inline="true">Cancel</button>';
	markup+= '</div>';
	
	return markup;
	
}

function showContactInfoPanel(contact){
	if(typeof(contact)!='undefined'){

	markup = '<div class="content-bar ui-bar ui-bar-c">';
	markup+=	'<div>';
	markup+=		'<img id="addContact" width="96" height="96" src="images/contactNoPicture.png" alt="Add picture">';
	markup+=	'</div>';
	markup+=	'<br/>';
	markup+=	'<fieldset data-role="fieldcontain">';
	markup+=		'<span class="contactName">'+contact.firstName+" ";							 
	markup+=		contact.lastName+'</span><br/><br/>';
	               for (var i=0; i< contact.phones.length; i++){
						markup+=		'<label for="contact_phone_number">'+contact.phones[i].number+' - <span class="phoneType">'+contact.phones[i].description+'</span></label> <br/>';			       
				   }

	markup+=	'</fieldset>';
	markup+= '</div>';

	markup+= '<div class="actions-bar ui-bar floatRight">';
	markup+= '</div>';
    
	return markup;
	}
	}