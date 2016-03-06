<?php

  /**
   * Reconnection error
   *
   * @package angie.library.database
   * @subpackage errors
   */
  class DBReconnectError extends Error {
    
    /**
     * Construct reconnection error
     *
     * @param string $message
     */
    function __construct($message = null) {
      if(empty($message)) {
        $message = "Can't reconnect";
      } // if
      
      parent::__construct($message);
    } // __construct
    
  }