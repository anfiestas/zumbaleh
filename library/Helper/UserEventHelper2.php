<?php
require_once 'Helper/DbHelper.php';
class UserEventHelper2 {
    
    public function __construct($dbConn){
        $this->dbConn=$dbConn;

    }

    public  function getEvents($pin)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user_event WHERE pin = ? order by id desc', $pin);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type;
               //Deletes event
                $result=$this->dbConn->delete('user_event', 'id='.$event->id);

               
              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
 
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public  function getEventsByUserId($userId)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user_event WHERE uid = ? order by id desc', $userId);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type;
               //Deletes event
                $result=$this->dbConn->delete('user_event', 'id='.$event->id);

               
              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
            
            
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public  function getEventsWithParamsByUserId($userId)
    {
     
        try {
            
            $this->dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $this->dbConn->fetchAll('SELECT * FROM user_event WHERE uid = ? order by id desc', $userId);
            
            $total_items=count($result);
            if ($total_items > 0)
            { 
               $eventsArray="OK,".$total_items;
              foreach($result as $event){
                          $eventsArray.=",".$event->event_type.",".(empty($event->params)?"null":$event->params);

               //Deletes event
               $result=$this->dbConn->delete('user_event', 'id='.$event->id);

              }
               
            }
            else{
               $eventsArray="OK,".$total_items;
            }
            
            
           return $eventsArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

 public function addEventToUser($userId,$pin,$eventType)
        { $result="noPush";
         try {
     
               
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

                  $userHelper = new UserHelper2($this->dbConn);
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

                  $this->dbConn->insert('user_event', $data);
                  $this->dbConn->commit();
                  }
               
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

        public function addEventToUserWithParams($userId,$pin,$params,$eventType)
        { $result="noPush";
         try {
     
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

                  $userHelper = new UserHelper2($this->dbConn);
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
                  
                  $message = "OK,1,".$eventType.",".(empty($params)?"null":$params);
                  $result=PushNotificationsHelper::sendGCM($userId,$pin,$registrationIDs,$message);
                }
                if(count($nonPushDevices) > 0){

                  $this->dbConn->insert('user_event', $data);
                  $this->dbConn->commit();
                  }
                
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
    
    
}
