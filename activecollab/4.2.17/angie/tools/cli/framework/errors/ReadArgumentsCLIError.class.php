<?php

  /**
   * Read CLI arguments error
   *
   * This error is thrown when we fail to read CLI arguments
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  class ReadArgumentsCLIError extends Error {
  
    /**
    * Constructor
    *
    * @param string $message
    * @return ReadArgumentsCLIError
    */
    function __construct($message = null) {
      if($message === null) {
        $message = 'Failed to read CLI arguments';
      } // if
      parent::__construct($message);
    } // __construct
  
  }