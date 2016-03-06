<?php

  /**
   * Print something to the user
   *
   * This is default output handler. It just defines printMessage() method and 
   * can be used as silent output
   * 
   * @package angie.tools.cli
   * @subpackage output
   */
  class Output {

    /**
     * Log output (except aborts) or show everything
     *
     * @var bool
     */
    protected $log_output = false;

    /**
     * Skip default 'file created' etc messages (does not affect aborts)
     *
     * @var bool
     */
    protected $skip_default_filesystem_info = false;

    /**
     * @param string $log_file_path
     */
    function __construct($log_output = false, $skip_default_filesystem_info = false) {
      $this->log_output = (boolean) $log_output;
      $this->skip_default_filesystem_info = (boolean) $skip_default_filesystem_info;
    } // construct
    
    /**
     * Print line
     * 
     * @param integer $num
     */
    function printLine($num = 1) {
      // psssst!
    } // printLine
  
    /**
     * Print messages
     *
     * @param string $message
     * @param string $type
     */
    function printMessage($message, $type = null) {
      // psssst!
    } // printMessage

    /**
     * @param string $message
     * @param string|null $type
     * @throws Exception
     */
    function abortWithMessage($message, $type = null) {
      if (is_null($type)) {
        $type = 'error';
      } // if
      $this->printMessage($message, $type, true);
      if ($this->log_output) {
        throw new Exception($message);
      } else {
        die();
      } // if
    } // abortWithMessage
    
    /**
     * Print table
     * 
     * @param array $columns
     * @param array $data
     */
    function printTable($columns, $data) {
      
    } // printTable
    
    /**
     * Print content
     *
     * @param string $content
     */
    function printContent($content) {
      // psssst!
    } // printContent
    
    /**
     * Ask user a simple y / n question
     *
     * @param string $question
     * @return boolean
     */
    function ask($question) {
      $this->printMessage("$question (y/n): ");
      return strtolower(trim(fgets(STDIN))) == 'y';
    } // ask
    
  }