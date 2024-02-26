<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("COUNTY_MODULE_ID", '7000');
define("COUNTY_MODULE_NAME", 'County Module');
Auth::module_registration(COUNTY_MODULE_ID, COUNTY_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages COUNTIES
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class CountyHandler{
    function __construct(){
        $this->method                  = $_SERVER['REQUEST_METHOD'];
        $this->url                     = $_SERVER['REQUEST_URI'];
        if(!isset($_SESSION['user_id'])){
            $this->view_all_counties();
        }else if(isset($_SESSION['user_id'])){
            $this->user_type           = $_SESSION['user_type'];
            $this->userId              = $_SESSION['user_id'];
            $this->account_character   = $_SESSION['account_character'];
            $moduelCheck               = Auth::module_security(COUNTY_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_all_counties();
                $this->view_district_by_id();
                $this->create_county();
                $this->create_county_districts();
                $this->update_county();
                $this->update_district();
                $this->remove_county();
                $this->remove_district();
            }else if($moduelCheck != 200){
                $this->view_all_counties();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }
        // else{
        //     Logout::log_user_out();
        // }
    }

    //This endpoint returns all counties
    protected function view_all_counties(){
        if($this->url == '/api/view-all-counties')
        {
            if($this->method == 'GET'){
                //Get company id
                $viewCounties               = new ViewCountyAndDistricts();
                // if($viewCounties->permission === 200){
                    $result                 = $viewCounties->get_all_counties_and_districts();
                    if($result === 500){
                        $response           = new Response(500, "Error returning counties.");
                        $response->send_response();
                    }else if($result === 404){
                        $response           = new Response(404, "No counties created at this time.");
                        $response->send_response();
                    }else{
                        $response           = new Response(200, "Created counties.", $result);
                        $response->send_response();
                    }
                // }else{
                //     $response = new Response(301, 'Unauthorized Module: Contact Admin.');
                //     $response->send_response();
                // }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns a district by the county id
    protected function view_district_by_id(){
        if(strpos($this->url, '/api/view-district-by-id') !== false)
        {
            if($this->method == 'GET'){
                $id                         = InputCleaner::sanitize($_GET['id']);
                //Get company id
                $businessId                 = Helper::get_business_id($this->userId, $this->account_character);
                $viewCounties               = new ViewCountyAndDistricts();
                if($viewCounties->permission === 200){
                    $result                 = $viewCounties->return_county_districts($id);
                    if($result === 500){
                        $response           = new Response(500, "Error returning district.");
                        $response->send_response();
                    }else if($result === 404){
                        $response           = new Response(404, "There is no district related to this county id.");
                        $response->send_response();
                    }else{
                        $response           = new Response(200, "District details.", $result);
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin.');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint add/creates new county
    protected function create_county(){
        if($this->url == '/api/add-new-county')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $addNewCounty          = new AddCountyAndDistricts();
                if($addNewCounty->permission === 200){
                    if(empty($title)){
                        $response = new Response(404, "provide the following: title");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        $details      = [
                            "title"         => $title,
                            "date"          => Helper::get_current_date(),
                        ];
                        $result                 = $addNewCounty->create_new_county($details);
                        if($result === 500){
                            $response           = new Response(500, "Error creating new county");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "New county created successfully.", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint add/creates new district under a county using the county id
    protected function create_county_districts(){
        if($this->url == '/api/add-new-county-district')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $countyId              = InputCleaner::sanitize($_POST['county_id']);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $adddistrict           = new AddCountyAndDistricts();
                if($adddistrict->permission === 200){
                    if(empty($title)){
                        $response = new Response(404, "provide the following: county_id, title");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId   = Helper::get_business_id($this->userId, $this->account_character);
                        $details      = [
                            "county_id"         => $countyId,
                            "district_title"    => $title,
                            "date"              => Helper::get_current_date()
                        ];
                        $result                 = $adddistrict->create_new_county_district($details);
                        if($result === 500){
                            $response           = new Response(500, "Error creating new district.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "New district created successfully.", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates/edits county
    protected function update_county(){
        if($this->url == '/api/update-county')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $countId               = InputCleaner::sanitize($_POST['county_id']);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $editCounty            = new EditCountyAndDistrict();
                if($editCounty->permission === 200){
                    if(empty($countId)||empty($title)){
                        $response = new Response(404, "provide the following: title, county_id");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId             = Helper::get_business_id($this->userId, $this->account_character);
                        $details                = ["title" => $title];
                        $result                 = $editCounty->update_county($countId, $details);
                        if($result === 500){
                            $response           = new Response(500, "Error updating county.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "County updated successfully..", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint updates/edits county
    protected function update_district(){
        if($this->url == '/api/update-county-district')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $countId               = InputCleaner::sanitize($_POST['county_id']);
                $title                 = InputCleaner::sanitize($_POST['title']);
                $districtId            = InputCleaner::sanitize($_POST['district_id']);
                $editCountyDistrict    = new EditCountyAndDistrict();
                if($editCountyDistrict->permission === 200){
                    if(empty($countId)||empty($title)){
                        $response = new Response(404, "provide the following: title, county_id, district_id");
                        $response->send_response();
                    }else{
                        //Get business id
                        $businessId             = Helper::get_business_id($this->userId, $this->account_character);
                        $details                = ["district_title" => $title];
                        $result                 = $editCountyDistrict->update_district($countId, $districtId, $details);
                        if($result === 500){
                            $response           = new Response(500, "Error updating district.");
                            $response->send_response();
                        }else{
                            $response           = new Response(200, "District updated successfully..", $result);
                            $response->send_response();
                        }
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This function removes county
    protected function remove_county(){
        if($this->url == '/api/remove-county')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $countyId              = InputCleaner::sanitize($_POST['county_id']);
                $status                = 1;
                //Get business id
                $businessId            = Helper::get_business_id($this->userId, $this->account_character);
                $removeCounty          = new RemoveCountyAndDistrict();
                if($removeCounty->permission === 200){
                    $details           = ["deleted"  => $status];
                    $removeIssue       = $removeCounty->remove_county_status($countyId, $details);
                    if($removeIssue === 500){
                        $response      = new Response(500, "Error removing county.");
                        $response->send_response();
                    }else{
                        $response      = new Response(200, "County removed successfully.");
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This function removes district
    protected function remove_district(){
        if($this->url == '/api/remove-district')
        {
            if($this->method == 'POST'){
                $_POST                 = json_decode(file_get_contents('php://input'), true);
                $countyId              = InputCleaner::sanitize($_POST['county_id']);
                $districtId            = InputCleaner::sanitize($_POST['district_id']);
                $status                = 1;
                //Get business id
                $businessId            = Helper::get_business_id($this->userId, $this->account_character);
                $removeDistrict        = new RemoveCountyAndDistrict();
                if($removeDistrict->permission === 200){
                    $details           = ["deleted"  => $status];
                    $removeIssue       = $removeDistrict->update_issue_remove_status($countyId, $districtId, $details);
                    if($removeIssue === 500){
                        $response      = new Response(500, "Error removing district.");
                        $response->send_response();
                    }else{
                        $response      = new Response(200, "District removed successfully.");
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, 'Unauthorized Module: Contact Admin');
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }
}
(new CountyHandler);