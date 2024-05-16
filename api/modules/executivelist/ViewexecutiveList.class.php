<?php
//SubModule Identity
define('MODULE_EXECUTIVELIST_HANDLER_ID', '10020240227182744');
define('SUB_VIEWEXECUTIVELIST', '10020240227182747');
define('SUB_NAME_VIEWEXECUTIVELIST', 'View executive List');
Auth::module_function_registration(SUB_VIEWEXECUTIVELIST, SUB_NAME_VIEWEXECUTIVELIST, MODULE_EXECUTIVELIST_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages ExecutiveList VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewexecutiveList {
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
            $auth              = Auth::function_check(SUB_VIEWEXECUTIVELIST, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all executive members
    public function return_executive_list($companyId, $pager, $filter){
        $filterCond = $filter != null ? " AND `status` = '$filter'" : '';
        $pageCond   = '';
        if ($pager != null) {
            $count    = 15; 
            $pageNum  = intval($pager);
            $offset   = $pageNum * $count;
            $pageCond = $pager != null ? " LIMIT $count OFFSET $offset" : '';
        }
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE `company_id` = '$companyId' $filterCond ORDER BY `id` DESC $pageCond");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count >= 1){
                $data       = [];
                $department = new Viewdepartments();
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[]     = [
                        'department_info' => $department->get_departments_details($companyId, $row['department_id']),
                        'staff_info'      => $row
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns a executive member detaild by id
    public function return_executive_member_details($companyId, $id){
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE company_id = '$companyId' AND `id` = $id ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count === 1){
                $department             = new Viewdepartments();
                $row                    = mysqli_fetch_assoc($query);
                $row['department_info'] =  $department->get_departments_details($companyId, $row['department_id']);
                return $row;
            }else{
                return 404;
            }
        }
    }

    //This method returns all executive members from a department
    public function return_department_executive_list($companyId, $departmentId, $pager = null){
        $pageCond = $pager != null ? "LIMIT 15 OFFSET $pager" : '';
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE `company_id` = '$companyId' AND `department_id` = $departmentId AND `status` != 'remove' ORDER BY `id` DESC $pageCond");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count >= 1){
                $data       = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns all executive members from a department WITHOUT PAGENATION
    public function return_department_executives_($companyId, $departmentId){
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE `company_id` = '$companyId' AND `department_id` = $departmentId AND `status` != 'remove' ORDER BY `id` DESC ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count >= 1){
                $data       = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $dailyAppointmentLimit =  $this->check_executive_active_solt($companyId, $departmentId, $row['id']);
                    if($dailyAppointmentLimit === 200){
                        $data[] = $row;
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method checks if an executive has an active/open appointment solt
    public function check_executive_active_solt($companyId, $departmentId, $executiveId){
        //Get executive settings
        $onlineAppointment = new ViewonlineAppointments();
        $executiveSettings = $onlineAppointment->get_executive_appointment_settings($companyId, $departmentId, $executiveId);
        if(is_array($executiveSettings)){
            $date                     = gmdate('Y-m-d');
            $today                    = strtolower(date('l', strtotime($date)));
            $todayAppointmentStatus   = 0;
            $todayAppointmentLimit    = $executiveSettings['open_solt'];

            //Get executive daily appointment
            foreach ($executiveSettings as $key => $value) {
                if($key === $today){
                    $todayAppointmentStatus = $value;
                }
            }

            if($todayAppointmentStatus === '1'){
                //Get all daily appointments
                $allDailyAppointments = $onlineAppointment->get_executive_online_appointment_daily_count($companyId, $departmentId, $executiveId, $date);
                if(is_array($allDailyAppointments)){
                    if($allDailyAppointments['total'] < $todayAppointmentLimit){
                        return 200;
                    }
                }
            }
        }
    }
}           