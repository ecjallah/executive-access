<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the Registration of every user on the app. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @Contact Phone: (+231) 770558804
 *      @Contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Registration{
    function __construct(){
        $this->method      = $_SERVER['REQUEST_METHOD'];
        $this->url         = $_SERVER['REQUEST_URI'];
        if($this->method == "POST"){
            if($this->url == '/api/app-registration')
            {
                $this->new_account_registration();
            }
        }else if($this->method == "GET"){
            if($this->url == '/api/get-registration-types'){
                $this->return_registration_account_types();
            }
            else if($this->url == '/api/view-public-elections'){
                $viewElection         = new ViewElections();
                $result               = $viewElection->get_all_elections();
                if($result === 500){
                    $response         = new Response(500, "Error returning issues.");
                    $response->send_response();
                }else if($result === 404){
                    $response         = new Response(404, "No issue created at this time.");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Created Elections.", $result);
                    $response->send_response();
                }
            }
        }else{
            $response = new Response(300, "This endpoint accepts the POST method", $this->method);
            $response->send_response();
        }
    }

    //This endpoint returns all created user account groups/types
    protected function return_registration_account_types(){
        $userGroups   = new ViewUserGroup();
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
    }

    //This method signup new health care
    private function new_account_registration(){
        $_POST              = json_decode(file_get_contents('php://input'), true);
        //FIRST REGISTRATION PROCESS
        if(InputCleaner::sanitize($_POST['verify']) == 'none'){
            $user_type      = InputCleaner::sanitize($_POST['user_type']);
            $number         = InputCleaner::sanitize($_POST['number']);
            $email          = InputCleaner::sanitize($_POST['email']);
            $country        = "Liberia";

            //Generates verification coode
            $code          = $this->generate_verification_code('registration', $user_type, $number, $email, $country);
            if($code === 200){
                $details   = ['number'  => $number, 'email' => $email];
                $response  = new Response(200, "Success", $details);
                $response->send_response();
            }else if($code === 500){
                $response  = new Response(500, "Error(generating_verification_code)", $code);
                $response->send_response();
            }
        }else{
            $user_type           = InputCleaner::sanitize($_POST['user_type']);
            $user_id             = $this->id_generator('900');
            $full_name           = InputCleaner::sanitize($_POST['full_name']);
            $address             = InputCleaner::sanitize($_POST['address']);
            $email               = InputCleaner::sanitize($_POST['email']);
            $county              = InputCleaner::sanitize($_POST['county']);
            $country             = "Liberia";

            //SECURITY DETAILS
            $number              = InputCleaner::sanitize($_POST['number']);
            $username            = InputCleaner::sanitize($_POST['username']);
            $password            = InputCleaner::sanitize($_POST['password']);
            $hashed_password     = password_hash($password, PASSWORD_DEFAULT);
            $last_updated        = gmdate('Y-m-d H:i:s');
            // $hashed_password     = password_hash($password, PASSWORD_DEFAULT);

            //CODE VERIFICATION PROCESS
            $verify  = new Auth;
            $result  = $verify->verify_verification_code($_POST['verify'], $number);
            if($result === 500){
                $response = new Response(500, "Error verifying code");
                $response->send_response();
            }else if($result === 404){
                $response = new Response(404, "Code does'nt matched!");
                $response->send_response();
            }else{
                //GET ACCOUNT AHARACTER FROM USER TYPE
                $getUserType       = new ViewUserGroup();
                $basic_details     = '';
                $regularSecurity   = '';
                $result            = $getUserType->return_account_group_by_id($user_type);
                if($result['account_type'] == 'business'){
                    //REGULAR USER INFORMATION
                    $basic_details = [
                        'user_id'           => $user_id,
                        'user_type'         => $user_type,
                        'full_name'         => $full_name,
                        'address'           => $address,
                        'image'             => '/media/images/user-image-placeholder.png',
                        'email'             => $email,
                        'city_providence'   => $county,
                        'country'           => $country,
                        'approval_status'   => "pending"
                    ];
                    //REGULAR USER SECURITY INFO
                    $regularSecurity = [
                        'user_id'        => $user_id,
                        'number'         => $number,
                        'user_type'      => $user_type,
                        'username'       => $username,
                        'country'        => $country,
                        'password'       => $hashed_password,
                        'last_updated'   => $last_updated
                    ];
                }else{
                    $response = new Response(301, 'You have been recorded!');
                    $response->send_response();
                }
                $result             =  $this->create_account($basic_details, $regularSecurity);
                if($result == 500){
                    $response       = new Response(500, "Error crreating new account");
                    $response->send_response();
                }else if($result === 200){
                    //LOG THE USER IN
                    $loginHandler   = new RegistrationLogin;
                    $login          = 200;
                    if($login == 200){
                        $response = new Response(200, "Account created sucessfully");
                        $response->send_response();
                    }else if($login == 404){
                        $response = new Response(404, "The username is already taken.");
                        $response->send_response();
                    }else if($login === 500){
                        $response = new Response(500, "Database Error!(Login)");
                        $response->send_response();
                    }else if($login === null){
                        $response = new Response(404, "Incorrect login credentials");
                        $response->send_response();
                    }
                }
            }
        }
    }

    //This method generates and store verification code for users
    public function generate_verification_code($type, $userType, $number, $email = null, $country = null){
        $code      = mt_rand();
        $str       = (string) $code;
        $str       = "$code";   
        $newCode   = substr($str, 3, 5);
        //Add Security Information
        $details = [
            'type'       => $type,
            'user_type'  => $userType,
            'code'       => $newCode,
            'number'     => $number,
            'country'    => $country,
            'email'      => $email,
            'date'       => gmdate('Y-m-d : H:s:i')
        ];
        $query      = CustomSql::insert_array('user_verification_code', $details);
        if($query === false){
            return 500;
        }else{
            // ALLOW TO SEND SMS
            // $sendSms               = new MessageCenter();
            // $result                = $sendSms->send_sms($newCode, $number);
            // if($result === 200){
            //     if($email != null){
            //         //ALLOW EMAIL SENDER
            //         $email_sender  = new EpadMiller();
            //         $details       = ['subject'  => "Verification code", 'message' => 'Your five digits verification code: '.$newCode, 'email' => $email];
            //         $result        = $email_sender->email_sender($details);
            //         return $result;
            //     }
            // }else 
            // if($result === 500){

                //ALLOW EMAIL SENDER
                $email_sender  = new BoneMiller();
                $details       = ['subject'  => "Account Verification Code", 'message' => 'Your six digit verification code: '.$newCode, 'email' => $email];
                $result        = $email_sender->email_sender($details);
                return 200;
            // }
        }
    }

    //This method creates new healthcare user
    public function create_account($details, $user_security){
        //CHECK IF USERNAME IS IN USED
        CustomSql::commit_off();
        $auth                = new Auth;
        //CHECK IF USERNAME IS IN USED 
        $usernameCheck       = $auth->username_check($user_security['username']);
        if($usernameCheck === 200){
            $query           = CustomSql::insert_array('user_accounts', $details);
            if($query === false){
                return 500;
            }else{
                $securityQuery    = CustomSql::insert_array('users_security', $user_security);
                if($securityQuery === false){
                    CustomSql::rollback();
                    return 500;
                }else{
                    CustomSql::save();
                    return 200;
                }
            }
        }else{
            //USERNAE IN USED
            return 404;
        }
    }

    //This method generates all users id
    public function id_generator($range){
        $date        = date('YmdHis');
        $id          = $range.$date;
        //Ckeck if id exists
        $query       = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_id` = $id ");
        if ($query === false) {
            return false;
        }else{
            $count   = $query->num_rows;
            if ($count >= 1) {
                $this->id_generator($range);
            }else{
                return $id;
            }
        }
    }
    
}

$registration = new Registration;
