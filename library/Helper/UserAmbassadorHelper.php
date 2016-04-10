<?php
require_once 'Helper/DbHelper.php';
require_once 'Helper/ToolsHelper.php';

class UserAmbassadorHelper {
    
    	
    public static function addAmbassador($ambassador_uid,$new_user_uid)
        {
         try {
                $dbHelper= new DbHelper();
                $db=$dbHelper->getConnectionDb();
                
                $db->beginTransaction();

                $db->setFetchMode(Zend_Db::FETCH_OBJ);

                $result=false;
               //if new user validated before by one embassador then nothing
               $result1 = $db->fetchAll('SELECT * FROM ambassador_program amb WHERE  new_user_uid=? and amb.validated=1', $new_user_uid);
               
               if (count($result1)== 0){

                    //create or update
                    $sql = "INSERT INTO ambassador_program (ambassador_uid, new_user_uid) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE ambassador_uid = ?, new_user_uid = ?";

                  $values = array("ambassador_uid"=>$ambassador_uid, "new_user_uid"=>$new_user_uid);

                  $db->query($sql, array_merge(array_values($values), array_values($values)));      
              

                    
                    $db->commit();
                    $result=true;
                }
               $db->closeConnection();

              //add ambassador stats
               require_once 'Helper/UserStatsHelper.php';
               UserStatsHelper::updateFieldIncrement($ambassador_uid,"ambassador_invite_count");

               return  $result;


            } catch (Exception $e) {
			   $db->rollBack();
                //throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);

            }
      
    }
    public static function validateAmbassadorAndSharePoints($new_user_uid,$new_user_tokens,$new_user_pin){

	     try {
		    $prizeTokens=300;
        $result=0;
		    $dbHelper= new DbHelper();
		    $dbConn=$dbHelper->getConnectionDb();
		  
		    $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);

            //get ambassador_uid if new_user_uid is not being validated before
            $result1 = $dbConn->fetchAll('SELECT * FROM ambassador_program amb,user us WHERE us.id=amb.ambassador_uid and amb.new_user_uid=? and amb.validated=0', $new_user_uid);
            $ambassador_uid = $result1[0]->ambassador_uid;
            $ambassador_tokens = $result1[0]->tokens;
           
            if (count($result1)== 1 && $ambassador_tokens >= 100){
                         

		    //get ambassador num of contacts recomendations
		     $result = $dbConn->fetchAll('SELECT count(ambassador_uid) as recomendations FROM ambassador_program WHERE ambassador_uid=? and validated=1 ', $ambassador_uid);
		     $countRecomendations=$result[0]->recomendations;
            
		    if($countRecomendations<=10)
		    	  $prizeTokens=500;

		    //Update ambassador tokens
                $data["tokens"] = $ambassador_tokens + $prizeTokens;
       
                $where[] = "id = ".$ambassador_uid;
                
                 $dbConn->update('user', $data, $where);
                 
		    //Update new user tokens
             	$data2["tokens"] = $new_user_tokens + $prizeTokens;
                
                 $where2[] = "id = ".$new_user_uid;
       
                 $dbConn->update('user', $data2, $where2);

		    //Validate ambassador_program DONE
				$data3["validated"] = 1;
                $data3["tokens"] = $prizeTokens;
                
                 $where3[] = "ambassador_uid = ".$ambassador_uid;
       
                 $dbConn->update('ambassador_program', $data3, $where3);
                 $result=$prizeTokens;


                 //Send message to Ambassador
                 require_once 'Helper/CountryHelper.php';
                 require_once 'Helper/UserEventHelper.php';
                 require_once 'Helper/IMessageHelper.php';
                 require_once 'Helper/UserHelper.php';
                 require_once 'Objects/Constants.php';

                  $spooraUser = UserHelper::getUserById(-1);   
                  $country = CountryHelper::getCountry($result1[0]->country_id);

                  $textPost1="Congratulations! You've got ".$result." extra spooris thanks to your spoora contact with pin ".$new_user_pin;
                  
                  if($country->getId()==196)
                    $textPost1="Enhorabuena! Acabas de ganar ".$result." spooris. Un nuevo usuario te ha referenciado como EMBAJADOR SPOORA.";
                  
                  else if($country->getId()==73)
                    $textPost1="Bravo, vous venez de ganer ".$result." spooris extra grace à votre contact spoora avec le pin ".$new_user_pin;
                  
                //Send message promo
                $mid = IMessageHelper::sendIMessage($spooraUser,utf8_decode($textPost1),$ambassador_uid ,$result1[0]->full_phone,$result1[0]->pin,2,null,null,Constants::SMS_SUBMITED);
                
                //add Event to destinationUser
                UserEventHelper::addEventToUser($ambassador_uid ,$result1[0]->pin,Constants::SMS_SUBMITED);
                //Update stats
                require_once 'Helper/UserStatsHelper.php';
                UserStatsHelper::updateFieldIncrement($new_user_uid,"commes_from_ambassador_count");

            }
		   

             

		    $dbConn->closeConnection();
		    
		   return $result;

        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_INTERNAL_SERVER);
        }
      
	    
    }
    
    
}