<?php
require_once 'Helper/DbHelper.php';
require_once 'Objects/UserPromo.php';
require_once 'Helper/ToolsHelper.php';

class UserPromoHelper {
    
    	
    public static function addPromoToUser($userId,$promoId)
        {
         try {
	        $key=strtoupper(ToolsHelper::getRandomString(10));
	         //TODO-Verify that this key does not exist for this promo
		 
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();
                //create message
                $data = array(
		'uid'     =>  $userId,
		'promo_id'   => $promoId,
                'promo_code'   => $key,
                'is_active'     => true
                );
            
                
                $db->insert('user_promo', $data);
                
                $db->commit();
                
               return $key;
           
           
            } catch (Exception $e) {
			   $db->rollBack();
                throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
    }
    public static function getUserPromos($userId){
	    
	     try {
		    
		    $dbHelper= new DbHelper();
		    $dbConn=$dbHelper->getConnectionDb();
		    
		    $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
		    $result = $dbConn->fetchAll('SELECT * FROM user_promo up WHERE is_active=true and user_id = ? ', $userId);
		    
		     if (count($result) > 0)
		     {
		     $i=0;
                
			foreach($result as $user_promo){
                           $nextPromo = new UserPromo($user_promo->uid,$user_promo->promo_id,$user_promo->promo_code,$user_promo->is_active);
                           $promoArray[$i]=$nextPromo;
                           $i++;
                        }
		    }
		    else{
		       $promoArray=null;
		    }
		    
		    $dbConn->closeConnection();
		    
		   return $promoArray;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
	    
    }
    
       public static function getUserPromoByPromoCode($promoId,$promoCode){
	    
	     try {
		    
		    $dbHelper= new DbHelper();
		    $dbConn=$dbHelper->getConnectionDb();
		    
		    $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
		    $result = $dbConn->fetchAll('SELECT * FROM user_promo up WHERE is_active=true and promo_id = ? and promo_code = ?', array($promoId,$promoCode));
		    
		     if (count($result)== 1)
		     {
			$userPromo = new UserPromo($result[0]->uid,$result[0]->promo_id,$result[0]->promo_code,$result[0]->is_active);
			
		     }
		     else{
			$userPromo=null;
			throw new Exception("Error not valid or not existing userID",Constants::ERROR_RESOURCE_NOT_FOUND);
		     }
		    
		    $dbConn->closeConnection();
		    
		   return $userPromo;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
	    
    }
    
    public static function getPromoCodeByUser($userId,$promoId){
	    
	     try {
		    
		    $dbHelper= new DbHelper();
		    $dbConn=$dbHelper->getConnectionDb();
		    
		    $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
		    $result = $dbConn->fetchAll('SELECT * FROM user_promo up WHERE is_active=true and promo_id = ? and uid = ?', array($promoId,$userId));
		    
		     if (count($result)== 1)
		     {
			$userPromo = new UserPromo($result[0]->uid,$result[0]->promo_id,$result[0]->promo_code,$result[0]->is_active);
			
		     }
		     else{
			$userPromo=null;
		     }
		    
		    $dbConn->closeConnection();
		    
		   return $userPromo;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
	    
    }
    
    
    public static function isPromoCodeValid($promoId,$promoCode){
	    
	     try {
		    $valid=false;
		    
		    $dbHelper= new DbHelper();
		    $dbConn=$dbHelper->getConnectionDb();
		    
		    $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
		    $result = $dbConn->fetchAll('SELECT * FROM user_promo WHERE promo_id=? and promo_code=?', array($promoId,$promoCode));
		    
		    if (count($result)== 1)
		         $valid=true;
		    else
		         $valid=false;
		    
		    
		    $dbConn->closeConnection();
		    
		   return $valid;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
	    
    }
    
    public static function setPromoCodeToActive($userId,$promoId,$is_active){
    	
	      try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();
                    
		    //boolean => true or false
                    $data["is_active"] = $is_active;	
                
                    $where[] = "user_id = ".$userId;
		    $where[] = "promo_id = ".$promoId;
                    $db->update('user_promo', $data, $where);
        
                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
	
    }
    
    
    
     public static function removeUserPromo($userId,$promoId){
	    
	     try {
                    $dbHelper= new DbHelper();
                    $db=$dbHelper->getConnectionDb();
                    
                    $where[] = "user_id = ".$userId;
		    $where[] = "promo_id = ".$promoId;
		    //$where = $db->quoteInto('user_id = ? and full_phone=?', array($userId,$fullPhone));
                    $db->delete('user_promo', $where);
                    

                    $db->closeConnection();
                    
                   return true;
                   
                   
                } catch (Exception $e) {
                   throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
              
                }
	    
    }
    
}