<?php 
class User {
    
    private $uid;
    private $name;
    private $mail;
    private $password;
    private $fullPhone;
    private $shortPhone;
    private $tempPhone;
    private $balance;
    private $countryId;
    private $imSecretKey;
    private $secretKey;
    private $pin;
    private $groupId;
    private $lastConnection;
    private $connectionTypeId;
    private $isGroup;
    private $groupOwnerId;
    private $groupOwnerPin;
    private $tokens;
    private $tokensProgram;
    private $tokensDay;
    private $bannerImpr;
    private $alias;
    private $description;

    
    public function __construct($uid,$name,$mail,$password,$fullPhone,$shortPhone,$balance,$countryId,$imSecretKey,$secretKey,$pin,$groupId,$lastConnection,$connectionTypeId,$isGroup,$groupOwnerId,$groupOwnerPin){
     
    $this->uid=$uid;
    $this->balance=$balance;
    $this->fullPhone=$fullPhone;
    $this->shortPhone=$shortPhone;
    $this->countryId=$countryId;
    $this->secretKey=$secretKey;
    $this->groupId=$groupId;
    $this->lastConnection=$lastConnection;
    $this->connectionTypeId=$connectionTypeId;
    $this->name=$name;
    //$this->password=$password;
    $this->mail=$mail;
    $this->pin=$pin;
    $this->imSecretKey=$imSecretKey;
    $this->isGroup=$isGroup;
    $this->groupOwnerId=$groupOwnerId;
    $this->groupOwnerPin=$groupOwnerPin;

    }
    
    public function setId($uid)
    {
        $this->uid=$uid;
    }
     public function setName($name)
    {
        $this->name=$name;
    }
    public function setAlias($alias)
    {
        $this->alias=$alias;
    }
     public function setDescription($description)
    {
        $this->description=$description;
    }

    public function setMail($mail)
    {
        $this->mail=$mail;
    }

    public function setTempPhone($tempPhone)
    {
        $this->tempPhone=$tempPhone;
    }
    
    
    public function setFullPhone($fullPhone)
    {
        $this->fullPhone=$fullPhone;
    }
    
    public function setShortPhone($shortPhone)
    {
        $this->shortPhone=$shortPhone;
    }
    
     public function setBalance($balance)
    {
         $this->balance=$balance;
    }
     public function setCountryId($countryId)
    {
        $this->countryId=$countryId;
    }
    
    public function setSecretKey($secretKey)
    {
        $this->secretKey=$secretKey;
    }

    public function setImSecretKey($imSecretKey)
    {
        $this->imSecretKey=$imSecretKey;
    }

    public function setPin($pin)
    {
        $this->pin=$pin;
    }

    public function setGroupId($groupId)
    {
        $this->groupId=$groupId;
    }
    
    public function setLastConnection($lastConnection)
    {
        $this->lastConnection=$lastConnection;
    }
    
    public function setConnectionTypeId($connectionTypeId)
    {
        $this->connectionTypeId=$connectionTypeId;
    }

    public function setisGroup($isGroup)
    {
        $this->isGroup=$isGroup;
    }
    public function setgroupOwnerId($groupOwnerId)
    {
        $this->groupOwnerId=$groupOwnerId;
    }

    public function setgroupOwnerPin($groupOwnerPin)
    {
        $this->groupOwnerPin=$groupOwnerPin;
    }

    public function setTokens($tokens)
    {
        $this->tokens=$tokens;
    }

    public function setTokensProgram($tokensProgram)
    {
        $this->tokensProgram=$tokensProgram;
    }
    public function setTokensDay($tokensDay)
    {
        $this->tokensDay=$tokensDay;
    }

    public function setBannerImpr($bannerImpr)
    {
        $this->bannerImpr=$bannerImpr;
    }


    
    public function getId()
    {
        return $this->uid;
    }
    
    public function getName()
    {
        return $this->name;
    }
    public function getAlias()
    {
        return $this->alias;
    }
    public function getDescription()
    {
        return $this->description;
    }
    
    /*public function getPassword()
    {
        return $this->password;
    }*/
    
    public function getMail()
    {
        return $this->mail;
    }

    public function getFullPhone()
    {
        return $this->fullPhone;
    }
    
    public function getShortPhone()
    {
        return $this->shortPhone;
    }

    public function getTempPhone()
    {
        return $this->tempPhone;
    }
    
    public function getBalance()
    {
        return $this->balance;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }
    
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function getImSecretKey()
    {
        return $this->imSecretKey;
    }

    public function getPin()
    {
        return $this->pin;
    }
    
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    public function getLastConnetion()
    {
        return $this->lastConnection;
    }
    
    public function getConnetionTypeId()
    {
        return $this->connectionTypeId;
    }
     public function getIsGroup()
    {
        return $this->isGroup;
    }
    public function getgroupOwnerId()
    {
        return $this->groupOwnerId;
    }
    public function getgroupOwnerPin()
    {
        return $this->groupOwnerPin;
    }
    public function getTokens()
    {
        return $this->tokens;
    }
    public function getTokensProgram()
    {
        return $this->tokensProgram;
    }
    public function getTokensDay()
    {
        return $this->tokensDay;
    }

    public function getBannerImpr()
    {
        return $this->bannerImpr;
    }
    
}
?>