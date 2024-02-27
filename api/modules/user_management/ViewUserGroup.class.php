<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("VIEW_USER_GROUP_MODULE_ID", '100010');
define("VIEW_USER_GROUP_FUNCTION_ID", '100012');
define("VIEW_USER_GROUP_FUNCTION_NAME", 'View User Account Group');
Auth::module_function_registration(VIEW_USER_GROUP_FUNCTION_ID, VIEW_USER_GROUP_FUNCTION_NAME, VIEW_USER_GROUP_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class creates new user. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class ViewUserGroup{
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
            $auth             = Auth::function_check(VIEW_USER_GROUP_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission = $auth;
        }
    }

    //This method returns all user group on the app
    public function return_all_user_account_group(){
        $query         = CustomSql::quick_select(" SELECT * FROM `user_account_type` WHERE `status` = 0 AND `id` != 5 ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $createdUser  = Helper::user_details($row['created_by']);
                    $data[] = [
                        "id"            => $row['id'],
                        "account_type"  => $row['account_type'],
                        "title"         => $row['title'],
                        "icon"          => $row['icon'],
                        "color"         => $row['color'],
                        "date_created"  => Helper::get_current_date($row['date_created']),
                        "created_by"    => $createdUser[0]['full_name']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }


    //This method returns particular user type or group by ID
    public function return_account_group_by_id($accountGroupId){
        $query         = CustomSql::quick_select(" SELECT * FROM `user_account_type` WHERE `id` = $accountGroupId ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count == 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $createdUser  = Helper::user_details($row['created_by']);
                    $data = [
                        "id"            => $row['id'],
                        "account_type"  => $row['account_type'],
                        "title"         => $row['title'],
                        "icon"          => $row['icon'],
                        "color"         => $row['color'],
                        "date_created"  => Helper::get_current_date($row['date_created']),
                        // "created_by"    => $createdUser[0]['full_name']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns user account groups details
    public function return_account_group_modules_info($accountGroupId){
        //Get already assign modlue to role
        $moduleManager                = new ModulesManager();
        $data                         = [];
        $data['assigned_modules'][]   = $this->return_all_assign_role_modules($accountGroupId);
        $data['app_modules'][]        = $moduleManager->return_all_app_modules();
        return $data;
    }

    //This method returns all the assigned modules related to a role
    public function return_all_assign_role_modules($accountGroupId){
        $query         = CustomSql::quick_select(" SELECT a.*, m.* FROM account_group_module a JOIN app_modules m ON m.module_id = a.module_id WHERE a.account_group_id = $accountGroupId ORDER BY m.module_id DESC ");
        // AND m.type != 'generic' 
        if($query === false){
            return 500;
        }else{
           $count      = $query->num_rows;
            if($count >= 1){
                $data  = [];
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
   
}