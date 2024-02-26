<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles the login of every user on the app. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */


 // WAITING TO BE IN USED
 
class RegistrationLogin extends Auth{

    function __construct()
    {
        $this->method    = $_SERVER['REQUEST_METHOD'];
        $this->url       = $_SERVER['REQUEST_URI'];
    }

    //QUICK REGISTRATION LOGIN
    public function registration_login($number, $password){
        $login    = $this->auth_user_login($number, $password);
        if($login === 200){
            return 200;
        }else if($login == 404){
            return 404;
        }else if($login == 502){
            return 502;
        }
        else if($login === false){
            return 500;
        }else if($login === 404){
            return 404;
        }
        
    }
}
