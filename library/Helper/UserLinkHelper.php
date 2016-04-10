<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/User.php';
require_once 'Objects/Constants.php';


class UserLinkHelper {
    
    
    public static function sendLinkRequest($uid,$with_user_id,$status)
    {
     
        try {
              $dbHelper= new DbHelper();
              $db=$dbHelper->getConnectionDb();
              $result=true;
    
              //Add contact to user
              $data = array(
                     'uid'              => $uid,
                     'with_user_id'     => $with_user_id,
                     'status'     => $status
              );

              //$db->insert('user_link_request', $data);
                $sql = "INSERT INTO user_link_request (uid, with_user_id, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE uid = ?, with_user_id = ?, status = ?";

              $values = array("uid"=>$uid, "with_user_id"=>$with_user_id, "status"=>$status);

              $db->query($sql, array_merge(array_values($values), array_values($values)));      
              
              $db->closeConnection();
            
           return true;
           
           
        } catch(Zend_Db_Statement_Mysqli_Exception $e){
          
            // code 1062: Mysqli statement execute error : Duplicate entry
            if($e->getCode() == 1062) 
            {  
                  $data = array(
                    'status'      => $status
                 );

                //Only can accept the user who receives invitation
                  if($status==Constants::WAITING){
                    $where[] = 'uid='.$uid.' and with_user_id='.$with_user_id." and status < 0";
                    $result=$db->update('user_link_request', $data, $where);
                  }
                  elseif($status==Constants::BLOCKED){
                    $where[] = 'uid='.$uid.' and with_user_id='.$with_user_id." and status > 0";
                    $result=$db->update('user_link_request', $data, $where);
                  }
                
                if($result){

                   return $result;
                } else{

                  //2-"existing request"
                  $result=2;
                  return $result;
                 }
              
            } 
            else 
            { 
                throw $e;
            }
        }catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
    }

    public static function getLinkStatus($uid,$with_user_id)
    {
     
         try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT status FROM user_link_request where uid=? and with_user_id=?',array($uid,$with_user_id));

            if (count($result) > 0)
            {
               $status=$result[0]->status;
              
            }
        
            else{
               $status=null;

            }
            
            $dbConn->closeConnection();
            
           return $status;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
      
    }

    public static function getSendToMe($uid)
    {

       try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where (ul.with_user_id=? and u.id=ul.uid and ul.status=?) 
                                        and ( u.id IN(SELECT with_user_id FROM user_link_request where uid=? and with_user_id=u.id and status >=?) 
                                        or u.id NOT IN(SELECT with_user_id FROM user_link_request where uid=? and with_user_id=u.id))',array($uid,Constants::WAITING,$uid,Constants::WAITING,$uid));

            $total_items=count($result);
            if ($total_items > 0)
            { 
               $usersArray="OK,".$total_items.",";
              foreach($result as $user){
                          $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                          (empty($user->name)?"null":$user->name).",".
                          (empty($user->mail)?"null":$user->mail).",".
                          (empty($user->is_group)?"null":$user->is_group).",".
                          (empty($user->group_owner_pin)?"null":$user->group_owner_pin).",9&c3";
              }
            }
            else{
               $usersArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

    public static function getAllAccepted($uid)
    {

       try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

            $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where (ul.with_user_id=? and u.id=ul.uid and ul.status=?) 
                                        and ( u.id IN(SELECT with_user_id FROM user_link_request where uid=? and with_user_id=u.id and status =?)) 
                                        ',array($uid,Constants::ACCEPTED,$uid,Constants::ACCEPTED));

            $total_items=count($result);
            if ($total_items > 0)
            { 
               $usersArray="OK,".$total_items.",";
              foreach($result as $user){
                          $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                          (empty($user->name)?"null":$user->name).",".
                          (empty($user->mail)?"null":$user->mail).",".
                          (empty($user->is_group)?"null":$user->is_group).",".
                          (empty($user->group_owner_pin)?"null":$user->group_owner_pin).",9&c3";
              }
            }
            else{
               $usersArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

     public static function getConversationsCount($uid)
    {

       try {
        
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

            $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where (ul.with_user_id=? and u.id=ul.uid and ul.status=?) 
                                        and ( u.id IN(SELECT with_user_id FROM user_link_request where uid=? and with_user_id=u.id and status =?)) 
                                        ',array($uid,Constants::ACCEPTED,$uid,Constants::ACCEPTED));

            $total_items=count($result);
           
            $dbConn->closeConnection();
            
           return $total_items;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }


    public static function getSendByMe($uid,$status,$format)
    {

       try {
            $format=strtolower($format);

            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

               $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where ul.uid=? and u.id=ul.with_user_id and ul.status=?',array($uid,$status));
            
            $total_items=count($result);
            if ($total_items > 0)
            { 

              if($format=="im"){
                   $usersArray="OK,".$total_items.",";
                  foreach($result as $user){
                              $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                              (empty($user->name)?"null":$user->name).",".
                              (empty($user->mail)?"null":$user->mail).",9&c3";
                  }
               }
               else if ($format=="array"){
                   $i=0;
                    foreach($result as $user){
                            $nextUser= new User($user->id,$user->name,$user->mail,$user->password,$user->full_phone, $user->short_phone, $user->balance,$user->country_id,$user->im_secret_key,$user->secret_key,$user->pin,$user->group_id,$user->last_connection,$user->connection_type_id,$user->is_group,$user->group_owner_id,$user->group_owner_pin);
                               
                            $usersArray[$i]=$nextUser;
                        $i++;
                    }
                }
            }
            else{
               $usersArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

    public static function getGroupMembersAndSendEvent($groupUser,$user,$status)
    {

       try {

            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

            $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where ul.uid=? and u.id=ul.with_user_id and ul.status=?',array($groupUser->getId(),$status));
            
            $total_items=count($result);
            if ($total_items > 0)
            {    $groupOwner = UserHelper::getUserById($groupUser->getgroupOwnerId());

                $usersArray="OK,".$total_items.",".$groupUser->getgroupOwnerPin().",".$groupOwner->getName().",9&c3";
                //",".$groupUser->getName().",";

                  foreach($result as $userInGroup){
                   
                              if($userInGroup->id!=$groupUser->getgroupOwnerId()){

                                $usersArray.=(is_null($userInGroup->pin)?"null":$userInGroup->pin).",".
                                (empty($userInGroup->name)?"null":$userInGroup->name).",9&c3";

                              }
                          
                  }
                  $usersArray=preg_replace('/\b9&c3$/', '', $usersArray);
                  $usersArray.=$groupUser->getName();

                  //Sent event to All Members
                   $userName=$user->getName();
                   if(is_null($userName) || $userName=="")
                       $userName=$user->getPin();
                  
                  $params=$groupUser->getPin()."9&c3".$user->getPin()."9&c3".$userName;
                  UserEventHelper::sendEventToAllUserLinksWithParams($groupUser->getId(),$groupUser->getPin(),$params,Constants::EVENT_GROUPS_USER_IN);   

            }
            else{
               $usersArray=$response="OK,0,".$groupUser->getgroupOwnerPin().",".$groupOwner->getName();
            }
            
            $dbConn->closeConnection();
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

    public static function getAllSendByMe($uid,$format)
    {

       try {
            $format=strtolower($format);

            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

               $result = $dbConn->fetchAll('SELECT u.* FROM user_link_request ul,user u where ul.uid=? and u.id=ul.with_user_id',array($uid));
            
            $total_items=count($result);
            if ($total_items > 0)
            { 

              if($format=="im"){
                   $usersArray="OK,".$total_items.",";
                  foreach($result as $user){
                              $usersArray.=(is_null($user->pin)?"null":$user->pin).",".
                              (empty($user->name)?"null":$user->name).",".
                              (empty($user->mail)?"null":$user->mail).",9&c3";
                  }
               }
               else if ($format=="array"){
                   $i=0;
                    foreach($result as $user){
                            $nextUser= new User($user->id,$user->name,$user->mail,$user->password,$user->full_phone, $user->short_phone, $user->balance,$user->country_id,$user->im_secret_key,$user->secret_key,$user->pin,$user->group_id,$user->last_connection,$user->connection_type_id,$user->is_group,$user->group_owner_id,$user->group_owner_pin);
                               
                            $usersArray[$i]=$nextUser;
                        $i++;
                    }
                }
            }
            else{
               $usersArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $usersArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }

    }

    public static function acceptLink($uid,$with_user_id)
    {
      
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $db->beginTransaction();
            $result=true;

               /* $data = array(
                'status'      => Constants::ACCEPTED
                 );*/

                //Only can accept the user who receives invitation
                //$where[] = 'uid='.$with_user_id.' and with_user_id='.$uid.' and status >='.Constants::WAITING;
               // $result=$db->update('user_link_request', $data, $where);
                $sql = "INSERT INTO user_link_request (uid, with_user_id, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE uid = ?, with_user_id = ?, status = ?";

              $values = array("uid"=>$with_user_id, "with_user_id"=>$uid, "status"=>Constants::ACCEPTED);

              $db->query($sql, array_merge(array_values($values), array_values($values)));  
                
               // if ($result){
                  $result= UserLinkHelper::sendLinkRequest($uid,$with_user_id,Constants::ACCEPTED);
               //  }
                // else{
                  //2-"existing request"
                //  $result=2;
               // }
              
                    
        $db->commit();
        $db->closeConnection();
        return $result;
   }catch (Exception $e) {
                $db->rollBack();
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }

    }

    public static function blockLink($uid,$with_user_id)
    {
      
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $db->beginTransaction();
             $result=true;

                //Deletes the doble link in confirmed table(breakes the link so users cannot comunicate between them)
               /* $where1[] = 'uid='.$uid.' and with_user_id='.$with_user_id;
                $where2[] = 'uid='.$with_user_id.' and with_user_id='.$uid;

                $result=$db->delete('user_link_confirmed', $where1);
                $result=$db->delete('user_link_confirmed', $where2);
               */
                //sets links request to blocked
                UserLinkHelper::sendLinkRequest($uid,$with_user_id,Constants::BLOCKED);
                //brake link only from blocker side
                //UserLinkHelper::sendLinkRequest($with_user_id,$uid,Constants::BLOCKED);
                   
                    
        $db->commit();
        $db->closeConnection();

        return $result;
   }catch (Exception $e) {
                $db->rollBack();
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }

    }

     public static function unblockLink($uid,$with_user_id)
    {
      
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $db->beginTransaction();
             $result=true;

                //sets links request to unblocked
                UserLinkHelper::sendLinkRequest($uid,$with_user_id,Constants::ACCEPTED);
                //unblock link only from blocker side
                //UserLinkHelper::sendLinkRequest($with_user_id,$uid,Constants::BLOCKED);
                   
                    
        $db->commit();
        $db->closeConnection();

        return $result;
   }catch (Exception $e) {
                $db->rollBack();
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }

    }

    public static function removeLink($uid,$with_user_id)
    {
      
       try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            $db->beginTransaction();
             $result=true;

                //Deletes the doble link in confirmed table(breakes the link so users cannot comunicate between them)
                $where1[] = 'uid='.$uid.' and with_user_id='.$with_user_id;
                $where2[] = 'uid='.$with_user_id.' and with_user_id='.$uid;

                $result=$db->delete('user_link_request', $where1);
                $result=$db->delete('user_link_request', $where2);
               
          
                    
        $db->commit();
        $db->closeConnection();

        return $result;
   }catch (Exception $e) {
                $db->rollBack();
                throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
            }

    }

    public static function sendInvitationLink($user,$toUser){
         
          $result=UserLinkHelper::sendLinkRequest($user->getId(),$toUser->getId(),Constants::WAITING);

          if($result==1){
                //add Event to destinationUser
                UserEventHelper::addEventToUser($toUser->getId(),$toUser->getPin(),Constants::EVENT_USERLINKS_RECEIVED);
                 return true;
            }
            else
              return false;
    }

    
}