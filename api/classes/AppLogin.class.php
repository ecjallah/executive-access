<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles the login of every user on the app. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class AppLogin extends Auth{
    private $method;
    private $url;
    function __construct()
    {
        $this->method    = $_SERVER['REQUEST_METHOD'];
        $this->url       = $_SERVER['REQUEST_URI'];
        $this->login_user_items();
        $this->all_users_login();
    }

    //DIRECT LOGIN
    private function all_users_login(){
        if($this->url == '/api/app-login'){
            if($this->method !== "POST")
            {
                $response = new Response(301, "This endpoint accepts the POST method");
                $response->send_response();
            }else{
                $_POST           = json_decode(file_get_contents('php://input'), true);
                if(!isset($_POST['username']) || !isset($_POST['password'])){
                    $response = new Response(301, "Expected data not sent(username/usernumber, password)");
                    $response->send_response();
                }else{
                    $number      = InputCleaner::sanitize($_POST['username']);
                    $password    = InputCleaner::sanitize($_POST['password']);
                    $app         = key_exists('app', $_POST) ? InputCleaner::sanitize($_POST['app']) : false;
                    $login       = $this->auth_user_login($number, $password);
                    if($login === 200){
                        //Get user assigned modules icons
                        $userId             = $_SESSION['user_id'];
                        $returnDetails      = null;
                            $getSessions    = new ModulesManager();
                            $sessions       = $getSessions->return_active_user_session();
                            $roleDetails    = $this->side_bar_items(is_array($sessions)?$sessions['userType']:$sessions, is_array($sessions)?$sessions['userId']:$sessions, $sessions['account_character']);
                            $roleDetails['side_bar_items'] = "N/A";
                            $returnDetails = [
                                'user_id'                   => $userId, 
                                'hashed_credentials'        => base64_encode("$number<>$password"),
                                'role_details'              => $roleDetails
                            ];
                            if($_SESSION['user_type'] == 5){
                                $returnDetails['default_password_status'] = $_SESSION['default_password_status'];
                            }
                        $response = new Response(200, "Login Successful", $returnDetails);
                        $response->send_response();
                    }else if($login == 404){
                        $response = new Response(404, "Incorrect login credentials");
                        $response->send_response();
                    }else if($login === 500){
                        $response = new Response(500, "Database Error(Login user)");
                        $response->send_response();
                    }else if(is_array($login)){
                        $response = new Response(201, "Please select account to login as", $login);
                        $response->send_response();
                    }else if($login == 309){
                        $response = new Response(309, "Your account has been blocked.");
                        $response->send_response();
                    }else{
                        $response = new Response(404, "Incorrect login credentialssss");
                        $response->send_response();
                    }
                }
            }
        }

        if($this->url == '/api/renew-app-session'){
            if($this->method !== "POST")
            {
                $response = new Response(301, "This endpoint accepts the POST method");
                $response->send_response();
            }else{
                $_POST           = json_decode(file_get_contents('php://input'), true);
                if(!isset($_POST['credentials'])){
                    $response = new Response(301, "Expected data not sent(credentials)");
                    $response->send_response();
                }else{
                    $credentials     = base64_decode(InputCleaner::sanitize($_POST['credentais']));
                    $credentialsData = explode("<>", $credentials);
                    $number          = $credentialsData[0];
                    $password        = $credentialsData[1];
                    $login           = $this->auth_user_login($number, $password);
                    if($login === 200){
                        //Get user assigned modules icons
                        $response = new Response(200, "Login Successful");
                        $response->send_response();
                    }else if($login == 404){
                        $response = new Response(404, "Incorrect login credentials");
                        $response->send_response();
                    }else if($login === 500){
                        $response = new Response(500, "Database Error(Login user)");
                        $response->send_response();
                    }else if(is_array($login)){
                        $response = new Response(201, "Please select account to login as", $login);
                        $response->send_response();
                    }else if($login == 309){
                        $response = new Response(309, "Your account has been blocked.");
                        $response->send_response();
                    }else{
                        $response = new Response(404, "Incorrect login credentials");
                        $response->send_response();
                    }
                }
            }
        }
    }

    //LOGIN USER SIDEBAR ITEMS
    private function login_user_items(){
        if($this->url == '/api/user-sidebar-items'){
            if($this->method !== "GET")
            {
                $response          = new Response(301, "This endpoint accepts the GET method");
                $response->send_response();
            }else{
                $getSessions       = new ModulesManager();
                $sessions          = $getSessions->return_active_user_session();
                if($sessions != 301){
                    $result        = $this->side_bar_items(is_array($sessions)?$sessions['userType']:$sessions, is_array($sessions)?$sessions['userId']:$sessions, $sessions['account_character']);
                    if($result === 404){
                        $response  = new Response(404, "No sidebar items");
                        $response->send_response();
                    }else if($result === 500){
                        $response  = new Response(500, "Error(Returning sidebar items)");
                        $response->send_response();
                    }else{
                        $response  = new Response(200, "User items success", $result);
                        $response->send_response();
                    }
                }else{
                    Logout::log_user_out();
                }
            }
        }
    }

    //FETCH LOGIN USER SIDEBAR ITEMS
    private function side_bar_items($userType, $userId, $userAccountType){
        if($userType == 1){
            $query         = CustomSql::quick_select(" SELECT * FROM `app_modules` ");
            if($query == false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        //get the user image and name
                        $userDetails  =  is_array(Helper::user_details($userId))?Helper::user_details($userId)[0]:Helper::user_details($userId);
                        $details      = [
                            'title' => $row['item_title'],
                            'icon'  => $row['icon'],
                            'type'  => $row['type'],
                            'link'  => $row['link']
                        ];
                        $data['side_bar_items'][] = $details;
                        $data['username']         = $userDetails['username'];
                        $data['full_image']       = $userDetails['image'];
                        $data['user_type_id']     = $userDetails['user_type'];
                        $data['user_type']        = Helper::return_user_type_from_id($userId);
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }else if($userAccountType == 'ministry' || $userAccountType == 'business' || $userAccountType == 'regular_user'){
            //Check if user is blocked or their account is pendong approval
            $accountBlockedChecker  = Helper::check_account_block_status($userId);
            if($accountBlockedChecker == 200){
                //Check account approval status
                $approvalStatus     = Helper::check_account_approval_status($userId);
                if($approvalStatus == 200){
                    $query              = CustomSql::quick_select(" SELECT g.*, m.* FROM `app_modules` m JOIN `account_group_module` g ON g.module_id = m.module_id WHERE account_group_id = $userType ");
                    if($query == false){
                        return 500;
                    }else{
                        $count     = $query->num_rows;
                        if($count >= 1){
                            $data  = [];
                            while ($row = mysqli_fetch_assoc($query)) {
                                //get the user image and name
                                $userDetails  =  is_array(Helper::user_details($userId))?Helper::user_details($userId)[0]:Helper::user_details($userId);
                                $details      = [
                                    'title' => $row['item_title'],
                                    'icon'  => $row['icon'],
                                    'type'  => $row['type'],
                                    'link'  => $row['link']
                                ];
                                $data['side_bar_items'][] = $details;
                                $data['username']         = $userDetails['username'];
                                $data['full_image']       = $userDetails['image'];
                                $data['user_type_id']     = $userDetails['user_type'];
                                $data['user_type']        = Helper::return_user_type_from_id($userId);
                            }
                            return $data;
                        }else{
                            return 404;
                        }
                    }
                }else{
                    $response = new Response(309, "Your account has not been approved at this time.");
                    $response->send_response();
                }
            }else{
                $response = new Response(309, "Your account has been blocked.");
                $response->send_response();
            }
        }else{
            //Get user assigned role
            $userAssignedRole  = Helper::get_user_assigned_role($userId);
            $query             = CustomSql::quick_select(" SELECT r.*, m.* FROM `role_modules` r JOIN `app_modules` m ON r.module_id = m.module_id WHERE r.role_id = $userAssignedRole AND m.type != 'generic'  ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        //get the user details
                        $userDetails    =  is_array(Helper::user_details($userId))?Helper::user_details($userId)[0]:Helper::user_details($userId);
                        $details     = [
                            'title' => $row['item_title'],
                            'icon'  => $row['icon'],
                            'type'  => $row['type'],
                            'link'  => $row['link']
                        ];
                        $data['side_bar_items'][]    = $details;
                        $data['username']            = $userDetails['username'];
                        $data['full_image']          = $userDetails['image'];
                        $data['user_type_id']        = $userDetails['user_type'];
                        $data['user_type']           = Helper::return_user_type_from_id($userId);
                    }
                    if($_SESSION['user_type'] == 5){
                        $data['default_password_status'] = $_SESSION['default_password_status'];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
    }
}
(new AppLogin);