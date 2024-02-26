<?php   
include_once dirname(__FILE__).'/Autoloader.class.php';
  /**
   * *********************************************************************************************************
   * @_forProject: MyWaste
   * @_purpose: Handles API calls made to external systems. 
   * @_version Release: 1.0
   * @_created Date: February 21, 2023
   * @_author(s):
   *   --------------------------------------------------------------------------------------------------
   *   1) Fullname of engineer. (Enoch C. Jallah)
   *      @contact Phone: (+231) 775901684
   *      @contact Mail: enochcjallah@gmail.com
   * *********************************************************************************************************
   */

    class ExternalAPIRequest extends BaseClass
    {
        private $contentType = 'text/plain';
        private $headers     = [];
        private $cookies     = '';
        private $response    = []; 
        private $info        = [];


        /** Sets the entire header of the request, replacing all the old values */
        public function set_headers(array $headers){
            $this->headers = $headers;
        }

        /** adds a new header to the already existing ones */
        public function add_header(string $header){
            if (is_array($this->headers) === true) {
                $this->headers[] = $header;
            } else {
                $this->headers = [$header];
            }
        }

        /** sets Cookie for the request, replacing all the old values */
        public function set_cookies(string $cookies){
            $this->cookies = $cookies;
        }

        /** adds the header Content-Type to the header array */
        public function set_content_type(string $contentType) {
            $this->contentType = $contentType;
            $this->add_header("Content-Type: $contentType");
        }

        /** sets the authorization header */
        public function set_authorization(string $authorization){
            $this->add_header("Authorization: $authorization");
        }

        /** formats the request body */
        public function format_request_body(array $body){
            $formattedBody = '';
            switch ($this->contentType) {
                case 'application/json':
                    $formattedBody = json_encode($body);
                    break;
                default:
                    $formattedBody = http_build_query($body);
                    break;
            }

            return $formattedBody;
        }

        /** makes an external api request */
        public function send(string $endPoint, string $requestMethod, array $body = []){
            $requestBody = $this->format_request_body($body);
            $requestOpt  = [
                CURLOPT_URL => "$endPoint",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $requestMethod,
            ];

            if ($this->cookies !== '') {
                $requestOpt[CURLOPT_COOKIE] = $this->cookies; 
            }

            if (is_array($this->headers) && count($this->headers) >= 1) {
                $requestOpt[CURLOPT_HTTPHEADER] = $this->headers; 
            }

            if (is_array($body) && count($body) >= 1) {
                $requestOpt[CURLOPT_POSTFIELDS] = $requestBody; 
            }
            
            $curl     = curl_init();
            curl_setopt_array($curl, $requestOpt);
            $response = curl_exec($curl);
            $reqInfo  = curl_getinfo($curl);
            $errNo    = curl_errno($curl);
            $error    = curl_error($curl);
            
            // exit;
            
            curl_close($curl); 
            if ($errNo == 0) {
                $this->info     = $reqInfo;
                $this->response = json_decode($response, true);
                $this->set_status(200);
                $this->set_msg("External request sucessful");
                return $this->response;
            } else {
                $this->set_error_no(500);
                $this->set_error("Unable to make request. $error");
                return false; 
            }
        }

        public function get_info_status(){
            return $this->info['http_code'];
        }

        /** returns the info from the request */
        public function get_info(){
            return $this->info;
        }

        /** returns the response of the request */
        public function get_response(){
            return $this->response; 
        }
    }
?>