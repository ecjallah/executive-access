<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("CANDIDATES_MODULE_ID", '5000');
define("CANDIDATES_MODULE_NAME", 'Candidate Module');
Auth::module_registration(CANDIDATES_MODULE_ID, CANDIDATES_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Candidates. 
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class CandidatesHandler{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(CANDIDATES_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_candidates_list();   
                $this->lookup_candidates_endpoint();
                $this->add_new_candidate();
                $this->remove_candidates();
                $this->edit_candidates();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint lookup beneficiary
    protected function lookup_candidates_endpoint(){
        if(strpos($this->url, '/api/lookup-candidate') !== false)
        {
            if($this->method == 'GET'){
                $businessId           = Helper::get_business_id($this->userId, $this->account_character);
                $candidateId          = InputCleaner::sanitize($_GET['candidate_id']);
                $candidates           = new ViewCandidate();
                $result               = $candidates->get_candidate_by_id($businessId, $candidateId);
                if($result === 500){
                    $response         = new Response(500, "Error returning candidate.");
                    $response->send_response();
                }else if($result === 404){
                    $response         = new Response(404, "Sorry, the is no candidate with the given id");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Candidate Details", $result);
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns all candidates
    protected function view_candidates_list(){
        if($this->url == '/api/view-candidate-list')
        {
            if($this->method == 'GET'){
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $viewCandidate            = new ViewCandidate();
                if($viewCandidate->permission === 200){
                    $result               = $viewCandidate->get_candidate_list($businessId);
                    if($result === 500){
                        $response         = new Response(500, "Error returning candidate list.");
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
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint create new candidate
    public function add_new_candidate(){
        if($this->url == '/api/add-candidate')
        {
            if($this->method == 'POST'){
                $_POST                  = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['election_type_id']) || empty($_POST['county']) || empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['position'])){
                    $response           = new Response(400, "please provide first_name, middle_name, last_name, election_type_id, county");
                    $response->send_response();
                }else{
                    $firstName          = InputCleaner::sanitize($_POST['first_name']);
                    $middleName         = InputCleaner::sanitize($_POST['middle_name']);
                    $lastName           = InputCleaner::sanitize($_POST['last_name']);
                    $position           = InputCleaner::sanitize($_POST['position']);
                    $electionTypeId     = InputCleaner::sanitize($_POST['election_type_id']);
                    $county             = InputCleaner::sanitize($_POST['county']);
                    $newCandidate       = new AddCandidate();
                    if($newCandidate->permission === 200){
                        //Get business id
                        $businessId     = Helper::get_business_id($this->userId, $this->account_character);
                        $details        = [
                            'party_id'          => $businessId,
                            'election_type_id'  => $electionTypeId,
                            'first_name'        => $firstName,
                            'middle_name'       => $middleName,
                            'last_name'         => $lastName,
                            'position'          => $position,
                            'full_name'         => $firstName.' '.$middleName.' '.$lastName,
                            'county'            => $county,
                            'image'             => '/media/images/default_avatar.png',
                            'date_added'        => gmdate('Y-m-d H:s:i')
                        ];
                        $result          = $newCandidate->add_new_candidate($details);
                        if($result === 500){
                            $response    = new Response(500, "Error adding candidate");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Candidate added successfully", $result);
                            $response->send_response();
                        }
                    }else{
                        $response = new Response(301, 'Unauthorized Module: Contact Admin');
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This endpoint updates/edits candidate
    public function edit_candidates(){
        if($this->url == '/api/edit-candidate')
        {
            if($this->method == 'POST'){
                $_POST                  = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['candidate_id']) || empty($_POST['election_type_id']) || empty($_POST['county']) || empty($_POST['first_name']) || empty($_POST['middle_name']) || empty($_POST['last_name'])){
                    $response           = new Response(400, "please provide first_name, middle_name, last_name, election_type_id, county");
                    $response->send_response();
                }else{
                    $candidateId        = InputCleaner::sanitize($_POST['candidate_id']);
                    $firstName          = InputCleaner::sanitize($_POST['first_name']);
                    $middleName         = InputCleaner::sanitize($_POST['middle_name']);
                    $lastName           = InputCleaner::sanitize($_POST['last_name']);
                    $electionTypeId     = InputCleaner::sanitize($_POST['election_type_id']);
                    $county             = InputCleaner::sanitize($_POST['county']);
                    $position           = InputCleaner::sanitize($_POST['position']);
                    $updateCandidate    = new EditCandidate();
                    if($updateCandidate->permission === 200){
                        //Get business id
                        $businessId     = Helper::get_business_id($this->userId, $this->account_character);
                        $details        = [
                            'party_id'          => $businessId,
                            'election_type_id'  => $electionTypeId,
                            'first_name'        => $firstName,
                            'middle_name'       => $middleName,
                            'last_name'         => $lastName,
                            'full_name'         => $firstName.' '.$middleName.' '.$lastName,
                            'position'          => $position,
                            'county'            => $county
                        ];
                        $identity        = ['column' => ['id','party_id'], 'value' => [$candidateId, $businessId]];
                        $result          = $updateCandidate->update_candidate($details, $identity);
                        if($result === 500){
                            $response    = new Response(500, "Error updating candidate");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Candidate updated successfully", $result);
                            $response->send_response();
                        }
                    }else{
                        $response = new Response(301, 'Unauthorized Module: Contact Admin');
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This function removes candidate
    protected function remove_candidates(){
        if($this->url == '/api/remove-candidate-from-list')
        {
            if($this->method == 'POST'){
                $_POST                   = json_decode(file_get_contents('php://input'), true);
                $candidateId             = InputCleaner::sanitize($_POST['candidate_id']);
                $offerStatus             = 1;
                $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                $candidate               = new RemoveCandidate();
                if($candidate->permission === 200){
                    $details             = ["deleted"  => $offerStatus];
                    $identity            = ['column' => ['party_id', 'id'], 'value' => [$businessId, $candidateId]];
                    $removeCandidate     = $candidate->remove_candidate($details, $identity);
                    if($removeCandidate === 500){
                        $response        = new Response(500, "Error removing candidate.");
                        $response->send_response();
                    }else{
                        $response        = new Response(200, "Candidate removed successfully.");
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }
}

(new CandidatesHandler);

