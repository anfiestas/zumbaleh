<?php
require_once 'Helper/DbHelper.php';

class UserEventHelper {
    
    
    public static function getEvents($pin)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user_event WHERE pin = ? order by id desc', $pin);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type;
               //Deletes event
                $result=$dbConn->delete('user_event', 'id='.$event->id);

               
              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
            
            
            $dbConn->closeConnection();
            
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getEventsByUserId($userId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user_event WHERE uid = ? order by id desc', $userId);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type;
               //Deletes event
                $result=$dbConn->delete('user_event', 'id='.$event->id);

               
              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
            
            
            $dbConn->closeConnection();
            
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function getEventsWithParamsByUserId($userId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user_event WHERE uid = ? order by id desc', $userId);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type.",".(empty($event->params)?"null":$event->params);

               //Deletes event
               $result=$dbConn->delete('user_event', 'id='.$event->id);

              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
            
            
            $dbConn->closeConnection();
            
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    //DEPRECATED, replace by sendEventToAllUserLinksWithParams
    public static function addEventToUser($userId,$pin,$eventType)
        {       
   
            $result="noPush";
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'          =>  $userId,
                'pin'          => $pin,
                'event_type'   => $eventType,
                );
            
                //GCM support
                 // send Android push notification
                 require_once 'Helper/UserHelper2.php';
                 require_once 'Helper/PushNotificationsHelper.php';

                  $userHelper = new UserHelper2($db);
                  $devices=$userHelper->getDevicesFromUser($userId);

                  $num_devices=count($devices);

                  $registrationIDs = array();
                  $nonPushDevices = array();

                  if ($num_devices > 0)
                  { 
                  //check user devices 
                  foreach($devices as $device){

                        if(!empty($device->push_id)){
                          //send push to device
                           array_push($registrationIDs,$device->push_id);
                        }else{
                          //create event in DB if not browser sesion
                          //TODO: BROWSER does not support events NOW
                          if($device->type==Constants::SESSION_TYPE_DEVICE){
                            array_push($nonPushDevices,$device->id);
                          }
                    }
         
                    }
                  }

              //send Notifications
               if(count($registrationIDs) > 0){

                  $message = "OK,1,".$eventType;
                  $result=PushNotificationsHelper::sendGCM($userId,$pin,$registrationIDs,$message);
                }
                if(count($nonPushDevices) > 0){

                  $db->insert('user_event', $data);
                  $db->commit();

                  }
                   $db->closeConnection();
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
        //DEPRECATED, replace by sendEventToAllUserLinksWithParams
        public static function addEventToUserWithParams($userId,$pin,$params,$eventType)
        {   
            $result="noPush";
         try {
     
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
                'uid'          =>  $userId,
                'pin'          =>  $pin,
                'params'   =>   $params,
                'event_type'   =>  $eventType
                );
            
                //GCM support
                 // send Android push notification
                 require_once 'Helper/UserHelper2.php';
                 require_once 'Helper/PushNotificationsHelper.php';

                  $userHelper = new UserHelper2($db);
                  $devices=$userHelper->getDevicesFromUser($userId);

                  $num_devices=count($devices);

                  $registrationIDs = array();
                  $nonPushDevices = array();

                  if ($num_devices > 0)
                  { 
                  //check user devices 
                  foreach($devices as $device){

                    if(!empty($device->push_id)){
                      //send push to device
                      
                       array_push($registrationIDs,$device->push_id);

                    }else{
                      //create event in DB if not browser sesion
                      //TODO: BROWSER does not support events NOW
                      if($device->type==Constants::SESSION_TYPE_DEVICE){
                        array_push($nonPushDevices,$device->id);
                      }

                    }
         
                    }
                  }

              //send Notifications
               if(count($registrationIDs) > 0){
                  
                  $message = "OK,".$eventType.",1,".(empty($params)?"null":$params).",9&c3";
                  $result=PushNotificationsHelper::sendGCM($userId,$pin,$registrationIDs,$message);
                }
                if(count($nonPushDevices) > 0){

                  $db->insert('user_event', $data);
                  $db->commit();
                  }
                
                  $db->closeConnection();
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
    /*
     Sends an event to all links from user UserId
    */

     public static function sendEventToAllUserLinksWithParams($userId,$userPin,$params,$eventType)
     {
      require_once 'Helper/PushNotificationsHelper.php';
        try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $db->fetchAll('SELECT u.*,ud.id as device_id,ud.mac_address,ud.push_id FROM user_link_request ul,user u,user_device ud where ul.uid=? and u.id=ul.with_user_id and ud.uid=u.id and ul.status=1', $userId);
                $db->closeConnection();
                $total_items=count($result);
              
                $registrationIDs = array();

                if ($total_items > 0)
                { 
                  foreach($result as $userDevice){
                        
                          if(!empty($userDevice->push_id))
                                    array_push($registrationIDs,$userDevice->push_id);
                      }
                        $message = "OK,".$eventType.",1,".(empty($params)?"null":$params).",9&c3";

                        //Send push to all Group devices directly with only one request
                        $result=PushNotificationsHelper::sendGCM($userId,$userPin,$registrationIDs,$message);

                }
                
           return true;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    
}
