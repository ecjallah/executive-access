<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

/**
 * *********************************************************************************************************
 * @_forProject: MyWaste
 * @_purpose: This class handles the doctor appointments. 
 * @_version Release: 1.0
 * @_created Date: February 21, 2023
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

 class CustomSql extends Db{

  public $con;

  //Insert into database using array
  public static function insert_array($table, $record){
    $columns  = '';
    $values   = '';
    foreach ($record as $column => $value) {
      $columns  .= "`$column`, ";
      $values   .= (!empty($value)) ? "'$value', " : "NULL,";
    }; 
      
    $columns  = substr($columns, 0, (strlen($columns)- 2));
    $values   = substr($values, 0, (strlen($values)- 2));  
    
    $sql      = "INSERT INTO `$table` ($columns) VALUES ($values)";
    $query    = self::$conn->query($sql);
          if ($query == true) {
      return true;
    } else {
      return self::$conn->error;
    }

  }

  //Insert into database using array WITH RETURNS
  public static function insert_array_with_returns($table, $record){
    $columns  = '';
    $values   = '';
    foreach ($record as $column => $value) {
      $columns  .= "`$column`, ";
      $values   .= (!empty($value)) ? "'$value', " : "NULL,";
    }; 
      
    $columns  = substr($columns, 0, (strlen($columns)- 2));
    $values   = substr($values, 0, (strlen($values)- 2));
    
    $sql      = "INSERT INTO `$table` ($columns) VALUES ($values)";
    $query    = self::$conn->query($sql);
    if ($query == true) {
      return self::$conn->insert_id;
    }else{
      return self::$conn->error;
    }
  }

  //Insert into database with raw sql 
  public static function insert_raw($statment){

      $sql = "{$statment}";
      $query  = self::$conn->query($sql);

      return $query;
    
  }

  //Quickly Select from database with sql statment
  public static function quick_select($statment){
    $sql     = "$statment";
    $result  = self::$conn->query($sql);
    return $result;
  }

  //Update data with array
  public static function update_sql($sql){

      $query  = self::$conn->query($sql);
      return $query;
  }

  //Update date using array
  public static function update_array(array $record, array $identity, string $table){

    $values = '';

    $identityColumn  = $identity['column'];
    $identityValue   = $identity['value'];
    $identityDetails = '';

    if(is_array($identityColumn) && is_array($identityValue)){
      if(count($identityColumn) == count($identityValue))
      {
        for ($index=0; $index < count($identityColumn); $index++) { 
            $column = $identityColumn[$index];
            $value  = $identityValue[$index];
            $identityDetails .= "`".$column."` = '".$value."' AND ";
        }
        $identityDetails = substr($identityDetails, 0, (strlen($identityDetails) - 5));
      }
    }
    else{
      $identityDetails = "`$identityColumn`='$identityValue'";
    }

    foreach ($record as $column => $value) {
      $value     = (!empty($value)) ? "'$value'" : "NULL";
            $values   .= "`$column` = $value, ";
    }; 
    
    $values = substr($values, 0, (strlen($values)- 2));
    $sql    = "UPDATE `$table` SET $values WHERE $identityDetails";
    $query  = self::$conn->query($sql);
    if ($query == true) {
          return true;
    } 
    else {
        return self::$conn->error;
    }

  }

  //Deletes data from datatbase
  public static function delete_sql($table, $condition){
    $sql    = " DELETE FROM `{$table}` WHERE {$condition} ";
    $query  = self::$conn->query($sql);
    return $query;
  }

  //Raw delete sql function
  public static function raw_delete($statment){
    $sql     = "DELETE '{$statment}' ";
    $query   = self::$conn->query($sql);
    
    return $query;
  }

  // Autocommit off
  public static function commit_off(){
   self::$conn->autocommit(FALSE);
  }

  //Rollback
  public static function rollback(){
    self::$conn->rollback();
  }

  //Save commit
  public static function save(){
    self::$conn->commit();
  }

}

