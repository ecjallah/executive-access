
<?php

//SubModule Identity
define('MODULE_DASHBOARD_HANDLER_ID', '10020231007220318');
define('SUB_ADDDASHBOARD', '10020231007220327');
define('SUB_NAME_ADDDASHBOARD', 'Adddashboard');
Auth::module_function_registration(SUB_ADDDASHBOARD, SUB_NAME_ADDDASHBOARD, MODULE_DASHBOARD_HANDLER_ID);

/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles/manages Dashboard ADD operations.
 * @_version Release: 1.0
 * @_created Date: 2023-10-07
 * @_author(s):Shell Bone Generator
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class Adddashboard {
    private $user_type;
    private $userId;
    public $permission;

    function __construct(){
        if(isset($_SESSION["user_id"])){
            $this->userId              = $_SESSION["user_id"];
            $this->user_type           = $_SESSION["user_type"];
            $this->permission          = null;

            //Check if user has right to access this class(this module function)
            $auth              = Auth::function_check('SUB_ADDDASHBOARD', $this->userId, $this->user_type);
            $this->permission  = $auth;
        }
    }

    //This method add given record
    public function add_record($details){
        $query    = CustomSql::insert_array("table", $details);
        if($query === false){
            return 500;
        }else{
            return 200;
        }
    }
}
            