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
            $this->update_regular_user_basic_info();
            $this->update_regular_user_security_info();
            $this->update_user_profile_image();
            $this->get_user_notification();
            $this->change_user_password();
            $this->update_account_location();
            $this->delete_user_account();
            // $this->return_all_user_accounts();
            // $this->switch_login_user_accounts();
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
                $result    = $userInfo->get_this_user_profile($this->userId);
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
            if($this->method == "GET"){
                $_GET           = json_decode(file_get_contents('php://input'), true);
                // $password       = InputCleaner::sanitize($_GET['password']);
                $userInfo       = new ViewUserProfile();
                $result         = $userInfo->get_user_security_information($this->userId);
                if($result == 500){
                    $response   = new Response(500, "Contact Admin(View User Profile)");
                    $response->send_response();
                }else if($result == 404){
                    $response   = new Response(404, "Wrong Password");
                    $response->send_response();
                }else if($result == 301){
                    $response   = new Response(301, "Sorry, You do do have right to access this function");
                    $response->send_response();
                }else{
                    $response   = new Response(200, "User basic Information", $result);
                    $response->send_response();
                }
            }else{
                $response       = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates the regular user basic information
    protected function update_regular_user_basic_info(){
        if($this->url == '/api/update-user-basic-info')
        {
            if($this->method == 'POST'){
                $_POST             = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['full_name']) || empty($_POST['gender']) || empty($_POST['email']) || empty($_POST['full_name'])){
                    $response      = new Response(400, "Provide the following: full_name, gender and email");
                    $response->send_response();
                }else{
                    $full_name         = InputCleaner::sanitize($_POST['full_name']);
                    $gender            = InputCleaner::sanitize($_POST['gender']);
                    $email             = InputCleaner::sanitize($_POST['email']);
                    $last_updated      = gmdate('Y-m-d H:i:s');
    
                    $details = [
                        'full_name'     => $full_name,
                        'gender'        => $gender,
                        'email'         => $email,
                        'last_updated'  => $last_updated
                    ];
                    $update_user_info   = new UpdateUserProfile();
                    $identity           = ['column' => 'user_id', 'value' => $this->userId];
                    $result             = $update_user_info->update_regular_user_basic_info($details, $identity);
                    if($result == 500){
                        $response       = new Response(500, "Error updating user account.");
                        $response->send_response();
                    }else{
                        $response       = new Response(200, "User account updated successfully");
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates the regular user basic information
    protected function update_regular_user_security_info(){
        if($this->url == '/api/update-user-security-info')
        {
            if($this->method == 'POST'){
                $_POST                = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['number']) || empty($_POST['password'])){
                    $response         = new Response(400, "Provide the following: number and password");
                    $response->send_response();
                }else{
                    $number           = InputCleaner::sanitize($_POST['number']);
                    $password         = InputCleaner::sanitize($_POST['password']);
                    $hashPassword     = password_hash($password, PASSWORD_DEFAULT);
                    $last_updated     = gmdate('Y-m-d H:i:s');
                    $details          = [
                        'number'        => $number,
                        'username'      => $number,
                        'password'      => $hashPassword,
                        'last_updated'  => $last_updated
                    ];
                    $update_user_info   = new UpdateUserProfile();
                    $identity           = ['column' => 'user_id', 'value' => $this->userId];
                    $result             = $update_user_info->update_regular_user_security_profile($details, $identity);
                    if($result == 500){
                        $response       = new Response(500, "Error updating user security");
                        $response->send_response();
                    }else{
                        $response       = new Response(200, "User security updated successfully.");
                        $response->send_response();
                    }
                }
            }else{
                $response               = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint changes user password
    protected function change_user_password(){
        if($this->url == '/api/change-user-password')
        {
            if($this->method == 'POST'){
                $_POST                = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['current_password']) || empty($_POST['new_password'])){
                    $response         = new Response(400, "Provide the following: current_password and new_password");
                    $response->send_response();
                }else{
                    $oldPassword      = InputCleaner::sanitize($_POST['current_password']);
                    $userInfo         = Helper::user_details($this->userId);
                    if(password_verify($oldPassword, $userInfo[0]['password'])){
                        $password         = InputCleaner::sanitize($_POST['new_password']);
                        $hashPassword     = password_hash($password, PASSWORD_DEFAULT);
                        $last_updated     = gmdate('Y-m-d H:i:s');
                        $details          = [
                            'password'      => $hashPassword,
                            'last_updated'  => $last_updated
                        ];
                        $update_user_info   = new UpdateUserProfile();
                        $identity           = ['column' => 'user_id', 'value' => $this->userId];
                        $result             = $update_user_info->update_regular_user_security_profile($details, $identity);
                        if($result == 500){
                            $response       = new Response(500, "Error updating user password");
                            $response->send_response();
                        }else{
                            $response       = new Response(200, "User password updated successfully.");
                            $response->send_response();
                        }
                    }else{
                        $response    = new Response(400, "Sorry, your current password does not match your given password.");
                        $response->send_response();
                    }
                }
            }else{
                $response               = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates user profile image
    public function update_user_profile_image(){
        if($this->url == "/api/update-user-profile-image")
        {
            if($this->method == "POST"){
                if(isset($_FILES['image'])){
                    $filename          = InputCleaner::sanitize($_FILES["image"]["name"]);
                    $image             = InputCleaner::sanitize($_FILES["image"]["tmp_name"]);

                    $fileLocation      = __ROOT__."/generic_images/profile_images/";
                    $saveLocation      = "/generic_images/profile_images/";
    
                    //File uploader
                    $fileUploads       = new FilesHandler($fileLocation, $saveLocation, $_FILES["image"], $filename);
                    $imageName         = $fileUploads->filesProcessor();
                    $editUserImg       = new UpdateUserProfile();
                    $details           = ["image"   => $imageName];
                    $identity          = ['column'  => ['user_id'], 'value' => [$this->userId]];
                    $imageEdit         = $editUserImg->update_regular_user_basic_info($details, $identity);
                    if($imageEdit === 500){
                        $response      = new Response(500, 'Error updating user image.');
                        $response->send_response();
                    }else{
                        $response      = new Response(200, 'Image updated successfully.');
                        $response->send_response();
                    }
                }else{
                    $response      = new Response(400, 'Please provide an image.');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns users notification(Admin, Driver and Regular users)
    protected function get_user_notification(){
        if($this->url == '/api/get-notifications')
        {
            if($this->method == 'GET'){
                $allNotifications    = new ViewUserProfile();
                $result              = $allNotifications->get_users_notifications($this->userId);
                if($result === 500){
                    $response        = new Response(500, "Error returning user notifications");
                    $response->send_response();
                }else if($result === 404){
                    $response        = new Response(404, "No notifications at this time.", $result);
                    $response->send_response();
                }else{
                    $response        = new Response(200, "User notifications", $result);
                    $response->send_response();
                }
            }else{
                $response            = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns all user profile/accounts
    // protected function return_all_user_accounts(){
    //     if($this->url == '/api/all-user-accounts')
    //     {
    //         if($this->method == 'GET'){
    //             $allAccounts   = new ViewUserProfile();
    //             $result        = $allAccounts->get_all_user_accounts($this->userId);
    //             if($result == 500){
    //                 $response = new Response(500, "Error returning all accounts");
    //                 $response->send_response();
    //             }else{
    //                 $response = new Response(200, "All user related accounts", $result);
    //                 $response->send_response();
    //             }
    //         }else{
    //             $response = new Response(300, "This endpoint accepts the GET method");
    //             $response->send_response();
    //         } 
    //     }
    // }

    // //This endpoint switch login user accounts(USER SWITCHING TO ANOTHER OWNED ACCOUNT) 
    // protected function switch_login_user_accounts(){
    //     if($this->url == '/api/switch-login-user-accounts')
    //     {
    //         if($this->method == 'POST'){
    //             $_POST        = json_decode(file_get_contents('php://input'), true);
    //             if(empty($_POST['account_type']) || empty($_POST['requested_health_care_id'])){
    //                 $response = new Response(301, "Expected data not sent(account_type, requested_health_care_id)");
    //                 $response->send_response();
    //             }else{
    //                 $accountType              = InputCleaner::sanitize($_POST['account_type']);
    //                 $incommingHealthcareId    = InputCleaner::sanitize($_POST['requested_health_care_id']);
    //                 $switchAccounts           = new ViewUserProfile();

    //                 if($accountType == "Health Staff Account"){
    //                     $accountType  = "health_staff";
    //                     $result       = $switchAccounts->switch_login_healthstaff_accounts($this->userId, $incommingHealthcareId);
    //                     // print_r($result);
    //                     if($result === 500){
    //                         $response = new Response(500, "Error(Switching health staff selected account....)");
    //                         $response->send_response();
    //                     }else if($result === 404){
    //                         //CODE HERE
    //                     }
    //                     else{
    //                         $response = new Response(200, "Account Switched Successful");
    //                         $response->send_response();
    //                     }
    //                 }else if ($accountType == "Personal Account"){

    //                     $accountType     = "regular_user";
    //                     $tempUserId      = $_SESSION['temp_session'];
    //                     $result          = $switchAccounts->switch_login_regular_accounts($tempUserId);
        
    //                     if($result == 500){
    //                         $response = new Response(500, "ErrorError(Switching to personal account)");
    //                         $response->send_response();

    //                     }else if($result == 404){

    //                         //CODE HERE
    //                     }
    //                     else{
    //                         $response = new Response(200, "Account Switched Successful");
    //                         $response->send_response();
    //                     }
    //                 }else{
    //                     Logout::log_user_out();
    //                 }
    //             }
    //         }else{
    //             $response = new Response(300, "This endpoint accepts the POST method");
    //             $response->send_response();
    //         } 
    //     }
    // }

    //This endpoint updates accounts location
    protected function update_account_location(){
        if($this->url == '/api/update-account-location')
        {
            if($this->method == 'POST'){
                $_POST                  = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['latitude']) || empty($_POST['longitude'])){
                    $response           = new Response(400, "Contact Admin(Please provide: latitude, and longitude)");
                    $response->send_response();
                }else{
                    $latitude           = InputCleaner::sanitize($_POST['latitude']);
                    $longitude          = InputCleaner::sanitize($_POST['longitude']);
                    $details            = ['longitude'  => $longitude, 'latitude'  => $latitude];
                    $update_user_info   = new UpdateUserProfile();
                    $identity           = ['column' => 'user_id', 'value' => $_SESSION['user_id']];
                    $updateLocation     = $update_user_info->update_regular_user_basic_info($details, $identity);
                    if($updateLocation === 500){
                        $response = new Response(500, "Error updating account location.");
                        $response->send_response();
                    }else{
                        $response = new Response(200, "Account location updated successfully");
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This endpoint deletes/updates regular account as deleted
    protected function delete_user_account(){
        if($this->url == '/api/delete-user-account')
        {
            if($this->method == 'POST'){
                $_POST               = json_decode(file_get_contents('php://input'), true);
                //Get healthcareId
                $accountDeletion     = new UpdateUserProfile;
                $result              = $accountDeletion->delete_user_account($this->userId);
                if($result === 500){
                    $response        = new Response(500, "Error deleting user account.");
                    $response->send_response();
                }else{
                    $response        = new Response(200, "Account deleted successfully.", $result);
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }
}
(new GenericUserHandler);