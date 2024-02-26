<?php
include_once dirname(__FILE__).'/Autoloader.class.php';

  /**
    * *********************************************************************************************************
    * @_forProject: MyWaste
    * @_purpose: This class clean all input for proper database operations
    * @_version Release: 1.0
    * @_created Date: February 21, 2023
    * @_dependencies: Bootstrap 4 and Above (HTML Component)
    * @_author(s):
    *   --------------------------------------------------------------------------------------------------
    *   1) Fullname of engineer. (Enoch C. Jallah)
    *      @contact Phone: (+231) 775901684
    *      @contact Mail: enochcjallah@gmail.com
    * *********************************************************************************************************
    */
class InputCleaner{

   public $dirtyInput;

   function __construct($dirtyInput)
   {
       
   }

   //Clean an input
   public static function sanitize($dirtyInput)
   {
        if(!is_array($dirtyInput))
        {
            $connect   = Db::$conn;
            $newString = htmlentities($dirtyInput);
            $newString = strip_tags($newString);
            $newString = mysqli_real_escape_string($connect, $newString);
            return $newString;
        }
        else{
            foreach ($dirtyInput as $key => $value) {
                $dirtyInput[$key] = self::sanitize($value);
            }
            return $dirtyInput;
        }
    }

    //Unclean an array
    public static function unsanitize($sanitizedString)
    {

        if(!is_array($sanitizedString))
        {
            $connect   = Db::$conn;
            $newString = html_entity_decode($sanitizedString);
            $newString = stripslashes($newString);
            return $newString;
        }
        else{
            foreach ($sanitizedString as $key => $value) {
                $sanitizedString[$key] = self::unsanitize($value);
            }
            return $sanitizedString;
        }
    }
}

