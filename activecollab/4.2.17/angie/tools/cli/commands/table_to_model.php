<?php

  /**
   * Run model generator
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandTableToModel extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Generate model code based on table definition';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);
      
      $table_names = explode(',', $this->getArgument(1));
      if(is_foreachable($table_names)) {
        foreach($table_names as $table_name) {
          $table = DB::loadTable($table_name);
          
          $output->printMessage("Model definition for table '$table_name':\n");
          $output->printContent($table->prepareModelDefinition() . "\n\n");
        } // foreach
      } else {
        $output->printMessage('Please specify table(s)');
      } // if`
    } // execute
  
  }