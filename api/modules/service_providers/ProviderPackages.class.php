<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
define("HANDLER_OFFERS_MODULE_ID", '4000100');
define("HANDLER_OFFERS_FUNCTION_ID", '4000101');
define("HANDLER_OFFERS_FUNCTION_NAME", 'Service providers package manager');
Auth::module_function_registration(HANDLER_OFFERS_FUNCTION_ID, HANDLER_OFFERS_FUNCTION_NAME, HANDLER_OFFERS_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class manages user interactions with service providers packages. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class ProviderPackages{
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth                      = Auth::function_check(HANDLER_OFFERS_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
            $this->permission          = $auth;
        }
    }

    //This method returns all created offers
    public function get_valid_providers(){
        //Load only business user accounts
        $query        = CustomSql::quick_select(" SELECT a.*, s.* FROM `user_accounts` a JOIN `users_security` s ON a.user_id = s.user_id WHERE a.user_type = 2 ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = [
                        "user_id"   => $row['user_id'],
                        "username"  => $row['username'],
                        "full_name" => $row['full_name'],
                        "address"   => $row['address'],
                        "number"    => $row['number'],
                        "image"     => $row['image']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns all linked and active hospitals
    public function return_linked_hospital($businessId){
        $query         = CustomSql::quick_select(" SELECT * FROM `linked_hospitals` WHERE `ministry_id` = $businessId AND `status` = 'active' ");
        if($query === false){
            return 500;
        }else{
            $count     = $query->num_rows;
            if($count >= 1){
                $data  = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $this->get_vaild_service_provide_by_id($row['hospital_id']);
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //This method returns a linked hospitals
    public function return_linked_hospital_by_id($businessId, $hospitalId){
        $query     = CustomSql::quick_select(" SELECT * FROM `linked_hospitals` WHERE ministry_id = $businessId AND hospital_id = $hospitalId AND status = 'active' ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count == 1){
                $row  = mysqli_fetch_assoc($query);
                return $this->get_vaild_service_provide_by_id($row['hospital_id']);
            }else{
                return 404;
            }
        }
    }

    //This method links hospital to ministry for service 
    public function link_health_service_provider_to_ministry($ministryId, $hospitalId){
        //Check if hospital id is vaild
        $checkServiceProviderId   = $this->get_vaild_service_provide_by_id($hospitalId);
        if($checkServiceProviderId != 500 && $checkServiceProviderId != 404){
            //Hospital id is vaild
            //Check if hospital is already linked to ministry
            $linkChecker     = $this->return_linked_hospital_by_id($ministryId, $hospitalId);
            if($linkChecker === 404){
                $details = [
                    "ministry_id"  => $ministryId,
                    "hospital_id"  => $hospitalId,
                    "date"         => Helper::get_current_date()
                ];
                $query      = CustomSql::insert_array('linked_hospitals', $details);
                if($query === false){
                    return 500;
                }else{
                    return 200;
                }
            }else{
                //Hospital is already linked
                return 400;
            }
        }else{
            //Hospital id is not vaild
            return 400;
        }
    }

    //This method UNLINKS hospital to ministry for service 
    public function unlink_health_service_provider_to_ministry($ministryId, $hospitalId){
        //Check if hospital id is vaild
        $checkServiceProviderId   = $this->get_vaild_service_provide_by_id($hospitalId);
        if($checkServiceProviderId != 500 && $checkServiceProviderId != 404){
            //Hospital id is vaild
            //Check if hospital is already linked to ministry
            $linkChecker     = $this->return_linked_hospital_by_id($ministryId, $hospitalId);
            if($linkChecker == 400){
                //Hospital is already linked
                return 400;
            }else{
                $details      = ["status" => 'unlinked'];
                $identity     = ['column' => ['ministry_id', 'hospital_id'], 'value' => [$ministryId, $hospitalId]];
                $query        = CustomSql::update_array($details, $identity, 'linked_hospitals');
                if($query === false){
                    return 500;
                }else{
                    return 200;
                }
            }
        }else{
            //Hospital id is not vaild
            return 400;
        }
    }

    //This method returns a service provider by id
    public function get_vaild_service_provide_by_id($searchValue){
        $query        = CustomSql::quick_select(" SELECT a.*, s.* FROM `user_accounts` a JOIN `users_security` s ON a.user_id = s.user_id WHERE a.user_type = 2 AND a.user_id = '$searchValue' OR s.username = '$searchValue' OR a.full_name = '$searchValue' ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            if($count >= 1){
                $data = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data = [
                        "user_id"   => $row['user_id'],
                        "username"  => $row['username'],
                        "full_name" => $row['full_name'],
                        "address"   => $row['address'],
                        "number"    => $row['number'],
                        "image"     => $row['image']
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}
