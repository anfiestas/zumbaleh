<?php 
class Transaction {
    
    private $tid;
    private $orderId;
    private $userId;
    private $shortPhone;
    private $promoId;
    private $promoCode;
    private $amount;
    private $currencyId;
    private $countryId;
    private $productId;
    private $groupId;
    private $startTime;
    private $endTime;
    private $status;
    private $statusDetail;
    private $gateway;
     
    public function __construct($tid, $orderId, $userId, $shortPhone, $amount, $currencyId,$countryId, $productId, $startTime, $endTime, $status, $statusDetail,$gateway,$promoId,$promoCode,$groupId)
	{
		$this->tid         	 = $tid;
		$this->orderId     	 = $orderId;
		$this->userId      	 = $userId;
		$this->shortPhone      	 = $shortPhone;
		$this->promoId      	 = $promoId;
		$this->promoCode      	 = $promoCode;
		$this->amount      	 = $amount;
		$this->currencyId  	 = $currencyId;
		$this->countryId  	 = $countryId;
                $this->productId   	 = $productId;
		$this->startTime   	 = $startTime;
		$this->endTime     	 = $endTime;
                $this->status     	 = $status;
		$this->statusDetail      = $statusDetail;
		$this->gateway     	 = $gateway;
		$this->groupId		 = $groupId;
	}
    
    public function setId($tid)
    {
        $this->tid=$tid;
    }
    
    public function setOrderId($orderId)
    {
        $this->orderId=$orderId;
    }
    
    public function setUserId($userId)
    {
        $this->userId=$userId;
    }
    
    public function setShortPhone($shortPhone)
    {
        $this->shortPhone=$shortPhone;
    }
    
    public function setAmount($amount)
    {
        $this->amount=$amount;
    }
    
    public function setCurrencyId($currencyId)
    {
        $this->currencyId=$currencyId;
    }
    
     public function setCountryId($countryId)
    {
        $this->countryId=$countryId;
    }
    
    public function setProductId($productId)
    {
        $this->productId=$productId;
    }
    
    
    public function setStartTime($startTime)
    {
        $this->startTime=$startTime;
    }
    
    public function setEndTime($endTime)
    {
        $this->endTime=$endTime;
    }
    
    public function setStatus($status)
    {
        $this->status=$status;
    }
    
    public function setStatusDetail($statusDetail)
    {
        $this->statusDetail=$statusDetail;
    }
    
    public function setGateWay($gateway)
    {
        $this->gateway=$gateway;
    }
    
    public function setPromoId($promoId)
    {
        $this->promoId=$promoId;
    }
    
    public function setPromoCode($promoCode)
    {
        $this->promoCode=$promoCode;
    }
    
    public function setGroupId($groupId)
    {
        $this->groupId=$groupId;
    }
    
    public function getId()
    {
        return $this->tid;
    }
    
    public function getOrderId()
    {
        return $this->orderId;
    }
    
    public function getUserId()
    {
        return $this->userId;
    }
    
    public function getShortPhone()
    {
        return $this->shortPhone;
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getCurrencyId()
    {
        return $this->currencyId;
    }
    
    public function getCountryId()
    {
        return $this->countryId;
    }
    
    public function getProductId()
    {
        return $this->productId;
    }
    
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getStatusDetail()
    {
        return $this->statusDetail;
    }
    
    public function getGateway()
    {
        return $this->gateway;
    }
    
    public function getPromoId()
    {
        return $this->promoId;
    }
    
    public function getPromoCode()
    {
        return $this->promoCode;
    }
    
    public function getGroupId()
    {
        return $this->groupId;
    }
}
?>