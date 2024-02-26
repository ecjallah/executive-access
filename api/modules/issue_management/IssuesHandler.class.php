<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("ISSUES_MODULE_ID", '3000100');
define("ISSUES_MODULE_NAME", 'Issues Module');
Auth::module_registration(ISSUES_MODULE_ID, ISSUES_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages ISSUES
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class IssuesHandler{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(ISSUES_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_all_issues();
                $this->create_issue();
                $this->update_issues();
                $this->remove_issues();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint returns all issues
    protected function view_all_issues(){
        if($this->url == '/api/view-all-issues')
        {
            if($this->method == 'GET'){
                //Get company id
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $viewIssues               = new ViewIssues();
                if($viewIssues->permission === 200){
                    $result               = $viewIssues->get_all_issues();
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
                    $response = new Response(301, 'Unauthorized Module: Contact Admin.');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint add/creates new issues
    protected function create_issue(){
        if($this->url == '/api/add-new-issue')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $description           = InputCleaner::sanitize($_POST['description']);
                $baseValue             = InputCleaner::sanitize($_POST['base_value']);
                $addIssue              = new AddIssue();
                if($addIssue->permission === 200){
                    if(empty($title) || empty($description) || empty($baseValue)){
                        $response = new Response(404, "provide the following: title, description, base_value");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        $details      = [
                            "issue_title"   => $title,
                            "description"   => $description,
                            "base_value"    => $baseValue,
                            "date"          => Helper::get_current_date(),
                            "added_by"      => $this->userId
                        ];
                        $result                 = $addIssue->create_new_issue($details);
                        if($result === 500){
                            $response           = new Response(500, "Error creating new issue");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "New issue created successfully.", $result);
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

    //This endpoint updates/edits issues
    protected function update_issues(){
        if($this->url == '/api/update-issue')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $issueId               = InputCleaner::sanitize($_POST['issue_id']);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $description           = InputCleaner::sanitize($_POST['description']);
                $baseValue             = InputCleaner::sanitize($_POST['base_value']);
                $editIssues            = new EditIssue();
                if($editIssues->permission === 200){
                    if(empty($title)||empty($description) ||empty($baseValue)){
                        $response = new Response(404, "provide the following: title, description, base_value");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        $details      = [
                            "issue_title"   => $title,
                            "base_value"    => $baseValue,
                            "description"   => $description,
                        ];
                        $result               = $editIssues->update_issue($issueId, $details);
                        if($result === 500){
                            $response           = new Response(500, "Error updating issue.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "Issue updated successfully..", $result);
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

    //This function removes issues
    protected function remove_issues(){
        if($this->url == '/api/remove-issue')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $issueId               = InputCleaner::sanitize($_POST['issue_id']);
                $offerStatus           = 1;
                //Get business id
                $businessId            = Helper::get_business_id($this->userId, $this->account_character);
                $issues                = new DeleteIssue();
                if($issues->permission === 200){
                    $details           = ["deleted"  => $offerStatus];
                    $removeIssue       = $issues->update_issue_remove_status($issueId, $details);
                    if($removeIssue === 500){
                        $response      = new Response(500, "Error removing issue.");
                        $response->send_response();
                    }else{
                        $response      = new Response(200, "Issue removed successfully.");
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
(new IssuesHandler);

