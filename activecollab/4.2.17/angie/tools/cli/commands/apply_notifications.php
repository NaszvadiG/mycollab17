<?php

  /**
   * Apply framework notifications to system module
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandApplyNotifications extends CLICommandGenerator {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Apply framework notifications to system module';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('f', 'force', 'Force new files'),
      array('t:', 'target:', 'Name of the target module (system is default)'),
    );

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $force = $this->getOption(array('f', 'force'), false);
      $target_module = $this->getOption(array('t', 'target'));

      if(empty($target_module)) {
        $target_module = 'system';
      } // if

      if($force) {
        if(!$output->ask("Are you sure that you want to forcefully apply framework notifications to '$target_module' module?")) {
          $output->abortWithMessage("Operation aborted");
        }
      } // if

      foreach(AngieApplication::getFrameworks() as $framework) {
        $this->applyFrameworkNotifications($framework, $target_module, $output, $force);
      } // foreach
    } // execute

  }