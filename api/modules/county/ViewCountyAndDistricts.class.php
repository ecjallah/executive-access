<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    define("VIEW_COUNTY_MODULE_ID", '7000');
    define("VIEW_COUNTY_FUNCTION_ID", '7001');
    define("VIEW_COUNTY_FUNCTION_NAME", 'View  Countires and districts');
    Auth::module_function_registration(VIEW_COUNTY_FUNCTION_ID, VIEW_COUNTY_FUNCTION_NAME, VIEW_COUNTY_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class View/shows counties and districts. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class ViewCountyAndDistricts{
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
                $auth              = Auth::function_check(VIEW_COUNTY_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns only counties
        public function get_all_counties_(){
            $query         = CustomSql::quick_select(" SELECT * FROM `county` WHERE `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[]    = [
                            "id"         => $row['id'],
                            "title"      => $row['title'],
                            "date"       => $row['date'],
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns all created counties
        public function get_all_counties_and_districts(){
            $query         = CustomSql::quick_select(" SELECT * FROM `county` WHERE `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[]    = [
                            "id"         => $row['id'],
                            "title"      => $row['title'],
                            "date"       => $row['date'],
                            "districts"  => $this->return_county_districts($row['id'])
                        ];
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns county by id
        public function get_county_by_id($countyId){
            $query          = CustomSql::quick_select(" SELECT * FROM `county` WHERE id = $countyId AND `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count      = $query->num_rows;
                if($count === 1){
                    $row    = mysqli_fetch_assoc($query);
                    $data   = [
                        "id"         => $row['id'],
                        "title"      => $row['title'],
                        "date"       => $row['date'],
                        "districts"  => $this->return_county_districts($row['id'])
                    ];
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns all related districts from a given county
        public function return_county_districts($countyId){
            $query         = CustomSql::quick_select(" SELECT * FROM `county_district` WHERE county_id = $countyId ");
            if($query === false){
                return 500;
            }else{
                $count     = $query->num_rows;
                if($count >= 1){
                    $data  = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $data[] = $row;
                    }
                    return $data;
                }else{
                    return 404;
                }
            }
        }
    }
