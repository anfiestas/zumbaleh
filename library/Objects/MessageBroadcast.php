<?php 
class MessageBroadcast {
  
    private $mid;
    public $uid;
    private $broadcastId;
    private $externalId;
    public $destinationNumber;
    private $countryId;
    public $text;
    private $userCost;
    private $realCost;
    private $providerId;
    public $status;
    private $statusDetail;
    public $statusTimeStamp;
    
    //Setters
    public function setMessageId($mid)
    {
        $this->mid=$mid;
    }
    
    public function setUserId($uid)
    {
        $this->uid=$uid;
    }
    
    public function setBroadcastId($broadcastId)
    {
        $this->broadcastId=$broadcastId;
    }
    
    public function setExternalId($externalId)
    {
        $this->externalId=$externalId;
    }
    
    public function setDestinationNumber($destinationNumber)
    {
        $this->destinationNumber=$destinationNumber;
    }
    
    public function setUserCost($userCost)
    {
        $this->userCost=$userCost;
    }
    
    public function setText($text)
    {
        $this->text=$text;
    }
    
    public function setCountryId($countryId)
    {
        $this->countryId=$countryId;
    }
    
    public function setRealCost($realCost)
    {
        $this->realCost=$realCost;
    }
    
    public function setProviderId($providerId)
    {
        $this->providerId=$providerId;
    }
    
    public function setStatus($status)
    {
        $this->status=$status;
    }
    
    public function setStatusDetail($status_detail)
    {
        $this->statusDetail=$status_detail;
    }
    
    public function setStatusTimeStamp($statusTimeStamp)
    {
        $this->statusTimeStamp=$statusTimeStamp;
    }

    
    //Getters
    public function getMessageId()
    {
        return $this->mid;
    }
    
    public function getUserId()
    {
        return $this->uid;
    }
    
    public function getBroadcastId()
    {
        return $this->broadcastId;
    }
    
    public function getExternalId()
    {
        return $this->externalId;
    }
    
    public function getDestinationNumber()
    {
        return $this->destinationNumber;
    }
    
    public function getUserCost()
    {
        return $this->userCost;
    }
    
    public function getCountryId()
    {
        return $this->countryId;
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function getRealCost()
    {
        return $this->realCost;
    }
    
    public function getProviderId()
    {
        return $this->providerId;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getStatusDetail()
    {
        return $this->statusDetail;
    }
    
    public function getStatusTimeStamp()
    {
        return $this->statusTimeStamp;
    }

}
?>