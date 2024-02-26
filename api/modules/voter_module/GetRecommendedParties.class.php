<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("VOTE_ACCOUNT_MODULE_ID", '9000');
define("VOTE_ACCOUNT_FUNCTION_ID", '90070');
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

class GetRecommendedParties {

    private $voterId;

    function __construct(string $voterId) {
        $this->voterId      =   $voterId;
    }

    public function findMatch() {
        try {

            $recommender = new IdealCandidateRecommender($this->voterId);
            $recommended = $recommender->getIdealCandidate();
            $result      = is_array($recommended) === true ? $recommended : json_decode($recommended, true);

            if ($result["status_code"] != 200)
            {
                return $result;
            }

            return array(
                "status_code" => 200,
                "body" => [
                    "message" => "Ok",
                    "dataset" => $result["body"]["dataset"]
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

// $vote = new GetRecommendedParties(voterId: 2);
// print "<pre>";
//     print_r( $vote->findMatch() );
// print "</pre>";

?>