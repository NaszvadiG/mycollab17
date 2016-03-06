<?php

  /**
   * CLI command handler does not exist
   *
   * This error is thrown when we try to contruct command handler that is not 
   * defined in CLI tool
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  class CLICommandHandlerDnxError extends Error {
    
    /**
    * Command hame
    *
    * @var string
    */
    var $command;
  
    /**
    * Constructor
    *
    * @param string $command
    * @return CLICommandHandlerDnxError
    */
    function __construct($command, $message = null) {
      if($message === null) {
        $message = "Command '$command' handler is missing";
      } // if
      
      $this->setCommand($command);
      parent::__construct($message, true);
    } // __construct
    
    /**
    * Return additional error params
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array('command' => $this->getCommand());
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get command
    *
    * @param null
    * @return string
    */
    function getCommand() {
      return $this->command;
    } // getCommand
    
    /**
    * Set command value
    *
    * @param string $value
    * @return null
    */
    function setCommand($value) {
      $this->command = $value;
    } // setCommand
  
  } // CLICommandHandlerDnxError

?>