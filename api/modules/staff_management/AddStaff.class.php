<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("CREATE_STAFFS_MODULE_ID", '200010');
define("CREATE_STAFFS_FUNCTION_ID", '200025');
define("CREATE_STAFFS_FUNCTION_NAME", 'Create new Staffs');
Auth::module_function_registration(CREATE_STAFFS_FUNCTION_ID, CREATE_STAFFS_FUNCTION_NAME, CREATE_STAFFS_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class creates new user. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class AddStaff{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(CREATE_STAFFS_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission  =  $auth;
        }
    }

    //This function creates a new user account
    public function create_new_staff_account(array $details){
        //Generate user id
        $personalUserId         = $this->id_generator('900');
        // "staff_personal_id" => $personalUserId,
        $basic_details          = [
            'user_id'           => $personalUserId,
            'user_type'         => $details['account_type'],
            'full_name'         => $details['first_name'].' '.$details['last_name'],
            'address'           => $details['address'],
            'image'             => '/media/images/user-image-placeholder.png',
            'email'             => $details['email'],
            'city_providence'   => $details['county'],
            'country'           => "Liberia"
        ];

        //REGULAR USER SECURITY INFO'
        $regularSecurity = [
            'user_id'        => $basic_details['user_id'],
            'number'         => $details['number'],
            'user_type'      => $details['account_type'],
            'username'       => $details['username'],
            'country'        => "Liberia",
            'password'       => $details['hashed_password'],
            'last_updated'   => Helper::get_current_date()
        ];

        $result              =  $this->create_regular_account($basic_details, $regularSecurity, $details);
        if($result == 500){
            $response        = new Response(500, "Error crreating new account");
            $response->send_response();
        }else if($result === 200){

        }
    }
    
    //This function checks if the staff is already added
    private function check_staff_status($businessId, $userId){
        $query     = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `business_id` = $businessId AND `staff_personal_id` = $userId ");
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

    //This function generates all users id
    private function id_generator($range){
        $date        = date('YmdHis');
        $id          = $range.$date;
        //Check if id exists
        $query       = CustomSql::quick_select(" SELECT * FROM staff_accounts WHERE `staff_id` = $id ");
        if ($query === false) {
            return 500;
        }else{
            $count   = $query->num_rows;
            if ($count >= 1) {
                $this->id_generator($range);
            }else{
                return $id;
            }
        }
    }

    //This method creates new regular account
    public function create_regular_account($details, $user_security, $staff_details){
        //CHECK IF USERNAME IS IN USED
        CustomSql::commit_off();
        $auth                     = new Auth;
        //CHECK IF USERNAME IS IN USED
        $usernameCheck            = $auth->username_check($user_security['username']);
        if($usernameCheck === 200){
            $userQuery            = CustomSql::insert_array('user_accounts', $details);
            if($userQuery === false){
                return 500;
            }else{
                $securityQuery    = CustomSql::insert_array('users_security', $user_security);
                if($securityQuery === false){
                    CustomSql::rollback();
                    return 500;
                }else{
                    //ADD USER AS STAFF
                    $accountType                         = Helper::return_user_type_from_id($details['user_id']);
                    if($accountType == 'staff'){
                        //Check staff status at a particular business
                        $staff_check                     = $this->check_staff_status($staff_details['business_id'], $details['user_id']);
                        if($staff_check == 200){
                            //Generate user id
                            $userId                      = $this->id_generator('700');
                            $staffInfo           =  [
                                "staff_personal_id" => $details['user_id'],
                                "business_id"       => $staff_details['business_id'],
                                "role_id"           => $staff_details['role_id'],
                                "staff_id"          => $userId,
                                "account_type"      => $staff_details['account_type'],
                                "added_date"        => gmdate("Y-m-d :H:s:i"),
                                "added_by"          => $this->userId
                            ];
                            $staff_details['staff_id']   = $userId;
                            $query                       = CustomSql::insert_array('staff_accounts', $staffInfo);
                            if($query === false){
                                CustomSql::rollback();
                                return 500;
                            }else{
                                CustomSql::save();
                                return 200;
                            }
                        }else{
                            return $staff_check;
                        }
                    }else{
                        CustomSql::rollback();
                        return 400;
                    }
                }
            }
        }else{
            //USERNAE IN USED
            return 404;
        }
    }
}