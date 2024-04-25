<?php   
  /**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Appointment VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-28
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

    class SmsHandler
    {
      public  $error;
      private $username;
      private $password;
      private $url;
      function __construct()
      {
        $this->username = 'akamara';
        $this->password = 'CoM2Me@tammacorp.com';
        $this->url      = 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30';
      }

      public function send_message($post_body){
        $post_body = json_decode($post_body, true);
        foreach ($post_body as $message => $details) {
          $dest    = $details['to'];
          if (strpos($dest, '23188') !== false || strpos($dest, '23155') !== false) {
            $post_body[$message]['from'] = ['type' => "REPLIABLE"];
          }else{
            $post_body[$message]['from'] = "ePaD";
          }
        }

        $post_data  = json_encode($post_body, true);
        $ch         = curl_init( );
        $headers    = array(
          'Content-Type:application/json',
          'Authorization:Basic '. base64_encode("$this->username:$this->password")
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $ch, CURLOPT_URL, $this->url);
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data);
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        $output = array();
        $output['server_response'] = curl_exec( $ch );
        $curl_info                 = curl_getinfo( $ch );
        $output['http_status']     = $curl_info[ 'http_code' ];
        $output['error']           = curl_error($ch);
        curl_close( $ch );

        if ($output['http_status'] != 201) {
          $this->error = $output;
          // return $this->error;
          return false;
        }else{
          return true;
        }
      } 
    }