<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("REMOVE_ELECTION_MODULE_ID", '4000100');
    define("REMOVE_ELECTION_FUNCTION_ID", '4000104');
    define("REMOVE_ELECTION_FUNCTION_NAME", 'Delete/Remove Election');
    Auth::module_function_registration(REMOVE_ELECTION_FUNCTION_ID, REMOVE_ELECTION_FUNCTION_NAME, REMOVE_ELECTION_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class removes/delete election. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class DeleteElection{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;
                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(REMOVE_ELECTION_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method updates offer REMOVE/DELETES status
        public function update_election_remove_status($electionId, $details){
            $identity     = ['column' => ['id'], 'value' => [$electionId]];
            $query        = CustomSql::update_array($details, $identity, 'election_type');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
