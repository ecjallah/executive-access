<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles all modules management Service plan of the app(Payment). 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class ModulesManager{
    public $method;
    public $url;
    public $userId;
    public $user_type;
    function __construct(){
        $this->method          = $_SERVER['REQUEST_METHOD'];
        $this->url             = $_SERVER['REQUEST_URI'];
        if(isset($_SESSION['user_id'])){
            $this->userId      = $_SESSION['user_id'];
            $this->user_type   = $_SESSION['user_type'];
        }else{
            //There is no set session 
            return 301;
        }
    }

    //This function returns the active user SESSION
    public function return_active_user_session(){
        if(isset($_SESSION['user_id'])){
            $details = [
                'userId'             => $_SESSION['user_id'],
                'userType'           => $_SESSION['user_type'],
                'account_character'  => $_SESSION['account_character']
            ];
            return $details;
        }else{
            // Logout::log_user_out();
            return 301;
        }
    }

    //This method returns all app modules
    public function return_all_app_modules(){
        $query         = CustomSql::quick_select(" SELECT * FROM `app_modules` ORDER BY `id` DESC ");
        if($query === false){
            return 500;
        }else{
           $count      = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $details  = [
                        "module_id"         => $row['module_id'],
                        "module_title"      => $row['item_title'],
                    ];
                    $data[]   = $details;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns all app modules
    public function return_business_assigned_modules($accountGroupId){
        $query         = CustomSql::quick_select(" SELECT m.*, r.* FROM `app_modules` m JOIN `account_group_module` r ON m.module_id = r.module_id WHERE r.	account_group_id = $accountGroupId ORDER BY r.id DESC ");
        if($query === false){
            return 500;
        }else{
           $count      = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[]  = [
                        "module_id"         => $row['module_id'],
                        "module_title"      => $row['item_title'],
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns all assigned modules
    public function return_staff_assigned_modules($businessId){
        $query         = CustomSql::quick_select(" SELECT m.*, r.* FROM `app_modules` m JOIN `role_modules` r ON m.module_id = r.module_id WHERE r. r.business_id = '$businessId' ORDER BY r.id DESC ");
        if($query === false){
            return 500;
        }else{
           $count      = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[]  = [
                        "module_id"         => $row['module_id'],
                        "module_title"      => $row['item_title']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}
(new ModulesManager);