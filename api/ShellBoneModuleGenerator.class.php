<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/CustomSql.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: eHealth | Developed By: Paul Glaydor
 * @_purpose: This class generates and configure shell bone module.
 * @_version Release: 1.0
 * @_created Date: 05/9/2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ShellBoneModuleGenerator{
    private $module_handler_name;
    private $module_last_child_id;
    private $module_view;
    private $module_update;
    private $module_delete;
    private $module_add;
    private $module_master_id = false;
    function __construct(array $module_birth_info){
        $this->generate_module_set($module_birth_info);
    }

    //This method creates new modules
    private function generate_module_set($module_birth_info){
        //Get module handler fullname
        $moduleFriendlyName                      = $module_birth_info['module_name'];
        $moduleSystemName                        = $module_birth_info['module_system_name'];
        $moduleMasterName                        = $this->class_name_provider($module_birth_info['module_system_name']);
        $moduleHandlerName                       = $this->module_handler_name    = $moduleMasterName.'Handler.class'.'.php';

        //FILES NAMING SECTION
        //Get viewer class name
        $viewName       = $this->module_view     = 'View'.$moduleMasterName.'.class'.'.php';
        
        //Get editor class name
        $editName       = $this->module_update   = 'Edit'.$moduleMasterName.'.class'.'.php';
        
        //Get deleter class name
        $deleteName     = $this->module_delete   = 'Delete'.$moduleMasterName.'.class'.'.php';
        
        //Get adder class name
        $addName        = $this->module_add      = 'Add'.$moduleMasterName.'.class'.'.php';
        $module_details = [
            "handler_name" => ucfirst($moduleHandlerName),
            "view"         => $viewName,
            "edit"         => $editName,
            "delete"       => $deleteName,
            "add"          => $addName
        ];

        //Create folder
        $this->module_folder_creator($moduleSystemName, $module_birth_info['module_system_name'], $module_details, $moduleFriendlyName);
    }

    //This method creates module folder

    private function module_folder_creator($moduleSystemName, $folder_name, $moduleInfo, $moduleFriendlyName){
        $result            = [];
        $folder            = str_replace(' ', '_', strtolower($folder_name));
        $module_path       = 'modules/'.$folder;
        if (!file_exists($module_path)) {
            mkdir ($module_path, 0755, true);
            foreach ($moduleInfo as $key => $value) {
                //Create files
                $result[]  = $this->file_creator($moduleSystemName, $key, $module_path, $value, $moduleFriendlyName);
            }
            print_r(['status'  => 200, 'message' => 'directory created successfully.', 'data' => $module_path]);
            return 0;
        }
        print_r(['status'  => 404, 'message' => 'directory already exist.']);
        return 0;
    }

    function class_name_provider($string, $capitalizeFirstCharacter = false){
        $str        = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    //This method gets/returns MODULE HANDLER MASTER CODE
    public function return_module_master_code(){}

    public function module_id_generator($range = 100){
        $date        = date('YmdHis');
        $id          = $range.$date;
        if($this->module_master_id == false){
            $this->module_master_id = $id;
            return;
        }else{
            if($this->module_last_child_id == null || $this->module_last_child_id == ''){
                $this->module_last_child_id  = $this->module_master_id;
            }
            $id     = $this->module_last_child_id++;
        }

        //Ckeck if id exists
        $query       = CustomSql::quick_select(" SELECT * FROM `app_modules` WHERE `module_id` = $id ");
        if ($query === false) {
            return false;
        }else{
            $count   = $query->num_rows;
            if ($count >= 1) {
                $this->module_id_generator($range);
            }else{
                return $id+2;
            }
        }
    }

    public function file_creator($moduleSystemName, $fileType, $module_path, $fileName, $moduleFriendlyName){
        $fileCode            = '';
        $currentdate         = gmdate('Y-m-d');
        $moduleHandlerId     = $this->module_id_generator();

        $moduleConstantId    = strtoupper(str_replace(' ', '_', $moduleSystemName.'_'.'HANDLER_ID'));
        $moduleConstantName  = strtoupper(str_replace(' ', '_', $moduleSystemName.'_'.'HANDLER'));
        $className           = explode('.', $fileName)[0];

        if($fileType == 'handler_name'){
        $fileCode        = '
<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
    
    //Module Identity
    define('."'$moduleConstantId'".', '.$this->module_master_id.');
    define('."'$moduleConstantName'".', '."'$moduleFriendlyName'".');
    Auth::module_registration('.$moduleConstantId.', '.$moduleConstantName.');
    /**
        * *********************************************************************************************************
        * @_forProject: Shell Bone
        * @_purpose: This class handles/manages '.$moduleFriendlyName.' operations.
        * @_version Release: 1.0
        * @_created Date: '.$currentdate.'
        * @_author(s):Shell Bone Generator
        *   --------------------------------------------------------------------------------------------------
        *   1) Fullname of engineer. (Paul Glaydor)
        *      @contact Phone: (+231) 770558804
        *      @contact Mail: conteeglaydor@gmail.com
        * *********************************************************************************************************
    */

    class '.$className.' {
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
                $moduelCheck               = Auth::module_security('.$moduleConstantId.', $this->userId, $this->user_type, $this->account_character);
                if($moduelCheck === 200){
                    //CALL FUNCTIONS HERE!
                    $this->get();
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

    new '.$className.'();
            ';
        }

        if($fileType == 'view'){
            $childModuleId                   = $this->module_id_generator();
            $subModuleFunctionNameLowerCase  = explode('.', $fileName)[0];
            $subModuleFunctionName           = strtoupper(explode('.', $fileName)[0]);
            $fileCode                        = '
<?php
//SubModule Identity
define('."'MODULE_$moduleConstantId'".', '."'$this->module_master_id'".');
define('."'SUB_$subModuleFunctionName'".', '."'$childModuleId'".');
define('."'SUB_NAME_$subModuleFunctionName'".', '."'$subModuleFunctionNameLowerCase'".');
Auth::module_function_registration('."SUB_$subModuleFunctionName".', '."SUB_NAME_$subModuleFunctionName".', '."MODULE_$moduleConstantId".');

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages '.$moduleFriendlyName.' VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: '.$currentdate.'
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class '.$className.' {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('."SUB_$subModuleFunctionName".', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method get from give database
    public function get_data_from_database(){
        $query          = CustomSql::quick_select(" SELECT * FROM `` WHERE ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count === 1){
                $row    = mysqli_fetch_assoc($query);
                return $row;
            }else{
                return 404;
            }
        }
    }
}
            ';
        }

        if($fileType == 'edit'){
            $childModuleId                   = $this->module_id_generator();
            $subModuleFunctionNameLowerCase  = explode('.', $fileName)[0];
            $subModuleFunctionName           = strtoupper(explode('.', $fileName)[0]);
            $fileCode                        = '
<?php
//SubModule Identity
define('."'MODULE_$moduleConstantId'".', '."'$this->module_master_id'".');
define('."'SUB_$subModuleFunctionName'".', '."'$childModuleId'".');
define('."'SUB_NAME_$subModuleFunctionName'".', '."'$subModuleFunctionNameLowerCase'".');
Auth::module_function_registration('."SUB_$subModuleFunctionName".', '."SUB_NAME_$subModuleFunctionName".', '."MODULE_$moduleConstantId".');

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages '.$moduleFriendlyName.' VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: '.$currentdate.'
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class '.$className.' {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('."SUB_$subModuleFunctionName".', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates given record by id
    public function update_record($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "table");
        if($query === false){
            return 500;
        }else{
            if($query === 200){
                return 200;
            }else{
                return 400;
            }
        }
    }
}
            ';
        }

        if($fileType == 'delete'){
            $childModuleId                   = $this->module_id_generator();
            $subModuleFunctionNameLowerCase  = explode('.', $fileName)[0];
            $subModuleFunctionName           = strtoupper(explode('.', $fileName)[0]);
            $fileCode                        = '
<?php
//SubModule Identity
define('."'MODULE_$moduleConstantId'".', '."'$this->module_master_id'".');
define('."'SUB_$subModuleFunctionName'".', '."'$childModuleId'".');
define('."'SUB_NAME_$subModuleFunctionName'".', '."'$subModuleFunctionNameLowerCase'".');
Auth::module_function_registration('."SUB_$subModuleFunctionName".', '."SUB_NAME_$subModuleFunctionName".', '."MODULE_$moduleConstantId".');

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages '.$moduleFriendlyName.' operations.
 * @_version Release: 1.0
 * @_created Date: '.$currentdate.'
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class '.$className.' {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('."SUB_$subModuleFunctionName".', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates/deletes given record by id
    public function update_record($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "table");
        if($query === false){
            return 500;
        }else{
            if($query === 200){
                return 200;
            }else{
                return 400;
            }
        }
    }
}
            ';
        }

        if($fileType == 'add'){
            $childModuleId                   = $this->module_id_generator();
            $subModuleFunctionNameLowerCase  = explode('.', $fileName)[0];
            $subModuleFunctionName           = strtoupper(explode('.', $fileName)[0]);
            $fileCode                        = '
<?php

//SubModule Identity
define('."'MODULE_$moduleConstantId'".', '."'$this->module_master_id'".');
define('."'SUB_$subModuleFunctionName'".', '."'$childModuleId'".');
define('."'SUB_NAME_$subModuleFunctionName'".', '."'$subModuleFunctionNameLowerCase'".');
Auth::module_function_registration('."SUB_$subModuleFunctionName".', '."SUB_NAME_$subModuleFunctionName".', '."MODULE_$moduleConstantId".');

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages '.$moduleFriendlyName.' ADD operations.
 * @_version Release: 1.0
 * @_created Date: '.$currentdate.'
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class '.$className.' {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('."SUB_$subModuleFunctionName".', $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method add given record
    public function add_record($details){
        $query    = CustomSql::insert_array("table", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}
            ';
        }

        $myfile   = fopen($module_path.'/'.$fileName, "c") or die("Unable to open file!");
        fwrite($myfile, $fileCode);
        fclose($myfile);
        return 200;
    }
}

$module_birth_info = [
    "module_system_name"  => $argv[1],
    "module_name"         => $argv[2]
    // "module_icon"         => $argv[3]
];

(new ShellBoneModuleGenerator($module_birth_info));