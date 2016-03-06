<?php

  /**
   * List all models command
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandModels extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List all defined models';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), explode(',', APPLICATION_MODULES));

      $tables = AngieApplicationModel::getTables();

      if($tables) {
        $names = array();

        foreach($tables as $table) {
          $names[] = $table->getName();
        } // foreach

        sort($names);

        foreach($names as $name) {
          $output->printMessage($name);
        } // if
      } // if
    } // execute
  
  }