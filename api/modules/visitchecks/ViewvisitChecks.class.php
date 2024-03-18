
<?php
//SubModule Identity
define('MODULE_VISITCHECKS_HANDLER_ID', '10020240314211538');
define('SUB_VIEWVISITCHECKS', '10020240314211541');
define('SUB_NAME_VIEWVISITCHECKS', 'ViewvisitChecks');
Auth::module_function_registration(SUB_VIEWVISITCHECKS, SUB_NAME_VIEWVISITCHECKS, MODULE_VISITCHECKS_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages VisitChecks VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-14
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewvisitChecks {
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
            $auth              = Auth::function_check(SUB_VIEWVISITCHECKS, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get appointment registered items
    public function get_appointment_registered_items($appointmentId){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointment_items` WHERE appointment_id = $appointmentId ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count > 1){
                $row    = mysqli_fetch_assoc($query);
                return $row;
            }else{
                return 404;
            }
        }
    }
}
            