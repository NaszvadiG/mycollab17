<?php

  /**
   * General database error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBError extends Error {

    /**
     * @param string $error_number
     * @param array|null $error_message
     * @param null $message
     */
    function __construct($error_number, $error_message, $message = null) {
      if($message === null) {
        $message = "Problem with database interaction. Database said: '$error_message'";
      } // if
      
      parent::__construct($message, array(
        'error_number' => $error_number,
        'error_message' => $error_message,
      ));
    } // __construct
  
  }
