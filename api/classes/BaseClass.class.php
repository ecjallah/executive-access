<?php  
include_once dirname(__FILE__).'/Autoloader.class.php';
  /**
  * *********************************************************************************************************
  * @_forProject: MyWaste
  * @_purpose: This class contains the core methods and properties every class should have;
  * @_version Release: 1.0
  * @_created Date: January 20, 2021
  * @_author(s):
  *   --------------------------------------------------------------------------------------------------
  *   1) Fullname of engineer. (Enoch C. Jallah)
  *      @contact Phone: (+231) 775901684
  *      @contact Mail: enochcjallah@gmail.com
  * *********************************************************************************************************
  */



	class BaseClass
	{
        protected $error;
        protected $errorCode;
		protected $msg;
		// protected $environment = ENVIRONMENT;
		// protected $SMS = SMS_STATE;
		protected $pageCounter = 25;
		private   $countPerPage;
		private   $pageNum;
		private   $userId;
		private   $csql;
		protected $tblPaginationMetaData = [
			'initial' => 'unset',
			'total'   => 'unset'
		];
		// private   $userId;
		// protected $userType; 

		public function __construct($userId = null)
		{
			// $this->environment = ENVIRONMENT;
			// if ($this->environment == 'production') {
			// 	$this->set_SMS(true);
			// }
			// $sessions       = Adapter::authentication()->get_sessions();
			// $this->userId   = $sessions['user_id'];
			// $this->userType = $sessions['user_type'];
		}

        /** returns the error message */
        public function error()
        {
            return $this->error;
        }
        
        /** returns an HTTP error code */
        public function error_no()
        {
            return $this->errorCode;
        }

		/** returns the HTTP status code for successful processes */
		public function status(){
			return $this->errorCode;
		}

		public function msg(){
			return $this->msg;
		}

        /** sets the HTTP error code */
        protected function set_error_no($code){
          $this->errorCode = $code;
		}
		
        /** sets the HTTP error code */
        protected function set_error($error){
          $this->error = $error;
        }

		/** sets a message for successful processes */
		protected function set_msg($msg)
		{
			$this->msg = $msg;
		}

		/** sets an HTTP status code for successful processes */
		protected function set_status($code)
		{
			$this->errorCode = $code;
		}

		/** sets the id of the currently logged in user */
		protected function set_user_id($userId){
			$this->userId = $userId;
		}

		/** sents the type of the currently logged in user */
		protected function set_user_type($userType){
			$this->userId = $userType;
		}
	}
?>