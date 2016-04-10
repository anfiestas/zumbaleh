<?php
require_once 'Helper/DbHelper.php';



class TransactionSelfPaymentHelper {
    
    
    public static function createTransaction($orderId,$userId,$entityName,$accountNumber,$countryId,$tokens,$amount, $currencyId,$eqExchange, $userOldTokens, 
     $shortPhone,$status, $status_detail,$promoId,$promoCode,$paypal_mail,$paypalName,$bannerImpr,$is_root)
    {
       //require_once 'Helper/UserHelper.php';
        try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->beginTransaction();
            // $currentTokens=($userOldTokens-$tokens);
            //create message
            $data = array(
    		    'order_id'		 => $orderId,
    		    'user_id'     	 => $userId,
    		    'bank_entity_name'     => $entityName,
            'bank_account_number'  => $accountNumber,
            'paypal_mail'  => $paypal_mail,
            'paypal_name'  => $paypalName,
    		    'country_id'  	 => $countryId,
    		    'tokens'  	 	 => $tokens,
    		    'currency_id'  	 => $currencyId,
    		    'amount'       	 => $amount,
    		    'eq_exchange'    => $eqExchange,
    		    'user_old_tokens'      => $userOldTokens,
            'banner_impr'      => $bannerImpr,
    		    'user_current_tokens'  => 0,
    		    'short_phone'     	   => $shortPhone,
    		    'promo_id'      	   => $promoId,
    		    'promo_code'           => $promoCode,
    		    'start_time'   	       => time(),
                'status'       	       => $status,
		        'status_detail'        => $status_detail,
                'gateway'              => Constants::GATEWAY_TRANSFER,
                'is_root'              => $is_root,
            );

            $db->insert('transaction_self_payment', $data);
            $tid = $db->lastInsertId();

            $db->commit();
            
           return $tid;
           
           
        
        }catch (Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
	   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
           $db->rollBack();
          //$n = $db->delete('messages', 'mid = '.$mid);
        }
      
    }

 public static function getTransactions($userId)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll('SELECT order_id,bank_entity_name,amount,currency_id,start_time FROM transaction_ong WHERE user_id = ? order by id', $userId);
           
             $total_items=count($result);

             $donationArray="".$total_items;

            if ($total_items > 0)
            {   
                
                  foreach($result as $transaction){
                        $donationArray.=",".$transaction->order_id.",".$transaction->ong_name.",".
                        number_format((float)$transaction->amount, 2, '.', '').",".$transaction->currency_id.",".$transaction->start_time;

                        $donationArray.=",9&c3";

                  }
               
            }
            
            $dbConn->closeConnection();

           return $donationArray;
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
  
    
    
}