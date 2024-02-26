<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
//SubModule Identity
define('MODULE_POLLING_MANAGEMENT_HANDLER_ID', '10020231003184411');
define('SUB_VIEWPOLLINGMANAGEMENT', '10020231003184414');
define('SUB_NAME_VIEWPOLLINGMANAGEMENT', 'View Polling Management');
Auth::module_function_registration(SUB_VIEWPOLLINGMANAGEMENT, SUB_NAME_VIEWPOLLINGMANAGEMENT, MODULE_POLLING_MANAGEMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages PollingManagement VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-03
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewpollingManagement {
    private $user_type;
    private $userId;
    public $permission;
    private $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->user_type             = $_SESSION["user_type"];
            $this->userId                = $_SESSION["user_id"];
            $this->account_character     = $_SESSION["account_character"];
            $this->permission            = null;
            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_VIEWPOLLINGMANAGEMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method retruns all precincts
    public function get_precincts($precinctId = null){
        $condition       = '';
        if($precinctId != null){
            $condition   = " p.`id` = $precinctId AND ";
        }
        $query          = CustomSql::quick_select(" SELECT p.*, c.title as county_name FROM `precincts` p JOIN county c ON p.county_id=c.id WHERE $condition p.status != 1 ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            $data       = [];
            if($count >= 1){
                while ($row = $query->fetch_assoc()) {
                    if ($precinctId !== null ) {
                        $data = $row;
                    } else {
                        $data[]    = $row;
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method retruns all precincts
    public function get_precincts_by_county($countyId){
        $query          = CustomSql::quick_select(" SELECT p.*, c.title as county_name FROM `precincts` p JOIN county c ON p.county_id=c.id WHERE p.county_id=$countyId AND p.status != 1 ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            $data       = [];
            if($count >= 1){
                while ($row = $query->fetch_assoc()) {
                    $data[]    = $row;
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns precinct details
    public function get_precinct_details($id){
        $query                          = CustomSql::quick_select(" SELECT p.*, c.title as county_name FROM `precincts` p JOIN county c ON p.county_id=c.id WHERE p.id = $id AND p.status != 1 ");
        if($query === false){
            return 500;
        }else{
            $count                        = $query->num_rows;
            if($count === 1){
                $row                    = mysqli_fetch_assoc($query);
                $row['polling_centers'] = $this->return_precinct_polling_centers($id);
                return $row;
            }else{
                return 404;
            }
        }
    }

    //THIS METHOD RETURNS ALL POLLING CENTERS
    public function return_all_polling_centers(){
        $query     = CustomSql::quick_select(" SELECT * FROM `polling_centers` WHERE status != 1 ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >=1){
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

    //THIS METHOD RETURNS ALL RELATED POLLING CENTERS RELATED TO A PRECINCT
    public function return_precinct_polling_centers($precinctId){
        $query     = CustomSql::quick_select(" SELECT * FROM `polling_centers` WHERE precinct_id = $precinctId AND status != 1 ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >=1){
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

    //THIS METHOD RETURNS POLLING CENTERS BY ID
    public function get_polling_center_by_id($centerId){
        $query     = CustomSql::quick_select(" SELECT * FROM `polling_centers` WHERE id = $centerId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }

    //THIS METHOD RETURNS POLLING CENTERS ALONE WITH PRECINCTS AND COUNTY
    public function get_polling_center_county_by_id($centerId){
        $query     = CustomSql::quick_select(" SELECT po.*, pp.* FROM `polling_centers` po JOIN `precincts` pp ON po.precinct_id = pp.id WHERE po.id = $centerId ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count === 1){
                return $query->fetch_assoc();
            }else{
                return 404;
            }
        }
    }
}       