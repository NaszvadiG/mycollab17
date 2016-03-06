<?php

  /**
   * Change prefix command
   * 
   * Walk through tables and change table prefix from existing value to a new 
   * value
   *
   * @package angie.tools.cli_commands
   */

  /**
   * Change table prefix
   */
  class CLICommandChangePrefix extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Change table prefix for all tables in the application';
    
    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('f:', 'from:', 'Original prefix value'),
      array('t:', 'to:', 'New prefix value'),
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
      
      $from = $this->getOption(array('f', 'from'));
      $to = $this->getOption(array('t', 'to'));
      
      if(empty($from) || empty($to)) {
        $output->printMessage('From and To option values are required');
        die();
      } // if
      
      // Connect to database
      DB::setConnection('default', new MySQLDBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME, false, DB_CHARSET));
      
      $tables = DB::getConnection()->listTables($from);
      
      $updated = 0;
      if(is_foreachable($tables)) {
        $db_name = DB_NAME;
        foreach($tables as $table) {
          $new_name = $to . substr($table, strlen($from));
          $rename = DB::execute("RENAME TABLE $db_name.$table TO $db_name.$new_name");
          if($rename && !is_error($rename)) {
            $output->printMessage("Table '$table' renamed to '$new_name'");
            $updated++;
          } else {
            $output->printMessage("Failed to rename '$table' table to '$new_name'");
          } // if
        } // foreach
      } // if
      
      $output->printMessage("\n$updated tables renamed");
    } // execute
  
  } // CLICommandChangePrefix
?>