<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("NATIONAL_ISSUES_MODULE_ID", '9000');
define("NATIONAL_ISSUES_FUNCTION_ID", '90030');
define("NATIONAL_ISSUES_FUNCTION_NAME", 'Get national issues');
Auth::module_function_registration(NATIONAL_ISSUES_FUNCTION_ID, NATIONAL_ISSUES_FUNCTION_NAME, NATIONAL_ISSUES_MODULE_ID);

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

class issuesNotFoundException extends Exception {};

class GetNationalIssues {

    public function getIssues() {
        try {
            $issues = $this->issues();
            return array(
                "status_code" => 200,
                "body" => [
                    "message" => "National issues retrieved",
                    "dataset" => $issues
                ]
            );
        } 
        catch (\issuesNotFoundException $th) {
            return array(
                "status_code" => 404,
                "body" => [
                    "message" => $th->getMessage(),
                    "dataset" => array()
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

    private function issues() {
        try {
            $query = CustomSql::quick_select(" SELECT added_by, id, issue_title, base_value, description, date  FROM `issues` WHERE deleted = '0' ");

            if ( $query->num_rows < 1 ) {
                throw new issuesNotFoundException;
            } 

            $data = $baseValues = [];
            $issuesData = $query->fetch_all(MYSQLI_ASSOC);

            foreach ($issuesData as $key => $value) {
                $data['issues'][] = [
                    "added_by"      => $value["added_by"],
                    "id"            => $value["id"],
                    "issue_title"   => $value["issue_title"],
                    "base_value"    => $value["base_value"],
                    "description"   => $value["description"],
                    "date"          => $value["date"],
                ];

                $baseValues[] = $value["base_value"];
            }

            $data["total_points"] = array_sum($baseValues);

            return $data;

        } 
        catch (\issuesNotFoundException $th) {
            throw new issuesNotFoundException("Sorry, national issues have not yet been added");
        }
        catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
?>