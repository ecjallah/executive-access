<?php

//SubModule Identity
define('MODULE_ONLINE_APPOINTMENTS_HANDLER_ID', '10020240416123315');
define('SUB_ADDONLINEAPPOINTMENTS', '10020240416123324');
define('SUB_NAME_ADDONLINEAPPOINTMENTS', 'Add online Appointments');
Auth::module_function_registration(SUB_ADDONLINEAPPOINTMENTS, SUB_NAME_ADDONLINEAPPOINTMENTS, MODULE_ONLINE_APPOINTMENTS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages OnlineAppointments ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-04-16
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class AddonlineAppointments {
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
            $auth              = Auth::function_check(SUB_ADDONLINEAPPOINTMENTS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method sets default appointment settings for all executive members
    public function set_default_appointment_settings($companyId, $departmentId){
        $todayDate            = Helper::get_current_date();
        $queryResult          = [];
        //Get all department executives
        $departmentExecutives = new ViewexecutiveList();
        $executiveList        = $departmentExecutives->return_department_executives_($companyId, $departmentId);
        foreach ($executiveList as $exective) {
            $details    = [
                "ministry_id"     => $companyId,
                "department_id"   => $departmentId,
                "executive_id"    => $exective['id'],
                "start_time"      => "00:00:00",
                "end_time"        => "00:00:00",
                "open_solt"       => "0",
                "added_by"        => $this->userId,
                "added_date"      => $todayDate
            ];
            $queryResult[]    = CustomSql::insert_array("appointment_settings", $details);
        }

        if(in_array(false, $queryResult)){
            CustomSql::rollback();
            return 500;
        }else{
            CustomSql::save();
            return 200;
        }
    }

    //This method sets default appointment settings for an executive member
    public function set_default_executive_appointment_settings($companyId, $departmentId, $executiveId){
        $todayDate            = Helper::get_current_date();
        $details              = [
            "ministry_id"     => $companyId,
            "department_id"   => $departmentId,
            "executive_id"    => $executiveId,
            "start_time"      => "00:00:00",
            "end_time"        => "00:00:00",
            "open_solt"       => "0",
            "added_by"        => $this->userId,
            "added_date"      => $todayDate
        ];
        $query    = CustomSql::insert_array("appointment_settings", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}