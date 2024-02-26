<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
//SubModule Identity
define('MODULE_POLLINGMANAGEMENT_HANDLER_ID', '10020231003184411');
define('SUB_DELETEPOLLINGMANAGEMENT', '10020231003184418');
define('SUB_NAME_DELETEPOLLINGMANAGEMENT', 'Delete Polling Management');
Auth::module_function_registration(SUB_DELETEPOLLINGMANAGEMENT, SUB_NAME_DELETEPOLLINGMANAGEMENT, MODULE_POLLINGMANAGEMENT_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages PollingManagement operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-03
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class DeletepollingManagement {
    private $user_type;
    private $userId;
    public $permission;
    private $account_character;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->user_type             = $_SESSION["user_type"];
            $this->userId                = $_SESSION["user_id"];
            $this->account_character     = $_SESSION["account_character"];
            $this->permission            = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_DELETEPOLLINGMANAGEMENT, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates/deletes given record by id
    public function delete_precinct($details, $identity){
        $query        = CustomSql::update_array($details, $identity, "precincts");
        if($query === false){
            return 500;
        }else{
            if($query === 200){
                return 200;
            }else{
                return 400;
            }
        }
    }

    //This method updates/deletes polling center by id
    public function delete_polling_center($details, $identity){
        $query        = CustomSql::update_array($details, $identity, "polling_centers");
        if($query === false){
            return 500;
        }else{
            if($query === 200){
                return 200;
            }else{
                return 400;
            }
        }
    }
}