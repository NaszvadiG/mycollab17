<?php

  /**
   * (re)build assets
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandBuildAssets extends CLICommandGenerator {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Build application assets';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      AngieApplication::rebuildAssets();

      $output->printMessage('Assets built');
    } // execute
  
  }