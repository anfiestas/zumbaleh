<?php
require_once 'Zend/Locale.php';
//require_once 'Zend/Registry.php';
//require_once 'Zend/Translate.php';
require_once 'Lang/Controller/Plugin/LanguageSetup.php';
//require_once 'Zend/loader/Autoloader.php';
define('ROOT_DIR', realpath(dirname(__FILE__)));

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	
	/** Translation initialization */
	protected function _initRoutes()
	{	//Zend_Session::start();
		$locale = new Zend_Locale();

		 
		// setup front controller
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();
			
		 //Routes definition
		 // Create route with language id (lang)
		$routeLang = new Zend_Controller_Router_Route(
				':lang',
				 array('lang' => $locale->getLanguage()),
				 array('lang' => '[a-z]{2}')
				 );
		//module services route without language
		$imServiceRoute = new Zend_Controller_Router_Route(
				    ':module/:controller/:action/*',
				    array('module' => 'imservices')
				);
		
		 // Instantiate default module route
		$routeDefault = new Zend_Controller_Router_Route_Module(
	        array(),$frontController->getDispatcher(),$frontController->getRequest());
			
		// Chain it with language route
		$routeLangDefault = $routeLang->chain($routeDefault);
		
		//Define REST  Controllers here:
			 $messagesRestRoute = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('messages'))
						);
			 $messagestatusRestRoute = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('messagestatus'))
						);
			 $usersRestRoute2 = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('users'))
						);
					
			 $transactionsRestRoute3 = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('transactions'))
						);
			 $versionsRestRoute = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('versions'))
						);
			 $userKeyRestRoute = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('userkey'))
						 );
			 $promoSmsRestRoute = new Zend_Rest_Route($frontController, array(),
						 array('default' => array('promosms'))
						 );

		
	    $router->addRoute('transactions', $transactionsRestRoute3);
		$router->addRoute('messages', $messagesRestRoute);
		$router->addRoute('users', $usersRestRoute2);
		$router->addRoute('messagestatus', $messagestatusRestRoute);
		$router->addRoute('versions', $versionsRestRoute);
		$router->addRoute('userkey', $userKeyRestRoute);
		$router->addRoute('promosms', $promoSmsRestRoute);
		$router->addRoute('imservices', $imServiceRoute);
		//Lang Route here to make work contextSwitch for mobile devices
		$router->addRoute('lang', $routeLang);
		$router->addRoute('default', $routeLangDefault);
		
		$frontController->throwExceptions(false);
		
		//require_once 'Http/Plugin/HttpAuthenticator.php';
		//Http Authentication Plugin
		//$frontController->registerPlugin(new Plugin_HttpAuthenticator());
		
		//Define layout
		Zend_Layout::startMvc(array('layoutPath' => ROOT_DIR.'/views/scripts/layouts'));
		//Language Plugin
		$frontController->registerPlugin(new Lang_Controller_Plugin_LanguageSetup());

		
		
		
	}
	
	

}

