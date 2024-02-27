<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("ASSIGN_USER_GROUP_MODULES_MODULE_ID", '100010');
define("ASSIGN_USER_GROUP_MODULES_FUNCTION_ID", '100017');
define("ASSIGN_USER_GROUP_MODULES_FUNCTION_NAME", 'Assign modules to user account groups');
Auth::module_function_registration(ASSIGN_USER_GROUP_MODULES_FUNCTION_ID, ASSIGN_USER_GROUP_MODULES_FUNCTION_NAME, ASSIGN_USER_GROUP_MODULES_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the viewing of user group information. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class AssignAccountGroupModules{
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
            $auth              = Auth::function_check(ASSIGN_USER_GROUP_MODULES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }
    }

    //This function assigns modules to roles
    public function assign_modules_to_account_group($details){
        $query    = CustomSql::insert_array('account_group_module', $details);
        if($query === false){
            return 500;
        }else{
           return 200;
        }
    }
}


