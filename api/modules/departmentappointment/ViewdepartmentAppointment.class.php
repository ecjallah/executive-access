
<?php
//SubModule Identity
define('MODULE_DEPARTMENTAPPOINTMENT_HANDLER_ID', '10020240311162847');
define('SUB_VIEWDEPARTMENTAPPOINTMENT', '10020240311162850');
define('SUB_NAME_VIEWDEPARTMENTAPPOINTMENT', 'ViewdepartmentAppointment');
Auth::module_function_registration(SUB_VIEWDEPARTMENTAPPOINTMENT, SUB_NAME_VIEWDEPARTMENTAPPOINTMENT, MODULE_DEPARTMENTAPPOINTMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages DepartmentAppointment VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-11
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewdepartmentAppointment {
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
            $auth              = Auth::function_check(SUB_VIEWDEPARTMENTAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get from give database
    public function get_data_from_database(){
        $query          = CustomSql::quick_select(" SELECT * FROM `` WHERE ");
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
            