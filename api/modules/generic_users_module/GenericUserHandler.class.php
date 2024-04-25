<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("USERHANDLER_MODULE_ID", '100050');
define("USERHANDLER_MODULE_NAME", 'Generic User Module');
Auth::module_registration(USERHANDLER_MODULE_ID, USERHANDLER_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the generic user profile modu;e. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class GenericUserHandler{
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId      = $_SESSION['user_id'];
            $this->user_type   = $_SESSION['user_type'];

            $this->method      = $_SERVER['REQUEST_METHOD'];
            $this->url         = $_SERVER['REQUEST_URI'];
            $this->get_login_user_details();
            $this->get_user_security();
            $this->return_all_user_accounts();
            $this->switch_login_user_accounts();
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint returns regualr Information reating to the login user
    protected function get_login_user_details(){
        if($this->url == '/api/get-user-personal-details')
        {
            if($this->method == 'GET'){    
                $userInfo  = new ViewUserProfile();
                $result    = $userInfo->get_this_user_profile($this->user_type, $this->userId);
                if($result == 500){
                    $response = new Response(500, "Contact Admin(View User Profile)");
                    $response->send_response();

                }else if($result == 404){
                    $response    = new Response(404, "Could not find the user.");
                    $response->send_response();

                }else if($result == 301){
                    $response    = new Response(301, "Sorry, You do do have right to access this function");
                    $response->send_response();
                }else{
                    $response = new Response(200, "User basic Information", $result);
                    $response->send_response();
                }
            } else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns user security information
    protected function get_user_security(){
        if($this->url == '/api/get-user-security-details')
        {
            if($this->method == "POST"){
                $_GET      = json_decode(file_get_contents('php://input'), true);
                $password  = InputCleaner::sanitize($_GET['password']);
    
                $userInfo  = new ViewUserProfile();
                $result    = $userInfo->get_user_security_information($password, $this->userId);
                if($result == 500){
                    $response = new Response(500, "Contact Admin(View User Profile)");
                    $response->send_response();

                }else if($result == 404){
                    $response    = new Response(404, "Wrong Password");
                    $response->send_response();

                }else if($result == 301){
                    $response    = new Response(301, "Sorry, You do do have right to access this function");
                    $response->send_response();
                }else{
                    $response = new Response(200, "User basic Information", $result);
                    $response->send_response();
                }
            } else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates the regular user basic information
    protected function update_regular_user_basic_info(){
        if($this->url == '/api/update-regular-user-basic-info')
        {
            if($this->method == 'POST'){
                $_POST             = json_decode(file_get_contents('php://input'), true);

                $firstName         = InputCleaner::sanitize($_POST['first_name']);
                $middleName        = InputCleaner::sanitize($_POST['middle_name']);
                $lastName          = InputCleaner::sanitize($_POST['last_name']);
                $dateOfBirth       = InputCleaner::sanitize($_POST['date_of_birth']);
                $placeOfBirth      = InputCleaner::sanitize($_POST['place_of_birth']);
                $address           = InputCleaner::sanitize($_POST['address']);
                $gender            = InputCleaner::sanitize($_POST['gender']);
                $maritalStatus     = InputCleaner::sanitize($_POST['marital_status']);
                $cityProvidence    = InputCleaner::sanitize($_POST['city_providence']);
                $email             = InputCleaner::sanitize($_POST['email']);
                $country           = InputCleaner::sanitize($_POST['country']);
                $image             = InputCleaner::sanitize($_POST['image']);
                $last_updated      = gmdate('Y-m-d H:i:s');


                $details = [
                    'first_name'       => $firstName,
                    'middle_name'      => $middleName,
                    'last_name'        => $lastName,
                    'date_of_birth'    => $dateOfBirth,
                    'place_of_birth'   => $placeOfBirth,
                    'gender'           => $gender,
                    'address'          => $address,
                    'marital_status'   => $maritalStatus,
                    'city_providence'  => $cityProvidence,
                    'email'            => $email,
                    'country'          => $country,
                    'image'            => $image,
                    'last_update'      => $last_updated
                ];

                $update_user_info   = new UpdateUserProfile();
		        $identity    = ['column' => 'user_id', 'value' => $this->userId];
                $result      = $update_user_info->update_regular_user_basic_info($details, $identity);
    
                if($result == 500){
                    $response = new Response(500, "Contact Admin(Update health care healthcare basic Profile)");
                    $response->send_response();

                }else{
                    $response = new Response(200, "Health care basic information updated successfully");
                    $response->send_response();
                }

            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates the regular user basic information
    protected function update_regular_user_security_info(){
        if($this->url == '/api/update-regular-user-security-info')
        {
            if($this->method == 'POST'){
                $_POST             = json_decode(file_get_contents('php://input'), true);

                $contact          = InputCleaner::sanitize($_POST['contact']);
                $username         = InputCleaner::sanitize($_POST['username']);
                $password         = InputCleaner::sanitize($_POST['password']);
                $hashPassword     = password_hash($password, PASSWORD_DEFAULT);
                $last_updated     = gmdate('Y-m-d H:i:s');
                
                $details = [
                    'number'         => $contact,
                    'username'        => $username,
                    'password'        => $hashPassword,
                    'last_updated'    => $last_updated
                ];

                $update_user_info   = new UpdateUserProfile();
		        $identity    = ['column' => 'user_id', 'value' => $this->userId];
                $result      = $update_user_info->update_regular_user_security_profile($details, $identity);
    
                if($result == 500){
                    $response = new Response(500, "Contact Admin(Update health care healthcare basic Profile)");
                    $response->send_response();

                }else{
                    $response = new Response(200, "Health care basic information updated successfully");
                    $response->send_response();
                }

            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns all user profile/accounts
    protected function return_all_user_accounts(){
        if($this->url == '/api/all-user-accounts')
        {
            if($this->method == 'GET'){
                $allAccounts   = new ViewUserProfile();
                $result        = $allAccounts->get_all_user_accounts($this->userId);
                if($result == 500){
                    $response = new Response(500, "Error returning all accounts");
                    $response->send_response();
                }else{
                    $response = new Response(200, "All user related accounts", $result);
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint switch login user accounts(USER SWITCHING TO ANOTHER OWNED ACCOUNT) 
    protected function switch_login_user_accounts(){
        if($this->url == '/api/switch-login-user-accounts')
        {
            if($this->method == 'POST'){
                $_POST        = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['account_type']) || empty($_POST['requested_health_care_id'])){
                    $response = new Response(301, "Expected data not sent(account_type, requested_health_care_id)");
                    $response->send_response();
                }else{
                    $accountType              = InputCleaner::sanitize($_POST['account_type']);
                    $incommingHealthcareId    = InputCleaner::sanitize($_POST['requested_health_care_id']);
                    $switchAccounts           = new ViewUserProfile();

                    if($accountType == "Health Staff Account"){
                        $accountType  = "health_staff";
                        $result       = $switchAccounts->switch_login_healthstaff_accounts($this->userId, $incommingHealthcareId);
                        // print_r($result);
                        if($result === 500){
                            $response = new Response(500, "Error(Switching health staff selected account....)");
                            $response->send_response();
                        }else if($result === 404){
                            //CODE HERE
                        }
                        else{
                            $response = new Response(200, "Account Switched Successful");
                            $response->send_response();
                        }
                    }else if ($accountType == "Personal Account"){

                        $accountType     = "regular_user";
                        $tempUserId      = $_SESSION['temp_session'];
                        $result          = $switchAccounts->switch_login_regular_accounts($tempUserId);
        
                        if($result == 500){
                            $response = new Response(500, "ErrorError(Switching to personal account)");
                            $response->send_response();

                        }else if($result == 404){

                            //CODE HERE
                        }
                        else{
                            $response = new Response(200, "Account Switched Successful");
                            $response->send_response();
                        }
                    }else{
                        Logout::log_user_out();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }
}

(new GenericUserHandler);

