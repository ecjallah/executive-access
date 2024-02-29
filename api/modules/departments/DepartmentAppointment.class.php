<?php
//SubModule Identity
define('MODULE_DEPARTMENTS_HANDLER_ID', '10020240227160102');
define('SUB_ADDDEPARTMENTS', '10020240227160118');
define('SUB_NAME_ADDDEPARTMENTS', 'Department Appointment');
Auth::module_function_registration(SUB_ADDDEPARTMENTS, SUB_NAME_ADDDEPARTMENTS, MODULE_DEPARTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Departments APPOINTMENT operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class DepartmentAppointment {
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
            $auth              = Auth::function_check(SUB_ADDDEPARTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method assignes staff to a department
    public function assign_staff_department($details){
        $query    = CustomSql::insert_array("department_appointment", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //This method unassignes staff from a department
    public function unassign_staff_from_department($details){
        $companyId      = $details['company_id'];
        $departmentId   = $details['department_id'];
        $staffId        = $details['staff_id'];
        $query          = CustomSql::delete_sql("department_appointment", " `company_id` = $companyId AND `department_id` = $departmentId AND `staff_id` = $staffId ");
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}