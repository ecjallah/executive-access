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

class DeleteUserAccounts{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId            = $_SESSION['user_id'];
            $this->user_type         = $_SESSION['user_type'];
        }        
    }

    //This function deletes a user account
    public function delete_user_accounts($userId){
        $query    = CustomSql::delete_sql('users', " user_id = '$userId' ");
        if($query === false){
            return $query;
        }else{
            return 200;
        }
    }

}


