<?php 
include_once dirname(__FILE__).'/Autoloader.class.php';

class IdealCandidateRecommender
{
    private string $voterId;
 
    function __construct(string $voterId) 
    {
        $this->voterId = InputCleaner::sanitize($voterId);
    }
 
    public function getIdealCandidate()
    {
        try {
            if ( $this->hasVoterPrioritizedIssues() == false ) {
                return array(
                    "status_code" => 403,
                    "body" => [
                        "message" => "Sorry, voter has not yet prioritized national issues",
                        "dataset" => array()
                    ]
                );
            } else {
                $voterIssuePriorityList = $this->getVoterPrioritizedIssues();
                $partyIssuePriorityList = $this->getPartyPrioritizedIssues();
                $result                 = $this->matchVoterPriorityAgainstCandidatesPriority($voterIssuePriorityList, $partyIssuePriorityList['partyList'], $partyIssuePriorityList['priorityList']);

                return array(
                    "status_code" => 200,
                    "body" => [
                        "message" => "Ok",
                        "dataset" => $result
                    ]
                );
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    private function hasVoterPrioritizedIssues(): bool {
        try {
            $query  = CustomSql::quick_select(" SELECT * FROM `voter_issue_priority` WHERE `voter_id` = '{$this->voterId}' ");

            if ($query->num_rows < 1) {
                return false;
            } else {
                return true;
            }
        } 
        catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function getVoterPrioritizedIssues(): array 
    {
        try {
            $query  = CustomSql::quick_select("SELECT * 
                FROM 
                    `voter_issue_priority` 
                LEFT JOIN 
                    `issues` 
                ON 
                    `voter_issue_priority`.`issue_id` = `issues`.id  
                WHERE 
                    `voter_issue_priority`.`voter_id` = '{$this->voterId}' 
                AND 
                    `voter_issue_priority`.purged = '0' 
            ");

            if ( $query->num_rows < 1) {
                throw new Exception(json_encode(array(
                    "status_code" => 404,
                    "body" => [
                        "message" => "Sorry, you have not yet prioritized any national issues",
                        "dataset" => []
                    ]
                )));
            }

            return $query->fetch_all(MYSQLI_ASSOC);

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function getPartyPrioritizedIssues(): array 
    {
        try {
            $query = CustomSql::quick_select("SELECT * FROM `party_issue_priority` LEFT JOIN `user_accounts` ON `party_issue_priority`.`party_id` = `user_accounts`.user_id WHERE `user_accounts`.user_type = '2' ");

            if ( $query->num_rows < 1 ) {
                throw new Exception(json_encode(array(
                    "status_code" => 404,
                    "body" => [
                        "message" => "Sorry, political parties have not yet prioritized any national issues",
                        "dataset" => []
                    ]
                )));
            }

            $partyList          = array();
            $priorityList       = array();
            $partyIssuePriority = $query->fetch_all(MYSQLI_ASSOC);

            foreach ($partyIssuePriority as $key => $issues) {
                $partyList[$issues['party_id']]     = $issues;
                $priorityList[]  = $issues;
            }

            return array(
                "partyList"     => $partyList,
                "priorityList"  => $priorityList
            );

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function matchVoterPriorityAgainstCandidatesPriority(array $voterIssuePriorityList, array $partyList, array $priorityList): array 
    {
        asort($voterIssuePriorityList);
        asort($priorityList);

        $evaluationResult   =   array();
        $partiesScore       =   array();
        $partyList          =   $partyList;
        $partyPriorityList  =   $priorityList;

        $uniquePartyNames           = array();
        $partyPointsAllotedToIssues = array();
        $voterPointsAllotedToIssues = array();

        // $comparisonProduct  = [];
        // $PartyNames         = array_unique($uniquePartyNames);
        // $finalResult        = array();


        // todo: get voter prioritization/points alloted to issues
        foreach ($voterIssuePriorityList as $key => $voterIssueList) {
            $voterId                =  $voterIssueList['voter_id'];
            $voterIssue             =  $voterIssueList['issue_id'];
            $voterAllocatedPoints   =  (empty($voterIssueList['point_allocated'])) ? 0 : $voterIssueList['point_allocated'];

            $voterPointsAllotedToIssues[] = $voterAllocatedPoints;
        }

        // todo: get party prioritization/points alloted to issues
        foreach ($partyPriorityList as $key => $priorityList) {
            $partyId                =   $priorityList['party_id'];
            $party                  =   $priorityList['full_name'];
            $partyIssue             =   $priorityList['issue_id'];
            $partyAllocatedPoints   =   (empty($priorityList['point_allocated'])) ? 0 : $priorityList['point_allocated'];
            $uniquePartyNames[]     = $party;

            // todo: group points by party
            foreach ($partyList as $key => $party_unique_item) {
                if ( $party_unique_item["full_name"] == $party ) {
                    $partyPointsAllotedToIssues[$party][] = $partyAllocatedPoints;
                }
            }
        }

        $comparisonProduct  = [];
        $PartyNames         = array_unique($uniquePartyNames);
        $finalResult        = array();
        echo '<pre>';
        print_r($partyPointsAllotedToIssues);
        print_r($voterPointsAllotedToIssues);

        // exit; 
        // todo: multiply voter points by parties points
        foreach ($PartyNames as $key => $partyName) {
            $politicalParty = $partyPointsAllotedToIssues[$partyName];
                
            foreach ($voterPointsAllotedToIssues as $key => $voterPoints) {
            
                $comparisonProduct[$partyName][] = $politicalParty[$key] * $voterPoints;
            }
        }

        print_r($comparisonProduct);

        // todo: sum product for each party to get score
        foreach ($PartyNames as $key => $party) {
            $finalResult[$party] = array_sum($comparisonProduct[$party]);
        }

        sort($partyList);


        return array(
            "partyList"                 => $partyList,
            "voter_match_with_party"    => $finalResult,
        );
    }
}


$ICR = new IdealCandidateRecommender("19");
$recommended = $ICR->getIdealCandidate();

print "<pre>";
    print_r( $recommended );
print "</pre>";


?>