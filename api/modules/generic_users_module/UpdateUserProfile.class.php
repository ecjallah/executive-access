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

class UpdateUserProfile{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId            = $_SESSION['user_id'];
            $this->user_type         = $_SESSION['user_type'];
        }        
    }

    //THIS FUNCTION UPDATES REGULAR USER INFORMATIONS(PROFILE)
    public function update_regular_user_profile(){

        //   $details      = [
        //     "first_name"         => 
        //     "last_name"          => 
        //     "middle_name"        => 
        //     "date_of_birth"      => 
        //     "place_of_birth"     => 
        //     "gender"             => 
        //     "marital_status"     => 
        //     "City_providence"    => 
        //     "email"              => 
        //     "country"            => 
        //     "number"             => 
        //     "password"           => 
        //     "image"              => 
        //   ];

        //   $condition    = " `user_id` = '$user_id' ";
        //   $updateQuery  = CustomSql::update_array($details, $condition, $table);
    }

    //THIS FUNCTION UPDATES HEALTH CARE PROFILE BASIC INFORMATION
    public function update_health_care_basic_info($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'health_care');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION UPDATES THE HEALTH CARE SECURITY INFORMATION
    public function update_health_care_security_profile($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'users_security');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION UPDATES REGULAR USER PROFILE BASIC INFORMATION
    public function update_regular_user_basic_info($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'regular_users');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS FUNCTION UPDATES REGULAR USER SECURITY INFORMATION
    public function update_regular_user_security_profile($details, $identity){
        $query    = CustomSql::update_array($details, $identity, 'users_security');
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }



}


