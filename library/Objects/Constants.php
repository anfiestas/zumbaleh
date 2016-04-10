<?php
class Constants {
    
    //Apply secret key
    const SECRET_APPLY_KEY = "c54813f3e70d6c20b95de6fe657377e3";
    
    //General Errors
    const HTTP_OK               		    = 200;
    const HTTP_OK_CREATED               = 201;
    const ERROR_BAD_REQUEST             = 400;
    const ERROR_AUTHENTICATION_FAILED   = 401;
    const ERROR_NOT_ENOUGH_CREDITS      = 402;
    const ERROR_USER_FORBIDDEN          = 403;
    const ERROR_USER_PIN_LOCKED         = 423;
    const ERROR_RESOURCE_NOT_FOUND      = 404;
    const ERROR_INTERNAL_SERVER         = 500;
    const ERROR_PROVIDER                = 506;
    
    //Message Status
    const SMS_AT_HOME        = -1;
    const SMS_DELIVERED      =  0;
    const SMS_SUBMITED       =  1;
    const SMS_FAILED         =  2;
    const SMS_UNKNOWN        =  3;
    const SMS_EXPIRED        =  4;
     
    //Transaction Status
    const TRANS_RECEIVED        = 5;
    const TRANS_STARTED         = 1;
    const TRANS_PENDING         = 2;
    const TRANS_FAILED          = 3;
    
    //Version Validity status
    const VERSION_VALID                     = 1;
    const VERSION_INVALID_BUT_CONTINUE      = 2;
    const VERSION_INVALID                   = 3;

    const VERSION_VALID_VALUE                = 46;
    const VERSION_INVALID_BUT_CONTINUE_VALUE = 38;
    const VERSION_INVALID_VALUE              = 37;

   const SESSION_TYPE_DEVICE    = 1;
   const SESSION_TYPE_BROWSER   = 2;
    
   const HOST_URL = "www.n2bolsamobile.com/";
   const DISABLE_SMS = "true";
   const GATEWAY_TRANSFER = "Transfer";
   const GATEWAY_LKXA = "LaCaixa";
   
   const LKXA_MERCHANT_PRIVATE_CODE     = "297089823";
   //percentage
   const LKXA_EXTRA_CHARGE = 0.027;
   
    //TEST
   const LKXA_URL='https://sis-t.sermepa.es:25443/sis/realizarPago';
   const LKXA_PRIVATE_KEY='qwertyasdf0123456789';
    
   //REAL
   //const LKXA_URL     = "https://sis.sermepa.es/sis/realizarPago";
   //const LKXA_PRIVATE_KEY               = "219A6N65P2337VR5";
   
   
   //Paypal
   
   /* REAL PARAMETERS Set up your API credentials, PayPal end point, and API version.*/	
    /*const PAYPAL_API_USER = 'n2bolsa_api1.gmail.com';
    const PAYPAL_API_PASS = 'P4AZTEHA2N84KKZB';
    const PAYPAL_API_SIGNATURE = 'APEqQynnceAkdrX5FWveekw8Po29AyTDEEsGBiSHU6WuoNi8fwmFckbs';
    const PAYPAL_API_ENVIRONMENT = 'live';
    */
    /* TEST PARAMETERS */
    const PAYPAL_API_USER  = 'toni_f_1268690167_biz_api1.hotmail.com';
    const PAYPAL_API_PASS  = '1268690172';
    const PAYPAL_API_SIGNATURE = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AUXGwxWT67UU4OUwJlSKiqaavR.C';
    const PAYPAL_API_ENVIRONMENT = 'sandbox';// sandbox or 'beta-sandbox'
    
        
   const GATEWAY_PAYPAL = "PayPal";
   const PAYPAL_CHARGE_EUR_PER = 0.034;
   const PAYPAL_CHARGE_EUR_FIX = 0.35;
   
   const PAYPAL_CHARGE_USD_PER = 0.039;
   const PAYPAL_CHARGE_USD_FIX = 0.30;
   
   const PAYPAL_CHARGE_GBP_PER = 0.039;
   const PAYPAL_CHARGE_GBP_FIX = 0.20;
   
   const PAYPAL_CHARGE_AUD_PER = 0.039;
   const PAYPAL_CHARGE_AUD_FIX = 0.30;
   
   const PAYPAL_CHARGE_CAD_PER = 0.039;
   const PAYPAL_CHARGE_CAD_FIX = 0.30;
   
   const PAYPAL_CHARGE_CHF_PER = 0.039;
   const PAYPAL_CHARGE_CHF_FIX = 0.55;

   const PAYPAL_CHARGE_JPY_PER = 0.039;
   const PAYPAL_CHARGE_JPY_FIX = 40;
   
   //International Dialing Prefix (IDD)
   const IDD_PREFIX = "00";
   const IDD_PREFIX2 = " ";
   
   //Promos
   const PROMO_NOTHING = 0;
   const PROMO_BOCA_A_BOCA = 1;
   const PROMO_EXISTING_USER = 2;
   
   //Prices groupes
   const GROUP_1 = 1;
   const GROUP_2 = 2;
   
   //user and phone params status
   const ONLY_USER_PARAMS = 1;
   const ONLY_PHONE_PARAMS = 2;
   const USER_AND_PHONE_PARAMS = 3;
   //user link status
   const WAITING = 0;
   const ACCEPTED = 1;
   const BLOCKED = -1;
   //User events
   const EVENT_USER_PIN_CHANGED    = 201;
   //Message Events
   const EVENT_MESSAGES_PENDINGS    = 1;
   const EVENT_MESSAGES_STATUS_CHANGED     = 2;
   //user events
   //Peticion de amistad recibida de un user o un grupo
   const EVENT_USERLINKS_RECEIVED  = 3;
   const EVENT_USERLINKS_STATUS_CHANGED  = 4;
   //groups events
   const EVENT_GROUPS_USER_IN            = 101;
   const EVENT_GROUPS_USER_EXIT_BY_ADMIN  = 102;
   const EVENT_GROUPS_USER_EXIT           = 103;

   const EVENT_GROUPS_USER_UPDATE_PROFILE_PHOTO  = 104;
   const EVENT_GROUPS_USER_UPDATE_PROFILE_NAME  = 105;
   const EVENT_GROUPS_USER_UPDATE_PROFILE_DESC  = 106;

   //Push Events
   const EVENT_PUSH_MESSAGES_PENDINGS = 696969;
   const EVENT_PUSH_MESSAGES_STATUS_CHANGED = 696989;

   //Tokens
    const TOKENS_OK       = 1;
    const TOKENS_NOT_OK   = 2;
   
    const MEMCACHE = FALSE;
    // 16 tokens max minute, cache clean after 300 seconds
    const MEMCACHE_TOKENS_SESSION = 60;
    const MAX_IM_MINUTE = 16;

    const WELCOME_TOKENS = 100;

    const TOKENS_USER_BANNED_ADBLOCK_PLUS     = 50;
    const TOKENS_USER_BANNED_GENERIC     = 51;
    const TOKENS_USER_CHEATER     = 52; //TODO implementar user stafador si no coinciden spooris y impresiones
    //Tokens program
    const TOKENS_PROG_DISABLED     = 0;
    const TOKENS_PROG_MONEY_TEST    = 1;
    const TOKENS_PROG_MONEY_REAL    = 2;
    const TOKENS_PROG_PROMOS_TEST   = 3;
    const TOKENS_PROG_PROMOS_REAL   = 4;

    //tamaño maximo de la trama de respuesta
    const RESPONSE_MAX_SIZE=2096;
    //Caracteres de control usados como minimo en una respuesta pendings
    const RESPONSE_IM_CONTROL_CHARS = 88;
    //RESPONSE_MAX_SIZE-RESPONSE_IM_CONTROL_CHARS
    const MAX_IM_TEXT_SIZE   = 2008;

    const MONTH_ONG_ID   = 9;//ACNUR
    const MONTH_ONG_NAME = "ACNUR";
    //MINIMUM BALANCE for donations and self payments
    const MIN_BALANCE_DONATIONS = 0;

    //******Modify here for eq exchange updates******/
    //EQUATION EXCHANGE (in cents) 100 spooris -> 0.02 euros
    const EQUATION_EXCHANGE_EUR   = 2;
    //Minimum value for self payment in euros
    const MIN_BALANCE_SELF_PAYMENT = 5;
    //fixed value for ONG donation (in euros). 1 euro
    const FIXED_SELF_PAYMENT_VALUE_ONG   = 1;
    const TOKENS_MAX_DAY = 1500;
    //TODO: Max tokens per month (not used now)
    //const TOKENS_MAX_MONTH = 6000;
    const TOKENS_MAX_BEFORE_AD = 80;

    const GCM_API_KEY = "AIzaSyCKG7jz6g25m-3lYZleQNN0YRcHYorJMUw";

    const GET_LAST_MESSAGES_PAGE_SIZE = 50;
    public static $categories = array(1 => "Comunidades",
                        2 => "Deportes",
                        3 => "Programas TV",
                        4 => "Musica",
                        5 => "Juegos",
                        6 => "Tecnologia",
                        7 => "Operadores",
                        8 => "Variado",
                        9 => "General");

}
