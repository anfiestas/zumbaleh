<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/Product.php';
class ProductHelper {
    
    
    public static function getProductByCurrencyCode($id,$code,$groupId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT prod.id,prod.name,prod.credits_number,prod.free_credits,pric.price_in_currency,pric.currency_id,prod.credits_sms,pric.price_sms,cur.html_symbol,prod.promo_id,pric.group_id FROM product prod,product_price pric,currency cur
                                        WHERE pric.product_id=prod.id and pric.currency_id=cur.id and prod.id = ? and cur.code=? and pric.group_id=?', array($id, $code,$groupId));
                        
            if (count($result)== 1)
            {
                $product= new Product($result[0]->id,$result[0]->name,$result[0]->credits_number,$result[0]->free_credits,$result[0]->price_in_currency,
                                      self::getPriceInCurrencyFormat($result[0]->currency_id,$result[0]->price_in_currency,$result[0]->html_symbol.";"),
                                      $result[0]->currency_id,$result[0]->price_sms,$result[0]->credits_sms,$result[0]->promo_id,$result[0]->group_id);
               
            }
            else{
               $products=null;
            }
            
            $dbConn->closeConnection();
            
           return $product;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
    public static function getProductsByCurrencyCode($code)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT prod.id,prod.name,prod.credits_number,prod.free_credits,pric.price_in_currency,pric.currency_id,prod.credits_sms,pric.price_sms,cur.html_symbol,prod.promo_id,pric.group_id FROM product prod,product_price pric,currency cur
                                        WHERE pric.product_id=prod.id and pric.currency_id=cur.id and cur.code=?',$code);
            
            if (count($result) > 0)
            {
                $productsArray = array();
                $i=0;
                
                foreach($result as $product){
                    $nextProduct= new Product($product->id,$product->name,$product->credits_number,$result[0]->free_credits,$product->price_in_currency,
                                              self::getPriceInCurrencyFormat($product->currency_id,$product->price_in_currency,$product->html_symbol.";"),
                                              $product->currency_id,$product->price_sms,$product->credits_sms,$product->promo_id,$product->group_id);
                    $productsArray[$i]=$nextProduct;
                    $i++;
                }
            }
            else{
               $productsArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $productsArray;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
        public static function getProductsByCurrencyCodeAndPromo($code,$promoId,$groupId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT prod.id,prod.name,prod.credits_number,prod.free_credits,pric.price_in_currency,pric.currency_id,prod.credits_sms,pric.price_sms,cur.html_symbol,prod.promo_id,pric.group_id FROM product prod,product_price pric,currency cur
                                        WHERE pric.product_id=prod.id and pric.currency_id=cur.id and cur.code=? and prod.promo_id=? and pric.group_id=?',array($code,$promoId,$groupId));
            
            if (count($result) > 0)
            {
                $productsArray = array();
                $i=0;
                
                foreach($result as $product){
                    $nextProduct= new Product($product->id,$product->name,$product->credits_number,$product->free_credits,$product->price_in_currency,
                                              self::getPriceInCurrencyFormat($product->currency_id,$product->price_in_currency,$product->html_symbol.";"),
                                              $product->currency_id,$product->price_sms,$product->credits_sms,$product->promo_id,$product->group_id);
                    $productsArray[$i]=$nextProduct;
                    $i++;
                }
            }
            else{
               $productsArray=null;
            }
            
            $dbConn->closeConnection();
            
           return $productsArray;
           
           
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error ". $e;
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "unknow error ".$e;
        }
      
    }
    
    public static function getPriceInCurrencyFormat($currencyId,$price,$currencySymbol){
        $priceInFormat="";
        if(floor($price) != $price)
           $price = number_format($price, 1, '.', '');
        switch($currencyId){
            //EUR
            case 1: $priceInFormat=$price."".$currencySymbol; break;
            //USD
            case 2: $priceInFormat=$currencySymbol."".$price; break;
            //AUD    
            case 3: $priceInFormat=$currencySymbol."".$price; break;
            //CAD    
            case 4: $priceInFormat=$currencySymbol."".$price; break;
            //GBP    
            case 5: $priceInFormat=$currencySymbol." ".$price; break;
            //CHF    
            case 6: $priceInFormat=$price." ".$currencySymbol; break;
            //JPY   
            case 8: $priceInFormat=$currencySymbol." ".$price; break;
        }
        return $priceInFormat;
    }
    
}