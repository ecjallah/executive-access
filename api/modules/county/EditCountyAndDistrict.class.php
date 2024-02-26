<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("UPDATE_COUNTY_MODULE_ID", '7000');
    define("UPDATE_COUNTY_FUNCTION_ID", '7003');
    define("UPDATE_COUNTY_FUNCTION_NAME", 'Edit County and district');
    Auth::module_function_registration(UPDATE_COUNTY_FUNCTION_ID, UPDATE_COUNTY_FUNCTION_NAME, UPDATE_COUNTY_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class update/edit county and districts. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class EditCountyAndDistrict{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(UPDATE_COUNTY_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method updates created county
        public function update_county($countyId, array $details){
            $identity     = ['column' => ['id'], 'value' => [$countyId]];
            $query        = CustomSql::update_array($details, $identity, 'county');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }

        //This method updates created district
        public function update_district($countyId, $districtId, array $details){
            $identity     = ['column' => ['id', 'county_id'], 'value' => [$districtId, $countyId]];
            $query        = CustomSql::update_array($details, $identity, 'county_district');
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
