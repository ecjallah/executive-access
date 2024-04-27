<?php
//SubModule Identity
define('MODULE_APPOINTMENTPURPOSE_HANDLER_ID', '10020240427213726');
define('SUB_VIEWAPPOINTMENTPURPOSE', '10020240427213729');
define('SUB_NAME_VIEWAPPOINTMENTPURPOSE', 'View appointment Purpose');
Auth::module_function_registration(SUB_VIEWAPPOINTMENTPURPOSE, SUB_NAME_VIEWAPPOINTMENTPURPOSE, MODULE_APPOINTMENTPURPOSE_HANDLER_ID);

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

class ViewappointmentPurpose {
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
            $auth              = Auth::function_check(SUB_VIEWAPPOINTMENTPURPOSE, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns appointment purpose
    public function get_appointment_purpose($companyId){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointment_purpose` WHERE ministry_id = $companyId AND status = 0 ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $row    = mysqli_fetch_assoc($query);
                return $row;
            }else{
                return 404;
            }
        }
    }

    //This method returns appointment purpose by id
    public function get_appointment_purpose_by_id($companyId, $id){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointment_purpose` WHERE ministry_id = $companyId AND id = $id AND status = 0 ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count === 1){
                $row    = mysqli_fetch_assoc($query);
                return $row;
            }else{
                return 404;
            }
        }
    }
}
            