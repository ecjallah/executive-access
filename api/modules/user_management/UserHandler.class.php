<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("USERHANDLER_MODULE_ID", '100010');
define("USERHANDLER_MODULE_NAME", 'User Management');
Auth::module_registration(USERHANDLER_MODULE_ID, USERHANDLER_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles the user module. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class UserHandler{
    public $userId;
    public $user_type;
    public $account_character;
    public $method;
    public $url;

    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(USERHANDLER_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck == 200){
                //CALL FUNCTIONS HERE!
                $this->create_new_user_account_group();
                $this->get_user_account_groups();
                $this->get_user_account_group_details();
                $this->assign_module_to_account_group();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint creates new USER ACCOUNT GROUP
    public function create_new_user_account_group(){
        if($this->url == '/api/create-user-account-group')
        {
            if($this->method == 'POST'){
                //Get data and clean them if possible
                $_POST              = json_decode(file_get_contents('php://input'), true);
                $addUserGroup       = new AddUserGroup();
                //permission 
                if($addUserGroup->permission === 200){
                    $account_type   = InputCleaner::sanitize($_POST['account_type']);
                    $title          = InputCleaner::sanitize($_POST['title']);
                    $icon           = InputCleaner::sanitize($_POST['icon']);
                    $color          = InputCleaner::sanitize($_POST['color']);
                    if(empty($account_type) || empty($title) || empty($icon) || empty($color)){
                        $response = new Response(400, "Please provide the following: account_type, title, icon, color");
                        $response->send_response();
                    }else{
                        $details  =  [
                            "account_type"    => $account_type,
                            "title"           => $title,
                            "icon"            => $icon,
                            "color"           => $color,
                            "date_created"    => Helper::get_current_date(),
                            "created_by"      => $this->userId
                        ];

                        $result        = $addUserGroup->create_new_user_group($details);
                        if($result === 500){
                            $response  = new Response(500, "Error creating new user account group.");
                            $response->send_response();
                        }else{
                            $response  = new Response(200, "New user group created successfully.");
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This endpoint returns a user group account details
    public function get_user_account_group_details(){
        if(strpos($this->url, '/api/view-user-account-group-modules') !== false)
        {
            if($this->method == 'GET'){
                $accountGroupId             = InputCleaner::sanitize($_GET['id']);
                $accountGroupsInfo          = new ViewUserGroup();
                if($accountGroupsInfo->permission === 200){
                    $result                 = $accountGroupsInfo->return_account_group_modules_info($accountGroupId);
                    if($result === 500){
                        $response           = new Response(500, "Error returning roles", $result);
                        $response->send_response();
                    }else if($result === 404){
                        $response           = new Response(404, "There is no role. Please create role(s)", $result);
                        $response->send_response();
                    }else{
                        $response           = new Response(200, "Roles", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint assigns modules rights to USER ACCOUNT GROUP
    public function assign_module_to_account_group(){
        if($this->url == '/api/assign-modules-to-account-group')
        {
            if($this->method == 'POST'){
                //Get data and clean them if possible
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $newUserRole           = new ViewAccountGroupModules();
                $assignModuleToGroup   = new AssignAccountGroupModules();
                //permission 
                if($newUserRole->permission === 200){
                    $accountGroupId    = InputCleaner::sanitize($_POST['account_group_id']);
                    $modules           = InputCleaner::sanitize($_POST['module_list']);
                    if(empty($accountGroupId) || empty($modules)){
                        $response = new Response(404, "Please send required data(account_group_id, modules_array)");
                        $response->send_response();
                    }else{
                        //Check if the modules already exist
                        $unassignedModules    = [];
                        $assignedModules      = [];
                        foreach ($modules as $moduleId) {
                            $moduleTest    = $newUserRole->check_user_group_reassigned_modules($accountGroupId, $moduleId);
                            if($moduleTest !== 301){
                                $unassignedModules[] = $moduleTest;
                            }else{
                                $assignedModules[]   = $moduleTest;
                            }
                        }
                        if(!empty($assignedModules) && $assignedModules != null){
                            $response = new Response(200, "Some module(s) are already assigned..");
                            $response->send_response();
                        }else{
                            foreach ($unassignedModules as $value) {
                                $details = [
                                    'account_group_id'   => $accountGroupId,
                                    'module_id'          => $value,
                                    'assigned_by'        => $this->userId,
                                    'date'               => gmdate('Y-m-d H:i:s'),
                                    'last_updated'       => $this->userId
                                ];
                                $result         = $assignModuleToGroup->assign_modules_to_account_group($details);
                                if($result === 500){
                                    $response = new Response(500, "Error Adding User Role", $result);
                                    $response->send_response();
                                }else{
                                    $response = new Response(200, "Modules have been added successfully!", $result);
                                    $response->send_response();
                                }
                            }
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint deletes a user account
    public function delete_user_account(){
        if($this->url == '/api/delete-user-account')
        {
            if($this->method == 'POST'){
                //Get data and clean them if possible
                $_POST         = json_decode(file_get_contents('php://input'), true);
                $userId        = InputCleaner::sanitize($_POST['user_id']);
                if(empty($userId)){
                    $response = new Response(400, "Please provide the following: user_id");
                    $response->send_response();
                }else{
                    $delete_user_info      = new DeleteUserAccounts();
                    $result      = $delete_user_info->delete_user_accounts($userId);
                    if($result === 500){
                        $response = new Response(500, "Error deleteing user account");
                        $response->send_response();
                    }else{
                        $response = new Response(200, "User account deleted successfully", $result);
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This endpoint returns all the roles realted to a healthcare
    public function get_user_account_groups(){
        if($this->url == '/api/view-all-account-groups')
        {
            if($this->method == 'GET'){
                $userGroups       = new ViewUserGroup();
                if($userGroups->permission === 200){
                    $result       = $userGroups->return_all_user_account_group();
                    if($result === 500){
                        $response = new Response(500, "Error returning user account groups.", $result);
                        $response->send_response();
                    }else if($result === 404){
                        $response = new Response(404, "There is no user account group currently.", $result);
                        $response->send_response();
                    }else{
                        $response = new Response(200, "Created user group.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

}

(new UserHandler);

