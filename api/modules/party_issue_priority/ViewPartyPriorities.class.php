<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// header("Allow-Control-Origin: *)");
define("VIEW_PARTY_PRIORITIES_MODULE_ID", '6000');
define("VIEW_PARTY_PRIORITIES_FUNCTION_ID", '6001');
define("VIEW_PARTY_PRIORITIES_FUNCTION_NAME", 'View Party Priorities');
Auth::module_function_registration(VIEW_PARTY_PRIORITIES_FUNCTION_ID, VIEW_PARTY_PRIORITIES_FUNCTION_NAME, VIEW_PARTY_PRIORITIES_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class View/shows party priorities. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class ViewPartyPriorities{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth                      = Auth::function_check(VIEW_PARTY_PRIORITIES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission          = $auth;
        }
    }

    //This method retruns party priorities
    public function return_part_priorities_issues($partyId){
        $query                    = CustomSql::quick_select(" SELECT i.*, p.* FROM `party_issue_priority` p JOIN `issues` i ON i.id = p.issue_id WHERE p.party_id = $partyId ORDER BY p.point_allocated DESC ");
        if($query === false){
            return 500;
        }else{
            $count                = $query->num_rows;
            $issues               = new ViewIssues();
            if($count >= 1){
                $data             = [];
                $totalDollarValue = 0;
                while ($row = mysqli_fetch_assoc($query)) {
                    $totalDollarValue += $issues->convert_base_value_to_dollar($row['base_value']);
                    $data['priorities'][] = [
                        "id"                 => $row['id'],
                        "issue_id"           => $row['issue_id'],
                        "point_allocated"    => $row['point_allocated']==null?0:$row['point_allocated'],
                        "money_allocated"    => $issues->convert_base_value_to_dollar($row['point_allocated']),
                        "added_by"           => $row['added_by'],
                        "issue_details"      => $issues->get_issue_by_id($row['issue_id'])
                    ];
                }
                $data['total_dollar_value'] = $totalDollarValue;
                return $data;
            }else{
                return $issues->get_all_issues();
            }
        }
    }
}
