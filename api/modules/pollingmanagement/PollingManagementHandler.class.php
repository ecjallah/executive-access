<?php
    session_start();
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('POLLINGMANAGEMENT_HANDLER_ID', 10020231003184411);
    define('POLLINGMANAGEMENT_HANDLER', 'Polling Management');
    Auth::module_registration(POLLINGMANAGEMENT_HANDLER_ID, POLLINGMANAGEMENT_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages Polling Management operations.
        * @_version Release: 1.0
        * @_created Date: 2023-10-03
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class PollingManagementHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type             = $_SESSION["user_type"];
                $this->userId                = $_SESSION["user_id"];
                $this->account_character     = $_SESSION["account_character"];
                $this->method                = $_SERVER["REQUEST_METHOD"];
                $this->url                   = $_SERVER["REQUEST_URI"];
                $moduelCheck                 = Auth::module_security(POLLINGMANAGEMENT_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get_all_precincts();
                    $this->add_new_precincts();
                    $this->update_precincts();
                    $this->remove_precincts();
                    $this->get_precinct_by_id();
                    $this->get_precinct_by_county();

                    //Polling centers
                    $this->add_new_polling_center();
                    $this->update_polling_centers();
                    $this->remove_polling_center();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET PRECINCTS ENDPOINT
        public function get_all_precincts(){
            if($this->url == "/api/get-precincts")
            {
                if($this->method == "GET"){
                    $businessId               = Helper::get_business_id($this->userId, 'business');
                    $polling                  = new ViewpollingManagement();
                    if($polling->permission === 200){
                        $result               = $polling->get_precincts();
                        if($result === 500){
                            $response         = new Response(500, " Error returning precincts. ");
                            $response->send_response();
                        }else if($result === 404){
                            $response         = new Response(404, " There is no precinct at the time.");
                            $response->send_response();
                        }else{
                            $response         = new Response(200, "Precincts", $result);
                            $response->send_response();
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //THIS ENDPOINT ADDS/CREATES NEW PRECINCTS
        public function add_new_precincts(){
            if($this->url == "/api/add-new-precinct")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $addprecinct             = new AddpollingManagement();
                    if($addprecinct->permission === 200){
                        if(empty($_POST['code']) || empty($_POST['title']) || empty($_POST['county_id'])){
                            $response        = new Response(404, " Please provide the following keys: code, title and county_id");
                            $response->send_response();
                        }else{
                            $details         = [
                                "code"        => InputCleaner::sanitize($_POST['code']),
                                "title"       => InputCleaner::sanitize($_POST['title']),
                                "county_id"   => InputCleaner::sanitize($_POST['county_id']),
                                "district_id" => key_exists('district_id', $_POST) ? InputCleaner::sanitize($_POST['district_id']): null,
                                "date"        => Helper::get_current_date(),
                                "added_by"    => $this->userId
                            ];
                            $result          = $addprecinct->create_new_precinct($details);
                            if($result === 500){
                                $response    = new Response(500, " Error adding/creating precinct. ");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Precinct added successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //GET WITH PARAMS
        protected function get_precinct_by_id(){
            if(strpos($this->url, "/api/get-precinct-details") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, 'business');
                    $polling             = new ViewpollingManagement();
                    if($polling->permission === 200){
                        if(empty($_GET['id'])){
                            $response        = new Response(404, " Please provide the following keys: id");
                            $response->send_response();
                        }else{
                            $id              = InputCleaner::sanitize($_GET['id']);
                            $result          = $polling->get_precinct_details($id);
                            if($result === 500){
                                $response    = new Response(500, " Error returning precinct details. ");
                                $response->send_response();
                            }else if($result === 404){
                                $response    = new Response(404, " Precincts is not avaliable.");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Precinct details", $result);
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                }
            }
        }

        protected function get_precinct_by_county(){
            if(strpos($this->url, "/api/get-precincts-by-county") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, 'business');
                    $polling             = new ViewpollingManagement();
                    if($polling->permission === 200){
                        if(empty($_GET['county-id'])){
                            $response        = new Response(404, " Please provide the following keys: county-id");
                            $response->send_response();
                        }else{
                            $id              = InputCleaner::sanitize($_GET['county-id']);
                            $result          = $polling->get_precincts_by_county($id);
                            if($result === 500){
                                $response    = new Response(500, " Error returning precinct by county. ");
                                $response->send_response();
                            }else if($result === 404){
                                $response    = new Response(404, " Precincts is not avaliable.");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Precinct by county", $result);
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                }
            }
        }

        //THIS ENDPOINT EDITS/UPDATE PRECINCTS
        public function update_precincts(){
            if($this->url == "/api/update-precincts")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $editPrecinct            = new EditpollingManagement();
                    if($editPrecinct->permission === 200){
                        if(empty($_POST['id']) || empty($_POST['code']) || empty($_POST['title']) || empty($_POST['county_id'])){
                            $response        = new Response(404, " Please provide the following keys: id, code, title and county_id");
                            $response->send_response();
                        }else{
                            $precinctsId     = InputCleaner::sanitize($_POST['id']);
                            $details         = [
                                "code"        => InputCleaner::sanitize($_POST['code']),
                                "title"       => InputCleaner::sanitize($_POST['title']),
                                "county_id"   => InputCleaner::sanitize($_POST['county_id']),
                                "date"        => Helper::get_current_date(),
                                "added_by"    => $this->userId
                            ];
                            $identity        = ['column' => ['id'], 'value' => [$precinctsId]];
                            $result          = $editPrecinct->update_precincts($details, $identity);
                            if($result === 500){
                                $response    = new Response(500, " Error updating/editing precinct. ");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Precinct updated successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //THIS ENDPOINT DELETES/REMOVE PRECINCTS
        public function remove_precincts(){
            if($this->url == "/api/remove-precinct")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $deletePrecinct          = new DeletepollingManagement();
                    if($deletePrecinct->permission === 200){
                        if(empty($_POST['id'])){
                            $response        = new Response(404, " Please provide the following keys: id");
                            $response->send_response();
                        }else{
                            $precinctsId     = InputCleaner::sanitize($_POST['id']);
                            $details         = ["status" => 1];
                            $identity        = ['column' => ['id'], 'value' => [$precinctsId]];
                            $result          = $deletePrecinct->delete_precinct($details, $identity);
                            if($result === 500){
                                $response    = new Response(500, " Error removing/deleting precinct. ");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Precinct deleted successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        ///////////////////////////////////////////////////////
            //POLLING CENTER SECTIONS
        ///////////////////////////////////////////////////////

        //THIS ENDPOINT ADDS/CREATES NEW POLLING CENTERS
        public function add_new_polling_center(){
            if($this->url == "/api/add-new-polling-center")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $addPollingcenter        = new AddpollingManagement();
                    if($addPollingcenter->permission === 200){
                        if(empty($_POST['precinct_id']) || empty($_POST['code']) || empty($_POST['title'])){
                            $response        = new Response(404, " Please provide the following keys: code, title and precinct_id");
                            $response->send_response();
                        }else{
                            $details         = [
                                "precinct_id" => InputCleaner::sanitize($_POST['precinct_id']),
                                "code"        => InputCleaner::sanitize($_POST['code']),
                                "title"       => InputCleaner::sanitize($_POST['title']),
                                "date"        => Helper::get_current_date(),
                                "added_by"    => $this->userId
                            ];
                            $result           = $addPollingcenter->create_new_polling_center($details);
                            if($result === 500){
                                $response     = new Response(500, " Error adding/creating polling center. ");
                                $response->send_response();
                            }else{
                                $response     = new Response(200, "Polling center added successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //THIS ENDPOINT EDITS/UPDATE POLLING CENTERS
        public function update_polling_centers(){
            if($this->url == "/api/update-polling-center")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $editPollingcenter       = new EditpollingManagement();
                    if($editPollingcenter->permission === 200){
                        if(empty($_POST['id']) || empty($_POST['code']) || empty($_POST['title']) || empty($_POST['precinct_id'])){
                            $response        = new Response(404, " Please provide the following keys: id, code, title and precinct_id");
                            $response->send_response();
                        }else{
                            $pollingCenterId = InputCleaner::sanitize($_POST['id']);
                            $details         = [
                                "code"          => InputCleaner::sanitize($_POST['code']),
                                "title"         => InputCleaner::sanitize($_POST['title']),
                                "precinct_id"   => InputCleaner::sanitize($_POST['precinct_id']),
                                "date"          => Helper::get_current_date(),
                                "added_by"      => $this->userId
                            ];
                            $identity        = ['column' => ['id'], 'value' => [$pollingCenterId]];
                            $result          = $editPollingcenter->update_polling_center($details, $identity);
                            if($result === 500){
                                $response    = new Response(500, " Error updating/editing polling center. ");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Polling center updated successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //THIS ENDPOINT DELETES/REMOVE POLLING CENTER
        public function remove_polling_center(){
            if($this->url == "/api/remove-polling-center")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $businessId              = Helper::get_business_id($this->userId, $this->account_character);
                    $deletePrecinct          = new DeletepollingManagement();
                    if($deletePrecinct->permission === 200){
                        if(empty($_POST['id'])){
                            $response        = new Response(404, " Please provide the following keys: id");
                            $response->send_response();
                        }else{
                            $centerId        = InputCleaner::sanitize($_POST['id']);
                            $details         = ["status" => 1];
                            $identity        = ['column' => ['id'], 'value' => [$centerId]];
                            $result          = $deletePrecinct->delete_polling_center($details, $identity);
                            if($result === 500){
                                $response    = new Response(500, " Error removing/deleting polling center. ");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Polling center deleted successfully.");
                                $response->send_response();
                            }
                        }
                    }else{
                        $response = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        ///////////////////////////////////////////////////////
            //POLLING CENTER SECTIONS ENDS
        ///////////////////////////////////////////////////////
    }
new PollingManagementHandler();         