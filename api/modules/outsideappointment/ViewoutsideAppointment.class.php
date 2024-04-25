<?php
//SubModule Identity
define('MODULE_OUTSIDEAPPOINTMENT_HANDLER_ID', '10020240416153100');
define('SUB_VIEWOUTSIDEAPPOINTMENT', '10020240416153103');
define('SUB_NAME_VIEWOUTSIDEAPPOINTMENT', 'View outside Appointment');
Auth::module_function_registration(SUB_VIEWOUTSIDEAPPOINTMENT, SUB_NAME_VIEWOUTSIDEAPPOINTMENT, MODULE_OUTSIDEAPPOINTMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages OutsideAppointment VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-16
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewoutsideAppointment {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId      = $_SESSION["user_id"];
            $this->user_type   = $_SESSION["user_type"];
            $this->permission  = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_VIEWOUTSIDEAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all ministries
    public function get_all_ministries(){
        $query          = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE user_type != 1 AND approval_status = 'approved' ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = [
                        "user_id"           => $row['user_id'],
                        "full_name"         => $row['full_name'],
                        "address"           => $row['address'],
                        "city_providence"   => $row['city_providence'],
                        "country"           => $row['country'],
                        "image"             => $row['image']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}       