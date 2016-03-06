<?php

  /**
   * Show application info
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandInfo extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Show application info';
      
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      if(CLI::initEnvironment($output, $this->getArgument(1))) {
        AngieApplication::bootstrapForCommandLineRequest($output);
        $output->printMessage(AngieApplication::getName() . ', v' . AngieApplication::getVersion());
      } // if
    } // execute
    
  }