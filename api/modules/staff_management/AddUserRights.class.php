<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("ADD_USER_RIGHT_MODULE_ID", '200010');
define("ADD_USER_RIGHT_FUNCTION_ID", '200024');
define("ADD_USER_RIGHT_FUNCTION_NAME", 'Assign Module to Role');
Auth::module_function_registration(ADD_USER_RIGHT_FUNCTION_ID, ADD_USER_RIGHT_FUNCTION_NAME, ADD_USER_RIGHT_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class assigns rights to role(s). 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class AddUserRights{
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
            $auth              = Auth::function_check(ADD_USER_RIGHT_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }        
    }

    //This function assigns modules to roles
    public function assign_modules_to_roles($details){
        CustomSql::commit_off();
        $query                             = CustomSql::insert_array('role_modules', $details);
        if($query === false){
            return 500;
        }else{
            $modleFunctions                = $this->return_all_module_functions($details['module_id']);
            if(is_array($modleFunctions)){
                $moduleFunctionStatus      = [];
                foreach ($modleFunctions as $mFunction) {
                    $details = [
                        'business_id'     => $details['business_id'],
                        'role_id'         => $details['role_id'],
                        'module_id'       => $details['module_id'],
                        'function_id'     => $mFunction,
                        'added_by'        => $this->userId,
                        'date_added'      => gmdate('Y-m-d H:i:s')
                    ];
                    $moduleFunctionStatus[]  = $this->assign_user_rights_functions($details);
                }
                
                if(!in_array(500, $moduleFunctionStatus)){
                    CustomSql::save();
                    return 200;
                }else{
                    CustomSql::rollback();
                    return 500;
                }
            }
        }
    }
    
    //This function assign the user functions or rights base on the assign modules
    public function assign_user_rights_functions($details){
        $query    = CustomSql::insert_array('staff_right', $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //This function returns alll module functions relating to a module
    public function return_all_module_functions($modules_id){
        $query    = CustomSql::quick_select("SELECT * FROM `module_function` WHERE `module_id` = $modules_id ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row['function_id'];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

}


