<?php

  /**
   * List all enabled modules command
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandModules extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List all enabled modules';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $output->printMessage('Enabled modules:');

      foreach(AngieApplication::getEnabledModuleNames() as $name) {
        $output->printMessage($name);
      } // foreach
    } // execute
  
  }