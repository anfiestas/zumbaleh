function MessageIM(fromUserId,fromPhone,toUserId,toPhone,text,timestamp,fileSize,isFromGroup,fromGroupMember,messageType) {
	this.fromPhone = fromPhone;
	this.toUserId = toUserId;
	this.fromUserId = fromUserId;
	this.toPhone = toPhone;
	this.text=text;
	this.timestamp = timestamp;
	this.fileSize = fileSize;
	this.isFromGroup = isFromGroup;
	this.fromGroupMember = fromGroupMember;
	this.messageType = messageType;
	
}
