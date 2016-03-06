<?php

  /**
   * Invalid project error
   *
   * CLI inits project based on path (you need to provide path to the project in 
   * order to init it). This error is thrown if path does not exist or if it's not
   * a valid Angie project
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  class InvalidProjectCLIError extends Error {
    
    /**
    * Project path
    *
    * @var string
    */
    var $project_path;
  
    /**
    * Constructor
    *
    * @param string $project_path
    * @param string $message
    * @return InvalidProjectCLIError
    */
    function __construct($project_path, $message = null) {
      if($message === null) {
        $message = "'$project_path' is not a valid Angie project";
      } // if
      
      $this->setProjectPath($project_path);
      parent::__construct($message, true);
    } // __construct
    
    /**
    * Return additional error params
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'project_path' => $this->getProjectPath(),
      );
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get project_path
    *
    * @param null
    * @return string
    */
    function getProjectPath() {
      return $this->project_path;
    } // getProjectPath
    
    /**
    * Set project_path value
    *
    * @param string $value
    * @return null
    */
    function setProjectPath($value) {
      $this->project_path = $value;
    } // setProjectPath
  
  } // InvalidProjectCLIError

?>