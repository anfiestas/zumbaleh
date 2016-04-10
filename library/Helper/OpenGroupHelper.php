<?php
require_once 'Helper/DbHelper.php';

class OpengroupHelper {
    
    
        public static function create($name,$description,$mode,$category_id,$admin_id,$admin_pin,$groupPassword)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                $timestamp=time();
                $db->beginTransaction();

                 $pin=UserHelper::getNewRandomPin(8);
                 $groupId=null;                                   

                 $data = array(
                                'pin'            => $pin,
                                'name'            => $name,
                                'is_group'       => 2,
                                'group_owner_id' => $admin_id,
                                'group_owner_pin' => $admin_pin,
                                'tokens_program' => 2,
                                'description' => $description,
                                'group_mode' => $mode,
                                'group_category_id' => $category_id
                            );
                                  
                        if ($groupPassword!=null)
                               $data['password'] = md5($groupPassword);

                                
                        $db->insert('user', $data);
                        $groupId = $db->lastInsertId();
        /*************CREATE OPEN GROUP FOR FAST READ TABLE***********/

                     $data = array(
                    'id'        => $groupId,
                    'pin'            => $pin,
                    'name'     => $name,
                    'description'  => $description,
                    'mode'  => $mode,
                    'category_id'  => $category_id,
                    'group_owner_id'  => $admin_id,
                    'group_owner_pin'  => $admin_pin
                    );

                 if($timestamp!=null){
                        $data['creation_date'] =  date('Y-m-d H:i:s',$timestamp);
                        $data['creation_time_gmt'] =  $timestamp;
                        }
                if ($groupPassword!=null)
                               $data['password'] = md5($groupPassword);

                $db->insert('opengroup', $data);
                $db->commit();
                
               return $groupId;
           
           
            } catch(Zend_Db_Statement_Mysqli_Exception $e){
          
                // code 1062: Mysqli statement execute error : Duplicate entry
                if($e->getCode() == 1062) 
                {  echo "Error 1, Duplicated Channel name";
                   throw new Exception("Error,1, Duplicated Alias name",Constants::ERROR_BAD_REQUEST);
                     
                     return false;
                  
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
        public static function getGroupsByCategory($category_id)
        {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);
            $result = $dbConn->fetchAll('SELECT * FROM opengroup WHERE category_id = ? and mode > 0', $category_id);
            $total_items=count($result);
            $groupList="";

             foreach($result as $group){
                 //($group['password']!=null? '1':'0')
                 $groupList.=$group['pin'].",".$group['name'].",".$group['mode'].",".$group['users_count'].",9&c3";
             }
            
            $dbConn->closeConnection();
            
           return "OK,".$total_items.",".$groupList;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

        public static function getInfo($groupId)
        {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);
            $result = $dbConn->fetchAll('SELECT * FROM opengroup WHERE pin = ?', $groupId);
            if ($result==null)
              return null;

            //$result2 = $dbConn->fetchAll('SELECT count(uid) FROM user_link_request WHERE uid = ?', $result[0]["id"]);
            //$total_items=$result2[0][0];
            
            $dbConn->closeConnection();
            
            //$result[0]["count"]= $total_items;


           return $result[0];
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

     public static function getNumElems($groupId)
        {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);

            $result2 = $dbConn->fetchAll('SELECT count(uid) FROM user_link_request WHERE uid = ?', $groupId);
            
            if ($result2==null)
              return null;

            $total_items=$result2[0][0];
            
            $dbConn->closeConnection();
            

           return $total_items;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }


        public static function updateFieldValue($id,$field,$value){
            
                try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();

                    $data = array($field => new Zend_Db_Expr($value)); 
        
                    $where[] = "id = ".$id;
       
                    $db->update('opengroup', $data , $where);
                    $db->commit();
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
            $result = $dbConn->fetchAll('SELECT '.$field.' FROM opengroup WHERE id = ?', $uid);
            

            
            $dbConn->closeConnection();
            
           return $result[0][0];
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    public static function searchGroupsByName($name)
        {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);
            $result = $dbConn->fetchAll('SELECT * FROM opengroup WHERE MATCH(name) against ("*'.$name.'*" in boolean mode)');
            $total_items=count($result);
            $groupList="";

             foreach($result as $group){

                 $groupList.=$group['pin'].",".$group['name'].",".($group['password']!=null? '1':'0').",9&c3";
             }

            
            $dbConn->closeConnection();
            
           return "OK,".$total_items.",".$groupList;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }

      public static function addUserToGroup($groupId,$userId,$alias)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                $db->beginTransaction();
                                

                 $data = array(
                                'group_id'    => $groupId,
                                'uid'         => $userId,
                                'alias'       => $alias);
                                  
                        $db->insert('opengroup_user', $data);
                        $db->commit();
                        $db->closeConnection();

               return true;
           
           
           } catch(Zend_Db_Statement_Mysqli_Exception $e){
          
                // code 1062: Mysqli statement execute error : Duplicate entry
                if($e->getCode() == 1062) 
                {  echo "Error 1, Duplicated Alias name or user already registered";
                   throw new Exception("Error,1, Duplicated Alias name",Constants::ERROR_BAD_REQUEST);
                     
                     return false;
                  
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

        public static function removeUserFromGroup($groupId,$userId)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                $db->beginTransaction();
                              
                        //Deletes event
                         $result=$db->delete('opengroup_user', array('group_id = ?' => $groupId,'uid =?' => $userId));

                        $db->commit();
                        $db->closeConnection();

               return true;
           
           
          
            } catch (Exception $e) {

                    $this->dbConn->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
        }

        public static function getOpengroupsByUser($userId)
        {
         try {
               
              $dbHelper= new DbHelper();
              $dbConn=$dbHelper->getConnectionDb();
              
              $dbConn->setFetchMode(Zend_Db::FETCH_BOTH);
              $result = $dbConn->fetchAll('SELECT o.id,o.pin,ou.uid,ou.alias FROM opengroup_user ou,user_link_request ulr,opengroup o where ou.uid=? and ou.group_id=ulr.uid and ulr.with_user_id=? and ou.group_id=o.id',array($userId,$userId));


  
              $dbConn->closeConnection();
              
             return $result;
           
          
            } catch (Exception $e) {

                    $this->dbConn->rollBack();
                    throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
        }

}