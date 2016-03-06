<?php

  /**
   * Init On Demand module
   *
   * Init On Demand via cli
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandInitSampleProject extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Init Sample Project';

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

      SampleProject::create();

      $output->printMessage('Sample project initialized');

    } // execute

  } // CLICommandInitSampleProject
