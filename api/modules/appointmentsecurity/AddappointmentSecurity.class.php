
<?php

//SubModule Identity
define('MODULE_APPOINTMENTSECURITY_HANDLER_ID', '10020240301194745');
define('SUB_ADDAPPOINTMENTSECURITY', '10020240301194754');
define('SUB_NAME_ADDAPPOINTMENTSECURITY', 'AddappointmentSecurity');
Auth::module_function_registration(SUB_ADDAPPOINTMENTSECURITY, SUB_NAME_ADDAPPOINTMENTSECURITY, MODULE_APPOINTMENTSECURITY_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages AppointmentSecurity ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-01
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class AddappointmentSecurity {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_ADDAPPOINTMENTSECURITY, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method add given record
    public function add_record($details){
        $query    = CustomSql::insert_array("table", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}
            