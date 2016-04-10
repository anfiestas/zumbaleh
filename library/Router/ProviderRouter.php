<?php

require_once 'Helper/CountryRouteHelper.php';

class ProviderRouter {

    private $destinationCountry;
    
    public function __construct($destinationCountry){
     
    $this->destinationCountry=$destinationCountry;
    }
    
    /*
    return: the better provider for this messages
    */
    public function getRoutes()
    {   
        if($this->destinationCountry==null)
            throw new Exception("Error destination Country is null",Constants::ERROR_INTERNAL_SERVER);
        
        $routes=CountryRouteHelper::getRoutes($this->destinationCountry);
        
        return $routes;
      
    }

}
?>