
<?php
//SubModule Identity
define('MODULE_APPOINTMENT_HANDLER_ID_', '10020240228203211');
define('SUB_EDITAPPOINTMENT', '10020240228203216');
define('SUB_NAME_EDITAPPOINTMENT', 'Editappointment');
Auth::module_function_registration(SUB_EDITAPPOINTMENT, SUB_NAME_EDITAPPOINTMENT, MODULE_APPOINTMENT_HANDLER_ID_);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Appointment VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-28
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Editappointment {
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
            $auth              = Auth::function_check(SUB_EDITAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates appointments
    public function update_executive_appointment($details, $identity){
        $query    = CustomSql::update_array($details, $identity, "appointments");
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}