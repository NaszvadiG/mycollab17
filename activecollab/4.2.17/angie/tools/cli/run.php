<?php

  /**
   * Angie console input file
   * 
   * Top level commands for angie CLI utility are placed in /bin/commands 
   * folder. This commands can have subcommands that are based on a specific 
   * subcommand implementation
   * 
   * @package angie.tools.cli
   */
  
  session_start();
  
  // ---------------------------------------------------
  //  Init resources
  // ---------------------------------------------------

  const AUTOLOAD_ALLOW_PATH_OVERRIDE = false; // before everything, because there's autoloading before initializing defaults...
  
  require_once realpath(dirname(__FILE__) . '/../../init.php');
  require_once dirname(__FILE__) . '/init.php';

  defined('ENVIRONMENT_PATH') or define('ENVIRONMENT_PATH', getcwd());
  
  // ---------------------------------------------------
  //  Go baby go!
  // ---------------------------------------------------
  
  $pieces = CLI::readArgv();
  
  $command = array_var($pieces, 1);
  if(trim($command) == '') {
    print "Command is missing\n";
    die();
  } // if

  // prepare log file
  $log_file_path = null;
  if (($key = array_search("--log-to-file", $pieces)) !== false) {
    $log_file_path = ENVIRONMENT_PATH . "/logs/cli_{$command}_".date("Y-m-d_H-i-s").".txt";
    Logger::log("CLI params: " . implode(" ", $pieces));

    // remove the log triggering param from array so handlers don't scream about it
    unset($pieces[$key]);
  } // if

  $skip_filesystem_info = false;
  if (($key = array_search("--skip-fs-info", $pieces)) !== false) {
    $skip_filesystem_info = true;
    unset($pieces[$key]);
  } // if

  try {
    $command_handler = CLI::useHandler($command);
    CLI::prepareCommand($command_handler, array_slice($pieces, 1));
    
    if($command_handler->getOption(array('h', 'help'))) {
      print $command_handler->getHelp();
    } elseif($command_handler->isQuiet()) {
      $command_handler->execute(new Output($log_file_path, $skip_filesystem_info)); // silent
    } else {
      $command_handler->execute(new CLIOutput($log_file_path, $skip_filesystem_info));
    } // if

    if (!is_null($log_file_path)) {
      Logger::logToFile($log_file_path);
    } // if
  } catch(Exception $error) {
    $string = "\nFatal error: " . $error->getMessage() . "\nError Parameters:\n\n";

    if ($error instanceof Error) {
      foreach($error->getParams() as $param => $value) {
        if(is_array($value)) {
          $string .= "$param: " . var_export($value, true) . "\n";
        } else {
          $string .= "$param: $value\n";
        } // if

      } // foreach
    } // if


    $string .= "\nBacktrace:\n\n" . $error->getTraceAsString() . "\n";

    if (is_null($log_file_path)) {
      fwrite(STDERR, $string);
    } else { // file logging mode: display error msg only
      Logger::log($string, Logger::ERROR);
      fwrite(STDERR, $error->getMessage());
      Logger::logToFile($log_file_path);
    } // if

    // ...and die!
    die();
  } // try