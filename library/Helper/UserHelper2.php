<?php
require_once 'Objects/User.php';
require_once 'Objects/UserDevice.php';


class UserHelper2 {

    public function __construct($dbConn){
        $this->dbConn=$dbConn;

    }

    public function getUser($fullPhone)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE full_phone = ? ', $fullPhone);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
	
            else{
               $user=null;
            }
            
            
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public function getUserById($Id)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE id = ? ', $Id);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
	
            else{
               $user=null;
            }
            
            
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public function getUserByPin($pin)
    {
     
        try {
           
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE pin = ?', $pin);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
    
            else{
               $user=null;
            }
            
            
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public function getUserByFullPhoneOrShortPhone($fullPhone)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE full_phone = ? or short_phone = ?', array($fullPhone,$fullPhone));
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
	    elseif(count($result)>= 1){
		 $user=null;
			   throw new Exception("Error duplicate userID (phone) ",Constants::ERROR_RESOURCE_NOT_FOUND);
	    }
            else{
               $user=null;
			   //throw new Exception("Error not valid or not existing userID",Constants::ERROR_RESOURCE_NOT_FOUND);
            }
            
            
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
	public function getUserByLastDigits($fullPhone)
	{
	    try {
		
		$this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
		$result = $this->dbConn->fetchAll('SELECT * FROM user WHERE full_phone like ?', "%".substr($fullPhone,-8));
		
		if (count($result)== 1)
		{
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
		}
		elseif(count($result)>= 1){
		     $user=null;
			       throw new Exception("Error duplicate userID (phone) ",Constants::ERROR_RESOURCE_NOT_FOUND);
		}
		else{
		   $user=null;
			       throw new Exception("Error not valid or not existing userID",Constants::ERROR_RESOURCE_NOT_FOUND);
		}
		
		
		
	       return $user;
	       
	       
	    } catch (Zend_Exception $e) {
	      throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
	    }
	  
	}

        public function getUserByMail($mail)
    {
        try {
    
        
        $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE mail = ?', $mail);
        
        if (count($result)== 1)
        {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
        }
        elseif(count($result)>= 1){
             $user=null;
                   throw new Exception("Error duplicate userID (phone) ",Constants::ERROR_RESOURCE_NOT_FOUND);
        }
        else{
           $user=null;
                   throw new Exception("Error not valid or not existing userID",Constants::ERROR_RESOURCE_NOT_FOUND);
        }
        
        
        
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
        public function createUser($fullPhone,$shortPhone,$balance,$country_id,$groupId,$isGroup,$groupOwnerId,$groupOwnerPin,$name)
        {
         try {
                
                $this->dbConn->beginTransaction();             

                    $pin=getNewRandomPin(8);
                    
                    //add <im_secret_key>:        
                    $salt = uniqid(mt_rand(), true);
                    $messageKey=$salt.time().$pin;
                    $im_secret_key=md5($messageKey);

                     $data = array(
                    'balance'        => $balance,
                    'country_id'     => $country_id,
                    'group_id'       => $groupId,
                    'pin'            => $pin,
                    'im_secret_key'  => $im_secret_key,
                    'is_group'       => $isGroup,
                    'group_owner_id' => $groupOwnerId,
                    'group_owner_pin' => $groupOwnerPin
                    );
                    if($fullPhone!=null)
                          $data['full_phone']=$fullPhone;
                    if($name!=null)
                          $data['name']=utf8_decode($name);
                

                
                $this->dbConn->insert('user', $data);
                $uid = $this->dbConn->lastInsertId();
                
                $this->dbConn->commit();
                
               return $uid;
           
           
            } catch (Exception $e) {
			   $this->dbConn->rollBack();
                throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
        }
        
        public function updateUser($user){
            
                try {
                   

                    if($user->getName()!=NULL)
                        $data["name"] = utf8_decode($user->getName());
                    if($user->getMail()!=NULL)
                        $data["mail"] = $user->getMail();
                    if($user->getFullPhone()!=NULL)
                        $data["full_phone"] = $user->getFullPhone();
                    if($user->getShortPhone()!=NULL)
                        $data["short_phone"] = $user->getShortPhone();
					
					$balance = $user->getBalance();
                    if(isset($balance))
                        $data["balance"] = $balance;
					
                    if($user->getCountryId()!=NULL)
                        $data["country_id"] = $user->getCountryId();	
                    if($user->getSecretKey()!=NULL)
                        $data["secret_key"] = $user->getSecretKey();
                    if($user->getImSecretKey()!=NULL)
                            $data["im_secret_key"] = $user->getImSecretKey();
            		if($user->getGroupId()!=NULL)
                            $data["group_id"] = $user->getGroupId();
            		if($user->getLastConnetion()!=0 && $user->getLastConnetion()!=NULL)
                            $data["last_connection"] = $user->getLastConnetion();
            		if($user->getConnetionTypeId()!=0 && $user->getConnetionTypeId()!=NULL)
                            $data["connection_type_id"] = $user->getConnetionTypeId();
		    
				
                    $where[] = "id = ".$user->getId();
       
                    $this->dbConn->update('user', $data, $where);
            
                   
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }
         public function setUserPassword($user,$password){
              try {
                    
                  //update new user balance

                    if($password!=NULL){

                        $data["password"] = md5($password);
                        $where[] = "id = ".$user->getId();
                        $this->dbConn->update('user', $data, $where);
                    }
                    
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

         }
	
    public function getNonePromoUsers()
    {
     
        try {
          
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user where user.id not in (SELECT uid from user_promo)order by id asc');
            
            if (count($result) > 0)
            {   $i=0;
		foreach($result as $user){
            $nextUser= new User($user->id,utf8_encode(str_replace(",",";",$user->name)),$user->mail,$user->password,$user->full_phone, $user->short_phone, $user->balance,$user->country_id,$user->im_secret_key,$user->secret_key,$user->pin,$user->group_id,$user->last_connection,$user->connection_type_id,$user->is_group,$user->group_owner_id,$user->group_owner_pin);
               
            $usersArray[$i]=$nextUser;
		    $i++;
		}
	    }
            else{
               $usersArray=null;
            }
            
            
            
           return $usersArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public function addNewRandomUserKey($user){
	
	//and <secret_key>: only if transaction was succeeded	
	$salt = uniqid(mt_rand(), true);
			  
	$messageKey=$salt.time().$user->getId().$user->getFullPhone();
	$user->setSecretKey(md5($messageKey),false);
	
	self::updateUser($user);
	
	return $user->getSecretKey();
    }


public function getNewRandomPin($length) {
    
    $numbers = "0123456789";
    $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
    $stringPrefix="";
    $stringSuffix="";

    for ($p = 0; $p < $length/2; $p++) {
        $stringPrefix .= $characters[mt_rand(0, (strlen($characters))-1)];
    }

    for ($p = 0; $p < $length/2; $p++) {
        $stringSuffix .= $numbers[mt_rand(0, (strlen($numbers))-1)];
    }
    
    
    return $stringPrefix.$stringSuffix;
}
    public function updateUserTimeStamp($user,$timeStampPost,$connectionTypePost){
        $updateUser=false; 
            if($timeStampPost!=null){
                $user->setLastConnection($timeStampPost);$updateUser=true;}
            if($connectionTypePost!=null){
                $user->setConnectionTypeId($connectionTypePost);$updateUser=true;}

        
            if($updateUser)
                $this->updateUser($user);
    }

     /*Help methods*/
     public function getUserAndcheckErrors($userIdPost,$userPhonePost){
      
      $responseArray=array();
        
         if(($userIdPost==NULL || $userIdPost=="-1") && ($userPhonePost==NULL || $userPhonePost=="-1"))
            throw new Exception("Error you need to pass id or phone params",Constants::ERROR_BAD_REQUEST);
        
     //replace - slashes and spaces
     $notAllowedChars = array("-");
     $userPhonePost = str_replace($notAllowedChars, "", $userPhonePost);
     //Verify user
        if($userIdPost!=null && $userIdPost!=-1)
           $user1 = self::getUserByPin($userIdPost);
         //remove + symbol if exist  
        if($userPhonePost!=null && $userPhonePost!=-1)
           $userPhonePost = PhoneNumberHelper::replacePlusSymbolByZeros($userPhonePost);
        

        if($user1!=null){
           
           //If userId and Phone
           if($userPhonePost!=null && $userPhonePost!=-1){
             
             //validate phone with user phone stored in DB (last 8 digits) 
             if(substr($user1->getFullPhone(),-8)==substr($userPhonePost,-8)){
              //UserId and Phone params and same phone
              $responseArray[0]=Constants::USER_AND_PHONE_PARAMS;
              $responseArray[1]=$user1;
             }
             else{
                 //if userId and Phone not the same then search user by Phone
             $user2 = self::getUserByFullPhoneOrShortPhone($userPhonePost);
               if($user2!=null){

                  //UserId and Phone params but different phone
                   $responseArray[0]=Constants::USER_AND_PHONE_PARAMS;
                   $responseArray[1]=$user2;
                   }
               else{
                 throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
               }
             }
           }
           //Only userId param
            $responseArray[0]=Constants::ONLY_USER_PARAMS;
            $responseArray[1]=$user1;
         }
         else
          {//Only phone param
           $user2 = self::getUserByFullPhoneOrShortPhone($userPhonePost);
           
            if($user2!=null){
          //Only phone param
          $responseArray[0]=Constants::ONLY_PHONE_PARAMS;
          $responseArray[1]=$user2;
        }
        else
          throw new Exception("Error user does not exist",Constants::ERROR_RESOURCE_NOT_FOUND);
        
          }
          return $responseArray;
    
    }

        /******Mac Address functions ********/
    public function getUserIdFromMacAddresses($mac_address)
    {
     
        try {
            $uid=null;
            if($mac_address!=null){
              
                $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $this->dbConn->fetchAll('SELECT * FROM user_device WHERE mac_address = ?', $mac_address);
                
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $uid=$result[0]->uid;
                   
                }
                
                
            }
           return $uid;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public function isSessionActive($uid,$mac_address)
    {
        $isActive=0;
        try {
            if($mac_address!=null){

                
                $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result =  $this->dbConn->fetchAll("SELECT active FROM user_device WHERE uid=? and mac_address = ?", array($uid,$mac_address));
  
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $isActive=$result[0]->active;
                   
                }
                
                
            }
           return $isActive;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public function registerMacToUser($userId,$pin,$type)
    {
         try {
     

                
                $this->dbConn->beginTransaction();
                //create message
                $data = array(
                'uid'          =>  $userId,
                'pin'          =>  $pin,
                'mac_address'   => $mac_address,
                'type'              => $type,
                'last_connection'   => time()
                );
                
                $this->dbConn->insert('user_device', $data);
                
                $this->dbConn->commit();
                
               return true;
           
           
            } catch(Zend_Db_Statement_Mysqli_Exception $e){
          
                // code 1062: Mysqli statement execute error : Duplicate entry
                if($e->getCode() == 1062) 
                {  
                     return true;
                  
                } 
                else 
                { 
                    throw $e;
                }
            } catch (Exception $e) {

                    $this->dbConn->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

    public function getMacIdFromMacAddresses($uid,$mac_address)
    {
        $mid=null;
        try {
            if($mac_address!=null){
                
                $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $this->dbConn->fetchAll("SELECT * FROM user_device WHERE uid=? and mac_address = ?", array($uid,$mac_address));
  
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $mid=$result[0]->id;
                   
                }
            }
           return $mid;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

     
   public function getDevicesFromUser($uid)
    {
     
        try {
            $devicesArray=null;

                $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $this->dbConn->fetchAll('SELECT * FROM user_device WHERE uid = ?', $uid);
                
                $total_items=count($result);
                
                if ($total_items > 0)
                { 
                    
                   $devicesArray=$result;
                }
            

           return $devicesArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public function recoverUser($pin,$mail,$fullPhone,$password)
    {
     
        try {
            $user=null;

           
                //TODO Check the full_phone way of recover
               $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                if(!empty($pin))
                     $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE pin = ? and password=?', array($pin,md5($password)));
                elseif(!empty($mail))
                     $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE mail = ? and password=?', array($mail,md5($password)));
                elseif(!empty($fullPhone))
                     $result = $this->dbConn->fetchAll('SELECT * FROM user WHERE full_phone like ? and password=?', array("%".substr($fullPhone, -9),md5($password)));

                $total_items=count($result);
          
                if ($total_items == 1)
                { 
                   $user = new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);

                }
                if ($total_items > 1){
                    //TODO
                    //send mail error no pueden haber 2 users con mismo numero y password
                }
                
                
                
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public function updateLastConnection($userPin,$lastConnectionTimeStamp){
            
                try {

                    
                  //update new user balance

                    if($lastConnectionTimeStamp!=0 && $lastConnectionTimeStamp!=NULL){

                        $data["last_connection"] = $lastConnectionTimeStamp;
                  
            
                        $where[] = "pin = '".$userPin."'";
           
                        $this->dbConn->update('user', $data, $where);

                        
                   }

                   
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

        public function addPinToHistory($userId,$pin)
        {
         try {
     
                
                $this->dbConn->beginTransaction();
                //create message
                $data = array(
                'uid'          =>  $userId,
                'pin'          => $pin,
                );
            
                
                $this->dbConn->insert('user_pin_history', $data);
                
                $this->dbConn->commit();
                
               return true;
           
           
            } catch(Zend_Db_Statement_Mysqli_Exception $e){
          
                // code 1062: Mysqli statement execute error : Duplicate entry
                if($e->getCode() == 1062) 
                {  
                     return true;
                  
                } 
                else 
                { 
                    throw $e;
                }
            } catch (Exception $e) {

                    $this->dbConn->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

     public function deleteUser($userId)
    {
     
        try {
        

               //Deletes event
                $result=$this->dbConn->delete('user', 'id='.$userId);

            
            
           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

     public function updateUserAndUserId($currentUserId,$user){
            
                try {
   

                    if($user->getId()!=NULL)
                        $data["id"] = $user->getId();
                    if($user->getName()!=NULL)
                        $data["name"] = utf8_decode($user->getName());
                    if($user->getMail()!=NULL)
                        $data["mail"] = $user->getMail();
                    if($user->getFullPhone()!=NULL)
                        $data["full_phone"] = $user->getFullPhone();
                    if($user->getShortPhone()!=NULL)
                        $data["short_phone"] = $user->getShortPhone();
                    
                    $balance = $user->getBalance();
                    if(isset($balance))
                        $data["balance"] = $balance;
                    
                    if($user->getCountryId()!=NULL)
                        $data["country_id"] = $user->getCountryId();    
                    if($user->getSecretKey()!=NULL)
                        $data["secret_key"] = $user->getSecretKey();
                    if($user->getImSecretKey()!=NULL)
                            $data["im_secret_key"] = $user->getImSecretKey();
                    if($user->getGroupId()!=NULL)
                            $data["group_id"] = $user->getGroupId();
                    if($user->getLastConnetion()!=0 && $user->getLastConnetion()!=NULL)
                            $data["last_connection"] = $user->getLastConnetion();
                    if($user->getConnetionTypeId()!=0 && $user->getConnetionTypeId()!=NULL)
                            $data["connection_type_id"] = $user->getConnetionTypeId();
            
                
                    $where[] = "id = ".$currentUserId;
       
                    $this->dbConn->update('user', $data, $where);
            
                    
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

         public function updateAndCleanUserDevices($currentUserId,$user){
                    try {
  

                    //Deletes old user_devices
                    $result=$this->dbConn->delete('user_device', 'uid='.$user->getId());
                    //Update new one
                    if($user->getId()!=NULL)
                        $data["uid"] = $user->getId();

                    $where[] = "uid = ".$currentUserId;
       
                    $this->dbConn->update('user_device', $data, $where);

                    
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

    public function getValidUsersByPhone($phoneListArray)
    {

       try {
        
      
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

             $query="select pin,full_phone,last_connection from user where full_phone like ";

              for ($i = 0; $i < count($phoneListArray); $i++) {
                   if($i==0)
                      $query.="'%".$phoneListArray[$i]."%'";
                   else
                      $query.="or  full_phone like '%".$phoneListArray[$i]."%'";

              }
           
             $query.=" LIMIT ".count($phoneListArray);

            $result = $this->dbConn->fetchAll($query);

            $total_items=count($result);
            if ($total_items > 0)
            { 
               $usersArray="OK,".$total_items.",";
              foreach($result as $user){
                          $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                          (empty($user->full_phone)?"null":$user->full_phone).",".
                          (empty($user->last_connection)?"0":$user->last_connection).",9&c3";

              }
            }
            else{
              $usersArray="OK,".$total_items;
            }
            
            
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

        public function updateSuper($user,$timeStampPost,$connectionTypePost,$mac_address){
            try {

                $uid=null;
                $data=null;
                if($mac_address!=null){
          
                    $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                    $result = $this->dbConn->fetchAll('SELECT * FROM user_device WHERE mac_address = ?', $mac_address);
                    
                    $total_items=count($result);
                    if ($total_items == 1)
                    { 
                       $uid=$result[0]->uid;
                       
                    }
                    

                   
                }

                if($timeStampPost!=0 && $timeStampPost!=NULL)
                            $data["last_connection"] = $timeStampPost;
                    if($connectionTypePost!=0 && $connectionTypePost!=NULL)
                            $data["connection_type_id"] = $connectionTypePost;
            
                if($data!=null){
                    $where[] = "id = ".$user->getId();
                    $this->dbConn->update('user', $data, $where);
                    }

                     
               return $uid;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }

        }

    
}