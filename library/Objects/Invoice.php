<?php 
class Invoice {
    
    private $id;
    private $orderId;
    private $userId;
    private $shortPhone;
    private $amount;
    private $currencyId;
    private $countryId;
    private $productId;
    private $startTime;
    private $endTime;
    private $gateway;
     
    public function __construct($id, $orderId, $userId, $shortPhone, $amount, $currencyId,$countryId, $productId, $startTime, $endTime,$gateway)
	{
		$this->id         	 = $id;
		$this->orderId     	 = $orderId;
		$this->userId      	 = $userId;
		$this->shortPhone      	 = $shortPhone;
		$this->amount      	 = $amount;
		$this->currencyId  	 = $currencyId;
		$this->countryId  	 = $countryId;
                $this->productId   	 = $productId;
		$this->startTime   	 = $startTime;
		$this->endTime     	 = $endTime;
		$this->gateway     	 = $gateway;
	}
    
    public function setId($id)
    {
        $this->id=$id;
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
    
    
    public function setGateWay($gateway)
    {
        $this->gateway=$gateway;
    }
    
    public function getId()
    {
        return $this->id;
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
    
    public function getGateway()
    {
        return $this->gateway;
    }
}
?>