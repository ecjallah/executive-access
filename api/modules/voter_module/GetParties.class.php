<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';

define("PARTIES_MODULE_ID", '9000');
define("PARTIES_FUNCTION_ID", '90020');
define("PARTIES_FUNCTION_NAME", 'Get political parties');
Auth::module_function_registration(PARTIES_FUNCTION_ID, PARTIES_FUNCTION_NAME, PARTIES_MODULE_ID);

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

class notFoundException extends Exception {};

class GetParties {

    public function getPartyList() {
        try {
            $parties = $this->getPartiesAndCandidates();
            return array(
                "status_code" => 200,
                "body" => [
                    "message" => "Party and candidates retrieved",
                    "dataset" => $parties
                ]
            );
        } 
        catch (\notFoundException $th) {
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

    private function getPartiesAndCandidates(): array {
        try {
            $query = CustomSql::quick_select(" SELECT * FROM `user_accounts`  WHERE user_type = '2' ");

            if ( $query->num_rows < 1 ) {
                throw new notFoundException;
            } 

            return $this->attachCandidatesToParty( $query->fetch_all(MYSQLI_ASSOC) );
        } 
        catch (\notFoundException $th) {
            throw new notFoundException("Sorry, political parties have not yet been added");
        }
        catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function attachCandidatesToParty(array $parties) {
        try {
            foreach ($parties as $key => $party) 
            {
                $partyId = $party['user_id'];
                $query = CustomSql::quick_select(" SELECT * FROM `candidates` WHERE `party_id` = '{$partyId}' ");

                if ( $query->num_rows < 1 ) {
                    $parties[$key]["candidates"] = [];
                } 

                $parties[$key]["candidates"] = $query->fetch_all(MYSQLI_ASSOC);
            }

            return $parties;

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
?>