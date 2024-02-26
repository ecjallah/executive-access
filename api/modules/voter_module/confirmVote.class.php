<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("VOTE_ACCOUNT_MODULE_ID", '9000');
define("VOTE_ACCOUNT_FUNCTION_ID", '90060');
define("VOTE_ACCOUNT_FUNCTION_NAME", 'Add vote');
Auth::module_function_registration(VOTE_ACCOUNT_FUNCTION_ID, VOTE_ACCOUNT_FUNCTION_NAME, VOTE_ACCOUNT_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Vote-Advisor
 * @_purpose: This class creates an account for voter. 
 * @_version Release: 1.0
 * @_created Date: 3/25/2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Hercules Nimley)
 *      @contact Phone: (+231) 0778636212
 *      @contact Mail: mnimley6@gmail.com
 * *********************************************************************************************************
*/

class confirmVote {

    private $voterId;
    private $partyId;
    private $voterChoice;

    function __construct(string $voterId, string $partyId, string $voterChoice) {
        $this->voterId      =   $voterId;
        $this->partyId      =   $partyId;
        $this->voterChoice  =   ($voterChoice == "yes") ? 1 : 0;
    }

    public function confirmVoteForParty() {
        try {
            $query = CustomSql::quick_select(" UPDATE `vote` 
                SET 
                    `change_mind_after_reviewing_facts` = '{$this->voterChoice}'
                WHERE 
                    `voter_info_id` = '{$this->voterId}' 
                AND 
                    `party_id` = '{$this->partyId}'  
            ");

            return array(
                "status_code" => 200,
                "body" => [
                    "message" => ($this->voterChoice == 1) ? "Vote has been confirmed for party" : "Vote was not confirmed for party",
                    "dataset" => []
                ]
            );


        } 
        catch (\Throwable $th) {
            return array(
                "status_code" => 500,
                "body" => [
                    "message" => $th->getMessage(),
                    "dataset" => array()
                ]
            );
        }
    }
}

// $vote = new confirmVote(voterId: 1, partyId: 2, voterChoice: "no");
// print "<pre>";
//     print_r( $vote->confirmVoteForParty() );
// print "</pre>";

?>