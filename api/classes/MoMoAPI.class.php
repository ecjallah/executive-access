<?php
include_once dirname(__FILE__).'/Autoloader.class.php';
/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles all SATCON API Request and Responses. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Enoch C. Jallah)
 *      @contact Phone: (+231) 775901684
 *      @contact Mail: enochcjallah@gmail.com
 * *********************************************************************************************************
 */

class MoMoAPI extends BaseClass
{
    private $hostURI          = "https://sandbox.momodeveloper.mtn.com";
    private $XRef             = "58e0b98f-90f5-4ae4-ad98-6bb2de4fe8ac";
    private $subKey           = "e37eb9fd6263491e834373629f337f11";
    private $accessToken      = '';
    private $source           = 'Ewallie-API';
    
    private $STBNumber;
	// function __construct(string $STBNumber)
	// {
    //     //00046657044189
    //     //2200b996
    //     $this->STBNumber = $STBNumber;
    // }

    private function create_api_key(){
        $request = Adapter::external_api_request();
        $request->set_headers([
            "X-Reference-Id: $this->XRef",
            "Content-Type: application/json",
            "Ocp-Apim-Subscription-Key: $this->subKey",
        ]);
        $response = $request->send($this->hostURI."/v1_0/apiuser/$this->XRef/apikey", 'POST', [
            "providerCallbackHost" => "mywaste.com"              
        ]);

        if ($response !== false) {
            if ($request->get_info_status() == 201) {
                return $response['apiKey'];
            } else {
                $this->set_error_no(503);
                $this->set_error("Unable to create key at the moment. Please try again later");
                return;
            }
        } else{
            $this->set_error_no($request->error_no());
            $this->set_error($request->error());
            return false;
        }
    }

    private function create_access_token(){
        $apiKey = $this->create_api_key();
        if ($apiKey !== false) {
            $basicAuth = "Basic ".base64_encode("$this->XRef:".$apiKey);
            $request = Adapter::external_api_request();
            $request->set_headers([
                "X-Reference-Id: $this->XRef",
                "Content-Type: application/json",
                "Ocp-Apim-Subscription-Key: $this->subKey",
                "Authorization: $basicAuth"
            ]);
            $response = $request->send($this->hostURI."/collection/token/", 'POST', [
                "providerCallbackHost" => "mywaste.com"              
            ]);

            if ($response !== false) {
                if ($request->get_info_status() == 200) {
                    $this->accessToken = "Bearer {$response['access_token']}";
                    return $this->accessToken;
                } else {
                    $this->set_error_no(503);
                    $this->set_error("Unable to access the API at the moment. Please try again later");
                    return;
                }
            } else {
                $this->set_error_no($request->error_no());
                $this->set_error($request->error());
                return false;
            }
            

        } else {
            return false; 
        }
    }

    public function request_to_pay(array $details){
        $accessToken = $this->create_access_token();
        $refId       = $this->generate_uuidv4();
        if ($accessToken !== false) {
            $request = Adapter::external_api_request();
            $request->set_headers([
                "X-Reference-Id: ".$refId,
                "X-Target-Environment: sandbox",
                "Ocp-Apim-Subscription-Key: $this->subKey",
            ]);
            $request->set_content_type("application/json");
            $request->set_authorization($accessToken);
            $response = $request->send($this->hostURI."/collection/v1_0/requesttopay", 'POST', [
                "amount" => $details['amount'],
                "currency" => "EUR",
                "externalId" => "1",
                "payer" => [
                    "partyIdType" => "MSISDN",
                    "partyId" => $details['phone_no']
                ],
                "payerMessage" => "Hi, this is a test stuff",
                "payeeNote" => $details['message']      
            ]);

            if ($response !== false) {
                if ($request->get_info_status() == 202) {
                    $transaction = $this->fetch_transaction_details($refId);
                    return $transaction;
                } else{
                    $message = is_array($response) && key_exists('message', $response) && !empty($response['message']) ? $response['message'] : '';
                    $this->set_error_no(503);
                    $this->set_error("Unable to complete transaction. ". $message);
                    return false; 
                }
            } else {
                $this->set_error_no($request->error_no());
                $this->set_error($request->error());
                return false;
            }

        } else {
            return false; 
        }
    }

    public function fetch_transaction_details(string $refId){
        $request = Adapter::external_api_request();
        $request->set_headers([
            "X-Reference-Id: ".$refId,
            "Content-Type: application/json",
            "X-Target-Environment: sandbox",
            "Ocp-Apim-Subscription-Key: $this->subKey",
        ]);
        $request->set_authorization($this->accessToken);
        $response = $request->send($this->hostURI."/collection/v1_0/requesttopay/$refId", 'GET');
        if ($response !== false) {
            if ($request->get_info_status() == 200) {
                return $response;
            } else {
                $this->set_error_no(500);
                $this->set_error("Unable to fetch transaction at the moment. Please try again later");
                return;
            }
        }else {
            $this->set_error_no($request->error_no());
            $this->set_error($request->error());
            return false;
        }
    }

    public function generate_uuidv4($data = null){
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

/* IMPLEMENTATION */
// $test = new MoMoAPI();
// echo $test->generate_uuidv4();
// echo '<pre>';
// print_r($test->request_to_pay([
//     "amount" => "300",
//     "phone_no" => "12345678",
//     "message" => "Please conform your my waste payment"
// ]));

// echo $test->error();
// echo $test->error_no();