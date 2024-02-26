<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("ELECTION_MANAGEMENT_MODULE_ID", '4000100');
define("ELECTION_MANAGEMENT_MODULE_NAME", 'Election Management Module');
Auth::module_registration(ELECTION_MANAGEMENT_MODULE_ID, ELECTION_MANAGEMENT_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages elections
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class ElectionManagementHandler{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(ELECTION_MANAGEMENT_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_all_elections();
                $this->create_election();
                $this->update_election();
                $this->remove_election();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint returns all election
    protected function view_all_elections(){
        if($this->url == '/api/view-all-elections')
        {
            if($this->method == 'GET'){
                //Get company id
                $viewElection         = new ViewElections();
                $result               = $viewElection->get_all_elections();
                if($result === 500){
                    $response         = new Response(500, "Error returning issues.");
                    $response->send_response();
                }else if($result === 404){
                    $response         = new Response(404, "No issue created at this time.");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Created Issue.", $result);
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint add/creates elections
    protected function create_election(){
        if($this->url == '/api/add-new-election')
        {
            if($this->method == 'POST'){
                $_POST            = json_decode(file_get_contents('php://input'), true);
                $electionTitle    = InputCleaner::sanitize($_POST['election_title']);
                $addElection      = new AddElection();
                if($addElection->permission === 200){
                    if(empty($electionTitle)){
                        $response = new Response(404, "provide the following: election_title");
                        $response->send_response();
                    }else{
                        //Get business id
                        // $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        $details      = [
                            "title"            => $electionTitle,
                            "date"             => Helper::get_current_date(),
                            "added_by"         => $this->userId,
                        ];

                        $result                 = $addElection->create_new_election($details);
                        if($result === 500){
                            $response           = new Response(500, "Error creating new election");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "New election created successfully.", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates/edits election
    protected function update_election(){
        if($this->url == '/api/update-election')
        {
            if($this->method == 'POST'){
                $_POST            = json_decode(file_get_contents('php://input'), true);
                $electionId       = InputCleaner::sanitize($_POST['election_id']);
                $electionTitle    = InputCleaner::sanitize($_POST['election_title']);
                $editElection     = new EditElection();
                if($editElection->permission === 200){
                    if(empty($electionTitle)){
                        $response = new Response(404, "provide the following: election_id, election_title");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId             = Helper::get_business_id($this->userId, $this->account_character);
                        $details                = ["title" => $electionTitle];
                        $result                 = $editElection->update_election($electionId, $details);
                        if($result === 500){
                            $response           = new Response(500, "Error updating election.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "Election updated successfully..", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This function removes elction
    protected function remove_election(){
        if($this->url == '/api/remove-election')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $electionId            = InputCleaner::sanitize($_POST['election_id']);
                $offerStatus           = 1;
                //Get business id
                $businessId            = Helper::get_business_id($this->userId, $this->account_character);
                $election              = new DeleteElection();
                if($election->permission === 200){
                    $details           = ["deleted"  => $offerStatus];
                    $removeIssue       = $election->update_election_remove_status($electionId, $details);
                    if($removeIssue === 500){
                        $response      = new Response(500, "Error removing election.");
                        $response->send_response();
                    }else{
                        $response      = new Response(200, "Election removed successfully.");
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

(new ElectionManagementHandler);

