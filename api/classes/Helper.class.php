<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This classcontains smaller functions that helps with utilities. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class Helper{
    public $userId;
    public $user_type;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId      = $_SESSION['user_id'];
            $this->user_type   = $_SESSION['user_type'];
        }
    }

    //This function returns user Informations(LOOKUP ANY USER)
    public static function lookup_users($searchValue){
        $query        = CustomSql::quick_select(" SELECT a.*, s.* FROM `user_accounts` a JOIN `users_security` s ON a.user_id = s.user_id WHERE a.user_id = '$searchValue' OR s.username = '$searchValue' ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = [
                        "user_id"   => $row['user_id'],
                        "username"  => $row['username'],
                        "full_name" => $row['full_name'],
                        "address"   => $row['address'],
                        "number"    => $row['number'],
                        "image"     => $row['image']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function lookups beneficiary by EMPLOYEE ID OR USER ID
    public static function lookup_beneficiary($searchValue){
        $query        = CustomSql::quick_select(" SELECT * FROM `beneficiary` WHERE id = '$searchValue' OR employee_id = '$searchValue' OR first_name LIKE '%$searchValue%' OR last_name LIKE '%$searchValue%' OR full_name LIKE '%$searchValue%' ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $ministryDetails = Helper::lookup_users($row['business_id']);
                    $data = [
                        "id"             => $row['id'],
                        "business_info"  => $ministryDetails,
                        'package_id'     => $row['package_id'],
                        "employee_id"    => $row['employee_id'],
                        "email"          => $row['email'],
                        "number"         => $row['number'],
                        "image"          => $row['image'],
                        "sex"            => $row['sex'],
                        "county"         => $row['county'],
                        "full_name"      => $row['full_name']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
    //This function returns a given user role
    public static function get_user_role_title($roleId){
        $query  = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE role_id = $roleId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                return $role  = $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }

    //This function returns the active user role
    public function get_login_user_role($userId){}

    // ************************************************
        //SHELL MODULE METHODS STARTS BELOW
    // ***********************************************

    //This function returns active user role
    public static function get_user_assigned_role($userId){
        $query     = CustomSql::quick_select("SELECT * FROM `staff_accounts` WHERE `staff_personal_id` = $userId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $staffRole = $query->fetch_assoc()['role_id'];
                // $staffRole = $query->fetch_assoc()['user_type'];
                return $staffRole;
            }else{
                return 404;
            }
        }
    }

    //This function returns all in formation relationg to a user
    public static function user_details($userId){
        $query        = CustomSql::quick_select(" SELECT a.*, s.* FROM `user_accounts` a JOIN `users_security` s ON s.user_id = a.user_id WHERE a.user_id = $userId ");
        if($query === false){
            return false;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function returns a user type from the user id
    public static function return_user_type_from_id($userId){
        //Get user details
        $userDetails   = Helper::user_details($userId)[0];
        $accountType   = $userDetails['user_type'];
        $query         = CustomSql::quick_select(" SELECT * FROM `user_account_type` WHERE id = $accountType ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc()['account_type'];
            }else{
                return 404;
            }
        }
    }

    //This function returns all the user roles in the system
    public static function get_all_users_roles(){
        $parent           = new Helper();
        $query   = CustomSql::quick_select(" SELECT * FROM `staff_role` ORDER BY `date_added` DESC ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = [
                        "role_id"       => $row['role_id'],
                        "role_title"    => $row['role_title'],
                        "created_date"  => $row['date_added']
                    ];
                    
                    $data[] = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //Check if a role belongs to the application
    public static function auth_check_user_role($roleId){
        $query  = CustomSql::quick_select(" SELECT * FROM `staff_role` WHERE role_id = $roleId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                return 200;
            }else{
                return 301;
            }
        }
    }

    //This function returns the current date and time
    public static function get_current_date($date = null){
        if($date == null){
            return gmdate('Y-m-d H:i:s');
        }else{
            return date('F j, Y g:i a', strtotime($date));
        }
    }

    //This function returns staff company id
    public static function get_staff_company_id($accountCharacter, $userId){
        if($accountCharacter == 'ninja' || $accountCharacter == 'business' ){
            return $userId;
        }else{
            $query          = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `staff_personal_id` = $userId OR `staff_id` = $userId AND `business_id` = $userId ORDER BY `id` LIMIT 1 ");
            if($query === false){
                return 500;
            }else{
                $count      = $query->num_rows;
                if($count == 1){
                    $result =  $query->fetch_assoc()['business_id'];
                    return $result;
                }
            }
        }
    }

    //This function returns user staff id
    public static function get_user_staff_id($accountCharacter, $userId){
        if($accountCharacter == 5){
            $query    = CustomSql::quick_select(" SELECT staff_id FROM staff_accounts WHERE `staff_personal_id` = '{$userId}' OR `staff_id` = '{$userId}' ");
            if($query === false){
                return false;
            }else{
                return $query->fetch_assoc()['staff_id'];
            }
        }else{
            return $userId;
        }
    }

    //This function returns staff company id
    public static function get_ministry_id_from_emp_id($employeeId){
        $query    = CustomSql::quick_select(" SELECT business_id FROM beneficiary WHERE `id` = '{$employeeId}' ");
        if($query === false){
            return 500;
        }else{
            return $query->fetch_assoc()['business_id'];
        }
    }

    //This function checks if a confrimation code of a given type is present and not used
    // public static function confrimation_code_checker($codeType, $number){
    //     $query     = CustomSql::quick_select(" SELECT * FROM `user_verification_code` WHERE `type` = '$codeType' AND `number` = '$number' AND `status` = '0' ORDER BY id DESC ");
    //     if($query === false){
    //         return 500;
    //     }else{
    //         $count = $query->num_rows;
    //         if($count >= 1){
    //             //Something is there
    //             $row      = $query->fetch_assoc();
    //             // ALLOW TO SEND SMS
    //             $sendSms  = new MessageCenter();
    //             $result   = $sendSms->send_sms($row['code'], $number);
    //             return $result;
    //         }else{
    //             return 200;
    //         }
    //     }
    // }

    //This function returns a staff details(PERSONAL ACCOUNT ID)
    public static function get_staff_personals($healthStaffId){
        $query        = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE `user_id` = $healthStaffId ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }

    //This function returns a business user type
    public static function get_business_id($userId, $accountType){
        if($accountType == 'business' || $accountType == 'ninja'){
            return $userId;
        }else if($accountType == 'staff'){
            $result =  Helper::get_staff_company_id($accountType, $userId);
            return $result;
        }
    }

    //This function checks if a hospital in currently linked to a ministry
    public function check_if_a_hospital_is_linked_to_ministry($ministryId, $hospitalId){
        $query    = CustomSql::quick_select(" SELECT * FROM `linked_hospitals` WHERE  ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){

            }else{
                return 404;
            }
        }
    }

    //This method check if aan account is blocked
    public static function check_account_block_status($accountId){
        $query     = CustomSql::quick_select(" SELECT * FROM `users_security` WHERE `user_id` = $accountId AND `blocked` = 1 ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                //USER IS NOT BLOCKED
                return 404;
            }else{
                //USER IS BLOCKED
                return 200;
            }
        }
    }

    //This method check if aan account is blocked
    public static function check_account_approval_status($accountId){
        $query     = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_id` = $accountId AND `approval_status` = 'approved' ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                //USER IS APPROVED
                return 200;
            }else{
                //USER IS NOT APPROVED
                return 404;
            }
        }
    }

    //This method returns staff department id
    public static function get_staff_department_id($staffId){
        $query     = CustomSql::quick_select(" SELECT * FROM `department_staff` WHERE staff_id = $staffId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data =  $query->fetch_assoc()['department_id'];
                return ['status' => 200, 'data' => $data] ;
            }else{
                return ['status' => 404] ;
            }
        }
    }

    //This method returns appointment department id
    public static function get_appointment_department_id($appointmentId){
        $query     = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE department_id = $appointmentId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data =  $query->fetch_assoc();
                return ['status' => 200, 'data' => $data] ;
            }else{
                return ['status' => 404] ;
            }
        }
    }

    //This method generates appointment verification code
    public function generate_appointment_verification_code($companyId, $appointmentId, $number){
        $code             = mt_rand();
        $str              = (string) $code;
        $str              = "$code";   
        $newCode          = substr($str, 0, 7);

        // ALLOW TO SEND SMS
        $sendSms          = new MessageCenter();
        $result           = $sendSms->send_sms($newCode, $number);
        if($result === 200){
            //Appointment Information
            $details      = [
                'token'            => $newCode,
                'approval_status'  => 'approved',
                'date_updated'     => gmdate('Y-m-d : H:s:i')
            ];
            $identity     = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointmentId]];
            $query        = CustomSql::update_array($details, $identity, 'appointments');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }else if($result === 500){
            return 500;
        }
    }

    //This method return a department id from a staff id
    public static function get_department_id_from_staff_id($staffId){
        $query     = CustomSql::quick_select(" SELECT * FROM `department_staff` WHERE staff_id = $staffId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data =  $query->fetch_assoc()['department_id'];
                return ['status' => 200, 'data' => $data] ;
            }else{
                return ['status' => 404] ;
            }
        }
    }

    //This method returns a minisrty id from a staff id
    public static function get_ministry_id_from_staff_id($staffId){
        $query     = CustomSql::quick_select(" SELECT * FROM `staff_accounts` WHERE staff_id = $staffId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data =  $query->fetch_assoc()['business_id'];
                return ['status' => 200, 'data' => $data] ;
            }else{
                return ['status' => 404] ;
            }
        }
    }
}