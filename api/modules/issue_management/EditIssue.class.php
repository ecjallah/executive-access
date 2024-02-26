<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("UPDATE_ISSUES_MODULE_ID", '3000100');
    define("UPDATE_ISSUES_FUNCTION_ID", '3000103');
    define("UPDATE_ISSUES_FUNCTION_NAME", 'Edit Issues');
    Auth::module_function_registration(UPDATE_ISSUES_FUNCTION_ID, UPDATE_ISSUES_FUNCTION_NAME, UPDATE_ISSUES_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class update/edit Issues. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class EditIssue{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(UPDATE_ISSUES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method updates created offers
        public function update_issue($issueId, array $details){
            $identity     = ['column' => ['id'], 'value' => [$issueId]];
            $query        = CustomSql::update_array($details, $identity, 'issues');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
