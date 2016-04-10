function httpAuthRequest(usernameEncoded,secretPass,strUrl,httpParams,paramsType,method,resultType,event,cb,cbError)
{
	try{
	authParams=getAuthenticationTokens(usernameEncoded,secretPass,strUrl,httpParams,method);
		// Asynchronously refresh the message list
		$.ajax({
			type: method,
		    url: strUrl,
			data: httpParams,
		    dataType: resultType,
		    cache:true,
		    timeout:120000,
			beforeSend: function(xhr) {
				xhr.setRequestHeader(authParams[0][0],authParams[0][1]);
				xhr.setRequestHeader(authParams[1][0], authParams[1][1]);
				xhr.setRequestHeader("Access-Control-Allow-Origin", "*");
				if(paramsType!=""){
					if (paramsType=="json")
						xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");
				}
		
			},
			success: function(result) {
			    	   cb(result, event);
			    	   
			     },
			error: cbError
		});
	
	}catch(exception){
		if($debug==true)
			alert("Exception in httpAuthRequest': " + exception);
	}
} 

function getAuthenticationTokens(usernameEncoded,secretPass,strUrl,httpParams,method){
	try{
	var $headerDate = new Date().toUTCString();
	var $URI = strUrl.replace($domain,"");
	//Only params by URL if GET method, otherwise params on the body
	if(method=="GET")
		$URI+="?"+httpParams;
	//$URI=$URI.split("?")[0];
	var $canonicalString1 = $headerDate +':'+encodeURI($URI)+':'+method;
	var $b64MACApply = Crypto.HMAC(Crypto.SHA1, $canonicalString1, "c54813f3e70d6c20b95de6fe657377e3", { asString: true });

	$.base64.is_unicode = false;
	$b64MACApply = $.base64.encode($b64MACApply);

    var $canonicalString2 = $headerDate +':'+"zumbaleh!"+':'+usernameEncoded;
    
	var $b64MACUser = Crypto.HMAC(Crypto.SHA1, $canonicalString2, secretPass, { asString: true });
    $b64MACUser = $.base64.encode($b64MACUser);

	var AuthParamsArray= new Array();
	AuthParamsArray[0]=new Array("X-Authorizations","zumbaleh! "+$.base64.encode(usernameEncoded)+":"+$b64MACApply+":"+$b64MACUser);
	AuthParamsArray[1]=new Array("X-Date",$headerDate);
	return AuthParamsArray;
	
	}catch(exception){
		if($debug==true)
			alert("Exception in getAuthenticationTokens': " + exception);
	}


}
