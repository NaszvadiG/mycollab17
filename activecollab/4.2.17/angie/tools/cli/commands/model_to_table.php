<?php

  /**
   * Run model generator
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandModelToTable extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Generate CREATE TABLE code based on model';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);
      
      $table_names = explode(',', $this->getArgument(1));
      if(is_foreachable($table_names)) {
        AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), explode(',', APPLICATION_MODULES));
        $table_prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
        
        foreach($table_names as $table_name) {
          try {
            $table = AngieApplicationModel::getTable($table_name);
          
            if($table instanceof DBTable) {
              $output->printMessage("SQL command for '$table_name' table is:");
              $output->printLine();
              $output->printContent($table->getCreateCommand($table_prefix));
            } else {
              $output->printMessage("Table '$table_name' not found", 'error');
            } // if
          } catch(InvalidParamError $e) {
            $output->printMessage($e->getMessage(), 'error');
          } // try
          
          $output->printLine(2);
        } // foreach
      } else {
        $output->printMessage('Please specify table(s)');
      } // if`
    } // execute
  
  }