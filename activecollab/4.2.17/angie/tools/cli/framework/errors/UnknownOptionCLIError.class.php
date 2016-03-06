<?php

  /**
   * Unknown option error
   *
   * This error is thrown when we encounter an uknown option while processing CLI 
   * command
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  class UnknownOptionCLIError extends Error {
  
    /**
     * Option
     *
     * @var string
     */
    private $option;
    
    /**
     * Constructor
     *
     * @param string $option
     * @param string $message
     */
    function __construct($option, $message = null) {
      if($message === null) {
        $message = "Unknown option '$option'";
      } // if
      
      $this->setOption($option);
      parent::__construct($message, true);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get option
     * 
     * @return string
     */
    function getOption() {
      return $this->option;
    } // getOption
    
    /**
     * Set option value
     *
     * @param string $value
     */
    function setOption($value) {
      $this->option = $value;
    } // setOption
  
  }