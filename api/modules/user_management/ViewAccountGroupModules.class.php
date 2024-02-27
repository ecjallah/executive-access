<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("VIEW_USER_GROUP_MODULES_MODULE_ID", '100010');
define("VIEW_USER_GROUP_MODULES_FUNCTION_ID", '100014');
define("VIEW_USER_GROUP_MODULES_FUNCTION_NAME", 'View User Account Group Modules');
Auth::module_function_registration(VIEW_USER_GROUP_MODULES_FUNCTION_ID, VIEW_USER_GROUP_MODULES_FUNCTION_NAME, VIEW_USER_GROUP_MODULES_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the viewing of user group information. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class ViewAccountGroupModules{
    public $userId;
    public $user_type;
    public $account_character;
    public $method;
    public $url;
    public $permission;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(VIEW_USER_GROUP_MODULES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }
    }

    //This function returns healthcare roles
    public function return_users_roles($healthcareId){
        return Helper::get_all_users_roles($healthcareId);
    }

    //This function returns role details of a role base on roleId
    public function get_role_details($roleId){
        //Check if the healthcare is the owner of the sent role id
        $result          = new Helper();
        $vaildRoles      = Helper::auth_check_user_role($roleId);
        if($vaildRoles !== 200){
            return $vaildRoles;
        }else{
            $query = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE role_id = $roleId ");
            if($query === false){
                return 500;
            }else{
                $count = $query->num_rows;
                if($count == 1){
                    $data = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data['role_details']  =[
                            "role_id"            => $row['role_id'],
                            "role_title"         => $row['role_title'],
                            "date_added"         => date('F j, Y g:i a', strtotime($row['date_added'])),
                            "creator_username"   => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['username']:Helper::lookup_users($row['added_by']),
                            "creator_image"      => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['image']:Helper::lookup_users($row['added_by'])
                        ];
                        
                        //Get already assign modlue to role
                        $assinedModules             = $this->return_all_assign_role_modules($roleId);
                        $data['assigned_modules']   = $assinedModules;
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
    }

    //This function returns all the assigned modules related to a role
    public function return_all_assign_role_modules($roleId){
        $query = CustomSql::quick_select(" SELECT r.*, m.* FROM role_modules r JOIN app_modules m ON m.module_id = r.module_id WHERE r.role_id = $roleId AND m.type != 'generic' ORDER BY m.module_id DESC ");
        if($query === false){
            return 500;
        }else{
           $count = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details  = [
                        "module_id"         => $row['module_id'],
                        "module_title"      => $row['item_title'],
                    ];
                    $data[]   = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function checks if the icomming modules are already assign to the same user group
    public function check_user_group_reassigned_modules($accountGroupId, $moduleId){
        $query     = CustomSql::quick_select(" SELECT * FROM `account_group_module` WHERE module_id = $moduleId AND account_group_id = $accountGroupId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                return 301;
            }else if($count == 0){
                return $moduleId;
            }
        }
    }
}


