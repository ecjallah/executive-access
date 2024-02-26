<?php   
include_once dirname(__FILE__).'/Autoloader.class.php';
  /**
   * *********************************************************************************************************
   * @_forProject: MyWaste
   * @_purpose: This class act as an adapter for other classes. 
   * @_version Release: 1.0
   * @_created Date: February 21, 2023
   *   --------------------------------------------------------------------------------------------------
   *   1) Fullname of engineer. (Enoch C. Jallah)
   *      @contact Phone: (+231) 775901684
   *      @contact Mail: enochcjallah@gmail.com
   * *********************************************************************************************************
   */

   class Adapter
   {
    
        public static function external_api_request(){
            return new ExternalAPIRequest();
        }

        public static function momo_api(string $stbNumber){
            return new MoMoAPI($stbNumber);
        }
        
   }
   