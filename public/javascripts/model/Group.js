
function Group() {

	this.userId = null;
	this.fullName = "";
	this.groupOwnerId=null;
	this.members = null;

}

function Group(userId, fullName,groupOwnerId) {

	this.userId = userId;
	this.fullName = fullName;
	this.groupOwnerId=groupOwnerId;
	this.members = null;

}

Group.prototype.setMembers = function(members){
	
	this.members=members;

};

Group.prototype.getGroupFromStorageById = function(groupId){
	
	groupJSON = getLocalStorageItem("group."+groupId);
	var group = null;
	if(groupJSON!=null){
		var group =  JSON.parse(groupJSON);
	}
	
	return group;

};
