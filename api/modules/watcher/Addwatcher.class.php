<?php
//SubModule Identity
define('WATCHER_MODULE_HANDLER_ID', '10020231005170803');
define('SUB_ADDWATCHER', '10020231005170812');
define('SUB_NAME_ADDWATCHER', 'Add watcher');
Auth::module_function_registration(SUB_ADDWATCHER, SUB_NAME_ADDWATCHER, WATCHER_MODULE_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Watcher ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-05
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Addwatcher {
    public $user_type;
    public $userId;
    public $account_character;
    public $method;
    public $url;
    public $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->user_type             = $_SESSION["user_type"];
            $this->userId                = $_SESSION["user_id"];
            $this->account_character     = $_SESSION["account_character"];
            $this->permission            = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_ADDWATCHER, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method add given record
    public function add_candidate_vote($details){
        //Check if selected data has been inserted for a candidate at a particular center
        $candidateVoteCheck = $this->watcher_data_checker($details['business_id'], $details['candidate_id'], $details['center_id']);
        if($candidateVoteCheck['status'] == 200 && $candidateVoteCheck['data'] == 0){
            $query    = CustomSql::insert_array("candidate_votes", $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }else{
            return 400;
        }
    }

    //This methode checks if a watcher has submitted a given data before
    public function watcher_data_checker($businessId, $candidateId, $centerId){
        $query    = CustomSql::quick_select(" SELECT * FROM `candidate_votes` WHERE business_id = $businessId AND candidate_id = $candidateId AND center_id = $centerId ");
        if($query === false){
            return ['status' => 500];
        }else{
            return ['status' => 200, 'data' => $query->num_rows];
        }
    }

    public function polling_center_submit_checker($businessId, $centerId){
        $query    = CustomSql::quick_select(" SELECT * FROM `candidate_votes` WHERE business_id = $businessId AND center_id = $centerId ");
        if($query === false){
            return ['status' => 500];
        }else{
            return ['status' => 200, 'data' => $query->num_rows];
        }
    }
}