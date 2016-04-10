<?php

/**
 * Front Controller plug in to set up the action stack.
 *
 */
class Lang_Controller_Plugin_LanguageSetup extends Zend_Controller_Plugin_Abstract
{
	
    public function dispatchLoopStartup(Zend_Controller_Request_Http $request)
    {   
	    $locale = new Zend_Locale();
		
		// default language when requested language is not available
		$defaultlanguage = 'en';
		 //UNCOMMENT WHEN USING more languages
        $langParam = $this->getRequest()->getParam('lang');
		
       //Zend Translate with notices disabled
		$translate = new Zend_Translate('csv', 
						APPLICATION_PATH .'/views/languages', 
						null,
						array('disableNotices' => true,
						'scan' => Zend_Translate::LOCALE_DIRECTORY));
				
		
		// not available languages are rerouted to English
		if($translate->isAvailable($langParam)){
		     try{
			$translate->setLocale($langParam);
			
		    }catch(Exception $e) {
				      $request->setControllerName('error');
				      $request->setActionName('error');
		    } 
		    
		}
		elseif($translate->isAvailable($locale->getLanguage())){
		    $translate->setLocale($locale->getLanguage());
		}
		else{
		    
		    $translate->setLocale($defaultlanguage);
		}
		 
		 

		 Zend_Registry::set('Zend_Translate',$translate);
		 Zend_Registry::set('Zend_Locale', $locale);
		 Zend_Registry::set('Zend_Request', $request);
    }
}