<?php

  /**
   * List all available angie commands
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandAll extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List all available commands with a short description';
  
    /**
     * Execute the command
     * 
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      $commands = $this->readCommands();
      
      if(is_foreachable($commands)) {
        $output->printMessage('Available commands:');
        $output->printMessage('');
        
        $data = array();
        
        foreach($commands as $command) {
          $handler = CLI::useHandler($command);
          $data[] = array($command, $handler->getDescription());
        } // foreach
        
        $output->printTable(array('Command', 'Description'), $data);
        
        $output->printMessage('');
        $output->printMessage('To see more information for any specific command type: angie <command> --help');
      } else {
        $output->printMessage('No commands available');
      } // if
    } // execute
    
    /**
     * Returns an array of available commands
     *
     * @return array
     */
    function readCommands() {
      $from = array(CLI_TOOL_PATH . '/commands');

      $config_file_path = getcwd() . '/config/config.php';

      if(is_file($config_file_path)) {
        require_once $config_file_path;

        $project_commands_dir = ROOT . '/' . APPLICATION_VERSION . '/cli/commands';
      } else {
        $project_commands_dir = false;
      } // if

      if($project_commands_dir && is_dir($project_commands_dir)) {
        $from[] = $project_commands_dir;
      } // if
      
      $result = array();
      foreach($from as $from_folder) {
        $files = get_files($from_folder, 'php');
        if(is_foreachable($files)) {
          foreach($files as $file) {
            $filename = basename($file);
            $result[] = substr($filename, 0, strlen($filename) - 4);
          } // foreach
        } // if
      } // if
      
      return $result;
    } // readCommands
  
  }