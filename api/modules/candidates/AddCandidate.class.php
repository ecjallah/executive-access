<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("ADD_CANDIDATE_MODULE_ID", '5000');
    define("ADD_CANDIDATE_FUNCTION_ID", '5002');
    define("ADD_CANDIDATE_FUNCTION_NAME", 'Add Candidate');
    Auth::module_function_registration(ADD_CANDIDATE_FUNCTION_ID, ADD_CANDIDATE_FUNCTION_NAME, ADD_CANDIDATE_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class creates new candidate. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class AddCandidate{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(ADD_CANDIDATE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns all created offers
        public function add_new_candidate(array $details){
            $query    = CustomSql::insert_array('candidates', $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
