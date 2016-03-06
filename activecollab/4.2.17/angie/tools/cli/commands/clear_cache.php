<?php

  /**
   * Clear cache
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandClearCache extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Clear cache';
  
    /**
     * Execute the command
     * 
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initProject();
      AngieApplication::cache()->clear();
      $output->printMessage('Cache is cleared');
    } // execute
  
  }