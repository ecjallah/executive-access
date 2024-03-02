<?php
//SubModule Identity
define('MODULE_APPOINTMENTSECURITY_HANDLER_ID', '10020240301194745');
define('SUB_VIEWAPPOINTMENTSECURITY', '10020240301194748');
define('SUB_NAME_VIEWAPPOINTMENTSECURITY', 'ViewappointmentSecurity');
Auth::module_function_registration(SUB_VIEWAPPOINTMENTSECURITY, SUB_NAME_VIEWAPPOINTMENTSECURITY, MODULE_APPOINTMENTSECURITY_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages AppointmentSecurity VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-01
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewappointmentSecurity {
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
            $auth              = Auth::function_check(SUB_VIEWAPPOINTMENTSECURITY, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

     //This method liikups an appointment
     public function lookup_appointment($companyId, $lookupVal){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = $companyId AND visitor_name LIKE '%{$lookupVal}%' AND status != 'delete' ");
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
}