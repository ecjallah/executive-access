<?php
//SubModule Identity
define('MODULE_VISITCHECKS_HANDLER_ID', '10020240314211538');
define('SUB_ADDVISITCHECKS', '10020240314211547');
define('SUB_NAME_ADDVISITCHECKS', 'Add visit Checks');
Auth::module_function_registration(SUB_ADDVISITCHECKS, SUB_NAME_ADDVISITCHECKS, MODULE_VISITCHECKS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages VisitChecks ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-14
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class AddvisitChecks {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_ADDVISITCHECKS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This adds appointment items
    public function add_appointment_items($details){
        $query    = CustomSql::insert_array("appointment_items", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}     