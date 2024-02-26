<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles server reponses. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */
 
//// Server Response ///
//SERVICE PLAN STATUS
DEFINE(500, 'DATABASE_ERROR');
DEFINE(501, 'SERVICE PLAN OVERDUE');
DEFINE(502, 'NO SERVICE PLAN');

//UNAUT
DEFINE(200, 'OK');
DEFINE(300, 'WRONG_METHOD');
DEFINE(301, 'UNAUTHORIZATION');
DEFINE(305, 'FUNCTION_UNAUTHORIZATION');
DEFINE(309, 'RESOURCE_UNAUTHORIZATION');
DEFINE(404, 'NOT FOUND');
DEFINE(400, 'BAD REQUEST');
DEFINE(201, 'MULTIPLE_PROFILES');

class Response{
    private $response;
    function __construct($responseType, $responseMessage, $others = NULL)
    {
        $this->response = array(
            'status'         => $responseType,
            'response_type'  => constant(strtoupper( str_replace(" ", "_", $responseType))),
            'message'        => $responseMessage,
            'response_body'  => $others
        );
    }

    public function send_response()
    {
        header('HTTP/1.0', 200);
        header('Content-Type: application/json');
        header('Accept: application/json');
        echo json_encode($this->response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit; 
    }

    public function get_response(){
        return json_encode($this->response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function get_status(){
        return $this->response['status'];
    }
}

