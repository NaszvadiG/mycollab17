<?php

  /**
   * Argument required error
   *
   * This error is thrown when required command argument is missing from command 
   * that is processed
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  class ArgumentRequiredCLIError extends Error {
    
    /**
    * Argument
    *
    * @var string
    */
    var $argument;
  
    /**
    * Constructor
    *
    * @param string $argument
    * @return ArgumentRequiredCLIError
    */
    function __construct($argument, $message = null) {
      if($message === null) {
        $message = "$argument is required";
      } // if
      
      $this->setArgument($argument);
      parent::__construct($message, true);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get argument
    *
    * @param null
    * @return string
    */
    function getArgument() {
      return $this->argument;
    } // getArgument
    
    /**
    * Set argument value
    *
    * @param string $value
    * @return null
    */
    function setArgument($value) {
      $this->argument = $value;
    } // setArgument
  
  } // ArgumentRequiredCLIError

?>