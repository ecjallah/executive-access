<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    // header("Allow-Control-Origin: *)");
    define("ADD_COUNTY_MODULE_ID", '7000');
    define("ADD_COUNTY_FUNCTION_ID", '7002');
    define("ADD_COUNTY_FUNCTION_NAME", 'Add/Create new county and districts');
    Auth::module_function_registration(ADD_COUNTY_FUNCTION_ID, ADD_COUNTY_FUNCTION_NAME, ADD_COUNTY_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class adds/creates county and districts.. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class AddCountyAndDistricts{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(ADD_COUNTY_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method creates new county
        public function create_new_county(array $details){
            $query    = CustomSql::insert_array('county', $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
        //This method creates new county district
        public function create_new_county_district(array $details){
            $query    = CustomSql::insert_array('county_district', $details);
            if($query === false){
                return 500;
            }else{
                return 200;
            }
        }
    }
