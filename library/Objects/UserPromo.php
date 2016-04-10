<?php 
class UserPromo {
    
    private $uid;
    private $promoId;
    private $promoCode;
    private $isActive;
    
    public function __construct($uid,$promoId,$promoCode,$isActive){
     
    $this->uid=$uid;
    $this->promoId=$promoId;
    $this->promoCode=$promoCode;
    $this->isActive=$isActive;
    }
    
    public function setUserId($uid)
    {
        $this->uid=$uid;
    }
    
    public function setPromoId($promoId)
    {
        $this->promoId=$promoId;
    }
    
    public function setPromoCode($promoCode)
    {
        $this->promoCode=$promoCode;
    }
    
    public function setIsActive($isActive)
    {
         $this->isActive=$isActive;
    }
    
    public function getUserId()
    {
        return $this->uid;
    }
    
    public function getPromoId()
    {
        return $this->promoId;
    }

    public function getPromoCode()
    {
        return $this->promoCode;
    }
    
    public function getIsActive()
    {
        return $this->isActive;
    }
    
}
?>