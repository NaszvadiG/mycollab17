<?php

  /**
   * Change table engine
   * 
   * This command will go through all application tables and change their 
   * storage engine. Usage:
   * 
   * - angie change_engine -t innodb -e 'ac_search_index,ac_another_table'
   *
   * @package angie.tools.cli_commands
   */
  
  /**
   * Change table engine
   */
  class CLICommandChangeEngine extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Change table engine';
    
    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('t:', 'to:', 'New table engine'),
      array('e:', 'exclude:', 'Coma-separated list of tables that will be excluded'),
    );
  
    /**
     * Execute the command
     * 
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initProject(null, 'config'); // Init project with regular configuration file
      
      $to = $this->getOption(array('t', 'to'));
      if(empty($to)) {
        $output->printMessage('To option values are required');
        die();
      } // if
      
      $exclude = explode(',', $this->getOption(array('e', 'exclude')));
      
      // Connect to database
      DB::setConnection('default', new MySQLDBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME, false, DB_CHARSET));
      $tables = DB::getConnection()->listTables(TABLE_PREFIX);
      
      $updated = 0;
      if(is_foreachable($tables)) {
        foreach($tables as $table) {
          if(in_array($table, $exclude)) {
            continue;
          } // if
          
          try {
            DB::execute("ALTER TABLE $table ENGINE=$to");
            $output->printMessage("Table '$table' updated");
            $updated++;
          } catch(Exception $e) {
            $output->printMessage("Failed to update '$table'");
          } // try
        } // foreach
      } // if
      
      $output->printMessage("\n$updated tables updated");
    } // execute
  
  }

?>