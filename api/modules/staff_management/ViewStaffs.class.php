<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("VIEW_STAFFS_MODULE_ID", '200010');
define("VIEW_STAFFS_FUNCTION_ID", '200021');
define("VIEW_STAFFS_FUNCTION_NAME", 'View Staffs');
Auth::module_function_registration(VIEW_STAFFS_FUNCTION_ID, VIEW_STAFFS_FUNCTION_NAME, VIEW_STAFFS_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the viewing of staff information. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */
 class ViewStaffs{
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
            $auth              = Auth::function_check(VIEW_STAFFS_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }        
    }

    //This function gets all the staff of a given healthcare
    public function return_staffs($businessId){
        $query     = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `business_id` = $businessId AND `block` = 0 ORDER BY `id` DESC ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row  = mysqli_fetch_assoc($query)) {
                    $userDetails    = Helper::user_details($row['staff_personal_id'])[0];
                    $roleTitle      = Helper::get_user_role_title($row['role_id'])['role_title'];
                    $details        = [
                        "user_id"             => $userDetails['user_id'],
                        "full_name"           => $userDetails['full_name'],
                        "image"               => $userDetails['image'],
                        "number"              => $userDetails['number'],
                        "username"            => $userDetails['username'],
                        "user_role_id"        => $row['role_id'],
                        "user_role_title"     => $roleTitle
                    ];
                    $data[]         = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function gets a given staff details
    public function return_staff_details($businessId, $userId){
        $query         = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `business_id` = $businessId AND `staff_id` = $userId AND `block` = 0 OR `business_id` = $businessId AND `staff_personal_id` = $userId AND `block` = 0 ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count == 1){
                $data  = [];
                while ($row  = mysqli_fetch_assoc($query)) {
                    $roleTitle      = '';
                    $userDetails    = Helper::user_details($row['staff_personal_id'])[0];
                    $role           = Helper::get_user_role_title($row['role_id']);
                    if(!empty($role)){
                        $roleTitle  = $role['role_title'];
                    }
                    $details        = [
                        "user_id"             => $userDetails['user_id'],
                        "full_name"           => $userDetails['full_name'],
                        "image"               => $userDetails['image'],
                        "number"              => $userDetails['number'],
                        "username"            => $userDetails['username'],
                        "email"               => $userDetails['email'],
                        "gender"              => $userDetails['gender'],
                        "user_role_id"        => $row['role_id'],
                        "user_role_title"     => $roleTitle
                    ];
                    $data[]         = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

}
