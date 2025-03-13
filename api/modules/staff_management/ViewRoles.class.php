<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("VIEW_USER_ROLE_MODULE_ID", '200010');
define("VIEW_USER_ROLE_FUNCTION_ID", '200022');
define("VIEW_USER_ROLE_FUNCTION_NAME", 'View Staff Role');
Auth::module_function_registration(VIEW_USER_ROLE_FUNCTION_ID, VIEW_USER_ROLE_FUNCTION_NAME, VIEW_USER_ROLE_MODULE_ID);

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

 class ViewRoles{
    public $userId;
    public $user_type; 
    public $account_character;
    public $permission;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;
            
            //Check if user has right to access this class(this module function)
            $auth                      = Auth::function_check(VIEW_USER_ROLE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission          =  $auth;
        }
    }

    //This function returns role details of a role base on roleId
    public function get_role_details($businessId, $roleId, $accountType, $accountTypeId){
        if($accountType == 'ninja'){
               //Check if the healthcare is the owner of the sent role id
               $result          = new Helper();
               $vaildRoles      = Helper::auth_check_user_role($roleId);
               if($vaildRoles !== 200){
                   return $vaildRoles;
               }else{
                   $query     = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE company_id = $businessId AND role_id = $roleId ");
                   if($query === false){
                       return 500;
                   }else{
                       $count = $query->num_rows;
                       if($count == 1){
                           $moduleManager   = new ModulesManager();
                           $data            = [];
                           while ($row = mysqli_fetch_assoc($query)) {
                               $data['role_details']  = [
                                   "role_id"            => $row['role_id'],
                                   "role_title"         => $row['role_title'],
                                   "date_added"         => date('F j, Y g:i a', strtotime($row['date_added'])),
       
                                   "creator_username"   => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['full_name']:Helper::lookup_users($row['added_by']),
                                   "creator_image"      => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['image']:Helper::lookup_users($row['added_by'])
                               ];
                               
                               //Get already assign modlue to role
                               $assinedModules             = $this->return_all_assign_role_modules($businessId, $roleId);
                               $data['assigned_modules']   = $assinedModules;
                               $data['app_modules'][]      = $moduleManager->return_all_app_modules();
                           }
                           return $data;
                       }else{
                           return 404;
                       }
                   }
               }
        }
        if($accountType == 'business' || $accountType == 'ministry'){
            //Check if the healthcare is the owner of the sent role id
            $result          = new Helper();
            $vaildRoles      = Helper::auth_check_user_role($roleId);
            if($vaildRoles !== 200){
                return $vaildRoles;
            }else{
                $query       = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE company_id = '$businessId' AND role_id = '$roleId' ");
                if($query === false){
                    return 500;
                }else{
                    $count   = $query->num_rows;
                    if($count == 1){
                        $moduleManager   = new ModulesManager();
                        $data            = [];
                        while ($row = mysqli_fetch_assoc($query)) {
                            $data['role_details']  = [
                                "role_id"            => $row['role_id'],
                                "role_title"         => $row['role_title'],
                                "date_added"         => date('F j, Y g:i a', strtotime($row['date_added'])),
    
                                "creator_username"   => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['full_name']:Helper::lookup_users($row['added_by']),
                                "creator_image"      => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['image']:Helper::lookup_users($row['added_by'])
                            ];
                            
                            //Get already assign modlue to role
                            $assinedModules             = $this->return_all_assign_role_modules($businessId, $roleId);
                            $data['assigned_modules']   = $assinedModules;
                            $data['app_modules'][]      = $moduleManager->return_business_assigned_modules($accountTypeId);
                        }
                        return $data;
                    }else{
                        return 404;
                    }
                }
            }
        }else if($accountType == 'staff'){
            $query       = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE `company_id` = '$businessId' AND `role_id` = '$roleId' ");
            if($query === false){
                return 500;
            }else{
                $count   = $query->num_rows;
                if($count == 1){
                    $moduleManager   = new ModulesManager();
                    $data            = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data['role_details']  = [
                            "role_id"            => $row['role_id'],
                            "role_title"         => $row['role_title'],
                            "date_added"         => date('F j, Y g:i a', strtotime($row['date_added'])),
                            "creator_username"   => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['full_name']:Helper::lookup_users($row['added_by']),
                            "creator_image"      => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['image']:Helper::lookup_users($row['added_by'])
                        ];
                        
                        //Get already assign modlue to role
                        $assinedModules             = $this->return_all_assign_role_modules($businessId, $roleId);
                        $data['assigned_modules']   = $assinedModules;
                        $data['app_modules'][]      = $moduleManager->return_staff_assigned_modules($businessId);
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
        else{
            return 301;
        }
    }

    //This function retuns all staff created roles
    public function return_all_roles($businessId, $accountCharacter = null){
        $query         = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE `company_id` = '$businessId' ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data['role_details'][]    = [
                        "role_id"            => $row['role_id'],
                        "role_title"         => $row['role_title'],
                        "date_added"         => date('F j, Y g:i a', strtotime($row['date_added'])),

                        "creator_username"   => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['full_name']:Helper::lookup_users($row['added_by']),
                        "creator_image"      => is_array(Helper::lookup_users($row['added_by']))?Helper::lookup_users($row['added_by'])[0]['image']:Helper::lookup_users($row['added_by'])
                    ];
                    
                    //Get already assign modlue to role
                    // $assinedModules             = $this->return_all_assign_role_modules($$row['role_id']);
                    // $data['assigned_modules']   = $assinedModules;

                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function returns all the assigned modules related to a role
    public function return_all_assign_role_modules($businessId, $roleId){
        $query    = CustomSql::quick_select(" SELECT r.*, m.* FROM role_modules r JOIN app_modules m ON m.module_id = r.module_id WHERE r.business_id = '$businessId' AND r.role_id = '$roleId' ORDER BY m.module_id DESC ");
        // AND m.type != 'generic' 
        if($query === false){
            return 500;
        }else{
           $count     = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details  = [
                        "module_id"     => $row['module_id'],
                        "module_title"  => $row['item_title'],
                    ];
                    $data[]   = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function checks if the icomming modules are already assign to the same role
    public function check_reassigned_modules($roleId, $moduleId){
        $query     = CustomSql::quick_select(" SELECT * FROM `role_modules` WHERE module_id = '$moduleId' AND role_id = $roleId ");
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


