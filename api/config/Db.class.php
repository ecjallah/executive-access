<?php
// include_once($_SERVER['DOCUMENT_ROOT'].'/api/classes/Autoloader.class.php');


class Db {
  private $host;
  private $user;
  private $password;
  private $database;

  public static $conn;
  public function connect(){
    $this->host        = "localhost";
    // $this->user        = "vote-advisor";
    $this->user        = "root";
    $this->password    = "password@123";
    $this->database    = "executive_access";

    $connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);
    if($connection == false){
      die("Error connecting to database!");
    }else{
      self::$conn = $connection;
    }
  }
  
} $conn = new Db();
  $conn->connect();

?>