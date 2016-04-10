<?php 
class UserDevice {
    
    private $id;
    private $uid;
    private $pin;
    private $mac_address;
    
    
    public function __construct($id,$uid,$pin,$mac_address){
     
    $this->id=$id;
    $this->uid=$uid;
    $this->pin=$pin;
    $this->mac_address=$mac_address;
    }
    
    public function setId($id)
    {
        $this->id=$id;
    }
    
    public function setUserId($uid)
    {
        $this->uid=$uid;
    }
    
    public function setPin($pin)
    {
        $this->pin=$pin;
    }

    public function setMacAddress($mac_address)
    {
        $this->mac_address=$mac_address;
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUserId()
    {
        return $this->uid;
    }
    
    public function getPin()
    {
        return $this->pin;
    }

    public function getMacAddress()
    {
        return $this->mac_address;
    }
    
    
}
?>