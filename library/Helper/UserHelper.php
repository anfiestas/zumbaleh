<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/UserDevice.php';


class UserHelper {
    
    
    public static function getUser($fullPhone)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user WHERE full_phone = ? ', $fullPhone);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
    
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function getUserById($Id)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user WHERE id = ? ', $Id);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
            }
    
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getUserByPin($pin)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user WHERE pin = ?', $pin);
            
            if (count($result)== 1)
            {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
                $user->setTokens($result[0]->tokens);
                $user->setTokensProgram($result[0]->tokens_program);
                $user->setTokensDay($result[0]->tokens_day);
                $user->setBannerImpr($result[0]->banner_impr);
                $user->setDescription($result[0]->description);
            }
    
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getUserTokensByPin($pin)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT id,pin,tokens,tokens_program FROM user WHERE pin = ?', $pin);
            

            if (count($result)== 1)
            {
               
                $user=$result[0];

            }
    
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function getUserByFullPhoneOrShortPhone($fullPhone)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user WHERE full_phone = ? or short_phone = ?', array($fullPhone,$fullPhone));
            
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
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getUserByFullPhoneOrShortPhone2($fullPhone)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user WHERE full_phone = ? or short_phone = ?', array($fullPhone,$fullPhone));
            
            if (count($result)== 1)
            {
                $user = $result[0];
               
            }
        elseif(count($result)>= 1){
         $user=null;
               throw new Exception("Error duplicate userID (phone) ",Constants::ERROR_RESOURCE_NOT_FOUND);
        }
            else{
               $user=null;
               //throw new Exception("Error not valid or not existing userID",Constants::ERROR_RESOURCE_NOT_FOUND);
            }
            
            $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function getUserByLastDigits($fullPhone)
    {
        try {
        $dbHelper= new DbHelper();
        $dbConn=$dbHelper->getConnectionDb();
        
        $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $dbConn->fetchAll('SELECT * FROM user WHERE full_phone like ?', "%".substr($fullPhone,-8));
        
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
        
        $dbConn->closeConnection();
        
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

        public static function getUserByMail($mail)
    {
        try {
        $dbHelper= new DbHelper();
        $dbConn=$dbHelper->getConnectionDb();
        
        $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $dbConn->fetchAll('SELECT * FROM user WHERE mail = ?', $mail);
        
        if (count($result)== 1)
        {
                $user= new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
               
        }
        elseif(count($result)>= 1){
             $user=null;

                   throw new Exception("Error duplicate mail",Constants::ERROR_RESOURCE_NOT_FOUND);
        }
        else{
           $user=null;
                   throw new Exception("Not existing mail",Constants::ERROR_RESOURCE_NOT_FOUND);
        }
        
        $dbConn->closeConnection();
        
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
        public static function createUser($fullPhone,$shortPhone,$balance,$country_id,$groupId,$isGroup,$groupOwnerId,$groupOwnerPin,$name,
            $description,$groupCategory,$groupPassword,$groupMode)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();             

                    $pin=UserHelper::getNewRandomPin(8);
                    
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
                    'group_owner_pin' => $groupOwnerPin,
                    'tokens_program' => 2,
                    'description' => $description,
                    'group_mode' => $groupMode,
                    'group_category_id' => $groupCategory
                    );
                    if($fullPhone!=null)
                          $data['full_phone']=$fullPhone;
                    if($name!=null)
                          $data['name']=utf8_decode($name);

                    if ($groupPassword!=null)
                         $data['password'] = md5($groupPassword);

                
                $db->insert('user', $data);
                $uid = $db->lastInsertId();
                
                $db->commit();
                
               return $uid;
           
           
            } catch (Exception $e) {
               $db->rollBack();
                throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
        }

    
        public static function updateUser($user){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    if($user->getName()!=NULL)
                        $data["name"] = utf8_decode($user->getName());
                    if($user->getMail()!=NULL)
                        $data["mail"] = $user->getMail();
                    if($user->getFullPhone()!=NULL)
                        $data["full_phone"] = $user->getFullPhone();
                    if($user->getShortPhone()!=NULL)
                        $data["short_phone"] = $user->getShortPhone();
                    if($user->getTempPhone()!=NULL)
                        $data["temp_phone"] = $user->getTempPhone();
                    
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
                    if($user->getTokens()!=0 && $user->getTokens()!=NULL)
                            $data["tokens"] = $user->getTokens();
                    if($user->getTokensProgram()!=0 && $user->getTokensProgram()!=NULL)
                            $data["tokens_program"] = $user->getTokensProgram();
                    if($user->getBannerImpr()!=0 && $user->getBannerImpr()!=NULL)
                            $data["banner_impr"] = $user->getBannerImpr();
                            
                
                    $where[] = "id = ".$user->getId();
       
                    $db->update('user', $data, $where);
            
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }
         public static function setUserPassword($user,$password){
              try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();
                    
                  //update new user balance

                    if($password!=NULL){

                        $data["password"] = md5($password);
                        $where[] = "id = ".$user->getId();
                        $db->update('user', $data, $where);
                    }
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

         }
    
     public static function updateUserTransDb($db,$user){
            
                try {
                
                    
                  //update new user balance
                    if($user->getName()!=NULL)
                        $data["name"] = utf8_decode($user->getName());
                    if($user->getMail()!=NULL)
                        $data["mail"] = $user->getMail();
                    if($user->getPassword()!=NULL)
                        $data["password"] = md5($user->getPassword());
                    if($user->getFullPhone()!=NULL)
                        $data["full_phone"] = $user->getFullPhone();
                    if($user->getShortPhone()!=NULL)
                        $data["short_phone"] = $user->getShortPhone();
                        
                    if($user->getCountryId()!=NULL)
                        $data["country_id"] = $user->getCountryId();
                        if($user->getSecretKey()!=NULL)
                        $data["secret_key"] = $user->getSecretKey();
                    if($user->getImSecretKey()!=NULL)
                        $data["im_secret_key"] = $user->getImSecretKey();
                        if($user->getGroupId()!=NULL)
                        $data["group_id"] = $user->getGroupId();
                        if($user->getLastConnection()!=0 && $user->getLastConnection()!=NULL)
                        $data["last_connection"] = $user->getLastConnection();
                        if($user->getConnetionTypeId()!=0 && $user->getConnetionTypeId()!=NULL)
                        $data["connection_type_id"] = $user->getConnetionTypeId();
                    
                       $balance = $user->getBalance();

                    if(isset($balance))
                        $data["balance"] = $balance;
            
                    $where[] = "id = ".$user->getId();
                    $db->update('user', $data, $where);
                    
                   return true;
                   
                   
                }catch (Exception $e) {
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                  
                }
        }
    
    public static function getNonePromoUsers()
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user where user.id not in (SELECT uid from user_promo)order by id asc');
            
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
            
            $dbConn->closeConnection();
            
           return $usersArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function addNewRandomUserKey($user){
    
    //and <secret_key>: only if transaction was succeeded   
    $salt = uniqid(mt_rand(), true);
              
    $messageKey=$salt.time().$user->getId().$user->getFullPhone();
    $user->setSecretKey(md5($messageKey),false);
    
    self::updateUser($user);
    
    return $user->getSecretKey();
    }


public static function getNewRandomPin($length) {
    
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

    public static function updateUserTimeStamp($user,$timeStampPost,$connectionTypePost){
        $updateUser=false; 
            if($timeStampPost!=null)
                $user->setLastConnection($timeStampPost);$updateUser=true; 
            if($connectionTypePost!=null)
                $user->setConnectionTypeId($connectionTypePost);$updateUser=true;
            if($updateUser)
                UserHelper::updateUser($user);
    }

    public static function calculateTokens($user){
     require_once 'Helper/ToolsHelper.php';

     $newTokensValue=$user->getTokens();
     $count_exceeded=false;

 //  ******* Memcache tokens/minute limit  ******  /
     if(Constants::MEMCACHE==TRUE){
             $memcache = new Memcache();
             $memcache->addServer('localhost', 11211);
             $memcache->connect('localhost', 11211);
                
            
             $count = $memcache->get("IM_counter".$user->getId());
             $sessionStart = $memcache->get("IM_session_start".$user->getId());
             $is_max_tokens_day = $memcache->get("IM_max_tokens_day".$user->getId());
             $count_exceeded=false;
               
            if($count==null){$count=0;}
            if($sessionStart==null){$sessionStart=time();}
            if($is_max_tokens_day==null){$is_max_tokens_day=0;}
            
             #echo ">>".(time()-$sessionStart);
            if(time()-$sessionStart < 60){
              //suma spooris
              #echo "in:".(time()-$sessionStart);
               if($count < Constants::MAX_IM_MINUTE){
                      $count_exceeded=false;

                }
              else{
                     $count_exceeded=true;
                     if($count==Constants::MAX_IM_MINUTE){
                        //Send IM
                      require_once 'Helper/UserEventHelper.php';
                         require_once 'Helper/IMessageHelper.php';
                    //Max tokens reached
                        $spooraUser = UserHelper::getUserById(-1);   

                            $textPost="Has superado la frecuencia de envío de mensajes máxima establecida en ".Constants::MAX_IM_MINUTE." mensajes / minuto. Durante los próximos 5 minutos los mensajes seguirán enviándose correctamente pero no sumarán spooris. Más info en https://www.myspoora.com/#faq";
                            $textAlertaSpam="ALERTA SPAMER: id:".$user->getId().", ".$user->getPin().", ".$user->getName().", spooris/dia:".$user->getTokensDay();
         
                            $mid = IMessageHelper::sendIMessage($spooraUser,$textPost,$user->getId(),null,$user->getPin(),2,null,null,Constants::SMS_SUBMITED);
                            //add Event to destinationUser
                            UserEventHelper::addEventToUser($user->getId(),$user->getPin(),Constants::SMS_SUBMITED);

                            ///ALERTA SPAMER
                            $writer = new Zend_Log_Writer_Stream('../private/logs/spamers.log');
                            $logger = new Zend_Log($writer);
                            $logger->info("id:".$user->getId().", ".$user->getPin().", ".$user->getName().", spooris/dia:".$user->getTokensDay());
                     }
                 }
            }else{// END OF MINUTE SESSION
              //If less than MAX_IN_MINUTE tokens OR after 5 minutes, reset sesion
                 if( ($count <= Constants::MAX_IM_MINUTE) || (time()-$sessionStart >= 360) ){
                      $sessionStart=time();
                      $count=0;
                  }
                  else
                    $count_exceeded=true;
            }
            
              $memcache->set("IM_session_start".$user->getId(), $sessionStart, false, 360);
              $memcache->set("IM_counter".$user->getId(), $count+1, false, 360);
    
    }   
          
    if($user->getTokensProgram() < Constants::TOKENS_USER_BANNED_ADBLOCK_PLUS && $count_exceeded==false && $user->getTokensDay() <= Constants::TOKENS_MAX_DAY)
    {     //  ******* Calcul tokens  ******  /
            if($newTokensValue <= 1000)
                    $newTokensValue=$newTokensValue+3;
                elseif($newTokensValue > 1000 && $newTokensValue <=2000)
                    $newTokensValue=$newTokensValue+2;
                elseif($newTokensValue > 2000)
                    $newTokensValue=$newTokensValue+1;
        }

        if($user->getTokensDay()>=Constants::TOKENS_MAX_DAY && $is_max_tokens_day==0){
                 require_once 'Helper/UserEventHelper.php';
                 require_once 'Helper/IMessageHelper.php';
            //Max tokens reached
                  if(Constants::MEMCACHE==TRUE){
                        $memcache->set("IM_max_tokens_day".$user->getId(), 1, false, 86400);
                    }
                $spooraUser = UserHelper::getUserById(-1);   

                  //$textPost="Congratulations! You've got your first 100 spooris. Now press your spooris counter on top of the screen to give back them to the entity of your choice";
                  
                  //if($country->getId()==196)
                    $textPost="Has alcanzado el límite de ".Constants::TOKENS_MAX_DAY." spooris diarios. El envío y recepción de mensajes seguirá funcionando correctamente pero no podrás acumular más spooris
durante la jornada de hoy.";
                  
                  /*else if($country->getId()==73)
                    $textPost="Bravo! Utilise bien tes premiers 100 spooris. Maintenant presse sur le compteur dans la partie supérieure de l'écran pour faire ton premier don :)";
                  */
                //Send message promo
                $mid = IMessageHelper::sendIMessage($spooraUser,$textPost,$user->getId(),null,$user->getPin(),2,null,null,Constants::SMS_SUBMITED);
             
                //add Event to destinationUser
                UserEventHelper::addEventToUser($user->getId(),$user->getPin(),Constants::SMS_SUBMITED);
        }
        return $newTokensValue;

    }

    
    public static function updateUserTokensTimeStamp($user,$timeStampPost,$connectionTypePost,$tokensPost,$tokensInc,$bannerImpr){
        $updateUser=false; 
        try {

            if($timeStampPost!=null)
                $data["last_connection"] = $timeStampPost;$updateUser=true; 
            if($connectionTypePost!=null)
                $data["connection_type_id"] = $connectionTypePost;$updateUser=true;
             if($tokensPost>=0){
                $data["tokens"] = $tokensPost;
                $data["tokens_day"] = $user->getTokensDay()+$tokensInc;$updateUser=true;
                //TODO - Ver si se puede actualizar solo de vez en cuando
                require_once 'Helper/UserStatsHelper.php';
                UserStatsHelper::updateFieldValue($user->getId(),"tokens_historic_count",UserStatsHelper::getFieldValue($user->getId(),"tokens_historic_count")+$tokensInc);
                 if($bannerImpr!=null && $bannerImpr>=0){
                     //no poner a cero, es la appli quien lo hace
                     //$data["banner_impr"] = $bannerImpr-$bannerImpr;
                     UserStatsHelper::updateFieldValue($user->getId(),"banner_impr_historic_count",UserStatsHelper::getFieldValue($user->getId(),"banner_impr_historic_count")+$bannerImpr);
                 }

             }

                
            
            if($updateUser && $user->getId()){
                 $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                      $where[] = "id = ".$user->getId();
       
                    $db->update('user', $data, $where);
            
                    $db->closeConnection();
            }

         } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
                
    }

     /*Help methods*/
     public static function getUserAndcheckErrors($userIdPost,$userPhonePost){
      require_once 'Helper/PhoneNumberHelper.php';
      $responseArray=array();
        
         if(($userIdPost==NULL || $userIdPost=="-1") && ($userPhonePost==NULL || $userPhonePost=="-1"))
            throw new Exception("Error you need to pass id or phone params",Constants::ERROR_BAD_REQUEST);
        
     //replace - slashes and spaces
     $notAllowedChars = array("-");
     $userPhonePost = str_replace($notAllowedChars, "", $userPhonePost);
     //Verify user
        if($userIdPost!=null && $userIdPost!=-1)
           $user1 = UserHelper::getUserByPin($userIdPost);
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
             $user2 = UserHelper::getUserByFullPhoneOrShortPhone($userPhonePost);
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
           $user2 = UserHelper::getUserByFullPhoneOrShortPhone($userPhonePost);
           
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
    public static function getMacIdFromMacAddresses($uid,$mac_address)
    {
        $mid=null;
        try {
            if($mac_address!=null){
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $dbConn->fetchAll("SELECT * FROM user_device WHERE uid=? and mac_address = ?", array($uid,$mac_address));
  
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $mid=$result[0]->id;
                   
                }
                
                
                $dbConn->closeConnection();
            }
           return $mid;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getUserDevice($uid,$deviceId)
    {
        $device=null;
        try {
            if($deviceId!=null){
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $dbConn->fetchAll("SELECT * FROM user_device WHERE uid=? and mac_address = ?", array($uid,$deviceId));
  
                $total_items=count($result);

                if ($total_items == 1)
                { 
                   $device=$result[0];
                  
                }
                
                
                $dbConn->closeConnection();
            }
           return $device;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getUserDeviceFromMacAddresses($uid,$mac_address)
    {
        $userDeviceObject=null;
        try {
            if($mac_address!=null){
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $dbConn->fetchAll("SELECT * FROM user_device WHERE uid=? and mac_address = ?", array($uid,$mac_address));
  
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $userDeviceObject=$result[0];
                   
                }
                
                
                $dbConn->closeConnection();
            }
           return $userDeviceObject;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }


    public static function isSessionActive($uid,$mac_address)
    {
        $isActive=0;
        try {
            if($mac_address!=null){
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $dbConn->fetchAll("SELECT active FROM user_device WHERE uid=? and mac_address = ?", array($uid,$mac_address));
  
                $total_items=count($result);
                if ($total_items == 1)
                { 
                   $isActive=$result[0]->active;
                   
                }
                
                
                $dbConn->closeConnection();
            }
           return $isActive;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function registerMacToUser($userId,$pin,$mac_address,$type)
    {
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'               =>  $userId,
                'pin'               =>  $pin,
                'mac_address'       => $mac_address,
                'type'              => $type,
                'last_connection'   => time()
                );
                
                $db->insert('user_device', $data);
                
                $db->commit();
                
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

                    $db->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

        public static function registerDeviceToUser($userId,$pin,$mac_address,$serial_id,$device_id,$type)
    {
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'               =>  $userId,
                'pin'               =>  $pin,
                'mac_address'       => $mac_address,
                'serial_id'       => $serial_id,
                'device_id'       => $device_id,
                'type'              => $type,
                'last_connection'   => time()
                );
                
                //$db->insert('user_device', $data);
                
                $sql = "INSERT INTO user_device (uid, pin, mac_address,serial_id,device_id,type,last_connection) VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE uid = ?, pin = ?, mac_address = ?, serial_id = ?, device_id = ?, type = ?, last_connection = ?";

                $db->query($sql, array_merge(array_values($data), array_values($data)));   

                $db->commit();
                
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

                    $db->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

    public static function registerMacAndPushIDToUser($userId,$pin,$mac_address,$pushID,$type)
    {
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'               =>  $userId,
                'pin'               =>  $pin,
                'mac_address'       => $mac_address,
                'push_id'           => $pushID,
                'type'              => $type,
                'last_connection'   => time()
                );
                
                $db->insert('user_device', $data);
                
                $db->commit();
                
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

                    $db->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

    public static function addPushIDToDevice($deviceId,$pushID){
        $updateUser=false; 
        try {

            if($pushID!=null)
                $data["push_id"] = $pushID;$updateUser=true;
                
            
            if($updateUser && $deviceId){
                 $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                      $where[] = "id = ".$deviceId;
       
                    $db->update('user_device', $data, $where);
            
                    $db->closeConnection();
            }

         } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
                
    }

    public static function removeMacAddress($mac_addressId)
    {

        try {
            if($mac_addressId!=null){
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                $dbConn->beginTransaction();
                
                 //Deletes session
                $result=$dbConn->delete('user_device', 'id='.$mac_addressId);
                
                $dbConn->commit();
                $dbConn->closeConnection();
            }
           return "OK";
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
     
   public static function getDevicesFromUser($uid)
    {
     
        try {
            $devicesArray=null;
      
                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $dbConn->fetchAll('SELECT * FROM user_device WHERE uid = ?', $uid);
                
                $total_items=count($result);
                
                if ($total_items > 0)
                { 
                    
                   $devicesArray=$result;
                }
                
                
                $dbConn->closeConnection();
            

           return $devicesArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function recoverUser($pin,$mail,$fullPhone,$password)
    {
     
        try {
            $user=null;

                $dbHelper= new DbHelper();
                $dbConn=$dbHelper->getConnectionDb();
                //TODO Check the full_phone way of recover
                $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
                if(!empty($pin))
                     $result = $dbConn->fetchAll('SELECT * FROM user WHERE pin = ? and password=?', array($pin,md5($password)));
                elseif(!empty($mail))
                     $result = $dbConn->fetchAll('SELECT * FROM user WHERE mail = ? and password=?', array($mail,md5($password)));
                elseif(!empty($fullPhone))
                     $result = $dbConn->fetchAll('SELECT * FROM user WHERE full_phone like ? and password=?', array("%".substr($fullPhone, -9),md5($password)));

                $total_items=count($result);
          
                if ($total_items == 1)
                { 
                   $user = new User($result[0]->id,utf8_encode(str_replace(",",";",$result[0]->name)),$result[0]->mail,$result[0]->password,$result[0]->full_phone, $result[0]->short_phone, $result[0]->balance,$result[0]->country_id,$result[0]->im_secret_key,$result[0]->secret_key,$result[0]->pin,$result[0]->group_id,$result[0]->last_connection,$result[0]->connection_type_id,$result[0]->is_group,$result[0]->group_owner_id,$result[0]->group_owner_pin);
                   $user->setTokens($result[0]->tokens);
                   $user->setBannerImpr($result[0]->banner_impr);
                }
                if ($total_items > 1){
                    //TODO
                    //send mail error no pueden haber 2 users con mismo numero y password
                }
                
                
                $dbConn->closeConnection();
            
           return $user;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function updateLastConnection($userPin,$lastConnectionTimeStamp){
            
                try {

                    
                  //update new user balance

                    if($lastConnectionTimeStamp!=0 && $lastConnectionTimeStamp!=NULL){
                        $dbHelper= new DbHelper();
                        $db=$dbHelper->getConnectionDb();
                            
                        $data["last_connection"] = $lastConnectionTimeStamp;
                  
            
                        $where[] = "pin = '".$userPin."'";
           
                        $db->update('user', $data, $where);

                        $db->closeConnection();
                   }

                   
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }


    public function updateSessionLastConnection($uid,$sessionId){
                    try {
                        $dbHelper= new DbHelper();
                        $db=$dbHelper->getConnectionDb();
                        $db->beginTransaction();

                       //Deactivate other sessions
                        if($uid!=NULL){
                            $data["active"] = 0;
                            $where[] = "id != ".$sessionId;
                            $where[] = "uid = ".$uid;
                            $db->update('user_device', $data, $where);

                        }
                        //Activate current Session
                        if($sessionId!=NULL){
                            $data2["last_connection"] = time();
                            $data2["active"] = 1;

                            $where2[] = "id = ".$sessionId;
           
                             $db->update('user_device', $data2, $where2);

                        }

                        $db->commit();
                    
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

         public function updateDeviceAndSerial($deviceId,$deviceIdPost,$serialIDPost){
                    try {

                        $dbHelper= new DbHelper();
                        $db=$dbHelper->getConnectionDb();
                        $db->beginTransaction();

                        //Activate current Session
                        if($deviceId!=NULL){

                            $data2["device_id"] = $deviceIdPost;
                            $data2["serial_id"] = $serialIDPost;

                            $where2[] = "id = ".$deviceId;
           
                             $db->update('user_device', $data2, $where2);

                        }

                        $db->commit();
                    
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

        public static function addPinToHistory($userId,$pin)
        {
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'          =>  $userId,
                'pin'          => $pin,
                );
            
                
                $db->insert('user_pin_history', $data);
                
                $db->commit();
                
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

                    $db->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

                }
      
    }

     public static function deleteUser($userId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            

               //Deletes event
                $result=$dbConn->delete('user', 'id='.$userId);

            $dbConn->closeConnection();
            
           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

     public static function updateUserAndUserId($currentUserId,$user){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

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
       
                    $db->update('user', $data, $where);
            
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

         public static function updateAndCleanUserDevices($currentUserId,$user){
                    try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    //Deletes old user_devices
                    $result=$db->delete('user_device', 'uid='.$user->getId());
                    //Update new one
                    if($user->getId()!=NULL)
                        $data["uid"] = $user->getId();

                    $where[] = "uid = ".$currentUserId;
       
                    $db->update('user_device', $data, $where);

                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

    public static function getValidUsersByPhone($currentUser,$phoneListArray)
    {
    require_once 'Helper/UserLinkHelper.php';
       try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

             $query="select id,pin,full_phone,last_connection from user where full_phone like ";

              for ($i = 0; $i < count($phoneListArray); $i++) {
                //TODO: phone size de momento el minimo lo dejo a 9 (spain,france y la mayoria)
                //TODO o check que en la respuesta esté el número de entrada
              
              //checking phone starting from right side only
              //remove all non numerique values from phone
              $phoneToCheck=preg_replace("/[^0-9,.]/", "",$phoneListArray[$i]);
                if(strlen($phoneToCheck) >= 9){
 
                   if($i==0)
                    $query.="'%". $phoneToCheck."%'";
                      //$query.="'%".substr($phoneListArray[$i],-9)."'";
                   else
                    $query.="or  full_phone like '%".$phoneToCheck."%'";
                      //$query.="or  full_phone like '%".substr($phoneListArray[$i],-9)."'";
                  }

              }
           
             $query.=" LIMIT ".count($phoneListArray);

         
            $result = $dbConn->fetchAll($query);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $usersArray="OK,".$total_items.",";
              foreach($result as $user){

                //Aceptarse mutuamente con el check
                //add myself to group
                UserLinkHelper::sendLinkRequest($currentUser->getId(),$user->id,Constants::ACCEPTED);
                UserLinkHelper::sendLinkRequest($user->id,$currentUser->getId(),Constants::ACCEPTED);

                          $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                          (empty($user->full_phone)?"null":$user->full_phone).",".
                          (empty($user->last_connection)?"0":$user->last_connection).",9&c3";

              }
            }
            else{
              $usersArray="OK,".$total_items;
            }
            
            $dbConn->closeConnection();
            

           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }


    public static function updateFieldValue($uid,$field,$value){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    $data = array($field =>$value); 
        
                    $where[] = "id = ".$uid;
       
                    $db->update('user', $data , $where);
            
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

      public static function getFieldValue($uid,$field)
        {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);
            $result = $dbConn->fetchAll('SELECT '.$field.' FROM user WHERE id = ?', $uid);
            

            
            $dbConn->closeConnection();
            
           return $result[0][0];
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    // Remove old devices associated with this mac address
     public static function cleanDevicesByMac($mac_address){
                    try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    //Deletes old user_devices
                    $result=$db->delete('user_device', array('mac_address = ?' => $mac_address));

                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

            // Remove old devices associated with this mac address
     public static function cleanDevicesByPushId($pushId,$deviceId){
                    try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();
                    //Deletes old user_devices
                    $result=$db->delete('user_device', array('push_id = ?' => $pushId,'id !=?' => $deviceId));

                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }

        }

    
}
