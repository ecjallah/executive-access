
<?php
//SubModule Identity
define('MODULE_DASHBOARD_HANDLER_ID', '10020231007220318');
define('SUB_EDITDASHBOARD', '10020231007220323');
define('SUB_NAME_EDITDASHBOARD', 'Editdashboard');
Auth::module_function_registration(SUB_EDITDASHBOARD, SUB_NAME_EDITDASHBOARD, MODULE_DASHBOARD_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Dashboard VIEW/GET operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-07
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Editdashboard {
    private $user_type;
    private $userId;
    private $account_character;
    public $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('SUB_EDITDASHBOARD', $this->userId, $this->user_type);
            $this->permission  = $auth;
        }
    }

    //This method updates given record by id
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
            