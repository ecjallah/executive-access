<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// header("Allow-Control-Origin: *)");
define("MARK_PARTY_PRIORITIES_PRIORITIES_MODULE_ID", '6000');
define("MARK_PARTY_PRIORITIES_PRIORITIES_FUNCTION_ID", '6002');
define("MARK_PARTY_PRIORITIES_PRIORITIES_FUNCTION_NAME", 'Set Part Issue Priorities');
Auth::module_function_registration(MARK_PARTY_PRIORITIES_PRIORITIES_FUNCTION_ID, MARK_PARTY_PRIORITIES_PRIORITIES_FUNCTION_NAME, MARK_PARTY_PRIORITIES_PRIORITIES_MODULE_ID);

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

class MakeIssuePriority{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;
            //Check if user has right to access this class(this module function)
            $auth                      = Auth::function_check(MARK_PARTY_PRIORITIES_PRIORITIES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission          = $auth;
        }
    }

    //This method sets issue priorities
    public function set_party_issue_priority($partyId, $details){
        CustomSql::commit_off();
        $date                 = Helper::get_current_date();
        $result               = [];
        $totalValue           = [];
        foreach ($details as $data) {
            //Chech issue status
            $issueChecker      = $this->check_if_party_has_been_prioritized($partyId, $data['id']);
            if($issueChecker === 200){
                $totalValue[]  = $data['value'];
                //Add party item priority
                $issueDetails  = [
                    "party_id"         => $partyId,
                    "issue_id"         => $data['id'],
                    "point_allocated"  => $data['value']==0?'0':$data['value']/1000000,
                    "date"             => $date
                ]; 
                $query        = CustomSql::insert_array('party_issue_priority', $issueDetails);
                if($query === false){
                    $result[] = 500;
                }else{
                    $result[] = 200;
                }
            }else{
                $result[]     = 404;
            }
        }
        if(count($totalValue) <= 1000000){
            if(in_array(500, $result)){
                CustomSql::rollback();
                return 500;
            }else if(in_array(404, $result)){
                CustomSql::rollback();
                return 404;
            }else{
                CustomSql::save();
                return 200;
            }
        }else{
            CustomSql::rollback();
            return 400;
        }
    }

    //This method checkes if an issue is aleady prioritized
    public function check_if_party_has_been_prioritized($partyId, $issueId){
        $query     = CustomSql::quick_select(" SELECT * FROM `party_issue_priority` WHERE party_id = $partyId AND issue_id = $issueId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count == 1){
                //Issue has been set
                return 404;
            }else{
                //Issue has not been set
                return 200;
            }
        }
    }
}
