<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// header("Allow-Control-Origin: *)");

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This handler Updates user profile information. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class UpdateUserProfile{
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId            = $_SESSION['user_id'];
            $this->user_type         = $_SESSION['user_type'];
        }        
    }

    //THIS FUNCTION UPDATES THE HEALTH CARE SECURITY INFORMATION
    public function update_health_care_security_profile($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'users_security');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION UPDATES REGULAR USER PROFILE BASIC INFORMATION
    public function update_regular_user_basic_info($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'user_accounts');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION UPDATES REGULAR USER SECURITY INFORMATION
    public function update_regular_user_security_profile($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'users_security');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS METHOD REGISTERS NEW AGENT PROMO CODE
    public function register_agent_promo_code($details){
        $query    = CustomSql::insert_array('agent_promo_code', $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS METHOD UPDATES REGISTERED AGENT PROMO CODE
    public function update_agent_promo_code($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'agent_promo_code');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION DELETES USER ACCOUNT
    public function delete_user_account($userId){
        //Deleted
        $details        = ["blocked"  => 1];
        $identity       = ['column'   => 'user_id', 'value' => $userId];
        $query          = CustomSql::update_array($details, $identity, 'users_security');
        if($query === false){
            return 500;
        }else{
            session_destroy();
            return 200;
        }
    }
}