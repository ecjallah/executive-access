<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("VIEW_CANDIDATE_MODULE_ID", '5000');
    define("VIEW_CANDIDATE_FUNCTION_ID", '5001');
    define("VIEW_CANDIDATE_FUNCTION_NAME", 'View Candidate');
    Auth::module_function_registration(VIEW_CANDIDATE_FUNCTION_ID, VIEW_CANDIDATE_FUNCTION_NAME, VIEW_CANDIDATE_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class View/shows candidate. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class ViewCandidate{
        public $user_type;
        public $userId;
        public $account_character;
        public $method;
        public $url;
        public $permission;
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(VIEW_CANDIDATE_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns all candidates
        public function get_candidate_list($businessId, $electionType = null){
            $condition    = " ";
            if($electionType != null){
                $condition= " AND election_type_id = $electionType ";
            }
            $query        = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE `party_id` = $businessId AND `deleted` = 0 $condition ORDER BY id DESC ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[] = [
                            'id'                => $row['id'],
                            'party_id'          => $row['party_id'],
                            'election_type_id'  => $row['election_type_id'],
                            'position'          => $row['position'],
                            'first_name'        => $row['first_name'],
                            'middle_name'       => $row['middle_name'],
                            'last_name'         => $row['last_name'],
                            'full_name'         => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                            'county'            => $row['county']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns candidate by id
        public function get_candidate_by_id($businessId, $candidateId){
            $query        = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE `party_id` = $businessId AND `id` = $candidateId AND `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count        = $query->num_rows;
                if($count === 1){
                    $row      = mysqli_fetch_assoc($query);
                    $data     = [
                        'id'                => $row['id'],
                        'party_id'          => $row['party_id'],
                        'election_type_id'  => $row['election_type_id'],
                        'first_name'        => $row['first_name'],
                        'position'          => $row['position'],
                        'middle_name'       => $row['middle_name'],
                        'last_name'         => $row['last_name'],
                        'full_name'         => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                        'county'            => $row['county']
                    ];
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns candidate by id
        public function get_candidate($candidateId){
            $query        = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE `id` = $candidateId AND `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count        = $query->num_rows;
                if($count === 1){
                    $row      = mysqli_fetch_assoc($query);
                    $data     = [
                        'id'                => $row['id'],
                        'party_id'          => $row['party_id'],
                        'election_type_id'  => $row['election_type_id'],
                        'first_name'        => $row['first_name'],
                        'position'          => $row['position'],
                        'middle_name'       => $row['middle_name'],
                        'last_name'         => $row['last_name'],
                        'full_name'         => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                        'county'            => $row['county']
                    ];
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns all generic candidates
        public function get_all_candidate_list($electionType = null){
            $condition    = " ";
            if($electionType != null){
                $condition= " AND election_type_id = $electionType ";
            }
            $query        = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE `deleted` = 0 $condition ORDER BY id DESC ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[] = [
                            'id'                => $row['id'],
                            'party_id'          => $row['party_id'],
                            'election_type_id'  => $row['election_type_id'],
                            'position'          => $row['position'],
                            'first_name'        => $row['first_name'],
                            'middle_name'       => $row['middle_name'],
                            'last_name'         => $row['last_name'],
                            'full_name'         => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'],
                            'county'            => $row['county']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
    }
