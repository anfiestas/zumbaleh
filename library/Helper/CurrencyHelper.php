<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Currency.php';
class CurrencyHelper {
    
    
    public static function getCurrency($currencyId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM currency WHERE id = ?', $currencyId);
            
            if (count($result)== 1)
            {
                $currency= new Currency($result[0]->id,$result[0]->name, $result[0]->code, $result[0]->html_symbol.";",$result[0]->one_euro_is,$result[0]->one_token_is);
               
            }
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $currency;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Exception("DB Login error");
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
	
	   public static function getCurrencyByCode($currencyCode)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM currency WHERE code = ?', $currencyCode);
            
            if (count($result)== 1)
            {
                $currency= new Currency($result[0]->id,$result[0]->name, $result[0]->code, $result[0]->html_symbol.";",$result[0]->one_euro_is,$result[0]->one_token_is);
               
            }
            else{
               $user=null;
            }
            
            $dbConn->closeConnection();
            
           return $currency;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Exception("DB Login error");
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
    
    public static function getAll()
    {
     $currenciesArray= array();
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT * FROM currency order by code asc');
            
            if (count($result) > 0)
            {
                foreach($result as $currency){
                    $nextCurrency = $currency= new Currency($currency->id,$currency->name, $currency->code, $currency->html_symbol.";",$currency->one_euro_is,$currency->one_token_is);
                    array_push($currenciesArray,$nextCurrency);
                    
                }
            }
            else{
               $currenciesArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $currenciesArray;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
}