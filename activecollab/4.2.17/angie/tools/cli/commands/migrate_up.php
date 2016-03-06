<?php

  /**
   * Migrate up, to the latest database version
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandMigrateUp extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Migrate up, to the latest release';

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $batch = AngieApplication::migration()->up($output);

      $output->printLine();

      if(count($batch)) {
        $output->printMessage('Following migrations have been executed, in this exact order:');
        $output->printLine();

        $counter = 0;
        $pad = strlen(count($batch));

        foreach($batch as $script_name) {
          $output->printMessage(str_pad(++$counter, $pad, '0', STR_PAD_LEFT) . '. ' . $script_name);
        } // if

        $output->printLine();
      } else {
        $output->printMessage('No migrations executed');
      } // if
    } // execute

  }