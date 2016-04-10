<?php
require_once 'Helper/CountryHelper.php';
require_once 'Objects/Constants.php';
class PhoneNumberHelper {
    
    
    public static function removeNationalPrefix($shortPhone,$country)
    {
     $ret=$shortPhone;
     $startWith="";
        try {
           $nationalPrefixes=$country->getNationalPrefix();
           $nationalPrefixes = explode(":", $nationalPrefixes);
           
		   
           foreach($nationalPrefixes as $i => $value){
            
                if (preg_match('/^'.$value.'/', $shortPhone)) {
                    $startWith= $value;
                    break;
                 }
            }
            
            $correctShortPhone=substr($shortPhone,strlen($startWith));
			
           return $correctShortPhone;
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
    
    public static function replacePlusSymbolByZeros($fullPhone){
	$correctPhone=$fullPhone;
	    //If + exist in the fullPhone
              if (preg_match('/^'.Constants::IDD_PREFIX2.'/', $fullPhone)) {
		
                     $correctPhone=substr($fullPhone,strlen(Constants::IDD_PREFIX2));
		    
		     $correctPhone = Constants::IDD_PREFIX.$correctPhone;
              }
	      
	      return $correctPhone;
	
    }
    
     public static function removeInternationalPrefix($fullPhone,$country)
    {
     $correctShortPhone=null;
     $startWith="";
     
        try {
              //If + exist in the fullPhone
              if (preg_match('/^'.Constants::IDD_PREFIX2.'/', $fullPhone)) {
                    $correctShortPhone=substr($fullPhone,strlen(Constants::IDD_PREFIX2));
              }
              else{
                    //Get IDD: International Dialing Prefix
                    $internationalPrefixes=$country->getIdd();
                    $internationalPrefixes = explode(":", $internationalPrefixes);
                    
                    foreach($internationalPrefixes as $i => $value){
                     
                         if (preg_match('/^'.$value.'/', $fullPhone)) {
                             $startWith= $value;
                             break;
                          }
                    }
                
                $correctShortPhone=substr($fullPhone,strlen($startWith));
            }
            
           return $correctShortPhone;
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
    
    public static function toInternationalFormat($fullPhone,$country)
    {
     $correctFullPhone=null;
        try {
           
                //remove IDD if exist
                $correctFullPhone=self::removeInternationalPrefix($fullPhone,$country);
                
                //user wants to send a local sms
                if( strcmp($correctFullPhone,$fullPhone)==0){
                    
                   $shortPhone=$fullPhone;
                   $correctShortPhone=self::removeNationalPrefix($shortPhone,$country);
                   $correctFullPhone = Constants::IDD_PREFIX.
                                      $country->getCountryCode().
                                      $correctShortPhone;
                }
                else{
                     $correctFullPhone = Constants::IDD_PREFIX.
                                      $correctFullPhone;
                }
             
           return $correctFullPhone;
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
    
    /*
   
    */
    public static function getPhoneCountry($phone,$userCountry)
    {
     $returnCountry = null;
     $count=0;
        try {
           
             //If 00 exist then we find the country
             if (preg_match('/^'.Constants::IDD_PREFIX.'/', $phone)) {
                //delete 00
                $shortPhone=substr($phone,2);
                
                for($i=4; $i > 0; $i--){
                    $countryCode=substr($shortPhone,0,$i);
                    $count=CountryHelper::countCountriesWithCountryCode($countryCode);

                    if ($count>=1) break;
                    
                }
                
                if($count==1){
                  $returnCountry = CountryHelper::getCountryByCode($countryCode);
                }
                elseif ($count >1){
                         //delete countryCode
                        $noInternationalPrefixPhone=substr($shortPhone,strlen($countryCode));
                        //Exception: Shared countryCode between Countries
                        //Get mobile_codes
                        
                         if($returnCountry==null){
                            
                                //If mobileCode not from Canada then is USA mobile by default
                                if ($countryCode==1){
                                   
                                    $returnCountry=CountryHelper::getCountry(225);
                                    $mobileCodeSize=3;
                                }
                                //If mobileCode not from Islands then is UK mobile by default
                                elseif ($countryCode==44){
                                    $mobileCodeSize=4;
                                    $returnCountry=CountryHelper::getCountry(224);
                                }
                                 elseif ($countryCode==7){
                                    $mobileCodeSize=1;
                                    $returnCountry=CountryHelper::getCountry(176);
                                }
                            }
                        
                            $mobileCode=substr($noInternationalPrefixPhone,0,$mobileCodeSize);
                            $returnCountryCheck=CountryHelper::getByCountryAndMobileCode($countryCode,$mobileCode);

                            if ($returnCountryCheck!=null)
                                $returnCountry=$returnCountryCheck;
                    }
                   
             }
             else{
                //Message goes to a national phone
                $returnCountry= $userCountry;
             }
           return $returnCountry;
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          throw new Exception("Zend Exception");
        }
      
    }
    
    
    
}