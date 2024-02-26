<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("REMOVE_COUNTY_MODULE_ID", '7000');
    define("REMOVE_COUNTY_FUNCTION_ID", '7004');
    define("REMOVE_COUNTY_FUNCTION_NAME", 'Remove County and districts');
    Auth::module_function_registration(REMOVE_COUNTY_FUNCTION_ID, REMOVE_COUNTY_FUNCTION_NAME, REMOVE_COUNTY_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class removes/delete county and districts. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class RemoveCountyAndDistrict{
        function __construct(){
            if(isset($_SESSION['user_id'])){

                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;
                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(REMOVE_COUNTY_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method updates REMOVE/DELETES county
        public function remove_county_status($issueId, $details){
            $identity     = ['column' => ['id'], 'value' => [$issueId]];
            $query        = CustomSql::update_array($details, $identity, 'county');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }

        //This method updates REMOVE/DELETES county DISTRICTS
        public function update_issue_remove_status($countyId, $districtId, $details){
            $identity     = ['column' => ['id', 'county_id'], 'value' => [$districtId, $countyId]];
            $query        = CustomSql::update_array($details, $identity, 'county_district');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
