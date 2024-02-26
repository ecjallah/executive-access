<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// header("Allow-Control-Origin: *)");
define("VIEW_USER_PROFILE_MODULE_ID", '100050');
define("VIEW_USER_PROFILE_FUNCTION_ID", '100051');
define("VIEW_USER_PROFILE_FUNCTION_NAME", 'View user Profile');
// Auth::module_function_registration(VIEW_USER_PROFILE_FUNCTION_ID, VIEW_USER_PROFILE_FUNCTION_NAME, VIEW_USER_PROFILE_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class View user information or Profile. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewUserProfile{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId      = $_SESSION['user_id'];
            $this->user_type   = $_SESSION['user_type'];

        }
    }

    //This function returns user personal profile information
    public function get_this_user_profile($userType, $user_id){
        $query   = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_id` = '$user_id' GROUP BY `user_id` ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
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

    //This function return user security profile information
    public function get_user_security_information($password, $user_id){
        $query   = CustomSql::quick_select(" SELECT * FROM `users_security` WHERE `user_id` = '$user_id'  ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    if(password_verify($password, $row['password'])){
                        $data[] = $row;
                    }else{
                        return 404;
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function returns all accounts related to a user
    public function get_all_user_accounts($userId){
        $details = [
            "personal_account"        => $this->get_user_regular_account($userId),
            "health_staff_accounts"   => $this->get_user_healthcare_accounts($userId)
        ];
        return $details;
    }

    //This function returns user regular account
    public function get_user_regular_account(string $userId){
        $query    = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_id` = $userId ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = [
                        "user_id"        => $row['user_id'],
                        "user_type"      => $row['user_type'], 
                        "image"          => $row['image'],
                        "full_name"      => $row['full_name']
                    ];
                    $data[] = $details;
                }
                return $data;
            }else{
                //Try using the health staff regular account id
                $regularId  = is_array(Helper::get_staff_personals($userId))? Helper::get_staff_personals($userId)['regular_account_id']:Helper::get_staff_personals($userId);
                $result     = $this->get_user_regular_account_helper($regularId);
                return $result;
            }
        }
    }

    //This function helps the get_user_regular_account in retrying the query using the regular account id
    private function get_user_regular_account_helper($regularId){
        $query    = CustomSql::quick_select(" SELECT * FROM `regular_users` WHERE `user_id` = $regularId ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = [
                        "user_id"        => $row['user_id'],
                        "user_type"      => $row['user_type'], 
                        "image"          => $row['image'],
                        "full_name"      => $row['full_name']
                    ];
                    $data[] = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function returns all user healthcare staff accounts
    public function get_user_healthcare_accounts($userId){
        $query = CustomSql::quick_select(" SELECT h.*, s.* FROM `health_staff` s JOIN `health_care` h ON h.user_id = s.health_care_id WHERE s.regular_account_id = $userId OR s.user_id = $userId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                //REETURN HEALTH STAFF INFORMATIONS
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = [
                        "healthcare_id"      => $row['health_care_id'],
                        "user_type"          => $row['user_type'], 
                        "healthcare_name"    => $row['health_care_name'],
                        "healthcare_image"   => $row['logo']
                    ];
                    $data[] = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This function switch a login user to a related healthstaff account
    public function switch_login_healthstaff_accounts($userId, $incommingHealthcareId){
        //Check if this user a really a health staff of the give healthcare he/she claims
        $query   = CustomSql::quick_select(" SELECT * FROM `health_staff` WHERE `user_id` = $userId OR regular_account_id = $userId AND health_care_id = $incommingHealthcareId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                $row  = $query->fetch_assoc();
                //Update session 
                $_SESSION['user_id']         = $row['user_id'];
                $_SESSION['user_type']       = $row['user_type'];
                $_SESSION['health_care_id']  = $row['health_care_id'];
                return 200;
            }else{
                return 404;
                // //Try using the health staff regular account id
                // $regularId  = Helper::get_staff_personals($userId);
                // echo $regularId;

                // // $result     = $this->switch_login_healthstaff_accounts($regularId, $incommingHealthcareId);
                // // if($result == 404 || $result == 500){
                // //     return $result;
                // // }else{
                // //     return 200;
                // // }
            }
        }
    }

    //This function switch a login user to a related regular account
    public function switch_login_regular_accounts($userId){
        //Check user id and staff account identity(UserId)
        $query = CustomSql::quick_select(" SELECT * FROM `regular_users` WHERE `user_id` = $userId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                $row = mysqli_fetch_assoc($query);
                $_SESSION['user_id']      = $row['user_id'];
                $_SESSION['user_type']    = $row['user_type'];

                return 200;

            }else{
                return 404;
            }
        }
    }
}


