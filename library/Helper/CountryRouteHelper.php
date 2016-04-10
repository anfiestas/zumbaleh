<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/CountryRoute.php';

class CountryRouteHelper {
    
    
    public static function getRoutes($country)
    {
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM country_route WHERE country_id = ?', $country->getId());
           
           if (count($result) > 0)
            {
                $i=0;
                
                foreach($result as $route){
                    $nextRoute = new CountryRoute($route->country_id,$route->preference,$route->provider_id,$route->price);
                    $routesArray[$i]=$nextRoute;
                    $i++;
                }
            }
            else{
               $routesArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $routesArray;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
    
    
}