<?php
include_once dirname(__FILE__).'/Autoloader.class.php';
/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the login of every user on the app. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class Logout extends Auth{
    public $method;
    public $url;
    function __construct()
    {
        $this->method    = $_SERVER['REQUEST_METHOD'];
        $this->url       = $_SERVER['REQUEST_URI'];

        if($this->method !== "POST"){
            $response = new Response(301, "This endpoint accepts the POST method", $this->url);
            $response->send_response();
        }else{
            $this->log_user_out();
        }
    }

    //This function logs out all users
    public static function log_user_out(){
        if(isset($_SESSION)){
            session_destroy();
            $response = new Response(301, "Session expired Please Login", "......Unsuccessful entry");
            $response->send_response();
        }else if(!isset($_SESSION)){
            $response = new Response(301, "Please Login as a vaid user!");
            $response->send_response();
        }
    }
}


if($_SERVER['REQUEST_URI'] == '/api/app-logout'){
    (new Logout());
}