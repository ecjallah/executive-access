<?php
//SubModule Identity
define('MODULE_APPOINTMENT_HANDLER_ID', '10020240228203211');
define('SUB_VIEWAPPOINTMENT', '10020240228203214');
define('SUB_NAME_VIEWAPPOINTMENT', 'Viewappointment');
Auth::module_function_registration(SUB_VIEWAPPOINTMENT, SUB_NAME_VIEWAPPOINTMENT, MODULE_APPOINTMENT_HANDLER_ID);

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

class Viewappointment {
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
            $auth              = Auth::function_check(SUB_VIEWAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all appointments
    public function return_all_appointments($companyId, $pager){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = $companyId AND status != 'delete' ORDER BY `id` DESC LIMIT 15 OFFSET $pager ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns appointment details
    public function get_appointment_details($companyId, $id){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = $companyId AND id = $id AND status != 'delete' ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count === 1){
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}