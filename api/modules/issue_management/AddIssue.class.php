<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("ADD_ISSUE_MODULE_ID", '3000100');
    define("ADD_ISSUE_FUNCTION_ID", '3000102');
    define("ADD_ISSUE_FUNCTION_NAME", 'Add/Create new issues');
    Auth::module_function_registration(ADD_ISSUE_FUNCTION_ID, ADD_ISSUE_FUNCTION_NAME, ADD_ISSUE_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class adds/creates ISSUES. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class AddIssue{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(ADD_ISSUE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns all created offers
        public function create_new_issue(array $details){
            $query    = CustomSql::insert_array('issues', $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
