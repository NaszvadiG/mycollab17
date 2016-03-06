<?php

  /**
   * Initialize application environment 
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandInitEnvironment extends CLICommandGenerator {
    
    // Names of the environments
    const ENV_PRODUCTION = 'production';
    const ENV_DEVELOPMENT = 'development';
    const ENV_TEST = 'test';
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Initialize application environment';
      
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      $environment = $this->getArgument(1);
      if(empty($environment)) {
        $environment = self::ENV_PRODUCTION;
      } // if

      print $environment . "\n";
      
      if($environment === self::ENV_PRODUCTION) {
        $config_file_name = 'config.php';
      } else {
        $config_file_name = "config.$environment.php";
      } // if
      
      $config_file = without_slash(getcwd()) . "/config/$config_file_name";
      if(is_file($config_file)) {
        require_once $config_file;
        $output->printMessage("Configuration file '$config_file_name' found and loaded", 'info');
        
        try {
          DB::setConnection('default', new MySQLDBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PERSIST, DB_CHARSET));
        } catch(Exception $e) {
          $output->printMessage('Failed to connect to database. Reason: ' . $e->getMessage(), 'error');
          return;
        } // try
        
        // Drop existing tables
        $existing_tables = DB::listTables(TABLE_PREFIX);
        if(is_foreachable($existing_tables)) {
          if($output->ask(count($existing_tables) . ' table(s) with ' . TABLE_PREFIX . ' table prefix found. Drop?')) {
            DB::dropTables($existing_tables);
            $output->printMessage('Existing tables dropped: ' . implode(', ', $existing_tables), 'info');
          } else {
            return;
          } // if
        } // if
        
        AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), explode(',', APPLICATION_MODULES));
        AngieApplicationModel::init($environment);
        
        if($environment) {
          $output->printMessage("'$environment' environment initialized");
        } else {
          $output->printMessage('Default environment initialized');
        } // if
      } else {
        $output->printMessage("Configuration file not defined @ $config_file", 'error');
      } // if
    } // execute
    
  }