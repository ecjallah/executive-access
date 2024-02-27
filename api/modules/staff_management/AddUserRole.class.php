<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("ADD_USER_ROLE_MODULE_ID", '200010');
define("ADD_USER_ROLE_FUNCTION_ID", '200021');
define("ADD_USER_ROLE_FUNCTION_NAME", 'Add Staff Role');
Auth::module_function_registration(ADD_USER_ROLE_FUNCTION_ID, ADD_USER_ROLE_FUNCTION_NAME, ADD_USER_ROLE_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class creates new user role. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class AddUserRole{
    public $userId;
    public $user_type; 
    public $account_character;
    public $method;             
    public $url; 
    public $permission;
    function __construct(){
        if(isset($_SESSION['user_id'])){ 
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(ADD_USER_ROLE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }        
    }

    //This function adds user roles
    public function add_user_role($details){
        $query    = CustomSql::insert_array('staff_role', $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    public function add_role_modules($assignModules){}
}