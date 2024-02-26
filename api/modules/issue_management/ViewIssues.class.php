<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
    define("VIEW_ISSUES_MODULE_ID", '3000100');
    define("VIEW_ISSUES_FUNCTION_ID", '3000101');
    define("VIEW_ISSUES_FUNCTION_NAME", 'View Issues');
    Auth::module_function_registration(VIEW_ISSUES_FUNCTION_ID, VIEW_ISSUES_FUNCTION_NAME, VIEW_ISSUES_MODULE_ID);

    /**
     * *********************************************************************************************************
     * @_forProject: Shell Bone
     * @_purpose: This class View/shows issues. 
     * @_version Release: 1.0
     * @_created Date: 11/23/2020
     * @_author(s):
     *   --------------------------------------------------------------------------------------------------
     *   1) Fullname of engineer. (Paul Glaydor)
     *      @contact Phone: (+231) 770558804
     *      @contact Mail: conteeglaydor@gmail.com
     * *********************************************************************************************************
     */

    class ViewIssues{
        function __construct(){
            if(isset($_SESSION['user_id'])){
                $this->userId              = $_SESSION['user_id'];
                $this->user_type           = $_SESSION['user_type'];
                $this->account_character   = $_SESSION['account_character'];
                $this->permission          = null;

                //Check if user has right to access this class(this module function)
                $auth              = Auth::function_check(VIEW_ISSUES_FUNCTION_ID, $this->userId, $this->user_type, $this->account_character);
                $this->permission  = $auth;
            }
        }

        //This method returns all created offers
        public function get_all_issues(){
            $query        = CustomSql::quick_select(" SELECT * FROM `issues` WHERE `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count    = $query->num_rows;
                if($count >= 1){
                    $data             = [];
                    $totalDollarValue = 0;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $totalDollarValue += $this->convert_base_value_to_dollar($row['base_value']);
                        $data['issues'][]    = [
                            "id"              => $row['id'],
                            "issue_title"     => $row['issue_title'],
                            "base_value"      => $this->convert_base_value_to_dollar($row['base_value']),
                            "raw_base_value"  => $row['base_value'],
                            "description"     => $row['description'],
                            "date"            => $row['date'],
                            "added_by"        => $row['added_by']
                        ];
                    }
                    $data['total_dollar_value'] = $totalDollarValue;
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method returns package by id
        public function get_issue_by_id($issueId){
            $query          = CustomSql::quick_select(" SELECT * FROM `issues` WHERE id = $issueId AND `deleted` = 0 ");
            if($query === false){
                return 500;
            }else{
                $count      = $query->num_rows;
                if($count === 1){
                    $row    = mysqli_fetch_assoc($query);
                    $data   = [
                        "id"              => $row['id'],
                        "issue_title"     => $row['issue_title'],
                        "base_value"      => $this->convert_base_value_to_dollar($row['base_value']),
                        "raw_base_value"  => $row['base_value'],
                        "description"     => $row['description'],
                        "date"            => $row['date'],
                        "added_by"        => $row['added_by']
                    ];
                    return $data;
                }else{
                    return 404;
                }
            }
        }

        //This method converts base values to dollar amount
        public function convert_base_value_to_dollar($baseValue){
            //1 MILLION MONEY VALUE
            return intval($baseValue)*intval(1000000);
        }
    }
