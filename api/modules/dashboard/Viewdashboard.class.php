<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";

//SubModule Identity
define('MODULE_DASHBOARD_HANDLER_ID', '10020231007220318');
define('SUB_VIEWDASHBOARD', '10020231007220321');
define('SUB_NAME_VIEWDASHBOARD', 'Viewdashboard');
Auth::module_function_registration(SUB_VIEWDASHBOARD, SUB_NAME_VIEWDASHBOARD, MODULE_DASHBOARD_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Dashboard VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-07
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Viewdashboard {
    public $user_type;
    public $userId;
    public $account_character;
    public $method;
    public $url;
    public $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId      = $_SESSION["user_id"];
            $this->user_type   = $_SESSION["user_type"];
            $this->permission  = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('SUB_VIEWDASHBOARD', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }


    //This method return dashboard basis information
    public function get_overview_info($companyId){
        $data =  [
            "total_appointment"             => isset($this->get_appointment_dash_info($companyId)['data'])?$this->get_appointment_dash_info($companyId)['data']:$this->get_appointment_dash_info($companyId),
            "total_department"              => isset($this->get_department_dash_info($companyId)['data'])?$this->get_department_dash_info($companyId)['data']:$this->get_department_dash_info($companyId),
            "total_executive"               => isset($this->get_executive_dash_info($companyId)['data'])?$this->get_executive_dash_info($companyId)['data']:$this->get_executive_dash_info($companyId),
            "total_department_executive"    => isset($this->get_department_staff_dash_info($companyId)['data'])?$this->get_department_staff_dash_info($companyId)['data']:$this->get_department_staff_dash_info($companyId)
        ];
        return $data;
    }

    //This method returns appointment info
    public function get_appointment_dash_info($companyId){
        $query     = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = $companyId");
        if($query === false){
            return ['status' => 500];
        }else{
            $data  = $query->num_rows;
            return ['status' => 200, 'data' => $data];
        }
    }

    //This method returns department info
    public function get_department_dash_info($companyId){
        $query     = CustomSql::quick_select(" SELECT * FROM `departments` WHERE company_id = $companyId");
        if($query === false){
            return ['status' => 500];
        }else{
            $data  = $query->num_rows;
            return ['status' => 200, 'data' => $data];
        }
    }

    //This method returns executive info
    public function get_executive_dash_info($companyId){
        $query     = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE company_id = $companyId");
        if($query === false){
            return ['status' => 500];
        }else{
            $data  = $query->num_rows;
            return ['status' => 200, 'data' => $data];
        }
    }

    //This method returns department staff
    public function get_department_staff_dash_info($companyId){
        $query     = CustomSql::quick_select(" SELECT * FROM `department_staff` WHERE company_id = $companyId");
        if($query === false){
            return ['status' => 500];
        }else{
            $data  = $query->num_rows;
            return ['status' => 200, 'data' => $data];
        }
    }

    //This function returns appointment operation stats
    public function appointment_stats($companyId, $dataFilter){
        $dataCenter    = new DataCenter();
        $data          = [];
        if($dataFilter == 'today'){
            $data      = $dataCenter->daily("appointments", "visit_date", " `company_id` = $companyId");
        }else if($dataFilter == 'yesterday'){
            $data      = $dataCenter->yesterday("appointments", "visit_date", " `company_id` = $companyId");
        }
        else if($dataFilter == 'this_week'){
            $data      = $dataCenter->weekly("appointments", "visit_date", " `company_id` = $companyId");
        }else if($dataFilter == 'this_month'){
            $data      = $dataCenter->monthly("appointments", "visit_date", " `company_id` = $companyId");
        }else if($dataFilter == 'this_year'){
            $data      = $dataCenter->yearly("appointments", "visit_date", " `company_id` = $companyId");
        }

        if(is_array($data)){
            while ($row = mysqli_fetch_assoc($data['data'])) {
                if($row['status'] == 'active'){
                    $data['active'][]     = $row['status'];
                }
                else if($row['status'] == 'pending'){
                    $data['pending'][]    = $row['status'];
                }
                else if($row['status'] == 'check_out'){
                    $data['check_out'][]  = $row['status'];
                }
                else if($row['status'] == 'expired'){
                    $data['expired'][]    = $row['status'];
                }
            }
            return [
                "active"    => isset($data['active'])?array_count_values($data['active'])['active']:0,
                "pending"   => isset($data['pending'])?array_count_values($data['pending'])['pending']:0,
                "check_out" => isset($data['check_out'])?array_count_values($data['check_out'])['check_out']:0,
                "expired"   => isset($data['expired'])?array_count_values($data['expired'])['expired']:0
            ];
        }else{
            return $data;
        }
    }
}