<?php
require_once 'Helper/DbHelper.php';

class BannedDeviceHelper {
    
    
    public static function isBanned($deviceId,$serial_id)
    {$isBanned=FALSE;
     
        try {

            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT device_id FROM banned_device WHERE device_id = ? or device_id = ?', array($deviceId,$serial_id));
           
             $total_items=count($result);


            if ($total_items > 0)
            {   
                
                 $isBanned=TRUE;
               
            }
            
            $dbConn->closeConnection();
            
           return $isBanned;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

    public static function addBanToUserDevices($pin,$userId)
    {$isBanned=FALSE;
     
        try {

            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            $dbConn->beginTransaction();
            //get user devices
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM user_device WHERE uid = ?', $userId);
                
                $total_items=count($result);
              
                
                if ($total_items > 0)
                {  
                  
                  //Ban all user devices
                  foreach($result as $userDevice){
      
                     if($userDevice->device_id!=null || $userDevice->serial_id!=null){

                           if($userDevice->device_id==null)
                                $deviceId = $userDevice->serial_id;
                           else
                                $deviceId = $userDevice->device_id;                        

                           $data = array(
                          'device_id'   => $deviceId,
                          'uid'         => $userId,
                          'pin'         => $pin,
                          'banned_date' => date('Y-m-d H:i:s',time())
                          );
                            $dbConn->insert('banned_device', $data);
                      }
                     
                    }
                     $dbConn->commit();
                }

              

            $dbConn->closeConnection();
            
           return $isBanned;
           
           
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
    
    
}