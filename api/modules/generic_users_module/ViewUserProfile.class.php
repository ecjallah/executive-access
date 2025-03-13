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

    //This method returns user personal profile information
    public function get_this_user_profile($user_id){
        $query   = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_id` = '$user_id' GROUP BY `user_id` ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }

    }

    //This method return user security profile information
    public function get_user_security_information($user_id){
        $query   = CustomSql::quick_select(" SELECT * FROM `users_security` WHERE `user_id` = '$user_id'  ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method helps the get_user_regular_account in retrying the query using the regular account id
    private function get_user_regular_account_helper($regularId){
        $query    = CustomSql::quick_select(" SELECT * FROM `regular_users` WHERE `user_id` = $regularId ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count === 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details = [
                        "user_id"    => $row['user_id'],
                        "user_type"  => $row['user_type'],
                        "image"      => $row['image'],
                        "full_name"  => $row['full_name']
                    ];
                    $data = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method gets_user_notifications
    public function get_users_notifications($userId){
        $query    = CustomSql::quick_select(" SELECT * FROM `notifications` WHERE account_id = $userId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $data       = [];
                while ($row = $query->fetch_assoc()) {
                    $details = ["notification"         => $row];
                    if($row['type'] === 'request' && $row['request_type'] === 'ride'){
                        //GET REQUEST DETAILS
                        $notificationDetails              = $this->get_ride_information($row['request_id']);
                        $details['notification_request']  = $notificationDetails;
                    }
                    $data[]  = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns saved ride information
    public function get_ride_information($rideId){
        $query    = CustomSql::quick_select(" SELECT * FROM `ride` WHERE id = $rideId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }

    //This method checks if a promo code is already in use
    public function promo_code_check($promoCode){
        $query      = CustomSql::quick_select(" SELECT * FROM `agent_promo_code` WHERE `promo_code` = '$promoCode' ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                return 200;
            }else{
                return 404;
            }
        }
    }

    //This method return all registered agents with assigned promo code
    public function get_all_agents($pager = 0, $limit = 10){
        $pager              = $pager*$limit;
        $query              = CustomSql::quick_select(" SELECT * FROM `agent_promo_code` ORDER BY id DESC LIMIT $limit OFFSET $pager ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            if($count >=1){
                $data       = [];
                while ($row = $query->fetch_assoc()) {
                    //Get user information
                    $agentInfo          = Helper::user_details($row['user_id'])[0];
                    $details            = [
                        "id"                => $row['id'],
                        "agent_id"          => $agentInfo['user_id'],
                        "agent_full_name"   => $agentInfo['full_name'],
                        "agent_image"       => $agentInfo['image'],
                        "promo_code"        => $row['promo_code'],
                        "status"            => $row['status'],
                        "promo_code_users"  => $this->return_agent_promo_code_users_amount($row['promo_code'])['status']==500?0:$this->return_agent_promo_code_users_amount($row['promo_code'])['data']
                    ];
                    $data[] = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns an agent promo-code users amount
    public function return_agent_promo_code_users_amount($promoCode){
        $query = CustomSql::quick_select(" SELECT * FROM `users_security` WHERE promo_code_registration = '$promoCode' ");
        if($query === false){
            return ['status' => 500];
        }else{
            return ['status' => 200, 'data' => $query->num_rows];
        }
    }
}