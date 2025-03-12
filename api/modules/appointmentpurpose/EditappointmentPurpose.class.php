<?php
//SubModule Identity
define('MODULE_APPOINTMENTPURPOSE_HANDLER_ID', '10020240427213726');
define('SUB_EDITAPPOINTMENTPURPOSE', '10020240427213731');
define('SUB_NAME_EDITAPPOINTMENTPURPOSE', 'Edit appointment Purpose');
Auth::module_function_registration(SUB_EDITAPPOINTMENTPURPOSE, SUB_NAME_EDITAPPOINTMENTPURPOSE, MODULE_APPOINTMENTPURPOSE_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages AppointmentPurpose VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class EditappointmentPurpose {
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
            $auth              = Auth::function_check(SUB_EDITAPPOINTMENTPURPOSE, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates appointment purpose by id
    public function update_appointment_purpose($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "appointment_purpose");
        if($query === false){
            return 500;
        }else{
            if($query === 200){
                return 200;
            }else{
                return 400;
            }
        }
    }
}
            