<?php

  /**
   * Migrate down
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandMigrateDown extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Migrate down';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('t:', 'to:', 'Downgrade to a given timestamp (YYYY-MM-DD)'),
    );

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $to = $this->getOption(array('t', 'to'));

      if($to && !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $to)) {
        $output->abortWithMessage("Value '$to' is not a valid timestamp. Use YYYY-MM-DD format");
      } // if

      if(empty($to)) {
        if(!$output->ask('No timestamp provided. Are you sure that you want to revert all migrations?')) {
          $output->abortWithMessage('Aborting');
        } // if
      } else {
        $output->printMessage("Reverting all migrations executed since '$to'");
      } // if

      $batch = AngieApplication::migration()->down($to, $output);

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