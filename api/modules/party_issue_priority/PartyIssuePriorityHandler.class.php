<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("PARTY_ISSUES_PRIORITY_MODULE_ID", '6000');
define("PARTY_ISSUES_PRIORITY_MODULE_NAME", 'Political Party Priorities Module');
Auth::module_registration(PARTY_ISSUES_PRIORITY_MODULE_ID, PARTY_ISSUES_PRIORITY_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Party issue Priorities. 
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class PartyIssuePriorityHandler{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(PARTY_ISSUES_PRIORITY_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->return_party_priorities();
                $this->set_party_priority();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint retruns party priorities and issues
    public function return_party_priorities(){
        if($this->url == '/api/get-party-issues-priorities')
        {
            if($this->method == 'GET'){
                $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                $partyPriorities          = new ViewPartyPriorities();
                if($partyPriorities->permission === 200){
                    $result               = $partyPriorities->return_part_priorities_issues($businessId);
                    if($result === 500){
                        $response         = new Response(500, "Error returning party issue priority");
                        $response->send_response();
                    }else{
                        $response         = new Response(200, "Party issues priority details", $result);
                        $response->send_response();
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

    //This endpoint sets party issue priorities
    public function set_party_priority(){
        if($this->url == '/api/set-party-issues-priorities')
        {
            if($this->method == 'POST'){
                $_POST                        = json_decode(file_get_contents('php://input'), true);
                if(empty($_POST['details'])){
                    $response = new Response(404, "please provide: details");
                    $response->send_response();
                }else{
                    $details                  = InputCleaner::sanitize($_POST['details']);
                    $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                    $setPartyPriorities       = new MakeIssuePriority();
                    if($setPartyPriorities->permission === 200){
                        $result               = $setPartyPriorities->set_party_issue_priority($businessId, $details);
                        if($result === 500){
                            $response         = new Response(500, "Error setting party issue priority.");
                            $response->send_response();
                        }else if($result === 404){
                            $response         = new Response(404, "Sorry, some issues has priority.");
                            $response->send_response();
                        }else{
                            $response         = new Response(200, "Issues prioritized successfully.");
                            $response->send_response();
                        }
                    }else{                
                        $response = new Response(301, 'Unauthorized Module: Contact Admin');
                        $response->send_response();
                    }
                }
            }else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            }
        }
    }
   
}

(new PartyIssuePriorityHandler);

