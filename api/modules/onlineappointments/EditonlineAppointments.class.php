<?php
//SubModule Identity
define('MODULE_ONLINEAPPOINTMENTS_HANDLER_ID', '10020240416123315');
define('SUB_EDITONLINEAPPOINTMENTS', '10020240416123320');
define('SUB_NAME_EDITONLINEAPPOINTMENTS', 'Edit online Appointments');
Auth::module_function_registration(SUB_EDITONLINEAPPOINTMENTS, SUB_NAME_EDITONLINEAPPOINTMENTS, MODULE_ONLINEAPPOINTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages OnlineAppointments VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-16
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class EditonlineAppointments {
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
            $auth              = Auth::function_check(SUB_EDITONLINEAPPOINTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates ministry appointment setting
    public function update_appointment_settings($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "appointment_settings");
        if($query === false){
            return 500;
        }else{
            if($query === 1){
                return 200;
            }else{
                return 400;
            }
        }
    }

    //This method updats online appointment directly
    public function update_direct_online_appointments($details, $identity){
        $query    = CustomSql::update_array($details, $identity, "appointments");
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //This method updates online appointments and generate appointment token
    public function update_online_appointment($details){
        //Genrate and send appointment token
        $helper          = new Helper();
        $appointmentCode = $helper->generate_appointment_verification_code($details['company_id'], $details['id'], $details['number']);
        return $appointmentCode;
    }
}