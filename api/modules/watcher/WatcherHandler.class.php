<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    //Module Identity
    define('WATCHER_HANDLER_ID', 10020231005170803);
    define('WATCHER_HANDLER', 'Watcher');
    Auth::module_registration(WATCHER_HANDLER_ID, WATCHER_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages Watcher operations.
        * @_version Release: 1.0
        * @_created Date: 2023-10-05
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

class WatcherHandler {
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
            $this->method                = $_SERVER["REQUEST_METHOD"];
            $this->url                   = $_SERVER["REQUEST_URI"];
            $moduelCheck                 = Auth::module_security(WATCHER_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->add_candidate_votes();
                $this->get_watcher_centers();
                $this->get_watcher_precincts_and_center();
                $this->get_watcher_assigned_counts();
                $this->view_watcher_candidates_list();
                $this->get_watcher_unsubmitted_precincts_and_centers();
            }else{
                $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //GET POLL WATCHER ASSIGNED CENTERS ENDPOINT
    public function get_watcher_centers(){
        if(strpos($this->url, "/api/get-watcher-centers") !== false)
        {
            if($this->method == "GET"){
                $watcherId                = key_exists('staff-id', $_GET) ? InputCleaner::sanitize($_GET['staff-id']) : $this->userId;
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $watcherCenter            = new Viewwatcher();
                if($watcherCenter->permission === 200){
                    $result               = $watcherCenter->get_poll_watcher_centers($businessId, $watcherId);
                    if($result === 500){
                        $response         = new Response(500, " Error returning assignment. ");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, " You are not assigned to any polling center. ");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "All assigned poling centers.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //GET POLL PRECINCTS ASSIGNED CENTERS ENDPOINT
    public function get_watcher_precincts_and_center(){
        if(strpos($this->url, "/api/get-watcher-precincts-and-centers") !== false)
        {
            if($this->method == "GET"){
                $watcherId                = key_exists('staff-id', $_GET) ? InputCleaner::sanitize($_GET['staff-id']) : $this->userId;
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $watcherCenter            = new Viewwatcher();
                if($watcherCenter->permission === 200){
                    $result               = $watcherCenter->get_precincts_and_centers($businessId, $watcherId);
                    if($result === 500){
                        $response         = new Response(500, " Error returning assignment. ");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, " You are not assigned to any polling center. ");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "All assigned poling centers.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //GET WATCHER UNSUBMITTED PRECINCTS AND CENTERS ENDPOINT
    public function get_watcher_unsubmitted_precincts_and_centers(){
        if(strpos($this->url, "/api/get-watcher-unsubmitted-precincts-and-centers") !== false)
        {
            if($this->method == "GET"){
                $watcherId                = key_exists('staff-id', $_GET) ? InputCleaner::sanitize($_GET['staff-id']) : $this->userId;
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $watcherCenter            = new Viewwatcher();
                if($watcherCenter->permission === 200){
                    $result               = $watcherCenter->get_precincts_and_centers($businessId, $watcherId, 'unsubmitted');
                    if($result === 500){
                        $response         = new Response(500, " Error returning assignment. ");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, " You are not assigned to any polling center. ");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "All assigned poling centers.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //GET POLL PRECINCTS ASSIGNED CENTERS ENDPOINT
    public function get_watcher_assigned_counts(){
        if($this->url == "/api/get-watcher-assigned-polling-centers-counts")
        {
            if($this->method == "GET"){
                $businessId                  = Helper::get_business_id($this->userId, $this->account_character);
                $watcherCenter               = new Viewwatcher();
                if($watcherCenter->permission === 200){
                    $watcher_assigned_count  = $watcherCenter->get_assigned_polling_center_center_counts($businessId, $this->userId)['count'];
                    $submited_centers        = $watcherCenter->get_submitted_polling_center_counts($businessId, $this->userId)['count'];
                    $result                  = [
                        "watcher_assigned_count"  => $watcher_assigned_count,
                        "submited_centers"        => $submited_centers,
                        "not_submited_centers"    => intval($watcher_assigned_count)-intval($submited_centers)
                    ];
                    $response                = new Response(200, "All assigned poling centers.", $result);
                    $response->send_response();
                }else{
                    $response = new Response(301, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //THIS ENDPOINT ADDS CANDIDATE VOTES
    public function add_candidate_votes(){
        if($this->url == "/api/add-candidate-votes")
        {
            if($this->method == "POST"){
                $_POST               = json_decode(file_get_contents("php://input"), true);
                $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                if(empty($_POST['center_id']) || empty($_POST['candidate_votes'])){
                    $response        = new Response(404, " Please provide the following keys: center_id and candidate_votes");
                    $response->send_response();
                }else{
                    $centerId        = InputCleaner::sanitize($_POST['center_id']);
                    $candidateVotes  = InputCleaner::sanitize($_POST['candidate_votes']);
                    $addVote         = new Addwatcher();
                    if($addVote->permission === 200){
                        $result      = [];
                        $viewCounty  = new ViewpollingManagement();
                        foreach ($candidateVotes as $value) {
                            $candidateId       = $value['candidate_id'];
                            if($value['candidate_id'] == 'invalid_vote'){
                                $candidateId   = 99;
                            }
                            //get center county id
                            $countyId         = $viewCounty->get_polling_center_county_by_id($centerId)['county_id'];
                            $precintId        = $viewCounty->get_polling_center_by_id($centerId)['precinct_id'];
                            $details = [
                                "business_id"  => $businessId,
                                "candidate_id" => $candidateId,
                                "value"        => $value['vote_value'],
                                "center_id"    => $centerId,
                                "county_id"    => $countyId,
                                "precint_id"   => $precintId,
                                "staff_id"     => $this->userId,
                                "date"         => Helper::get_current_date()
                            ];
                            $result[]    = $addVote->add_candidate_vote($details);
                        }
                        if(in_array(500, $result)){
                            $response    = new Response(500, " Error adding candidate votes. ");
                            $response->send_response();
                        }else if(in_array(400, $result)){
                            $response    = new Response(400, "Sorry, candidate vote has been added for this center.");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Candidate vote added successfully.");
                            $response->send_response();
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    //This method returns all watcher national candidates
    protected function view_watcher_candidates_list(){
        if($this->url == '/api/get-national-candidates-list')
        {
            if($this->method == 'GET'){
                // $_POST                    = json_decode(file_get_contents("php://input"), true);
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $viewCandidate            = new Viewwatcher();
                if($viewCandidate->permission === 200){
                    $countyId             = null;
                    $districtId           = null;
                    $electionId           = 0;
                    $result               = [];
                    if(isset($_GET['county-id'])){
                        //SENTOR
                        $countyId         = InputCleaner::sanitize($_GET['county-id']);
                        $electionId       = 2;
                    }if(isset($_GET['county-id']) && isset($_GET['district-id'])){
                        //RESP
                        $countyId         = InputCleaner::sanitize($_GET['county-id']);
                        $districtId       = InputCleaner::sanitize($_GET['district-id']);
                        $electionId       = 3;
                    }else{
                        $electionId       = 1;
                    }
                    $result               = $viewCandidate->get_center_candidate_list($electionId, $countyId, $districtId);
                    if($result === 500){
                        $response         = new Response(500, "Error returning watcher candidate list.");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, "No candidate at this time.");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "Candidate list.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin.');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }
}
new WatcherHandler();