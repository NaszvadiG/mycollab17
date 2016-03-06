<?php

  /**
   * Archive tasks via CLI
   *
   * @package angie.tools.cli.commands
   */
  class CLICommandArchiveCompletedTasks extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Archive tasks';

    /**
     * Execute the command
     *
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $two_months_ago = DateTimeValue::makeFromTimestamp(time()-(86400*60));

      /**
       * @var Task[] $tasks
       */
      $tasks = Tasks::find([
        "conditions" => ["state = ? and completed_on IS NOT NULL and completed_on <= ?", STATE_VISIBLE, $two_months_ago]
      ]);

      $total_tasks = count($tasks);

      if ($total_tasks) {
        $output->printMessage("Found {$total_tasks} tasks for archiving");
        $i = 1;
        foreach ($tasks as $task) {
          $task->state()->archive();
          $output->printMessage("Archived {$i}/{$total_tasks}");
          $i++;
        } // foreach
      } // if

    } // execute

  }