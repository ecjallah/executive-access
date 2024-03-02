
<?php
//SubModule Identity
define('MODULE_DEPARTMENTS_HANDLER_ID', '10020240227160102');
define('SUB_VIEWDEPARTMENTS', '10020240227160105');
define('SUB_NAME_VIEWDEPARTMENTS', 'Viewdepartments');
Auth::module_function_registration(SUB_VIEWDEPARTMENTS, SUB_NAME_VIEWDEPARTMENTS, MODULE_DEPARTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Departments VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Viewdepartments {
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
            $auth              = Auth::function_check(SUB_VIEWDEPARTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get departments with pagination
    public function get_all_departments($companyId, $pager){
        $query              = CustomSql::quick_select(" SELECT * FROM `departments` WHERE `company_id` = $companyId AND `delete` = 0 ORDER BY `date_added` DESC LIMIT 15 OFFSET $pager ");
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

    //This method get departments without pagination
    public function get_departments_list($companyId){
        $query              = CustomSql::quick_select(" SELECT * FROM `departments` WHERE `company_id` = $companyId AND `delete` = 0 ORDER BY `date_added` DESC ");
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


    //This method get department details by id
    public function get_departments_details($companyId, $id){
        $query          = CustomSql::quick_select(" SELECT * FROM `departments` WHERE `company_id` = $companyId AND `id` = $id AND `delete` = 0 ");
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

    //This method get department staff
    public function get_department_staff($companyId, $departmentId){
        $query          = CustomSql::quick_select(" SELECT * FROM `department_staff` WHERE `company_id` = $companyId AND `department_id` = $departmentId ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $data   = [];
                $staffDetails = new ViewStaffs();
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = $staffDetails->return_staff_details($companyId, $row['staff_id'])[0];
                    $data[]  = [
                        "user_id"   => $details['user_id'],
                        "full_name" => $details['full_name'],
                        "image"     => $details['image'],
                        "number"    => $details['number']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}
            