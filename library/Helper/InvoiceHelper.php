<?php
require_once 'Helper/DbHelper.php';


class InvoiceHelper {
    
    
    public static function createInvoice($userId, $shortPhone, $amount, $currencyId, $countryId, $productId, $orderId, $startTime, $endTime, $gateway)
    {
     
        try {
            $dbHelper= new DbHelper();
            $db=$dbHelper->getConnectionDb();
            
            $db->beginTransaction();
            //create message
            $data = array(
	    'user_id'     	 => $userId,
            'order_id'     	 => $orderId,
	    'short_phone'     	 => $shortPhone,
            'amount'       	 => $amount,
            'currency_id'  	 => $currencyId,
            'product_id'  	 => $productId,
	    'country_id'  	 => $countryId,
	    'start_time'   	 => $startTime,
	    'end_time'   	 => $endTime,
	    'gateway'      	 => $gateway
            );

            $db->insert('invoice', $data);
            $tid = $db->lastInsertId();
	    
            $db->commit();
            
           return $tid;
           
           
        }catch (Exception $e) {
	   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
           $db->rollBack();
          //$n = $db->delete('messages', 'mid = '.$mid);
        }
      
    }
    
    public static function createInvoiceFromTransaction($transaction){
	
	 self::createInvoice($transaction->getUserId(),$transaction->getShortPhone(),$transaction->getAmount(),
			     $transaction->getCurrencyId(),$transaction->getCountryId(),$transaction->getProductId(),
			     $transaction->getOrderId(),$transaction->getStartTime(),$transaction->getEndTime(),$transaction->getGateway());
	
    }
    
   
    
}