<?php
//SubModule Identity
define('MODULE_APPOINTMENT_HANDLER_ID', '10020240228203211');
define('SUB_VIEWAPPOINTMENT', '10020240228203214');
define('SUB_NAME_VIEWAPPOINTMENT', 'View appointment');
Auth::module_function_registration(SUB_VIEWAPPOINTMENT, SUB_NAME_VIEWAPPOINTMENT, MODULE_APPOINTMENT_HANDLER_ID);

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

class Viewappointment {
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
            $auth              = Auth::function_check(SUB_VIEWAPPOINTMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method returns all appointments
    public function return_all_appointments($companyId, $pager, $filter = null, $type = null, $approval_status = 'pending'){
        $typeCondition = '';
        if($type != null){
            $typeCondition = " AND appointment_type = 'online' AND approval_status = $approval_status ";
        }

        $pageCond     = '';
        if ($pager != null) {
            $count    = 15; 
            $pageNum  = intval($pager);
            $offset   = $pageNum * $count;
            $pageCond = $pager != null ? " LIMIT $count OFFSET $offset" : '';
        }

        $filterCond     = '';
        if($filter != null){
            $filterCond = " AND status = '$filter' ";
        }
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = '$companyId' AND status != 'delete' $filterCond $typeCondition ORDER BY `visit_date` ASC $pageCond");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count >= 1){
                $keys   = [];
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $row['start_day']         = gmdate('d', strtotime($row['visit_date']));
                    $row['start_month']       = gmdate('m', strtotime($row['visit_date']));
                    $row['start_year']        = gmdate('Y', strtotime($row['visit_date']));
                    $row['start_time']        = substr($row['start_time'], 0, strrpos($row['start_time'], ':'));
                    $row['end_time']          = substr($row['end_time'], 0, strrpos($row['end_time'], ':'));
                    $row['executive_details'] = (new ViewexecutiveList())->return_executive_member_details($row['company_id'], $row['executive_id']);
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

    //This method returns department appointments
    public function return_department_appointments($companyId, $departmentId, $pager, $filter = null){
        $pageCond     = '';
        if ($pager != null) {
            $count    = 15; 
            $pageNum  = intval($pager);
            $offset   = $pageNum * $count;
            $pageCond = $pager != null ? " LIMIT $count OFFSET $offset" : '';
        }

        $filterCond     = '';
        if($filter != null){
            $filterCond = " AND status = '$filter' ";
        }

        $query     = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = '$companyId' AND department_id =$departmentId AND status != 'delete' $filterCond ORDER BY `visit_date` ASC $pageCond");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            if($count >= 1){
                $keys   = [];
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $row['start_day']         = gmdate('d', strtotime($row['visit_date']));
                    $row['start_month']       = gmdate('m', strtotime($row['visit_date']));
                    $row['start_year']        = gmdate('Y', strtotime($row['visit_date']));
                    $formatted                = date("l, M d, Y", strtotime($row['visit_date']));
                    $row['executive_details'] = (new ViewexecutiveList())->return_executive_member_details($row['company_id'], $row['executive_id']);
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

    //This method returns appointment details
    public function get_appointment_details($companyId, $id){
        $visitChecks    = new ViewvisitChecks();
        $query          = CustomSql::quick_select(" SELECT * FROM `appointments` WHERE company_id = '$companyId' AND id = $id AND status != 'delete' ");
        if($query === false){
            return 500;
        }else{
            $count      = $query->num_rows;
            if($count === 1){
                $data   = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $data[] = $row;
                    $data['registered_items'] = $visitChecks->get_appointment_registered_items($id);
                }
                return $data;
            }else{
                return 404;
            }
        }
    }
}