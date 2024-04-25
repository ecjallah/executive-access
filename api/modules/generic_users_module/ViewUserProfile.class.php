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
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
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
}


