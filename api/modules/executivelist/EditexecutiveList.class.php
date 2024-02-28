
<?php
//SubModule Identity
define('MODULE_EXECUTIVELIST_HANDLER_ID', '10020240227182744');
define('SUB_EDITEXECUTIVELIST', '10020240227182749');
define('SUB_NAME_EDITEXECUTIVELIST', 'EditexecutiveList');
Auth::module_function_registration(SUB_EDITEXECUTIVELIST, SUB_NAME_EDITEXECUTIVELIST, MODULE_EXECUTIVELIST_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages ExecutiveList VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2024-02-27
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class EditexecutiveList {
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
            $auth              = Auth::function_check(SUB_EDITEXECUTIVELIST, $this->userId, $this->user_type, $this->account_character);
            $this->permission  = $auth;
        }
    }

    //This method updates executive details
    public function update_executive_details($details, $identity){
        $query        = CustomSql::update_array($details, $identity, "executive_members");
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
            