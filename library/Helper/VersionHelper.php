<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Version.php';
class VersionHelper {
    
    
    public static function getVersion($number)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM version WHERE number = ?', $number);
            
            if (count($result)== 1)
            {
                $version= new Version($result[0]->id,$result[0]->number, $result[0]->validity);
               
            }
            else{
		    //throw new Exception("Error not valid or not existing Version",Constants::ERROR_RESOURCE_NOT_FOUND);
                $version=null;
            }
            
            $dbConn->closeConnection();
            
           return $version;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    
}