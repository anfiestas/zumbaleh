<?php 
class Currency {
    
    private $currencyId;
    private $name;
    private $code;
    private $htmlSymbol;
    private $oneEuroIs;
    private $oneTokenIs;
    
    
    public function __construct($currencyId,$name,$code,$htmlSymbol,$oneEuroIs,$oneTokenIs){
     
    $this->currencyId=$currencyId;
    $this->name=$name;
    $this->code=$code;
    $this->htmlSymbol=$htmlSymbol;
    $this->oneEuroIs=$oneEuroIs;
    $this->oneTokenIs=$oneTokenIs;
    }
    
    public function setId($currencyId)
    {
        $this->currencyId=$currencyId;
    }
    
    public function setName($name)
    {
        $this->setName=$name;
    }
    
    public function setCode($code)
    {
        $this->code=$code;
    }
    
    public function setHtmlSymbol($htmlSymbol)
    {
         $this->htmlSymbol=$htmlSymbol;
    }
    
    public function setOneEuroIs($oneEuroIs)
    {
        $this->oneEuroIs=$oneEuroIs;
    }
    
    public function setOneTokenIs($oneTokenIs)
    {
        $this->oneTokenIs=$oneTokenIs;
    }

    public function getId()
    {
        return $this->currencyId;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getHtmlSymbol()
    {
        return $this->htmlSymbol;
    }
    
    public function getOneEuroIs()
    {
        return $this->oneEuroIs;
    }

    public function getOneTokenIs()
    {
        return $this->oneTokenIs;
    }
}
?>