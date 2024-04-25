
<?php
//SubModule Identity
define('MODULE_OUTSIDEAPPOINTMENT_HANDLER_ID', '10020240416153100');
define('SUB_EDITOUTSIDEAPPOINTMENT', '10020240416153105');
define('SUB_NAME_EDITOUTSIDEAPPOINTMENT', 'EditoutsideAppointment');
Auth::module_function_registration(SUB_EDITOUTSIDEAPPOINTMENT, SUB_NAME_EDITOUTSIDEAPPOINTMENT, MODULE_OUTSIDEAPPOINTMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages OutsideAppointment VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-16
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class EditoutsideAppointment {
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
            $auth              = Auth::function_check(SUB_EDITOUTSIDEAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates given record by id
    public function update_record($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "table");
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
            