<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

// Module Identity
define("VOTER_MODULE_ID", '9000');
define("VOTER_MODULE_NAME", 'Voter Management');
Auth::module_registration(VOTER_MODULE_ID, VOTER_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the staff management. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class VoterHandler {

    private $method;
    private $url;

    function __construct(){
        $this->method  = $_SERVER['REQUEST_METHOD'];
        $this->url     = $_SERVER['REQUEST_URI'];
        $this->AddVoter();
        $this->GetPoliticalParties();
        $this->voteForParty();
        $this->confirmPartyVote();
        $this->GetNationalIssues();
        $this->VoterIssuePrioritizer();
        $this->RecommendPartiesToVoter();
    }

    public function AddVoter() {
        if($this->url == '/api/add-voter')
        {
            if ($this->method == 'POST') {
                $_POST          = json_decode(file_get_contents('php://input'), true);

                if ( empty($_POST['mobile']) || empty($_POST['dob']) || empty($_POST['occupation']) || empty($_POST['gender']) || empty($_POST['county']) ) {
                    $response = new Response(404, "Please send the required data(mobile, dob, occupation, gender, county)");
                    $response->send_response();
                }
                else {
                    $mobile         = InputCleaner::sanitize($_POST['mobile']);
                    $dob            = InputCleaner::sanitize($_POST['dob']);
                    $occupation     = InputCleaner::sanitize($_POST['occupation']);
                    $gender         = InputCleaner::sanitize($_POST['gender']);
                    $county         = InputCleaner::sanitize($_POST['county']);

                    CustomSql::commit_off();

                    $voter = new AddVoter($mobile, $dob, $occupation, $gender, $county);
                    $voterResult = $voter->add();

                    if ($voterResult["status_code"] == 500) {
                        $response = new Response(500, $voterResult["body"]["message"]);
                        $response->send_response();
                    } 
                    else {
                        CustomSql::save();
                        $response = new Response($voterResult["status_code"], $voterResult["body"]["message"], $voterResult["body"]["dataset"]);
                        $response->send_response();
                    }
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    public function GetPoliticalParties() {
        if($this->url == '/api/get-political-parties')
        {
            if ($this->method == 'GET') {
                $_GET           =   json_decode(file_get_contents('php://input'), true);
                $party          =   new GetParties();
                $partyResult    =   $party->getPartyList();

                if ($partyResult["status_code"] == 500) {
                    $response = new Response(500, $partyResult["body"]["message"]);
                    $response->send_response();
                } 
                else {
                    $response = new Response($partyResult["status_code"], $partyResult["body"]["message"], $partyResult["body"]["dataset"]);
                    $response->send_response();
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    public function voteForParty() {
        if($this->url == '/api/vote-for-party')
        {
            if ($this->method == 'POST') {
                $_POST          = json_decode(file_get_contents('php://input'), true);

                if ( empty($_POST['voterId']) || empty($_POST['partyId']) || empty($_POST['county']) ) {
                    $response = new Response(404, "Please send the required data(voterId, partyId, county)");
                    $response->send_response();
                }
                else {
                    $voterId    = InputCleaner::sanitize($_POST['voterId']);
                    $partyId    = InputCleaner::sanitize($_POST['partyId']);
                    $county     = InputCleaner::sanitize($_POST['county']);

                    CustomSql::commit_off();

                    $VoteForParty = new Vote($voterId, $partyId, $county);
                    $voteResult = $VoteForParty->voteForParty();

                    if ($voteResult["status_code"] == 500) {
                        $response = new Response(500, $voteResult["body"]["message"]);
                        $response->send_response();
                    } 
                    else {
                        CustomSql::save();
                        $response = new Response($voteResult["status_code"], $voteResult["body"]["message"], $voteResult["body"]["dataset"]);
                        $response->send_response();
                    }
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    public function confirmPartyVote() {
        if($this->url == '/api/confirm-vote-for-party')
        {
            if ($this->method == 'POST') {
                $_POST          = json_decode(file_get_contents('php://input'), true);

                if ( empty($_POST['voterId']) || empty($_POST['partyId']) || empty($_POST['voterChoice']) ) {
                    $response = new Response(404, "Please send the required data(voterId, partyId, voterChoice)");
                    $response->send_response();
                }
                else if ( !in_array($_POST['voterChoice'], array("yes", "no")) ) {
                    $response = new Response(400, "(voterChoice) must be either: (yes/no) ");
                    $response->send_response();
                }
                else {
                    $voterId        =   InputCleaner::sanitize($_POST['voterId']);
                    $partyId        =   InputCleaner::sanitize($_POST['partyId']);
                    $voterChoice    =   InputCleaner::sanitize($_POST['voterChoice']);

                    CustomSql::commit_off();

                    $VoteForParty = new confirmVote($voterId, $partyId, $voterChoice);
                    $voteResult = $VoteForParty->confirmVoteForParty();

                    if ($voteResult["status_code"] == 500) {
                        $response = new Response(500, $voteResult["body"]["message"]);
                        $response->send_response();
                    } 
                    else {
                        CustomSql::save();
                        $response = new Response($voteResult["status_code"], $voteResult["body"]["message"], $voteResult["body"]["dataset"]);
                        $response->send_response();
                    }
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    public function GetNationalIssues() {
        if($this->url == '/api/get-all-issues')
        {
            if ($this->method == 'GET') {
                $_GET           =   json_decode(file_get_contents('php://input'), true);
                $issues         =   new GetNationalIssues();
                $issuesResult    =   $issues->getIssues();

                if ($issuesResult["status_code"] == 500) {
                    $response = new Response(500, $issuesResult["body"]["message"]);
                    $response->send_response();
                } 
                else {
                    $response = new Response($issuesResult["status_code"], $issuesResult["body"]["message"], $issuesResult["body"]["dataset"]);
                    $response->send_response();
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    public function VoterIssuePrioritizer() {
        if($this->url == '/api/get-voter-issue-prioritization')
        {
            if ($this->method == 'POST') {
                $_POST  = json_decode(file_get_contents('php://input'), true);

                if ( empty($_POST['issues']) || empty($_POST['voter_id']) ) {
                    $response = new Response(404, "Please send the required data(voter_id, issues)");
                    $response->send_response();
                }
                else {
                    $voter_id = InputCleaner::sanitize($_POST['voter_id']);
                    $issues   = InputCleaner::sanitize($_POST['issues']);

                    CustomSql::commit_off();

                    $PrioritizeIssue    =   new PrioritizeIssue($voter_id, $issues);
                    $PrioritizerResult  =   $PrioritizeIssue->recordVoterPrioritizationOfIssues();

                    if ($PrioritizerResult["status_code"] == 500) {
                        $response = new Response(500, $PrioritizerResult["body"]["message"]);
                        $response->send_response();
                    } 
                    else {
                        CustomSql::save();
                        $response = new Response($PrioritizerResult["status_code"], $PrioritizerResult["body"]["message"], $PrioritizerResult["body"]["dataset"]);
                        $response->send_response();
                    }
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }

    public function RecommendPartiesToVoter() {
        if($this->url == '/api/recommend-parties-to-voter')
        {
            if ($this->method == 'POST') {
                $_POST  = json_decode(file_get_contents('php://input'), true);

                if ( empty($_POST['voter_id']) ) {
                    $response = new Response(404, "Please send the required data(voter_id)");
                    $response->send_response();
                }
                else {
                    $voter_id = InputCleaner::sanitize($_POST['voter_id']);

                    CustomSql::commit_off();

                    $RecommendedParties    =   new GetRecommendedParties($voter_id);
                    $RecommendedPartiesResult  =   $RecommendedParties->findMatch();

                    if ($RecommendedPartiesResult["status_code"] == 500) {
                        $response = new Response(500, $RecommendedPartiesResult["body"]["message"]);
                        $response->send_response();
                    } 
                    else {
                        CustomSql::save();
                        $response = new Response($RecommendedPartiesResult["status_code"], $RecommendedPartiesResult["body"]["message"], $RecommendedPartiesResult["body"]["dataset"]);
                        $response->send_response();
                    }
                }
            }
            else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            } 
        }
    }
}

(new VoterHandler);
