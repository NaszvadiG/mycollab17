<?php

  /**
   * Console command
   * 
   * This object is populated by Angie_Console and populated with arguments and 
   * options. Parsed information is easily available through set of methods.
   *
   * @package angie.tools.cli
   * @subpackage commands
   */
  class CLICommand {
    
    /**
     * Arguments extracted from the command
     *
     * @var array
     */
    var $arguments;
    
    /**
     * Command options extracted from the command
     *
     * @var array
     */
    var $options;
    
    /**
     * Return argument on a specific possition
     *
     * @param integer $position
     * @return mixed
     */
    function getArgument($position) {
      return array_var($this->arguments, $position);
    } // getArgument
    
    /**
     * Return specific option
     * 
     * In case where option is no present $default value is returend
     *
     * @param mixed $names
     * @param mixed $default
     * @return mixed
     */
    function getOption($names, $default = '') {
      if(!is_array($names)) {
        $names = array($names);
      } // if
      
      if(is_foreachable($names)) {
        foreach($names as $name) {
          if(isset($this->options[$name])) {
            return $this->options[$name];
          } // if
        } // foreach
      } // if
      return $default;
    } // getOption
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get arguments
     *
     * @return array
     */
    function getArguments() {
      return $this->arguments;
    } // getArguments
    
    /**
     * Set arguments value
     *
     * @param array $value
     */
    function setArguments($value) {
      $this->arguments = $value;
    } // setArguments
    
    /**
     * Get options
     *
     * @return array
     */
    function getOptions() {
      return $this->options;
    } // getOptions
    
    /**
     * Set options value
     *
     * @param array $value
     */
    function setOptions($value) {
      $this->options = $value;
    } // setOptions
  
  }