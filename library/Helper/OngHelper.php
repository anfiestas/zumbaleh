<?php
require_once 'Helper/DbHelper.php';

class OngHelper {
    
    
    public static function getOngIdListByCountry($country_id)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT ong_id FROM country_ong WHERE country_id = ? order by ong_id desc', $country_id);
           
             $total_items=count($result);

             $ongArray="".$total_items;

            if ($total_items > 0)
            {   
                
                  foreach($result as $ong){
                        $ongArray.=",".$ong->ong_id;

                  }
               
            }
            
            $dbConn->closeConnection();
            
           return $ongArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
    
}