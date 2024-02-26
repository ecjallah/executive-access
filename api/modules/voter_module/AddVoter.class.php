<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("ADD_VOTER_ACCOUNT_MODULE_ID", '9000');
define("ADD_VOTER_ACCOUNT_FUNCTION_ID", '90010');
define("ADD_VOTER_ACCOUNT_FUNCTION_NAME", 'Create voter account');
Auth::module_function_registration(ADD_VOTER_ACCOUNT_FUNCTION_ID, ADD_VOTER_ACCOUNT_FUNCTION_NAME, ADD_VOTER_ACCOUNT_MODULE_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Vote-Advisor
 * @_purpose: This class creates an account for voter. 
 * @_version Release: 1.0
 * @_created Date: 3/25/2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Hercules Nimley)
 *      @contact Phone: (+231) 0778636212
 *      @contact Mail: mnimley6@gmail.com
 * *********************************************************************************************************
*/

class AddVoter {

    private $mobile;
    private $dob;
    private $occupation;
    private $gender;
    private $county;

    function __construct(string $mobile, string $dob, string $occupation, string $gender, string $county) {
        $this->mobile       =   $mobile;
        $this->dob          =   $dob;
        $this->occupation   =   $occupation;
        $this->gender       =   $gender;
        $this->county       =   $county;
    }

    public function add() {
        try {
            if ( $this->does_voter_exist($this->mobile) == true ) {
                return array(
                    "status_code" => 403,
                    "body" => [
                        "message" => "Sorry, the mobile number belongs to another user",
                        "dataset" => array()
                    ]
                );
            }

            $query = CustomSql::quick_select(" INSERT INTO `voter_info`(`phone_number`, `date_of_brith`, `occupation`, `gender`, `county`) VALUES('{$this->mobile}', '{$this->dob}', '{$this->occupation}', '{$this->gender}', '{$this->county}') ");

            return array(
                "status_code" => 200,
                "body" => [
                    "message" => "Account created",
                    "dataset" => [
                        "voter_id" => Db::$conn->insert_id
                    ]
                ]
            );
        } 
        catch (\Throwable $th) {
            return array(
                "status_code" => 500,
                "body" => [
                    "message" => $th->getMessage(),
                    "dataset" => array()
                ]
            );
        }
    }

    private function does_voter_exist(string $mobile): bool {
        try {
            $query = CustomSql::quick_select(" SELECT * FROM `voter_info` WHERE `phone_number` = '{$mobile}' ");

            if ($query->num_rows < 1) {
                return false;
            } else {
                return true;
            }
        } 
        catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
?>