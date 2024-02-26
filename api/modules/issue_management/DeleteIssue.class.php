<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("REMOVE_ISSUE_MODULE_ID", '3000100');
    define("REMOVE_ISSUE_FUNCTION_ID", '3000104');
    define("REMOVE_ISSUE_FUNCTION_NAME", 'Delete/Remove Package');
    Auth::module_function_registration(REMOVE_ISSUE_FUNCTION_ID, REMOVE_ISSUE_FUNCTION_NAME, REMOVE_ISSUE_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class removes/delete issues. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class DeleteIssue{
        function __construct(){
            if(isset($_SESSION['user_id'])){

                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(REMOVE_ISSUE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method updates offer REMOVE/DELETES status
        public function update_issue_remove_status($issueId, $details){
            $identity     = ['column' => ['id'], 'value' => [$issueId]];
            $query        = CustomSql::update_array($details, $identity, 'issues');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
