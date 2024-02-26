<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("PARTY_MANAGEMENT_MODULE_ID", '7000100');
define("PARTY_MANAGEMENT_MODULE_NAME", 'Party Management');
Auth::module_registration(PARTY_MANAGEMENT_MODULE_ID, PARTY_MANAGEMENT_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages political parties.
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class PartyManagementHandler{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(PARTY_MANAGEMENT_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_all_pending_parties();
                $this->view_all_approved_parties();
                $this->update_party_status();
                $this->block_and_unblock_party();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint returns all pending approval parties
    protected function view_all_pending_parties(){
        if($this->url == '/api/view-all-pending-parties')
        {
            if($this->method == 'GET'){
                //Get company id
                $viewParties              = new ViewParties();
                if($viewParties->permission === 200){
                    $result               = $viewParties->get_all_pending_approval_parties();
                    if($result === 500){
                        $response         = new Response(500, "Error returning pending parties.");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, "No pending parties.");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "All pending parties.", $result);
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

    //This endpoint returns all approval parties
    protected function view_all_approved_parties(){
        if($this->url == '/api/view-all-approved-parties')
        {
            if($this->method == 'GET'){
                //Get company id
                $viewParties              = new ViewParties();
                if($viewParties->permission === 200){
                    $result               = $viewParties->get_all_approved_parties();
                    if($result === 500){
                        $response         = new Response(500, "Error returning approved parties.");
                        $response->send_response();
                    }else if($result === 404){
                        $response         = new Response(404, "No approved parties.");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "All approved parties.", $result);
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

    //This endpoint updates/edit/approves party
    protected function update_party_status(){
        if($this->url == '/api/update-party-approval-status')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $partyId               = InputCleaner::sanitize($_POST['party_id']);
                $status                = InputCleaner::sanitize($_POST['status']);
                $partyApproval         = new PartyApproval();
                if($partyApproval->permission === 200){
                    if(empty($partyId)||empty($status)){
                        $response = new Response(404, "provide the following: party_id, status");
                        $response->send_response();
                    }else{
                        if($status == 'approved' || $status == 'rejected' || $status == 'pending'){
                            $details                = ["approval_status" => $status];
                            $result                 = $partyApproval->update_political_party($partyId, $details);
                            if($result === 500){
                                $response           = new Response(500, "Error updating party approval status.");
                                $response->send_response();
                            }else{
                                $response           = new Response(200, "Party approval status updated successfully..", $result);
                                $response->send_response();
                            }
                        }else{
                            $response = new Response(404, "status can only be approved or rejected");
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

    //This endpoint blocks/unblocks party
    protected function block_and_unblock_party(){
        if($this->url == '/api/block-and-unblock-political-party')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $partyId               = InputCleaner::sanitize($_POST['party_id']);
                $status                = InputCleaner::sanitize($_POST['status']);
                $partyApproval         = new PartyApproval();
                if($partyApproval->permission === 200){
                    if(empty($partyId)||empty($status)){
                        $response = new Response(404, "provide the following: party_id, status");
                        $response->send_response();
                    }else{
                        $sqlStatus      = intval(0);
                        if($status == 'block'){
                            $sqlStatus = 1;
                        }else if($status == 'unblock'){
                            $sqlStatus = '0 ';
                        }
                        $details                = ["blocked" => $sqlStatus];
                        $result                 = $partyApproval->update_political_party_security($partyId, $details);
                        if($result === 500){
                            $response           = new Response(500, "Error updating party $status status.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "Party $status successfully..", $result);
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
}
(new PartyManagementHandler);

