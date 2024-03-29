<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    //Module Identity
    define('DASHBOARD_HANDLER_ID', 10020231007220318);
    define('DASHBOARD_HANDLER', 'Dashboard');
    Auth::module_registration(DASHBOARD_HANDLER_ID, DASHBOARD_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages Dashboard operations.
        * @_version Release: 1.0
        * @_created Date: 2023-10-07
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class DashboardHandler {
        public $user_type;
        public $userId;
        public $account_character;
        public $method;
        public $url;
        public $permission;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type           = $_SESSION["user_type"];
                $this->userId              = $_SESSION["user_id"];
                $this->account_character   = $_SESSION['account_character'];
                $this->method              = $_SERVER["REQUEST_METHOD"];
                $this->url                 = $_SERVER["REQUEST_URI"];
                $moduelCheck               = Auth::module_security(DASHBOARD_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get_appointment_dash_info();
                    $this->appointment_operation_stats();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //THIS ENDPOINT RETURNS APPOINTMENT INFORMATION
        public function get_appointment_dash_info(){
            if($this->url == "/api/get-overview-reports")
            {
                if($this->method == "GET"){
                    $viewDashboard          = new Viewdashboard();
                    if($viewDashboard->permission === 200){
                        $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                        $result             = $viewDashboard->get_overview_info($companyId);
                        if($result === 500){
                            $response       = new Response(500, "Error loading candidate report.");
                            $response->send_response();
                        }else if($result === 404){
                            $response       = new Response(404, "There is no overview report at this time.");
                            $response->send_response();
                        }else{
                            $response       = new Response(200, "Overview report.", $result);
                            $response->send_response();
                        }
                    }else{
                        $response  = new Response(301, "Unauthorized Module: Contact Admin");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                }
            }
        }

        //This endpoint returns all appointments
        protected function appointment_operation_stats(){
            if($this->url == '/api/appointment-operation-stats')
            {
                if($this->method == 'POST'){
                    $viewDashboard       = new Viewdashboard();
                    if($viewDashboard->permission === 200){
                        $_POST           = json_decode(file_get_contents('php://input'), true);
                        $companyId       = Helper::get_business_id($this->userId, $this->account_character);

                        //Get healthcare/hospital id
                        $filter          = InputCleaner::sanitize($_POST['data_key']);
                        $result          = $viewDashboard->appointment_stats($companyId, $filter);
                        if($result === 500){
                            $response    = new Response(500, "Error returning pharmacy order request stats");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Appointment stats", $result);
                            $response->send_response();
                        }
                    }else{
                        $response = new Response(305, 'Unauthorized Module: Contact Admin');
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }
    }
new DashboardHandler();