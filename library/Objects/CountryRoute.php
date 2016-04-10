<?php 
class CountryRoute {
    
    private $country_id;
    private $preference;
    private $provider_id;
    private $price;
    
    public function __construct($country_id,$preference,$provider_id,$price){
     
    $this->country_id=$country_id;
    $this->preference=$preference;
    $this->provider_id=$provider_id;
    $this->price=$price;
    
    }
    
    public function setCountryId($country_id)
    {
        $this->country_id=$country_id;
    }
     public function setPreference($preference)
    {
        $this->preference=$preference;
    }
    
     public function setProviderId($provider_id)
    {
        $this->provider_id=$provider_id;
    }
    
    public function setPrice($price)
    {
        $this->price=$price;
    }
    

    public function getCountryId()
    {
        return $this->country_id;
    }
    
    public function getPreference()
    {
        return $this->preference;
    }
    
    public function getProviderId()
    {
        return $this->provider_id;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    

}
?>