<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('APPOINTMENTSECURITY_HANDLER_ID', 10020240301194745);
    define('APPOINTMENTSECURITY_HANDLER', 'AppointmentSecurity');
    Auth::module_registration(APPOINTMENTSECURITY_HANDLER_ID, APPOINTMENTSECURITY_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages AppointmentSecurity operations.
        * @_version Release: 1.0
        * @_created Date: 2024-03-01
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class AppointmentSecurityHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type           = $_SESSION["user_type"];
                $this->userId              = $_SESSION["user_id"];
                $this->account_character   = $_SESSION["account_character"];
                $this->method              = $_SERVER["REQUEST_METHOD"];
                $this->url                 = $_SERVER["REQUEST_URI"];
                $moduelCheck               = Auth::module_security(APPOINTMENTSECURITY_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->lookup_appointment();
                    $this->get_params();
                    $this->add();
                    $this->update();
                    $this->delete();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //LOOKUP VISIT ENDPOINT
        public function lookup_appointment(){
            if($this->url == "/api/lookup-appointment")
            {
                if($this->method == "POST"){
                    $_POST              = json_decode(file_get_contents("php://input"), true);
                    $lookupAppointment  = new ViewappointmentSecurity();
                    $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                    $lookupVal          = InputCleaner::sanitize($_POST['search']);
                    if($lookupAppointment->permission === 200){
                        $result         = $lookupAppointment->lookup_appointment($companyId, $lookupVal);
                        if($result === 500){
                            $response   = new Response(500, "Error lookingup appointment.");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, " Sorry, there is no appointment matching. ");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "Appointment Info.", $result);
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

        //GET WITH PARAMS
        protected function get_params(){
            if(strpos($this->url, "/api/") !== false)
            {
                if($this->method == "GET"){
                    // $providerId           = InputCleaner::sanitize($_GET["id"]);
                    // $serviceProvider      = new ProviderPackages();
                    // if($serviceProvider->permission === 200){
                    //     $result               = $serviceProvider->get_vaild_service_provide_by_id($providerId);
                    //     if($result === 500){
                    //         $response         = new Response(500, "");
                    //         $response->send_response();
                    //     }else if($result === 404){
                    //         $response         = new Response(404, "");
                    //         $response->send_response();
                    //     }else{
                    //         $response         = new Response(200, "", $result);
                    //         $response->send_response();
                    //     }else{
                    //         $response = new Response(301, "Unauthorized Module: Contact Admin");
                    //         $response->send_response();
                    //     }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //POST ENDPOINT
        public function add(){
            if($this->url == "/api/")
            {
                if($this->method == "POST"){
                    $_POST                       = json_decode(file_get_contents("php://input"), true);
                    // $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                    // $partyPriorities          = new ViewPartyPriorities();
                    // if($partyPriorities->permission === 200){
                    //     $result               = "//"
                    //     if($result === 500){
                    //         $response         = new Response(500, " ");
                    //         $response->send_response();
                    //     }else if($result === 404){
                    //         $response         = new Response(404, " ");
                    //         $response->send_response();
                    //     }else{
                    //         $response         = new Response(200, "", $result);
                    //         $response->send_response();
                    //     }
                    // }else{
                    //     $response = new Response(301, "Unauthorized Module: Contact Admin");
                    //     $response->send_response();
                    // }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //UPDATE ENDPOINT
        public function update(){
            if($this->url == "/api/")
            {
                if($this->method == "POST"){
                    $_POST                       = json_decode(file_get_contents("php://input"), true);
                    // $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                    // $partyPriorities          = new ViewPartyPriorities();
                    // if($partyPriorities->permission === 200){
                    //     $result               = "//"
                    //     if($result === 500){
                    //         $response         = new Response(500, " ");
                    //         $response->send_response();
                    //     }else{
                    //         $response         = new Response(200, "",);
                    //         $response->send_response();
                    //     }
                    // }else{
                    //     $response = new Response(301, "Unauthorized Module: Contact Admin");
                    //     $response->send_response();
                    // }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //DELETE ENDPOINT
        public function delete(){
            if($this->url == "/api/")
            {
                if($this->method == "POST"){
                    $_POST                       = json_decode(file_get_contents("php://input"), true);
                    // $businessId               = Helper::get_business_id($this->userId, $this->account_character);
                    // $partyPriorities          = new ViewPartyPriorities();
                    // if($partyPriorities->permission === 200){
                    //     $result               = "//"
                    //     if($result === 500){
                    //         $response         = new Response(500, " ");
                    //         $response->send_response();
                    //     }else{
                    //         $response         = new Response(200, "",);
                    //         $response->send_response();
                    //     }
                    // }else{
                    //     $response = new Response(301, "Unauthorized Module: Contact Admin");
                    //     $response->send_response();
                    // }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }
    }

new AppointmentSecurityHandler();