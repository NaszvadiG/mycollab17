<?php

  /**
   * Autoload error implementation
   *
   * @package angie.library.errors
   */
  class AutoloadError extends Error {
    
    /**
     * Construct autoload error instance
     *
     * @param string $class
     * @param string $message
     */
    function __construct($class, $message = null) {
      if(empty($message)) {
        $message = "Failed to load class '$class'";
      } // if
      
      parent::__construct($message, array(
        'class' => $class
      ));
    } // __construct
    
  } //AutoloadError