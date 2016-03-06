<?php

  /**
   * Init On Demand module
   *
   * Init On Demand via cli
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandClearSampleProject extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Clear Sample Project';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(

    );

    /**
     * Execute the command
     *
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      SampleProject::cleanUp();

      $output->printMessage('Sample project removed');

    } // execute

  } // CLICommandClearSampleProject
