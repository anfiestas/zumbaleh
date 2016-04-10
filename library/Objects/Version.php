<?php 
class Version {
    
    private $id;
    private $number;
    private $validity;
    
    
    public function __construct($id,$number,$validity){
     
    $this->id=$id;
    $this->number=$number;
    $this->validity=$validity;
    }
    
    public function setId($id)
    {
        $this->id=$id;
    }
    
    public function setNumber($number)
    {
        $this->number=$number;
    }
    
    public function setValidity($validity)
    {
        $this->validity=$validity;
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getNumber()
    {
        return $this->number;
    }
    
    public function getValidity()
    {
        return $this->validity;
    }
    
    
}
?>