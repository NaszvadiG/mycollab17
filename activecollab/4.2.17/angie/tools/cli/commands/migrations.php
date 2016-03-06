<?php

  /**
   * List all defined migrations
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandMigrations extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List migrations';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('s:', 'since', 'Since given time stamp (YYYY-MM-DD format is recognised)'),
    );

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $since = $this->getOption(array('s', 'since'));

      $scripts_listed = 0;
      $all_scripts = AngieApplication::migration()->getScripts();

      if(is_foreachable($all_scripts)) {
        foreach($all_scripts as $changset => $scripts) {
          if($since && $since > AngieApplication::migration()->getChangesetTimestamp($changset)) {
            continue;
          } // if

          $output->printLine();
          $output->printMessage($changset . ':');
          $output->printLine();

          $pad = strlen(count($scripts));
          $counter = 0;

          foreach($scripts as $script) {
            $scripts_listed++;

            $message = $script->getDescription();

            if($script->getExecuteAfter()) {
              $message .= ' (executes after ' . implode(', ', $script->getExecuteAfter()) . ')';
            } // if

            $executed = $script->isExecuted() ? '[EXECUTED] ' : '';

            $output->printMessage(str_pad(++$counter, $pad, '0', STR_PAD_LEFT) . '. ' . $executed . $message);
          } // foreach
        } // foreach
      } // if

      if(empty($scripts_listed)) {
        if($since) {
          $output->printMessage("There are no migration scripts created since '$since'");
        } else {
          $output->printMessage("There are no migration scripts");
        } // if
      } // if

      $output->printLine();
    } // execute

  }