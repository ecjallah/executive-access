<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('APPOINTMENTPURPOSE_HANDLER_ID', 10020240427213726);
    define('APPOINTMENTPURPOSE_HANDLER', 'Appointment Purpose');
    Auth::module_registration(APPOINTMENTPURPOSE_HANDLER_ID, APPOINTMENTPURPOSE_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages AppointmentPurpose operations.
        * @_version Release: 1.0
        * @_created Date: 2024-04-27
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class AppointmentPurposeHandler {
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
                $moduelCheck               = Auth::module_security(APPOINTMENTPURPOSE_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get_purpose();
                    $this->add_appointment_purpose();
                    $this->get_appointment_purpose_details();
                    $this->update_appointment_purpose();
                    $this->delete_appointment_purpose();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET PURPOSE ENDPOINT
        public function get_purpose(){
            if($this->url == "/api/get-purpose")
            {
                if($this->method == "GET"){
                    $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                    $appointmentPurpose       = new ViewappointmentPurpose();
                    if($appointmentPurpose->permission === 200){
                        $result               = $appointmentPurpose->get_appointment_purpose($companyId);
                        if($result === 500){
                            $response         = new Response(500, " Error returning appointment purpose(s). ");
                            $response->send_response();
                        }else if($result === 404){
                            $response         = new Response(404, " There is no appointment purpose. ");
                            $response->send_response();
                        }else{
                            $response         = new Response(200, "Appointment purpose list.", $result);
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

        //GET APPOINTMENT PURPOSE BY PARAMS
        protected function get_appointment_purpose_details(){
            if(strpos($this->url, "/api/get-appointment-purpose-details") !== false)
            {
                if($this->method == "GET"){
                    $companyId            = Helper::get_business_id($this->userId, $this->account_character);
                    $purposeId            = InputCleaner::sanitize($_GET["id"]);
                    $getPurpose           = new ViewappointmentPurpose();
                    if($getPurpose->permission === 200){
                        $result           = $getPurpose->get_appointment_purpose_by_id($companyId, $purposeId);
                        if($result === 500){
                            $response     = new Response(500, " Error returning appointment purpose details. ");
                            $response->send_response();
                        }else if($result === 404){
                            $response     = new Response(404, " There is no appointment purpose matching the given id. ");
                            $response->send_response();
                        }else{
                            $response     = new Response(200, "Appointment purpose details.", $result);
                            $response->send_response();
                        }
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //CRERATE NEW APPOINTMENT PURPOSE ENDPOINT
        protected function add_appointment_purpose(){
            if($this->url == "/api/add-appointment-purpose")
            {
                if($this->method == "POST"){
                    $_POST                        = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['appointment_purpose'])){
                        $response                 = new Response(500, " Please provide appointment_purpose as a key. ");
                        $response->send_response();
                    }else{
                        $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                        $newAppointmentPurpose    = InputCleaner::sanitize($_POST['appointment_purpose']);
                        $appointmentPurpose       = new AddappointmentPurpose();
                        if($appointmentPurpose->permission === 200){
                            $details              = [
                                "ministry_id"     => $companyId,
                                "purpose"         => $newAppointmentPurpose
                            ];
                            $result               = $appointmentPurpose->create_appointment_purpose($details);
                            if($result === 500){
                                $response         = new Response(500, " Error creating new appointment purpose. ");
                                $response->send_response();
                            }else{
                                $response         = new Response(200, "Appointment purpose created successfully.", $result);
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

        //UPDATE APPOINTMENT PURPOSE
        protected function update_appointment_purpose(){
            if($this->url == "/api/update-appointment-purpose")
            {
                if($this->method == "POST"){
                    $_POST                        = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['appointment_purpose_id']) || empty($_POST['appointment_purpose'])){
                        $response                 = new Response(400, " Please provide appointment_purpose_id and appointment_purpose as a key. ");
                        $response->send_response();
                    }else{
                        $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                        $id                       = InputCleaner::sanitize($_POST['appointment_purpose_id']);
                        $newAppointmentPurpose    = InputCleaner::sanitize($_POST['appointment_purpose']);
                        $appointmentPurpose       = new EditappointmentPurpose();
                        if($appointmentPurpose->permission === 200){
                            $details              = ["purpose" => $newAppointmentPurpose];
                            $identity             = ['column' => ['ministry_id', 'id'], 'value' => [$companyId, $id]];
                            $result               = $appointmentPurpose->update_appointment_purpose($details, $identity);
                            if($result === 500){
                                $response         = new Response(500, " Error editing new appointment purpose. ");
                                $response->send_response();
                            }else{
                                $response         = new Response(200, "Appointment purpose edited successfully.", $result);
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

        //DELETE APPOINTMENT PURPOSE
        protected function delete_appointment_purpose(){
            if($this->url == "/api/delete-appointment-purpose")
            {
                if($this->method == "POST"){
                    $_POST                        = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['appointment_purpose_id'])){
                        $response                 = new Response(500, " Please provide appointment_purpose_id as a key. ");
                        $response->send_response();
                    }else{
                        $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                        $id                       = InputCleaner::sanitize($_POST['appointment_purpose_id']);
                        $appointmentPurpose       = new DeleteappointmentPurpose();
                        if($appointmentPurpose->permission === 200){
                            $details              = ["status" => 1];
                            $identity             = ['column' => ['ministry_id', 'id'], 'value' => [$companyId, $id]];
                            $result               = $appointmentPurpose->delete_appointment_purpose($details, $identity);
                            if($result === 500){
                                $response         = new Response(500, " Error deleting new appointment purpose. ");
                                $response->send_response();
                            }else{
                                $response         = new Response(200, "Appointment purpose deleted successfully.");
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
    }
new AppointmentPurposeHandler();    