<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('DEPARTMENTS_HANDLER_ID', 10020240227160102);
    define('DEPARTMENTS_HANDLER', 'Departments');
    Auth::module_registration(DEPARTMENTS_HANDLER_ID, DEPARTMENTS_HANDLER);
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages Departments operations.
        * @_version Release: 1.0
        * @_created Date: 2024-02-27
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class DepartmentsHandler {
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
                $moduelCheck                = Auth::module_security(DEPARTMENTS_HANDLER_ID, $this->userId, $this->user_type, $this->account_character);
                $this->get_company_department();
                $this->get_department_executives();
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->add_department();
                    $this->update_department();
                    $this->get_department_by_params();
                    $this->delete_department();
                    $this->assign_staff_to_department();
                    $this->unassign_staff_from_department();
                    $this->get_department_staff();
                }else{
                    $response = new Response($moduelCheck, "Unauthorized Module: Contact Admin");
                    $response->send_response();
                }
            }else{
                Logout::log_user_out();
            }
        }

        //GET ALL DEPARTMENTS ENDPOINT
        public function get_company_department(){
            if(strpos($this->url, "/api/get-departments") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $departmetnView      = new Viewdepartments();
                    // if($departmetnView->permission === 200){
                        $pager           = key_exists('pager', $_GET) ? InputCleaner::sanitize($_GET["pager"]) : null;
                        $result          = $departmetnView->get_all_departments($businessId, $pager);
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

        //GET department BY PARAMS
        protected function get_department_by_params(){
            if(strpos($this->url, "/api/get-department-details") !== false)
            {
                if($this->method == "GET"){
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $departmetnView      = new Viewdepartments();
                    if($departmetnView->permission === 200){
                        $departmentId    = InputCleaner::sanitize($_GET["department_id"]);
                        $result          = $departmetnView->get_departments_details($businessId, $departmentId);
                        if($result === 500){
                            $response    = new Response(500, "Error returning department details.");
                            $response->send_response();
                        }else if($result === 404){
                            $response    = new Response(404, "There is no departments matching the provided id.");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, "Departments details.", $result);
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

        //CREATE NEW DEPARTMENT ENDPOINT
        public function add_department(){
            if($this->url == "/api/create-new-department")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $title               = InputCleaner::sanitize($_POST['title']);
                    $date                = Helper::get_current_date();
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $createDepartment    = new Adddepartments();
                    if($createDepartment->permission === 200){
                        $details         = [
                            'company_id' => $businessId,
                            'title'      => $title,
                            'date_added' => $date
                        ];
                        $result               = $createDepartment->create_department($details);
                        if($result === 500){
                            $response         = new Response(500, " Error creating dapartments ");
                            $response->send_response();
                        }else{
                            $response         = new Response(200, "Department created successfully.");
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

        //GET DEPARTMENT EXECUTIVES ENDPOINT
        public function get_department_executives(){
            if(strpos($this->url, "/api/get-department-executives") !== false)
            {
                if($this->method == "GET"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $departmentId        = InputCleaner::sanitize($_GET['department-id']);
                    $pager               = key_exists('pager', $_GET) ? InputCleaner::sanitize($_GET['pager']) : null;
                    $companyId           = Helper::get_business_id($this->userId, $this->account_character);
                    $executive           = new ViewexecutiveList();
                    // if($executive->permission === 200){
                        $result          = $executive->return_department_executive_list($companyId, $departmentId, $pager);
                        if($result === 500){
                            $response         = new Response(500, "Error creating dapartments");
                            $response->send_response();
                        }else if($result === 404){
                            $response         = new Response(404, "There is no executive member assigned to this department.");
                            $response->send_response();
                        }else{
                            $response         = new Response(200, "Department executives.", $result);
                            $response->send_response();
                        }
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

        //GET DEPARTMENT STAFF ENDPOINT
        public function get_department_staff(){
            if(strpos($this->url, "/api/get-department-staff") !== false)
            {
                if($this->method == "GET"){
                    $departmentId       = InputCleaner::sanitize($_GET['department-id']);
                    $companyId          = Helper::get_business_id($this->userId, $this->account_character);
                    $departments        = new Viewdepartments();
                    if($departments->permission === 200){
                        $result         = $departments->get_department_staff($companyId, $departmentId);
                        if($result === 500){
                            $response   = new Response(500, "Error returing dapartment staff.");
                            $response->send_response();
                        }else if($result === 404){
                            $response   = new Response(404, "There is no staff assigned to this department.");
                            $response->send_response();
                        }else{
                            $response   = new Response(200, "Department executives.", $result);
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

        //UPDATE DEPARTMENT ENDPOINT
        public function update_department(){
            if($this->url == "/api/update-department")
            {
                if($this->method == "POST"){
                    $_POST              = json_decode(file_get_contents("php://input"), true);
                    $id                 = InputCleaner::sanitize($_POST['department_id']);
                    $title              = InputCleaner::sanitize($_POST['title']);
                    $date               = Helper::get_current_date();
                    $businessId         = Helper::get_business_id($this->userId, $this->account_character);
                    $updateDepartment    = new Editdepartments();
                    if($updateDepartment->permission === 200){
                        $details         = [
                            'id'           => $id,
                            'title'        => $title,
                            'date_updated' => $date
                        ];
                        $identity        = ['column' => ['company_id', 'id'], 'value' => [$businessId, $id]];
                        $result          = $updateDepartment->update_department($details, $identity);
                        if($result === 500){
                            $response    = new Response(500, " Error updating department. ");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, " Department updated successfully. ",);
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

        //DELETE DEPARTMENT ENDPOINT
        public function delete_department(){
            if($this->url == "/api/delete-department")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $id                  = InputCleaner::sanitize($_POST['department_id']);
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $deleteDepartment    = new Deletedepartments();
                    if($deleteDepartment->permission === 200){
                        $details         = ['delete' => 1];
                        $identity        = ['column' => ['company_id', 'id'], 'value' => [$businessId, $id]];
                        $result          = $deleteDepartment->delete_department($details, $identity);
                        if($result === 500){
                            $response    = new Response(500, " Error deleting department. ");
                            $response->send_response();
                        }else{
                            $response    = new Response(200, " Department deleted successfully. ",);
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

        //ASSIGN STAFF TO DEPARTMENT ENDPOINT
        public function assign_staff_to_department(){
            if($this->url == "/api/assign-staff-to-department")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $staffId             = InputCleaner::sanitize($_POST['staff_id']);
                    $departmentId        = InputCleaner::sanitize($_POST['department_id']);
                    $date                = Helper::get_current_date();
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $staffAssignment     = new DepartmentAppointment();
                    if($staffAssignment ->permission === 200){
                        $details         = [
                            'company_id'    => $businessId,
                            'staff_id'      => $staffId,
                            'department_id' => $departmentId,
                            'date_added'    => $date
                        ];
                        $result             = $staffAssignment->assign_staff_department($details);
                        if($result === 500){
                            $response       = new Response(500, " Error assigning staff to dapartments. ");
                            $response->send_response();
                        }else{
                            $response       = new Response(200, "Staff assigned to department successfully.");
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

        //UNASSIGN STAFF FROM DEPARTMENT ENDPOINT
        public function unassign_staff_from_department(){
            if($this->url == "/api/unassign-staff-from-department")
            {
                if($this->method == "POST"){
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $staffId             = InputCleaner::sanitize($_POST['staff_id']);
                    $departmentId        = InputCleaner::sanitize($_POST['department_id']);
                    $date                = Helper::get_current_date();
                    $businessId          = Helper::get_business_id($this->userId, $this->account_character);
                    $staffAssignment     = new DepartmentAppointment();
                    if($staffAssignment ->permission === 200){
                        $details         = [
                            'company_id'    => $businessId,
                            'staff_id'      => $staffId,
                            'department_id' => $departmentId
                        ];
                        $result             = $staffAssignment->unassign_staff_from_department($details);
                        if($result === 500){
                            $response       = new Response(500, "Error unassigning staff from dapartments.");
                            $response->send_response();
                        }else{
                            $response       = new Response(200, "Staff unassigned from department successfully.");
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
new DepartmentsHandler();  