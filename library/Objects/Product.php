<?php 
class Product {
    
    private $productId;
    private $name;
    private $creditsNumber;
    private $price;
    private $priceAndSymbol;
    private $currencyId;
    private $priceSms;
    private $creditsSms;
    private $promoId;
    private $freeCredits;
    private $groupId;
    
    public function __construct($productId,$name,$creditsNumber,$freeCredits,$price,$priceAndSymbol,$currencyId,$priceSms,$creditsSms,$promoId,$groupId){
     
    $this->productId=$productId;
    $this->name=$name;
    $this->creditsNumber=$creditsNumber;
    $this->price=$price;
    $this->priceAndSymbol=$priceAndSymbol;
    $this->currencyId=$currencyId;
    $this->priceSms=$priceSms;
    $this->creditsSms=$creditsSms;
    $this->promoId=$promoId;
    $this->freeCredits=$freeCredits;
     $this->groupId=$groupId;
    }
    
    public function setId($productId)
    {
        $this->productId=$productId;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function setCreditsNumber($creditsNumber)
    {
         $this->creditsNumber=$creditsNumber;
    }
    
    public function setPrice($price)
    {
        $this->price=$price;
    }
    
    public function setPriceAndSymbol($priceAndSymbol)
    {
        $this->priceAndSymbol=$priceAndSymbol;
    }
    
    public function setCurrency($currencyId)
    {
         $this->currencyId=$currencyId;
    }
    
    public function setPriceSms($priceSms)
    {
         $this->priceSms=$priceSms;
    }
    
    public function setCreditsSms($creditsSms)
    {
         $this->creditsSms=$creditsSms;
    }
    
    public function setFreeCredits($freeCredits)
    {
         $this->freeCredits=$freeCredits;
    }
    
    public function setPromoId($promoId)
    {
         $this->promoId=$promoId;
    }
    
    public function setGroupId($groupId)
    {
        $this->groupId=$groupId;
    }
    
    public function getId()
    {
        return $this->productId;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getCreditsNumber()
    {
        return $this->creditsNumber;
    }
    
    public function getFreeCredits()
    {
        return $this->freeCredits;
    }

    public function getPrice()
    {
        return $this->price;
    }
    
    public function getPriceAndSymbol()
    {
        return $this->priceAndSymbol;
    }
    
    public function getCurrencyId()
    {
        return $this->currencyId;
    }
    
    public function getPriceSms()
    {
        return $this->priceSms;
    }
    
    public function getCreditsSms()
    {
        return $this->creditsSms;
    }
    
    public function getPromoId()
    {
        return $this->promoId;
    }
    
    public function getGroupId()
    {
        return $this->groupId;
    }
}
?>