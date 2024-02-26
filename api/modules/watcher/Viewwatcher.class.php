<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
//SubModule Identity
define('MODULE_WATCHER_HANDLER_ID', '10020231005170803');
define('SUB_VIEWWATCHER', 10020231005170806);
define('SUB_NAME_VIEWWATCHER', 'Viewwatcher');
Auth::module_function_registration(SUB_VIEWWATCHER, SUB_NAME_VIEWWATCHER, MODULE_WATCHER_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Watcher VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-05
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Viewwatcher {
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
            $auth              = Auth::function_check(SUB_VIEWWATCHER, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get from give database
    public function get_poll_watcher_centers($businessId, $watcherId){
        $query              = CustomSql::quick_select(" SELECT * FROM `watcher_assignment` WHERE business_id = $businessId AND user_id = $watcherId ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            $getPrecincts   = new ViewpollingManagement();
            if($count >= 1){
                $data       = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $row["precinct_info"] = $getPrecincts->get_precincts();
                    $data[] = $row;
                }
                $data['count'] = $count;
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns all precincts alone with their centers
    public function get_precincts_and_centers($businessId, $watcherId, $type = null){
        $query              = CustomSql::quick_select(" SELECT * FROM `watcher_assignment` WHERE business_id = $businessId AND user_id = $watcherId GROUP BY precinct_id");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            $getPrecincts   = new ViewpollingManagement();
            if($count >= 1){
                $data                  = [];
                $centerSubmitCheck     = new Addwatcher();
                while ($row = mysqli_fetch_assoc($query)) {
                    if($type == 'unsubmitted'){
                        $row["precinct_info"]        = $getPrecincts->get_precincts($row['precinct_id']);
                        $row['assigned_centers']     = $this->get_unsubmitted_precinct_centers($businessId, $watcherId, $row['precinct_id']);
                        $data[]                      = $row;
                    }else{
                        $row["precinct_info"]        = $getPrecincts->get_precincts($row['precinct_id']);
                        $row['assigned_centers']     = $this->get_assigned_precinct_centers($watcherId, $row['precinct_id']);
                        $data[]                      = $row;
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    private function get_unsubmitted_precinct_centers($businessId, $watcherId, $precinctId){
        $query              = CustomSql::quick_select(" SELECT * FROM `watcher_assignment` WHERE user_id = '$watcherId' AND precinct_id = $precinctId ");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            $getPrecincts   = new ViewpollingManagement();
            if($count >= 1){
                $data       = [];
                $tester     = [];
                $centerSubmitCheck     = new Addwatcher();
                while ($row = mysqli_fetch_assoc($query)) {
                    //Check if polling center has been submitted for.
                    $checkerResult               = $centerSubmitCheck->polling_center_submit_checker($businessId, $row['polling_center_id']);
                    if($checkerResult['status'] == 200){
                        if($checkerResult['data'] == 0){
                            $pollingPlaceDetails = $getPrecincts->get_polling_center_by_id($row['polling_center_id']);
                            $data[]              = $pollingPlaceDetails;
                        }
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    private function get_assigned_precinct_centers($watcherId, $precinctId){
        $query              = CustomSql::quick_select(" SELECT * FROM `watcher_assignment` WHERE user_id='$watcherId' AND precinct_id = $precinctId");
        if($query === false){
            return 500;
        }else{
            $count          = $query->num_rows;
            $getPrecincts   = new ViewpollingManagement();
            if($count >= 1){
                $data       = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $pollingPlaceDetails = $getPrecincts->get_polling_center_by_id($row['polling_center_id']);
                    $data[] = $pollingPlaceDetails;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
    //THIS METHOD RETURNS ASSIGNED POLLING CENTER COUNTS
    public function get_assigned_polling_center_center_counts($businessId, $watcherId){
        $query  = CustomSql::quick_select(" SELECT COUNT(*) FROM `watcher_assignment` WHERE business_id = $businessId AND user_id = $watcherId ");
        if($query === false){
            return 500;
        }else{
            return ["count"  => $query->fetch_assoc()['COUNT(*)']];
        }
    }

    //THIS METHOD RETURNS SUBMITTED POLLING CENTER COUNTS
    public function get_submitted_polling_center_counts($businessId, $watcherId){
        $query  = CustomSql::quick_select(" SELECT * FROM `candidate_votes` WHERE business_id = $businessId AND staff_id = $watcherId GROUP BY center_id ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["count"  => $count];
        }
    }

    //THIS METHOD SEARCH POLLING CENTERS BY DISTRICT, CODE NAME
    public function search_polling_centers($searchValue){
        $query         = CustomSql::quick_select(" SELECT * FROM `polling_centers` WHERE `code` = '$searchValue' OR title LIKE '%$searchValue%' ");
        if($query === false){
            return 500;
        }else{
            $precinct  = new ViewpollingManagement();
            $count     = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = $query->fetch_assoc()) {
                    $row['precinct_details'] = $precinct->get_precincts($row['precinct_id']);
                    $data[]                  = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //THIS METHOD SEARCH precints CENTERS BY DISTRICT, CODE NAME
    public function search_precints_centers($searchValue){
        $query         = CustomSql::quick_select(" SELECT p.*, c.title AS county FROM `precincts` p JOIN `county` c ON p.county_id = c.id WHERE c.`title` LIKE '%$searchValue%' OR p.`code` LIKE '%$searchValue%' OR p.`title` LIKE '%$searchValue%' ");
        if($query === false){
            return 500;
        }else{
            $precinct  = new ViewpollingManagement();
            $count     = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = $query->fetch_assoc()) {
                    $row['polling_center'] = $precinct->return_precinct_polling_centers($row['id']);
                    $data[] = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
    // THIS METHOD RETURNS CANDIDATE BY POLLING CENTER
    public function get_center_candidate_list($electionType, $county_id = null, $district_id = null){
        $condition     = "";
        if(isset($county_id) && !isset($district_id)){
            $condition = " `election_type_id` = $electionType AND `county` = $county_id AND ";
        }
        if(isset($county_id) && isset($district_id)){
            $condition = " `election_type_id` = $electionType AND `county` = $county_id AND `district_id` = $district_id AND ";
        }else{
            $condition = " `election_type_id` = $electionType AND ";
        }
        $query         = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE $condition `deleted` = 0 ORDER BY id ASC ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = [
                        'id'                => $row['id'],
                        'party_id'          => $row['party_id'],
                        'election_type_id'  => $row['election_type_id'],
                        'position'          => $row['position'],
                        'first_name'        => $row['first_name'],
                        'middle_name'       => $row['middle_name'],
                        'last_name'         => $row['last_name'],
                        'full_name'         => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                        'county'            => $row['county']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}