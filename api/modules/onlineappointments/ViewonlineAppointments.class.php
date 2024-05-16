<?php
//SubModule Identity
define('MODULE_ONLINEAPPOINTMENTS_HANDLER_ID', '10020240416123315');
define('SUB_VIEWONLINEAPPOINTMENTS', '10020240416123318');
define('SUB_NAME_VIEWONLINEAPPOINTMENTS', 'View online Appointments');
Auth::module_function_registration(SUB_VIEWONLINEAPPOINTMENTS, SUB_NAME_VIEWONLINEAPPOINTMENTS, MODULE_ONLINEAPPOINTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages OnlineAppointments VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-16
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewonlineAppointments {
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
            $auth              = Auth::function_check(SUB_VIEWONLINEAPPOINTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all department executive appointment settings
    public function get_ministry_executives_appointment_settings($businessId, $departmentId){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointment_settings` WHERE ministry_id = $businessId AND department_id = $departmentId ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            $executives = new ViewexecutiveList();
            if($count >= 1){
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    //Get executive info from settings
                    $executiveInfo                               = $executives->return_executive_member_details($businessId, $row['executive_id']);
                    if(is_array($executiveInfo)){
                        $executiveInfo['online_settings']        = $row;
                        $data['executives'][]                    = $executiveInfo;
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }


    //This method returns an executive appointment settings
    public function get_executive_appointment_settings($businessId, $departmentId, $executiveId){
        $query      = CustomSql::quick_select(" SELECT * FROM `appointment_settings` WHERE ministry_id = $businessId AND department_id = $departmentId AND executive_id = $executiveId ");
        if($query === false){
            return 500;
        }else{
            $count  = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }

    //This method returns executive online appointment daily counts
    public function get_executive_online_appointment_daily_count($companyId, $departmentId, $executiveId, $date){
        $dataCenter = new DataCenter();
        $query      = $dataCenter->raw_sql_daily("SELECT * FROM `appointments` WHERE company_id = $companyId AND department_id = $departmentId AND executive_id = $executiveId AND appointment_type = 'online' ", 'date_added');
        return $query;
    }
}