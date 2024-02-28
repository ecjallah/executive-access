<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('EXECUTIVELIST_HANDLER_ID', 10020240227182744);
    define('EXECUTIVELIST_HANDLER', 'Executive List');
    Auth::module_registration(EXECUTIVELIST_HANDLER_ID, EXECUTIVELIST_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages ExecutiveList operations.
        * @_version Release: 1.0
        * @_created Date: 2024-02-27
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class ExecutiveListHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type            = $_SESSION["user_type"];
                $this->userId               = $_SESSION["user_id"];
                $this->account_character    = $_SESSION["account_character"];
                $this->method               = $_SERVER["REQUEST_METHOD"];
                $this->url                  = $_SERVER["REQUEST_URI"];
                $moduelCheck                = Auth::module_security(EXECUTIVELIST_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->add_executive_member();
                    $this->get_executive_members();
                    $this->executive_get_departments();
                    $this->get_executive_details();
                    $this->update_executive_member();
                    $this->delete_executive_member();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET EXECUTIVE ENDPOINT
        public function get_executive_members(){
            if(strpos($this->url, "/api/get-executive-members") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $executiveList       = new ViewexecutiveList();
                    if($executiveList->permission === 200){
                        $pager           = InputCleaner::sanitize($_GET["pager"]);
                        $filter          = InputCleaner::sanitize($_GET["filter"]);
                        $result          = $executiveList->return_executive_list($businessId, $pager, $filter);
                        if($result === 500){
                            $response    = new Response(500, "Error returning executive memebers.");
                            $response->send_response();
                        }else if($result === 404){
                            $response    = new Response(404, "There is no executive memeber at this time. Please add executive(s).");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Executive memebers list", $result);
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

        //GET ALL DEPARTMENTS FOR EXECUTIVE ENDPOINT
        public function executive_get_departments(){
            if($this->url == "/api/executive-get-departments")
            {
                if($this->method == "GET"){
                    $businessId      = Helper::get_business_id($this->userId, $this->account_character);
                    $departmetnView  = new Viewdepartments();
                    $result          = $departmetnView->get_departments_list($businessId);
                    if($result === 500){
                        $response    = new Response(500, "Error returning departments.");
                        $response->send_response();
                    }else if($result === 404){
                        $response    = new Response(404, "There is no departments at this time. Please create department(s).");
                        $response->send_response();
                    }else{
                        $response    = new Response(200, "Departments list", $result);
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //GET EXECUTIVE MEMBER DETAILS PARAMS
        protected function get_executive_details(){
            if(strpos($this->url, "/api/get-executive-member-details") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $executiveList       = new ViewexecutiveList();
                    if($executiveList->permission === 200){
                        $id              = InputCleaner::sanitize($_GET["executive_id"]);
                        $result          = $executiveList->return_executive_member_details($businessId, $id);
                        if($result === 500){
                            $response    = new Response(500, "Error returning executive memebers details.");
                            $response->send_response();
                        }else if($result === 404){
                            $response    = new Response(404, "There is no executive memeber that matches the provided id.");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Executive memebers details", $result);
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

        //POST EXECUTIVE MEMBER ENDPOINT
        public function add_executive_member(){
            if($this->url == "/api/add-new-executive-member")
            {
                if($this->method == "POST"){
                    $_POST            = json_decode(file_get_contents("php://input"), true);
                    $departmentId     = InputCleaner::sanitize($_POST['department_id']);
                    $first_name       = InputCleaner::sanitize($_POST['first_name']);
                    $middle_name      = InputCleaner::sanitize($_POST['middle_name']);
                    $last_name        = InputCleaner::sanitize($_POST['last_name']);
                    $number           = InputCleaner::sanitize($_POST['number']);
                    $date             = Helper::get_current_date();
                    $businessId       = Helper::get_business_id($this->userId, $this->account_character);
                    $createdExecutive = new AddexecutiveList();
                    if($createdExecutive->permission === 200){
                        $details      = [
                            'company_id'    => $businessId,
                            'department_id' => $departmentId,
                            'first_name'    => $first_name,
                            'middle_name'   => $middle_name,
                            'last_name'     => $last_name,
                            'full_name'     => $first_name.' '.$middle_name.' '.$last_name,
                            'number'        => $number,
                            'date_added'    => $date
                        ];
                        $result             = $createdExecutive->create_executive_member($details);
                        if($result === 500){
                            $response       = new Response(500, "Error creating executive member.");
                            $response->send_response();
                        }else{
                            $response       = new Response(200, "Executive member added successfully.");
                            $response->send_response();
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

        //UPDATE EXECUTIVE MEMBER ENDPOINT
        public function update_executive_member(){
            if($this->url == "/api/update-executive-member-info")
            {
                $_POST            = json_decode(file_get_contents("php://input"), true);
                $executiveId      = InputCleaner::sanitize($_POST['executive_id']);
                $departmentId     = InputCleaner::sanitize($_POST['department_id']);
                $first_name       = InputCleaner::sanitize($_POST['first_name']);
                $middle_name      = InputCleaner::sanitize($_POST['middle_name']);
                $last_name        = InputCleaner::sanitize($_POST['last_name']);
                $number           = InputCleaner::sanitize($_POST['number']);
                $date             = Helper::get_current_date();
                $businessId       = Helper::get_business_id($this->userId, $this->account_character);
                $updateExecutive  = new EditexecutiveList();
                if($updateExecutive->permission === 200){
                    $details      = [
                        'department_id' => $departmentId,
                        'first_name'    => $first_name,
                        'middle_name'   => $middle_name,
                        'last_name'     => $last_name,
                        'full_name'     => $first_name.' '.$middle_name.' '.$last_name,
                        'number'        => $number,
                        'date_added'    => $date
                    ];
                    $identity           = ['column' => ['company_id', 'id'], 'value' => [$businessId, $executiveId]];
                    $result             = $updateExecutive->update_executive_details($details, $identity);
                    if($result === 500){
                        $response       = new Response(500, "Error updating executive member.");
                        $response->send_response();
                    }else{
                        $response       = new Response(200, "Executive member updated successfully.");
                        $response->send_response();
                    }
                }else{
                    $response = new Response(301, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }
        }

        //DELETE EXECUTIVE MEMBER ENDPOINT
        public function delete_executive_member(){
            if($this->url == "/api/delete-executive_member")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $id                  = InputCleaner::sanitize($_POST['executive_id']);
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $deleteExecutive     = new DeleteexecutiveList();
                    if($deleteExecutive->permission === 200){
                        $details         = ['status' => 'remove'];
                        $identity        = ['column' => ['company_id', 'id'], 'value' => [$businessId, $id]];
                        $result          = $deleteExecutive->delete_executive_member($details, $identity);
                        if($result === 500){
                            $response    = new Response(500, "Error deleting executive member.");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Executive member remove successfully.");
                            $response->send_response();
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
    }
new ExecutiveListHandler();