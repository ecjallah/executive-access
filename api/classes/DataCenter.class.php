<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php';
// session_start();
/**
 * *********************************************************************************************************
 * @_forProject: eHealth | Developed By: TEES BIT
 * @_purpose: This class handles all data modifications.
 * @_version Release: 1.0
 * @_created Date: 10/20/2021
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
*/

class DataCenter{
    //This function return records base on all
    public function all($table, $condition, $groupBy = null, $limit = null){
        $todayDate    = (new \DateTime())->format('Y-m-d'); 
        $query        = CustomSql::quick_select(" SELECT * FROM $table WHERE {$condition} $groupBy $limit ");
        // return "SELECT * FROM $table WHERE {$condition} $groupBy $limit";
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records base on all(RAW)
    public function raw_sql_all($sql){
        $todayDate    = (new \DateTime())->format('Y-m-d'); 
        $query        = CustomSql::quick_select(" $sql ");
        if($query === false){
            return 500;
        }else{
            $count    = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records base on current day
    public function daily($table, $colum, $condition, $groupBy = null, $condition2 = '', $limit = null){
        $todayDate    = (new \DateTime())->format('Y-m-d'); 
        $query        = CustomSql::quick_select(" SELECT * FROM $table WHERE {$condition} AND DATE($colum) = CURDATE() {$condition2} AND DATE($colum) = CURDATE() $groupBy $limit ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records base on current day(RAW)
    public function raw_sql_daily($sql, $colum, $sql_end = ''){
        $todayDate    = (new \DateTime())->format('Y-m-d'); 
        $query        = CustomSql::quick_select(" {$sql} AND DATE($colum) = CURDATE() AND DATE($colum) = CURDATE() {$sql_end} ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records from yesterday
    public function yesterday($table, $colum, $condition, $groupBy = null, $condition2 = '', $limit = null){
        $query     = CustomSql::quick_select(" SELECT * FROM {$table} WHERE {$condition} AND date({$colum}) = DATE(NOW() - INTERVAL 1 DAY) {$condition2} AND date($colum) = DATE(NOW() - INTERVAL 1 DAY)");
          if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records from yesterday(RAW)
    public function raw_sql_yesterday($sql, $colum, $sql_end = ''){
        $query     = CustomSql::quick_select(" {$sql} AND date({$colum}) = DATE(NOW() - INTERVAL 1 DAY) AND date($colum) = DATE(NOW() - INTERVAL 1 DAY) {$sql_end}");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            $data  = [];
            while ($row = mysqli_fetch_assoc($query)) {
                $data[] = ["total" => $count, "data" => $row];
            }
            return ["total" => $count,];
        }
    }

    //This function return records from this weekly
    public function weekly($table, $colum, $condition, $groupBy = null, $condition2 = '', $limit = null){
        $query    = CustomSql::quick_select(" SELECT * FROM {$table} WHERE $condition AND YEARWEEK($colum) = YEARWEEK(NOW()) $groupBy ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function return records from this weekly(RAW)
    public function raw_sql_weekly($sql, $colum, $sql_end = ''){
        $query    = CustomSql::quick_select(" $sql AND YEARWEEK($colum) = YEARWEEK(NOW()) {$sql_end} ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function returns records from this monthly 
    public function monthly($table, $colum, $condition, $groupBy = null, $condition2 = '', $limit = null){
        $query     = CustomSql::quick_select(" SELECT * FROM {$table} WHERE $condition AND MONTH({$colum}) = MONTH(CURRENT_DATE()) AND YEAR({$colum}) = YEAR(CURRENT_DATE()) $groupBy ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function returns records from this monthly(RAW)
    public function raw_sql_monthly($sql, $colum, $sql_end = ''){
        $query     = CustomSql::quick_select(" {$sql} AND MONTH({$colum}) = MONTH(CURRENT_DATE()) AND YEAR({$colum}) = YEAR(CURRENT_DATE()) {$sql_end} ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function returs records from this year
    public function yearly($table, $colum, $condition, $groupBy = null, $condition2 = '', $limit = null){
        $query     = CustomSql::quick_select(" SELECT * FROM {$table} WHERE $condition AND YEAR({$colum}) = YEAR(CURRENT_DATE()) $groupBy ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function returs records from this year
    public function raw_sql_yearly($sql, $colum, $sql_end = ''){
        $query     = CustomSql::quick_select(" {$sql} AND YEAR({$colum}) = YEAR(CURRENT_DATE()) {$sql_end} ");
        if($query === false){
            return 500;
        }else{
            $count = $query->num_rows;
            return ["total" => $count, "data" => $query];
        }
    }

    //This function calculates the total amount
    public function total_amount(array $amount){
        return array_sum($amount);
    }
}