<?php
//SubModule Identity
define('MODULE_APPOINTMENTSECURITY_HANDLER_ID', '10020240301194745');
define('SUB_VIEWAPPOINTMENTSECURITY', '10020240301194748');
define('SUB_NAME_VIEWAPPOINTMENTSECURITY', 'ViewappointmentSecurity');
Auth::module_function_registration(SUB_VIEWAPPOINTMENTSECURITY, SUB_NAME_VIEWAPPOINTMENTSECURITY, MODULE_APPOINTMENTSECURITY_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages AppointmentSecurity VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-03-01
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class ViewappointmentSecurity {
    private $user_type;
    private $userId;
    public $permission;
    public $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId      = $_SESSION["user_id"];
            $this->user_type   = $_SESSION["user_type"];
            $this->permission  = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_VIEWAPPOINTMENTSECURITY, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

     //This method liikups an appointment
     public function lookup_appointment($companyId, $lookupVal){
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = '$companyId' AND visitor_name LIKE '%{$lookupVal}%' AND status != 'delete' AND `status` != 'completed'");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $keys   = [];
                $data   = [];
                $visitChecks = new ViewvisitChecks();
                while ($row = mysqli_fetch_assoc($query)) {
                    $row['executive_details'] = (new ViewexecutiveList())->return_executive_member_details($row['company_id'], $row['executive_id']);
                    $row['start_time']        = substr($row['start_time'], 0, strrpos($row['start_time'], ':'));
                    $row['end_time']          = substr($row['end_time'], 0, strrpos($row['end_time'], ':'));
                    $row['registered_items']  = $visitChecks->get_appointment_registered_items($row['id']);
                    $formatted                = date("l, M d, Y", strtotime($row['visit_date']));
                    $dateKey                  = strtotime(date('Y-m-d', strtotime($row['visit_date'])));
                    $index                    = count($keys);

                    if (in_array($dateKey, $keys)) {
                        $index = array_keys($keys, $dateKey)[0];
                    } else {
                        $keys[] = $dateKey;
                    }

                    if (!key_exists($index, $data)) {
                        $data[$index] = [
                            'formatted_date' => $formatted,
                            'appointments'   => [$row]
                        ];
                    } else {
                        $data[$index]['appointments'][] = $row; 
                    }
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}