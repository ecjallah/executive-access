<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    define("PARTY_APPROVAL_MODULE_ID", '7000100');
    define("PARTY_APPROVAL_FUNCTION_ID", '7000102');
    define("PARTY_APPROVAL_FUNCTION_NAME", 'Approve political parties');
    Auth::module_function_registration(PARTY_APPROVAL_FUNCTION_ID, PARTY_APPROVAL_FUNCTION_NAME, PARTY_APPROVAL_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class approves political parties list. 
     * @_version Release: 1.0
     * @_created Date: 04/11/2023
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class PartyApproval{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth                      = Auth::function_check(PARTY_APPROVAL_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission          = $auth;
            }
        }

        //This method updates/approves political party
        public function update_political_party($partyId, array $details){
            CustomSql::commit_off();
            $identity                 = ['column' => ['user_id'], 'value' => [$partyId]];
            $query                    = CustomSql::update_array($details, $identity, 'user_accounts');
            if($query === false){
                CustomSql::rollback();
                return 500;
            }else{
                if($details['approval_status'] == 'approved'){
                    //Update security
                    $securityDetails  = ["blocked" => 0];
                    $securityUpdate   = $this->update_political_party_security($partyId, $securityDetails);
                    CustomSql::save();
                    return $securityUpdate;
                }
                CustomSql::save();
                return 200;
            }
        }

        //This method updates created county
        public function update_political_party_security($partyId, array $details){
            $identity     = ['column' => ['user_id'], 'value' => [$partyId]];
            $query        = CustomSql::update_array($details, $identity, 'users_security');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
