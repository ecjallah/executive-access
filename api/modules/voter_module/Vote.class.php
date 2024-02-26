<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("VOTE_ACCOUNT_MODULE_ID", '9000');
define("VOTE_ACCOUNT_FUNCTION_ID", '90050');
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

class Vote {

    private $voterId;
    private $partyId;
    private $county;

    function __construct(string $voterId, string $partyId, string $county) {
        $this->voterId      =   $voterId;
        $this->partyId      =   $partyId;
        $this->county       =   $county;
    }

    public function voteForParty() {
        try {
            $query = CustomSql::quick_select(" INSERT INTO `vote`(`voter_info_id`, `party_id`, `country`) VALUES('{$this->voterId}', '{$this->partyId}', '{$this->county}') ");

            return array(
                "status_code" => 200,
                "body" => [
                    "message" => "Vote has been added for party",
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
    // change_mind_after_reviewing_facts
}


// $vote = new Vote(voterId: 1, partyId: 2, county: "Grand Bassa");
// print "<pre>";
//     print_r( $vote->voteForParty() );
// print "</pre>";

?>