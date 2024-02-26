<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("ISSUE_PRIORITIZATION_MODULE_ID", '9000');
define("ISSUE_PRIORITIZATION_FUNCTION_ID", '90040');
define("ISSUE_PRIORITIZATION_FUNCTION_NAME", 'Store Voter Issues Prioritization');
Auth::module_function_registration(ISSUE_PRIORITIZATION_FUNCTION_ID, ISSUE_PRIORITIZATION_FUNCTION_NAME, ISSUE_PRIORITIZATION_MODULE_ID);

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

class notImplementedException extends Exception {};

class PrioritizeIssue {

    private $voter_id;
    private $issues;

    function __construct(string $voter_id, array $issues) {
        $this->voter_id = $voter_id;
        $this->issues   = $issues;
    }

    public function recordVoterPrioritizationOfIssues() {
        try {
            $this->assignPointsToIssues();

            $recommender = new IdealCandidateRecommender($this->voter_id);
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
        catch (\notImplementedException $th) {
            return array(
                "status_code" => 501,
                "body" => [
                    "message" => $th->getMessage(),
                    "dataset" => array()
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

    private function assignPointsToIssues(): bool {
        try {
            
            foreach ($this->issues as $key => $issue) {
                $issueId  = $issue["id"];
                $points   = $issue["points"];

                $query = CustomSql::quick_select(" INSERT INTO `voter_issue_priority`(`voter_id`, `issue_id`, `point_allocated`) VALUES('{$this->voter_id}', '{$issueId}', '{$points}') ");
                
                if (Db::$conn->affected_rows < 1) {
                    throw new notImplementedException;
                } 
            }

            return true;
        } 
        catch (\notImplementedException $th) {
            throw new notImplementedException("One voter issue not added");
        }
        catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
?>