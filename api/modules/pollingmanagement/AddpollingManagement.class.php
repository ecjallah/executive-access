<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
//SubModule Identity
define('MODULE_POLLINGMANAGEMENT_HANDLER_ID', '10020231003184411');
define('SUB_ADDPOLLINGMANAGEMENT', '10020231003184420');
define('SUB_NAME_ADDPOLLINGMANAGEMENT', 'Add Polling Management');
Auth::module_function_registration(SUB_ADDPOLLINGMANAGEMENT, SUB_NAME_ADDPOLLINGMANAGEMENT, MODULE_POLLINGMANAGEMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages PollingManagement ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-03
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class AddpollingManagement {
    private $user_type;
    private $userId;
    public $permission;
    private $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->user_type             = $_SESSION["user_type"];
            $this->userId                = $_SESSION["user_id"];
            $this->account_character     = $_SESSION["account_character"];
            $this->permission            = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_ADDPOLLINGMANAGEMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method add/creates new precinct
    public function create_new_precinct($details){
        $query    = CustomSql::insert_array("precincts", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //This method adds new polling center
    public function create_new_polling_center($details){
        $query    = CustomSql::insert_array("polling_centers", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }

    //THIS METHOD ASSIGNS USERS/STAFFS TO A POLLING CENTER
    public function assign_staff_to_polling_center($businessId, $userId, $centerIds){
        $results                      = [];
        foreach ($centerIds as $value) {
            $centerId                 = $value['center_id'];
            $precintId                = $value['precinct_id'];
            //Check if user is already assigned
            $assignmentchecker        = $this->check_user_polling_assignment($businessId, $userId, $centerId);
            if(is_array($assignmentchecker) && $assignmentchecker['data'] == 0){
                $details              = [
                    "business_id"       => $businessId,
                    "user_id"           => $userId,
                    "precinct_id"       => $precintId,
                    "polling_center_id" => $centerId,
                    "date"              => Helper::get_current_date(),
                    "assigned_by"       => $this->userId
                ];
                $query         = CustomSql::insert_array('watcher_assignment',$details);

                if($query === false){
                    $results[] = 500;
                }else{
                    $results[] = 200;
                }
            }else{
                $results[]     = 404;
            }
        }
        if(in_array(500, $results)){
            CustomSql::rollback();
            return 500;
        }else if(in_array(404, $results)){
            CustomSql::save();
            return 404;
        }else{
            CustomSql::save();
            return 200;
        }
    }

    //THIS METHOD UNASSIGNS USERS/STAFFS TO A POLLING CENTER
    public function unassign_staff_to_polling_center($businessId, $userId, $centerIds){
        $results                      = [];
        foreach ($centerIds as $value) {
            //Check if user is already assigned
            $assignmentchecker        = $this->check_user_polling_assignment($businessId, $userId, $value);
            if(is_array($assignmentchecker) && $assignmentchecker['data'] == 1){
                $condition     = " business_id = $businessId AND user_id = $userId AND polling_center_id = $value";
                $query         = CustomSql::delete_sql('watcher_assignment', $condition);
                if($query === false){
                    $results[] = 500;
                }else{
                    $results[] = 200;
                }
            }else{
                $results[]     = 404;
            }
        }
        if(in_array(500, $results)){
            CustomSql::rollback();
            return 500;
        }else if(in_array(404, $results)){
            CustomSql::save();
            return 404;
        }else{
            CustomSql::save();
            return 200;
        }
    }

    //This method checks if a user is already assignd to a given polling center
    public function check_user_polling_assignment($businessId, $userId, $pollingCenterId){
        $query    = CustomSql::quick_select(" SELECT * FROM `watcher_assignment` WHERE business_id = $businessId AND user_id = $userId AND polling_center_id = $pollingCenterId ");
        if($query === false){
            return ['status' => 500];
        }else{
            return ['status' => 200, 'data' => $query->num_rows];
        }
    }
}