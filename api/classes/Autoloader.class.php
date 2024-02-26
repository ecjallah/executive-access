<?php
if(!isset($_SESSION)){
    session_start();
}
// header('Access-Control-Allow-Origin: http://cfmp.local');
// header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

include_once(dirname(__FILE__) . '/../config/Db.class.php');
class Autoloader{
    function __construct()
    {
        define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
        spl_autoload_register(function($className){
            $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            if(file_exists(__ROOT__. '/api/config' .$className . ".class.php")){
                include_once __ROOT__.'/api/config/' .$className . ".class.php";
            }else if(file_exists(__ROOT__. '/api/classes/' . $className. ".class.php")){
                include_once __ROOT__.'/api/classes/' . $className. ".class.php";
            }else if(file_exists(__ROOT__. '/api/modules/user_management/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/user_management/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/staff_management/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/staff_management/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/issue_management/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/issue_management/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/county/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/county/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/service_providers/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/service_providers/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/candidates/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/candidates/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/election_management/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/election_management/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/candidates/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/candidates/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/employee_visit_check/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/employee_visit_check/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/party_issue_priority/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/party_issue_priority/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/generic_users_module/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/generic_users_module/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/ussd/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/ussd/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/party_management/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/party_management/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/pollingmanagement/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/pollingmanagement/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/watcher/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/watcher/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/voter_module/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/voter_module/' . $className. ".class.php";
            }
            else if(file_exists(__ROOT__. '/api/modules/dashboard/' . $className. ".class.php")){
                include_once __ROOT__.'/api/modules/dashboard/' . $className. ".class.php";
            }
            else{
                echo "Can't find the requested class anywhere on this Api";
            }
        });
    }
}

(new Autoloader());

