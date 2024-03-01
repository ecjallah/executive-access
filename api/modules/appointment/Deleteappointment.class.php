
<?php
//SubModule Identity
define('MODULE_APPOINTMENT_HANDLER_ID', '10020240228203211');
define('SUB_DELETEAPPOINTMENT', '10020240228203218');
define('SUB_NAME_DELETEAPPOINTMENT', 'Deleteappointment');
Auth::module_function_registration(SUB_DELETEAPPOINTMENT, SUB_NAME_DELETEAPPOINTMENT, MODULE_APPOINTMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Appointment operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-28
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Deleteappointment {
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
            $auth              = Auth::function_check(SUB_DELETEAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method removes/deletes given record by id
    public function remove_appointment($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "appointments");
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
            