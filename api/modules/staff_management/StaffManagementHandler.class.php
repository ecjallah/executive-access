<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("STAFF_MANAGEMENT_MODULE_ID", '200010');
define("STAFF_MANAGEMENT_MODULE_NAME", 'Staff Management');
Auth::module_registration(STAFF_MANAGEMENT_MODULE_ID, STAFF_MANAGEMENT_MODULE_NAME);
/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the staff management. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class StaffManagementHandler{
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
    private $permission;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(STAFF_MANAGEMENT_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck == 200){
                //CALL FUNCTIONS HERE!
                $this->add_user_role();
                $this->create_new_staff();
                $this->role_details();
                $this->view_staffs();
                $this->edit_staff_details();
                $this->view_staff_details();
                $this->delete_user_account();
                $this->assign_module_rights_to_role();
                $this->view_staff_roles();
                $this->lookup_users();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint assigns modules rights to roles
    public function assign_module_rights_to_role(){
        if($this->url == '/api/assign-module-rights-to-role')
        {
            if($this->method == 'POST'){
                //Get data and clean them if possible
                $_POST      = json_decode(file_get_contents('php://input'), true);
                $role_id    = InputCleaner::sanitize($_POST['role_id']);
                $modules    = InputCleaner::sanitize($_POST['module_list']);
                if(empty($role_id) || empty($modules)){
                    $response = new Response(404, "Please send required data(role_id, modules_array)");
                    $response->send_response();
                }else{
                    CustomSql::commit_off();
                    //Check if the modules already exist
                    $result               = new ViewRoles();
                    $unassignedModules    = [];
                    $assignedModules      = [];
                    foreach ($modules as $moduleId) {
                        $moduleTest    = $result->check_reassigned_modules($role_id, $moduleId);
                        if($moduleTest !== 301){
                            $unassignedModules[] = $moduleTest;
                        }else{
                            $assignedModules[] = $moduleTest;
                        }
                    }

                    if(!empty($assignedModules) && $assignedModules != null){
                        $response = new Response(200, "Some module(s) are already assigned..");
                        $response->send_response();
                    }else{
                        $queryResults      = [];
                        foreach ($unassignedModules as $value) {
                            //Ckeck if the module is assign to this hospital befor assigning the module to a right
                            $approval      = Auth::check_assigned_modules($value, $role_id);
                            if($approval == 404){
                                $response  = new Response(404, "The right you wish to assign to this role is not vaild");
                                $response->send_response();
                            }else if($approval === 200){
                                //Get company id
                                $businessId = Helper::get_business_id($this->userId, $this->account_character);
                                $details    = [
                                    'business_id'     => $businessId,
                                    'role_id'         => $role_id,
                                    'module_id'       => $value,
                                    'assigned_by'     => $this->userId,
                                    'date'            => gmdate('Y-m-d H:i:s'),
                                    'last_updated'    => $this->userId
                                ];
                                $newUserRole    = new AddUserRights();
                                $result         = $newUserRole->assign_modules_to_roles($details);
                                $queryResults[] = $result;
                             
                            }else if($approval === 500){
                                $response = new Response(500, "Error Checking user roles nad modules(rights)");
                                $response->send_response();
                            }
                        }

                        if(in_array(500, $queryResults)){
                            CustomSql::rollback();
                            $response = new Response(500, "Error assigning modules to staff role.", $result);
                            $response->send_response();
                        }else{
                            CustomSql::save();
                            $response = new Response(200, "Modles/rights have been added successfully!", $result);
                            $response->send_response();
                        }
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }
    
    //This endpoint creates new user
    public function create_new_staff(){
        if($this->url == '/api/create-new-staff')
        {
            if($this->method == 'POST'){
                $addUser = new AddStaff();
                if($addUser->permission === 200){
                    //Get data and clean them if possible
                    $_POST          = json_decode(file_get_contents('php://input'), true);
                    $roleId         = InputCleaner::sanitize($_POST['role_id']);
                    $firstName      = InputCleaner::sanitize($_POST['first_name']);
                    $lastName       = InputCleaner::sanitize($_POST['last_name']);
                    $address        = InputCleaner::sanitize($_POST['address']);
                    $sex            = InputCleaner::sanitize($_POST['sex']);
                    $email          = InputCleaner::sanitize($_POST['email']);
                    $county         = InputCleaner::sanitize($_POST['county']);
                    $number         = InputCleaner::sanitize($_POST['number']);
                    $username       = InputCleaner::sanitize($_POST['username']);
                    $status         = InputCleaner::sanitize($_POST['status']);
                    if(empty($email) || empty($roleId) || empty($firstName)|| empty($lastName)|| empty($address)|| empty($sex)|| empty($number) || empty($username) || empty($county)){
                        $response   = new Response(400, "Please provide the following: role_id, first_name, email, last_name, sex, address, number, username");
                        $response->send_response();
                    }else{
                        //Get company id
                        $hashed_password        = password_hash('password@123', PASSWORD_DEFAULT);
                        //Create staff regular account
                        $companyId              = Helper::get_staff_company_id($this->account_character, $this->userId);
                        if($status == 'enumerator'){
                            $roleId            = 10;
                        }

                        $staffDetails           =  [
                            "business_id"       => $companyId,
                            "role_id"           => $roleId,
                            "first_name"        => $firstName,
                            "last_name"         => $lastName,
                            "address"           => $address,
                            "sex"               => $sex,
                            "county"            => $county,
                            "email"             => $email,
                            "number"            => $number,
                            "username"          => $username,
                            "hashed_password"   => $hashed_password,
                            "account_type"      => 5,
                            "added_date"        => gmdate("Y-m-d :H:s:i"),
                            "added_by"          => $this->userId
                        ];

                        $result       = $addUser->create_new_staff_account($staffDetails);
                        if($result === 500){
                            $response = new Response(500, "Error Adding new user");
                            $response->send_response();
                        }else if($result === 404){
                            $response = new Response(404, "This user is aleady a staff.");
                            $response->send_response();
                        }else if($result === 400){
                            $response = new Response(400, "This user can not be added as a staff.");
                            $response->send_response();
                        }else{
                            $response = new Response(200, "New user added Successfully, User may use the default password: password@123");
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

    //This endpoint adds new user role
    public function add_user_role(){
        if($this->url == '/api/add-staff-role')
        {
            if($this->method == 'POST'){
                $_POST            = json_decode(file_get_contents('php://input'), true);
                $newUserRole      = new AddUserRole();
                if($newUserRole->permission === 200){
                    $role_title   = InputCleaner::sanitize($_POST['role_title']);
                    if(empty($role_title)){
                        $response = new Response(301, "Provide: role_title");
                        $response->send_response();
                    }else{
                        //Get company id
                        $companyId  = Helper::get_staff_company_id($this->account_character, $this->userId);
                        $details    = [
                            'company_id'      => $companyId,
                            'role_title'      => $role_title,
                            'date_added'      => gmdate("Y-m-d H:s:i"),
                            'added_by'        => $this->userId
                        ];
                        $result        = $newUserRole->add_user_role($details);
                        if($result === false){
                            $response = new Response(500, "Error adding user role", $result);
                            $response->send_response();
                        }else{
                            $response = new Response(200, "Role added successfully", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            } else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This function returns all users
    public function view_staffs(){
        if($this->url == '/api/view-staffs')
        {
            if($this->method == 'GET'){
                $view_staffs     = new ViewStaffs();
                if($view_staffs->permission === 200){
                    //Get company id
                    $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                    $result       = $view_staffs->return_staffs($businessId);
                    if($result === 500){
                        $response = new Response(500, 'Error(Returning users)');
                        $response->send_response();
                    }else if($result === 404){
                        $response = new Response(404, 'There is no users currently');
                        $response->send_response();
                    }else{
                        $response = new Response(200, 'All users', $result);
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

    //This function returns a user details
    public function view_staff_details(){
        if(strpos($this->url, '/api/view-staff-details') !== false)
        {
            if($this->method == 'GET'){
                $userId           = InputCleaner::sanitize($_GET['user_id']);
                $view_staffs      = new ViewStaffs();
                if($view_staffs->permission === 200){
                    //Get company id
                    $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                    $result       = $view_staffs->return_staff_details($businessId, $userId);
                    if($result === 500){
                        $response = new Response(500, 'Error(Returning user details)');
                        $response->send_response();
                    }else if($result === 404){
                        $response = new Response(404, 'There is no details for the given user.');
                        $response->send_response();
                    }else{
                        $response = new Response(200, 'User details', $result);
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

    //This endpoint edits a user
    public function edit_staff_details(){
        if($this->url == '/api/edit-staff-details')
        {
            if($this->method == 'POST'){
                //Get data and clean them if possible
                $_POST          = json_decode(file_get_contents('php://input'), true);
                $businessId     = Helper::get_business_id($this->userId, $this->account_character);
                $roleId         = InputCleaner::sanitize($_POST['role_id']);
                $staffId        = InputCleaner::sanitize($_POST['user_id']);
                $firstName      = InputCleaner::sanitize($_POST['first_name']);
                $lastName       = InputCleaner::sanitize($_POST['last_name']);
                $address        = InputCleaner::sanitize($_POST['address']);
                $sex            = InputCleaner::sanitize($_POST['sex']);
                $email          = InputCleaner::sanitize($_POST['email']);
                if(empty($staffId) || empty($email) || empty($roleId) || empty($firstName)|| empty($lastName)|| empty($address)|| empty($sex)){
                    $response   = new Response(400, "Please provide the following: role_id, user_id, first_name, email, last_name, sex, address");
                    $response->send_response();
                }else{
                    $details   =  [
                        "first_name"        => $firstName,
                        "last_name"         => $lastName,
                        "full_name"         => $firstName.' '.$lastName,
                        "address"           => $address,
                        "gender"            => $sex,
                        "email"             => $email,
                        "last_updated"      => gmdate("Y-m-d :H:s:i"),
                    ];
                    $update_user_info      = new EditUser();
                    $identity              = ['column' => ['user_id', 'user_type'], 'value' => [$staffId, 5]];
                    $result                = $update_user_info->update_user_info($businessId, $roleId, $staffId, $details, $identity);
                    if($result === 500){
                        $response          = new Response(500, "Error updating staff info");
                        $response->send_response();
                    }else if($result === 400){
                        $response          = new Response(400, "The username already exist..");
                        $response->send_response();
                    }else{
                        $response          = new Response(200, "Staff info updated successfully");
                        $response->send_response();
                    }
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

    //This endpoint returns details of user rolew(sub-modules)
    public function role_details(){
        if(strpos($this->url, '/api/view-role-details') !== false)
        {
            if($this->method == 'GET'){
                $roleId           = InputCleaner::sanitize($_GET['role_id']);
                $roleDetails      = new ViewRoles();
                if($roleDetails->permission === 200){
                    if(empty($roleId)){
                        $response = new Response(404, "Please send required data(role_id)");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        // return_user_type_from_id
                        $result       = $roleDetails->get_role_details($businessId, $roleId, $this->account_character, $this->user_type);
                        if($result == 500){
                            $response = new Response(500, 'Error(Returning role details)');
                            $response->send_response();
                        }else if($result == 404){
                            $response = new Response(404, 'There are no role details currently');
                            $response->send_response();
                        }else if($result == 301){
                            $response = new Response(301, 'Stop playing.., You have been recorded!');
                            $response->send_response();
                        }
                        else{
                            $response = new Response(200, 'Role Details', $result);
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

    //This endpoint returns all created staff roles
    public function view_staff_roles(){
        if($this->url == '/api/view-staff-roles')
        {
            if($this->method == 'GET'){
                $roleDetails      = new ViewRoles();
                if($roleDetails->permission === 200){
                    //Get company id
                    $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                    $result       = $roleDetails->return_all_roles($businessId, $this->account_character);
                    if($result == 500){
                        $response = new Response(500, 'Error(Returning roles)');
                        $response->send_response();
                    }else if($result == 404){
                        $response = new Response(404, 'There are no roles currently.');
                        $response->send_response();
                    }else if($result == 301){
                        $response = new Response(301, 'Stop playing.., You have been recorded!');
                        $response->send_response();
                    }
                    else{
                        $response = new Response(200, 'Created staff roles', $result);
                        $response->send_response();
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

    //Lookup regular user
    public function lookup_users(){
        if($this->url == '/api/lookup-users')
        {
            if($this->method == 'POST'){
                $_POST        = json_decode(file_get_contents('php://input'), true); 
                $searchValue  = InputCleaner::sanitize($_POST['search_value']);
                if(empty($searchValue)){
                    $response = new Response(404, "Please send required data(search_value)");
                    $response->send_response();
                }else{
                    $result    = Helper::lookup_users($searchValue);
                    if($result == 500){
                        $response = new Response(500, 'Error(Returning user)');
                        $response->send_response();
                    }else if($result == 404){
                        $response = new Response(404, 'There are no user matching your search');
                        $response->send_response();
                    }else if($result == 301){
                        $response = new Response(301, 'Stop playing.., You have been recorded!');
                        $response->send_response();
                    }else{
                        $response = new Response(200, 'User Details', $result);
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }
}
(new StaffManagementHandler);