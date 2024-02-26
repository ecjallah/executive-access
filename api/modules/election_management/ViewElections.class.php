<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    define("VIEW_ELECTION_MODULE_ID", '4000100');
    define("VIEW_ELECTION_FUNCTION_ID", '4000101');
    define("VIEW_ELECTION_FUNCTION_NAME", 'View Elections');
    Auth::module_function_registration(VIEW_ELECTION_FUNCTION_ID, VIEW_ELECTION_FUNCTION_NAME, VIEW_ELECTION_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class View/shows elections. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class ViewElections{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(VIEW_ELECTION_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns all elections
        public function get_all_elections(){
            $query        = CustomSql::quick_select(" SELECT * FROM `election_type` WHERE `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[] = [
                            "id"        => $row['id'],
                            "status"    => $row['status'],
                            "title"     => $row['title'],
                            "date"      => $row['date'],
                            "added_by"  => $row['added_by']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns election by id
        public function get_elections_by_id($electionId){
            $query        = CustomSql::quick_select(" SELECT * FROM `election_type` WHERE id = $electionId AND `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count      = $query->num_rows;
                if($count === 1){
                    $row    = mysqli_fetch_assoc($query);
                    $data = [
                        "id"        => $row['id'],
                        "status"    => $row['status'],
                        "title"     => $row['title'],
                        "date"      => $row['date'],
                        "added_by"  => $row['added_by']
                    ];
                    return $data;
                }else{
                    return 404;
                }
            }
        }
    }
