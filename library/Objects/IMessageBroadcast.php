<?php
require_once 'Objects/MessageBroadcast.php';
class IMessageBroadcast extends MessageBroadcast {
    
    public $sourceNumber;
    public $sourcePin;
    public $destinationUserId;
    public $destinationPin;
    public $messageTypeId;
    public $groupMemberPin;
    
    //Setters
    public function setSourceNumber($sourceNumber)
    {
        $this->sourceNumber=$sourceNumber;
    }
    public function setSourcePin($sourcePin)
    {
        $this->sourcePin=$sourcePin;
    }
    public function setDestinationUserId($destinationUserId)
    {
        $this->destinationUserId=$destinationUserId;
    }

    public function setDestinationPin($destinationPin)
    {
        $this->destinationPin=$destinationPin;
    }
    
    public function setMessageTypeId($messageTypeId)
    {
        $this->messageTypeId=$messageTypeId;
    }
    
    public function setGroupMemberPin($groupMemberPin)
    {
        $this->groupMemberPin=$groupMemberPin;
    }

    
    public function getSourceNumber()
    {
        return $this->sourceNumber;
    }
    public function getSourcePin()
    {
        return $this->sourcePin;
    }
    
    public function getDestinationUserId()
    {
        return $this->destinationUserId;
    }

    public function getDestinationPin()
    {
        return $this->destinationPin;
    }
    
     public function getMessageTypeId()
    {
        return $this->messageTypeId;
    }

    public function getGroupMemberPin()
    {
        return $this->groupMemberPin;
    }
   
}
?>