<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    define("VIEW_PARTIES_MODULE_ID", '7000100');
    define("VIEW_PARTIES_FUNCTION_ID", '7000101');
    define("VIEW_PARTIES_FUNCTION_NAME", 'View political parties');
    Auth::module_function_registration(VIEW_PARTIES_FUNCTION_ID, VIEW_PARTIES_FUNCTION_NAME, VIEW_PARTIES_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class View/shows parties list. 
     * @_version Release: 1.0
     * @_created Date: 04/11/2023
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class ViewParties{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth                      = Auth::function_check(VIEW_PARTIES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission          = $auth;
            }
        }

        //This method returns all created offers
        public function get_all_approved_parties(){
            $query        = CustomSql::quick_select(" SELECT ua.*, us.blocked, us.number, us.username FROM `user_accounts` ua JOIN `users_security` us ON ua.user_id=us.user_id WHERE ua.`user_type` = 2 AND ua.`approval_status` = 'approved' ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data    = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[]    = [
                            "user_id"     => $row['user_id'],
                            "full_name"   => $row['full_name'],
                            "image"       => $row['image'],
                            "number"      => $row['number'],
                            "username"    => $row['username'],
                            "blocked"     => $row['blocked']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns all PEDING APPROVAL parties
        public function get_all_pending_approval_parties(){
            $query        = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_type` = 2 AND `approval_status` = 'pending' ");
            if($query === false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[]    = [
                            "user_id"     => $row['user_id'],
                            "full_name"   => $row['full_name'],
                            "image"       => $row['image']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
        //This method returns a political party
        public function get_political_party_by_id($partyId){
            $query        = CustomSql::quick_select(" SELECT * FROM `user_accounts` WHERE `user_type` = 2 AND `user_id` = $partyId ");
            if($query === false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[]    = [
                            "user_id"     => $row['user_id'],
                            "full_name"   => $row['full_name'],
                            "image"       => $row['image']
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

    }
