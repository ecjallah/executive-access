<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// header("Allow-Control-Origin: *)");

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This handler Updates user profile information. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class EditUser{
    public $userId;
    public $user_type;
    public $account_character;
    public $method;
    public $url;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId            = $_SESSION['user_id'];
            $this->user_type         = $_SESSION['user_type'];
        }        
    }

    //This function update/edits a user profile
    public function update_user_info($businessId, $roleId, $staffId, $details, $identity){
        CustomSql::commit_off();
        $query            = CustomSql::update_array($details, $identity, 'user_accounts');
        if($query === false){
            return 500;
        }else{
            $updateRole  = $this->update_staff_role($businessId, $staffId, $roleId);
            if($updateRole === 200){
                CustomSql::save();
            }else{
                CustomSql::rollback();
            }
            return $updateRole;
        }
    }

    //This method updates staff role
    public function update_staff_role($businessId, $staffId, $roleId){
        $userStaffId  = Helper::get_user_staff_id(5, $staffId);
        $details      = ['role_id' => $roleId];
        $identity     = ['column' => ['business_id', 'staff_id'], 'value' => [$businessId, $userStaffId]];
        $query        = CustomSql::update_array($details, $identity, 'staff_accounts');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}


