<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Country.php';
class CountryHelper {
    
    
    public static function getCountry($id)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM country WHERE id = ?', $id);
           
            if (count($result)== 1)
            {
                $country= new Country($result[0]->id,$result[0]->idd,$result[0]->country_code,$result[0]->mobile_code,$result[0]->national_prefix,$result[0]->coverage,$result[0]->name,$result[0]->group_id);
               
            }
            else{
               $country=null;
            }
            
            $dbConn->closeConnection();
            
           return $country;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
    public static function getCountryByCode($CountryCode){
        
         try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM country WHERE country_code = ?', $CountryCode);
            
            if (count($result) >= 1)
            {
                $country= new Country($result[0]->id,$result[0]->idd,$result[0]->country_code,$result[0]->mobile_code,$result[0]->national_prefix,$result[0]->coverage,$result[0]->name,$result[0]->group_id);
               
            }
            else{
               $country=null;
            }
            
            $dbConn->closeConnection();
            
           return $country;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
    }
    
    public static function getAll()
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM country order by name asc');
            
            if (count($result) > 0)
            {
                $i=0;
                
                foreach($result as $country){
                    $nextCountry = new Country($country->id,$country->idd,$country->country_code,$country->mobile_code,$country->national_prefix,$country->coverage,$country->name,$country->group_id);
                    $countriesArray[$i]=$nextCountry;
                    $i++;
                }
            }
            else{
               $countriesArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $countriesArray;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
    public static function countCountriesWithCountryCode($countryCode){
        $countryCount=0;
        
         try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT count(country_code) as country_count FROM country WHERE country_code = ?',$countryCode);
            
            if (count($result) > 0)
            {
                $countryCount = $result[0]->country_count;
            }
            
            $dbConn->closeConnection();
            
           return $countryCount;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
        
    }
    
      public static function getByCountryAndMobileCode($countryCode,$mobileCode){
        $country=null;
        
         try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM country where country_code=? and mobile_code like ?',array($countryCode,"%".$mobileCode."%"));
            
            if (count($result) > 0)
            {
                $country= new Country($result[0]->id,$result[0]->idd,$result[0]->country_code,$result[0]->mobile_code,$result[0]->national_prefix,$result[0]->coverage,$result[0]->name,$result[0]->group_id);
            }
            
            $dbConn->closeConnection();
            
           return $country;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
        
    }
    
    
    
}