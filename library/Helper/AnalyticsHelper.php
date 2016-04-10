<?php
require_once 'Helper/DbHelper.php';

class AnalyticsHelper {
    
    
    public static function getCountUsersDataByQuery($query)
    {
     
        try {
            $dbHelper= new DbHelper();
            $dbConn=$dbHelper->getConnectionDb();
            
            $dbConn->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $dbConn->fetchAll("SELECT FROM_UNIXTIME(creation_time_gmt, '%d/%m%/%Y' ) as creation_time_gmt,creation_time_gmt as time_ts, count(uid) as users FROM user_stats WHERE ".$query." GROUP BY FROM_UNIXTIME( creation_time_gmt,  '%Y/%m/%d')");
           
            $total_items=count($result);
            $data = array();
            $dates = array();
             $last_date=0;
            if ($total_items > 0)
            {   
                
                  foreach($result as $res){
                    $creationTimeTs=strtotime('tomorrow', $res->time_ts);

                    $dateDiff=0;
                    if($last_date >0){
                    $dateDiff=  $creationTimeTs - $last_date;
                    $dateDiff= floor($dateDiff/(24*60*60));
                  
                    }
              
                        if($dateDiff > 1 ){

                           for($i=1; $i<= ($dateDiff-1); $i++){
                         
                            $zeroDataDays=  $last_date + (24*60*59*($i));
                            array_push($dates,date('d/m/Y',$zeroDataDays));
                            array_push($data,0);

                            }

                        }
                        $value = [$res->users];
                        array_push($data,$value);
                        array_push($dates,$res->creation_time_gmt);
                         $last_date=strtotime('tomorrow', $res->time_ts);
                  
                  }
            }

            $dbConn->closeConnection();
            
           
            return array($dates, $data);
           
           
        } catch (Zend_Exception $e) {
          throw new Exception($e,Constants::ERROR_RESOURCE_NOT_FOUND);
        }
      
    }
    
}