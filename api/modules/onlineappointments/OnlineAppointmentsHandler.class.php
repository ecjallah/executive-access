<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('ONLINEAPPOINTMENTS_HANDLER_ID', 10020240416123315);
    define('ONLINEAPPOINTMENTS_HANDLER', 'Online Appointments');
    Auth::module_registration(ONLINEAPPOINTMENTS_HANDLER_ID, ONLINEAPPOINTMENTS_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages OnlineAppointments operations.
        * @_version Release: 1.0
        * @_created Date: 2024-04-16
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class OnlineAppointmentsHandler {
        private $user_type;
        private $userId;
        private $account_character;
        private $method;
        private $url;
        function __construct(){
            if(isset($_SESSION["user_id"])){
                $this->user_type           = $_SESSION["user_type"];
                $this->userId              = $_SESSION["user_id"];
                $this->method              = $_SERVER["REQUEST_METHOD"];
                $this->url                 = $_SERVER["REQUEST_URI"];
                $this->account_character   = $_SESSION["account_character"];
                $moduelCheck               = Auth::module_security(ONLINEAPPOINTMENTS_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get_appointment_settings();
                    $this->update_appointment_settings();
                    $this->get_online_appointment();
                    $this->apply_online_appointment_actions();
                    // $this->get_department_executives_appointment_settings();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET MINISTRY APPOINTMENT SETTINGS ENDPOINT
        public function get_appointment_settings(){
            if($this->url == "/api/get-appointment-settings")
            {
                if($this->method == "GET"){
                    $companyId                = Helper::get_business_id($this->userId, $this->account_character);
                    $appointmentSettings      = new ViewonlineAppointments();
                    if($appointmentSettings->permission === 200){
                        $departmentId         = Helper::get_staff_department_id($this->userId);

                        // if($departmentId['status'] === 404){
                        //     $response             = new Response(400, "Sorry, you are not in a department. Only staff(s) that are assigned to a given department is allowed to performn this action.");
                        //     $response->send_response();
                        // }else{
                            $result               = $appointmentSettings->get_ministry_appointment_settings($companyId, 1);
                            // $result               = $appointmentSettings->get_ministry_appointment_settings($companyId, $departmentId['data']);
                            if($result === 500){
                                $response         = new Response(500, "Error returing appointment settings.");
                                $response->send_response();
                            }else if($result === 404){
                                //SET DEFAULT APPOINTMENT
                                $defaultAppointment = new AddonlineAppointments();
                                $defaultResult      = $defaultAppointment->set_default_appointment_settings($companyId);
                                if($defaultResult === 500){
                                    $response       = new Response(500, "Error setting default appointment settings.");
                                    $response->send_response();
                                }else{
                                    $result         = $appointmentSettings->get_ministry_appointment_settings($companyId, $departmentId);
                                    $response       = new Response(200, "Ministry appointment settings.", $result);
                                    $response->send_response();
                                }
                            }else{
                                $response           = new Response(200, "Ministry appointment settings.", $result);
                                $response->send_response();
                            }

                        // }
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

        //GET DEPARTMENT EXECUTIVES ENDPOINT
        // public function get_department_executives_appointment_settings(){
        //     if(strpos($this->url, "/api/get-department-executives-appointment-settings") !== false)
        //     {
        //         if($this->method == "GET"){
        //             $departmentId    = Helper::get_staff_department_id($this->userId);
        //             $executiveId     = key_exists('executive_id', $_GET) ? InputCleaner::sanitize($_GET['executive_id']) : null;
        //             $pager           = key_exists('pager', $_GET) ? InputCleaner::sanitize($_GET['pager']) : null;
        //             $companyId       = Helper::get_business_id($this->userId, $this->account_character);
        //             $executive       = new ViewexecutiveList();

        //             $result          = $executive->return_department_executive_list($companyId, $departmentId, $pager);
        //             if($result === 500){
        //                 $response    = new Response(500, "Error returning dapartment exectives");
        //                 $response->send_response();
        //             }else if($result === 404){
        //                 $response    = new Response(404, "There is no executive member assigned to this department.");
        //                 $response->send_response();
        //             }else{
        //                 $response    = new Response(200, "Department executives.", $result);
        //                 $response->send_response();
        //             }
                  
        //         }else{                
        //             $response = new Response(300, "This endpoint accepts the POST method");
        //             $response->send_response();
        //         } 
        //     }
        // }

        //UPDATE APPOINTMENT SETTINGS ENDPOINT
        public function update_appointment_settings(){
            if($this->url == "/api/update-appointment-settings")
            {
                if($this->method == "POST"){
                    $_POST                  = json_decode(file_get_contents("php://input"), true);
                    if(empty($_POST['department_id']) || empty($_POST['executive_id']) || empty($_POST['start_time']) || empty($_POST['end_time']) || empty($_POST['monday']) || empty($_POST['tuesday']) || empty($_POST['wednesday']) || empty($_POST['thursday']) || empty($_POST['friday'])|| empty($_POST['saturday']) || empty($_POST['open_solt'])){
                        $response           = new Response(400, " Please provide the following: department_id, executive_id, start_time, end_time, monday, tuesday, wednesday, thursday, friday, saturday and open_solt ");
                        $response->send_response();
                    }else{
                        $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                        $todayDate          = Helper::get_current_date();
                        $details            = [
                            "ministry_id"   => $companyId,
                            "department_id" => InputCleaner::sanitize($_POST['department_id']),
                            "executive_id"  => InputCleaner::sanitize($_POST['executive_id']),
                            "start_time"    => InputCleaner::sanitize($_POST['start_time']),
                            "end_time"      => InputCleaner::sanitize($_POST['end_time']),
                            "monday"        => InputCleaner::sanitize($_POST['monday']),
                            "tuesday"       => InputCleaner::sanitize($_POST['tuesday']),
                            "wednesday"     => InputCleaner::sanitize($_POST['wednesday']),
                            "thursday"      => InputCleaner::sanitize($_POST['thursday']),
                            "friday"        => InputCleaner::sanitize($_POST['friday']),
                            "saturday"      => InputCleaner::sanitize($_POST['saturday']),
                            "open_solt"     => InputCleaner::sanitize($_POST['open_solt']),
                            "added_by"      => $this->userId,
                            "added_date"    => $todayDate
                        ];
                        $identity                 = ['column' => ['ministry_id'], 'value' => [$companyId]];
                        $appointmentSetting       = new EditonlineAppointments();
                        if($appointmentSetting->permission === 200){
                            $result               = $appointmentSetting->update_appointment_settings($details, $identity);
                            if($result === 500){
                                $response         = new Response(500, "Error updating appointment settings.");
                                $response->send_response();
                            }else{
                                $response         = new Response(200, "Appointment settings updated successfully.", $result);
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

        //GET ONLINE APPOINTMENT ENDPOINT
        public function get_online_appointment(){
            if(strpos($this->url, "/api/get-online-appointments") !== false)
            {
                if($this->method == "GET"){
                    $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                    $pager              = key_exists('pager', $_GET) ? InputCleaner::sanitize($_GET['pager']) : null;
                    $filter             = key_exists('filter', $_GET) ? InputCleaner::sanitize($_GET['filter']) : null;

                    $appointment_type   = key_exists('appointment_type', $_GET) ? InputCleaner::sanitize($_GET['appointment_type']) : null;
                    $approval_status    = key_exists('approval_status', $_GET) ? InputCleaner::sanitize($_GET['approval_status']) : null;
                    $getAppointment     = new Viewappointment();
                    if($getAppointment->permission === 200){
                        $result         = $getAppointment->return_all_appointments($companyId, $pager, $filter, $appointment_type, $approval_status);
                        if($result === 500){
                            $response   = new Response(500, "Error returning online appointments.");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "There is no online appointment at this time.");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "Online appointment list. $pager", $result);
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

        //APPROVE/REJECT ONLINE APPOINTMENT ENDPOINT
        public function apply_online_appointment_actions(){
            if($this->url == "/api/apply-online-appointment-action")
            {
                if($this->method == "POST"){
                    $_POST                 = json_decode(file_get_contents("php://input"), true);
                    $companyId             = Helper::get_business_id($this->userId, $this->account_character);
                    if(empty($_POST['appointment_id']) || empty($_POST['status'])){
                        $response          = new Response(400, "Please provide the following: appointment_id and status");
                        $response->send_response();
                    }else{
                        $viewAppointment   = new Viewappointment;
                        $appointmentId     = InputCleaner::sanitize($_POST['appointment_id']);
                        $status            = InputCleaner::sanitize($_POST['status']);

                        //Check appointment status
                        $appointment       = new Viewappointment();
                        $editAppointment   = new EditonlineAppointments();
                        if($appointment->permission === 200){
                            $appointmentStatusCheck = $appointment->get_appointment_details($companyId, $appointmentId);

                            if(is_array($appointmentStatusCheck) && $appointmentStatusCheck[0]['appointment_type'] == 'online'){
                                $result             = null;
                                $appointmentStatus  = $appointmentStatusCheck[0]['approval_status'];
                                if($appointmentStatus == 'pending' && $status == 'approved'){
                                    //UPDATE STATUS
                                    $details        = [
                                        "approval_status"  => $status,
                                        "company_id"       => $companyId,
                                        "id"               => $appointmentId,
                                        "number"           => $appointmentStatusCheck[0]['visitor_number']
                                    ];
                                    $result         = $editAppointment->update_online_appointment($details);
                                }else if($appointmentStatus == 'pending' && $status == 'rejected'){
                                    $details        = ["approval_status"  => $status];
                                    $identity       = ['column' => ['company_id', 'id'], 'value' => [$companyId, $appointmentId]];
                                    $result         = $editAppointment->update_direct_online_appointments($details, $identity);
                                }

                                if($result == 200){
                                    $response       = new Response(200, "Appointment status has been updated successfully.");
                                    $response->send_response();
                                }else if($result == 500){
                                    $response       = new Response(400, "Error sending sms to client.");
                                    $response->send_response();
                                }
                                else{
                                    $response       = new Response(400, "Sorry, this appointment is already `{$appointmentStatus}`.");
                                    $response->send_response();
                                }
                            }else{
                                $response           = new Response(404, "Sorry, we could find any appointment matching your request.");
                                $response->send_response();
                            }
                        }else{
                            $response      = new Response(301, "Unauthorized Module: Contact Admin");
                            $response->send_response();
                        }
                        // }
                    }
                }else{                
                    $response = new Response(300, "This endpoint accepts the POST method");
                    $response->send_response();
                } 
            }
        }
    }
new OnlineAppointmentsHandler();