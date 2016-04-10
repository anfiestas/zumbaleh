<?php 
class Country {
    
    private $id;
    private $idd;
    private $country_code;
    private $mobile_code;
    private $national_prefix;
    private $name;
    private $coverage;
    private $groupId;
    
    public function __construct($id,$idd,$country_code,$mobile_code,$national_prefix,$coverage,$name,$groupId){
     
    $this->id=$id;
    $this->idd=$idd;
    $this->country_code=$country_code;
    $this->mobile_code=$mobile_code;
    $this->national_prefix=$national_prefix;
    $this->name=$name;
    $this->coverage = $coverage;
    $this->groupId = $groupId;
    
    }
    
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setIdd($idd)
    {
        $this->idd=$idd;
    }
    
     public function setCountryCode($country_code)
    {
        $this->country_code=$country_code;
    }
    
     public function setMobileCode($mobile_code)
    {
        $this->mobile_code=$mobile_code;
    }
    
    public function setNationalPrefix($national_prefix)
    {
        $this->national_prefix=$national_prefix;
    }
    
     public function setName($name)
    {
         $this->name=$name;
    }
    
    public function setCoverage($coverage)
    {
         $this->coverage=$coverage;
    }
    
    public function setGroupId($groupId)
    {
        $this->groupId=$groupId;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getIdd()
    {
        return $this->idd;
    }
    
    public function getCountryCode()
    {
        return $this->country_code;
    }
    
    public function getMobileCode()
    {
        return $this->mobile_code;
    }
    
    public function getNationalPrefix()
    {
        return $this->national_prefix;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getCoverage()
    {
        return $this->coverage;
    }
    
    public function getGroupId()
    {
        return $this->groupId;
    }

}
?>