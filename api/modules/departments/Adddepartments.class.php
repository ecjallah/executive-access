
<?php

//SubModule Identity
define('MODULE_DEPARTMENTS_HANDLER_ID', '10020240227160102');
define('SUB_ADDDEPARTMENTS', '10020240227160111');
define('SUB_NAME_ADDDEPARTMENTS', 'Adddepartments');
Auth::module_function_registration(SUB_ADDDEPARTMENTS, SUB_NAME_ADDDEPARTMENTS, MODULE_DEPARTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Departments ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Adddepartments {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId      = $_SESSION["user_id"];
            $this->user_type   = $_SESSION["user_type"];
            $this->permission  = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_ADDDEPARTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method creates new departments
    public function create_department($details){
        $query    = CustomSql::insert_array("departments", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}