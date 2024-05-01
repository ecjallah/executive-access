<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('OUTSIDEAPPOINTMENT_HANDLER_ID', 10020240416153100);
    define('OUTSIDEAPPOINTMENT_HANDLER', 'Outside Appointment');
    Auth::module_registration(OUTSIDEAPPOINTMENT_HANDLER_ID, OUTSIDEAPPOINTMENT_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages OutsideAppointment operations.
        * @_version Release: 1.0
        * @_created Date: 2024-04-16
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class OutsideAppointmentHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            // if(isset($_SESSION["user_id"])){
            //     $this->user_type           = $_SESSION["user_type"];
            //     $this->userId              = $_SESSION["user_id"];
                $this->method              = $_SERVER["REQUEST_METHOD"];
                $this->url                 = $_SERVER["REQUEST_URI"];
            //     $this->account_character   = $_SESSION["account_character"];
             
                $this->submit_outside_appointment();
                $this->get_all_departments_from_ministry();
                $this->get_all_ministries();
                $this->get_all_departments_executives_from_ministry();
            // }else{
            //     Logout::log_user_out();
            // }
        }

        //GET ALL MINISTRIES ENDPOINT
        public function get_all_ministries(){
            if($this->url == "/api/get-all-ministries")
            {
                if($this->method == "GET"){
                    $ministries         = new ViewoutsideAppointment();
                    $result             = $ministries->get_all_ministries();
                        if($result === 500){
                            $response   = new Response(500, "Error returning ministries.");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "There is no ministry currently.");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "All ministries.", $result);
                            $response->send_response();
                        }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //GET DEPARTMENTS FROM A MINISTRY ENDPOINT
        public function get_all_departments_from_ministry(){
            if(strpos($this->url, "/api/get-ministry-departments") !== false)
            {
                if($this->method == "GET"){
                    $ministryId         = InputCleaner::sanitize($_GET['ministry_id']);
                    $departments        = new Viewdepartments();
                    $result             = $departments->get_departments_list($ministryId);
                        if($result === 500){
                            $response   = new Response(500, "Error returning ministry departments..");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "There is no department in this ministry currently.");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "Ministry departments.", $result);
                            $response->send_response();
                        }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //GET EXECUTIVES FROM A MINISTRY AND A DEPARTMENT ENDPOINT
        public function get_all_departments_executives_from_ministry(){
            if(strpos($this->url, "/api/get-department-executives") !== false)
            {
                if($this->method == "GET"){
                    $ministryId         = InputCleaner::sanitize($_GET['ministry_id']);
                    $departmentId       = InputCleaner::sanitize($_GET['department_id']);
                    $ministries         = new ViewexecutiveList();
                    $result             = $ministries->return_department_executives_($ministryId, $departmentId);
                        if($result === 500){
                            $response   = new Response(500, "Error returning ministry department executives..");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "There is no executive from this department in this ministry currently.");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "Ministry department executives.", $result);
                            $response->send_response();
                        }
                }else{                
                    $response = new Response(300, "This endpoint accepts the GET method");
                    $response->send_response();
                } 
            }
        }

        //OUTSIDE APPOINTMENT SUBMITTION ENDPOINT
        public function submit_outside_appointment(){
            if($this->url == "/api/set-outside-appointment")
            {
                if($this->method == "POST"){
                    $_POST                    = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['ministry_id']) || empty($_POST['executive_id']) || empty($_POST['department_id']) || empty($_POST['visitor_name']) || empty($_POST['purpose'])|| empty($_POST['number']) || empty($_POST['visit_date']) || empty($_POST['start_time']) || empty($_POST['end_time'])){
                        $response             = new Response(400, "Please provide the following: executive_id, department_id, visitor_name, purpose, number, visit_date, start_time, end_time and status");
                        $response->send_response();
                    }else{
                        $addAppointment       = new Addappointment();
                        // if($addAppointment->permission === 200){
                            $companyId        = InputCleaner::sanitize($_POST['ministry_id']);
                            $executive_id     = InputCleaner::sanitize($_POST['executive_id']);
                            $department_id    = InputCleaner::sanitize($_POST['department_id']);
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
                                    "company_id"        => $companyId,
                                    "executive_id"      => $executive_id,
                                    "department_id"     => $department_id,
                                    "visitor_name"      => $visitor_name,
                                    "purpose"           => $purpose,
                                    "start_time"        => $start_time,
                                    "end_time"          => $end_time,
                                    "visitor_number"    => $number,
                                    "appointment_type"  => 'online',
                                    "approval_status"   => 'pending',
                                    "status"            => 'pending',
                                    "visit_date"        => $final_visit_date,
                                    "date_added"        => $currentDate,
                                    "added_by"          => 1000
                                ];

                                $result           = $addAppointment->add_new_appointments($details);
                                if($result === 500){
                                    $response     = new Response(500, "Error submitting online appointment.");
                                    $response->send_response();
                                }else{
                                    $response     = new Response(200, "Online appointment sent successfully. A sms will be sent as soon as your appointment is approved.", $result);
                                    $response->send_response();
                                }
                            } else {
                                $response     = new Response(400, "Appointment date and time cannot be a date or time of the past.");
                                $response->send_response();
                            }
                        // }else{
                        //     $response = new Response(301, "Unauthorized Module: Contact Admin");
                        //     $response->send_response();
                        // }
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }
    }
new OutsideAppointmentHandler();