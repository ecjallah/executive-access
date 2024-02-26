<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("REMOVE_CANDIDATE_MODULE_ID", '5000');
    define("REMOVE_CANDIDATE_FUNCTION_ID", '5004');
    define("REMOVE_CANDIDATE_FUNCTION_NAME", 'Remove Candidate');
    Auth::module_function_registration(REMOVE_CANDIDATE_FUNCTION_ID, REMOVE_CANDIDATE_FUNCTION_NAME, REMOVE_CANDIDATE_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class removed candidate from list. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class RemoveCandidate{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;
                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(REMOVE_CANDIDATE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method removed candidate from list
        public function remove_candidate($details, $identity){
            $query        = CustomSql::update_array($details, $identity, 'candidates');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
