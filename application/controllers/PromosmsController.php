<?php 
require_once 'Helper/CurrencyHelper.php';
require_once 'Helper/ProductHelper.php';
require_once 'Helper/UserPromoHelper.php';
require_once 'Helper/UserHelper.php';
/**
 * Class MessagesController
 */
 
class PromosmsController extends Zend_Rest_Controller {
    
    public function init(){
        
        $this->_helper->viewRenderer->setNoRender(true);
		//IMPORTANT: to disable the layout html content to be printed in REST responses
		$this->_helper->layout->disableLayout();
    }
    
    /**
     * indexAction
     */

  public function indexAction()
    {
         //$this->getResponse()
          //  ->appendBody("From indexAction() returning all users");
    }
    
    /*getUserBalance
      return: cod_error,balance
     */
    public function getAction()
    {
	    
            $promoCode=$this->_getParam('id');

             if ($promoCode!=null)
	     {
		$this->_redirect(Zend_Registry::get('Zend_Translate')->getLocale().'/credits/selection/promo/'.$promoCode);
		
	     }

		
            

    }
    
    
    public function postAction()
    {
      // $this->getResponse()
      //     ->appendBody("From postAction()users");

    }
    
    
    
    
    public function putAction()
    {
        //$this->getResponse()
        //   ->appendBody("From putAction() updating the user");

    }
    
    public function deleteAction()
    {
        //$this->getResponse()
        //    ->appendBody("From deleteAction() deleting the user");

    }
    
    public function detailsAction()
    {   $this->_helper->viewRenderer->setNoRender(false);
	$this->_helper->layout->disableLayout();
	
	//get payment values from POST form
	$currencyIdPost=null;
	$userCurrency=null;
	$groupIdPost=null;
	$promo=null;
	$promoId=Constants::PROMO_BOCA_A_BOCA;
	
	//Form values
	 if($this->_getParam('currency')!=null){$currencyIdPost =  $this->_getParam('currency');}
	 if($this->_getParam('group')!=null){$groupIdPost =  $this->_getParam('group');}


	
	//set view Parameters
	
	if($currencyIdPost==null)
	     $currencyIdPost='EUR';
	if($groupIdPost==null)
	     $groupIdPost= Constants::GROUP_1;//TODO if IP from europe ->group1 else group2
	     
        //get Currency list
	$this->view->currencyList = CurrencyHelper::getAll();
	
	$userCurrency=CurrencyHelper::getCurrencyByCode($currencyIdPost);
	
	$this->view->userCurrency=$userCurrency;
	$this->view->productList = ProductHelper::getProductsByCurrencyCodeAndPromo($currencyIdPost,$promoId,$groupIdPost);
	
	
    }
    
    /*Creates a promo Code for all users that don't have promoCode*/
    /*public function codesgenAction()
    {
    
      $usersArray = UserHelper::getNonePromoUsers();
      
      foreach($usersArray as $user){
	        UserPromoHelper::addPromoToUser($user->getId(),Constants::PROMO_BOCA_A_BOCA);
              
            }
    
    }*/

}