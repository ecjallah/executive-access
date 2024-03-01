
<?php

//SubModule Identity
define('MODULE_APPOINTMENT_HANDLER_ID', '10020240228203211');
define('SUB_ADDAPPOINTMENT', '10020240228203220');
define('SUB_NAME_ADDAPPOINTMENT', 'Addappointment');
Auth::module_function_registration(SUB_ADDAPPOINTMENT, SUB_NAME_ADDAPPOINTMENT, MODULE_APPOINTMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Appointment ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-28
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Addappointment {
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
            $auth              = Auth::function_check(SUB_ADDAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method adds new executive appointments
    public function add_new_appointments($details){
        $query    = CustomSql::insert_array("appointments", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}