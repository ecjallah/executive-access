<?php
include_once dirname(__FILE__).'/Autoloader.class.php';
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

class MessageCenter{
    public $userId;
    public $user_type;
    public $permission;
    public $method;
    public $url;
    public $nodeServerLocation;
    public $nodeServerPort;
    function __construct()
    {
        $this->method                = $_SERVER['REQUEST_METHOD'];
        $this->url                   = $_SERVER['REQUEST_URI'];
        $this->nodeServerLocation    = "";
        $this->nodeServerPort        = "";
    }

    //NOTIFICATION INSERTER
    public function notification_inserter(array $data){
        $query    = CustomSql::insert_array('notifications', $data);
        if($query === false){
            return 500;
        }else if($query == 1){
            $this->instance_notification_fetcher($data['account_id']);
        }
    }

    //INSTANCE NOTIFICATION FETCHER
    public function instance_notification_fetcher($accountId){
        $query    = CustomSql::quick_select(" SELECT * FROM `notifications` WHERE account_id = $accountId AND `status` = 0 ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count>= 1){
                $data       = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data = [
                       "account_id"      => $row['account_id'],
                       "message"         => $row['message'],
                       "date"            => $row['date'],
                       "description"     => $row['message'],
                    ];
                }
                return $data;
            }else{
                return 404;
            }
        }
    }

    //MODULE REAL TIME FETCHER
    // public function module_request__real_time_notification($requestId){
    //     $query         = CustomSql::quick_select(" SELECT * FROM `module_requests` WHERE `id` = $requestId AND `triker_status` = 0 ");
    //     if($query === false){
    //         return 500;
    //     }else{
    //         $count     = $query->num_rows;
    //         if($count === 1){
    //             $sqlData                = $query->fetch_assoc();
    //             $healthcareId           = $sqlData['health_care_id'];
    //             $requestedModule        = $sqlData['requested_to'];
    //             //Get healthstaff that are assigned the above requested 'requestedModule' in the given hospital
    //             $assignedModuleStaffs   = Helper::get_module_related_roles($healthcareId, $requestedModule);
    //             $notificationDetails    = [];
    //             if(count($assignedModuleStaffs) >= 1){
    //                 //Has aleast more than one staff assigned this module
    //                 $notificationDetails[]     = ['healthcare_account_id'  => $healthcareId];
    //                 foreach ($assignedModuleStaffs as $staffId) {
    //                     $notificationDetails[] = [
    //                         'notification_type'     => "Request Notification",
    //                         'account_id'            => $staffId['user_id'],
    //                         'message'               => $sqlData['message'],
    //                         'message_triker_status' => $sqlData['triker_status'],
    //                         'notification_status'   => $sqlData['notification_status'],
    //                         'date'                  => date('F j, Y, g:i a', strtotime($sqlData['date']))
    //                     ];
    //                 }
    //             }else{
    //                 $notificationDetails[] = [
    //                     'notification_type'     => "Request Notification",
    //                     'healthcare_account_id' => $healthcareId,
    //                     'message'               => $sqlData['message'],
    //                     'message_triker_status' => $sqlData['triker_status'],
    //                     'notification_status'   => $sqlData['notification_status'],
    //                     'date'                  => date('F j, Y, g:i a', strtotime($sqlData['date']))
    //                 ];
    //             }

    //             $response  = $this->send_express_notification($notificationDetails);
    //             if($response == 'OK' || $response === 200){
    //                 return 200;
    //             }else{
    //                 return 400;
    //             }
    //             // return $response;
    //         }else{
    //             return 404;
    //         }
    //     }
    // }

    //This function sends notification to EXPRESS server
    public function send_express_notification($details){
        $curl      = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT                => $this->nodeServerPort,
            CURLOPT_URL                 => $this->nodeServerLocation,
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_ENCODING            => "",
            CURLOPT_MAXREDIRS           => 10,
            CURLOPT_TIMEOUT             => 30,
            CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST       => "POST",
            CURLOPT_HTTPHEADER    => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 3636984c-5a62-cf2c-73e3-9e2ef4e5f8df"
            ),
            CURLOPT_POSTFIELDS    => json_encode($details),
        ));
        $response  = curl_exec($curl);
        $err       = curl_error($curl);
        curl_close($curl);
        return $response;
    }

    //SMS NOTIFICATIONS
    public function send_sms($messageDetails, $userNumber, $codeType = 'verification'){
        $message                = $messageDetails;
        if($codeType == 'verification'){
            $message            = 'Your verification code is:'.' '.$messageDetails;
        }
        $parentClass            = new MessageCenter();
        $formatedNo             = $parentClass->format_phone_number($userNumber);
        $messageContent         = [
            [
                "to"            => $formatedNo,
                "from"          => "EXecutive Access",
                "body"          => $message,
                "routingGroup"  => "PREMIUM"
            ]
        ];

        $messenger  = new SmsHandler();
        $sendSms    = $messenger->send_message(json_encode($messageContent));
        if($sendSms === false){
            $result = [
                'status'  => 500,
                'message' => 'Your sms has failed!'
            ];
            return 500;
        }else{
            $result = [
                'status'  => 200,
                'message' => 'Sms sent successfully!'
            ];
            return 200;
        }
    }

    // formats phone number for sms
    public function format_phone_number($phoneNo)
    {
        //Get the country related to this given number
        $country     = "Liberia";
        $countryCode = 231;
        if($countryCode !== false)
        {
            $phoneNo = (substr($phoneNo, 0, 1) == '+')? substr($phoneNo, 1, strlen($phoneNo)) : $phoneNo;
            if(!empty(strpos($phoneNo, $countryCode)) !== false)
            {
                $phoneNo = (strlen($phoneNo) > 9 && strpos($phoneNo, $countryCode) == 0)? substr($phoneNo, strlen($countryCode), strlen($phoneNo)) : $phoneNo;
            }
            $phoneNo = (substr($phoneNo, 0, 1) == '0')? substr($phoneNo, 1, strlen($phoneNo)) : $phoneNo;
            $phoneNo = $countryCode.$phoneNo;
            return $phoneNo;
        }
        else {
            return 500;
        }
    }
    //USER INTERACTIVE MESSAGES AND NOTIFICATIONS CENTER
}