<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";

//SubModule Identity
define('MODULE_DASHBOARD_HANDLER_ID', '10020231007220318');
define('SUB_VIEWDASHBOARD', '10020231007220321');
define('SUB_NAME_VIEWDASHBOARD', 'Viewdashboard');
Auth::module_function_registration(SUB_VIEWDASHBOARD, SUB_NAME_VIEWDASHBOARD, MODULE_DASHBOARD_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Dashboard VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-07
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Viewdashboard {
    public $user_type;
    public $userId;
    public $account_character;
    public $method;
    public $url;
    public $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('SUB_VIEWDASHBOARD', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get from give database
    public function get_voter_report($businessId, $countyId = null, $precintId = null, $pollingCenterId = null){
        //GET ALL PRESIDENTIAL CANDIDATES
        $candidateInfo       = new ViewCandidate();
        $result              = $candidateInfo->get_all_candidate_list(1);
        if(is_array($result)){
            $dataResult      = [];
            $totalVotes      = [];
            foreach ($result as $value) {
                $candidateId        = $value['id'];
                $countyCond         = $countyId != null ? "AND `county_id`=$countyId" : "";
                $precinctCond       = $precintId != null ? "AND `precint_id`=$precintId" : "";
                $pollingCenterCond  = $pollingCenterId != null ? "AND `center_id`=$pollingCenterId" : "";
                $query       = CustomSql::quick_select(" SELECT * FROM `candidate_votes` WHERE `candidate_id` = $candidateId $countyCond $precinctCond $pollingCenterCond");
                
                if($query === false){
                    $dataResult[]    = 500;
                }else{
                    $count           = $query->num_rows;
                    if($count >= 1){
                        while ($row  = $query->fetch_assoc()) {
                            $details = [
                                "candidate_info"  => $value,
                                "vote_value"      => $row['value']
                            ];
                            $totalVotes[] = $row['value'];
                            $dataResult['candidates'][]  = $details;
                        }
                    }else{
                        $totalVotes[] = 0;
                        $dataResult['candidates'][]      = [
                            "candidate_info"  => $value,
                            "vote_value"      => 0
                        ];
                    }
                }
            }
            //Get invaild votes
            $dataResult['total_vote']    = [
                "full_name"   => "Total Votes",
                "vote_value"  => array_sum($totalVotes)
            ];
            $totalInvalid = $this->get_invaild_votes_per_county($countyId, $precintId, $pollingCenterId);
            $dataResult['invaild_vote']  = $totalInvalid;
            $dataResult['candidates'][]  = [
                "candidate_info" => [
                    "full_name" =>"Invalid Votes",
                    "id"        =>"99",
                ],
                "vote_value" => $totalInvalid['vote_value']
            ];
            return $dataResult;
        }else{
            return $result;
        }
        
    }
    
    //This method returns invaild votes per county, precints and centers
    public function get_invaild_votes_per_county($countyId, $precintId, $pollingCenterId){
        $countyCond         = $countyId != null ? "AND `county_id`=$countyId" : "";
        $precinctCond       = $precintId != null ? "AND `precint_id`=$precintId" : "";
        $pollingCenterCond  = $pollingCenterId != null ? "AND `center_id`=$pollingCenterId" : "";
        $query              = CustomSql::quick_select("SELECT * FROM `candidate_votes` WHERE `candidate_id` = 99 $countyCond $precinctCond $pollingCenterCond");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $data          = [];
                while ($row    = $query->fetch_assoc()) {
                    $data = [
                        "full_name"   => "Invaild Votes",
                        "vote_value"  => $row['value']
                    ];
                }
                return $data;
            }else{
                $data = [
                    "full_name"   => "Invaild Votes",
                    "vote_value"  => 0
                ];
                return $data;
            }
        }
        
    }

    //This method returns polling centers report
    public function get_polling_center_report(){
        //Get all polling places
        $pollingCenters    = new ViewpollingManagement();
        $allPollingCenters = $pollingCenters->return_all_polling_centers();
        return $allPollingCenters;
    }

    //This method returns county vote report
    public function get_county_vote_report($businessId){
        //Get all registred county
        // $allCounties  = 
    }
}