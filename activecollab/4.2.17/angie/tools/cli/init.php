<?php

  /**
   * Initialize CLI framework
   * 
   * @package angie.tools.cli
   */
  
  define('CLI_TOOL_PATH', ANGIE_PATH . '/tools/cli');
  
  require CLI_TOOL_PATH . '/framework/CLI.class.php';
  
  require CLI_TOOL_PATH . '/framework/command/CLICommand.class.php';
  require CLI_TOOL_PATH . '/framework/command/CLICommandExecutable.class.php';
  require CLI_TOOL_PATH . '/framework/command/CLICommandGenerator.class.php';
  
  require CLI_TOOL_PATH . '/framework/errors/CLICommandHandlerDnxError.class.php';
  require CLI_TOOL_PATH . '/framework/errors/ReadArgumentsCLIError.class.php';
  require CLI_TOOL_PATH . '/framework/errors/ArgumentRequiredCLIError.class.php';
  require CLI_TOOL_PATH . '/framework/errors/UnknownOptionCLIError.class.php';

  require CLI_TOOL_PATH . '/framework/output/Output.class.php';
  require CLI_TOOL_PATH . '/framework/output/CLIOutput.class.php';