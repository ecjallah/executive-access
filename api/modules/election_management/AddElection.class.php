<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("ADD_ELECTION_MODULE_ID", '4000100');
    define("ADD_ELECTION_FUNCTION_ID", '4000102');
    define("ADD_ELECTION_FUNCTION_NAME", 'Add/create new elections');
    Auth::module_function_registration(ADD_ELECTION_FUNCTION_ID, ADD_ELECTION_FUNCTION_NAME, ADD_ELECTION_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class adds/creates ELECTION. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class AddElection{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(ADD_ELECTION_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method create new election
        public function create_new_election(array $details){
            $query    = CustomSql::insert_array('election_type', $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
