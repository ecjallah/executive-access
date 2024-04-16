<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('VISITCHECKS_HANDLER_ID', 10020240314211538);
    define('VISITCHECKS_HANDLER', 'VisitChecks');
    Auth::module_registration(VISITCHECKS_HANDLER_ID, VISITCHECKS_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages VisitChecks operations.
        * @_version Release: 1.0
        * @_created Date: 2024-03-14
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class VisitChecksHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type           = $_SESSION["user_type"];
                $this->userId              = $_SESSION["user_id"];
                $this->account_character   = $_SESSION['account_character'];
                $this->method              = $_SERVER["REQUEST_METHOD"];
                $this->url                 = $_SERVER["REQUEST_URI"];
                // $moduelCheck               = Auth::module_security(VISITCHECKS_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                // if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->lookup_appointment();
                    $this->apply_visit_actions();
                    $this->add_appointment_items();
                    $this->get();
                    $this->get_params();
                    $this->delete();
                // }else{
                //     $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                //     $response->send_response();
                // }
            }else{
                Logout::log_user_out();
            }
        }

        //GET ENDPOINT
        public function get(){
            if($this->url == "/api/")
            {
                if($this->method == "GET"){
                    // $businessId               = Helper::get_business_id($this->userId);
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
                    $response = new Response(300, "This endpoint accepts the GET method");
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

        //LOOKUP APPOINTMENT VISIT ENDPOINT
        public function lookup_appointment(){
            if($this->url == "/api/lookup-appointment-info")
            {
                if($this->method == "POST"){
                    $_POST              = json_decode(file_get_contents("php://input"), true);
                    $lookupAppointment  = new ViewappointmentSecurity();
                    $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                    $lookupVal          = InputCleaner::sanitize($_POST['search']);
                    if($lookupAppointment->permission === 200){
                        $result         = $lookupAppointment->lookup_appointment($companyId, $lookupVal);
                        if($result === 500){
                            $response   = new Response(500, "Error lookingup appointment details.");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "Sorry, there is no appointment matching.");
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

        //APPOINTMENT VISIT OPERATIONS ENDPOINT
        public function apply_visit_actions(){
            if($this->url == "/api/apply-visit-operation")
            {
                if($this->method == "POST"){
                    $_POST             = json_decode(file_get_contents("php://input"), true);
                    $companyId         = Helper::get_business_id($this->userId, $this->account_character);
                    if(empty($_POST['appointment_id']) || empty($_POST['status'])){
                        $response      = new Response(400, "Please provide the following: appointment_id and status");
                        $response->send_response();
                    }else{
                        $appointmentId = InputCleaner::sanitize($_POST['appointment_id']);
                        $status        = InputCleaner::sanitize($_POST['status']);
                        $tagNumber     = key_exists('tag_number', $_POST) ? InputCleaner::sanitize($_POST['tag_number']) : false;

                        //Check appointment status
                        $appointment                = new Viewappointment();
                        $editAppointment            = new Editappointment();
                        if($appointment->permission === 200){
                            $appointmentStatusCheck = $appointment->get_appointment_details($companyId, $appointmentId);
                            if(is_array($appointmentStatusCheck)){
                                $result             = null;
                                $appointmentStatus  = $appointmentStatusCheck[0]['status'];
                                if($appointmentStatus == 'pending' && $appointmentStatus != 'expired' && $status != 'active'){
                                    $response       = new Response(400, "Sorry, this appointment can only be active/checked in!");
                                    $response->send_response();
                                }else if($appointmentStatus == 'active' && $appointmentStatus != 'expired' && $status != 'completed'){
                                    $response       = new Response(400, "Sorry, this appointment can only be completed/checked out!.");
                                    $response->send_response();
                                }else if($appointmentStatus == 'pending' && $status == 'active'){
                                    //UPDATE STATUS
                                    $details        = ["status" => $status];
                                    if ($tagNumber != false) {
                                        $details['tag_number'] = $tagNumber;
                                    }
                                    $identity       = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointmentId]];
                                    $result         = $editAppointment->update_executive_appointment($details, $identity);
                                }else if($appointmentStatus == 'active' && $status == 'completed'){
                                    //UPDATE STATUS
                                    $details        = ["status" => $status];
                                    if ($tagNumber != false) {
                                        $details['tag_number'] = $tagNumber;
                                    }
                                    $identity       = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointmentId]];
                                    $result         = $editAppointment->update_executive_appointment($details, $identity);
                                }else if($appointmentStatus == $status){
                                    $response       = new Response(400, "Sorry, this appointment is already `{$appointmentStatus}`.");
                                    $response->send_response();
                                }
                                if($result == 200){
                                    $response       = new Response(200, "Appointment status has been updated successfully.");
                                    $response->send_response();
                                }else{
                                    $response       = new Response(400, "Sorry, there is an issue with the appointment, please contact admin..", $result);
                                    $response->send_response();
                                }
                            }else{
                                $response           = new Response(404, "Sorry, we could find any appointment matching your request.");
                                $response->send_response();
                            }
                        }else{
                            $response = new Response(301, "Unauthorized Module: Contact Admin");
                            $response->send_response();
                        }
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }

        //APPOINTMENT ITEM ENDPOINT
        public function add_appointment_items(){
            if($this->url == "/api/add-appointment-items")
            {
                if($this->method == "POST"){
                    $_POST                 = json_decode(file_get_contents("php://input"), true);
                    $companyId             = Helper::get_business_id($this->userId, $this->account_character);
                    if(empty($_POST['appointment_id']) || empty($_POST['items'])){
                        $response          = new Response(400, "Please provide the following: appointment_id and items");
                        $response->send_response();
                    }else{
                        $addAppointmentItems  = new AddvisitChecks();
                        if($addAppointmentItems->permission === 200){
                            $appointmentId = InputCleaner::sanitize($_POST['appointment_id']);
                            $items         = InputCleaner::sanitize($_POST['items']);
                            $result        = [];
                            if (is_array($items)) {
                                foreach ($items as $item_info) {
                                    $details     = [
                                        "appointment_id" => $appointmentId,
                                        "item_name"      => $item_info['item_name'],
                                        "serial_number"  => $item_info['serial_number'],
                                        "date"           => gmdate("Y-m-d H:i:s")
                                    ];
                                    $result       = $addAppointmentItems->add_appointment_items($details);
                                }
                                if($result === 500){
                                    $response     = new Response(500, "Error registering appointment item.");
                                    $response->send_response();
                                }else{
                                    $response     = new Response(200, "Appointment items registered successfully. ", $result);
                                    $response->send_response();
                                }
                            } else {
                                $response     = new Response(400, " Appointment date and time cannot be a date or time of the past");
                                $response->send_response();
                            }
                        }else{
                            $response = new Response(301, "Unauthorized Module: Contact Admin");
                            $response->send_response();
                        }
                    }
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
new VisitChecksHandler();