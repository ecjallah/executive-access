<?php
include_once dirname(__FILE__).'/Autoloader.class.php';
/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class manages all the app modules and their functions. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class Auth{
    private $method;
    private $url;
    function __construct(){
        $this->method    = $_SERVER['REQUEST_METHOD'];
        $this->url       = $_SERVER['REQUEST_URI'];
    }

    //This function is the regular login function
    protected function auth_user_login($username, $password){
        $query     = CustomSql::quick_select(" SELECT * FROM `users_security` WHERE `username` = '$username' ");
        if($query === false){
            return 500;
        }else{
            $row   =  mysqli_fetch_assoc($query);
            if(is_array($row)){
                if($username == $row['username'] && password_verify($password, $row['password'])){
                    //Login 
                    if($row['blocked'] == 1){
                        return 309;
                    }else {
                        //GET USER TYPE CHARACTER/TYPE FROM USER TYPE ID
                        $account_character        = new ViewUserGroup();
                        $character                = $account_character->return_account_group_by_id($row['user_type']);
                        if(is_array($character)){
                            $sessionController    = $this->session_power_on($row['user_type'], $row['user_id'], $character['account_type'], $row['default_password_change']);
                            return $sessionController;
                        }else{
                            return 404;
                        }
                    }
                }else if($row['blocked'] == 1){
                    return 309;
                }else{
                    return 404;
                }
            }else {
                return 404;
            }
        }
    }

    //This function start and assign sessions
    protected static function session_power_on($userType, $userId, $accountCharacter, $defaultPasswordStatus = 0){
        $_SESSION['user_id']                    = $userId;
        $_SESSION['user_type']                  = $userType;
        $_SESSION['account_character']          = $accountCharacter;
        $_SESSION['default_password_status']    = $defaultPasswordStatus==null?0:$defaultPasswordStatus;
        session_write_close();
        if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && isset($_SESSION['account_character'])){
            return 200;
        }
    }

    //This function registers newly built modules
    public static function module_registration($moduleId, $moduleName){
        //Check if module was registered
        $checkModuleRegistration    = CustomSql::quick_select("SELECT * FROM app_modules WHERE module_id = $moduleId");
        if($checkModuleRegistration === false){
            return 500;
        }else{
            $count         = $checkModuleRegistration->num_rows;
            if($count == 0){
                // Register module if not registered
                $addModule = CustomSql::insert_raw(" INSERT INTO app_modules(module_id, module_title, module_type) VALUES('$moduleId', '$moduleName', 'system') ");
                if($addModule === false){
                    return 500;
                }else{
                    return $addModule;
                }
            }
        }
    }

    //This function registers newly built modules functions in the database
    public static function module_function_registration($functionId, $functionName, $moduleId){
        //Check if module was registered
        $checkModuleRegistration  = CustomSql::quick_select(" SELECT * FROM module_function WHERE function_id = $functionId ");
        if($checkModuleRegistration === false){
            return false;
        }else{
            $count                = $checkModuleRegistration->num_rows;
            if($count == 0){
                // Register module if not register
                $addModule        = CustomSql::insert_raw(" INSERT INTO module_function(module_id, function_id, function_title) VALUES('$moduleId', '$functionId', '$functionName') ");
                if($addModule === false){
                    return false;
                }else{
                    return $addModule;
                }
            }
        }
    }

    //This function checks if a user has right to a particular module
    public static function module_security($moduleId, $userId, $userType, $accountType){
        //Check the user type
        if($userType == 1){
            return 200;
        }if($accountType == 'ministry' || $accountType == 'business' || $accountType == 'regular_user'){
            $query         = CustomSql::quick_select(" SELECT g.*, m.* FROM `app_modules` m JOIN `account_group_module` g ON g.module_id = m.module_id WHERE account_group_id = $userType AND g.module_id = $moduleId ");
            if($query == false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count == 1){
                    return 200;
                }else{
                    return 301;
                }
            }
        }else{
            //Get the current user role
            $userRoleId    = Helper::get_user_assigned_role($userId);
            $query         = CustomSql::quick_select(" SELECT r.*, m.* FROM `role_modules` r JOIN `app_modules` m ON r.module_id = m.module_id  WHERE r.module_id = $moduleId AND r.role_id = $userRoleId ");
            if($query === false){
                $response  = new Response(500, "Registering Module sql error");
                $response->send_response();
            }else{
                $count     = $query->num_rows;
                if($count == 1){
                  return 200;
                }else{
                 return 301;
                }
            }
        }
    }

    //This function checks if a username is in use by another user
    public function username_check($userName){
        $query    = CustomSql::quick_select(" SELECT * FROM users_security WHERE `username` = '$userName' ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                return 404;
            }else{
                return 200;
            }
        }
    }

    //This function verifys verification codes
    public function verify_verification_code($code, $userNumber){
        $query    = CustomSql::quick_select( "SELECT * FROM user_verification_code WHERE `number` = $userNumber AND `status` = '0' ORDER BY `date` DESC LIMIT 1 " );
        if($query === false){
            return false;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $systemCode = $query->fetch_assoc()['code'];
                if($code == $systemCode){
                    //UPDATE CODE STATUS
                    $updateQuery = CustomSql::update_sql("UPDATE user_verification_code SET `status` = 1 WHERE `number` = $userNumber ");
                    if($updateQuery === false){
                        return 500;
                    }else{
                        return 200;
                    }
                }else{
                    return 404;
                }
            }else{
                return 404;
            }
        }
    }

    //This function ckecks if a business has a particular module assigned to them
    public static function check_assigned_modules($moduleId, $role_id){
        $query        = CustomSql::quick_select(" SELECT * FROM `app_modules` WHERE `module_id` = $moduleId ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                //Check if this usre role belones to this business
                $role = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE `role_id` = '$role_id' ");
                if($role === false){
                    return 500;
                }else{
                    $count = $role->num_rows;
                    if($count == 1){
                        return 200;
                    }else{
                        return 404;
                    }
                }
            }else{
                return 404;
            }
        }
    }

    //This function check user rights on a function level
    public static function function_check($functionId, $userId, $userType, $accountCharacter){
        if($accountCharacter == 'staff'){
            //Get user/staff ID
            $staffId      = Helper::get_user_staff_id($userType, $userId);
            //Get staff company id
            $companyId    = Helper::get_staff_company_id($userType, $userId);

            //Check the user role from health_staff table
            $query        = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `staff_id` = $staffId AND `business_id` = $companyId ");
            if($query === false){
                return 500;
            }else{
                $count               = $query->num_rows;
                $userRole            = 0;
                if($count === 1){
                    $userRole        = $query->fetch_assoc()['role_id'];
                    //Check the health_staff right table
                    $rights_query    = CustomSql::quick_select(" SELECT * FROM staff_right WHERE `role_id` = $userRole AND `business_id` = $companyId AND function_id = $functionId ");
                    if($rights_query === false){
                        return 500;
                    }else{
                        $rightCount  = $query->num_rows;
                        if($rightCount === 1){
                            $rightStatus = mysqli_fetch_assoc($rights_query);
                            if(is_array($rightStatus)){
                                if($rightStatus['status'] == 'off'){
                                    return 305;
                                }else{
                                    return 200;
                                }
                            }
                        }else{
                            return 305;
                        }
                    }
                }else{
                    return 305;
                }
            }
        }else{
            return 200;
        }
    }

    //THIS METHOD RETURNS MODULE ID FROM SUB MODULER ID
    public static function get_module_id_from_sub_module_id($moduleFunction){
        //Check if this usre role belones to this hospital
        $query     = CustomSql::quick_select(" SELECT * FROM `module_function` WHERE `function_id` = $moduleFunction ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                return ['status' => 200, 'data' => $query->fetch_assoc()];
            }else{
                return ['status' => 404];
            }
        }
    }

    //This function checks if a health care has a particular function and role assign to them(TRYING THE ASSIGN A RIGH TO A ROLE)
    public static function check_health_care_assign_function($functionId, $health_care_id, $role_id){        
        $query = CustomSql::quick_select(" SELECT f.*, m.* FROM module_function f JOIN hospital_module m ON m.module_id = f.module_id WHERE m.health_care_id = $health_care_id AND f.function_id = $functionId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                //Check if this usre role belones to this hospital
                $role = CustomSql::quick_select(" SELECT * FROM staff_role WHERE health_care_id = $health_care_id AND role_id = $role_id ");
                if($role === false){
                    return 500;
                }else{
                    $count = $role->num_rows;
                    if($count === 1){
                        return 200;
                    }else{
                        return 404;
                    }
                }
            }else{
                return 404;
            }
        }
    }

    //This function ckecks if a healthcare has a particular module assigned to them
    public static function check_healthcare_assigned_modules($moduleId, $health_care_id, $role_id){
        $query = CustomSql::quick_select(" SELECT * FROM `hospital_module` WHERE `health_care_id` = $health_care_id AND `module_id` = $moduleId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                //Check if this usre role belones to this hospital
                $role = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE `health_care_id` = $health_care_id AND `role_id` = '$role_id' ");
                if($role === false){
                    return 500;
                }else{
                    $count = $role->num_rows;
                    if($count == 1){
                        return 200;
                    }else{
                        return 404;
                    }
                }
            }else{
                return 404;
            }
        }

    }
}
