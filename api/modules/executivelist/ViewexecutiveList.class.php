
<?php
//SubModule Identity
define('MODULE_EXECUTIVELIST_HANDLER_ID', '10020240227182744');
define('SUB_VIEWEXECUTIVELIST', '10020240227182747');
define('SUB_NAME_VIEWEXECUTIVELIST', 'ViewexecutiveList');
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
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_VIEWEXECUTIVELIST, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all executive members
    public function return_executive_list($companyId, $pager, $filter){
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE `company_id` = $companyId AND `status` = '$filter' ORDER BY `id` DESC LIMIT 15 OFFSET $pager");
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
        $query              = CustomSql::quick_select(" SELECT * FROM `executive_members` WHERE company_id = $companyId AND `id` = $id ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count === 1){
                $data       = [];
                $department = new Viewdepartments();
                $row        = mysqli_fetch_assoc($query);
                $data[]     = [
                    'department_info' => $department->get_departments_details($companyId, $row['department_id']),
                    'staff_info'      => $row
                ];
                return $data;
            }else{
                return 404;
            }
        }
    }
}
            