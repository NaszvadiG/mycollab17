<?php

  /**
   * Command Line Interface implementation
   * 
   * This class is used for easy parsing of CLI (Command Line Interface) requests 
   * and optaining data from CLI. As a result of processing this class returns 
   * CLICommand instance than be used for interaction with arguments and options 
   * of the input command.
   *
   * @package angie.tools.cli
   */
  final class CLI {

    /**
     * Instance path
     *
     * @var string
     */
    static private $instance_path = false;

    /**
     * Return instance path
     *
     * @return string
     */
    static function getInstancePath() {
      if(self::$instance_path === false) {
        self::$instance_path = getcwd();
      } // if

      return self::$instance_path;
    } // getInstancePath

    /**
     * Cached project cli folder path
     *
     * @var string
     */
    static private $project_cli_path = null;

    /**
     * Return project CLI path
     *
     * @return string
     */
    static function getProjectCliPath() {
      if(self::$project_cli_path === null) {
        $config_file_path = CLI::getInstancePath() . '/config/config.php';

        if(is_file($config_file_path)) {
          require_once $config_file_path;

          self::$project_cli_path = ROOT . '/' . APPLICATION_VERSION . '/cli';
        } else {
          self::$project_cli_path = false;
        } // if
      } // if

      return self::$project_cli_path;
    } // getProjectCliPath

    /**
     * Return project commands folder path
     *
     * @return string
     */
    static function getProjectCommandsPath() {
      return CLI::getProjectCliPath() . '/commands';
    } // getProjectCommandsPath

    /**
     * Return project templates folder path
     *
     * @return string
     */
    static function getProjectTemplatesPath() {
      return CLI::getProjectCliPath() . '/templates';
    } // getProjectTemplatesPath
    
    /**
     * Return populated console command handler
     *
     * @param CLICommand $command_handler
     * @param array $from_arguments
     * @param array $short_options
     * @param array $long_options
     * @return CLICommand
     */
    static function prepareCommand($command_handler = null, $from_arguments = null, $short_options = null, $long_options = null) {
      if(is_null($command_handler)) {
        $command_handler = new CLICommand();
      } // if
      
      if(!($command_handler instanceof CLICommand)) {
        return new InvalidParamError('command_handler', $command_handler, '$command_handler is not valid instance of CLICommand class');
      } // if
      
      // If we are missing option definitions, but we have an executable command 
      // than we can extract defintiions from the handler instance...
      if((is_null($short_options) || is_null($long_options)) && $command_handler instanceof CLICommandExecutable) {
        $handlers_options = $command_handler->getOptionDefinitions();
        
        $handlers_short_options = array();
        $handlers_long_options = array();
        
        if(is_foreachable($handlers_options)) {
          foreach($handlers_options as $handlers_option) {
            list($short, $long, $help) = $handlers_option;
            if($short) {
              $handlers_short_options[] = $short;
            } // if
            if($long) {
              $handlers_long_options[] = $long;
            } // if
          } // foreach
        } // if
        
        if(is_null($short_options)) {
          $short_options = $handlers_short_options;
        } // if
        
        if(is_null($long_options)) {
          $long_options = $handlers_long_options;
        } // if
      } // if
      
      $process = CLI::processCommand($from_arguments, $short_options, $long_options);
      
      if($process && !is_error($process)) {
        $command_handler->setArguments($process['arguments']);
        $command_handler->setOptions($process['options']);
        
        return $command_handler;
      } else {
        return $process;
      } // if
    } // prepareCommand
    
    /**
     * Extract data from command line argumnets
     * 
     * $arguments is an array of console arguments. If NULL it will be read 
     * using readArgv() function
     * 
     * The $options parameter may contain the following elements: individual 
     * characters, and characters followed by a colon to indicate an option 
     * argument is to follow. For example, an option string x recognizes an 
     * option -x, and an option string x: recognizes an option and argument -x 
     * argument. It does not matter if an argument has leading white space.
     * 
     * As a result this function returns an array where first element is a set 
     * of non-option arguments and the second one is the array of options
     *
     * @param array $from_arguments
     * @param array $short_options
     * @param array $long_options
     * @return array
     * @throws ArgumentRequiredCLIError
     * @throws UnknownOptionCLIError
     */
    static function processCommand($from_arguments = null, $short_options = null, $long_options = null) {
      if(is_null($from_arguments)) {
        $arguments = CLI::readArgv();
      } else {
        $arguments = (array) $from_arguments;
      } // if
      
      if(!is_array($short_options)) {
        $short_options = array();
      } // if
      
      if(!is_array($long_options)) {
        $long_options = array();
      } // if
      
      $options = array();
      $non_options = array();
      
      $skip_next = false;
      foreach($arguments as $k => $argument) {
        if($skip_next) {
          $skip_next = false;
          continue;
        } // if
        
        // Long
        if(str_starts_with($argument, '--')) {
          $option = substr($argument, 2);
          
          // Present, but without arguments
          if(in_array($option, $long_options)) {
            $options[$option] = true;
          } else {
            
            // Requires an argument
            if(($pos = strpos($option, '=')) !== false) {
              list($option_name, $option_value) = explode('=', $option);
              if(in_array($option_name . ':', $long_options)) {
                $options[$option_name] = CLI::processValue($option_value);
              } else {
                CLI::useError('ArgumentRequiredCLIError');
                throw new ArgumentRequiredCLIError($option);
              } // if
              
            // argument is required
            } else {
              
              // We have a long one with required value but no = (no value)
              if(in_array($option . ':', $long_options)) {
                CLI::useError('ArgumentRequiredCLIError');
                throw new ArgumentRequiredCLIError($option);
                
              // We don't have this option defined
              } else {
                CLI::useError('UnknownOptionCLIError');
                throw new UnknownOptionCLIError($option);
              } // if
              
            } // if
          } // if
          
        // Short
        } elseif(str_starts_with($argument, '-')) {
          $option = substr($argument, 1);
          
          // Present, but without arguments
          if(in_array($option, $short_options)) {
            $options[$option] = true;
            
          // Requires an argument
          } elseif(in_array($option . ':', $short_options)) {
            if(isset($arguments[$k + 1])) {
              $options[$option] = CLI::processValue(array_var($arguments, $k + 1, true));
              $skip_next = true;
            } else {
              CLI::useError('ArgumentRequiredCLIError');
              throw new ArgumentRequiredCLIError($option);
            } // if
            
          // Unknown option
          } else {
            CLI::useError('UnknownOptionCLIError');
            throw new UnknownOptionCLIError($option);
          } // if
          
        // Argument
        } else {
          $non_options[] = CLI::processValue($argument);
        } // if
      } // foreach
      
      return array(
        'arguments' => $non_options,
        'options'   => $options,
      ); // array
    } // processCommand
    
    /**
     * Process single value
     * 
     * This function is called to process a single value (any value that is not 
     * a short or long option name)
     *
     * @param string
     * @return mixed
     */
    static function processValue($value) {
      if(is_string($value)) {
        if(str_starts_with($value, '[') && str_ends_with($value, ']')) {
          return explode(',', substr($value, 1, strlen($value) - 2));
        } else {
          return $value;
        } // if
      } else {
        return $value;
      } // if
    } // processValue
   
    /**
     * Safely read the $argv PHP array across different PHP configurations
     * 
     * Will take care on register_globals and register_argc_argv ini directives
     *
     * @return mixed the $argv
     */
    static function readArgv() {
      global $argv;
      if(!is_array($argv)) {
        if(!@is_array($_SERVER['argv'])) {
          if(!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
            return new ReadArgumentsCLIError();
          } // if
          return $GLOBALS['HTTP_SERVER_VARS']['argv'];
        } // if
        return $_SERVER['argv'];
      } // if
      return $argv;
    } // readArgv
    
    /**
     * Find, load, construct and return handler for $command
     *
     * @param string $command
     * @return CLICommandExecutable
     * @throws CLICommandHandlerDnxError
     */
    static function useHandler($command) {
      $command_file = CLI_TOOL_PATH . "/commands/$command.php";
      if(is_file($command_file)) {
        require_once $command_file;
      } else {
        $project_command_file = CLI::getProjectCommandsPath() . "/$command.php";
        if(is_file($project_command_file)) {
          require_once $project_command_file;
        } else {
          CLI::useError('CLICommandHandlerDnxError');
          throw new CLICommandHandlerDnxError($command);
        } // if
      } // if
      
      $command_class = 'CLICommand' . Inflector::camelize($command);
      if(!class_exists($command_class)) {
        CLI::useError('CLICommandHandlerDnxError');
        throw new CLICommandHandlerDnxError($command);
      } // if
      
      $command_handler = new $command_class();
      if(!($command_handler instanceof CLICommandExecutable)) {
        CLI::useError('CLICommandHandlerDnxError');
        throw new CLICommandHandlerDnxError($command);
      } // if
      
      return $command_handler;
    } // useHandler
    
    /**
     * Use CLI framework error
     *
     * @param string $error
     */
    static function useError($error) {
      if(!class_exists($error)) {
        require_once CLI_TOOL_PATH . "/framework/errors/$error.class.php";
      } // if
    } // useError
    
    /**
     * Initialize application environment
     *
     * @param Output $output
     * @param string $environment
     * @return boolean
     */
    static function initEnvironment(Output $output, $environment = null) {
      $config_file = $environment ? CLI::getInstancePath() . "/config/config.$environment.php" : CLI::getInstancePath() . '/config/config.php';
      
      if(is_file($config_file)) {
        require_once $config_file;
        require_once ANGIE_PATH . '/init.php';

        if($environment == 'test') {
          AngieApplication::bootstrapForTest($output);
        } else {
          AngieApplication::bootstrapForCommandLineRequest($output);
        } // if

        
        return true;
      } else {
        $output->printMessage("Failed to load configuration file. Expected location: '$config_file'", 'error');
        return false;
      } // if
    } // initEnvironment

    /**
     * Initialize application mailer for CLI
     *
     * @return boolean
     */
    static function initMailer() {
      AngieApplication::initMailer();
      AngieApplication::initRouter();
      return true;
    } //initMailer
  
  }