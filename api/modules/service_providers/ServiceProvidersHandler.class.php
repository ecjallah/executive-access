<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

//Module Identity
define("SERVICE_PROVIDERS_MODULE_ID", '4000100');
define("SERVICE_PROVIDERS_MODULE_NAME", 'Service Providers');
Auth::module_registration(SERVICE_PROVIDERS_MODULE_ID, SERVICE_PROVIDERS_MODULE_NAME);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles packages for regular users. 
 * @_version Release: 1.0
 * @_created Date: 01/13/2022
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class ServiceProvidersHandler{
    
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
    function __construct(){
        if(isset($_SESSION['user_id'])){
            $this->userId              = $_SESSION['user_id'];
            $this->user_type           = $_SESSION['user_type'];
            $this->account_character   = $_SESSION['account_character'];
            $this->method              = $_SERVER['REQUEST_METHOD'];
            $this->url                 = $_SERVER['REQUEST_URI'];
            $moduelCheck               = Auth::module_security(SERVICE_PROVIDERS_MODULE_ID, $this->userId, $this->user_type, $this->account_character);
            if($moduelCheck === 200){
                //CALL FUNCTIONS HERE!
                $this->view_linked_service_providers();
                $this->lookup_service_provider();
                $this->link_hospital_to_ministry();
                $this->unlink_hospital_to_ministry();
            }else{
                $response = new Response($moduelCheck, 'Unauthorized Module: Contact Admin');
                $response->send_response();
            }
        }else{
            Logout::log_user_out();
        }
    }

    //This endpoint lookup service provider
    protected function lookup_service_provider(){
        if(strpos($this->url, '/api/lookup-service-provider-by-id') !== false)
        {
            if($this->method == 'GET'){
                //Get company id
                $providerId           = InputCleaner::sanitize($_GET['id']);
                $serviceProvider      = new ProviderPackages();
                $result               = $serviceProvider->get_vaild_service_provide_by_id($providerId);
                if($result === 500){
                    $response         = new Response(500, "Error returning service provider.");
                    $response->send_response();
                }else if($result === 404){
                    $response         = new Response(404, "Sorry, the is no service provider with the given search");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Service provider details", $result);
                    $response->send_response();
                }
            }else{                
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint returns all service provider that are linked to a business/ministry
    protected function view_linked_service_providers(){
        if($this->url == '/api/get-all-linked-service-providers')
        {
            if($this->method == 'GET'){
                //Get business id
                $businessId           = Helper::get_business_id($this->userId, $this->account_character);
                $providers            = new ProviderPackages();
                $result               = $providers->return_linked_hospital($businessId);
                if($result === 500){
                    $response         = new Response(500, "Error returning linked service providers.");
                    $response->send_response();
                }else if($result === 404){
                    $response         = new Response(404, "No linked service provider at this time.");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Linked service providers.", $result);
                    $response->send_response();
                }
            }else{   
                $response = new Response(300, "This endpoint accepts the GET method");
                $response->send_response();
            } 
        }
    }

    //This endpoint links hospitals to ministry
    protected function link_hospital_to_ministry(){
        if($this->url == '/api/link-hospital-to-ministry')
        {
            if($this->method == 'POST'){
                $_POST                = json_decode(file_get_contents('php://input'), true);
                $hospitalId           = InputCleaner::sanitize($_POST['hospital_id']);
                $ministryId           = Helper::get_business_id($this->userId, $this->account_character);
                $providers            = new ProviderPackages();
                $result               = $providers->link_health_service_provider_to_ministry($ministryId, $hospitalId);
                if($result === 500){
                    $response         = new Response(500, "Error linking hospital to ministry.");
                    $response->send_response();
                }else if($result === 400){
                    $response         = new Response(400, "This service provider is already linked.");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Health care service provider linked successfully.", $result);
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }

    //This endpoint links hospitals to ministry
    protected function unlink_hospital_to_ministry(){
        if($this->url == '/api/unlink-hospital-from-ministry')
        {
            if($this->method == 'POST'){
                $_POST                = json_decode(file_get_contents('php://input'), true);
                $hospitalId           = InputCleaner::sanitize($_POST['hospital_id']);
                $ministryId           = Helper::get_business_id($this->userId, $this->account_character);
                $providers            = new ProviderPackages();
                $result               = $providers->unlink_health_service_provider_to_ministry($ministryId, $hospitalId);
                if($result === 500){
                    $response         = new Response(500, "Error unlinking hospital to ministry.");
                    $response->send_response();
                }else if($result === 400){
                    $response         = new Response(400, "This service provider was never linked.");
                    $response->send_response();
                }else{
                    $response         = new Response(200, "Health care service provider has been unlinked successfully.", $result);
                    $response->send_response();
                }
            }else{
                $response = new Response(300, "This endpoint accepts the POST method");
                $response->send_response();
            }
        }
    }
}
(new ServiceProvidersHandler);