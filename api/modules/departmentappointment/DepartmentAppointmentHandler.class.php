<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('DEPARTMENTAPPOINTMENT_HANDLER_ID', 10020240311162847);
    define('DEPARTMENTAPPOINTMENT_HANDLER', 'Department Appointment');
    Auth::module_registration(DEPARTMENTAPPOINTMENT_HANDLER_ID, DEPARTMENTAPPOINTMENT_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages DepartmentAppointment operations.
        * @_version Release: 1.0
        * @_created Date: 2024-03-11
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class DepartmentAppointmentHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type        = $_SESSION["user_type"];
                $this->userId           = $_SESSION["user_id"];
                $this->account_character= $_SESSION["account_character"];
                $this->method           = $_SERVER["REQUEST_METHOD"];
                $this->url              = $_SERVER["REQUEST_URI"];
                $moduelCheck            = Auth::module_security(DEPARTMENTAPPOINTMENT_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get_department_appointment();
                    $this->get_appointment_details();
                    $this->add_department_appointment();
                    $this->update_department_appointment();
                    $this->update_department_appointment_status();
                    $this->delete_department_appointment();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET DEPARTMENT APPOINTMENT ENDPOINT
        public function get_department_appointment(){
            if(strpos($this->url, "/api/get-appointments-from-department") !== false)
            {
                if($this->method == "GET"){
                    $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                    $pager              = InputCleaner::sanitize($_GET['pager']);
                    $staffId            = $this->userId;
                    $departmentId       = Helper::get_staff_department_id($staffId);
                    if($departmentId['status'] === 200){
                        $departmentId       = $departmentId['data'];
                        $getAppointment     = new Viewappointment();
                        if($getAppointment->permission === 200){
                            $result         = $getAppointment->return_department_appointments($companyId, $departmentId, $pager);
                            if($result === 500){
                                $response   = new Response(500, "Error returning department appointments.");
                                $response->send_response();
                            }else if($result === 404){
                                $response   = new Response(404, "There is no department appointment at this time.");
                                $response->send_response();
                            }else{
                                $response   = new Response(200, "Department active appointment list.", $result);
                                $response->send_response();
                            }
                        }else{
                            $response = new Response(301, "Unauthorized Module: Contact Admin");
                            $response->send_response();
                        }
                    }else{
                        $response   = new Response(404, "Sorry, you are not assigned to a department.");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                }
            }
        }

        //GET DEPARTMENT APPOINTMENT DETAILS WITH PARAMS
        protected function get_appointment_details(){
            if(strpos($this->url, "/api/get-department-appointment-details") !== false)
            {
                if($this->method == "GET"){
                    $companyId              = Helper::get_business_id($this->userId, $this->account_character);
                    $id                     = InputCleaner::sanitize($_GET['id']);
                    $departmentId           = Helper::get_appointment_department_id($this->userId);
                    if($departmentId['status'] === 200){
                        $getAppointment     = new Viewappointment();
                        if($getAppointment->permission === 200){
                            $result         = $getAppointment->return_all_appointments($companyId, $id);
                            if($result === 500){
                                $response   = new Response(500, "Error returning appointment details.");
                                $response->send_response();
                            }else if($result === 404){
                                $response   = new Response(404, "There is no appointment at this time.");
                                $response->send_response();
                            }else{
                                $response   = new Response(200, "Active appointment details.", $result);
                                $response->send_response();
                            }
                        }else{
                            $response = new Response(301, "Unauthorized Module: Contact Admin");
                            $response->send_response();
                        }
                    }else{
                        $response   = new Response(404, "Sorry, you are not assigned to a department.");
                        $response->send_response();
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                }
            }
        }

        //DEPARTMENT APPOINTMENT CREATION ENDPOINT
        public function add_department_appointment(){
            if($this->url == "/api/add-new-department-appointment")
            {
                if($this->method == "POST"){
                    $_POST                 = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['executive_id']) || empty($_POST['visitor_name']) || empty($_POST['purpose'])|| empty($_POST['number']) || empty($_POST['visit_date']) || empty($_POST['start_time']) || empty($_POST['end_time'])){
                        $response          = new Response(400, "Please provide the following: executive_id, visitor_name, purpose, number, visit_date, start_time, end_time and status");
                        $response->send_response();
                    }else{
                        $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                        $addAppointment           = new Addappointment();
                        if($addAppointment->permission === 200){
                            $departmentId         = Helper::get_appointment_department_id($this->userId);
                            if($departmentId['status'] === 200){
                                $executive_id     = InputCleaner::sanitize($_POST['executive_id']);
                                $department_id    = 1;
                                $visitor_name     = InputCleaner::sanitize($_POST['visitor_name']);
                                $purpose          = InputCleaner::sanitize($_POST['purpose']);
                                $start_time       = InputCleaner::sanitize($_POST['start_time']);
                                $end_time         = InputCleaner::sanitize($_POST['end_time']);
                                $number           = InputCleaner::sanitize($_POST['number']);
                                $visit_date       = InputCleaner::sanitize($_POST['visit_date']);
                                $final_visit_date = "$visit_date $start_time";
                                $currentDate      = gmdate("Y-m-d H:i:s");
                                $currentTimestamp = strtotime($currentDate);
                                $VisitTimestamp   = strtotime($final_visit_date);
                                if ($currentTimestamp < $VisitTimestamp) {
                                    $details     = [
                                        "company_id"     => $companyId,
                                        "executive_id"   => $executive_id,
                                        "department_id"  => $department_id,
                                        "visitor_name"   => $visitor_name,
                                        "purpose"        => $purpose,
                                        "start_time"     => $start_time,
                                        "end_time"       => $end_time,
                                        "visitor_number" => $number,
                                        "visit_date"     => $final_visit_date,
                                        "date_added"     => $currentDate,
                                        "added_by"       => $this->userId
                                    ];
        
                                    $result           = $addAppointment->add_new_appointments($details);
                                    if($result === 500){
                                        $response     = new Response(500, "Error adding new appointment.");
                                        $response->send_response();
                                    }else{
                                        $response     = new Response(200, "Department appointment added successfully.", $result);
                                        $response->send_response();
                                    }
                                }else{
                                    $response         = new Response(400, "Appointment date and time cannot be a date or time of the past.");
                                    $response->send_response();
                                }
                            }else{
                                $response   = new Response(404, "Sorry, you are not assigned to a department.");
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

        //UPDATE DEPARTMENT APPOINTMENT ENDPOINT
        public function update_department_appointment(){
            if($this->url == "/api/update-department-appointment")
            {
                if($this->method == "POST"){
                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['appointment_id']) || empty($_POST['executive_id']) || empty($_POST['visitor_name']) || empty($_POST['purpose'])|| empty($_POST['number']) || empty($_POST['visit_date'])){
                        $response            = new Response(400, "Please provide the following: executive_id, visitor_name, purpose, number, visit_date, and status");
                        $response->send_response();
                    }else{
                        $companyId           = Helper::get_business_id($this->userId, $this->account_character);
                        $editAppointment     = new Editappointment();
                        if($editAppointment->permission === 200){
                            $departmentId         = Helper::get_appointment_department_id($this->userId);
                            if($departmentId['status'] === 200){
                                $appointment_id  = InputCleaner::sanitize($_POST['appointment_id']);
                                $executive_id    = InputCleaner::sanitize($_POST['executive_id']);
                                $department_id   = $departmentId['data'];
                                $visitor_name    = InputCleaner::sanitize($_POST['visitor_name']);
                                $purpose         = InputCleaner::sanitize($_POST['purpose']);
                                $number          = InputCleaner::sanitize($_POST['number']);
                                $visit_date      = InputCleaner::sanitize($_POST['visit_date']);

                                $details     = [
                                    "company_id"     => $companyId,
                                    "executive_id"   => $executive_id,
                                    "department_id"  => $department_id,
                                    "visitor_name"   => $visitor_name,
                                    "purpose"        => $purpose,
                                    "visitor_number" => $number,
                                    "visit_date"     => $visit_date,
                                    "added_by"       => $this->userId
                                ];
                                $identity         = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointment_id]];
                                $result           = $editAppointment->update_executive_appointment($details, $identity);
                                if($result === 500){
                                    $response     = new Response(500, "Error updating appointment.");
                                    $response->send_response();
                                }else{
                                    $response     = new Response(200, "Appointment updated successfully.", $result);
                                    $response->send_response();
                                }
                            }else{
                                $response   = new Response(404, "Sorry, you are not assigned to a department.");
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

        //CHECK APPOINTMENT IN/OUT ENDPOINT
        public function update_department_appointment_status(){
            if($this->url == "/api/update-department-appointment-status")
            {
                if($this->method == "POST"){
                    $_POST       = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['appointment_id']) || empty($_POST['appointment_status'])){
                        $response          = new Response(400, "Please provide the following: appointment_id, appointment_status");
                        $response->send_response();
                    }else{
                        $companyId         = Helper::get_business_id($this->userId, $this->account_character);
                        $editAppointment   = new Editappointment();
                        if($editAppointment->permission === 200){
                            $departmentId         = Helper::get_appointment_department_id($this->userId);
                            if($departmentId['status'] === 200){
                                $appointment_id      = InputCleaner::sanitize($_POST['appointment_id']);
                                $appointment_status  = InputCleaner::sanitize($_POST['appointment_status']);
                                $updated_date        = Helper::get_current_date();

                                $details          = [
                                    "status"        => $appointment_status,
                                    "date_updated"  => $updated_date
                                ];
                                $identity         = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointment_id]];
                                $result           = $editAppointment->update_executive_appointment($details, $identity);
                                if($result === 500){
                                    $response     = new Response(500, "Error updating appointment.");
                                    $response->send_response();
                                }else{
                                    $response     = new Response(200, "Appointment updated successfully.", $result);
                                    $response->send_response();
                                }
                            }else{
                                $response         = new Response(404, "Sorry, you are not assigned to a department.");
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

        //DELETE APPOINTMENT ENDPOINT
        public function delete_department_appointment(){
            if($this->url == "/api/delete-appointment")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $id                  = InputCleaner::sanitize($_POST['appointment_id']);
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $removeAppointment   = new Deleteappointment();
                    if($removeAppointment->permission === 200){
                        $departmentId        = Helper::get_appointment_department_id($this->userId);
                        if($departmentId['status'] === 200){
                            $details         = ['status' => 'delete'];
                            $identity        = ['column' => ['company_id', 'id'], 'value' => [$businessId, $id]];
                            $result          = $removeAppointment->remove_appointment($details, $identity);
                            if($result === 500){
                                $response    = new Response(500, "Error deleting appointment.");
                                $response->send_response();
                            }else{
                                $response    = new Response(200, "Appointment deleted successfully.",);
                                $response->send_response();
                            }
                        }else{
                            $response        = new Response(404, "Sorry, you are not assigned to a department.");
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
    new DepartmentAppointmentHandler();  