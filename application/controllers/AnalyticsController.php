<?php
require_once 'ServicesRest/ServicesRest.php';
require_once 'Zend/Rest/Server.php';

class AnalyticsController extends Zend_Controller_Action
{
protected $server;
	
	
   public function init()
    {
       

    }
    public function indexAction()
    {
	
	 
    }
 
    public function chartAction()
    {
      $userPost     = $this->_getParam('user');
      $passPost = $this->_getParam('pass');

      if($userPost!="pasper")
           throw new Exception("Auth error");
      if($passPost!="perlinoTio1!")
           throw new Exception("Auth error");

      require_once 'Helper/AnalyticsHelper.php';
      $result = AnalyticsHelper::getCountUsersDataByQuery("tokens_historic_count >0");

      //print_r($data);
      $serie1=json_encode($result[1]);
      $dates=json_encode($result[0]);

       $this->view->usersWithMoreThan1Token= $serie1;
    
     
      //tokens_historic_count =0
      $result2 = AnalyticsHelper::getCountUsersDataByQuery("tokens_historic_count =0");
      $serie2=json_encode($result2[1]);
      $dates2=json_encode($result2[0]);

       $this->view->usersWithZeroTokens= $serie2;
      
       if(count($dates) > count($dates2))
                $this->view->categoriesDates = $dates;
       else
          $this->view->categoriesDates = $dates2;

       /**Conversations*/ 

      $result = AnalyticsHelper::getCountUsersDataByQuery("conversations_count >0 and  conversations_count <= 3");

      //print_r($data);
      $serie1=json_encode($result[1]);
      $convdates=json_encode($result[0]);

       $this->view->usersWithMax3Convs= $serie1;
    
     
      //
      $result2 = AnalyticsHelper::getCountUsersDataByQuery("conversations_count = 0");
      $serie2=json_encode($result2[1]);
      $convdates2=json_encode($result2[0]);

       $this->view->usersWithZeroConvs= $serie2;

      $result3 = AnalyticsHelper::getCountUsersDataByQuery("conversations_count > 3");
      $serie3=json_encode($result3[1]);
      $convdates3=json_encode($result3[0]);

       $this->view->usersWithMore3Convs= $serie3;
      
       if(count($convdates) > count($convdates2))
                $this->view->categoriesDates2 = $convdates;
       else
          $this->view->categoriesDates2 = $convdates2;


        if(count($convdates3) > count($this->view->categoriesDates))
              $this->view->categoriesDates2 = $convdates3;

    }

	

}

