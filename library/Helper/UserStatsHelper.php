<?php
require_once 'Helper/DbHelper.php';

class UserStatsHelper {
    
    
        public static function createStats($userId,$pin,$timestamp)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();

                     $data = array(
                    'uid'        => $userId,
                    'pin'     => $pin,
                    'creation_time_gmt'  => time()
                    );

                     if($timestamp!=null){
                        $data['creation_date'] =  date('Y-m-d H:i:s',$timestamp);
                        $data['creation_time_gmt'] =  $timestamp;
                        }
                
                $db->insert('user_stats', $data);
                $db->commit();
                
               return true;
           
           
            } catch (Exception $e) {
           $db->rollBack();
               // throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
        }

        public static function updateFieldIncrement($uid,$field){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    $data = array($field => new Zend_Db_Expr($field.' + 1')); 
        
                    $where[] = "uid = ".$uid;
       
                    $db->update('user_stats', $data , $where);
            
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

        public static function updateFieldDecrement($uid,$field){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    $data = array($field => new Zend_Db_Expr($field.' - 1')); 
        
                    $where[] = "uid = ".$uid;
       
                    $db->update('user_stats', $data , $where);
            
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                    echo $e;
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
        }

        public static function updateFieldValue($uid,$field,$value){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    $data = array($field => new Zend_Db_Expr($value)); 
        
                    $where[] = "uid = ".$uid;
       
                    $db->update('user_stats', $data , $where);
            
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
            $result = $dbConn->fetchAll('SELECT '.$field.' FROM user_stats WHERE uid = ?', $uid);
            

            
            $dbConn->closeConnection();
            
           return $result[0][0];
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    
}