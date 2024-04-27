<?php

//SubModule Identity
define('MODULE_APPOINTMENTPURPOSE_HANDLER_ID', '10020240427213726');
define('SUB_ADDAPPOINTMENTPURPOSE', '10020240427213735');
define('SUB_NAME_ADDAPPOINTMENTPURPOSE', 'Add appointment Purpose');
Auth::module_function_registration(SUB_ADDAPPOINTMENTPURPOSE, SUB_NAME_ADDAPPOINTMENTPURPOSE, MODULE_APPOINTMENTPURPOSE_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages AppointmentPurpose ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class AddappointmentPurpose {
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
            $auth              = Auth::function_check(SUB_ADDAPPOINTMENTPURPOSE, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method created new appointment purpose
    public function create_appointment_purpose($details){
        $query    = CustomSql::insert_array("appointment_purpose", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}
            