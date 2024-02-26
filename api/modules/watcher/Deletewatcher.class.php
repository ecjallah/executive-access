<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/api/classes/Autoloader.class.php";
//SubModule Identity
define('MODULE_WATCHER_HANDLER_ID', '10020231005170803');
define('SUB_DELETEWATCHER', '10020231005170810');
define('SUB_NAME_DELETEWATCHER', 'Deletewatcher');
Auth::module_function_registration(SUB_DELETEWATCHER, SUB_NAME_DELETEWATCHER, MODULE_WATCHER_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Watcher operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-05
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Deletewatcher {
    private $user_type;
    private $userId;
    private $account_character;
    private $method;
    private $url;
    private $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->user_type             = $_SESSION["user_type"];
            $this->userId                = $_SESSION["user_id"];
            $this->account_character     = $_SESSION["account_character"];
            $this->permission            = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check(SUB_DELETEWATCHER, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates/deletes given record by id
    public function update_record($details, $identity){
        $query            = CustomSql::update_array($details, $identity, "table");
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